<?php

namespace App\Repositories;

use App\Presenters\AdvertPresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AdvertRepository;
use App\Entities\Advert;
use App\Validators\AdvertValidator;

/**
 * Class AdvertRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AdvertRepositoryEloquent extends AppRepository implements AdvertRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Advert::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return AdvertValidator::class;
    }


    public function presenter()
    {
        return AdvertPresenter::class;
    }

}
