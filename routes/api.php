<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\DebugController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/purchase', [PurchaseController::class, 'store']);
Route::get('/report/purchase-summary', [ReportController::class, 'purchaseSummary']);
Route::get('/debug/sql-analysis', [DebugController::class, 'sqlAnalysis']);