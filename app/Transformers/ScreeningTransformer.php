<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Screening;

/**
 * Class ScreeningTransformer.
 *
 * @package namespace App\Transformers;
 */
class ScreeningTransformer extends TransformerAbstract
{
    /**
     * Transform the Screening entity.
     *
     * @param \App\Entities\Screening $model
     *
     * @return array
     */
    public function transform(Screening $model)
    {
        return [
            'id'               => (int) $model->id,
            'number'           => str_pad($model->id, 6, '0', STR_PAD_LEFT),
            'pregnant'         => $model->pregnant,
            'tanned_skin'      => $model->tanned_skin,
            'consume_alcohol'  => $model->consume_alcohol,
            'sour_cream'       => $model->sour_cream,
            'face_lotion'      => $model->face_lotion,
            'arterial_tension' => $model->arterial_tension??'',
            'weight'           => $model->weight??'',
            'glucose'          => $model->glucose??'',
            'imc'              => $model->imc??'',
            'observation'      => $model->observation??'',
            'patient_id'       => $model->patient_id,
            'patient'          => $model->patient,
            'date'             => $model->created_at->format('d/m/Y'),
            'date_complete'    => $model->created_at->locale('pt-BR')->translatedFormat('l d F, Y'),
            'time'             => $model->created_at->format('H:i'),
            'created_at'       => $model->created_at->toDateTimeString(),
            'updated_at'       => $model->updated_at->toDateTimeString()
        ];
    }
}
