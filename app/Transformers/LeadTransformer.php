<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Lead;

/**
 * Class LeadTransformer.
 *
 * @package namespace App\Transformers;
 */
class LeadTransformer extends TransformerAbstract
{
    /**
     * Transform the Lead entity.
     *
     * @param \App\Entities\Lead $model
     *
     * @return array
     */
    public function transform(Lead $model)
    {
        return [
            'id'         => (int) $model->id,
            'name'       => $model->name,
            'phone'      => $model->phone,
            'phone_link'  => str_replace(["(",")","-",' '],'',$model->phone),
            'code'       => $model->code,
            'ads_name'   => $model->advert->name,
            'status'     => $model->status == 0 ? 'Inativa' : 'Ativa',
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString()
        ];
    }
}
