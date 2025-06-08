<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Patient;

/**
 * Class PatientTransformer.
 *
 * @package namespace App\Transformers;
 */
class PatientTransformer extends TransformerAbstract
{
    /**
     * Transform the Patient entity.
     *
     * @param \App\Entities\Patient $model
     *
     * @return array
     */
    public function transform(Patient $model)
    {
        return [
            'id'          => (int) $model->id,
            'name'        => $model->name,
            'social_name' => $model->social_name,
            'last_message'=> $model->getLastMessageByChatId(),
            'chat_id'     => $model->chat_id,
            'phone'       => $model->phone,
            'phone_link'  => str_replace(["(",")","-"],'',$model->phone),
            'birth_date'  => $model->birth_date->format('Y-m-d'),
            'birth_date_title'  => $model->birth_date->format('d/m/Y'),
            'age'         => date_diff(date_create($model->birth_date), date_create('now'))->y,
            'sex'         => $model->sex,
            'created_at'  => $model->created_at->toDateTimeString(),
            'updated_at'  => $model->updated_at->toDateTimeString()
        ];
    }
}
