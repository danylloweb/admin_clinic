<?php

namespace App\Http\Controllers;

use App\Services\SalesOrderService;
use App\Validators\SalesOrderValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class SalesOrdersController.
 *
 * @package namespace App\Http\Controllers;
 */
class SalesOrdersController extends Controller
{
    /**
     * @var SalesOrderService
     */
    protected $service;

    /**
     * @var SalesOrderValidator
     */
    protected $validator;

    /**
     * SalesOrdersController constructor.
     *
     * @param SalesOrderService $service
     * @param SalesOrderValidator $validator
     */
    public function __construct(SalesOrderService $service, SalesOrderValidator $validator)
    {
        $this->service   = $service;
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function updateInstallment(Request $request, $id): JsonResponse
    {
        return response()->json($this->service->updateInstallment($id, $request->all()));
    }
    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        return response()->json($this->service->updateStatus($id, $request->all()));
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function updateTypePayment(Request $request, $id): JsonResponse
    {
        return response()->json($this->service->updateTypePayment($id, $request->all()));
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function updateBrandPayment(Request $request, $id): JsonResponse
    {
        return response()->json($this->service->updateBrandPayment($id, $request->all()));
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function updatePartialPayment(Request $request, $id): JsonResponse
    {
        return response()->json($this->service->updatePartialPayment($id, $request->all()));
    }

}
