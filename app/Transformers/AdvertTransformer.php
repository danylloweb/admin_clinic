<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Advert;

/**
 * Class AdvertTransformer.
 *
 * @package namespace App\Transformers;
 */
class AdvertTransformer extends TransformerAbstract
{
    /**
     * Transform the Advert entity.
     *
     * @param \App\Entities\Advert $model
     *
     * @return array
     */
    public function transform(Advert $model)
    {
        $price_spent = $model->price_per_click * $model->qty_click_confirmed;

        return [
            'id'                  => (int) $model->id,
            'name'                => $model->name,
            'description'         => $model->description,
            'code'                => $model->code,
            'url_site'            => $model->url_site,
            'url_checkout'        => $model->url_checkout,
            'status'              => $model->status == 0 ? 'Inativa' : 'Ativa',
            'qty_click_confirmed' => $model->qty_click_confirmed,
            'qty_click_checkout'  => $model->qty_click_checkout,
            'qty_convert'         => $model->qty_convert,
            'price_per_click'     => $model->price_per_click,
            'message_to_lead'     => $model->message_to_lead??'',
            'price_investing'     => number_format($price_spent,2,',','.'),
            'created_at'          => $model->created_at->toDateTimeString(),
            'updated_at'          => $model->updated_at->format('d/m/Y H:i:s')
        ];
    }
}
