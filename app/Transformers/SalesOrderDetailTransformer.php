<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\SalesOrder;

/**
 * Class SalesOrderTransformer.
 *
 * @package namespace App\Transformers;
 */
class SalesOrderDetailTransformer extends TransformerAbstract
{
    /**
     * Transform the SalesOrder entity.
     *
     * @param \App\Entities\SalesOrder $model
     *
     * @return array
     */
    public function transform(SalesOrder $model):array
    {
        $items = [];
        if ($model->salesOrderItems) {
            foreach ($model->salesOrderItems as $item) {
                $items[] = [
                    'id'             => $item->id,
                    'schedule_id'    => $item->schedule_id??'',
                    'procedure_name' => $item->procedure_name,
                    'procedure_title'=> $item->schedule? $item->schedule->procedure->name:$item->procedure_name,
                    'date'           => $item->schedule? date_create($item->schedule->date)->format('d/m/Y'):'',
                    'time'           => $item->schedule? $item->schedule->time :'',
                    'qty'            => $item->qty,
                    'status'         => $item->schedule?
                        $this->getTitleStatusSchedule($item->schedule->status):'Aguadando agendamento🕓',
                    'price'          => number_format($item->price,2,',','.'),
                    'price_total'    => number_format($item->price * $item->qty,2,',','.'),
                 ];
            }
        }

        $installment        = $model->amount / $model->qty_installments;
        $installment_tax    = $model->getInstallmentTax();
        $amount_installment = $installment + ($installment * $installment_tax);
        $amount             = number_format($model->amount,2,',','.');
        $amount_pix         = $model->amount >= 250 ? number_format($model->amount - ($model->amount * 0.05),
            2, ',', '.') : $amount;

        return [
            'id'                 => (int) $model->id,
            'number'             => str_pad($model->id, 6, "0", STR_PAD_LEFT),
            'amount_pix'         => $amount_pix,
            'amount'             => $amount,
            'partial_amount'     => number_format($model->partial_amount,2,',','.'),
            'amount_credit'      => number_format( $amount_installment * $model->qty_installments, 2, ',', '.'),
            'amount_debit'       => number_format( $model->getDebitAmount(), 2, ',', '.'),
            'amount_installment' => number_format($amount_installment,2,',','.'),
            'patient_name'       => $model->patient->name??'',
            'phone'              => $model->patient->phone??'',
            'discount'           => $model->discount,
            'qty_installments'   => $model->qty_installments,
            'type_payment_title' => $this->getTitleTypePayment($model->type_payment),
            'type_payment'       => $model->type_payment,
            'items'              => $items,
            'status'             => $model->status,
            'status_title'       => $this->getTitleStatus($model->status),
            'brand_card'         => $model->brand_card,
            'created_at'         => $model->created_at->toDateTimeString(),
            'updated_at'         => $model->updated_at->toDateTimeString()
        ];
    }

    /**
     * @param $id_type_payment
     * @return string|void
     */
    private function getTitleTypePayment($id_type_payment)
    {
        switch ($id_type_payment) {
            case 1:
                return 'PIX';
            case 2:
                return 'Cartão de Crédito';
            case 3:
                return 'Cartão de Débito';
            case 4:
                return 'Dinheiro';
        }
    }

    private function getTitleStatus($status)
    {
        switch ($status) {
            case 0:
                return 'Inicial';
            case 1:
                return 'Pago';
            case 2:
                return 'Cancelado';
            case 3:
                return 'Parcial';
            case 4:
                return 'Finalizado';
                default:
                    return 'Aguadando pagamento';

        }
    }

    private function getTitleStatusSchedule($status)
    {
        switch ($status) {
            case "Confirmado":
                return "Confirmado ✅";
            case "Marcado":
                return "Agendado 🔵";
            case "Cancelado":
                return "Cancelada ❌";
            case "Adiado":
                return "Adiado 🕓";
                default:
                    return "Aguardando Agendamento  🕓";
        }
    }
}
