<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Criterias\FilterByPatientIdCriteria;
use App\Repositories\FacialEvaluationRepository;
use App\Repositories\PatientMedicalRecordRepository;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * FacialEvaluationService
 */
class FacialEvaluationService extends AppService
{
    /**
     * @var FacialEvaluationRepository
     */
    protected $repository;

    /**
     * @param FacialEvaluationRepository $repository
     */
    public function __construct(FacialEvaluationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $limit
     * @return mixed
     * @throws RepositoryException
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
