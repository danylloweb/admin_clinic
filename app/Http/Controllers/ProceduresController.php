<?php

namespace App\Http\Controllers;

use App\Services\ProcedureService;
use App\Validators\ProcedureValidator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Class ProceduresController.
 *
 * @package namespace App\Http\Controllers;
 */
class ProceduresController extends Controller
{
    /**
     * @var ProcedureService
     */
    protected $service;

    /**
     * @var ProcedureValidator
     */
    protected $validator;

    /**
     * ProceduresController constructor.
     *
     * @param ProcedureService $service
     * @param ProcedureValidator $validator
     */
    public function __construct(ProcedureService $service, ProcedureValidator $validator)
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
        $objects   = Cache::store('redis')->tags('procedures')->remember($cacheName, 12000, function () use ($limit) {
            return $this->service->all($limit);
        });
        return response()->json($objects, 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function replicate(int $id): JsonResponse
    {
        return response()->json($this->service->replicate($id));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStatus(int $id, Request $request)
    {
        return response()->json($this->service->update($request->all(), $id));
    }

    /**
     * @param int $id
     * @return Factory|View|\Illuminate\Foundation\Application|Application
     *
     */
    public function procedureShow(int $id): Factory|View|\Illuminate\Foundation\Application|Application
    {
        $procedure = $this->service->find($id, true);
        return view('procedures.show', [
            'title'    => 'Procedimento',
            'subtitle' => 'Detalhe do(a) Procedimento',
            'patient'  => $procedure,
            'routeCreate' => route('panel.procedures.index'),
        ]);
    }


}
