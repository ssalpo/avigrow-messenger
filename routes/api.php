<?php

use App\Http\Controllers\AvitoController;
use App\Http\Controllers\FastTemplateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('/fast-templates', FastTemplateController::class);

Route::get('/accounts', [AvitoController::class, 'getAccounts']);

Route::get('/chats/{account}', [AvitoController::class, 'chats']);

Route::get('/chats/{account}/{chatId}/info', [AvitoController::class, 'chatInfo']);

Route::post('/chats/{account}/{chatId}/mark-as-read', [AvitoController::class, 'markAsRead'])->name('chats.mark-as-read');

Route::get('/messages/{account}/{chatId}', [AvitoController::class, 'getMessages']);

Route::post('/messages/{account}/{chatId}/send', [AvitoController::class, 'sendMessage']);

Route::delete('/messages/{account}/{chatId}/{messageId}', [AvitoController::class, 'deleteMessage']);

Route::post('/webhook/{account}', [AvitoController::class, 'handleWebhook']);
