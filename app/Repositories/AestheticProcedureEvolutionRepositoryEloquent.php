<?php

namespace App\Repositories;

use App\Repositories\AestheticProcedureEvolutionRepository;
use App\Entities\AestheticProcedureEvolution;
use App\Presenters\AestheticProcedureEvolutionPresenter;
use App\Validators\AestheticProcedureEvolutionValidator;

/**
 * Class AestheticProcedureEvolutionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AestheticProcedureEvolutionRepositoryEloquent extends AppRepository implements AestheticProcedureEvolutionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AestheticProcedureEvolution::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return AestheticProcedureEvolutionValidator::class;
    }


    public function presenter()
    {
        return AestheticProcedureEvolutionPresenter::class;
    }

}
