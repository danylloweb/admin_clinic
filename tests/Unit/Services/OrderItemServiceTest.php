<?php

namespace Tests\Unit\Services;

use App\Entities\OrderItem;
use App\Entities\Schedule;
use App\Entities\ServiceConfiguration;
use App\Enums\ItemTypeEnum;
use App\Enums\OrderItemStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\ScheduleStatusEnum;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ScheduleStatusLogRepository;
use App\Repositories\ScheduleRepository;
use App\Services\OrderItemService;
use Tests\TestCase;
use Mockery;
use App\Criterias\AppRequestCriteria;

class OrderItemServiceTest extends TestCase
{
    private $orderItemRepository;
    private $scheduleStatusLogRepository;
    private $orderRepository;
    private $service;
    private $scheduleRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderItemRepository = Mockery::mock(OrderItemRepository::class);
        $this->scheduleStatusLogRepository = Mockery::mock(ScheduleStatusLogRepository::class);
        $this->orderRepository = Mockery::mock(OrderRepository::class);
        $this->scheduleRepository = Mockery::mock(ScheduleRepository::class);

        $this->service = new OrderItemService(
            $this->orderItemRepository,
            $this->scheduleStatusLogRepository,
            $this->orderRepository,
            $this->scheduleRepository
        );
    }

    public function test_it_should_schedule_service_with_valid_data()
    {
        // Arrange
        $orderItem = Mockery::mock(OrderItem::class)->makePartial();
        $orderItem->id = 1;
        $orderItem->customer_id = 123;
        $orderItem->order_id = 456;
        $orderItem->status = OrderItemStatusEnum::SCHEDULED;
        $orderItem->external_order_id = 'EXT123';
        $orderItem->ref_description = 'Service Description';
        $orderItem->ref_parent_description = 'Parent Service Description';
        $orderItem->price = 100.00;
        $orderItem->quantity = 1;
        
        // Mock ServiceConfiguration
        $serviceConfig = Mockery::mock(ServiceConfiguration::class)->makePartial();
        $serviceConfig->ref_id_provider = 'PROVIDER123';
        $orderItem->shouldReceive('getAttribute')->with('serviceConfiguration')->andReturn($serviceConfig);
        
        // Mock customer_order_by attribute
        $orderItem->shouldReceive('getCustomerOrderByAttribute')->andReturn('Customer Name');
        
        $schedule = Mockery::mock(Schedule::class)->makePartial();
        $schedule->id = 1;
        $schedule->customer_id = 123;
        $schedule->order_id = 456;
        $schedule->order_item_id = 1;
        $schedule->provider_id = 789;
        $schedule->ref_id = 'REF123';
        $schedule->ref_parent_id = 'PARENT123';
        $schedule->channel = 1;

        // Mock save methods
        $schedule->shouldReceive('save')->once()->andReturn(true);
        $orderItem->shouldReceive('save')->once()->andReturn(true);
        
        // Set up the relationship
        $orderItem->schedule = $schedule;

        $scheduleData = [
            'order_item_id' => 1,
            'when_date' => '2024-03-20',
            'when_time_start' => '09:00',
            'when_time_end' => '10:00'
        ];

        // Mock repository responses
        $this->orderItemRepository->shouldReceive('skipPresenter->findWhere')
            ->once()
            ->andReturn(collect([$orderItem]));

        $this->scheduleStatusLogRepository->shouldReceive('skipPresenter->create')
            ->once()
            ->with([
                'schedule_id' => 1,
                'status' => ScheduleStatusEnum::SCHEDULED,
                'author' => "customer_id:123",
                'log' => "Agendamento Realizado.",
            ])
            ->andReturn(true);

        $this->orderRepository->shouldReceive('modifyOrderStatusByItemStatus')
            ->once()
            ->with(456, OrderItemStatusEnum::SCHEDULED->value, OrderStatusEnum::SCHEDULED->value)
            ->andReturn(true);

        // Act
        $result = $this->service->scheduleService($scheduleData);

        // Assert
        $this->assertEquals(OrderItemStatusEnum::SCHEDULED, $orderItem->status);
        $this->assertEquals(ScheduleStatusEnum::SCHEDULED, $orderItem->schedule->status);
        $this->assertEquals('2024-03-20', $orderItem->schedule->when_date);
        $this->assertEquals('09:00', $orderItem->schedule->when_time_start);
        $this->assertEquals('10:00', $orderItem->schedule->when_time_end);
        $this->assertEquals('PROVIDER123', $result['ref_id_provider']);
    }

    public function test_it_should_fail_schedule_service_with_invalid_order_item()
    {
        // Arrange
        $scheduleData = [
            'order_item_id' => 999,
            'when_date' => '2024-03-20',
            'when_time_start' => '09:00',
            'when_time_end' => '10:00'
        ];

        $this->orderItemRepository->shouldReceive('skipPresenter->findWhere')
            ->once()
            ->andReturn(collect([]));

        // Act
        $result = $this->service->scheduleService($scheduleData);

        // Assert
        $this->assertTrue($result['error']);
        $this->assertEquals('O pagamento deste serviço ainda não foi confirmado.', $result['message']);
    }

    public function test_it_should_create_order_item()
    {
        $data = [
            'order_id' => 1,
            'type' => ItemTypeEnum::SERVICE->value,
            'price' => 100.00,
            'quantity' => 1,
            'ref_id' => 'REF001',
            'customer_id' => 1
        ];

        $expectedResult = array_merge($data, ['id' => 1]);

        $this->orderItemRepository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->service->create($data);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_return_all_order_items_paginated()
    {
        $limit = 20;
        $expectedResult = [
            'data' => [
                [
                    'id' => 1,
                    'order_id' => 1,
                    'type' => ItemTypeEnum::SERVICE->value,
                    'status' => OrderStatusEnum::PAYMENT_PENDING->value
                ],
                [
                    'id' => 2,
                    'order_id' => 1,
                    'type' => ItemTypeEnum::SERVICE->value,
                    'status' => OrderStatusEnum::CONFIRMED->value
                ]
            ],
            'total' => 2,
            'per_page' => 20,
            'current_page' => 1
        ];

        $this->orderItemRepository->shouldReceive('resetCriteria')
            ->once()
            ->andReturn($this->orderItemRepository);

        $this->orderItemRepository->shouldReceive('pushCriteria')
            ->withAnyArgs()
            ->andReturn($this->orderItemRepository);

        $this->orderItemRepository->shouldReceive('paginate')
            ->once()
            ->with($limit)
            ->andReturn($expectedResult);

        $result = $this->service->all($limit);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_update_order_item()
    {
        $id = 1;
        $data = [
            'price' => 150.00,
            'quantity' => 2
        ];

        $expectedResult = array_merge(['id' => $id], $data);

        $this->orderItemRepository->shouldReceive('update')
            ->once()
            ->with($data, $id)
            ->andReturn($expectedResult);

        $result = $this->service->update($data, $id);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_find_order_item_by_id()
    {
        $id = 1;
        $expectedResult = [
            'id' => $id,
            'order_id' => 1,
            'type' => ItemTypeEnum::SERVICE->value,
            'price' => 100.00,
            'status' => OrderItemStatusEnum::PAYMENT_PENDING->value
        ];

        $this->orderItemRepository->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($expectedResult);

        $result = $this->service->find($id);
        $this->assertEquals($expectedResult, $result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
} 