<?php

namespace App\Transformers;

use App\Entities\PaymentOrder;
use League\Fractal\TransformerAbstract;

/**
 * Class OrderTransformer.
 *
 * @package namespace App\Transformers;
 */
class PaymentOrderTransformer extends TransformerAbstract
{
    /**
     * Transform the Order entity.
     *
     * @param PaymentOrder $model
     *
     * @return array
     */
    public function transform(PaymentOrder $model): array
    {
        return [
            'id' => (int)$model->id,

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
