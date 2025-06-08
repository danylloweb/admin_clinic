<?php


use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('jwt.auth')->get('/dashboard-data', function () {
    return response()->json(['message' => 'Acesso permitido', 'user' => auth()->user()]);
});
Route::get('startschedule', 'ForgotPasswordController@startSchedule');
Route::get('startGetLastCommand', 'ForgotPasswordController@startGetLastCommand');
Route::group(['middleware' => ['authGateway']], function () {
    Route::get('user-authenticated', 'UsersController@getUserLogged');
    Route::resource('procedures', 'ProceduresController', ['create', 'edit']);
    Route::resource('procedureTypes', 'ProcedureTypesController', ['create', 'edit']);
    Route::resource('patients', 'PatientsController', ['create', 'edit']);
    Route::put('schedule/update-status/{id}', 'SchedulesController@updateStatus');
    Route::resource('schedules', 'SchedulesController', ['create', 'edit']);
    Route::resource('screenings', 'ScreeningsController', ['create', 'edit']);
    Route::resource('clinicalHistories', 'ClinicalHistoriesController', ['create', 'edit']);
    Route::resource('users', 'UsersController', ['create', 'edit']);
    Route::resource('salesOrderItems', 'SalesOrderItemsController', ['create', 'edit']);
    Route::resource('salesOrders', 'SalesOrdersController', ['create', 'edit']);
    Route::put('schedule/salesOrdersItems/{id}', 'SchedulesController@scheduleItem');
    Route::put('saleOrder-update-installment/{id}', 'SalesOrdersController@updateInstallment');
    Route::put('update-status/salesOrders/{id}', 'SalesOrdersController@updateStatus');
    Route::put('update-typePayment/salesOrders/{id}', 'SalesOrdersController@updateTypePayment');
    Route::put('update-brandPayment/salesOrders/{id}', 'SalesOrdersController@updateBrandPayment');
    Route::put('update-partialPayment/salesOrders/{id}', 'SalesOrdersController@updatePartialPayment');
    Route::post('getImageProfile', 'PatientsController@getImageProfile');
    Route::post('getImageProfileById', 'PatientsController@getImageProfileById');
    Route::resource('campaigns', 'CampaignsController', ['create', 'edit']);
    Route::put('replicate/procedure/{id}', 'ProceduresController@replicate');
    Route::put('procedure-update-status/{id}', 'ProceduresController@updateStatus');
    Route::resource('adverts', 'AdvertsController', ['create', 'edit']);
    Route::resource('leads', 'LeadsController', ['create', 'edit']);
    Route::post('register-click', 'AdvertsController@registerClick');
    Route::post('register-click-checkout', 'AdvertsController@registerClickCheckout');
    Route::get('lead-by-code/{code}', 'AdvertsController@getByCode');
    Route::get('getPatientDetail/{id}', 'PatientsController@show');
    Route::resource('followUpMessages', 'FollowUpMessagesController', ['create', 'edit']);
    Route::resource('followUpScheduleMessages', 'FollowUpScheduleMessagesController', ['create', 'edit']);
    Route::resource('followUpSchedules', 'FollowUpSchedulesController', ['create', 'edit']);
    Route::post('send-message-direct', 'FollowUpMessagesController@sendMessageDirect');
    Route::get('schedule-calendar', 'SchedulesController@calendar');

});
Route::any('{any}', function () {
    return response()->json(['message' => 'Unauthorized'], 401);
})->where('any', '.*');

Route::fallback(function () {
    return response()->json(['message' => 'Unauthorized'], 401);
});
