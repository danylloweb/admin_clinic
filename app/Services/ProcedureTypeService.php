<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Repositories\ProcedureRepository;
use App\Repositories\ProcedureTypeRepository;

/**
 * ProcedureTypeService
 */
class ProcedureTypeService extends AppService
{
    /**
     * @var ProcedureTypeRepository
     */
    protected $repository;

    /**
     * @param ProcedureTypeRepository $repository
     */
    public function __construct(ProcedureTypeRepository $repository)
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
