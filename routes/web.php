<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

Route::get('/accounts/{account}/chats/{chat}', [HomeController::class, 'messages'])->name('account.chat.messages');
Route::get('/accounts/{account}/chats', [HomeController::class, 'index'])->name('account.chats');
