<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class PwaController extends Controller
{
    public function manifest()
    {
        return view('manifest');
    }

}
