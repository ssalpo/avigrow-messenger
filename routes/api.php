<?php

use App\Http\Controllers\AvitoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/accounts', [AvitoController::class, 'getAccounts']);

Route::get('/chats/{account}', [AvitoController::class, 'chats']);

Route::get('/chats/{account}/{chatId}/info', [AvitoController::class, 'chatInfo']);

Route::get('/messages/{account}/{chatId}', [AvitoController::class, 'getMessages']);

Route::post('/messages/{account}/{chatId}/send', [AvitoController::class, 'sendMessage']);

Route::post('/webhook/{account}', [AvitoController::class, 'handleWebhook']);
