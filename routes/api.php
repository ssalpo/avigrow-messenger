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

Route::get('/chats/{account}/{chatId}/info', [AvitoController::class, 'chatInfo']);

Route::post('/chats/{account}/{chatId}/mark-as-read', [AvitoController::class, 'markAsRead'])->name('chats.mark-as-read');

Route::get('/messages/{account}/{chatId}', [AvitoController::class, 'getMessages']);

Route::post('/messages/{account}/{chatId}/send', [AvitoController::class, 'sendMessage']);

Route::delete('/messages/{account}/{chatId}/{messageId}', [AvitoController::class, 'deleteMessage']);

Route::post('/webhook/{account}', AvitoWebhookHandler::class);

Route::post('t-webhook', TelegramWebhookController::class);
Route::post('d-webhook-order', [DigisellerWebhookController::class, 'order']);
Route::post('d-webhook-message', [DigisellerWebhookController::class, 'message']);
