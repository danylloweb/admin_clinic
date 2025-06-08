<?php

namespace App\Http\Controllers;

use App\Services\ScheduleService;
use App\Validators\ScheduleValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Class SchedulesController.
 *
 * @package namespace App\Http\Controllers;
 */
class SchedulesController extends Controller
{
    /**
     * @var ScheduleService
     */
    protected $service;

    /**
     * @var ScheduleValidator
     */
    protected $validator;

    /**
     * SchedulesController constructor.
     *
     * @param ScheduleService $service
     * @param ScheduleValidator $validator
     */
    public function __construct(ScheduleService $service, ScheduleValidator $validator)
    {
        $this->service   = $service;
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request,$id): JsonResponse
    {
        return response()->json($this->service->updateStatus($id, $request->all()));
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
        $objects   = Cache::store('redis')->tags('schedules')->remember($cacheName, 12000, function () use ($limit) {
            return $this->service->all($limit);
        });
        return response()->json($objects, 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function scheduleItem(Request $request, $id): JsonResponse
    {
        return response()->json($this->service->scheduleItem($id, $request->all()));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function calendar(Request $request)
    {
        return $this->service->calendar()['data'];
    }
}
