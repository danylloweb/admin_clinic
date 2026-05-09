<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\FacialEvaluation;

/**
 * Class FacialEvaluationTransformer.
 *
 * @package namespace App\Transformers;
 */
class FacialEvaluationTransformer extends TransformerAbstract
{
    /**
     * Transform the FacialEvaluation entity.
     *
     * @param \App\Entities\FacialEvaluation $model
     *
     * @return array
     */
    public function transform(FacialEvaluation $model)
    {
        return [
            'id'                         => (int) $model->id,
            'patient_id'                 => (int) $model->patient_id,
            'professional_id'            => (int) $model->professional_id,
            'chief_complaint'            => $model->chief_complaint,
            'skin_type'                  => $model->skin_type,
            'oiliness'                   => $model->oiliness,
            'hydration'                  => $model->hydration,
            'sensitivity'                => $model->sensitivity,
            'acne'                       => (bool) $model->acne,
            'acne_notes'                 => $model->acne_notes,
            'melasma'                    => (bool) $model->melasma,
            'melasma_notes'              => $model->melasma_notes,
            'wrinkles'                   => (bool) $model->wrinkles,
            'wrinkles_notes'             => $model->wrinkles_notes,
            'flaccidity'                 => (bool) $model->flaccidity,
            'flaccidity_notes'           => $model->flaccidity_notes,
            'spots'                      => (bool) $model->spots,
            'spots_notes'                => $model->spots_notes,
            'dilated_pores'              => (bool) $model->dilated_pores,
            'dilated_pores_notes'        => $model->dilated_pores_notes,
            'fitzpatrick_type'           => $model->fitzpatrick_type,
            'aesthetic_history'          => $model->aesthetic_history,
            'allergies'                  => $model->allergies,
            'medications_in_use'         => $model->medications_in_use,
            'patient_objective'          => $model->patient_objective,
            'treatment_plan'             => $model->treatment_plan,
            'photo_front'                => $model->photo_front,
            'photo_profile_right'        => $model->photo_profile_right,
            'photo_profile_left'         => $model->photo_profile_left,
            'consent_accepted'           => (bool) $model->consent_accepted,
            'patient_signature'          => $model->patient_signature,
            'professional_signature'     => $model->professional_signature,
            'signature_token'            => $model->signature_token,
            'signature_token_expires_at' => $model->signature_token_expires_at ? $model->signature_token_expires_at->toDateTimeString() : null,
            'created_at'                 => $model->created_at->ToDateTimeString(),
            'updated_at'                 => $model->updated_at->ToDateTimeString(),
        ];
    }
}
