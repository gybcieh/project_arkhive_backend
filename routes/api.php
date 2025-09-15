<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PcvlController;
use App\Http\Controllers\Api\TownController;
use App\Http\Controllers\Api\BarangayController;
use App\Http\Controllers\Api\PurokController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('pcvls/search', [PcvlController::class, 'search']);
Route::get('pcvls/kbbl', [PcvlController::class, 'fetchKbbls']);
Route::post('pcvls/update/tags/{pcvl}', [PcvlController::class, 'updateVoterTag']);
Route::post('pcvls/update/roles/{pcvl}', [PcvlController::class, 'updateVoterRole']);

Route::apiResource('users', UserController::class);
Route::apiResource('pcvls', PcvlController::class)->only(['index', 'show']);
Route::get('pcvls/{pcvl}', [PcvlController::class, 'show']);
Route::apiResource('towns', TownController::class)->only(['index']);
Route::apiResource('barangays', BarangayController::class)->only(['index']);
Route::apiResource('puroks', PurokController::class)->only(['index']);