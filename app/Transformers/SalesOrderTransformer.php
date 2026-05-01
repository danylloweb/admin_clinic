<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\SalesOrder;

/**
 * Class SalesOrderTransformer.
 *
 * @package namespace App\Transformers;
 */
class SalesOrderTransformer extends TransformerAbstract
{
    /**
     * Transform the SalesOrder entity.
     *
     * @param \App\Entities\SalesOrder $model
     *
     * @return array
     */
    public function transform(SalesOrder $model)
    {
        return [
            'id'                 => (int) $model->id,
            'amount'             => number_format($model->amount,2,',','.'),
            'price'              => $model->amount,
            'patient_name'       => $model->patient->social_name,
            'patient_id'         => $model->patient_id,
            'discount'           => $model->discount,
            'qty_installments'   => $model->qty_installments,
            'status'             => $model->status,
            'status_title'       => $this->getTitleStatus($model->status),
            'type_payment_title' => $this->getTitleTypePayment($model->type_payment),
            'date'               => $model->created_at->format('d/m/Y'),
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
                return 'Aguardando pagamento';
        }
    }
}
