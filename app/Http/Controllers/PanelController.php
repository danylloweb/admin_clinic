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
            'title' => 'Dashboard',
            'subtitle' => 'Painel de Controle',
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function procedureIndex(): View|Factory|Application
    {
        return view('procedures.index', [
            'title' => 'Procedimentos',
            'subtitle' => 'Lista de Procedimentos',
        ]);
    }



}
