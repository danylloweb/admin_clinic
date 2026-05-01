<?php

namespace App\Repositories;

use App\Presenters\SalesOrderPresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\SalesOrderRepository;
use App\Entities\SalesOrder;
use App\Validators\SalesOrderValidator;

/**
 * Class SalesOrderRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SalesOrderRepositoryEloquent extends AppRepository implements SalesOrderRepository
{
    protected $fieldSearchable = [
        'id' => '=',
        'patient.name' => 'like',
        'patient.social_name' => 'like',
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SalesOrder::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return SalesOrderValidator::class;
    }

    /**
     * @return string
     */
    public function presenter()
    {
        return SalesOrderPresenter::class;
    }

}
