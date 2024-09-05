<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewScheduleRequest;
use App\Http\Resources\ReviewScheduleResource;
use App\Models\ReviewSchedule;
use Illuminate\Http\RedirectResponse;

class ReviewScheduleController extends Controller
{
    public function index(string $account): \Inertia\Response
    {
        $reviews = ReviewSchedule::forAccount($account)->orderByDesc('created_at')->get();

        return inertia('ReviewScheduleList', ['reviews' => ReviewScheduleResource::collection($reviews)->collection]);
    }

    public function store(ReviewScheduleRequest $request): void
    {
        if (!ReviewSchedule::hasAnyForChat($request->chat_id)) {
            ReviewSchedule::create($request->validated() + ['send_at' => now()->addMonth()->setTime(19, 0, 0)]);
        }
    }

    public function destroy(int $account, int $reviewSchedule): RedirectResponse
    {
       ReviewSchedule::where('id', $reviewSchedule)->firstOrFail()->delete();

        return redirect()->back();
    }
}
