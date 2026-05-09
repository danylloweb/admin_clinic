<?php

namespace App\Transformers;

use App\Entities\BodyEvaluation;
use League\Fractal\TransformerAbstract;

/**
 * Class FacialEvaluationTransformer.
 *
 * @package namespace App\Transformers;
 */
class BodyEvaluationTransformer extends TransformerAbstract
{
    /**
     * Transform the FacialEvaluation entity.
     *
     * @param BodyEvaluation $model
     *
     * @return array
     */
    public function transform(BodyEvaluation $model)
    {
        return [
            'id'                         => (int) $model->id,
            'patient_id'                 => (int) $model->patient_id,
            'professional_id'            => (int) $model->professional_id,
            'chief_complaint'            => $model->chief_complaint,
            'body_type'                  => $model->body_type,
            'cellulite'                  => $model->cellulite,
            'flaccidity'                 => $model->flaccidity,
            'localized_fat'              => $model->localized_fat,
            'stretch_marks'              => $model->stretch_marks,
            'varicose_veins'             => $model->varicose_veins,
            'aesthetic_history'          => $model->aesthetic_history,
            'allergies'                  => $model->allergies,
            'medications_in_use'         => $model->medications_in_use,
            'patient_objective'          => $model->patient_objective,
            'treatment_plan'             => $model->treatment_plan,
            'created_at'                 => $model->created_at->ToDateTimeString(),
            'updated_at'                 => $model->updated_at->ToDateTimeString(),
        ];
    }
}
