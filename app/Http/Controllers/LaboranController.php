<?php
namespace App\Http\Controllers;
use App\Models\{Mahasiswa,MataKuliah,Ruangan,Dosen,Asisten,Praktikum,Presensi,NilaiAsistensi,NilaiUjian,NilaiEvaluasi,RekapDetailNilai,User,Role};
use Illuminate\Http\{Request,RedirectResponse};
use Illuminate\Support\Facades\{Hash,DB};
use Illuminate\View\View;

class LaboranController extends Controller
{
    public function dashboard(): View {
        return view('laboran.dashboard', [
            'totalMK'        => MataKuliah::count(),
            'totalMahasiswa' => Mahasiswa::count(),
            'totalAsisten'   => Asisten::count(),
            'totalDosen'     => Dosen::count(),
            'mataKuliah'     => MataKuliah::withCount(['praktikum', 'mahasiswa'])->latest()->get(),
        ]);
    }

    // ── Mata Kuliah ────────────────────────────────────────────────────────
    public function mataKuliah(): View {
        return view('laboran.mata-kuliah.index', [
            'mataKuliahAll' => MataKuliah::withCount('praktikum')->latest()->get()
        ]);
    }
    public function mataKuliahStore(Request $request): RedirectResponse {
        $request->validate(['kode_mk'=>['required','unique:mata_kuliah,kode_mk'],'nama_mk'=>['required']]);
        MataKuliah::create($request->only('kode_mk','nama_mk'));
        return back()->with('success','Mata kuliah ditambahkan.');
    }
    public function mataKuliahDestroy(MataKuliah $mataKuliah): RedirectResponse {
        $mataKuliah->delete(); return back()->with('success','Mata kuliah dihapus.');
    }

    // ── Ruangan ────────────────────────────────────────────────────────────
    public function ruangan(): View {
        return view('laboran.ruangan.index', ['ruanganAll' => Ruangan::latest()->get()]);
    }
    public function ruanganStore(Request $request): RedirectResponse {
        $request->validate(['nama_ruangan'=>['required','unique:ruangan,nama_ruangan']]);
        Ruangan::create($request->only('nama_ruangan'));
        return back()->with('success','Ruangan ditambahkan.');
    }
    public function ruanganDestroy(Ruangan $ruangan): RedirectResponse {
        $ruangan->delete(); return back()->with('success','Ruangan dihapus.');
    }

    // ── Kelas / Praktikum ──────────────────────────────────────────────────
    public function kelas(): View {
        return view('laboran.kelas.index', [
            'kelasAll'   => Praktikum::with(['mataKuliah','dosen','asisten','ruangan'])->withCount('mahasiswa')->latest()->get(),
            'mataKuliah' => MataKuliah::orderBy('nama_mk')->get(),
            'dosenAll'   => Dosen::orderBy('nama_dosen')->get(),
            'asistenAll' => Asisten::orderBy('nama_asisten')->get(),
            'ruanganAll' => Ruangan::orderBy('nama_ruangan')->get(),
        ]);
    }
    public function kelasStore(Request $request): RedirectResponse {
        $v = $request->validate([
            'mata_kuliah_id' => ['required','exists:mata_kuliah,id'],
            'nama_kelas'     => ['required','string','max:50'],
            'jadwal'         => ['nullable','string','max:100'],
            'ruangan_id'     => ['nullable','exists:ruangan,id'],
            'dosen_id'       => ['nullable','exists:dosen,id'],
            'asisten_id'     => ['nullable','exists:asisten,id'],
            'asisten2_id'    => ['nullable','exists:asisten,id'],
        ]);
        Praktikum::create($v);
        return back()->with('success','Kelas ditambahkan.');
    }
    public function kelasDestroy(Praktikum $praktikum): RedirectResponse {
        $praktikum->delete(); return back()->with('success','Kelas dihapus.');
    }

    /** Dashboard 1 kelas: kelola asisten1/2 + kelola praktikan di dalamnya */
    public function kelasShow(Praktikum $praktikum): View {
        return view('laboran.kelas.show', [
            'kelas'               => $praktikum->load(['mataKuliah','dosen','ruangan','asisten','asisten2']),
            'asistenAll'          => Asisten::orderBy('nama_asisten')->get(),
            'mahasiswaDiKelas'    => Mahasiswa::where('praktikum_id', $praktikum->id)->orderBy('nama_mahasiswa')->get(),
            'mahasiswaBelumKelas' => Mahasiswa::whereNull('praktikum_id')->orderBy('nama_mahasiswa')->get(),
        ]);
    }
    /** Ganti/tambah/hilangkan Asisten 1 & 2 untuk kelas ini */
    public function kelasUpdate(Request $request, Praktikum $praktikum): RedirectResponse {
        $v = $request->validate([
            'asisten_id'  => ['nullable','exists:asisten,id'],
            'asisten2_id' => ['nullable','exists:asisten,id'],
        ]);
        $praktikum->update($v);
        return back()->with('success','Asisten kelas diperbarui.');
    }
    /** Masukkan mahasiswa yang belum punya kelas ke kelas ini */
    public function kelasTambahMahasiswa(Request $request, Praktikum $praktikum): RedirectResponse {
        $v = $request->validate(['mahasiswa_id' => ['required','exists:mahasiswa,id']]);
        Mahasiswa::where('id', $v['mahasiswa_id'])->update(['praktikum_id' => $praktikum->id]);
        return back()->with('success','Mahasiswa ditambahkan ke kelas ini.');
    }
    /** Keluarkan mahasiswa dari kelas ini (mahasiswa TIDAK dihapus, cuma jadi tanpa kelas) */
    public function kelasHapusMahasiswa(Praktikum $praktikum, Mahasiswa $mahasiswa): RedirectResponse {
        if ($mahasiswa->praktikum_id === $praktikum->id) {
            $mahasiswa->update(['praktikum_id' => null]);
        }
        return back()->with('success','Mahasiswa dikeluarkan dari kelas ini.');
    }
 
    // ── Asisten ────────────────────────────────────────────────────────────
    public function asisten(): View {
        return view('laboran.asisten.index', ['asistenAll' => Asisten::with('user')->latest()->get()]);
    }
    public function asistenStore(Request $request): RedirectResponse {
        $v = $request->validate([
            'nama_asisten' => ['required'],
            'nim'          => ['required','unique:asisten,nim'],
            'username'     => ['required','unique:users,username'],
            'password'     => ['required','min:6'],
        ]);
        DB::beginTransaction();
        try {
            $rid  = Role::where('role_name','asisten')->value('id');
            $user = User::create(['username'=>$v['username'],'password'=>Hash::make($v['password']),'role_id'=>$rid]);
            Asisten::create(['nama_asisten'=>$v['nama_asisten'],'nim'=>$v['nim'],'user_id'=>$user->id]);
            DB::commit(); return back()->with('success','Asisten ditambahkan.');
        } catch(\Exception $e) { DB::rollBack(); return back()->with('error',$e->getMessage()); }
    }
    public function asistenDestroy(Asisten $asisten): RedirectResponse {
        $asisten->user?->delete(); $asisten->delete();
        return back()->with('success','Asisten dihapus.');
    }
    public function asistenResetPassword(Request $request, Asisten $asisten): RedirectResponse {
        $v = $request->validate([
            'password' => ['required','min:6','confirmed'],
        ]);
        // current_session_id ikut di-null-kan → kalau ada sesi lama yang masih aktif, otomatis ter-logout
        $asisten->user?->update([
            'password'           => Hash::make($v['password']),
            'current_session_id' => null,
        ]);
        return back()->with('success', "Password {$asisten->nama_asisten} berhasil direset.");
    }

    // ── Dosen ──────────────────────────────────────────────────────────────
    public function dosen(): View {
        return view('laboran.dosen.index', [
            'dosenAll' => Dosen::with(['user','praktikum.mataKuliah'])->latest()->get(),
            'mataKuliah' => MataKuliah::orderBy('nama_mk')->get(),
        ]);
    }
    public function dosenStore(Request $request): RedirectResponse {
        $v = $request->validate([
            'nama_dosen' => ['required'],
            'nidn'       => ['nullable','unique:dosen,nidn'],
            'username'   => ['required','unique:users,username'],
            'password'   => ['required','min:6'],
        ]);
        DB::beginTransaction();
        try {
            $rid  = Role::where('role_name','dosen')->value('id');
            $user = User::create(['username'=>$v['username'],'password'=>Hash::make($v['password']),'role_id'=>$rid]);
            Dosen::create(['nama_dosen'=>$v['nama_dosen'],'nidn'=>$v['nidn']??null,'user_id'=>$user->id]);
            DB::commit(); return back()->with('success','Dosen ditambahkan.');
        } catch(\Exception $e) { DB::rollBack(); return back()->with('error',$e->getMessage()); }
    }
    public function dosenDestroy(Dosen $dosen): RedirectResponse {
        $dosen->user?->delete(); $dosen->delete();
        return back()->with('success','Dosen dihapus.');
    }
    public function dosenResetPassword(Request $request, Dosen $dosen): RedirectResponse {
        $v = $request->validate(['password' => ['required','min:6','confirmed']]);
        $dosen->user?->update([
            'password'           => Hash::make($v['password']),
            'current_session_id' => null,
        ]);
        return back()->with('success', "Password {$dosen->nama_dosen} berhasil direset.");
    }

    // ── Mahasiswa — kini memilih praktikum bukan mata kuliah ───────────────
    public function mahasiswa(): View {
        return view('laboran.mahasiswa.index', [
            'mahasiswaAll' => Mahasiswa::with('praktikum.mataKuliah')->latest()->paginate(20),
            'praktikumAll' => Praktikum::with(['mataKuliah','asisten'])->orderBy('id')->get(),
        ]);
    }
    public function mahasiswaStore(Request $request): RedirectResponse {
        $v = $request->validate([
            'nim_mahasiswa'  => ['required','unique:mahasiswa,nim_mahasiswa'],
            'nama_mahasiswa' => ['required','string'],
        ]);
        Mahasiswa::create($v);
        return back()->with('success','Mahasiswa ditambahkan. Tentukan kelasnya lewat menu Kelas Praktikum → Edit.');
    }
    public function mahasiswaEdit(Mahasiswa $mahasiswa): View {
        return view('laboran.mahasiswa.edit', [
            'mahasiswa'    => $mahasiswa,
            'praktikumAll' => Praktikum::with(['mataKuliah','asisten'])->get(),
        ]);
    }
    public function mahasiswaUpdate(Request $request, Mahasiswa $mahasiswa): RedirectResponse {
        $v = $request->validate([
            'nim_mahasiswa'  => ['required',"unique:mahasiswa,nim_mahasiswa,{$mahasiswa->id}"],
            'nama_mahasiswa' => ['required','string'],
            'praktikum_id'   => ['nullable','exists:praktikum,id'],
        ]);
        $mahasiswa->update($v);
        return redirect()->route('laboran.mahasiswa')->with('success','Data mahasiswa diperbarui.');
    }
    public function mahasiswaDestroy(Mahasiswa $mahasiswa): RedirectResponse {
        $mahasiswa->delete(); return back()->with('success','Mahasiswa dihapus.');
    }

    // ── Pengentrian Nilai & Absensi per Mahasiswa ──────────────────────────
    public function mahasiswaNilai(Mahasiswa $mahasiswa): View {
        $pid = $mahasiswa->praktikum_id;
        return view('laboran.mahasiswa.nilai', [
            'mahasiswa'      => $mahasiswa->load('praktikum.mataKuliah'),
            'presensiList'   => Presensi::where(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$pid])->orderBy('pertemuan_ke')->get(),
            'nilaiAsistensi' => NilaiAsistensi::firstOrCreate(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$pid]),
            'nilaiUjian'     => NilaiUjian::firstOrCreate(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$pid]),
            'nilaiEvaluasi'  => NilaiEvaluasi::firstOrCreate(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$pid]),
            'rekap'          => RekapDetailNilai::where(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$pid])->first(),
            'jumlahPertemuan'=> 14,
        ]);
    }
    public function mahasiswaNilaiUpdate(Request $request, Mahasiswa $mahasiswa): RedirectResponse {
        $pid = $mahasiswa->praktikum_id;
        $v   = $request->validate([
            'p1'=>['nullable','numeric','min:0','max:100'],
            'p2'=>['nullable','numeric','min:0','max:100'],
            'p3'=>['nullable','numeric','min:0','max:100'],
            'p4'=>['nullable','numeric','min:0','max:100'],
            'p5'=>['nullable','numeric','min:0','max:100'],
            'p6'=>['nullable','numeric','min:0','max:100'],
            'p7'=>['nullable','numeric','min:0','max:100'],
            'p8'=>['nullable','numeric','min:0','max:100'],
            'p9'=>['nullable','numeric','min:0','max:100'],
            'p10'=>['nullable','numeric','min:0','max:100'],
            'p11'=>['nullable','numeric','min:0','max:100'],
            'p12'=>['nullable','numeric','min:0','max:100'],
            'p13'=>['nullable','numeric','min:0','max:100'],
            'p14'=>['nullable','numeric','min:0','max:100'],
            'nilai_asistensi1'=>['nullable','numeric','min:0','max:100'],
            'nilai_asistensi2'=>['nullable','numeric','min:0','max:100'],
            'nilai_asistensi3'=>['nullable','numeric','min:0','max:100'],
            'nilai_MID'=>['nullable','numeric','min:0','max:100'],
            'nilai_UAS'=>['nullable','numeric','min:0','max:100'],
            'presensi'=>['nullable','array'],
            'presensi.*.status_kehadiran'=>['in:H,I,S,A'],
        ]);
        NilaiEvaluasi::updateOrCreate(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$pid],
            array_intersect_key($v, array_flip(['p1','p2','p3','p4','p5','p6','p7','p8','p9','p10','p11','p12','p13','p14'])));
        NilaiAsistensi::updateOrCreate(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$pid],
            array_intersect_key($v, array_flip(['nilai_asistensi1','nilai_asistensi2','nilai_asistensi3'])));
        NilaiUjian::updateOrCreate(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$pid],
            array_intersect_key($v, array_flip(['nilai_MID','nilai_UAS'])));
        foreach ($request->input('presensi',[]) as $pId => $pData) {
            Presensi::where('id',$pId)->update(['status_kehadiran'=>$pData['status_kehadiran']]);
        }
        RekapDetailNilai::hitungDanSimpan($mahasiswa->id, $pid);
        return back()->with('success','Nilai dan presensi berhasil disimpan.');
    }
}