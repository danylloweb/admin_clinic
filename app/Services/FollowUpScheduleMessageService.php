<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Criterias\FilterByPatientIdCriteria;
use App\Repositories\FollowUpScheduleMessageRepository;

/**
 * FollowUpScheduleMessageService
 */
class FollowUpScheduleMessageService extends AppService
{
    /**
     * @var FollowUpScheduleMessageRepository
     */
    protected $repository;

    /**
     * @param FollowUpScheduleMessageRepository $repository
     */
    public function __construct(FollowUpScheduleMessageRepository $repository)
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
