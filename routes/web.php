<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FastTemplateController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\ReviewScheduleController;
use App\Models\Account;
use App\Models\AnalyzeReview;
use Illuminate\Support\Facades\Route;

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'auth']);

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/accounts/{account}/chats/{chat}', [HomeController::class, 'messages'])->name('account.chat.messages');
    Route::get('/accounts/{account}/chats', [HomeController::class, 'index'])->name('account.chats');
    Route::resource('/fast-templates', FastTemplateController::class);

    Route::group(['prefix' => '/accounts/{account}'], function() {
        Route::resource('schedule-reviews', ReviewScheduleController::class)->only(['index', 'store', 'destroy']);
    });
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

    $account = \App\Models\Account::findOrFail($account);

    $response = (new \App\Services\Avito)->getTokenByCode($code);

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

Route::get('/test', function (\App\Services\Avito $avito) {
    $accountId = 1;

    $account = Account::findOrFail($accountId);
    $items = $avito->setAccount($account)->reviews();

    foreach ($items['reviews'] as $review) {

    }

});
