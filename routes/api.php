<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RequestController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);
Route::get('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::controller(RequestController::class)->middleware('auth:sanctum')
    ->prefix('requests')
    ->group(function () {
        Route::get('/', 'list');
        Route::get('/{id}', 'detail');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'delete');
        Route::post('/new','store');
        Route::post('/{id}/run','run');

        Route::get('/{id}/history','getHistory');
});
