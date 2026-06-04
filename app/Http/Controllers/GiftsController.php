<?php

namespace App\Http\Controllers;

use App\Http\Requests\GiftCreateRequest;
use App\Services\GiftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GiftsController extends Controller
{
    protected $service;

    public function __construct(GiftService $service)
    {
        $this->service = $service;
    }

    public function newGift(GiftCreateRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $payload['source'] = 'gift-page';
        $payload['status'] = 'novo';

        return response()->json($this->service->create($payload), 201);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $list    =  $this->service->all($request->query->get('limit', 20));
        $listAll = json_encode($list);
        return response()->json($this->convertPaginationResponse(json_decode($listAll,true)), 200);
    }
}

