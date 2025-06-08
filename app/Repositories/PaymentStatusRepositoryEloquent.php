<?php

namespace App\Repositories;

use App\Entities\PaymentStatus;

/**
 * Class PaymentStatusRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PaymentStatusRepositoryEloquent extends AppRepository implements PaymentStatusRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return PaymentStatus::class;
    }
}
