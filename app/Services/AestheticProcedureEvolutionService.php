<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Criterias\FilterByPatientIdCriteria;
use App\Repositories\AestheticProcedureEvolutionRepository;
use Prettus\Repository\Exceptions\RepositoryException;

class AestheticProcedureEvolutionService extends AppService
{
    protected $repository;

    public function __construct(AestheticProcedureEvolutionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
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

