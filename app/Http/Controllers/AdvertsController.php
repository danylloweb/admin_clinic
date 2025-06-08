<?php

namespace App\Http\Controllers;

use App\Services\AdvertService;
use App\Validators\AdvertValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class AdvertsController.
 *
 * @package namespace App\Http\Controllers;
 */
class AdvertsController extends Controller
{
    /**
     * @var AdvertService
     */
    protected $service;

    /**
     * @var AdvertValidator
     */
    protected $validator;

    /**
     * AdvertsController constructor.
     *
     * @param AdvertService $service
     * @param AdvertValidator $validator
     */
    public function __construct(AdvertService $service, AdvertValidator $validator)
    {
        $this->service   = $service;
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registerClick(Request $request):JsonResponse
    {
        return response()->json($this->service->registerClick($request->all()));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registerClickCheckout(Request $request):JsonResponse
    {
        return response()->json($this->service->registerClickCheckout($request->all()));
    }


    /**
     * @param $code
     * @return JsonResponse
     */
    public function getByCode($code): JsonResponse
    {
        return response()->json(['data' => $this->service->findWhere(['code' => $code],true)]);
    }
}
