<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// public route
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/kendaraan', [KendaraanController::class, 'index']);
Route::get('/kendaraan/{id}', [KendaraanController::class, 'show']);

Route::middleware(['auth:sanctum'])->group(function () {
    //ADMIN
    Route::post('/kendaraan/add', [KendaraanController::class, 'store']);
    Route::post('/kendaraan/{id}/edit', [KendaraanController::class, 'update']);
    Route::post('/kendaraan/{id}/delete', [KendaraanController::class,'destroy',]);


    //USERS
    Route::get('/pesanan', [PesananController::class, 'index']);
    Route::get('/pesanan/{id}', [PesananController::class, 'show']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/pesanan/add', [PesananController::class, 'store']);
    Route::post('/pesanan/{id}/edit', [PesananController::class, 'update']);
    Route::post('/pesanan/{id}/delete', [PesananController::class, 'destroy']);
});
