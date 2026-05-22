<?php

namespace App\Transformers;

use App\Entities\BodyEvaluation;
use League\Fractal\TransformerAbstract;

/**
§ * Class BodyEvaluationTransformer.
 *
 * @package namespace App\Transformers;
 */
class BodyEvaluationTransformer extends TransformerAbstract
{
    /**
     * Transform the BodyEvaluation entity.
     *
     * @param BodyEvaluation $model
     *
     * @return array
     */
    public function transform(BodyEvaluation $model)
    {
        $professional = $model->professional;

        return [
            'id' => (int) $model->id,
            'patient_id' => (int) $model->patient_id,
            'professional_id' => $model->professional_id ? (int) $model->professional_id : null,
            'professional' => $professional ? [
                'id' => (int) $professional->id,
                'name' => (string) $professional->name,
            ] : null,
            'weight' => $model->weight,
            'height' => $model->height,
            'fat_percentage' => $model->fat_percentage,
            'muscle_mass' => $model->muscle_mass,
            'objectives' => $model->objectives,
            'perimetry' => $model->perimetry,
            'cellulite' => $model->cellulite,
            'flaccidity' => $model->flaccidity,
            'liquid_retention' => (bool) $model->liquid_retention,
            'body_map_areas' => $model->body_map_areas,
            'medical_history' => $model->medical_history,
            'previous_procedures' => $model->previous_procedures,
            'treatment_plan' => $model->treatment_plan[0],
            'evolution_sessions' => $model->evolution_sessions,
            'photo_front' => $model->photo_front,
            'photo_profile_right' => $model->photo_profile_right,
            'photo_profile_left' => $model->photo_profile_left,
            'consent_accepted' => (bool) $model->consent_accepted,
            'patient_signature' => $model->patient_signature,
            'professional_signature' => $model->professional_signature,
            'signature_token' => $model->signature_token,
            'signature_token_expires_at' => optional($model->signature_token_expires_at)->toDateTimeString(),
            'signed_at' => optional($model->signed_at)->toDateTimeString(),
            'created_at' => optional($model->created_at)->toDateTimeString(),
            'updated_at' => optional($model->updated_at)->toDateTimeString(),
        ];
    }
}
