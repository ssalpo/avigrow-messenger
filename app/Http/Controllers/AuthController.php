<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\AuthRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Auth/Login');
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

    public function signup(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Auth/Signup');
    }

    public function register(RegisterRequest $request)
    {
        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $company = Company::create(['name' => $request->company, 'created_by' => $user->id]);

            $company->users()->attach($user->id);

            return $user;
        });

        Auth::loginUsingId($user->id);

        return to_route('accounts.index');
    }
}
