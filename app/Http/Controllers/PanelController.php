<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class PainelController.
 *
 * @package namespace App\Http\Controllers;
 */
class PanelController extends Controller
{

    /**
     */
    public function __construct()
    {
    }

    /**
     * @return View|Factory|Application
     */
    public function login(): View|Factory|Application
    {
        return view('login');
    }

    /**
     * @return View|Factory|Application
     */
    public function dashboard(): View|Factory|Application
    {
        return view('dashboard', [
            'title'       => 'Dashboard',
            'subtitle'    => 'Painel de Controle',
            'routeCreate' => route('dashboard'),
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function scheduleIndex(): View|Factory|Application
    {
        return view('schedules.index', [
            'title'       => 'Agendamentos',
            'subtitle'    => 'Lista de Agendamentos',
            'routeCreate' => '#',
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function procedureIndex(): View|Factory|Application
    {
        return view('procedures.index', [
            'title'       => 'Procedimentos',
            'subtitle'    => 'Lista de Procedimentos',
            'routeCreate' => route('panel.procedure.create'),
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function campaignIndex(): View|Factory|Application
    {
        return view('campaigns.index', [
            'title'       => 'Campanhas WhatsApp',
            'subtitle'    => 'Lista de Campanhas',
            'routeCreate' => route('panel.campaign.create'),
        ]);
    }

    public function patientIndex(): View|Factory|Application
    {
        return view('patients.index', [
            'title'       => 'Pacientes',
            'subtitle'    => 'Lista de Pacientes',
            'routeCreate' => route('panel.patient.create'),
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function patientCreate(): View|Factory|Application
    {
        return view('patients.create', [
            'title'       => 'Paciente',
            'subtitle'    => 'Criação Paciente',
            'routeCreate' => route('panel.patient.create'),
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function procedureCreate(): View|Factory|Application
    {
        return view('procedures.create', [
            'title'       => 'Novo Procedimento',
            'subtitle'    => 'Criação Procedimento',
            'routeCreate' => route('panel.procedure.create'),
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function salesOrderIndex(): View|Factory|Application
    {
        return view('sales-orders.index', [
            'title'       => 'Pedidos',
            'subtitle'    => 'Lista de Pedidos',
            'routeCreate' => route('panel.sales-order.create'),
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function salesOrderCreate(Request $request): View|Factory|Application
    {
        return view('sales-orders.create', [
            'title'       => 'Pedidos',
            'subtitle'    => 'Novo Pedido',
            'routeCreate' => route('panel.sales-order.create'),
            'userId'      => $request->attributes->get('user_jwt')?->id,
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function salesOrderEdit(Request $request, int $id): View|Factory|Application
    {
        return view('sales-orders.edit', [
            'title'    => 'Pedidos',
            'subtitle' => 'Editar Pedido',
            'orderId'  => $id,
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function salesOrderInvoice(Request $request): View|Factory|Application
    {
        $items = json_decode((string) $request->input('items', '[]'), true);
        if (!is_array($items)) {
            $items = [];
        }

        $socialName = $request->input('social_name', 'Paciente');
        $date = $request->input('date', now()->format('d/m/Y'));

        return view('sales-orders.invoice', [
            'documentTitle' => $socialName . ' - ' . $date,
            'socialName' => $socialName,
            'patientName' => $request->input('patient_name', $socialName),
            'phone' => $request->input('phone', '-'),
            'date' => $date,
            'paymentLabel' => $request->input('payment_label', 'Nao informado'),
            'brandLabel' => $request->input('brand_label', 'Nao informado'),
            'qtyInstallments' => (int)$request->input('qty_installments', 1),
            'subtotal' => (float)$request->input('subtotal', 0),
            'pixAmount' => (float)$request->input('pix_amount', 0),
            'debitAmount' => (float)$request->input('debit_amount', 0),
            'creditTotal' => (float)$request->input('credit_total', 0),
            'installmentAmount' => (float)$request->input('installment_amount', 0),
            'items' => $items,
        ]);
    }



}
