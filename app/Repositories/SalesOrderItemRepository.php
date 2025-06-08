<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface SalesOrderItemRepository.
 *
 * @package namespace App\Repositories;
 */
interface SalesOrderItemRepository extends RepositoryInterface
{
    public function removeAllBySalesOrderId(int $salesOrderId);
    //
}
