<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Schedule;

/**
 * Class ScheduleTransformer.
 *
 * @package namespace App\Transformers;
 */
class ScheduleTransformer extends TransformerAbstract
{
    /**
     * Transform the Schedule entity.
     *
     * @param \App\Entities\Schedule $model
     *
     * @return array
     */
    public function transform(Schedule $model)
    {
        $sale = $model->getSaleOrderStatus();
        return [
            'id'                   => (int) $model->id,
            'procedure_id'         => $model->procedure_id,
            'procedure_name'       => $model->procedure->name,
            'procedure_type'       => $model->procedure->procedureType->name,
            'procedure_price'      => $model->procedure->price,
            'price'                => number_format($model->procedure->price,2,',','.'),
            'procedure_price_cost' => $model->procedure->cost_price,
            'patient_id'           => $model->patient_id,
            'patient_name'         => $model->patient->name,
            'last_message'         => $model->patient->getLastMessageByChatId(),
            'date'                 => $model->date->format('d/m/Y'),
            'date_real'            => $model->date->format('Y-m-d'),
            'time'                 => $model->time,
            'phone'                => $model->patient->phone,
            'phone_link'           => str_replace(["(",")","-"],'',$model->patient->phone),
            'status'               => $model->status,
            'status_title'         => $this->getTitleStatusSchedule($model->status),
            'observation_status'   => $model->observation_status,
            'professional'         => $model->user->name??'',
            'saleStatus'           => $this->getTitleStatus($sale['status']),
            'sale_id'              => $sale['id'],
            'created_at'           => $model->created_at->toDateTimeString(),
            'updated_at'           => $model->updated_at->toDateTimeString()
        ];
    }

    /**
     * @param $status
     * @return string|void
     */
    private function getTitleStatus($status)
    {
        switch ($status) {
            case 0:
                return "Orçamento🟡";
            case 1:
                return "Paga✅";
            case 2:
                return "Cancelada❌";

        }
    }

    /**
     * @param $status
     * @return string|void
     */
    private function getTitleStatusSchedule($status)
    {
        switch ($status) {
            case "Confirmado":
                return "Confirmado✅";
            case "Marcado":
                return "Marcado🔵";
            case "Cancelado":
                return "Cancelada❌";
            case "Adiado":
                return "Adiado🕓";
        }
    }
}
