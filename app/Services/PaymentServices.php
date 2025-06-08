<?php

namespace App\Services;

use App\Entities\Payment;
use App\Entities\PaymentGateway;
use App\Entities\PaymentMethod;
use App\Entities\PaymentStatus;
use App\Repositories\PaymentRepository;
use App\Services\Integracions\Payment\MercadoPagoService;
use App\Services\Interfaces\PaymentServiceInterface;

use function Symfony\Component\String\s;

class PaymentServices extends AppService
{
    /**
     * @var PaymentRepository
     */
    protected $repository;

    /**
     * @var MercadoPagoService
     */
    private $mercadoPagoService;

    public function __construct(PaymentRepository $repository, MercadoPagoService $mercadoPagoService)
    {
        $this->repository = $repository;
        $this->mercadoPagoService = $mercadoPagoService;
    }

    private function getActiveWrapper($active = PaymentGateway::MERCADO_PAGO): ?PaymentServiceInterface
    {
        switch ($active) {
            case PaymentGateway::MERCADO_PAGO:
                return $this->mercadoPagoService;
            default:
                return null;
        }
    }

    /**
     * @param array $data
     * @return Payment|null
     */
    public function createPayment(array $data): ?Payment
    {
        switch ($data['payment_gateway_id']) {
            case PaymentMethod::PIX:
                $payment = $this->getActiveWrapper($data['payment_gateway_id'])
                    ->createPix($data);
                break;
            default:
                $payment = null;
                break;
        }

        $dataPayment = [
            'reference'          => $payment['id'],
            'payment_status_id'  => PaymentStatus::PENDING,
            'payment_method_id'  => PaymentMethod::PIX,
            'payment_gateway_id' => $data['payment_gateway_id'],
            'info'               => json_encode($payment)
        ];

        return $this->repository->skipPresenter()->create($dataPayment);
    }

    /**
     * @param Payment $payment
     * @return void
     */
    public function canceledPayment(Payment $payment)
    {
        $this->repository->update(['payment_status_id' => PaymentStatus::CANCELLED,], $payment->id);
    }
}
