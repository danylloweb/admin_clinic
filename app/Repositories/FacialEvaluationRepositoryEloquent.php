<?php

namespace App\Repositories;

use App\Presenters\FacialEvaluationPresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\FacialEvaluationRepository;
use App\Entities\FacialEvaluation;
use App\Validators\FacialEvaluationValidator;

/**
 * Class FacialEvaluationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FacialEvaluationRepositoryEloquent extends AppRepository implements FacialEvaluationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return FacialEvaluation::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return FacialEvaluationValidator::class;
    }

    /**
     * @return string
     */
   public function presenter()
   {
       return FacialEvaluationPresenter::class;
   }

}
