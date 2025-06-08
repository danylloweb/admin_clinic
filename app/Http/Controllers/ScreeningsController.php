<?php

namespace App\Http\Controllers;

use App\Services\ScreeningService;
use App\Validators\ScreeningValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Class ScreeningsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ScreeningsController extends Controller
{
    /**
     * @var ScreeningService
     */
    protected $service;

    /**
     * @var ScreeningValidator
     */
    protected $validator;

    /**
     * ScreeningsController constructor.
     *
     * @param ScreeningService $service
     * @param ScreeningValidator $validator
     */
    public function __construct(ScreeningService $service, ScreeningValidator $validator)
    {
        $this->service   = $service;
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request): JsonResponse
    {
        $limit     = $request->query->get('limit', 15);
        $cacheName = str_replace($request->url(), '', $request->fullUrl());
        $objects   = Cache::store('redis')->tags('screenings')->remember($cacheName, 12000, function () use ($limit) {
            return $this->service->all($limit);
        });
        return response()->json($objects, 200);
    }

}
