<?php
namespace App\Http\Controllers;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\Support\Facades\{Auth, Hash};
use Illuminate\View\View;

class DosenController extends Controller
{
    /** Form ganti password sendiri */
    public function gantiPassword(): View
    {
        return view('dosen.ganti-password');
    }

    public function gantiPasswordUpdate(Request $request): RedirectResponse
    {
        $v = $request->validate([
            'password_lama' => ['required', 'current_password'],
            'password_baru' => ['required', 'min:6', 'confirmed'],
        ], [
            'password_lama.current_password' => 'Password lama tidak sesuai.',
            'password_baru.confirmed'        => 'Konfirmasi password tidak cocok.',
        ]);

        Auth::user()->update(['password' => Hash::make($v['password_baru'])]);

        return redirect()->route('pengawas.dashboard')->with('success', 'Password berhasil diubah.');
    }
}