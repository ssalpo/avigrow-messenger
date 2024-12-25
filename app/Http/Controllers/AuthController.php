<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(): \Inertia\Response|\Inertia\ResponseFactory
    {
        $users = User::all();

        return inertia('Auth/Login', compact('users'));
    }

    public function auth(AuthRequest $request): RedirectResponse
    {
        if (Auth::attempt($request->all())) {
            $request->session()->regenerate();

            return redirect()->to('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
