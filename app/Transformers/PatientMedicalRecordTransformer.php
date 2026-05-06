<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\PatientMedicalRecord;

/**
 * Class PatientMedicalRecordTransformer.
 *
 * @package namespace App\Transformers;
 */
class PatientMedicalRecordTransformer extends TransformerAbstract
{
    /**
     * Transform the PatientMedicalRecord entity.
     *
     * @param \App\Entities\PatientMedicalRecord $model
     *
     * @return array
     */
    public function transform(PatientMedicalRecord $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
