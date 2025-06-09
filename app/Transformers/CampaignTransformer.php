<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Campaign;

/**
 * Class CampaignTransformer.
 *
 * @package namespace App\Transformers;
 */
class CampaignTransformer extends TransformerAbstract
{
    /**
     * Transform the Campaign entity.
     *
     * @param \App\Entities\Campaign $model
     *
     * @return array
     */
    public function transform(Campaign $model)
    {
        return [
            'id'          => (int) $model->id,
            'name'        => $model->name,
            'description' => $model->description,
            'url_image'   => $model->url_image,
            'date'        => $model->date,
            'status'      => $model->status == 0 ? 'Criado' : 'Concluida',
            'created_at'  => $model->created_at->toDateTimeString(),
            'updated_at'  => $model->updated_at->toDateTimeString()
        ];
    }
}
