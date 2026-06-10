<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Services\ProductLotService;
use App\Services\StockMovementService;
use App\Services\ProcedureConsumptionService;
use App\Validators\ProductValidator;
use App\Validators\ProductLotValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Prettus\Validator\Contracts\ValidatorInterface;

class ProductsController extends Controller
{
    protected $service;
    protected $lotService;
    protected $movementService;
    protected $consumptionService;
    protected $validator;
    protected $lotValidator;

    public function __construct(
        ProductService $service,
        ProductLotService $lotService,
        StockMovementService $movementService,
        ProcedureConsumptionService $consumptionService,
        ProductValidator $validator,
        ProductLotValidator $lotValidator
    ) {
        $this->service = $service;
        $this->lotService = $lotService;
        $this->movementService = $movementService;
        $this->consumptionService = $consumptionService;
        $this->validator = $validator;
        $this->lotValidator = $lotValidator;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $limit = $request->query->get('limit', 15);
            return response()->json($this->service->all($limit));
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception);
        }
    }

    public function create()
    {
        return redirect()->route('panel.products.create');
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);
            $product = $this->service->create($request->all());
            return response()->json($product, 201);
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception, 422);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->service->find($id);
            return response()->json($product, 200);
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception, 404);
        }
    }

    public function edit(int $id)
    {
        return redirect()->route('panel.products.edit', ['id' => $id]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);
            $product = $this->service->update($request->all(), $id);
            Cache::store('redis')->tags('products')->flush();
            return response()->json($product, 200);
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception, 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->service->delete($id);
            Cache::store('redis')->tags('products')->flush();
            return response()->json($result, 200);
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception, 404);
        }
    }

    public function storeLot(Request $request): JsonResponse
    {
        try {
            $this->lotValidator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);
            $lot = $this->lotService->create($request->all());
            return response()->json($lot, 201);
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception, 422);
        }
    }

    public function updateLot(Request $request, int $id): JsonResponse
    {
        try {
            $this->lotValidator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);
            $lot = $this->lotService->update($request->all(), $id);
            return response()->json($lot, 200);
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception, 422);
        }
    }

    public function getLots(int $productId): JsonResponse
    {
        try {
            $lots = $this->lotService->getByProduct($productId);
            return response()->json(['data' => $lots], 200);
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception, 404);
        }
    }

    public function getAlerts(int $productId): JsonResponse
    {
        try {
            $alerts = $this->service->getAlertsByProduct($productId);
            return response()->json(['alerts' => $alerts], 200);
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception, 404);
        }
    }

    public function getMovements(int $productId): JsonResponse
    {
        try {
            $movements = $this->movementService->getByProduct($productId);
            return response()->json(['data' => $movements], 200);
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception);
        }
    }

    public function getConsumptionHistory(int $productId): JsonResponse
    {
        try {
            $history = $this->consumptionService->getProductHistory($productId);
            return response()->json(['data' => $history], 200);
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception, 404);
        }
    }
}

