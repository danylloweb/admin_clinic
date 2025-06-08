<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\FollowUpSchedule;

/**
 * Class FollowUpScheduleTransformer.
 *
 * @package namespace App\Transformers;
 */
class FollowUpScheduleTransformer extends TransformerAbstract
{
    /**
     * Transform the FollowUpSchedule entity.
     *
     * @param \App\Entities\FollowUpSchedule $model
     *
     * @return array
     */
    public function transform(FollowUpSchedule $model)
    {
        return [
            'id'                    => (int) $model->id,
            'patient_id'            => $model->patient_id,
            'patient_name'          => $model->patient->name,
            'sales_order_id'        => $model->sales_order_id,
            'status'                => $model->status,
            'qty_messages'          => $model->qty_messages,
            'completion_percentage' => $model->completion_percentage,
            'user_id'               => $model->user_id,
            'user_name'             => $model->user->name,
            'created_at'            => $model->created_at->toDateTimeString(),
            'updated_at'            => $model->updated_at->toDateTimeString()
        ];
    }
}
