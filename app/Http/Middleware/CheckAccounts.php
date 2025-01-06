<?php

namespace App\Http\Middleware;

use App\Models\Account;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccounts
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            session('selectedCompanyId') === auth()->user()->myCompany->id &&
            !Account::isOwner()->exists()
        ) {
            return to_route('accounts.index');
        }

        return $next($request);
    }
}
