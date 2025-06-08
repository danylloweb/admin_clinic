<?php

namespace App\Repositories;

use App\Presenters\ProcedurePresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ProcedureRepository;
use App\Entities\Procedure;
use App\Validators\ProcedureValidator;

/**
 * Class ProcedureRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProcedureRepositoryEloquent extends AppRepository implements ProcedureRepository
{

    protected $fieldSearchable = [
        'name' => 'like'
    ];

    /**
     * Regras para busca
     *
     * @var array
     */
    protected $fieldsRules = [
        'name' => ['string', 'max:20']
    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Procedure::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {
        return ProcedureValidator::class;
    }

    /**
     * @return string
     */
   public function presenter()
   {
       return ProcedurePresenter::class;
   }

}
