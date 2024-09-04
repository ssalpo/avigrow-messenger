<?php

namespace App\Http\Controllers;

use App\Http\Requests\FastTemplateRequest;
use App\Models\FastTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FastTemplateController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(FastTemplate::orderByDesc('updated_at')->get());
    }

    public function store(FastTemplateRequest $request): void
    {
        FastTemplate::create($request->validated());
    }

    public function update(int $id, FastTemplateRequest $request): void
    {
        FastTemplate::findOrFail($id)->update($request->validated());
    }

    public function destroy(int $id): void
    {
        $fastTemplate = FastTemplate::findOrFail($id);

        $fastTemplate->delete();
    }
}
