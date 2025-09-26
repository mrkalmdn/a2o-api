<?php

use App\Http\Controllers\Api\V1\ExportController;
use App\Http\Controllers\Api\V1\MarketController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    /** @var User $user */
    $user = User::query()->where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return response()->json([
        'token' => $user->createToken($request->header('user-agent'))->plainTextToken
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
    });

    Route::get('/reports', ReportController::class);
    Route::get('/exports', ExportController::class);
    Route::get('/markets', MarketController::class);
});
