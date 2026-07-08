<?php
namespace App\Http\Controllers;
use App\Models\{Mahasiswa,MataKuliah,Ruangan,Dosen,Asisten,Praktikum,Presensi,NilaiAsistensi,NilaiUjian,NilaiEvaluasi,RekapDetailNilai,User,Role};
use Illuminate\Http\{Request,RedirectResponse};
use Illuminate\Support\Facades\{Auth,Hash,DB};
use Illuminate\View\View;

class LaboranController extends Controller
{
    // ── Ganti Password (Laboran sendiri) ───────────────────────────────────
    public function gantiPassword(): View {
        return view('laboran.ganti-password');
    }

    public function gantiPasswordUpdate(Request $request): RedirectResponse {
        $v = $request->validate([
            'password_lama' => ['required','current_password'],
            'password_baru' => ['required','min:6','confirmed'],
        ], [
            'password_lama.current_password' => 'Password lama tidak sesuai.',
            'password_baru.confirmed'        => 'Konfirmasi password tidak cocok.',
        ]);
        Auth::user()->update(['password' => Hash::make($v['password_baru'])]);
        return redirect()->route('laboran.dashboard')->with('success','Password berhasil diubah.');
    }
    public function dashboard(): View {
        // ── Stat counts ───────────────────────────────────────────────
        $totalMK         = MataKuliah::count();
        $totalMahasiswa  = Mahasiswa::count();
        $totalAsisten    = Asisten::count();
        $totalDosen      = Dosen::count();
        $totalKelas      = Praktikum::count();
        $totalRuangan    = Ruangan::count();

        // ── Kelengkapan kelas ─────────────────────────────────────────
        $kelasTotal          = $totalKelas;
        $kelasTanpaDosen     = Praktikum::whereNull('dosen_id')->count();
        $kelasTanpaAsisten   = Praktikum::whereNull('asisten_id')->count();
        $kelasTanpaAsisten2  = Praktikum::whereNull('asisten2_id')->count();
        $kelasTanpaRuangan   = Praktikum::whereNull('ruangan_id')->count();
        $kelasTanpaMahasiswa = Praktikum::doesntHave('mahasiswa')->count();

        // ── Presensi ──────────────────────────────────────────────────
        $totalPresensi  = \App\Models\Presensi::count();
        $totalAlpa      = \App\Models\Presensi::where('status_kehadiran','A')->count();
        $totalHadir     = \App\Models\Presensi::where('status_kehadiran','H')->count();
        $totalIzin      = \App\Models\Presensi::where('status_kehadiran','I')->count();
        $totalSakit     = \App\Models\Presensi::where('status_kehadiran','S')->count();
        $mahasiswaAlpa  = Mahasiswa::whereHas('presensi', function($q) {
            $q->where('status_kehadiran','A')
              ->groupBy('mahasiswa_id','praktikum_id')
              ->havingRaw('COUNT(*) >= ?', [Mahasiswa::BATAS_ALPA]);
        })->count();

        // ── Asisten tanpa kelas ───────────────────────────────────────
        $asistenTanpaKelas = Asisten::whereDoesntHave('praktikum')
            ->whereDoesntHave('praktikumSebagaiAsisten2')->count();

        // ── Kelas terbesar ────────────────────────────────────────────
        $kelasTerbesar = Praktikum::withCount('mahasiswa')
            ->with('mataKuliah')
            ->orderByDesc('mahasiswa_count')
            ->limit(5)->get();

        // ── Tabel mata kuliah ─────────────────────────────────────────
        $mataKuliah = MataKuliah::withCount(['praktikum','praktikum as mahasiswa_count' => function($q) {
            $q->join('mahasiswa_praktikum','praktikum.id','=','mahasiswa_praktikum.praktikum_id');
        }])->latest()->get();

        return view('laboran.dashboard', compact(
            'totalMK','totalMahasiswa','totalAsisten','totalDosen',
            'totalKelas','totalRuangan',
            'kelasTotal','kelasTanpaDosen','kelasTanpaAsisten','kelasTanpaAsisten2',
            'kelasTanpaRuangan','kelasTanpaMahasiswa',
            'totalPresensi','totalAlpa','totalHadir','totalIzin','totalSakit',
            'mahasiswaAlpa','asistenTanpaKelas',
            'kelasTerbesar','mataKuliah'
        ));
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
    public function mataKuliahUpdate(Request $request, MataKuliah $mataKuliah): RedirectResponse {
        $request->validate([
            'kode_mk' => ['required','max:20',"unique:mata_kuliah,kode_mk,{$mataKuliah->id}"],
            'nama_mk' => ['required','string'],
        ], [
            'kode_mk.unique' => 'Kode MK sudah dipakai mata kuliah lain.',
        ]);
        $mataKuliah->update($request->only('kode_mk','nama_mk'));
        return back()->with('success','Mata kuliah berhasil diperbarui.');
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
    public function ruanganUpdate(Request $request, Ruangan $ruangan): RedirectResponse {
        $request->validate([
            'nama_ruangan' => ['required','string',"unique:ruangan,nama_ruangan,{$ruangan->id}"],
        ], [
            'nama_ruangan.unique' => 'Nama ruangan sudah dipakai ruangan lain.',
        ]);
        $ruangan->update($request->only('nama_ruangan'));
        return back()->with('success','Ruangan berhasil diperbarui.');
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
        $hariValid    = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
        $mulaiValid   = ['07:00','09:40','10:30','13:00','14:30','15:40'];
        $selesaiValid = ['09:30','10:20','12:10','14:20','15:30','18:10','18:20'];

        $v = $request->validate([
            'mata_kuliah_id' => ['required','exists:mata_kuliah,id'],
            'nama_kelas'     => ['required','string','max:50'],
            'hari'           => ['nullable', 'in:' . implode(',', $hariValid)],
            'jam_mulai'      => ['nullable', 'in:' . implode(',', $mulaiValid)],
            'jam_selesai'    => ['nullable', 'in:' . implode(',', $selesaiValid)],
            'ruangan_id'     => ['nullable','exists:ruangan,id'],
            'dosen_id'       => ['nullable','exists:dosen,id'],
            'asisten_id'     => ['nullable','exists:asisten,id'],
            'asisten2_id'    => ['nullable','exists:asisten,id'],
        ]);

        if (!empty($v['hari']) && !empty($v['jam_mulai']) && !empty($v['jam_selesai'])) {
            $v['jadwal'] = $v['hari'] . ', ' . $v['jam_mulai'] . '–' . $v['jam_selesai'];
        } elseif (!empty($v['hari'])) {
            $v['jadwal'] = $v['hari'];
        }

        Praktikum::create($v);
        return back()->with('success','Kelas ditambahkan.');
    }
    public function kelasDestroy(Praktikum $praktikum): RedirectResponse {
        $praktikum->delete(); return back()->with('success','Kelas dihapus.');
    }

    /** Dashboard 1 kelas: kelola asisten1/2 + kelola praktikan di dalamnya */
    public function kelasShow(Praktikum $praktikum): View {
        $sudahDiSini       = $praktikum->mahasiswa()->pluck('mahasiswa.id');
        $mahasiswaBelumKelas = Mahasiswa::whereNotIn('id', $sudahDiSini)
                                ->orderBy('nama_mahasiswa')->get();
        return view('laboran.kelas.show', [
            'kelas'               => $praktikum->load(['mataKuliah','dosen','ruangan','asisten','asisten2']),
            'asistenAll'          => Asisten::orderBy('nama_asisten')->get(),
            'dosenAll'            => Dosen::orderBy('nama_dosen')->get(),
            'ruanganAll'          => Ruangan::orderBy('nama_ruangan')->get(),
            'mahasiswaDiKelas'    => $praktikum->mahasiswa()->orderBy('nama_mahasiswa')->get(),
            'mahasiswaBelumKelas' => $mahasiswaBelumKelas,
        ]);
    }
    /** Ganti/tambah/hilangkan Asisten 1 & 2 untuk kelas ini */
    public function kelasUpdate(Request $request, Praktikum $praktikum): RedirectResponse {
        $hariValid    = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
        $mulaiValid   = ['07:00','09:40','10:30','13:00','14:30','15:40'];
        $selesaiValid = ['09:30','10:20','12:10','14:20','15:30','18:10','18:20'];

        $v = $request->validate([
            'hari'        => ['required', 'in:' . implode(',', $hariValid)],
            'jam_mulai'   => ['required', 'in:' . implode(',', $mulaiValid)],
            'jam_selesai' => ['required', 'in:' . implode(',', $selesaiValid)],
            'ruangan_id'  => ['required','exists:ruangan,id'],
            'dosen_id'    => ['required','exists:dosen,id'],
            'asisten_id'  => ['required','exists:asisten,id'],
            'asisten2_id' => ['nullable','exists:asisten,id'],
        ], [
            'hari.required'        => 'Hari wajib dipilih.',
            'hari.in'              => 'Hari tidak valid.',
            'jam_mulai.required'   => 'Jam mulai wajib dipilih.',
            'jam_mulai.in'         => 'Jam mulai tidak valid.',
            'jam_selesai.required' => 'Jam selesai wajib dipilih.',
            'jam_selesai.in'       => 'Jam selesai tidak valid.',
            'ruangan_id.required'  => 'Ruangan wajib dipilih.',
            'dosen_id.required'    => 'Dosen wajib dipilih.',
            'asisten_id.required'  => 'Asisten 1 wajib dipilih.',
        ]);

        // Susun ulang kolom jadwal string untuk kompatibilitas tampilan lama
        if (!empty($v['hari']) && !empty($v['jam_mulai']) && !empty($v['jam_selesai'])) {
            $v['jadwal'] = $v['hari'] . ', ' . $v['jam_mulai'] . '–' . $v['jam_selesai'];
        } elseif (!empty($v['hari'])) {
            $v['jadwal'] = $v['hari'];
        } else {
            $v['jadwal'] = null;
        }

        $praktikum->update($v);
        return back()->with('success','Jadwal, ruangan, dosen & asisten kelas diperbarui.');
    }
    /** Masukkan mahasiswa yang belum punya kelas ke kelas ini */
    public function kelasTambahMahasiswa(Request $request, Praktikum $praktikum): RedirectResponse {
        $v = $request->validate(['mahasiswa_id' => ['required','exists:mahasiswa,id']]);

        $sudahAda = $praktikum->mahasiswa()->where('mahasiswa.id', $v['mahasiswa_id'])->exists();
        if ($sudahAda) {
            return back()->with('error', 'Mahasiswa sudah ada di kelas ini.');
        }

        $praktikum->mahasiswa()->attach($v['mahasiswa_id']);
        return back()->with('success', 'Mahasiswa ditambahkan ke kelas ini.');
    }
    /** Keluarkan mahasiswa dari kelas ini (mahasiswa TIDAK dihapus, cuma jadi tanpa kelas) */
    public function kelasHapusMahasiswa(Praktikum $praktikum, Mahasiswa $mahasiswa): RedirectResponse {
        $praktikum->mahasiswa()->detach($mahasiswa->id);
        return back()->with('success', 'Mahasiswa dikeluarkan dari kelas ini.');
    }
 
    // ── Asisten ────────────────────────────────────────────────────────────
    public function asisten(): View {
        return view('laboran.asisten.index', [
            'asistenAll' => Asisten::with([
                'user',
                'praktikum.mataKuliah',
                'praktikumSebagaiAsisten2.mataKuliah',
            ])->latest()->get(),
        ]);
    }
    public function asistenStore(Request $request): RedirectResponse {
        $v = $request->validate([
            'nama_asisten' => ['required'],
            'nim'          => ['required','regex:/^\d+$/','unique:asisten,nim'],
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
    public function asistenUpdate(Request $request, Asisten $asisten): RedirectResponse {
        $v = $request->validate([
            'nama_asisten' => ['required','string'],
            'nim'          => ['required','regex:/^\d+$/',"unique:asisten,nim,{$asisten->id}"],
        ], [
            'nim.regex'  => 'NIM hanya boleh berisi angka.',
            'nim.unique' => 'NIM sudah dipakai asisten lain.',
        ]);
        $asisten->update($v);
        return back()->with('success', "Data {$asisten->nama_asisten} berhasil diperbarui.");
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
            'nama_dosen' => ['required','string'],
            'nidn'       => ['nullable','regex:/^\d+$/','max:20','unique:dosen,nidn'],
            'username'   => ['required','unique:users,username'],
            'password'   => ['required','min:6'],
        ], [
            'nidn.regex' => 'NIDN hanya boleh berisi angka.',
        ]);
        DB::beginTransaction();
        try {
            $rid  = Role::where('role_name','dosen')->value('id');
            $user = User::create(['username'=>$v['username'],'password'=>Hash::make($v['password']),'role_id'=>$rid]);
            Dosen::create(['nama_dosen'=>$v['nama_dosen'],'nidn'=>$v['nidn']??null,'user_id'=>$user->id]);
            DB::commit(); return back()->with('success','Dosen ditambahkan.');
        } catch(\Exception $e) { DB::rollBack(); return back()->with('error',$e->getMessage()); }
    }
    public function dosenUpdate(Request $request, Dosen $dosen): RedirectResponse {
        $v = $request->validate([
            'nama_dosen' => ['required','string'],
            'nidn'       => ['nullable','regex:/^\d+$/','max:20',"unique:dosen,nidn,{$dosen->id}"],
        ], [
            'nidn.regex'  => 'NIDN hanya boleh berisi angka.',
            'nidn.unique' => 'NIDN sudah dipakai dosen lain.',
        ]);
        $dosen->update($v);
        return back()->with('success', "Data {$dosen->nama_dosen} berhasil diperbarui.");
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
    public function mahasiswa(Request $request): View {
        $q = $request->input('q', '');
        $mahasiswaAll = Mahasiswa::with('praktikum.mataKuliah')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nim_mahasiswa', 'like', "%{$q}%")
                        ->orWhere('nama_mahasiswa', 'like', "%{$q}%");
                });
            })
            ->orderBy('nama_mahasiswa')
            ->paginate(20)
            ->withQueryString();

        return view('laboran.mahasiswa.index', compact('mahasiswaAll', 'q'));
    }
    public function mahasiswaStore(Request $request): RedirectResponse {
        $v = $request->validate([
            'nim_mahasiswa'  => ['required','regex:/^\d+$/','unique:mahasiswa,nim_mahasiswa'],
            'nama_mahasiswa' => ['required','string'],
        ], [
            'nim_mahasiswa.regex' => 'NIM hanya boleh berisi angka.',
        ]);
        unset($v['_form']);
        Mahasiswa::create($v);
        return back()->with('success','Mahasiswa ditambahkan. Tentukan kelasnya lewat menu Kelas Praktikum → Edit.');
    }
    public function mahasiswaEdit(Mahasiswa $mahasiswa): View {
        return view('laboran.mahasiswa.edit', [
            'mahasiswa' => $mahasiswa,
        ]);
    }
    public function mahasiswaUpdate(Request $request, Mahasiswa $mahasiswa): RedirectResponse {
        $v = $request->validate([
            'nim_mahasiswa'  => ['required','regex:/^\d+$/',"unique:mahasiswa,nim_mahasiswa,{$mahasiswa->id}"],
            'nama_mahasiswa' => ['required','string'],
        ], [
            'nim_mahasiswa.regex' => 'NIM hanya boleh berisi angka.',
        ]);
        $mahasiswa->update($v);
        return redirect()->route('laboran.mahasiswa')->with('success', 'Data mahasiswa diperbarui.');
    }
    public function mahasiswaDestroy(Mahasiswa $mahasiswa): RedirectResponse {
        $mahasiswa->delete(); return back()->with('success','Mahasiswa dihapus.');
    }

    // ── Pengentrian Nilai & Absensi per Mahasiswa ──────────────────────────
    public function mahasiswaNilai(Mahasiswa $mahasiswa, Praktikum $praktikum): View {
        $pid = $praktikum->id;
        return view('laboran.mahasiswa.nilai', [
            'mahasiswa'      => $mahasiswa->load('praktikum.mataKuliah'),
            'praktikum'      => $praktikum,
            'presensiList'   => Presensi::where(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$pid])->orderBy('pertemuan_ke')->get(),
            'nilaiAsistensi' => NilaiAsistensi::firstOrCreate(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$pid]),
            'nilaiUjian'     => NilaiUjian::firstOrCreate(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$pid]),
            'nilaiEvaluasi'  => NilaiEvaluasi::firstOrCreate(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$pid]),
            'rekap'          => RekapDetailNilai::where(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$pid])->first(),
            'jumlahPertemuan'=> 14,
        ]);
    }
    public function mahasiswaNilaiUpdate(Request $request, Mahasiswa $mahasiswa, Praktikum $praktikum): RedirectResponse {
        $pid = $praktikum->id;
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