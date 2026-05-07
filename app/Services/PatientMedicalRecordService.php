<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Criterias\FilterByPatientIdCriteria;
use App\Repositories\PatientMedicalRecordRepository;

/**
 * ScreeningService
 */
class PatientMedicalRecordService extends AppService
{
    /**
     * @var PatientMedicalRecordRepository
     */
    protected $repository;

    /**
     * @param PatientMedicalRecordRepository $repository
     */
    public function __construct(PatientMedicalRecordRepository $repository)
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



}
