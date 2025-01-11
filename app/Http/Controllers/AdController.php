<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Ad;
use Illuminate\Http\JsonResponse;

class AdController extends Controller
{
    public function index(int $accountId): JsonResponse
    {
        return response()->json(
            Ad::whereAccountId($accountId)->get(['external_id', 'price', 'title', 'url'])
        );
    }
}
