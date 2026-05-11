<?php

namespace App\Repositories;

use App\Presenters\FacialEvaluationPresenter;
use App\Validators\FacialEvaluationValidator;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\BodyEvaluationRepository;
use App\Entities\BodyEvaluation;
use App\Validators\BodyEvaluationValidator;

/**
 * Class BodyEvaluationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BodyEvaluationRepositoryEloquent extends AppRepository implements BodyEvaluationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BodyEvaluation::class;
    }


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
