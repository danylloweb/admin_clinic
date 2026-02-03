<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
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



}
