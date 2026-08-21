<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Procedure;

/**
 * Class ProcedureTransformer.
 *
 * @package namespace App\Transformers;
 */
class ProcedureTransformer extends TransformerAbstract
{
    /**
     * Transform the Procedure entity.
     *
     * @param \App\Entities\Procedure $model
     *
     * @return array
     */
    public function transform(Procedure $model)
    {

        $installment        = $model->price / 1;
        $installment_tax    = $model->getInstallmentTax();
        $amount_installment = $installment + ($installment * $installment_tax);
        return [
            'id'                     => (int) $model->id,
            'name'                   => $model->name,
            'short_name'             => mb_strimwidth( $model->name, 0, 30, "..."),
            'description'            => $model->description,
            'procedure_type_id'      => $model->procedure_type_id,
            'procedure_type_name'    => $model->procedureType->name,
            'execution_time'         => $model->execution_time,
            'minimum_amount_of_time' => $model->minimum_amount_of_time,
            'non_competing'          => $model->non_competing,
            'price'                  => number_format($model->price,2,',','.'),
            'credit_price'           => number_format($amount_installment,2,',','.'),
            'cost_price'             => number_format($model->cost_price,2,',','.'),
            'percentage_on_sale'     => $model->percentage_on_sale,
            'status'                 => $model->status == 1 ? 'Ativo': 'Inativo',
            'status_enum'            => $model->status,
            'observation'            => $model->observation??'',
            'step_by_step'           => $model->step_by_step??'',
            'patient_instructions'   => $model->patient_instructions??'',
            'message_schedule'       => $model->message_schedule??'',
            'author'                 => $model->author,
            'is_package'             => $model->is_package,
            'qty'                    => $model->qty,
            'unit_price'             => $model->unit_price,
            'message_schedule_after' => $model->message_schedule_after??'',
            'unit_price_formated'    => number_format($model->unit_price,2,',','.'),
            'created_at'             => $model->created_at->toDateTimeString(),
            'updated_at'             => $model->updated_at->toDateTimeString()
        ];
    }
}
