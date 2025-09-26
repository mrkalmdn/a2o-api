<?php

use App\Http\Controllers\Api\V1\ExportController;
use App\Http\Controllers\Api\V1\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/reports', ReportController::class);
Route::get('/exports', ExportController::class);
