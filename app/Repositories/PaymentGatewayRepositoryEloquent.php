<?php

namespace App\Repositories;

use App\Entities\PaymentGateway;

/**
 * Class GatewayRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PaymentGatewayRepositoryEloquent extends AppRepository implements PaymentGatewayRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return PaymentGateway::class;
    }
}
