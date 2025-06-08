<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\FollowUpMessage;

/**
 * Class FollowUpMessageTransformer.
 *
 * @package namespace App\Transformers;
 */
class FollowUpMessageTransformer extends TransformerAbstract
{
    /**
     * Transform the FollowUpMessage entity.
     *
     * @param \App\Entities\FollowUpMessage $model
     *
     * @return array
     */
    public function transform(FollowUpMessage $model)
    {
        return [
            'id'          => (int) $model->id,
            'name'        => $model->name,
            'message'     => $model->message,
            'short_message' => $this->shortMessage($model->message),
            'status'      => $model->status,
            'created_at'  => $model->created_at->toDateTimeString(),
            'updated_at'  => $model->updated_at->toDateTimeString()
        ];
    }

    private function shortMessage($message): string
    {
        if (strlen($message) <= 60) return $message;
        $truncada = substr($message, 0, 60 - 3);
        return $truncada . '...';
    }
}
