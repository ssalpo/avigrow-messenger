<?php

namespace App\Http\Controllers;

use App\Models\FmTag;
use Illuminate\Http\JsonResponse;

class FmTagController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            FmTag::currentCompany()->pluck('name')
        );
    }
}
