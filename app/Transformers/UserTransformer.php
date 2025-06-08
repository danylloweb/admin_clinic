<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\User;

/**
 * Class UserTransformer.
 *
 * @package namespace App\Transformers;
 */
class UserTransformer extends TransformerAbstract
{
    /**
     * Transform the User entity.
     *
     * @param User $model
     *
     * @return array
     */
    public function transform(User $model): array
    {
        return [
            'id'          => (int) $model->id,
            'name'        => $model->name,
            'cpf'         => $model->cpf,
            'email'       => $model->email,
            'phone'       => $model->phone,
            'advice'      => $model->advice??'',
            'img'         => $model->img,
            'userType'    => $model->userType,
            'has_medical' => $model->has_medical,
            'created_at'  => $model->created_at->toDateTimeString(),
            'updated_at'  => $model->updated_at->toDateTimeString()
        ];
    }
}
