<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeSyncRequest;
use App\Models\Company;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        $employees = User::employee()->get();

        return inertia('Employee/Index', compact('employees'));
    }

    public function sync(EmployeeSyncRequest $request): RedirectResponse
    {
        $user = User::whereEmail($request->email)->first();
        $password = Str::random();

        if (is_null($user)) {

            if ($request->alreadyHasAccount) {
                throw ValidationException::withMessages(['email' => 'User does not exist.']);
            }

            $user = User::create($request->validated() + ['password' => bcrypt($password)]);
        }

        $user->companies()->syncWithoutDetaching(auth()->user()->myCompany);

        UserService::refreshRelatedCompaniesCache($user);

        if ($user->wasRecentlyCreated) {
            return redirect()->back()->with('backData', ['employeeAccountPassword' => $password]);
        }

        return redirect()->back();
    }

    public function destroy(int $userId): RedirectResponse
    {
        $user = User::employee()->findOrFail($userId);
        $myCompany = auth()->user()->myCompany;

        $user->companies()->detach($myCompany);

        UserService::refreshRelatedCompaniesCache($user);

        return redirect()->back();
    }
}
