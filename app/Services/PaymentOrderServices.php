<?php

namespace App\Services;

use App\Entities\PaymentOrder;
use App\Repositories\PaymentOrderRepository;
use Exception;

class PaymentOrderServices extends AppService
{
    protected $repository;
    private $paymentServices;

    public function __construct(
        PaymentOrderRepository $repository,
        PaymentServices $paymentServices
    ) {
        $this->repository = $repository;
        $this->paymentServices = $paymentServices;
    }

    /**
     * @throws Exception
     */
    public function generate(array $data): PaymentOrder
    {
        $payment = $this->paymentServices->createPayment($data);

        $dataPaymentOrder = [
            'amount' => $data['amount'],
            'payment_id' => $payment->id
        ];

        return $this->repository->skipPresenter()->create($dataPaymentOrder);
    }

    public function canceledOrder(PaymentOrder $paymentOrder)
    {
        $this->paymentServices->canceledPayment($paymentOrder->payment);
    }
}
