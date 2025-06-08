<?php

namespace Tests\Unit\Services;

use App\Criterias\AppRequestCriteria;
use App\Enums\ScheduleStatusEnum;
use App\Enums\OrderItemStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProfessionalRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\ScheduleStatusLogRepository;
use App\Services\ScheduleService;
use Tests\TestCase;
use Mockery;
use Illuminate\Database\Eloquent\Collection;
use App\Transformers\OrderItemEventCompletedTransformer;
use App\Transformers\OrderItemEventInProgressTransformer;
use App\Integrations\OmniServiceBffIntegration;
use App\Entities\NotificationTransactionLog;

class ScheduleServiceTest extends TestCase
{
    protected $scheduleService;
    protected $repository;
    protected $statusLogRepository;
    protected $orderItemRepository;
    protected $orderRepository;
    protected $professionalRepository;
    protected $bffIntegration;
    protected $notificationTransactionLogRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ScheduleRepository::class);
        $this->statusLogRepository = Mockery::mock(ScheduleStatusLogRepository::class);
        $this->orderItemRepository = Mockery::mock(OrderItemRepository::class);
        $this->orderRepository = Mockery::mock(OrderRepository::class);
        $this->professionalRepository = Mockery::mock(ProfessionalRepository::class);
        $this->bffIntegration = Mockery::mock(OmniServiceBffIntegration::class);
        $this->notificationTransactionLogRepository = Mockery::mock(NotificationTransactionLog::class);

        $this->scheduleService = new ScheduleService(
            $this->repository,
            $this->statusLogRepository,
            $this->orderItemRepository,
            $this->orderRepository,
            $this->professionalRepository,
            $this->bffIntegration,
            $this->notificationTransactionLogRepository
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_should_create_schedule()
    {
        $data = [
            'order_item_id' => 1,
            'customer_id' => 1,
            'order_id' => 1,
            'provider_id' => 1,
            'professional_id' => 1,
            'scheduled_date' => '2024-03-01',
            'scheduled_time' => '10:00:00',
            'status' => ScheduleStatusEnum::SCHEDULING_PENDING->value
        ];

        $expectedResult = (object)array_merge($data, ['id' => 1]);

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->scheduleService->create($data, true);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_return_all_schedules_paginated()
    {
        $limit = 20;
        $expectedResult = [
            'data' => [
                [
                    'id' => 1,
                    'order_item_id' => 1,
                    'customer_id' => 1,
                    'scheduled_date' => '2024-03-01',
                    'scheduled_time' => '10:00:00',
                    'status' => ScheduleStatusEnum::SCHEDULING_PENDING->value
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
            ->withAnyArgs()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('paginate')
            ->once()
            ->with($limit)
            ->andReturn($expectedResult);

        $result = $this->scheduleService->all($limit);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_update_schedule_status()
    {
        $scheduleId = 1;
        $newStatus = ScheduleStatusEnum::CONFIRMED;
        $updateData = [
            'status' => $newStatus->value,
            'status_updated_at' => now(),
            'status_updated_by' => 'system',
            'status_log' => 'Schedule confirmed automatically'
        ];

        $schedule = (object)[
            'id' => $scheduleId,
            'status' => ScheduleStatusEnum::SCHEDULING_PENDING->value
        ];

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('update')
            ->once()
            ->with($updateData, $scheduleId)
            ->andReturn($schedule);

        $result = $this->scheduleService->update($updateData, $scheduleId, true);
        $this->assertEquals($schedule, $result);
    }

    public function test_it_should_find_schedule_by_id()
    {
        $id = 1;
        $expectedResult = [
            'id' => $id,
            'order_item_id' => 1,
            'customer_id' => 1,
            'scheduled_date' => '2024-03-01',
            'scheduled_time' => '10:00:00',
            'status' => ScheduleStatusEnum::SCHEDULING_PENDING->value
        ];

        $this->repository->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($expectedResult);

        $result = $this->scheduleService->find($id);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_handle_invalid_status_update()
    {
        $scheduleId = 1;
        $updateData = [
            'status' => ScheduleStatusEnum::CONFIRMED->value,
            'status_updated_at' => now(),
            'status_updated_by' => 'system'
        ];

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('update')
            ->once()
            ->with($updateData, $scheduleId)
            ->andReturn(null);

        $result = $this->scheduleService->update($updateData, $scheduleId, true);
        $this->assertNull($result);
    }

    public function test_it_should_select_professional_successfully()
    {
        $scheduleId = 1;
        $professionalId = 2;
        $customerId = 3;
        $orderItemId = 4;

        $schedule = Mockery::mock('stdClass');
        $schedule->id = $scheduleId;
        $schedule->customer_id = $customerId;
        $schedule->professional_id = null;
        $schedule->order_item_id = $orderItemId;
        $schedule->status = ScheduleStatusEnum::SCHEDULED->value;
        $schedule->shouldReceive('save')->once()->andReturn(true);

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->andReturn(collect([$schedule]));

        $this->professionalRepository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->professionalRepository);

        $this->professionalRepository->shouldReceive('find')
            ->with($professionalId)
            ->andReturn((object)['id' => $professionalId]);

        $this->orderItemRepository->shouldReceive('setPresenter')
            ->once()
            ->andReturnSelf();

        $this->orderItemRepository->shouldReceive('find')
            ->with($orderItemId)
            ->andReturn((object)['id' => $orderItemId]);

        $this->statusLogRepository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->statusLogRepository);

        $this->statusLogRepository->shouldReceive('create')
            ->once()
            ->andReturn(true);

        $result = $this->scheduleService->selectProfessional([
            'schedule_id' => $scheduleId,
            'professional_id' => $professionalId
        ]);

        $this->assertEquals((object)['id' => $orderItemId], $result);
    }

    public function test_it_should_confirm_scheduling_successfully()
    {
        $scheduleId = 1;
        $customerId = 3;
        $orderId = 4;
        $orderItemId = 5;

        $schedule = Mockery::mock('stdClass');
        $schedule->id = $scheduleId;
        $schedule->customer_id = $customerId;
        $schedule->order_id = $orderId;
        $schedule->order_item_id = $orderItemId;
        $schedule->status = ScheduleStatusEnum::SCHEDULED->value;
        $schedule->shouldReceive('save')->once()->andReturn(true);

        $orderItem = Mockery::mock('stdClass');
        $orderItem->id = $orderItemId;
        $orderItem->status = OrderItemStatusEnum::SCHEDULED->value;
        $orderItem->shouldReceive('save')->once()->andReturn(true);

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->andReturn(collect([$schedule]));

        $this->orderItemRepository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->orderItemRepository);

        $this->orderItemRepository->shouldReceive('find')
            ->with($orderItemId)
            ->andReturn($orderItem);

        $this->statusLogRepository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->statusLogRepository);

        $this->statusLogRepository->shouldReceive('create')
            ->once()
            ->andReturn(true);

        $this->orderRepository->shouldReceive('modifyOrderStatusByItemStatus')
            ->once()
            ->andReturn(true);

        $result = $this->scheduleService->comfirmScheduling([
            'schedule_id' => $scheduleId
        ]);

        $this->assertEquals(['message' => 'Agendamento confirmado.'], $result);
    }

    public function test_it_should_set_scheduling_in_progress_successfully()
    {
        $scheduleId = 1;
        $customerId = 3;
        $orderId = 4;
        $orderItemId = 5;
        $professionalId = 6;

        // Mock de Schedule e Professional como objetos simples
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
        
        $professional = new \stdClass();
        $professional->id = $professionalId;
        $professional->name = 'John Doe';
        $professional->document = '12345678901';
        $professional->avatar_url = 'https://example.com/avatar.jpg';

        // Usando Mockery para o Schedule
        $schedule = Mockery::mock();
        $schedule->id = $scheduleId;
        $schedule->customer_id = $customerId;
        $schedule->order_id = $orderId;
        $schedule->order_item_id = $orderItemId;
        $schedule->professional_id = $professionalId;
        $schedule->status = ScheduleStatusEnum::SCHEDULED->value;
        $schedule->professional = $professional;
        $schedule->when_date = '2024-01-01';
        $schedule->when_time_start = '10:00';
        $schedule->when_time_end = '11:00';
        $schedule->shouldReceive('save')->andReturn(true);

        // Mock de ServiceConfiguration
        $serviceConfig = new \stdClass();
        $serviceConfig->slug = 'test-service';

        // Criando um mock que estende OrderItem para ser compatível com type hint
        $mockItem = Mockery::mock(\App\Entities\OrderItem::class);
        $mockItem->shouldReceive('save')->andReturn(true);
        $mockItem->shouldReceive('setAttribute')->andReturn(true);
        $mockItem->shouldReceive('getAttribute')
            ->with('serviceConfiguration')
            ->andReturn($serviceConfig);
        $mockItem->shouldReceive('getAttribute')
            ->andReturn(null);
        $mockItem->shouldReceive('__get')
            ->with('serviceConfiguration')
            ->andReturn($serviceConfig);
        $mockItem->shouldReceive('__isset')
            ->with('serviceConfiguration')
            ->andReturn(true);
        $mockItem->shouldReceive('getRelationValue')
            ->with('serviceConfiguration')
            ->andReturn($serviceConfig);
        $mockItem->shouldAllowMockingProtectedMethods();
        $mockItem->id = $orderItemId;
        $mockItem->status = \App\Enums\OrderItemStatusEnum::SCHEDULING_PENDING->value;
        $mockItem->serviceConfiguration = $serviceConfig;
        $mockItem->schedule = $schedule;
        $mockItem->external_order_id = 'ORDER123';
        $mockItem->ref_id = 'REF123';
        $mockItem->ref_parent_id = 'PARENT123';
        $mockItem->ref_description = 'Service Description';
        $mockItem->ref_parent_description = 'Parent Description';
        $mockItem->ref_image_url = 'https://example.com/image.jpg';
        $mockItem->ref_parent_image_url = 'https://example.com/parent-image.jpg';
        $mockItem->quantity = 1;
        $mockItem->price = 99.90;

        // Criamos uma coleção com o schedule
        $scheduleCollection = collect([$schedule]);

        // Expectativas do repositório
        $this->repository->shouldReceive('skipPresenter')
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with([
                'id' => $scheduleId,
                ['status','IN',[
                    ScheduleStatusEnum::SCHEDULED->value,
                    ScheduleStatusEnum::CONFIRMED->value,
                    ScheduleStatusEnum::PRE_EXECUTION->value
                ]],
                ['professional_id' ,'!=', null]
            ])
            ->andReturn($scheduleCollection);

        $this->orderItemRepository->shouldReceive('skipPresenter')
            ->times(2)
            ->andReturn($this->orderItemRepository);

        $this->orderItemRepository->shouldReceive('find')
            ->with($orderItemId)
            ->times(2)
            ->andReturn($mockItem);

        $this->statusLogRepository->shouldReceive('skipPresenter')
            ->andReturn($this->statusLogRepository);

        $this->statusLogRepository->shouldReceive('create')
            ->once()
            ->with([
                'schedule_id' => $scheduleId,
                'status' => ScheduleStatusEnum::IN_PROGRESS,
                'author' => "customer_id:{$customerId}",
                'log' => "John Doe está trabalhando no(a) test-service",
            ])
            ->andReturn(true);

        $this->orderRepository->shouldReceive('modifyOrderStatusByItemStatus')
            ->once()
            ->with($orderId, \App\Enums\OrderItemStatusEnum::IN_PROGRESS->value, \App\Enums\OrderStatusEnum::IN_PROGRESS->value)
            ->andReturn(true);

        // Mockamos o transformador para retornar dados simples
        $mockTransformer = \Mockery::mock(\App\Transformers\OrderItemEventInProgressTransformer::class);
        $mockTransformer->shouldReceive('transform')
            ->with($mockItem)
            ->andReturn([
                'external_order_id' => 'ORDER123',
                'order_item_id' => $orderItemId,
                'event_name' => 'SERVICE_HUB_WORKER_ARRIVING',
                'attributes' => []
            ]);

        app()->instance(\App\Transformers\OrderItemEventInProgressTransformer::class, $mockTransformer);

        // Executamos o teste
        $result = $this->scheduleService->inProgressScheduling([
            'schedule_id' => $scheduleId
        ]);

        $this->assertIsArray($result);
    }

    public function test_it_should_complete_scheduling_successfully()
    {
        $scheduleId = 1;
        $customerId = 3;
        $orderId = 4;
        $orderItemId = 5;
        $professionalId = 6;

        // Mock de Schedule e Professional como objetos simples
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
        
        $professional = new \stdClass();
        $professional->id = $professionalId;
        $professional->name = 'John Doe';
        $professional->document = '12345678901';
        $professional->avatar_url = 'https://example.com/avatar.jpg';

        // Mock de ServiceConfiguration
        $serviceConfig = new \stdClass();
        $serviceConfig->slug = 'test-service';

        // Usando Mockery para o Schedule
        $schedule = Mockery::mock();
        $schedule->id = $scheduleId;
        $schedule->customer_id = $customerId;
        $schedule->order_id = $orderId;
        $schedule->order_item_id = $orderItemId;
        $schedule->professional_id = $professionalId;
        $schedule->status = ScheduleStatusEnum::IN_PROGRESS->value;
        $schedule->professional = $professional;
        $schedule->when_date = '2024-01-01';
        $schedule->when_time_start = '10:00';
        $schedule->when_time_end = '11:00';
        $schedule->shouldReceive('save')->andReturn(true);

        // Criando um mock que estende OrderItem para ser compatível com type hint
        $mockItem = Mockery::mock(\App\Entities\OrderItem::class);
        $mockItem->shouldReceive('save')->andReturn(true);
        $mockItem->shouldReceive('setAttribute')->andReturn(true);
        $mockItem->shouldReceive('getAttribute')
            ->with('serviceConfiguration')
            ->andReturn($serviceConfig);
        $mockItem->shouldReceive('getAttribute')
            ->andReturn(null);
        $mockItem->shouldReceive('__get')
            ->with('serviceConfiguration')
            ->andReturn($serviceConfig);
        $mockItem->shouldReceive('__isset')
            ->with('serviceConfiguration')
            ->andReturn(true);
        $mockItem->shouldReceive('getRelationValue')
            ->with('serviceConfiguration')
            ->andReturn($serviceConfig);
        $mockItem->shouldAllowMockingProtectedMethods();
        $mockItem->id = $orderItemId;
        $mockItem->status = \App\Enums\OrderItemStatusEnum::SCHEDULING_PENDING->value;
        $mockItem->serviceConfiguration = $serviceConfig;
        $mockItem->schedule = $schedule;
        $mockItem->external_order_id = 'ORDER123';
        $mockItem->ref_id = 'REF123';
        $mockItem->ref_parent_id = 'PARENT123';
        $mockItem->ref_description = 'Service Description';
        $mockItem->ref_parent_description = 'Parent Description';
        $mockItem->ref_image_url = 'https://example.com/image.jpg';
        $mockItem->ref_parent_image_url = 'https://example.com/parent-image.jpg';
        $mockItem->quantity = 1;
        $mockItem->price = 99.90;

        // Criamos uma coleção com o schedule
        $scheduleCollection = collect([$schedule]);

        // Expectativas do repositório
        $this->repository->shouldReceive('skipPresenter')
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with([
                'id' => $scheduleId,
                'status' => ScheduleStatusEnum::IN_PROGRESS->value
            ])
            ->andReturn($scheduleCollection);

        $this->orderItemRepository->shouldReceive('skipPresenter')
            ->twice()
            ->andReturn($this->orderItemRepository);

        $this->orderItemRepository->shouldReceive('find')
            ->with($orderItemId)
            ->twice()
            ->andReturn($mockItem);

        $this->statusLogRepository->shouldReceive('skipPresenter')
            ->andReturn($this->statusLogRepository);

        $this->statusLogRepository->shouldReceive('create')
            ->once()
            ->with([
                'schedule_id' => $scheduleId,
                'status' => ScheduleStatusEnum::COMPLETED,
                'author' => "customer_id:{$customerId}",
                'log' => "Agendamento finalizado.",
            ])
            ->andReturn(true);

        $this->orderRepository->shouldReceive('modifyOrderStatusByItemStatus')
            ->once()
            ->with($orderId, \App\Enums\OrderItemStatusEnum::COMPLETED->value, \App\Enums\OrderStatusEnum::COMPLETED->value)
            ->andReturn(true);

        // Mockamos o transformador para retornar dados simples
        $mockTransformer = \Mockery::mock(\App\Transformers\OrderItemEventCompletedTransformer::class);
        $mockTransformer->shouldReceive('transform')
            ->with($mockItem)
            ->andReturn([
                'external_order_id' => 'ORDER123',
                'order_item_id' => $orderItemId,
                'event_name' => 'SERVICE_HUB_VISIT_COMPLETED',
                'attributes' => []
            ]);

        app()->instance(\App\Transformers\OrderItemEventCompletedTransformer::class, $mockTransformer);

        // Executamos o teste
        $result = $this->scheduleService->completeScheduling([
            'schedule_id' => $scheduleId
        ]);

        $this->assertIsArray($result);
    }

    public function test_it_should_cancel_scheduling_successfully()
    {
        $scheduleId = 1;
        $customerId = 3;
        $orderId = 4;
        $orderItemId = 5;

        $schedule = Mockery::mock('stdClass');
        $schedule->id = $scheduleId;
        $schedule->customer_id = $customerId;
        $schedule->order_id = $orderId;
        $schedule->order_item_id = $orderItemId;
        $schedule->status = ScheduleStatusEnum::CONFIRMED->value;
        $schedule->shouldReceive('save')->once()->andReturn(true);

        $orderItem = Mockery::mock('stdClass');
        $orderItem->id = $orderItemId;
        $orderItem->status = OrderItemStatusEnum::CONFIRMED->value;
        $orderItem->shouldReceive('save')->once()->andReturn(true);

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->andReturn(collect([$schedule]));

        $this->orderItemRepository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->orderItemRepository);

        $this->orderItemRepository->shouldReceive('find')
            ->with($orderItemId)
            ->andReturn($orderItem);

        $this->statusLogRepository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->statusLogRepository);

        $this->statusLogRepository->shouldReceive('create')
            ->once()
            ->andReturn(true);

        $this->orderRepository->shouldReceive('modifyOrderStatusByItemStatus')
            ->once()
            ->andReturn(true);

        $result = $this->scheduleService->cancelScheduling([
            'schedule_id' => $scheduleId
        ]);

        $this->assertEquals(['message' => 'Agendamento cancelado.'], $result);
    }

    public function test_it_should_not_select_professional_when_schedule_not_found()
    {
        $scheduleId = 1;
        $professionalId = 2;

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->andReturn(collect([]));

        $result = $this->scheduleService->selectProfessional([
            'schedule_id' => $scheduleId,
            'professional_id' => $professionalId
        ]);

        $this->assertEquals(['message' => 'Profissional não pode ser selecionado.'], $result);
    }

    public function test_it_should_not_select_professional_when_professional_not_found()
    {
        $scheduleId = 1;
        $professionalId = 2;
        $customerId = 3;

        $schedule = Mockery::mock('stdClass');
        $schedule->id = $scheduleId;
        $schedule->customer_id = $customerId;
        $schedule->professional_id = 1;
        $schedule->status = ScheduleStatusEnum::SCHEDULED->value;

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->andReturn(collect([$schedule]));

        $this->professionalRepository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->professionalRepository);

        $this->professionalRepository->shouldReceive('find')
            ->with($professionalId)
            ->andReturn(null);

        $result = $this->scheduleService->selectProfessional([
            'schedule_id' => $scheduleId,
            'professional_id' => $professionalId
        ]);

        $this->assertEquals(['message' => 'Profissional inativo.'], $result);
    }

    public function test_it_should_not_confirm_scheduling_when_schedule_not_found()
    {
        $scheduleId = 1;

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->andReturn(collect([]));

        $result = $this->scheduleService->comfirmScheduling([
            'schedule_id' => $scheduleId
        ]);

        $this->assertEquals(['error' => true, 'message' => 'Agendamento não pode ser Confirmado.'], $result);
    }

    public function test_it_should_not_set_in_progress_when_schedule_not_found()
    {
        $scheduleId = 1;

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->andReturn(collect([]));

        $result = $this->scheduleService->inProgressScheduling(['schedule_id' => $scheduleId]);
        $this->assertEquals(['error' => true, 'message' => 'Agendamento não pode ser Iniciado.'], $result);
    }

    public function test_it_should_not_complete_scheduling_when_schedule_not_found()
    {
        $scheduleId = 1;

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->andReturn(collect([]));

        $result = $this->scheduleService->completeScheduling(['schedule_id' => $scheduleId]);
        $this->assertEquals(['error' => true, 'message' => 'Agendamento não pode ser finalizado.'], $result);
    }

    public function test_it_should_not_cancel_scheduling_when_schedule_not_found()
    {
        $scheduleId = 1;

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->andReturn(collect([]));

        $result = $this->scheduleService->cancelScheduling([
            'schedule_id' => $scheduleId
        ]);

        $this->assertEquals(['error' => true, 'message' => 'Agendamento não pode ser cancelado.'], $result);
    }

    public function test_it_should_not_select_same_professional_twice()
    {
        $scheduleId = 1;
        $professionalId = 2;
        $customerId = 3;

        $schedule = Mockery::mock('stdClass');
        $schedule->id = $scheduleId;
        $schedule->customer_id = $customerId;
        $schedule->professional_id = $professionalId;
        $schedule->status = ScheduleStatusEnum::SCHEDULED->value;

        $this->repository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->repository);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->andReturn(collect([$schedule]));

        $this->professionalRepository->shouldReceive('skipPresenter')
            ->once()
            ->andReturn($this->professionalRepository);

        $this->professionalRepository->shouldReceive('find')
            ->with($professionalId)
            ->andReturn((object)['id' => $professionalId]);

        $result = $this->scheduleService->selectProfessional([
            'schedule_id' => $scheduleId,
            'professional_id' => $professionalId
        ]);

        $this->assertEquals(['message' => 'Profissional já selecionado.'], $result);
    }
} 