<?php

namespace App\Http\Controllers;

use App\Http\Requests\FastTemplateRequest;
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
            FmTag::has('fastTemplates')->with('fastTemplates')->get()
        );
    }

    public function store(FastTemplateRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $fastTemplate = FastTemplate::create($request->validated());

            $tag = FmTag::query()->firstOrCreate(['name' => $request->tag]);

            $fastTemplate->fmTags()->sync($tag);
        });

        return redirect()->back();
    }

    public function update(int $id, FastTemplateRequest $request): void
    {
        DB::transaction(function () use ($id, $request) {
            $fastTemplate = FastTemplate::findOrFail($id);

            $fastTemplate->update($request->validated());

            $tag = FmTag::query()->firstOrCreate(['name' => $request->tag]);

            $fastTemplate->fmTags()->sync($tag);
        });
    }

    public function destroy(int $id): void
    {
        $fastTemplate = FastTemplate::findOrFail($id);

        $fastTemplate->delete();
    }
}
