<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Criterias\FilterByPatientIdCriteria;
use App\Repositories\PatientRepository;
use Carbon\Carbon;

/**
 * PatientService
 */
class PatientService extends AppService
{
    /**
     * @var PatientRepository
     */
    protected $repository;

    /**
     * @param PatientRepository $repository
     */
    public function __construct(PatientRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $limit
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function all($limit = 20)
    {
        return $this->repository
            ->resetCriteria()
            ->pushCriteria(app(FilterByPatientIdCriteria::class))
            ->pushCriteria(app(AppRequestCriteria::class))
            ->paginate($limit);
    }

    /**
     * @param array $data
     * @param bool $skipPresenter
     * @return mixed
     */
    public function create(array $data, bool $skipPresenter = false)
    {
        $data['birth_date'] = Carbon::create($data['birth_date'])->format('Y-m-d');
        $data['chat_id']    = "55".str_replace(["(",")9","-"],'',$data['phone']). "@c.us";
        return parent::create($data, $skipPresenter);
    }

    /**
     * @param array $data
     * @param $id
     * @param bool $skipPresenter
     * @return array|mixed
     */
    public function update(array $data, $id, bool $skipPresenter = false)
    {
        $data['birth_date'] = Carbon::create($data['birth_date'])->format('Y-m-d');
        $data['chat_id']    = "55".str_replace(["(",")9","-"],'',$data['phone']). "@c.us";
        return parent::update($data, $id, $skipPresenter);
    }

    /**
     * @param $patient_id
     * @param $date_warning
     * @return void
     */
    public function sendWarning($patient_id, $date_warning)
    {
        $scape = ["(",")","-"];
        $patient = $this->repository->skipPresenter()->find($patient_id);
        $this->sendSMS([
            'number' => str_replace($scape,'', $patient->phone),
            'msg'    => "Renovar informa: Sua consulta foi agendada para o dia $date_warning"
        ]);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function verifyPatientPhone(array $data)
    {
//        if($this->repository->skipPresenter()->findWhere(['phone' => $data['phone']])->first()){
//            return ['error' => true, 'message'=> 'já existe um cadastro com esse numero'];
//        }else {
            return ['error' => false, 'message'=> 'ok'];
//        }
    }

    /**
     * @param $patient_id
     * @param $date_warning
     * @param $message_schedule
     * @return void
     */
    public function sendWarningWhatsApp($patient_id, $date_warning, $message_schedule)
    {
        try {
            $patient = $this->repository->skipPresenter()->find($patient_id);
            $message = $this->startOfMessage($patient->social_name, $date_warning);
            $message.= $message_schedule? ", $message_schedule.":"";
            $this->sendMessageToWhatsApp($patient->chat_id, $message);
        }catch (\Exception $exception){
            \Log::info($exception->getMessage());
            return;
        }

    }

    /**
     * @param $patient_name
     * @param $date_warning
     * @return string
     */
    private function startOfMessage($patient_name,$date_warning):string
    {
        return "Ola $patient_name, atendimento foi agendado para o dia $date_warning";
    }

    /**
     * @return mixed
     */
    public function getBirthdaysOfTheMonth()
    {
        return $this->repository->getBirthdaysOfTheMonth();
    }
}
