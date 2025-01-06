<?php

use App\Http\Controllers\AvitoController;
use App\Http\Controllers\AvitoWebhookHandler;
use App\Http\Controllers\DigisellerWebhookController;
use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/accounts', [AvitoController::class, 'getAccounts']);

Route::post('/webhook/{account}/{token}', AvitoWebhookHandler::class);

Route::post('t-webhook', TelegramWebhookController::class);
Route::post('d-webhook-order', [DigisellerWebhookController::class, 'order']);
Route::post('d-webhook-message', [DigisellerWebhookController::class, 'message']);
