<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ActiveConversationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutocompleteController;
use App\Http\Controllers\AvitoController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\BotGreetingController;
use App\Http\Controllers\BotQuizController;
use App\Http\Controllers\BotScheduleController;
use App\Http\Controllers\BotScheduleSlotController;
use App\Http\Controllers\BotTriggerController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CodeKeyController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FastTemplateController;
use App\Http\Controllers\FmTagController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewScheduleController;
use App\Models\Account;
use App\Services\Avito;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'guest'], function () {
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'auth']);

    Route::get('signup', [AuthController::class, 'signup'])->name('signup');
    Route::post('register', [AuthController::class, 'register'])->name('register');
});

Route::middleware(['auth'])->group(function () {
    Route::post('accounts/{account}/save-settings', [AccountController::class, 'saveSettings'])->name('accounts.save-settings');
    Route::resource('accounts', AccountController::class)->middleware('auth');

    Route::post('/companies/{company}/toggle', [CompanyController::class, 'toggleCompany'])->name('companies.toggle');

    Route::get('active-conversations', [ActiveConversationController::class, 'getLists'])->name('active-conversations.list');
    Route::delete('active-conversations/{id}', [ActiveConversationController::class, 'destroy'])->name('active-conversations.destroy');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::group(['prefix' => 'autocomplete', 'as' => 'autocomplete.'], function () {

    });
});

Route::middleware(['auth', 'check.accounts'])->group(function () {

    Route::match(array('GET', 'POST'), '/', [ChatController::class, 'index'])->name('home');

    Route::post('/send-payment-receipt', [ChatController::class, 'sendPaymentReceipt'])->name('send-payment-receipt');

    Route::get('products/trash', [ProductController::class, 'trash'])->name('products.trash');
    Route::post('products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::resource('products', ProductController::class);

    Route::resource('/fast-templates', FastTemplateController::class);

    Route::post('/bots/{bot}/update-settings', [BotController::class, 'updateSettings'])->name('bots.update-settings');
    Route::get('/bots/{bot}/connected-add-treeview', [BotController::class, 'connectedAdTreeView'])->name('bots.connected-add-treeview');
    Route::post('/bots/{bot}/change-activity', [BotController::class, 'changeActivity'])->name('bots.change-activity');
    Route::post('/bots/{bot}/change-type/{type}', [BotController::class, 'changeType'])->name('bots.change-type');
    Route::post('/bots/{bot}/attach-accounts', [BotController::class, 'attachAccounts'])->name('bots.attach-accounts');
    Route::post('/bots/{bot}/attach-ads', [BotController::class, 'attachAds'])->name('bots.attach-ads');
    Route::resource('bots', BotController::class);

    Route::post('/bots/{bot}/schedules/{dayOfWeek}/toggle-status', [BotScheduleController::class, 'toggleStatus'])->name('bots.schedules.toggle-status');
    Route::post('/bots/{bot}/schedules/{dayOfWeek}/slots', [BotScheduleSlotController::class, 'store'])->name('bots.schedules.slots.store');
    Route::patch('/bots/{bot}/schedules/{dayOfWeek}/slots/{slot}', [BotScheduleSlotController::class, 'update'])->name('bots.schedules.slots.update');
    Route::delete('/bots/{bot}/schedules/{dayOfWeek}/slots/{slot}', [BotScheduleSlotController::class, 'destroy'])->name('bots.schedules.slots.destroy');

    Route::resource('bots.greetings', BotGreetingController::class);
    Route::resource('bots.triggers', BotTriggerController::class);
    Route::post('/bots/{bot}/quizzes/resort', [BotQuizController::class, 'resort'])->name('bots.quizzes.resort');
    Route::resource('bots.quizzes', BotQuizController::class);

    Route::group(['prefix' => '/accounts/{account}'], function () {
        Route::post('/messages/{chatId}/send-image', [MessageController::class, 'sendImage'])->name('messages.send-image');
        Route::post('/messages/{chatId}/send', [AvitoController::class, 'sendMessage'])->name('messages.send');
        Route::delete('/messages/{chatId}/{messageId}', [AvitoController::class, 'deleteMessage'])->name('messages.destroy');

        Route::resource('schedule-reviews', ReviewScheduleController::class)->only(['index', 'store', 'destroy']);

        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/answer', [ReviewController::class, 'answer'])->name('reviews.answer');
        Route::delete('/reviews/{review}/answer/{answer}', [ReviewController::class, 'answerDestroy'])->name('reviews.answer.destroy');
        Route::get('/chats/{chatId}/info', [ChatController::class, 'chatInfo'])->name('chats.info');
        Route::post('/chats/{chatId}/mark-as-read', [ChatController::class, 'markAsRead'])->name('chats.mark-as-read');

        Route::get('/messages/{chatId}', [AvitoController::class, 'getMessages'])->name('messages.getPaginated');

        Route::match(array('GET', 'POST'), 'chats/{chat}', [ChatController::class, 'messages'])->name('account.chat.messages');
        Route::match(array('GET', 'POST'), 'chats', [ChatController::class, 'index'])->name('account.chats');

    });

    Route::get('code-keys/histories', [CodeKeyController::class, 'histories'])->name('code-keys.histories');
    Route::post('code-keys/{code_key}/mark-as-receipt', [CodeKeyController::class, 'markAsReceipt'])->name('code-keys.mark-as-receipt');
    Route::post('code-keys/{code_key}/restore', [CodeKeyController::class, 'restore'])->name('code-keys.restore');
    Route::resource('code-keys', CodeKeyController::class)->only(['index', 'store', 'destroy']);

    Route::group(['prefix' => 'autocomplete', 'as' => 'autocomplete.'], function () {
        Route::get('products', [AutocompleteController::class, 'products'])->name('products');
    });

    Route::get('fm-tags', [FmTagController::class, 'index'])->name('fm-tags.index');

    Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'sync'])->name('employees.sync');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
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

Route::get('digiseller-sign-generator', function () {
    $token = request('token');
    $time = time();
    $shaToken = hash('sha256', $token . $time);
    return <<<MSG
Token: {$shaToken} <br />
Timestamp: {$time}
MSG;

});
