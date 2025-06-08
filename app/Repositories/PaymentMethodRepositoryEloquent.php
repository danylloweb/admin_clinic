<?php

namespace App\Repositories;

use App\Entities\PaymentMethod;

/**
 * Class PaymentMethodRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PaymentMethodRepositoryEloquent extends AppRepository implements PaymentMethodRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return PaymentMethod::class;
    }
}
