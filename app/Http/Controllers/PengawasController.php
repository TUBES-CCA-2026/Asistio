<?php
namespace App\Http\Controllers;
use App\Models\{Praktikum,Presensi};
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PengawasController extends Controller
{
    public function dashboard(): View {
        $user  = Auth::user();
        $dosen = $user->dosen;
        $kelasList = $dosen
            ? Praktikum::where('dosen_id', $dosen->id)->with(['mataKuliah','asisten'])->withCount('mahasiswa')->get()
            : collect();
        return view('pengawas.dashboard', compact('user','dosen','kelasList'));
    }

    /** Rekap data per kelas praktikum */
    public function rekap(Praktikum $praktikum): View {
        $mahasiswaList = $praktikum->mahasiswa()->with(['rekap','presensi'])->orderBy('nama_mahasiswa')->get();
        $presensiAll   = Presensi::where('praktikum_id', $praktikum->id)->get()
            ->groupBy('mahasiswa_id')->map(fn($r) => $r->keyBy('pertemuan_ke'));
        return view('pengawas.rekap', compact('praktikum','mahasiswaList','presensiAll'));
    }
}
