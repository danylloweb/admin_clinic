<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Criterias\FilterByPatientIdCriteria;
use App\Repositories\FollowUpMessageRepository;
use App\Repositories\PatientRepository;

/**
 * FollowUpMessageService
 */
class FollowUpMessageService extends AppService
{
    /**
     * @var FollowUpMessageRepository
     */
    protected $repository;
    protected PatientRepository $patientRepository;

    /**
     * @param FollowUpMessageRepository $repository
     * @param PatientRepository $patientRepository
     *
     */
    public function __construct(FollowUpMessageRepository $repository,PatientRepository $patientRepository, private WApiService $wapiService)
    {
        $this->repository = $repository;
        $this->patientRepository = $patientRepository;
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
     * @return string[]
     */
    public function sendMessageDirect(array $data): array
    {
        $message = $this->repository->skipPresenter()->find($data['follow_up_message_id']);
        $patient = $this->patientRepository->skipPresenter()->find($data['patient_id']);
        $messageSend = str_replace("[!!Paciente!!]", $patient->social_name, $message->message);
        $this->wapiService->sendText($patient->phone,$messageSend);
        return ['message'=> 'envidada'];
    }

}
