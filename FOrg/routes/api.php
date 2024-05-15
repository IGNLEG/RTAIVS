<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventEquipmentController;
use App\Http\Controllers\EventFileController;
use App\Http\Controllers\EventMessageController;
use App\Http\Controllers\EventStaffController;
use App\Http\Controllers\EventsVehicleController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\QualificationController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\WarehouseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    $request->user()->getRoleNames();
    return $request->user();
});

Route::group(['middleware' => ['auth:sanctum', 'admin']], function(){
    Route::apiResource('/qualifications', QualificationController::class);
    Route::post('/warehouses', [WarehouseController::class, 'store']);
    Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'update']);
    Route::get('/warehouses/{warehouse}', [WarehouseController::class, 'show']);
    Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroy']);
    Route::apiResource('/clients', ClientController::class);
    Route::put('/companies/{company}', [CompanyController::class, 'update']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy']);
    Route::delete('/users/{id}', [RegisteredUserController::class, 'destroy']);
    Route::get('/users', [RegisteredUserController::class, 'index']);
    Route::put('/users/{id}/role', [RegisteredUserController::class, 'changeUserRole']);

});
Route::group(['middleware' => ['auth:sanctum', 'organizer']], function(){
    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);
    Route::get('/users/event/org', [RegisteredUserController::class, 'index']);
    Route::get('/clients/event/org', [ClientController::class, 'index']);

    Route::delete('/event/files/{id}', [EventFileController::class, 'destroy']);
    Route::post('/event/files', [EventFileController::class, 'store']);

});
Route::group(['middleware' => ['auth:sanctum', 'warehouse']], function(){
    Route::apiResource('/tags', TagController::class);
    Route::post('/vehicles', [VehicleController::class, 'store']);
    Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update']);
    Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy']);
    Route::post('/equipment', [EquipmentController::class, 'store']);
    Route::put('/equipment/{equipment}', [EquipmentController::class, 'update']);
    Route::delete('/equipment/{equipment}', [EquipmentController::class, 'destroy']);
    Route::get('/warehouses', [WarehouseController::class, 'index']);

});
Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show']);
    Route::get('/equipment', [EquipmentController::class, 'index']);
    Route::get('/equipment/{equipment}', [EquipmentController::class, 'show']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::get('/companies/{company}', [CompanyController::class, 'show']);
    Route::get('/event/{id}/files', [EventFileController::class, 'index']);
    Route::get('/events/{id}/equipment', [EventEquipmentController::class, 'ShowEventEquipment']);
    Route::put('/events/{id}/equipment', [EventEquipmentController::class, 'UpdateEquipment']);
    Route::get('/events/{id}/vehicles', [EventsVehicleController::class, 'ShowEventVehicles']);
    Route::put('/events/{id}/vehicles', [EventsVehicleController::class, 'UpdateVehicles']);
    Route::get('/event/files/{id}', [EventFileController::class, 'show']);
    Route::get('/staff/events', [EventStaffController::class, 'getEmployeeEventsByDate']);
    Route::get('/event/staff', [EventStaffController::class, 'getEventEmployees']);
    Route::get('/inbox', [MessageController::class, 'index']);
    Route::post('/inbox', [MessageController::class, 'store']);
    Route::delete('/inbox/{id}', [MessageController::class, 'delete']);
    Route::get('/events/{id}/messages', [EventMessageController::class, 'index']);
    Route::post('/events/{id}/messages', [EventMessageController::class, 'store']);
    Route::delete('/events/{id}/messages/{m_id}', [EventMessageController::class, 'destroy']);
    Route::get('/inbox/users/names', [RegisteredUserController::class, 'names']);
    Route::get('/clients/{id}/name', [ClientController::class, 'name']);
    Route::get('/users/{id}', [RegisteredUserController::class, 'show']);
    Route::put('/users/{user}', [RegisteredUserController::class, 'update']);
});
