<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;

class CompanyController extends Controller
{
    public function toggleCompany(int $companyId): RedirectResponse
    {
        $exists = auth()->user()?->companies->pluck('id')->contains($companyId);

        if (!$exists) {
            abort(404);
        }

        session()?->put('selectedCompanyId', $companyId);

        return to_route('home');
    }
}
