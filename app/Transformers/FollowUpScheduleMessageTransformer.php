<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\FollowUpScheduleMessage;

/**
 * Class FollowUpScheduleMessageTransformer.
 *
 * @package namespace App\Transformers;
 */
class FollowUpScheduleMessageTransformer extends TransformerAbstract
{
    /**
     * Transform the FollowUpScheduleMessage entity.
     *
     * @param \App\Entities\FollowUpScheduleMessage $model
     *
     * @return array
     */
    public function transform(FollowUpScheduleMessage $model)
    {
        return [
            'id'                    => (int) $model->id,
            'patient_id'            => $model->patient_id,
            'patient_name'          => $model->patient->name,
            'sales_order_id'        => $model->sales_order_id,
            'follow_up_message_id'  => $model->follow_up_message_id,
            'follow_up_message'     => $model->followUpMessage,
            'date'                  => $model->date,
            'time'                  => $model->time,
            'status'                => $model->status,
            'created_at'            => $model->created_at->toDateTimeString(),
            'updated_at'            => $model->updated_at->toDateTimeString()
        ];
    }
}
