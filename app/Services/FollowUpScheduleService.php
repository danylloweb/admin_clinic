<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Criterias\FilterByPatientIdCriteria;
use App\Repositories\FollowUpScheduleRepository;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * FollowUpScheduleService
 */
class FollowUpScheduleService extends AppService
{
    /**
     * @var FollowUpScheduleRepository
     */
    protected $repository;

    /**
     * @param FollowUpScheduleRepository $repository
     */
    public function __construct(FollowUpScheduleRepository $repository)
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
