<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class AutocompleteController extends Controller
{
    public function products()
    {
        return response()->json(Product::all('id', 'name'));
    }
}
