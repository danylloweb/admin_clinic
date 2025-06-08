<?php

namespace App\Repositories;

use App\Entities\PaymentOrder;
use App\Presenters\PaymentOrderPresenter;

/**
 * Class OrderRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PaymentOrderRepositoryEloquent extends AppRepository implements PaymentOrderRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return PaymentOrder::class;
    }

    /**
     * @return string
     */
    public function presenter(): string
    {
        return PaymentOrderPresenter::class;
    }
}
