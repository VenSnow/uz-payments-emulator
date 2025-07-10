<?php

use App\Http\Controllers\CallbackController;
use App\Http\Controllers\NotifyTestController;
use App\Http\Controllers\PaymeController;
use App\Http\Controllers\PaymeJsonRpcController;
use EnumTools\Http\Controllers\EnumController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('enums/{enum}', EnumController::class);

Route::post('/notify-test', [NotifyTestController::class, 'handle']);
Route::get('/callback-test', [CallbackController::class, 'handle']);

Route::post('payme', PaymeJsonRpcController::class);
