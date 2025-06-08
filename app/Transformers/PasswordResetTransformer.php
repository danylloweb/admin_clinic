<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\PasswordReset;

/**
 * Class PasswordResetTransformer.
 *
 * @package namespace App\Transformers;
 */
class PasswordResetTransformer extends TransformerAbstract
{
    /**
     * Transform the PasswordReset entity.
     *
     * @param PasswordReset $model
     *
     * @return array
     */
    public function transform(PasswordReset $model): array
    {
        return [
            'id'          => (int) $model->id,
            'email'       => $model->email,
            'token'       => $model->token,
            'created_at'  => $model->created_at->toDateTimeString(),
            'updated_at'  => $model->updated_at->toDateTimeString()
        ];
    }
}
