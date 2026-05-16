<?php

use App\Http\Controllers\CampaignsController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\UploadsController;
use App\Http\Controllers\PatientMedicalRecordController;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\ProceduresController;
use App\Http\Controllers\FacialEvaluationsController;
use App\Http\Controllers\BodyEvaluationsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/',[PanelController::class,'login'])->name('login');
Route::get('/login', [PanelController::class,'login'])->name('login2');
Route::get('/chat', function () {
    return view('chats.index');
});

Route::get('/prontuario/sucesso', [PatientMedicalRecordController::class, 'successStatus'])->name('patient-medical-record.success');
Route::get('/prontuario/{token}', [PatientMedicalRecordController::class, 'publicForm'])->name('patient-medical-record.show');
Route::post('/prontuario/{token}', [PatientMedicalRecordController::class, 'submitPublicForm'])->name('patient-medical-record.submit');
Route::get('/prontuario/status/{token}', [PatientMedicalRecordController::class, 'formSuccess'])->name('patient-medical-record.status');

// Rotas públicas para assinatura de ficha de avaliação facial
Route::get('/facial-evaluation/{token}', [FacialEvaluationsController::class, 'showSignatureForm'])->name('facial-evaluation.sign');
Route::post('/facial-evaluation/{token}/sign', [FacialEvaluationsController::class, 'processSignature'])->name('facial-evaluation.process-sign');

// Rotas públicas para assinatura de ficha de avaliação corporal
Route::get('/body-evaluation/{token}', [BodyEvaluationsController::class, 'showSignatureForm'])->name('body-evaluation.sign');
Route::post('/body-evaluation/{token}/sign', [BodyEvaluationsController::class, 'processSignature'])->name('body-evaluation.process-sign');

Route::middleware(['jwt.web'])->group(function () {
    Route::get('/dashboard',[PanelController::class,'dashboard'])->name('dashboard');
    Route::get('/panel-schedules-index',[PanelController::class,'scheduleIndex'])->name('panel.schedules.index');
    Route::get('/panel-schedules-print',[PanelController::class,'schedulesPrintPage'])->name('panel.schedules.print');
    Route::get('/panel-schedules-calendar',[PanelController::class,'scheduleCalendar'])->name('panel.schedules.calendar');
    Route::get('/panel-procedures-index',[PanelController::class,'procedureIndex'])->name('panel.procedures.index');
    Route::get('/panel-campaigns-index',[PanelController::class,'campaignIndex'])->name('panel.campaigns.index');
    Route::get('/panel-campaign-show/{id}', [CampaignsController::class, 'panelShow'])->name('panel-campaign-show');
    Route::get('/panel-campaign-create', [CampaignsController::class, 'create'])->name('panel.campaign.create');
    Route::get('/panel-campaign-send/{id}', [CampaignsController::class, 'panelSend'])->name('panel.campaign.send');
    Route::post('/panel-campaign-send/{id}/start', [CampaignsController::class, 'startSend'])->name('panel.campaign.send.start');
    Route::post('/panel-campaign-send/{id}/process', [CampaignsController::class, 'processSend'])->name('panel.campaign.send.process');
    Route::get('/panel-campaign-send/{id}/progress', [CampaignsController::class, 'sendProgress'])->name('panel.campaign.send.progress');
    Route::post('/panel-uploads-store', [UploadsController::class, 'store'])->name('panel.uploads.store');
    Route::get('/panel-patients-index',[PanelController::class,'patientIndex'])->name('panel.patient.index');
    Route::get('/panel-patients-create',[PanelController::class,'patientCreate'])->name('panel.patient.create');
    Route::get('/panel-patients-show/{id}',[PatientsController::class,'patientShow'])->name('panel.patient.show');
    Route::get('/panel-patients-chat/{id}',[PatientsController::class,'patientChat'])->name('panel.patient.chat');
    Route::get('/panel-patients-medical-record-link/{patientId}', [PatientMedicalRecordController::class, 'issueLink'])->name('panel.patient.medical-record.link');
    Route::get('/panel-patients-medical-record-show/{patientId}', [PatientMedicalRecordController::class, 'panelShow'])->name('panel.patient.medical-record.show');
    Route::get('/panel-procedure-show/{id}',[ProceduresController::class,'procedureShow'])->name('panel.procedure.show');
    Route::get('/panel-procedure-create',[PanelController::class,'procedureCreate'])->name('panel.procedure.create');
    Route::get('/panel-sales-orders-index',[PanelController::class,'salesOrderIndex'])->name('panel.sales-order.index');
    Route::get('/panel-sales-orders-create',[PanelController::class,'salesOrderCreate'])->name('panel.sales-order.create');
    Route::match(['get', 'post'], '/panel-sales-orders-invoice',[PanelController::class,'salesOrderInvoice'])->name('panel.sales-order.invoice');
    Route::get('/panel-sales-orders-send-invoice-whatsapp/{id}', [PanelController::class, 'sendSalesOrderInvoiceWhatsapp'])->name('panel.sales-order.invoice.send-whatsapp');
    Route::get('/panel-sales-orders-edit/{id}',[PanelController::class,'salesOrderEdit'])->name('panel.sales-order.edit');
    Route::get('/panel-users-index',[PanelController::class,'usersIndex'])->name('panel.users.index');
    Route::get('/panel-users-edit/{id}',[PanelController::class,'usersEdit'])->name('panel.users.edit');

    // Rotas autenticadas para ficha de avaliação facial
    Route::get('/panel-facial-evaluations-index/{patientId}', [FacialEvaluationsController::class, 'panelIndex'])->name('panel.facial-evaluations.index');
    Route::get('/panel-facial-evaluations-create/{patientId}', [FacialEvaluationsController::class, 'create'])->name('panel.facial-evaluations.create');
    Route::post('/panel-facial-evaluations-store', [FacialEvaluationsController::class, 'store'])->name('panel.facial-evaluations.store');
    Route::get('/panel-facial-evaluations-show/{id}', [FacialEvaluationsController::class, 'show'])->name('panel.facial-evaluations.show');
    Route::get('/panel-facial-evaluations-edit/{id}', [FacialEvaluationsController::class, 'edit'])->name('panel.facial-evaluations.edit');
    Route::put('/panel-facial-evaluations-update/{id}', [FacialEvaluationsController::class, 'update'])->name('panel.facial-evaluations.update');
    Route::delete('/panel-facial-evaluations-delete/{id}', [FacialEvaluationsController::class, 'destroy'])->name('panel.facial-evaluations.destroy');
    Route::post('/panel-facial-evaluations-generate-token/{id}', [FacialEvaluationsController::class, 'generateSignatureToken'])->name('panel.facial-evaluations.generate-token');
    Route::post('/panel-facial-evaluations-send-link/{id}', [FacialEvaluationsController::class, 'sendSignatureLink'])->name('panel.facial-evaluations.send-link');

    // Rotas autenticadas para ficha de avaliação corporal
    Route::get('/panel-body-evaluations-index/{patientId}', [BodyEvaluationsController::class, 'panelIndex'])->name('panel.body-evaluations.index');
    Route::get('/panel-body-evaluations-create/{patientId}', [BodyEvaluationsController::class, 'create'])->name('panel.body-evaluations.create');
    Route::post('/panel-body-evaluations-store', [BodyEvaluationsController::class, 'store'])->name('panel.body-evaluations.store');
    Route::get('/panel-body-evaluations-show/{id}', [BodyEvaluationsController::class, 'show'])->name('panel.body-evaluations.show');
    Route::get('/panel-body-evaluations-edit/{id}', [BodyEvaluationsController::class, 'edit'])->name('panel.body-evaluations.edit');
    Route::put('/panel-body-evaluations-update/{id}', [BodyEvaluationsController::class, 'update'])->name('panel.body-evaluations.update');
    Route::delete('/panel-body-evaluations-delete/{id}', [BodyEvaluationsController::class, 'destroy'])->name('panel.body-evaluations.destroy');
    Route::post('/panel-body-evaluations-generate-token/{id}', [BodyEvaluationsController::class, 'generateSignatureToken'])->name('panel.body-evaluations.generate-token');
    Route::post('/panel-body-evaluations-send-link/{id}', [BodyEvaluationsController::class, 'sendSignatureLink'])->name('panel.body-evaluations.send-link');
});

