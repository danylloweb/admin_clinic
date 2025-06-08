<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Criterias\FilterByFinalDateSaleCriteria;
use App\Criterias\FilterByHasProcedureSalesItemCriteria;
use App\Criterias\FilterByPatientIdCriteria;
use App\Criterias\FilterByStartDateSaleCriteria;
use App\Criterias\FilterByStatusCriteria;
use App\Criterias\FilterByTypePaymentCriteria;
use App\Entities\SalesOrder;
use App\Presenters\SalesOrderDetailPresenter;
use App\Repositories\ProcedureRepository;
use App\Repositories\SalesOrderItemRepository;
use App\Repositories\SalesOrderRepository;

/**
 * SalesOrderService
 */
class SalesOrderService extends AppService
{
    /**
     * @var SalesOrderRepository
     */
    protected $repository;
    /**
     * @var SalesOrderItemRepository
     */
    protected $salesOrderItemRepository;
    protected $procedureRepository;

    /**
     * @param SalesOrderRepository $repository
     * @param SalesOrderItemRepository $salesOrderItemRepository
     * @param ProcedureRepository $procedureRepository
     */
    public function __construct(SalesOrderRepository $repository,
                                SalesOrderItemRepository $salesOrderItemRepository,
                                ProcedureRepository $procedureRepository)
    {
        $this->repository = $repository;
        $this->salesOrderItemRepository = $salesOrderItemRepository;
        $this->procedureRepository = $procedureRepository;
    }

    /**
     * @param int $limit
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function all(int $limit = 20)
    {
        $response = $this->repository
            ->resetCriteria()
            ->pushCriteria(app(FilterByTypePaymentCriteria::class))
            ->pushCriteria(app(FilterByPatientIdCriteria::class))
            ->pushCriteria(app(FilterByStartDateSaleCriteria::class))
            ->pushCriteria(app(FilterByFinalDateSaleCriteria::class))
            ->pushCriteria(app(FilterByStatusCriteria::class))
            ->pushCriteria(app(FilterByHasProcedureSalesItemCriteria::class))
            ->pushCriteria(app(AppRequestCriteria::class))
            ->paginate($limit);
        $items         = $response['data'];
        $filteredItems = array_filter($items, function ($item) {return $item['status'] == 1;});
        $total_price   = number_format(array_sum(array_column($filteredItems, 'price')), 2, ',', '.');
        $response['total']['total_price'] = $total_price;
        return $response;
    }

    /**
     * @param array $data
     * @param bool $skipPresenter
     * @return mixed
     */
    public function create(array $data, bool $skipPresenter = false)
    {

        $sale_data_create = [
            'patient_id'       => $data['patient_id'],
            'qty_installments' => $data['qty_installments'],
            'author_id'        => $data['user_id'],
            'type_payment'     => $data['type_payment'],
            'discount'         => 0,
            'brand_card'       =>  $data['brand_card'],
        ];
        $sale_order = $this->repository->skipPresenter()->create($sale_data_create);
        $amount    = 0;
        $qty_total = 0;
        foreach ($data['items'] as $item) {
            $procedure = $this->procedureRepository->skipPresenter()->find($item['procedure_id']);
            if ($procedure->is_package == SalesOrder::IS_PACKAGE){
                $qty_item = $procedure->qty;
            }else{
                $qty_item = $item['qty'];
            }
            $amount  += $procedure->price * $item['qty'];
            $qty_total += $item['qty'];
            for ($qty = 1; $qty <= $qty_item; $qty++){
                if ($procedure->is_package == SalesOrder::IS_PACKAGE){
                    $procedure_name = $procedure->name." ".$qty."º Sessão";
                    $price_item = $procedure->unit_price;
                }else{
                    $procedure_name = $procedure->name;
                    $price_item     = $procedure->price;
                }
                $item_data_create = [
                    'sales_order_id' => $sale_order->id,
                    'procedure_id'   => $item['procedure_id'],
                    'procedure_name' => $procedure_name,
                    'price'          => $price_item,
                    'qty'            => 1,
                ];
                $this->salesOrderItemRepository->skipPresenter()->create($item_data_create);
            }
        }
        $sale_order->amount = $amount;
        $sale_order->qty    = $qty_total;
        $sale_order->save();
        return $sale_order;
    }

    /**
     * @param $id
     * @param bool $skip_presenter
     * @return mixed
     */
    public function find($id, bool $skip_presenter = false)
    {
        $sale = $this->repository->setPresenter(SalesOrderDetailPresenter::class)->find($id);
        $image_patient = $this->getlinkImageByPhone($sale['data']['phone']);
        $sale['data']['image_patient'] = $image_patient->success??'';
        return $sale;
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updateInstallment($id, $data): mixed
    {
        if ($data['qty_installments'] > 1){
            $data['type_payment'] = 2;
        }
        return $this->repository->update($data, $id);
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updateStatus($id, $data): mixed
    {
        $sale = $this->repository->skipPresenter()->find($id);
        if ($sale->status === 1){
            return $sale;
        }
        return $this->repository->skipPresenter()->update(['status' => $data['status']], $id);
    }

    /**
     * @param $id
     * @return array
     */
    public function delete($id): array
    {
        $this->salesOrderItemRepository->removeAllBySalesOrderId($id);
        return parent::delete($id);
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updateTypePayment($id, $data): mixed
    {
        return $this->repository->skipPresenter()->update(['type_payment' => $data['type_payment']], $id);
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updateBrandPayment($id, $data): mixed
    {
        return $this->repository->skipPresenter()->update(['brand_card' => $data['brand_card']], $id);
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updatePartialPayment($id, $data): mixed
    {
        return $this->repository->skipPresenter()->update([
            'partial_amount' => floatval( $data['partial_amount']),
            'status' => 3], $id);
    }

}
