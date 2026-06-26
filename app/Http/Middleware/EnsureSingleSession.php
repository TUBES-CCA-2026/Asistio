<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleSession
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jangan jalankan pengecekan ini di route login/logout itu sendiri —
        // mencegah kemungkinan redirect loop balik ke halaman yang sama.
        if ($request->routeIs('login') || $request->routeIs('logout')) {
            return $next($request);
        }

        if (Auth::check()) {
            $user = Auth::user();
            $currentId = $request->session()->getId();

            if ($user->current_session_id && $user->current_session_id !== $currentId) {
                $user->update(['current_session_id' => null]); // ← INI yang sebelumnya hilang
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Akun Anda telah login di perangkat/browser lain. Sesi ini otomatis diakhiri.');
            }
        }

        return $next($request);
    }
}