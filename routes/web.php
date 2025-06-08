<?php

use App\Http\Controllers\PanelController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/',[PanelController::class,'login'])->name('login');
Route::get('/login', [PanelController::class,'login'])->name('login2');

Route::middleware(['jwt.web'])->group(function () {
    Route::get('/dashboard',[PanelController::class,'dashboard'])->name('dashboard');
    Route::get('/panel-procedures-index',[PanelController::class,'procedureIndex'])->name('panel.procedures.index');
});

