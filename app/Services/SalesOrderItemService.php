<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Repositories\SalesOrderItemRepository;

/**
 * SalesOrderItemService
 */
class SalesOrderItemService extends AppService
{
    /**
     * @var SalesOrderItemRepository
     */
    protected $repository;

    /**
     * @param SalesOrderItemRepository $repository
     */
    public function __construct(SalesOrderItemRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $limit
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function all(int $limit = 20)
    {
        return $this->repository
            ->resetCriteria()
            ->pushCriteria(app(AppRequestCriteria::class))
            ->paginate($limit);
    }

}
