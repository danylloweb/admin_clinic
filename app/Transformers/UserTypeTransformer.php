<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\UserType;

/**
 * Class UserTypeTransformer.
 *
 * @package namespace App\Transformers;
 */
class UserTypeTransformer extends TransformerAbstract
{
    /**
     * Transform the UserType entity.
     *
     * @param UserType $model
     *
     * @return array
     */
    public function transform(UserType $model): array
    {
        return [
            'id'         => (int) $model->id,
            'name'        => $model->name,
            'created_at'  => $model->created_at->toDateTimeString(),
            'updated_at'  => $model->updated_at->toDateTimeString()
        ];
    }
}
