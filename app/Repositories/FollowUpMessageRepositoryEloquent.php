<?php

namespace App\Repositories;

use App\Presenters\FollowUpMessagePresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\FollowUpMessageRepository;
use App\Entities\FollowUpMessage;
use App\Validators\FollowUpMessageValidator;

/**
 * Class FollowUpMessageRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FollowUpMessageRepositoryEloquent extends AppRepository implements FollowUpMessageRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return FollowUpMessage::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return FollowUpMessageValidator::class;
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
        return FollowUpMessagePresenter::class;
    }

}
