<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Repositories\ClinicalHistoryRepository;

/**
 * ClinicalHistoryService
 */
class ClinicalHistoryService extends AppService
{
    /**
     * @var ClinicalHistoryRepository
     */
    protected $repository;

    /**
     * @param ClinicalHistoryRepository $repository
     */
    public function __construct(ClinicalHistoryRepository $repository)
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
            ->pushCriteria(app(AppRequestCriteria::class))
            ->paginate($limit);
    }

}
