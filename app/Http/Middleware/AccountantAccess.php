<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountantAccess
{
    private const ALLOWED_ROUTES = [
        'managedashboard',
        'accountant.payments.index',
        'accountant.payments.edit',
        'accountant.payments.update',
        'history',
        'logout',
        'dashboard.profile.show',
        'dashboard.profile.edit',
        'dashboard.profile.update',
        'dashboard.profile.password.edit',
        'dashboard.profile.password.update',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (
            strtolower($request->user()->position ?? '') === 'accountant'
            && !in_array($request->route()->getName(), self::ALLOWED_ROUTES, true)
        ) {
            abort(403, 'You are not authorized to access this page.');
        }

        return $next($request);
    }
}
