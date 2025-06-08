<?php

namespace App\Repositories;

use App\Presenters\FollowUpScheduleMessagePresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\FollowUpScheduleMessageRepository;
use App\Entities\FollowUpScheduleMessage;
use App\Validators\FollowUpScheduleMessageValidator;

/**
 * Class FollowUpScheduleMessageRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FollowUpScheduleMessageRepositoryEloquent extends AppRepository implements FollowUpScheduleMessageRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return FollowUpScheduleMessage::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return FollowUpScheduleMessageValidator::class;
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
        return FollowUpScheduleMessagePresenter::class;
    }

}
