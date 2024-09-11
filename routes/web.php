<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CodeKeyController;
use App\Http\Controllers\FastTemplateController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewScheduleController;
use App\Models\Account;
use App\Services\Avito;
use Illuminate\Support\Facades\Route;

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'auth']);

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::resource('/fast-templates', FastTemplateController::class);

    Route::group(['prefix' => '/accounts/{account}'], function() {
        Route::resource('schedule-reviews', ReviewScheduleController::class)->only(['index', 'store', 'destroy']);

        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');

        Route::get('chats/{chat}', [HomeController::class, 'messages'])->name('account.chat.messages');
        Route::get('chats', [HomeController::class, 'index'])->name('account.chats');
    });

    Route::get('code-keys/histories', [CodeKeyController::class, 'histories'])->name('code-keys.histories');
    Route::post('code-keys/{code_key}/mark-as-receipt', [CodeKeyController::class, 'markAsReceipt'])->name('code-keys.mark-as-receipt');
    Route::post('code-keys/{code_key}/restore', [CodeKeyController::class, 'restore'])->name('code-keys.restore');
    Route::resource('code-keys', CodeKeyController::class)->only(['index', 'store', 'destroy']);
});

Route::get('redirect', function() {
    $code = request('code');
    $account = request('state');

    if(!$code) {
        echo 'Code not found!';

        return '';
    }

    if(!$account) {
        echo 'Account not found!';

        return '';
    }

    $account = Account::findOrFail($account);

    $response = (new Avito)->getTokenByCode($code);

    $account->update([
        'external_access_token' => $response['access_token'],
        'external_refresh_token' => $response['refresh_token'],
        'external_access_token_expire_in' => $response['expires_in'],
    ]);

    return redirect('/');
});


Route::group(['prefix' => 'pwa', 'as' => 'pwa.'], static function () {
    Route::get('manifest', [PwaController::class, 'manifest'])->name('manifest');
});
