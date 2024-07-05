<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\SupplyChainController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



// Route:: ('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/login', [AuthController::class, 'login']);


    Route::middleware('auth.sanctum')->group(function () {

        Route::get('/get-profile', [AuthController::class, 'getProfile']);

        // CLIENT
        Route::post('/add-client', [ClientController::class, 'addClient']);
        Route::get('/all-client', [ClientController::class, 'allClient']);
        // MACHINE
        Route::post('/add-machine', [MachineController::class, 'addMachine']);
        Route::get('/all-machine', [MachineController::class, 'allMachine']);
        // USER
        Route::post('/add-user', [UserController::class, 'addUser']);
        Route::get('/all-user', [UserController::class, 'allUser']);
        // SUPPLY_CHAIN
        Route::post('/add-supply', [SupplyChainController::class, 'addsupply']);
        Route::get('/get-spinner-details', [SupplyChainController::class, 'getSpinnerDetails']);
        Route::get('/all-supply', [SupplyChainController::class, 'allSupply']);

    
    });