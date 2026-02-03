<?php

namespace App\Transformers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
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
            'photo'       => $this->getLinkImageByPhone($model->chat_id)->success ?? 'https://ui-avatars.com/api/?name='.urlencode($model->name),
            'phone_link'  => str_replace(["(",")","-"],'',$model->phone),
            'sex'         => $model->sex,
            'birth_date'  => Carbon::create($model->birth_date)->format('d/m/Y'),
            'created_at'  => $model->created_at->toDateTimeString(),
            'updated_at'  => $model->updated_at->toDateTimeString()
        ];
    }

    private function getLinkImageByPhone($chat_id):mixed
    {
        return Cache::store('redis')->tags('imageProfile')->remember($chat_id, 1220000, function () use ($chat_id) {

        });
    }
}
