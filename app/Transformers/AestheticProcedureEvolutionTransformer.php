<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\AestheticProcedureEvolution;

/**
 * Class AestheticProcedureEvolutionTransformer.
 *
 * @package namespace App\Transformers;
 */
class AestheticProcedureEvolutionTransformer extends TransformerAbstract
{
    /**
     * Transform the AestheticProcedureEvolution entity.
     *
     * @param \App\Entities\AestheticProcedureEvolution $model
     *
     * @return array
     */
    public function transform(AestheticProcedureEvolution $model): array
    {
        $professional = $model->professional;

        return [
            'id' => (int) $model->id,
            'schedule_id' => $model->schedule_id ? (int) $model->schedule_id : null,
            'patient_id' => (int) $model->patient_id,
            'professional_id' => $model->professional_id ? (int) $model->professional_id : null,
            'professional' => $professional ? [
                'id' => (int) $professional->id,
                'name' => (string) $professional->name,
            ] : null,
            'procedure_name' => $model->procedure_name,
            'start_date' => optional($model->start_date)->toDateString(),
            'evolution_sessions' => $model->evolution_sessions,
            'photo_before' => $model->photo_before,
            'photo_after' => $model->photo_after,
            'result_evaluation' => $model->result_evaluation,
            'patient_signature' => $model->patient_signature,
            'professional_signature' => $model->professional_signature,
            'signed_at' => optional($model->signed_at)->toDateTimeString(),
            'created_at' => optional($model->created_at)->toDateTimeString(),
            'updated_at' => optional($model->updated_at)->toDateTimeString(),
        ];
    }
}
