<?php

namespace App\Repositories;

use App\Presenters\LeadPresenter;
use App\Repositories\LeadRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\Lead;
use App\Validators\LeadValidator;

/**
 * Class LeadRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class LeadRepositoryEloquent extends AppRepository implements LeadRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Lead::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {

        return LeadValidator::class;
    }

    /**
     * @return string
     */
    public function presenter()
    {
        return LeadPresenter::class;
    }

}
