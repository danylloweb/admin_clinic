<?php

namespace App\Repositories;

use App\Presenters\ProcedureTypePresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ProcedureTypeRepository;
use App\Entities\ProcedureType;
use App\Validators\ProcedureTypeValidator;

/**
 * Class ProcedureTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProcedureTypeRepositoryEloquent extends AppRepository implements ProcedureTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProcedureType::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return ProcedureTypeValidator::class;
    }

    /**
     * @return string
     */
   public function presenter()
   {
       return ProcedureTypePresenter::class;
   }

}
