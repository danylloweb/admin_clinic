<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\ClinicalHistory;

/**
 * Class ClinicalHistoryTransformer.
 *
 * @package namespace App\Transformers;
 */
class ClinicalHistoryTransformer extends TransformerAbstract
{
    /**
     * Transform the ClinicalHistory entity.
     *
     * @param \App\Entities\ClinicalHistory $model
     *
     * @return array
     */
    public function transform(ClinicalHistory $model)
    {
        return [
            'id'                         => (int) $model->id,
            'patient_id'                 => $model->patient_id,
            'type_of_food'               => $model->type_of_food,
            'consume_alcohol'            => $model->consume_alcohol,
            'smoke'                      => $model->smoke,
            'practice_physical_activity' => $model->practice_physical_activity,
            'liters_of_water_per_day'    => $model->liters_of_water_per_day,
            'use_medication'             => $model->use_medication,
            'have_allergies'             => $model->have_allergies,
            'use_anabolic_hormones'      => $model->use_anabolic_hormones,
            'children'                   => $model->children,
            'pacemaker'                  => $model->pacemaker,
            'metal_prosthesis'           => $model->metal_prosthesis,
            'diabetes'                   => $model->diabetes,
            'oncology'                   => $model->oncology,
            'arterial_hypertension'      => $model->arterial_hypertension,
            'blood_type'                 => $model->blood_type,
            'observation'                => $model->observation,
            'created_at'                 => $model->created_at->toDateTimeString(),
            'updated_at'                 => $model->updated_at->toDateTimeString()
        ];
    }
}
