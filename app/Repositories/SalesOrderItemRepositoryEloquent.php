<?php

namespace App\Repositories;

use App\Presenters\SalesOrderItemPresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\SalesOrderItemRepository;
use App\Entities\SalesOrderItem;
use App\Validators\SalesOrderItemValidator;

/**
 * Class SalesOrderItemRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SalesOrderItemRepositoryEloquent extends AppRepository implements SalesOrderItemRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SalesOrderItem::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return SalesOrderItemValidator::class;
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
        return SalesOrderItemPresenter::class;
    }

    public function removeAllBySalesOrderId(int $salesOrderId)
    {
        $query = $this->model();
        return $query::where('sales_order_id',$salesOrderId)->delete();
    }

}
