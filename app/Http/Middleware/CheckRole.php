<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;
        
        if (!$userRole) {
            abort(403, 'Unauthorized action.');
        }

        if (!in_array($userRole->name, $roles)) {
            // If user tries to access another role's area, redirect them to their own dashboard
            if (method_exists($request->user(), 'isAdmin') && $request->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            if (method_exists($request->user(), 'isKasir') && $request->user()->isKasir()) {
                return redirect()->route('kasir.dashboard');
            }
            if (method_exists($request->user(), 'isGudang') && $request->user()->isGudang()) {
                return redirect()->route('gudang.dashboard');
            }
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
