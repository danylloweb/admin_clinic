<?php

namespace App\Repositories;

use App\Presenters\ScreeningPresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ScreeningRepository;
use App\Entities\Screening;
use App\Validators\ScreeningValidator;

/**
 * Class ScreeningRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ScreeningRepositoryEloquent extends AppRepository implements ScreeningRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Screening::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {
        return ScreeningValidator::class;
    }

    /**
     * @return string
     */
    public function presenter()
    {
        return ScreeningPresenter::class;
    }

}
