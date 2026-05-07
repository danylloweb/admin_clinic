<?php

namespace App\Http\Controllers;

use App\Services\CampaignService;
use App\Validators\CampaignValidator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class CampaignsController.
 *
 * @package namespace App\Http\Controllers;
 */
class CampaignsController extends Controller
{
    /**
     * @var CampaignService
     */
    protected $service;

    /**
     * @var CampaignValidator
     */
    protected $validator;

    /**
     * CampaignsController constructor.
     *
     * @param CampaignService $service
     * @param CampaignValidator $validator
     */
    public function __construct(CampaignService $service, CampaignValidator $validator)
    {
        $this->service   = $service;
        $this->validator = $validator;
    }

    /**
     * @param int $id
     * @return View|Factory|Application
     */
    public function panelShow(int $id): View|Factory|Application
    {
        $campaign = $this->service->find($id,true);
        return view('campaigns.show', [
            'title'    => 'Campanha WhatsApp',
            'subtitle' => 'Detalhes da Campanha',
            'campaign' => $campaign,
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function create(): View|Factory|Application
    {
        return view('campaigns.create', [
            'title'    => 'Criar Campanha',
            'subtitle' => 'Formulário de Criação de Campanha',
        ]);
    }

    public function panelSend(int $id): View|Factory|Application
    {
        $campaign = $this->service->find($id, true);
        return view('campaigns.send', [
            'title' => 'Disparo de Campanha',
            'subtitle' => 'Envio em lote via WhatsApp',
            'campaign' => $campaign,
        ]);
    }

    public function startSend(int $id): JsonResponse
    {
        return response()->json($this->service->startDispatch($id));
    }

    public function processSend(Request $request, int $id): JsonResponse
    {
        $page    = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', 5);
        return response()->json($this->service->processDispatchPage($id, $page, $perPage));
    }

    public function sendProgress(int $id): JsonResponse
    {
        return response()->json($this->service->dispatchProgress($id));
    }

}
