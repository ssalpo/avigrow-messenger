<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewAnswerRequest;
use App\Services\Avito;
use App\Services\GeminiService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(
        protected Avito $avito
    )
    {
    }

    public function index(): \Illuminate\Http\JsonResponse|\Inertia\Response|\Inertia\ResponseFactory
    {
        $page = request()->get('page');
        $account = \request()->attributes->get('activeAccount');

        $reviews = $this->avito->setAccount($account)->reviews(limit: 50, page: $page ?? 1);

        $lastPage = ceil($reviews['total'] / 50) - 1;

        $reviews['reviews'] = array_map(function ($item) {
            $item['createdAt'] = Carbon::createFromTimestamp($item['createdAt'])->format('d-m-Y H:i');
            return $item;
        }, $reviews['reviews']);

        if ($page) {
            return response()->json($reviews['reviews']);
        }

        return inertia('Reviews', [
            'accountId' => $account->id,
            'reviews' => $reviews['reviews'],
            'lastPage' => $lastPage,
        ]);
    }

    public function answer(int $reviewId, ReviewAnswerRequest $request): RedirectResponse
    {
        $account = \request()->attributes->get('activeAccount');

        $response = $this->avito
            ->setAccount($account)
            ->sendAnswerToReview($reviewId, $request->message);

        return redirect()->back()->with('backData', [
            'reviewAnswerResponse' => $response
        ]);
    }

    public function answerDestroy(int $reviewId, int $answerId): RedirectResponse
    {
        $this->avito
            ->setAccount(\request()->attributes->get('account'))
            ->deleteReviewAnswer($answerId);

        return redirect()->back();
    }

    public function aiAnswerGenerator(Request $request)
    {
        if (!$request->text || !$request->context) {
            return;
        }

        return response()->json(
            (new GeminiService)->processReviewAnswer($request->context, $request->text)
        );
    }
}
