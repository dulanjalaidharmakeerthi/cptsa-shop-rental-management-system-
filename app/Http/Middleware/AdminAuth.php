<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if session contains admin_id
        if (!session()->has('admin_id')) {
            return redirect('/admin/login')->with('error', 'Please login as admin to access this page.');
        }

        return $next($request);
    }
}
