<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutocompleteController;
use App\Http\Controllers\CodeKeyController;
use App\Http\Controllers\FastTemplateController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewScheduleController;
use App\Http\Controllers\TransactionController;
use App\Models\Account;
use App\Services\Avito;
use Illuminate\Support\Facades\Route;

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'auth']);

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('/send-payment-receipt', [HomeController::class, 'sendPaymentReceipt'])->name('send-payment-receipt');

    Route::get('transactions/statistics', [TransactionController::class, 'statistics'])->name('transactions.statistics');
    Route::resource('transactions', TransactionController::class);

    Route::get('products/trash', [ProductController::class, 'trash'])->name('products.trash');
    Route::post('products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::resource('products', ProductController::class);

    Route::resource('/fast-templates', FastTemplateController::class);

    Route::group(['prefix' => '/accounts/{account}'], function () {
        Route::resource('schedule-reviews', ReviewScheduleController::class)->only(['index', 'store', 'destroy']);

        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');

        Route::get('chats/{chat}', [HomeController::class, 'messages'])->name('account.chat.messages');
        Route::get('chats', [HomeController::class, 'index'])->name('account.chats');

        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::resource('orders', OrderController::class);

    });

    Route::resource('accounts', AccountController::class);

    Route::get('code-keys/histories', [CodeKeyController::class, 'histories'])->name('code-keys.histories');
    Route::post('code-keys/{code_key}/mark-as-receipt', [CodeKeyController::class, 'markAsReceipt'])->name('code-keys.mark-as-receipt');
    Route::post('code-keys/{code_key}/restore', [CodeKeyController::class, 'restore'])->name('code-keys.restore');
    Route::resource('code-keys', CodeKeyController::class)->only(['index', 'store', 'destroy']);

    Route::group(['prefix' => 'autocomplete', 'as' => 'autocomplete.'], function () {
        Route::get('products', [AutocompleteController::class, 'products'])->name('products');
    });
});

Route::get('redirect', function () {
    $code = request('code');
    $account = request('state');

    if (!$code) {
        echo 'Code not found!';

        return '';
    }

    if (!$account) {
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


Route::get('meta-code-generator', function () {

    $codes = [];

    for ($i = 0; $i < 5; $i++) {
        $code = mt_rand(0, 9);
        $textCode = \Illuminate\Support\Str::random(5);

        $textCode[mt_rand(0, 4)] = $code;

        $codes[] = $textCode;
    }

    return response(
        mb_strtoupper(implode('-', $codes))
    );
});
