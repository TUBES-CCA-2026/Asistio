<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /** Tampilkan halaman login — tanpa pemilihan role */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect($this->redirectPath());
        }
        return view('auth.login');
    }

    /** Proses login — sistem auto-deteksi role dari database */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username harus diisi.',
            'password.required' => 'Password harus diisi.',
        ]);

        // Rate limiter: max 5 percobaan per menit
        $key = Str::lower($request->input('username')).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        if (!Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->boolean('remember'))) {
            RateLimiter::hit($key);
            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        // Sistem auto-redirect berdasarkan role di database
        // Catat session ini sebagai satu-satunya sesi aktif milik akun ini.
        // Login dari device lain akan menimpa nilai ini, sehingga sesi lama otomatis tidak valid.
        Auth::user()->update(['current_session_id' => $request->session()->getId()]);

        return redirect($this->redirectPath());
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::user()?->update(['current_session_id' => null]);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda berhasil keluar.');
    }

    /** Tentukan URL redirect berdasarkan role yang tersimpan di DB */
    private function redirectPath(): string
    {
        return match (Auth::user()?->role_name) {
            'laboran' => route('laboran.dashboard'),
            'asisten' => route('asisten.dashboard'),
            'dosen'   => route('pengawas.dashboard'),
            default   => route('login'),
        };
    }
}
