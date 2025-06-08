<?php

namespace App\Repositories;

use App\Presenters\FollowUpSchedulePresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\FollowUpScheduleRepository;
use App\Entities\FollowUpSchedule;
use App\Validators\FollowUpScheduleValidator;

/**
 * Class FollowUpScheduleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FollowUpScheduleRepositoryEloquent extends AppRepository implements FollowUpScheduleRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return FollowUpSchedule::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return FollowUpScheduleValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @return string
     */
    public function presenter()
    {
        return FollowUpSchedulePresenter::class;
    }
}
