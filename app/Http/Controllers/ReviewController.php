<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\Avito;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function __construct(
        protected Avito $avito
    )
    {
    }

    public function index(int $account): \Illuminate\Http\JsonResponse|\Inertia\Response|\Inertia\ResponseFactory
    {
        $page = request()->get('page');
        $reviews = $this->avito->setAccount(Account::findOrFail($account))->reviews(page: $page ?? 1);

        $lastPage = ceil($reviews['total'] / 10) - 1;

        $reviews['reviews'] = array_map(function($item) {
            $item['createdAt'] = Carbon::createFromTimestamp($item['createdAt'])->format('d-m-Y H:i');
            return $item;
        }, $reviews['reviews']);

        if($page) {
           return response()->json($reviews['reviews']);
        }

        return inertia('Reviews', [
            'accountId' => $account,
            'reviews' => $reviews['reviews'],
            'lastPage' => $lastPage,
        ]);
    }
}
