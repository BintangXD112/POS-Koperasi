<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveSession
{
	/**
	 * Handle an incoming request.
	 * - Memastikan sesi masih aktif berdasarkan last_activity
	 * - Memperbarui timestamp aktivitas setiap request
	 */
	public function handle(Request $request, Closure $next): Response
	{
		$lifetimeMinutes = (int) config('session.lifetime', 120);
		$now = now()->timestamp;

		$lastActivity = (int) $request->session()->get('last_activity', $now);
		$timeoutAt = $lastActivity + ($lifetimeMinutes * 60);

		if ($now > $timeoutAt) {
			// Session timeout → logout aman
			if ($request->user()) {
				auth()->logout();
			}
			$request->session()->invalidate();
			$request->session()->regenerateToken();

			return redirect('/login')->with('error', 'Sesi berakhir karena tidak ada aktivitas. Silakan login kembali.');
		}

		// Update last activity timestamp
		$request->session()->put('last_activity', $now);

		$response = $next($request);

		// Prevent caching on authenticated pages to block back navigation after logout
		$response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
		$response->headers->set('Pragma', 'no-cache');
		$response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

		return $response;
	}
}
