<?php
namespace App\Http\Controllers;
use App\Models\{Praktikum,Mahasiswa,Presensi,NilaiAsistensi,NilaiUjian,NilaiEvaluasi,RekapDetailNilai};
use Illuminate\Http\{Request,RedirectResponse};
use Illuminate\Support\Facades\{Auth,Hash};
use Illuminate\View\View;

class AsistenController extends Controller
{
    private function getAsisten() { return Auth::user()->asisten; }

    /**
     * Cek apakah asisten yang login berwenang mengakses kelas ini
     * (baik sebagai Asisten 1 maupun Asisten 2).
     */
    private function isAuthorizedForKelas(Praktikum $praktikum): bool
    {
        $asisten = $this->getAsisten();
        if (!$asisten) return false;
        return $praktikum->asisten_id  === $asisten->id
            || $praktikum->asisten2_id === $asisten->id;
    }

    /** Dashboard: daftar SEMUA kelas yang diampu asisten ini (Asisten 1 ATAU Asisten 2) */
    public function dashboard(): View
    {
        $asisten   = $this->getAsisten();
        // Gunakan semuaPraktikum() agar menggabungkan kelas sebagai Asisten 1 dan 2
        $kelasList = $asisten ? $asisten->semuaPraktikum() : collect();
        return view('asisten.dashboard', compact('asisten','kelasList'));
    }

    /** Presensi per kelas (Praktikum) */
    public function presensi(Request $request, Praktikum $praktikum): View
    {
        abort_unless($this->isAuthorizedForKelas($praktikum), 403, 'Anda tidak berwenang mengakses kelas ini.');

        $pertemuan    = max(1, min($request->integer('pertemuan', 1), 14));
        $mahasiswaList = $praktikum->mahasiswa()->orderBy('nama_mahasiswa')->get();
        $presensiMap  = Presensi::where('praktikum_id', $praktikum->id)
            ->where('pertemuan_ke', $pertemuan)->get()->keyBy('mahasiswa_id');
        $stats = [
            'total' => $mahasiswaList->count(),
            'hadir' => $presensiMap->where('status_kehadiran','H')->count(),
            'alpa'  => $presensiMap->where('status_kehadiran','A')->count(),
        ];
        return view('asisten.presensi', compact('praktikum','mahasiswaList','presensiMap','pertemuan','stats'));
    }

    public function presensiSimpan(Request $request, Praktikum $praktikum): RedirectResponse
    {
        abort_unless($this->isAuthorizedForKelas($praktikum), 403, 'Anda tidak berwenang mengakses kelas ini.');

        $pertemuan = max(1, min($request->integer('pertemuan', 1), 14));
        $request->validate(['presensi'=>'array','presensi.*.status_kehadiran'=>'nullable|in:H,I,S,A']);
        foreach ($request->input('presensi',[]) as $mahasiswaId => $data) {
            // Lewati mahasiswa yang status kehadirannya belum dipilih (tidak dipaksa default 'Hadir')
            if (empty($data['status_kehadiran'])) continue;
            Presensi::updateOrCreate(
                ['mahasiswa_id'=>$mahasiswaId,'praktikum_id'=>$praktikum->id,'pertemuan_ke'=>$pertemuan],
                ['status_kehadiran'=>$data['status_kehadiran'],'catatan'=>$data['catatan']??null]
            );
        }
        return back()->with('success',"Presensi pertemuan {$pertemuan} disimpan.");
    }

    /** Nilai per kelas (Praktikum) */
    public function nilai(Praktikum $praktikum): View
    {
        abort_unless($this->isAuthorizedForKelas($praktikum), 403, 'Anda tidak berwenang mengakses kelas ini.');

        $mahasiswaList = $praktikum->mahasiswa()->orderBy('nama_mahasiswa')->get();
        $nilaiMap = [];
        foreach ($mahasiswaList as $m) {
            $nilaiMap[$m->id] = [
                'evaluasi'  => NilaiEvaluasi::firstOrCreate(['mahasiswa_id'=>$m->id,'praktikum_id'=>$praktikum->id]),
                'asistensi' => NilaiAsistensi::firstOrCreate(['mahasiswa_id'=>$m->id,'praktikum_id'=>$praktikum->id]),
                'ujian'     => NilaiUjian::firstOrCreate(['mahasiswa_id'=>$m->id,'praktikum_id'=>$praktikum->id]),
                'rekap'     => RekapDetailNilai::where(['mahasiswa_id'=>$m->id,'praktikum_id'=>$praktikum->id])->first(),
            ];
        }
        return view('asisten.nilai', compact('praktikum','mahasiswaList','nilaiMap'));
    }

    public function nilaiSimpan(Request $request, Praktikum $praktikum, Mahasiswa $mahasiswa): RedirectResponse
    {
        abort_unless($this->isAuthorizedForKelas($praktikum), 403, 'Anda tidak berwenang mengakses kelas ini.');

        $v = $request->validate([
            'nilai_evaluasi1'=>['nullable','numeric','min:0','max:100'],
            'nilai_evaluasi2'=>['nullable','numeric','min:0','max:100'],
            'nilai_evaluasi3'=>['nullable','numeric','min:0','max:100'],
            'nilai_evaluasi4'=>['nullable','numeric','min:0','max:100'],
            'nilai_asistensi1'=>['nullable','numeric','min:0','max:100'],
            'nilai_asistensi2'=>['nullable','numeric','min:0','max:100'],
            'nilai_asistensi3'=>['nullable','numeric','min:0','max:100'],
            'nilai_MID'=>['nullable','numeric','min:0','max:100'],
            'nilai_UAS'=>['nullable','numeric','min:0','max:100'],
        ]);
        NilaiEvaluasi::updateOrCreate(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$praktikum->id],
            array_filter(array_intersect_key($v, array_flip(['nilai_evaluasi1','nilai_evaluasi2','nilai_evaluasi3','nilai_evaluasi4'])),fn($v)=>$v!==null));
        NilaiAsistensi::updateOrCreate(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$praktikum->id],
            array_filter(array_intersect_key($v, array_flip(['nilai_asistensi1','nilai_asistensi2','nilai_asistensi3'])),fn($v)=>$v!==null));
        NilaiUjian::updateOrCreate(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$praktikum->id],
            array_filter(array_intersect_key($v, array_flip(['nilai_MID','nilai_UAS'])),fn($v)=>$v!==null));
        RekapDetailNilai::hitungDanSimpan($mahasiswa->id, $praktikum->id);
        return back()->with('success','Nilai disimpan.');
    }

    /** Rekap presensi per kelas */
    public function rekap(Praktikum $praktikum): View
    {
        abort_unless($this->isAuthorizedForKelas($praktikum), 403, 'Anda tidak berwenang mengakses kelas ini.');

        $mahasiswaList = $praktikum->mahasiswa()->orderBy('nama_mahasiswa')->get();
        $presensiAll   = Presensi::where('praktikum_id', $praktikum->id)->get()
            ->groupBy('mahasiswa_id')->map(fn($r) => $r->keyBy('pertemuan_ke'));
        return view('asisten.rekap', compact('praktikum','mahasiswaList','presensiAll'));
    }

    /** Form ganti password sendiri */
    public function gantiPassword(): View
    {
        return view('asisten.ganti-password');
    }

    public function gantiPasswordUpdate(Request $request): RedirectResponse
    {
        $v = $request->validate([
            'password_lama' => ['required','current_password'],
            'password_baru' => ['required','min:6','confirmed'],
        ], [
            'password_lama.current_password' => 'Password lama tidak sesuai.',
            'password_baru.confirmed'        => 'Konfirmasi password tidak cocok.',
        ]);
        Auth::user()->update(['password' => Hash::make($v['password_baru'])]);
        return redirect()->route('asisten.dashboard')->with('success','Password berhasil diubah.');
    }
}