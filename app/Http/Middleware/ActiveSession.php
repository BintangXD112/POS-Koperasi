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

			return redirect()->route('login')->with('error', 'Sesi berakhir karena tidak ada aktivitas. Silakan login kembali.');
		}

		// Update last activity timestamp
		$request->session()->put('last_activity', $now);

		return $next($request);
	}
}
