<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\ProcedureType;

/**
 * Class ProcedureTypeTransformer.
 *
 * @package namespace App\Transformers;
 */
class ProcedureTypeTransformer extends TransformerAbstract
{
    /**
     * Transform the ProcedureType entity.
     *
     * @param \App\Entities\ProcedureType $model
     *
     * @return array
     */
    public function transform(ProcedureType $model)
    {
        return [
            'id'          => (int) $model->id,
            'name'        => $model->name,
            'created_at'  => $model->created_at->toDateTimeString(),
            'updated_at'  => $model->updated_at->toDateTimeString()
        ];
    }
}
