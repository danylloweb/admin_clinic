<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\SalesOrderItem;

/**
 * Class SalesOrderItemTransformer.
 *
 * @package namespace App\Transformers;
 */
class SalesOrderItemTransformer extends TransformerAbstract
{
    /**
     * Transform the SalesOrderItem entity.
     *
     * @param \App\Entities\SalesOrderItem $model
     *
     * @return array
     */
    public function transform(SalesOrderItem $model)
    {
        return [
            'id'            => (int) $model->id,
            'sales_order_id'=> $model->sales_order_id,
            'procedure_id'  => $model->procedure_id,
            'procedure_name'=> $model->procedure_name,
            'price'         => $model->price,
            'qty'           => $model->qty,
            'created_at'    => $model->created_at->toDateTimeString(),
            'updated_at'    => $model->updated_at->toDateTimeString()
        ];
    }
}
