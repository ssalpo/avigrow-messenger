<?php

namespace App\Http\Controllers;

use App\Http\Requests\FastTemplateRequest;
use App\Jobs\IncrementUsingFastTemplate;
use App\Models\FastTemplate;
use App\Models\FmTag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FastTemplateController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            FmTag::relatedToMe()
                ->has('fastTemplates')
                ->with(['fastTemplates' => fn ($q) => $q->orderByDesc('number_of_uses')])
                ->get()
        );
    }

    public function store(FastTemplateRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $fastTemplate = FastTemplate::create($request->validated());

            $tag = FmTag::relatedToMe()->firstOrCreate(['name' => $request->tag]);

            $fastTemplate->fmTags()->sync($tag);
        });

        return redirect()->back();
    }

    public function update(int $id, FastTemplateRequest $request): void
    {
        DB::transaction(function () use ($id, $request) {
            $fastTemplate = FastTemplate::relatedToMe()->findOrFail($id);

            $fastTemplate->update($request->validated());

            $tag = FmTag::relatedToMe()->firstOrCreate(['name' => $request->tag]);

            $fastTemplate->fmTags()->sync($tag);
        });
    }

    public function destroy(int $id): void
    {
        $fastTemplate = FastTemplate::relatedToMe()->findOrFail($id);

        $fastTemplate->delete();
    }

    public function incrementUses(int $id): void
    {
        IncrementUsingFastTemplate::dispatch($id);
    }
}
