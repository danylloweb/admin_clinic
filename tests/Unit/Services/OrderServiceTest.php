<?php

namespace Tests\Unit\Services;

use App\Criterias\AppRequestCriteria;
use App\Criterias\FilterByCustomerIdCriteria;
use App\Enums\ItemTypeEnum;
use App\Enums\OrderItemStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\ScheduleStatusEnum;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\ScheduleStatusLogRepository;
use App\Services\OrderService;
use Tests\TestCase;
use Mockery;
use Illuminate\Database\Eloquent\Collection;
use App\Integrations\OmniServiceBffIntegration;

class OrderServiceTest extends TestCase
{
    protected $orderService;
    protected $repository;
    protected $orderItemRepository;
    protected $scheduleRepository;
    protected $scheduleStatusLogRepository;
    protected $omniServiceBffIntegration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(OrderRepository::class);
        $this->orderItemRepository = Mockery::mock(OrderItemRepository::class);
        $this->scheduleRepository = Mockery::mock(ScheduleRepository::class);
        $this->scheduleStatusLogRepository = Mockery::mock(ScheduleStatusLogRepository::class);
        $this->omniServiceBffIntegration = Mockery::mock(OmniServiceBffIntegration::class);

        $this->orderService = new OrderService(
            $this->repository,
            $this->orderItemRepository,
            $this->scheduleRepository,
            $this->scheduleStatusLogRepository,
            $this->omniServiceBffIntegration
        );
    }

    public function test_it_should_return_all_orders_paginated()
    {
        $limit = 20;
        $expectedResult = [
            'data' => [
                [
                    'id' => 1,
                    'customer_id' => 1,
                    'external_order_id' => 'ORD-001',
                    'total' => 150.00,
                    'status' => OrderStatusEnum::PAYMENT_PENDING
                ]
            ],
            'total' => 1,
            'per_page' => 20,
            'current_page' => 1
        ];

        $this->repository->shouldReceive('resetCriteria')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('pushCriteria')
            ->times(3)
            ->andReturn($this->repository);

        $this->repository->shouldReceive('paginate')
            ->once()
            ->with($limit)
            ->andReturn($expectedResult);

        $result = $this->orderService->all($limit);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_create_new_order()
    {
        $orderData = [
            'customer' => [
                'id' => 1,
                'name' => 'John Doe'
            ],
            'order_id' => 'ORD-001',
            'order_hash' => 'hash123',
            'session_id' => 'sess123',
            'zip_code' => '12345-678',
            'channel' => 'web',
            'items' => [
                [
                    'ref_id' => 'ref_id',
                    'ref_parent_id' => 'ref_parent_id',
                    'ref_image_url' => 'ref_image_url',
                    'ref_parent_image_url' => 'ref_parent_image_url',
                    'ref_description' => 'ref_description',
                    'ref_parent_description' => 'ref_parent_description',
                    'type' => 'service',
                    'price' => 100.00,
                    'quantity' => 1,
                    'ref_image_url' => 'https://example.com/image.jpg',
                    'ref_parent_image_url' => 'https://example.com/parent-image.jpg',
                    'ref_description' => 'Item description',
                    'ref_parent_description' => 'Parent description'
                ]
            ]
        ];

        $expectedOrder = Mockery::mock();
        $expectedOrder->items = new Collection();
        $expectedOrder->shouldReceive('items->createMany')->once();

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('create')
            ->once()
            ->andReturn($expectedOrder);

        $result = $this->orderService->create($orderData);
        $this->assertEquals(["message" => "Pedido criado com sucesso."], $result);
    }

    public function test_it_should_confirm_payment()
    {
        $paymentData = [
            'order_id' => 'ORD-001'
        ];

        // Configuração do Mockery para permitir chamadas flexíveis
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(true);

        // Mock do item de pedido
        $mockItem = \Mockery::mock()->makePartial();
        $mockItem->id = 1;
        $mockItem->ref_id = 'ref1';
        $mockItem->ref_parent_id = 'parent1';
        $mockItem->type = ItemTypeEnum::SERVICE->value;
        $mockItem->shouldReceive('save')->andReturn(true);

        // Mock do pedido
        $mockOrder = \Mockery::mock()->makePartial();
        $mockOrder->status = OrderStatusEnum::PAYMENT_PENDING->value;
        $mockOrder->customer_id = 1;
        $mockOrder->provider_id = 1;
        $mockOrder->id = 1;
        $mockOrder->channel = 'web';
        $mockOrder->items = new Collection([$mockItem]);
        $mockOrder->shouldReceive('save')->andReturn(true);

        // Mock do agendamento
        $mockSchedule = \Mockery::mock()->makePartial();
        $mockSchedule->id = 1;

        // Configuração dos repositórios
        $repositoryMock = \Mockery::mock(OrderRepository::class);
        $repositoryMock->shouldReceive('skipPresenter')->once()->andReturnSelf();
        $repositoryMock->shouldReceive('findWhere')
            ->with(['external_order_id' => 'ORD-001'])
            ->andReturn(new Collection([$mockOrder]));

        $scheduleRepoMock = \Mockery::mock(ScheduleRepository::class);
        $scheduleRepoMock->shouldReceive('skipPresenter')->once()->andReturnSelf();
        $scheduleRepoMock->shouldReceive('create')
            ->with([
                'order_item_id' => 1,
                'customer_id' => 1,
                'order_id' => 1,
                'provider_id' => 1,
                'ref_id' => 'ref1',
                'ref_parent_id' => 'parent1',
                'channel' => 'web',
                'status' => ScheduleStatusEnum::SCHEDULING_PENDING
            ])
            ->andReturn($mockSchedule);

        $logRepoMock = \Mockery::mock(ScheduleStatusLogRepository::class);
        $logRepoMock->shouldReceive('skipPresenter')->times(2)->andReturnSelf();
        $logRepoMock->shouldReceive('create')
            ->with([
                'schedule_id' => 1,
                'status' => ScheduleStatusEnum::SCHEDULING_PENDING,
                'author' => 'MM_SQS',
                'log' => 'Pagamento confirmado.'
            ])
            ->andReturn(['success' => true]);

        $bffMock = \Mockery::mock(OmniServiceBffIntegration::class);
        $bffMock->shouldReceive('send')->andReturn([]);

        $itemRepoMock = \Mockery::mock(OrderItemRepository::class);

        // Serviço com os mocks
        $service = new OrderService(
            $repositoryMock,
            $itemRepoMock,
            $scheduleRepoMock,
            $logRepoMock,
            $bffMock
        );

        // Execução e verificação
        $result = $service->paymentConfirmation($paymentData);
        $expectedResponse = ["data" => ["message" => "Pagamento confirmado com sucesso."]];
        $this->assertEquals($expectedResponse, $result);
    }

    public function test_it_should_verify_notifications()
    {
        $data = ['customer_id' => 1];
        $mockOrder = Mockery::mock();
        $mockOrder->id = 1;

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with([
                'customer_id' => 1,
                'notifications.status' => 'sent'
            ])
            ->andReturn(new Collection([$mockOrder]));

        $result = $this->orderService->findWhere([
            'customer_id' => 1,
            'notifications.status' => 'sent'
        ]);

        $this->assertNotEmpty($result);
    }

    public function test_it_should_return_false_when_no_notifications()
    {
        $data = ['customer_id' => 1];

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with([
                'customer_id' => 1,
                'notifications.status' => 'sent'
            ])
            ->andReturn(new Collection([]));

        $result = $this->orderService->findWhere([
            'customer_id' => 1,
            'notifications.status' => 'sent'
        ]);

        $this->assertEmpty($result);
    }
}
