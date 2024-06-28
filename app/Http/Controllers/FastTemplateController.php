<?php

namespace App\Http\Controllers;

use App\Models\FastTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FastTemplateController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(FastTemplate::orderByDesc('updated_at')->get());
    }

    public function store(Request $request)
    {
        $request->validate(['content' => 'required|min:2']);

        FastTemplate::create([
            'content' => $request->post('content')
        ]);
    }

    public function update(int $id, Request $request)
    {
        $request->validate(['content' => 'required|min:2']);

        FastTemplate::findOrFail($id)->update([
            'content' => $request->post('content')
        ]);
    }

    public function destroy(int $id): void
    {
        $fastTemplate = FastTemplate::findOrFail($id);

        $fastTemplate->delete();
    }
}
