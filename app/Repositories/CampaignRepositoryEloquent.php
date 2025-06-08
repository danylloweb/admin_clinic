<?php

namespace App\Repositories;

use App\Presenters\CampaignPresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CampaignRepository;
use App\Entities\Campaign;
use App\Validators\CampaignValidator;

/**
 * Class CampaignRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CampaignRepositoryEloquent extends AppRepository implements CampaignRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Campaign::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {

        return CampaignValidator::class;
    }

    /**
     * @return string
     */
    public function presenter()
    {
        return CampaignPresenter::class;
    }

}
