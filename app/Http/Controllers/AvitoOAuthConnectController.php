<?php

namespace App\Http\Controllers;

use App\Services\AccountService;
use Illuminate\Http\Request;

class AvitoOAuthConnectController extends Controller
{
    public function __invoke(Request $request, AccountService $accountService): \Inertia\Response|\Inertia\ResponseFactory
    {
        $state = request('state');
        $code = request('code');

        if (!$state || !$code) {
            return inertia(
                'OAuthConnect',
                ['authConnectError' => 'Некорректные данные переданы, обратитесь к администратору!']
            );
        }

        $account = $accountService->connectOAuth($state, $code)->makeVisible(['connection_status']);

        return inertia('OAuthConnect', ['account' => $account]);
    }
}
