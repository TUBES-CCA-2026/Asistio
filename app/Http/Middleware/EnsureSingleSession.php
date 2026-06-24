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
        if (Auth::check()) {
            $user = Auth::user();
            $currentId = $request->session()->getId();

            if ($user->current_session_id && $user->current_session_id !== $currentId) {
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