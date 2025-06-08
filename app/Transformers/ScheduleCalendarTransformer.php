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
class ScheduleCalendarTransformer extends TransformerAbstract
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
        $date = Carbon::create($model->date);
        $start = Carbon::create( $model->date->format('Y-m-d') . ' ' . $model->time);
        $end   = $start->copy()->addMinutes($model->procedure->execution_time);
        $sale  = $model->getSaleOrderStatus();
        $range = "De: ".$start->format('H:i') . ' - ' . $end->format('H:i');
        return [
            'id'          => (int) $model->id,
            'title'       => $model->patient->social_name,
            'description' => $model->procedure->name,
            'patient_id'  => $model->patient_id,
            'range_time'  => $range,
            'status_title'=> $this->getTitleStatusSchedule($model->status),
            'start'       => $start->toIso8601String(),
            'end'         => $end->toIso8601String(),
            'color'       => $this->getColorStatusSchedule($model->status),
            'saleStatus'  => $this->getTitleStatus($sale['status']),
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
    private function getColorStatusSchedule($status)
    {
        switch ($status) {
            case "Confirmado":
                return "green";
            case "Marcado":
                return "purple";
            case "Cancelado":
                return "red";
            case "Adiado":
                return "blue";
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
                return "Cancelado❌";
            case "Adiado":
                return "Adiado🕓";
        }
    }

}
