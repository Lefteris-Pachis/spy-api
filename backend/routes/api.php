<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SpyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->post('/spies', [SpyController::class, 'store']);

Route::middleware('api')->group(function () {
    // User registration
    Route::post('/register', [AuthController::class, 'register']);

    // User login
    Route::post('/login', [AuthController::class, 'login']);
});
