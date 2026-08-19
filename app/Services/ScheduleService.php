<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Criterias\FilterByDateEndScheduleCriteria;
use App\Criterias\FilterByDateScheduleCriteria;
use App\Criterias\FilterByDateStartScheduleCriteria;
use App\Criterias\FilterByPatientScheduleCriteria;
use App\Criterias\FilterByProfessionalScheduleCriteria;
use App\Criterias\FilterByProcedureScheduleCriteria;
use App\Criterias\FilterByStatusCriteria;
use App\Criterias\FilterByTypeProcedureScheduleCriteria;
use App\Presenters\ScheduleCalendarPresenter;
use App\Repositories\ProcedureRepository;
use App\Repositories\SalesOrderItemRepository;
use App\Repositories\SalesOrderRepository;
use App\Repositories\ScheduleRepository;
use Carbon\Carbon;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * ScheduleService
 */
class ScheduleService extends AppService
{
    /**
     * @var ScheduleRepository
     */
    protected $repository;
    /**
     * @var PatientService
     */
    protected PatientService $patientService;
    /**
     * @var ProcedureRepository
     */
    protected ProcedureRepository $procedureRepository;
    /**
     * @var SalesOrderItemRepository
     */
    protected SalesOrderItemRepository $salesOrderItemRepository;
    /**
     * @var SalesOrderRepository
     */
    protected SalesOrderRepository $salesOrderRepository;

    /**
     * @param ScheduleRepository $repository
     * @param PatientService $patientService
     * @param ProcedureRepository $procedureRepository
     * @param SalesOrderItemRepository $salesOrderItemRepository
     */
    public function __construct(ScheduleRepository  $repository,
                                PatientService      $patientService,
                                ProcedureRepository $procedureRepository,
                                SalesOrderItemRepository $salesOrderItemRepository,
                                SalesOrderRepository $salesOrderRepository, private WApiService $wapiService)
    {
        $this->repository               = $repository;
        $this->patientService           = $patientService;
        $this->procedureRepository      = $procedureRepository;
        $this->salesOrderItemRepository = $salesOrderItemRepository;
        $this->salesOrderRepository     = $salesOrderRepository;
    }

    /**
     * @param int $limit
     * @return mixed
     * @throws RepositoryException
     */
    public function all(int $limit = 9999): mixed
    {
        $response =  $this->repository
            ->resetCriteria()
            ->pushCriteria(app(FilterByTypeProcedureScheduleCriteria::class))
            ->pushCriteria(app(FilterByProcedureScheduleCriteria::class))
            ->pushCriteria(app(FilterByStatusCriteria::class))
            ->pushCriteria(app(FilterByPatientScheduleCriteria::class))
            ->pushCriteria(app(FilterByProfessionalScheduleCriteria::class))
//            ->pushCriteria(app(FilterByDateScheduleCriteria::class))
            ->pushCriteria(app(FilterByDateStartScheduleCriteria::class))
            ->pushCriteria(app(FilterByDateEndScheduleCriteria::class))
            ->pushCriteria(app(AppRequestCriteria::class))
            ->paginate($limit);

        $result         = $response['data'];
        $total_price    = 0;
        $total_cost     = 0;
        $estimate_price = 0;

        foreach ($result as $item) {
            if ($item['status'] == "Confirmado"){
                $total_price = $total_price + $item['procedure_price'];
                $total_cost  = $total_cost  + $item['procedure_price_cost'];
            }
            if ($item['status'] == "Marcado"){
                $estimate_price = $estimate_price + $item['procedure_price'];
            }
        }

        $percent_to_professional = $total_price * 0.40;

        $response['total'] = [
            'total_price'   => number_format($total_price,2,',','.'),
            'total_cost'    => number_format($total_cost,2,',','.'),
            'estimate_cost' => number_format($estimate_price,2,',','.'),
            'percent_to_professional' => number_format($percent_to_professional,2,',','.')
        ];
        return $response;
    }

    /**
     * @param array $data
     * @param bool $skipPresenter
     * @return mixed
     */
    public function create(array $data, bool $skipPresenter = false): mixed
    {
        $date_warning = $data['date'] ." ". $data['time'];
        $data['date'] = Carbon::create($data['date'])->format('Y-m-d');
        $data['time'] = Carbon::create($data['time'])->format('H:i:s');
        $schedule     = parent::create($data, $skipPresenter);
        $procedure    = $this->getProcedure($data['procedure_id']);

        if ($data['send_message'] == 'send'){
            $date_warning = Carbon::create($date_warning)->format('d/m/Y H:i:s');
            $this->patientService->sendWarningWhatsApp($data['patient_id'], $date_warning,$procedure->message_schedule??null);
        }

//        $this->sendNotificationToEmployers($data['date'], $data['time'], $procedure->name);
        return $schedule;
    }

    /**
     * @param array $data
     * @param $id
     * @param bool $skipPresenter
     * @return array|mixed
     */
    public function update(array $data, $id, bool $skipPresenter = false): mixed
    {
        $data['date'] = Carbon::create($data['date'])->format('Y-m-d');
        $data['time'] = Carbon::create($data['time'])->format('H:i:s');

        $result = parent::update($data, $id, $skipPresenter);

        // If procedure changed, sync procedure_name on linked SalesOrderItem
        if (!empty($data['procedure_id'])) {
            $item = $this->salesOrderItemRepository->skipPresenter()->findWhere(['schedule_id' => $id])->first();
            if ($item) {
                $procedure = $this->getProcedure((int) $data['procedure_id']);
                $item->procedure_name = $procedure->name;
                $item->save();
            }
        }

        return $result;
    }

    /**
     * @param int $procedure_id
     * @return mixed
     */
    private function getProcedure(int $procedure_id):mixed
    {
        return $this->procedureRepository->skipPresenter()->find($procedure_id);
    }

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function updateStatus(int $id, array $data): mixed
    {
        $data_update = ['status' => $data['status'], 'observation_status' => $data['observation_status']];
        if ($data['status'] === "Confirmado"){
            $data_update['professional_id'] = $data['professional_id'];

            $schedule = $this->find($id,true);
            $message  = $schedule->procedure->message_schedule_after;

            if (!$this->isEmpty($message)){
                $this->wapiService->sendText($schedule->patient->phone, $message);
            }
        }
        return parent::update($data_update, $id);
    }

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function scheduleItem(int $id, array $data): mixed
    {
        $item = $this->salesOrderItemRepository->skipPresenter()->find($id);
        $data_schedule = [
            'date'         => $data['date_schedule'],
            'time'         => $data['time_schedule'],
            'procedure_id' => $item->procedure_id,
            'patient_id'   => $item->getPatientId(),
            'send_message' => $data['send_message']??'not',
        ];
        $schedule = $this->create($data_schedule,false);
        $item->schedule_id = $schedule['data']['id'];
        $item->save();
        return $schedule;
    }

    /**
     * @param $date_schedule
     * @param $time
     * @param string $procedure_name
     * @return void
     */
//    private function sendNotificationToEmployers($date_schedule, $time, string $procedure_name): void
//    {
//        try {
//            $dataSchedule = Carbon::createFromFormat('Y-m-d', $date_schedule);
//
//            $now   = Carbon::now(); // Obtém a data e hora atual usando Carbon
//            $start = $now->copy()->setHour(9)->setMinute(0)->setSecond(0); // Define a hora de início como 09:00 da manhã
//            $final = $now->copy()->setHour(21)->setMinute(0)->setSecond(0); // Define a hora de término como 21:00 da noite
//            // Verifica se a hora atual está entre o início e o fim
//            if ($now->between($start, $final) && $dataSchedule->isSameDay($now)) {
//                $message = "Atenção um novo agendamento de $procedure_name para hoje as $time ";
//                $this->sendMessageToWhatsApp("120363146408206361@g.us", $message);
//            }
//        } catch (\Exception $exception) {
//            \Log::info($exception->getMessage());
//            return;
//        }
//    }

    public function calendar(): mixed
    {
        return $this->repository
            ->resetCriteria()
            ->pushCriteria(app(FilterByDateStartScheduleCriteria::class))
            ->pushCriteria(app(FilterByDateEndScheduleCriteria::class))
            ->pushCriteria(app(AppRequestCriteria::class))
            ->setPresenter(app(ScheduleCalendarPresenter::class))
            ->all();
    }
}
