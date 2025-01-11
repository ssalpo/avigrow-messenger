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
            FmTag::currentCompany()
                ->has('fastTemplates')
                ->with(['fastTemplates' => fn ($q) => $q->orderByDesc('number_of_uses')])
                ->get()
        );
    }

    public function all(int $accountId): JsonResponse
    {
        return response()->json(
            FastTemplate::currentCompany()->orderBy('number_of_uses')->get()
        );
    }

    public function store(int $accountId, FastTemplateRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $companyParams = ['company_id' => request()->attributes->get('currentCompanyId')];

            $fastTemplate = FastTemplate::create($request->validated() + $companyParams);

            $tag = FmTag::currentCompany()->firstOrCreate(['name' => $request->tag] + $companyParams);

            $fastTemplate->fmTags()->sync($tag);
        });

        return redirect()->back();
    }

    public function update(int $accountId, int $id, FastTemplateRequest $request): void
    {
        DB::transaction(function () use ($id, $request) {
            $fastTemplate = FastTemplate::currentCompany()->findOrFail($id);

            $fastTemplate->update($request->validated());

            $tag = FmTag::currentCompany()->firstOrCreate(['name' => $request->tag]);

            $fastTemplate->fmTags()->sync($tag);
        });
    }

    public function destroy(int $accountId, int $id): void
    {
        $fastTemplate = FastTemplate::currentCompany()->findOrFail($id);

        $fastTemplate->delete();
    }

    public function incrementUses(int $accountId, int $id): void
    {
        $currentCompanyId = request()->attributes->get('currentCompanyId');

        IncrementUsingFastTemplate::dispatch($currentCompanyId, $id);
    }
}
