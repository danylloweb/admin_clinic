<?php

namespace App\Http\Controllers;

use App\Services\AppService;
use App\Repositories\SupplierRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuppliersController extends Controller
{
    protected $repository;

    public function __construct(SupplierRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $limit = $request->query->get('limit', 20);
            $suppliers = $this->repository->paginate($limit);
            return response()->json($this->convertPaginationResponse(json_decode(json_encode($suppliers), true)), 200);
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $supplier = $this->repository->create($request->all());
            return response()->json($supplier, 201);
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception, 422);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $supplier = $this->repository->find($id);
            return response()->json($supplier, 200);
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception, 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $supplier = $this->repository->update($request->all(), $id);
            return response()->json($supplier, 200);
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception, 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->repository->delete($id);
            return response()->json(['success' => (bool) $result], 200);
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception, 404);
        }
    }
}

