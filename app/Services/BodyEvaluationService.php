<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Criterias\FilterByPatientIdCriteria;
use App\Repositories\BodyEvaluationRepository;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * BodyEvaluationService
 */
class BodyEvaluationService extends AppService
{
    /**
     * @var BodyEvaluationRepository
     */
    protected $repository;

    /**
     * @param BodyEvaluationRepository $repository
     */
    public function __construct(BodyEvaluationRepository $repository)
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


