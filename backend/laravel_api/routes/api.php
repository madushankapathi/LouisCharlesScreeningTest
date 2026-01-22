<?php

use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

//Route::middleware('auth:sanctum')->group(function () {
//    Route::prefix('v1')->group(function () {
//        Route::apiResource('bookings', BookingController::class);
//        Route::get('analytics/bookings', [AnalyticsController::class, 'index']);
//    });
//});

//Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
//    Route::apiResource('bookings', BookingController::class);
//    Route::get('analytics/bookings', [AnalyticsController::class, 'index']);
//});

Route::middleware(['auth:sanctum', 'tenant'])
    ->prefix('v1')
    ->group(function () {
        Route::apiResource('bookings', \App\Http\Controllers\Api\BookingController::class);
        Route::get('analytics/bookings', [AnalyticsController::class, 'index']);
    });

//Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
//    Route::get('analytics/bookings', [AnalyticsController::class, 'index']);
//});

