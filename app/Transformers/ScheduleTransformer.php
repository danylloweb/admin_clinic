<?php

namespace App\Transformers;

use Carbon\Carbon;
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
        $procedure = $model->procedure;
        $patient = $model->patient;
        $procedureType = $procedure?->procedureType;
        $phone = $patient?->phone;
        $procedureItem = $model->getProcedureItem();

        return [
            'id'                   => (int) $model->id,
            'procedure_id'         => $model->procedure_id,
            'procedure_name'       => $procedureItem?->procedure_name ?? '-',
            'procedure_type'       => $procedureType?->name ?? '-',
            'procedure_price'      => (float) ($procedureItem->price ?? 0),
            'price'                => number_format((float) ($procedure?->price ?? 0),2,',','.'),
            'item_price'           => "R$".number_format((float) ($procedureItem->price ?? 0),2,',','.'),
            'procedure_price_cost' => (float) ($procedure?->cost_price ?? 0),
            'patient_id'           => $model->patient_id,
            'patient_name'         => $patient?->name ?? '-',
            'last_message'         => $patient?->getLastMessageByChatId(),
            'date'                 => $model->date ? Carbon::create($model->date)->format('d/m/Y') : '-',
            'date_real'            => $model->date ? Carbon::create($model->date)->format('Y-m-d') : null,
            'time'                 => $model->time ?? '-',
            'phone'                => $phone,
            'phone_link'           => $phone ? preg_replace('/\D+/', '', $phone) : '',
            'status'               => $model->status,
            'status_title'         => $this->getTitleStatusSchedule($model->status) ?? ($model->status ?: '-'),
            'observation_status'   => $model->observation_status,
            'professional'         => $model->user->name??'',
            'saleStatus'           => $this->getTitleStatus($sale['status']) ?? '-',
            'sale_id'              => $sale['id'] ?? 0,
            'created_at'           => $model->created_at?->toDateTimeString(),
            'updated_at'           => $model->updated_at?->toDateTimeString()
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
                return "Orçamento 🟡";
            case 1:
                return "Pago ✅";
            case 2:
                return "Cancelada ❌";
            case 3:
                return "Parcial 💰";
            case 4:
                return "Finalizada ✅";

        }

        return null;
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

        return null;
    }
}
