<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewAnswerRequest;
use App\Models\Account;
use App\Services\Avito;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;

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
        $reviews = $this->avito->setAccount(Account::findOrFail($account))->reviews(limit: 50, page: $page ?? 1);

        $lastPage = ceil($reviews['total'] / 50) - 1;

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

    public function answer(int $accountId, int $reviewId, ReviewAnswerRequest $request): RedirectResponse
    {
        $response = $this->avito
            ->setAccount(Account::findOrFail($accountId))
            ->sendAnswerToReview($reviewId, $request->message);

        return redirect()->back()->with('backData', $response);
    }

    public function answerDestroy(int $accountId, int $reviewId, int $answerId): RedirectResponse
    {
        $this->avito
            ->setAccount(Account::findOrFail($accountId))
            ->deleteReviewAnswer($answerId);

        return redirect()->back();
    }
}
