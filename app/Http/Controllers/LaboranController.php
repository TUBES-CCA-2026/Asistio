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
        $totalHadir     = \App\Models\Presensi::where('status_kehadiran','H')->count();
        $totalIzin      = \App\Models\Presensi::where('status_kehadiran','I')->count();
        $totalSakit     = \App\Models\Presensi::where('status_kehadiran','S')->count();
        $totalAlpa      = \App\Models\Presensi::whereIn('status_kehadiran',['A','I','S'])->count();
        $mahasiswaAlpa  = Mahasiswa::whereHas('presensi', function($q) {
            $q->whereIn('status_kehadiran',['A','I','S'])
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
    public function kelas(Request $request): View {
        $q    = $request->input('q', '');
        $sort = in_array($request->input('sort'), ['nama_kelas','jadwal']) ? $request->input('sort') : null;
        $dir  = $request->input('dir') === 'desc' ? 'desc' : 'asc';

        $kelasAll = Praktikum::with(['mataKuliah','dosen','asisten','asisten2','ruangan'])
            ->withCount('mahasiswa')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama_kelas', 'like', "%{$q}%")
                        ->orWhere('hari', 'like', "%{$q}%")
                        ->orWhere('jam_mulai',  'like', "%{$q}%")
                        ->orWhere('jam_selesai','like', "%{$q}%")
                        ->orWhereHas('mataKuliah', fn($m) => $m->where('nama_mk', 'like', "%{$q}%"))
                        ->orWhereHas('dosen',      fn($d) => $d->where('nama_dosen', 'like', "%{$q}%"))
                        ->orWhereHas('ruangan',    fn($r) => $r->where('nama_ruangan', 'like', "%{$q}%"))
                        ->orWhereHas('asisten',    fn($a) => $a->where('nama_asisten', 'like', "%{$q}%"))
                        ->orWhereHas('asisten2',   fn($a) => $a->where('nama_asisten', 'like', "%{$q}%"));
                });
            })
            ->orderBy($sort ?? 'id', $sort ? $dir : 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('laboran.kelas.index', [
            'kelasAll'   => $kelasAll,
            'q'          => $q,
            'sort'       => $sort,
            'dir'        => $dir,
            'mataKuliah' => MataKuliah::orderBy('nama_mk')->get(),
            'dosenAll'   => Dosen::orderBy('nama_dosen')->get(),
            'asistenAll' => Asisten::orderBy('nama_asisten')->get(),
            'ruanganAll' => Ruangan::orderBy('nama_ruangan')->get(),
        ]);
    }
    public function kelasStore(Request $request): RedirectResponse {
        $hariValid    = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        $mulaiValid   = ['07:00','08:00','09:00','09:40','10:00','10:30','11:00','13:00','14:00','14:30','15:00','15:40','16:00'];
        $selesaiValid = ['08:40','09:30','10:20','11:20','12:00','12:10','14:20','15:00','15:20','15:30','16:20','17:00','18:10','18:20'];

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

        // ── Cek tabrakan jadwal ───────────────────────────────────────
        if (!empty($v['hari']) && !empty($v['jam_mulai']) && !empty($v['jam_selesai'])) {
            $mulai   = $v['jam_mulai'];
            $selesai = $v['jam_selesai'];

            // Query dasar: hari sama + rentang jam overlap (bukan hanya jam persis sama)
            // Overlap: mulai_baru < selesai_lama AND selesai_baru > mulai_lama
            $baseQuery = fn($q, $excludeId = null) => $q
                ->where('hari', $v['hari'])
                ->where('jam_mulai',   '<', $selesai)
                ->where('jam_selesai', '>', $mulai)
                ->when($excludeId, fn($q2) => $q2->where('id','!=',$excludeId))
                ->with('mataKuliah');

            // 1. Tabrakan ruangan
            if (!empty($v['ruangan_id'])) {
                $tabRuangan = $baseQuery(
                    Praktikum::where('ruangan_id', $v['ruangan_id'])
                )->first();
                if ($tabRuangan) {
                    return back()->withInput()->with('error_tabrakan',
                        'Ruangan sudah digunakan kelas <strong>' .
                        e($tabRuangan->mataKuliah?->nama_mk) . ' ' . e($tabRuangan->nama_kelas) .
                        '</strong> pada ' . e($tabRuangan->hari) . ', ' .
                        e($tabRuangan->jam_mulai) . '&ndash;' . e($tabRuangan->jam_selesai) . '.'
                    );
                }
            }

            // 2. Tabrakan dosen
            if (!empty($v['dosen_id'])) {
                $tabDosen = $baseQuery(
                    Praktikum::where('dosen_id', $v['dosen_id'])
                )->first();
                if ($tabDosen) {
                    return back()->withInput()->with('error_tabrakan',
                        'Dosen sudah mengajar di kelas <strong>' .
                        e($tabDosen->mataKuliah?->nama_mk) . ' ' . e($tabDosen->nama_kelas) .
                        '</strong> pada ' . e($tabDosen->hari) . ', ' .
                        e($tabDosen->jam_mulai) . '&ndash;' . e($tabDosen->jam_selesai) . '.'
                    );
                }
            }

            // 3. Tabrakan nama kelas (misal A1 tidak boleh ada 2 kelas A1 di jam yang sama)
            if (!empty($v['nama_kelas'])) {
                $tabKelas = $baseQuery(
                    Praktikum::where('nama_kelas', $v['nama_kelas'])
                )->first();
                if ($tabKelas) {
                    return back()->withInput()->with('error_tabrakan',
                        'Kelas <strong>' . e($v['nama_kelas']) . '</strong> sudah terjadwal ' .
                        'di mata kuliah <strong>' . e($tabKelas->mataKuliah?->nama_mk) .
                        '</strong> pada ' . e($tabKelas->hari) . ', ' .
                        e($tabKelas->jam_mulai) . '&ndash;' . e($tabKelas->jam_selesai) . '.'
                    );
                }
            }

            // 4. Tabrakan asisten 1
            if (!empty($v['asisten_id'])) {
                $tabA1 = $baseQuery(
                    Praktikum::where(function($q) use ($v) {
                        $q->where('asisten_id',  $v['asisten_id'])
                          ->orWhere('asisten2_id', $v['asisten_id']);
                    })
                )->first();
                if ($tabA1) {
                    return back()->withInput()->with('error_tabrakan',
                        'Asisten 1 sudah mendampingi kelas <strong>' .
                        e($tabA1->mataKuliah?->nama_mk) . ' ' . e($tabA1->nama_kelas) .
                        '</strong> pada ' . e($tabA1->hari) . ', ' .
                        e($tabA1->jam_mulai) . '&ndash;' . e($tabA1->jam_selesai) . '.'
                    );
                }
            }

            // 5. Tabrakan asisten 2
            if (!empty($v['asisten2_id'])) {
                $tabA2 = $baseQuery(
                    Praktikum::where(function($q) use ($v) {
                        $q->where('asisten_id',  $v['asisten2_id'])
                          ->orWhere('asisten2_id', $v['asisten2_id']);
                    })
                )->first();
                if ($tabA2) {
                    return back()->withInput()->with('error_tabrakan',
                        'Asisten 2 sudah mendampingi kelas <strong>' .
                        e($tabA2->mataKuliah?->nama_mk) . ' ' . e($tabA2->nama_kelas) .
                        '</strong> pada ' . e($tabA2->hari) . ', ' .
                        e($tabA2->jam_mulai) . '&ndash;' . e($tabA2->jam_selesai) . '.'
                    );
                }
            }
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
        $hariValid    = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        $mulaiValid   = ['07:00','08:00','09:00','09:40','10:00','10:30','11:00','13:00','14:00','14:30','15:00','15:40','16:00'];
        $selesaiValid = ['08:40','09:30','10:20','11:20','12:00','12:10','14:20','15:00','15:20','15:30','16:20','17:00','18:10','18:20'];

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

        // ── Cek tabrakan jadwal (kecuali kelas ini sendiri) ──────────
        if (!empty($v['hari']) && !empty($v['jam_mulai']) && !empty($v['jam_selesai'])) {
            $mulai   = $v['jam_mulai'];
            $selesai = $v['jam_selesai'];
            $selfId  = $praktikum->id;

            $baseQuery = fn($q) => $q
                ->where('hari', $v['hari'])
                ->where('jam_mulai',   '<', $selesai)
                ->where('jam_selesai', '>', $mulai)
                ->where('id', '!=', $selfId)
                ->with('mataKuliah');

            // 1. Tabrakan ruangan
            if (!empty($v['ruangan_id'])) {
                $tabRuangan = $baseQuery(
                    Praktikum::where('ruangan_id', $v['ruangan_id'])
                )->first();
                if ($tabRuangan) {
                    return back()->withInput()->with('error_tabrakan',
                        'Ruangan sudah digunakan kelas <strong>' .
                        e($tabRuangan->mataKuliah?->nama_mk) . ' ' . e($tabRuangan->nama_kelas) .
                        '</strong> pada ' . e($tabRuangan->hari) . ', ' .
                        e($tabRuangan->jam_mulai) . '&ndash;' . e($tabRuangan->jam_selesai) . '.'
                    );
                }
            }

            // 2. Tabrakan dosen
            if (!empty($v['dosen_id'])) {
                $tabDosen = $baseQuery(
                    Praktikum::where('dosen_id', $v['dosen_id'])
                )->first();
                if ($tabDosen) {
                    return back()->withInput()->with('error_tabrakan',
                        'Dosen sudah mengajar di kelas <strong>' .
                        e($tabDosen->mataKuliah?->nama_mk) . ' ' . e($tabDosen->nama_kelas) .
                        '</strong> pada ' . e($tabDosen->hari) . ', ' .
                        e($tabDosen->jam_mulai) . '&ndash;' . e($tabDosen->jam_selesai) . '.'
                    );
                }
            }

            // 3. Tabrakan nama kelas
            if (!empty($v['nama_kelas'])) {
                $tabKelas = $baseQuery(
                    Praktikum::where('nama_kelas', $v['nama_kelas'])
                )->first();
                if ($tabKelas) {
                    return back()->withInput()->with('error_tabrakan',
                        'Kelas <strong>' . e($v['nama_kelas']) . '</strong> sudah terjadwal ' .
                        'di mata kuliah <strong>' . e($tabKelas->mataKuliah?->nama_mk) .
                        '</strong> pada ' . e($tabKelas->hari) . ', ' .
                        e($tabKelas->jam_mulai) . '&ndash;' . e($tabKelas->jam_selesai) . '.'
                    );
                }
            }

            // 4. Tabrakan asisten 1
            if (!empty($v['asisten_id'])) {
                $tabA1 = $baseQuery(
                    Praktikum::where(function($q) use ($v) {
                        $q->where('asisten_id',  $v['asisten_id'])
                          ->orWhere('asisten2_id', $v['asisten_id']);
                    })
                )->first();
                if ($tabA1) {
                    return back()->withInput()->with('error_tabrakan',
                        'Asisten 1 sudah mendampingi kelas <strong>' .
                        e($tabA1->mataKuliah?->nama_mk) . ' ' . e($tabA1->nama_kelas) .
                        '</strong> pada ' . e($tabA1->hari) . ', ' .
                        e($tabA1->jam_mulai) . '&ndash;' . e($tabA1->jam_selesai) . '.'
                    );
                }
            }

            // 5. Tabrakan asisten 2
            if (!empty($v['asisten2_id'])) {
                $tabA2 = $baseQuery(
                    Praktikum::where(function($q) use ($v) {
                        $q->where('asisten_id',  $v['asisten2_id'])
                          ->orWhere('asisten2_id', $v['asisten2_id']);
                    })
                )->first();
                if ($tabA2) {
                    return back()->withInput()->with('error_tabrakan',
                        'Asisten 2 sudah mendampingi kelas <strong>' .
                        e($tabA2->mataKuliah?->nama_mk) . ' ' . e($tabA2->nama_kelas) .
                        '</strong> pada ' . e($tabA2->hari) . ', ' .
                        e($tabA2->jam_mulai) . '&ndash;' . e($tabA2->jam_selesai) . '.'
                    );
                }
            }
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

    /** Enroll banyak mahasiswa sekaligus ke kelas ini via checkbox */
    public function kelasEnrollBanyak(Request $request, Praktikum $praktikum): RedirectResponse
    {
        $v = $request->validate([
            'mahasiswa_ids'   => ['required', 'array', 'min:1'],
            'mahasiswa_ids.*' => ['exists:mahasiswa,id'],
        ], [
            'mahasiswa_ids.required' => 'Pilih minimal 1 mahasiswa.',
            'mahasiswa_ids.min'      => 'Pilih minimal 1 mahasiswa.',
        ]);

        $sudahAda   = $praktikum->mahasiswa()->pluck('mahasiswa.id')->toArray();
        $akanDitambah = array_diff($v['mahasiswa_ids'], $sudahAda);

        if (empty($akanDitambah)) {
            return back()->with('error', 'Semua mahasiswa yang dipilih sudah ada di kelas ini.');
        }

        $praktikum->mahasiswa()->attach($akanDitambah);

        $jumlah = count($akanDitambah);
        return back()->with('success', "{$jumlah} mahasiswa berhasil ditambahkan ke kelas ini.");
    }
 
    // ── Asisten ────────────────────────────────────────────────────────────
    // ── Hapus Semua Kelas Praktikum ───────────────────────────────────────
    public function kelasHapusSemua(): RedirectResponse
    {
        DB::transaction(function () {
            // Hapus semua data turunan kelas dulu
            \App\Models\RekapDetailNilai::query()->delete();
            \App\Models\NilaiEvaluasi::query()->delete();
            \App\Models\NilaiAsistensi::query()->delete();
            \App\Models\NilaiUjian::query()->delete();
            \App\Models\Presensi::query()->delete();
            DB::table('mahasiswa_praktikum')->delete();
            Praktikum::query()->delete();
        });
        return back()->with('success', 'Semua data kelas praktikum beserta nilai & presensi berhasil dihapus.');
    }

    // ── Hapus Semua Asisten ───────────────────────────────────────────────
    public function asistenHapusSemua(): RedirectResponse
    {
        DB::transaction(function () {
            $userIds = Asisten::pluck('user_id')->filter();
            Asisten::query()->delete();
            if ($userIds->isNotEmpty()) {
                User::whereIn('id', $userIds)->delete();
            }
        });
        return back()->with('success', 'Semua data asisten beserta akun berhasil dihapus.');
    }

    // ── Hapus Semua Dosen ─────────────────────────────────────────────────
    public function dosenHapusSemua(): RedirectResponse
    {
        DB::transaction(function () {
            $userIds = Dosen::pluck('user_id')->filter();
            Dosen::query()->delete();
            if ($userIds->isNotEmpty()) {
                User::whereIn('id', $userIds)->delete();
            }
        });
        return back()->with('success', 'Semua data dosen beserta akun berhasil dihapus.');
    }

    // ── Hapus Semua Mahasiswa ─────────────────────────────────────────────
    public function mahasiswaHapusSemua(): RedirectResponse
    {
        DB::transaction(function () {
            \App\Models\RekapDetailNilai::query()->delete();
            \App\Models\NilaiEvaluasi::query()->delete();
            \App\Models\NilaiAsistensi::query()->delete();
            \App\Models\NilaiUjian::query()->delete();
            \App\Models\Presensi::query()->delete();
            DB::table('mahasiswa_praktikum')->delete();
            Mahasiswa::query()->delete();
        });
        return back()->with('success', 'Semua data mahasiswa beserta nilai & presensi berhasil dihapus.');
    }
    // ── Import Kelas Praktikum via Excel ──────────────────────────────────
    public function kelasImport(Request $request): RedirectResponse
    {
        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ], [
            'file_excel.required' => 'File Excel wajib dipilih.',
            'file_excel.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file_excel.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $path = $request->file('file_excel')->getRealPath();
        $rows = $this->bacaExcelKelas($path);

        if (empty($rows)) {
            return back()->with('error', 'File Excel kosong atau format tidak dikenali.');
        }

        $berhasil = 0;
        $dilewati = 0;
        $duplikat = 0;
        $errors   = [];

        $hariValid    = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        $mulaiValid   = ['07:00','08:00','09:00','09:40','10:00','10:30','11:00','13:00','14:00','14:30','15:00','15:40','16:00'];
        $selesaiValid = ['08:40','09:30','10:20','11:20','12:00','12:10','14:20','15:00','15:20','15:30','16:20','17:00','18:10','18:20'];

        foreach ($rows as $index => $row) {
            $nomorBaris = $index + 2;

            $kodeMk     = isset($row[0]) ? trim((string) $row[0]) : '';
            $namaKelas  = isset($row[1]) ? trim((string) $row[1]) : '';
            $hari       = isset($row[2]) ? trim((string) $row[2]) : '';
            $jamMulai   = isset($row[3]) ? trim((string) $row[3]) : '';
            $jamSelesai = isset($row[4]) ? trim((string) $row[4]) : '';
            $namaDosen  = isset($row[5]) ? trim((string) $row[5]) : '';
            $namaA1     = isset($row[6]) ? trim((string) $row[6]) : '';
            $namaA2     = isset($row[7]) ? trim((string) $row[7]) : '';
            $namaRuangan= isset($row[8]) ? trim((string) $row[8]) : '';

            if ($kodeMk === '' && $namaKelas === '') continue;

            if ($kodeMk === '') {
                $errors[] = "Baris {$nomorBaris}: Kode MK kosong, dilewati.";
                $dilewati++; continue;
            }
            if ($namaKelas === '') {
                $errors[] = "Baris {$nomorBaris}: Nama Kelas kosong (Kode MK: {$kodeMk}), dilewati.";
                $dilewati++; continue;
            }

            $mk = MataKuliah::where('kode_mk', $kodeMk)->first();
            if (!$mk) {
                $errors[] = "Baris {$nomorBaris}: Kode MK '{$kodeMk}' tidak ditemukan, dilewati.";
                $dilewati++; continue;
            }

            if ($hari !== '' && !in_array($hari, $hariValid)) {
                $errors[] = "Baris {$nomorBaris}: Hari '{$hari}' tidak valid, dilewati.";
                $dilewati++; continue;
            }
            if ($jamMulai !== '' && !in_array($jamMulai, $mulaiValid)) {
                $errors[] = "Baris {$nomorBaris}: Jam Mulai '{$jamMulai}' tidak valid, dilewati.";
                $dilewati++; continue;
            }
            if ($jamSelesai !== '' && !in_array($jamSelesai, $selesaiValid)) {
                $errors[] = "Baris {$nomorBaris}: Jam Selesai '{$jamSelesai}' tidak valid, dilewati.";
                $dilewati++; continue;
            }

            $sudahAda = Praktikum::where('mata_kuliah_id', $mk->id)
                ->where('nama_kelas', $namaKelas)
                ->exists();
            if ($sudahAda) {
                $duplikat++; continue;
            }

            // Resolve nama → ID (tidak cocok → null, tidak error)
            $dosenId   = $namaDosen  ? Dosen::where('nama_dosen',   $namaDosen)->value('id')   : null;
            $asistenId = $namaA1     ? Asisten::where('nama_asisten',$namaA1)->value('id')      : null;
            $asisten2Id= $namaA2     ? Asisten::where('nama_asisten',$namaA2)->value('id')      : null;
            $ruanganId = $namaRuangan? Ruangan::where('nama_ruangan',$namaRuangan)->value('id') : null;

            // Pesan info jika nama diisi tapi tidak cocok
            if ($namaDosen   && !$dosenId)   $errors[] = "Baris {$nomorBaris}: Dosen '{$namaDosen}' tidak ditemukan — kolom dikosongkan.";
            if ($namaA1      && !$asistenId) $errors[] = "Baris {$nomorBaris}: Asisten 1 '{$namaA1}' tidak ditemukan — kolom dikosongkan.";
            if ($namaA2      && !$asisten2Id)$errors[] = "Baris {$nomorBaris}: Asisten 2 '{$namaA2}' tidak ditemukan — kolom dikosongkan.";
            if ($namaRuangan && !$ruanganId) $errors[] = "Baris {$nomorBaris}: Ruangan '{$namaRuangan}' tidak ditemukan — kolom dikosongkan.";

            $data = [
                'mata_kuliah_id' => $mk->id,
                'nama_kelas'     => $namaKelas,
                'hari'           => $hari       ?: null,
                'jam_mulai'      => $jamMulai   ?: null,
                'jam_selesai'    => $jamSelesai ?: null,
                'dosen_id'       => $dosenId,
                'asisten_id'     => $asistenId,
                'asisten2_id'    => $asisten2Id,
                'ruangan_id'     => $ruanganId,
            ];

            if ($hari && $jamMulai && $jamSelesai) {
                $data['jadwal'] = $hari . ', ' . $jamMulai . '–' . $jamSelesai;
            } elseif ($hari) {
                $data['jadwal'] = $hari;
            }

            Praktikum::create($data);
            $berhasil++;
        }

        $pesan = "{$berhasil} kelas berhasil diimport.";
        if ($duplikat > 0) $pesan .= " {$duplikat} kelas duplikat dilewati.";
        if ($dilewati > 0) $pesan .= " {$dilewati} baris tidak valid dilewati.";

        if (!empty($errors)) session()->flash('import_errors_kelas', $errors);

        return back()->with($berhasil > 0 ? 'success' : 'error', $pesan);
    }

    /**
     * Generate template Excel import kelas praktikum secara dinamis.
     * Kolom: Kode MK | Nama Kelas | Hari | Jam Mulai | Jam Selesai
     * Baris contoh diisi otomatis dari data MK yang sudah ada di DB.
     */
    public function kelasTemplateExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $path = public_path('templates/template_import_kelas.xlsx');

        if (!file_exists($path)) {
            abort(404, 'File template tidak ditemukan. Pastikan file ada di public/templates/');
        }

        return response()->download($path, 'template_import_kelas.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
    
    public function asisten(Request $request): View {
        $q    = $request->input('q', '');
        $sort = in_array($request->input('sort'), ['nama_asisten','nim'])
            ? $request->input('sort')
            : null;
        $dir  = $request->input('dir') === 'desc' ? 'desc' : 'asc';

        $asistenAll = Asisten::with([
                'user',
                'praktikum.mataKuliah',
                'praktikumSebagaiAsisten2.mataKuliah',
            ])
            ->when($q, fn($query) => $query->where(function ($sub) use ($q) {
                $sub->where('nama_asisten', 'like', "%{$q}%")
                    ->orWhere('nim', 'like', "%{$q}%")
                    // Cari berdasarkan username
                    ->orWhereHas('user', fn($u) =>
                        $u->where('username', 'like', "%{$q}%")
                    )
                    // Cari berdasarkan kelas yang didampingi (sebagai Asisten 1)
                    ->orWhereHas('praktikum', fn($p) =>
                        $p->where('nama_kelas', 'like', "%{$q}%")
                          ->orWhereHas('mataKuliah', fn($mk) =>
                              $mk->where('kode_mk',  'like', "%{$q}%")
                                 ->orWhere('nama_mk', 'like', "%{$q}%")
                          )
                    )
                    // Cari berdasarkan kelas yang didampingi (sebagai Asisten 2)
                    ->orWhereHas('praktikumSebagaiAsisten2', fn($p) =>
                        $p->where('nama_kelas', 'like', "%{$q}%")
                          ->orWhereHas('mataKuliah', fn($mk) =>
                              $mk->where('kode_mk',  'like', "%{$q}%")
                                 ->orWhere('nama_mk', 'like', "%{$q}%")
                          )
                    );
            }))
            ->orderBy($sort ?? 'nama_asisten', $sort ? $dir : 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('laboran.asisten.index', compact('asistenAll', 'q', 'sort', 'dir'));
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
    public function dosen(Request $request): View {
        $q    = $request->input('q', '');
        $sort = in_array($request->input('sort'), ['nama_dosen', 'nidn'])
            ? $request->input('sort')
            : null;
        $dir  = $request->input('dir') === 'desc' ? 'desc' : 'asc';

        $dosenAll = Dosen::with(['user', 'praktikum.mataKuliah'])
            ->when($q, fn($query) => $query->where(function ($sub) use ($q) {
                $sub->where('nama_dosen', 'like', "%{$q}%")
                    ->orWhere('nidn', 'like', "%{$q}%")
                    ->orWhereHas('user', fn($u) =>
                        $u->where('username', 'like', "%{$q}%")
                    )
                    ->orWhereHas('praktikum', fn($p) =>
                        $p->where('nama_kelas', 'like', "%{$q}%")
                          ->orWhereHas('mataKuliah', fn($mk) =>
                              $mk->where('kode_mk',  'like', "%{$q}%")
                                 ->orWhere('nama_mk', 'like', "%{$q}%")
                          )
                    );
            }))
            ->orderBy($sort ?? 'nama_dosen', $sort ? $dir : 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('laboran.dosen.index', compact('dosenAll', 'q', 'sort', 'dir'));
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
        $q         = $request->input('q', '');
        $filterError = $request->boolean('error');
        $sortValid = ['nim_mahasiswa', 'nama_mahasiswa'];
        $sort = in_array($request->input('sort'), $sortValid) ? $request->input('sort') : null;
        $dir  = $request->input('dir') === 'desc' ? 'desc' : 'asc';

        $mahasiswaAll = Mahasiswa::with('praktikum.mataKuliah')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nim_mahasiswa',  'like', "%{$q}%")
                        ->orWhere('nama_mahasiswa', 'like', "%{$q}%")
                        ->orWhereHas('praktikum', function ($p) use ($q) {
                            $p->where('nama_kelas', 'like', "%{$q}%")
                              ->orWhereHas('mataKuliah', fn($m) => $m->where('nama_mk',  'like', "%{$q}%")
                                                                      ->orWhere('kode_mk', 'like', "%{$q}%"));
                        });
                });
            })
            ->when($filterError, function ($query) {
                // Mahasiswa yang melebihi batas tidak hadir (A/I/S) di minimal 1 kelas
                $query->whereHas('presensi', function ($p) {
                    $p->whereIn('status_kehadiran', ['A','I','S'])
                      ->groupBy('praktikum_id')
                      ->havingRaw('COUNT(*) >= ?', [Mahasiswa::BATAS_ALPA]);
                });
            })
            ->orderBy($sort ?? 'nama_mahasiswa', $sort ? $dir : 'asc')
            ->paginate(10)
            ->withQueryString();

        // Hitung total mahasiswa bermasalah untuk label tombol
        $jumlahError = Mahasiswa::whereHas('presensi', function ($p) {
            $p->whereIn('status_kehadiran', ['A','I','S'])
              ->groupBy('praktikum_id')
              ->havingRaw('COUNT(*) >= ?', [Mahasiswa::BATAS_ALPA]);
        })->count();

        return view('laboran.mahasiswa.index', compact('mahasiswaAll', 'q', 'sort', 'dir', 'filterError', 'jumlahError'));
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

    // ── Import Mahasiswa via Excel ─────────────────────────────────────────
    public function mahasiswaImport(Request $request): RedirectResponse
    {
        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ], [
            'file_excel.required' => 'File Excel wajib dipilih.',
            'file_excel.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file_excel.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $path = $request->file('file_excel')->getRealPath();
        $rows = $this->bacaExcel($path);

        if (empty($rows)) {
            return back()->with('error', 'File Excel kosong atau format tidak dikenali.');
        }

        $berhasil = 0;
        $dilewati = 0;
        $duplikat = 0;
        $errors   = [];

        foreach ($rows as $index => $row) {
            // Header ada di baris 4, data mulai baris 5 — sesuai template kelas
            $nomorBaris = $index + 5;
            $nim  = isset($row[0]) ? trim((string) $row[0]) : '';
            $nama = isset($row[1]) ? trim((string) $row[1]) : '';

            if ($nim === '' && $nama === '') continue;

            if ($nim === '') {
                $errors[] = "Baris {$nomorBaris}: NIM kosong, dilewati.";
                $dilewati++; continue;
            }
            if ($nama === '') {
                $errors[] = "Baris {$nomorBaris}: Nama kosong (NIM: {$nim}), dilewati.";
                $dilewati++; continue;
            }
            if (!preg_match('/^\d+$/', $nim)) {
                $errors[] = "Baris {$nomorBaris}: NIM '{$nim}' harus angka saja, dilewati.";
                $dilewati++; continue;
            }
            if (Mahasiswa::where('nim_mahasiswa', $nim)->exists()) {
                $duplikat++; continue;
            }

            Mahasiswa::create(['nim_mahasiswa' => $nim, 'nama_mahasiswa' => $nama]);
            $berhasil++;
        }

        $pesan = "{$berhasil} mahasiswa berhasil diimport.";
        if ($duplikat > 0) $pesan .= " {$duplikat} NIM duplikat dilewati.";
        if ($dilewati > 0) $pesan .= " {$dilewati} baris tidak valid dilewati.";

        if (!empty($errors)) session()->flash('import_errors', $errors);

        return back()->with($berhasil > 0 ? 'success' : 'error', $pesan);
    }

    public function mahasiswaTemplateExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $path = public_path('templates/template_import_mahasiswa.xlsx');

        if (!file_exists($path)) {
            abort(404, 'File template tidak ditemukan. Pastikan file ada di public/templates/');
        }

        return response()->download($path, 'template_import_mahasiswa.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Resolve nama teks → ID model.
     * Urutan: exact match → contains (hanya jika hasil tepat 1) → ambiguous/not found.
     */
    private function resolveNama(string $modelClass, string $kolom, string $nama, int $nomorBaris, string $label, array &$errors): ?int
    {
        if ($nama === '') return null;

        // 1. Exact match (case-insensitive)
        $exact = $modelClass::whereRaw("LOWER({$kolom}) = ?", [mb_strtolower($nama)])->get();
        if ($exact->count() === 1) return $exact->first()->id;

        // 2. Contains match
        $like = $modelClass::where($kolom, 'like', '%' . $nama . '%')->get();

        if ($like->count() === 1) {
            // Unik — aman dipakai
            return $like->first()->id;
        }

        if ($like->count() === 0) {
            $errors[] = "Baris {$nomorBaris}: {$label} '{$nama}' tidak ditemukan — kolom dikosongkan.";
            return null;
        }

        // Lebih dari 1 hasil — ambiguous
        $namaList = $like->pluck($kolom)->join(', ');
        $errors[] = "Baris {$nomorBaris}: {$label} '{$nama}' ambigu ({$like->count()} hasil: {$namaList}) — tulis nama lebih lengkap, kolom dikosongkan.";
        return null;
    }
    
    /**
     * Baca file .xlsx untuk import kelas praktikum.
     * Menangani format jam sebagai string ("07:00") maupun
     * sebagai nilai numerik Excel (desimal, misal 0.2916).
     */
    private function bacaExcelKelas(string $path): array
    {
        if (!class_exists('ZipArchive')) return [];

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) return [];

        // Baca shared strings
        $sharedStrings = [];
        $ssXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssXml !== false) {
            // Hapus SEMUA namespace (default + prefixed) sebelum parse
            $ssXml = preg_replace('/\s+xmlns(?::\w+)?="[^"]*"/', '', $ssXml);
            $ss = @simplexml_load_string($ssXml);
            if ($ss) {
                foreach ($ss->si as $si) {
                    $val = '';
                    foreach ($si->xpath('.//t') as $t) $val .= (string) $t;
                    $sharedStrings[] = $val;
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheetXml === false) return [];

        // Hapus SEMUA namespace (default + prefixed) sebelum parse
        $sheetXml = preg_replace('/\s+xmlns(?::\w+)?="[^"]*"/', '', $sheetXml);
        // Hapus prefix pada tag dan atribut (misal mc:Ignorable, x14:xxx, dll)
        $sheetXml = preg_replace('/<(\/?)\w+:(\w+)/', '<$1$2', $sheetXml);
        $sheetXml = preg_replace('/\s\w+:\w+="[^"]*"/', '', $sheetXml);

        $sheet = @simplexml_load_string($sheetXml);
        if (!$sheet) return [];

        $rows        = [];
        $headerFound = false;

        foreach ($sheet->sheetData->row as $row) {
            // 0=kode_mk, 1=nama_kelas, 2=hari, 3=jam_mulai, 4=jam_selesai
            // 5=nama_dosen, 6=nama_asisten1, 7=nama_asisten2, 8=nama_ruangan
            $rowData = [0=>'',1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>'',8=>''];

            foreach ($row->c as $cell) {
                $ref       = (string) $cell['r'];
                $colLetter = preg_replace('/[^A-Za-z]/', '', $ref);
                $colIndex  = $this->kolomKeIndex($colLetter);
                $type      = (string) $cell['t'];

                $value = '';
                if (isset($cell->v)) {
                    $value = trim((string) $cell->v);
                } elseif (isset($cell->is->t)) {
                    $value = trim((string) $cell->is->t);
                }

                if ($type === 's' && isset($sharedStrings[(int) $value])) {
                    $value = $sharedStrings[(int) $value];
                }
                if ($type === 'inlineStr' && isset($cell->is)) {
                    $val2 = '';
                    foreach ($cell->is->xpath('.//t') as $t) $val2 .= (string) $t;
                    $value = $val2;
                }

                // Kolom jam: konversi desimal Excel → HH:MM
                if (in_array($colIndex, [3, 4]) && is_numeric($value) && $value !== '') {
                    $totalMenit = round((float) $value * 24 * 60);
                    $jam        = intdiv($totalMenit, 60);
                    $menit      = $totalMenit % 60;
                    $value      = str_pad($jam, 2, '0', STR_PAD_LEFT) . ':' . str_pad($menit, 2, '0', STR_PAD_LEFT);
                }

                if ($colIndex <= 8) {
                    $rowData[$colIndex] = trim($value);
                }
            }

            $col0 = strtolower(trim($rowData[0]));

            // Deteksi baris header — cari baris yang kolom A berisi "kode mk" atau variasinya
            if (!$headerFound) {
                if (
                    $col0 === 'kode mk' ||
                    $col0 === 'kode_mk' ||
                    str_contains($col0, 'kode mk') ||
                    str_contains($col0, 'kode_mk')
                ) {
                    $headerFound = true;
                }
                continue;
            }

            // Lewati baris kosong
            if ($rowData[0] === '' && $rowData[1] === '') continue;

            $rows[] = $rowData;
        }

        return $rows;
    }

    private function bacaExcel(string $path): array
    {   
        if (!class_exists('ZipArchive')) return [];

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) return [];

        // Shared strings — opsional, tidak semua xlsx punya
        $sharedStrings = [];
        $ssXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssXml !== false) {
            $ss = simplexml_load_string($ssXml);
            if ($ss) {
                foreach ($ss->si as $si) {
                    $val = '';
                    foreach ($si->xpath('.//t') as $t) $val .= (string) $t;
                    $sharedStrings[] = $val;
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheetXml === false) return [];

        // Hapus namespace agar xpath/children bisa diakses langsung
        $sheetXml = preg_replace('/xmlns[^=]*="[^"]*"/', '', $sheetXml);
        $sheetXml = preg_replace('/\s+/', ' ', $sheetXml);

        $sheet = simplexml_load_string($sheetXml);
        if (!$sheet) return [];

        $rows        = [];
        $headerFound = false;

        foreach ($sheet->sheetData->row as $row) {
            $rowData = [0 => '', 1 => ''];

            foreach ($row->c as $cell) {
                $ref       = (string) $cell['r'];
                $colLetter = preg_replace('/[^A-Za-z]/', '', $ref);
                $colIndex  = $this->kolomKeIndex($colLetter);
                $type      = (string) $cell['t'];

                // Ambil nilai: coba <v> dulu, lalu <is><t>
                $value = '';
                if (isset($cell->v)) {
                    $value = trim((string) $cell->v);
                } elseif (isset($cell->is->t)) {
                    $value = trim((string) $cell->is->t);
                }

                // Shared string
                if ($type === 's' && isset($sharedStrings[(int) $value])) {
                    $value = $sharedStrings[(int) $value];
                }

                // Inline string (type="inlineStr")
                if ($type === 'inlineStr' && isset($cell->is)) {
                    $value = '';
                    foreach ($cell->is->xpath('.//t') as $t) {
                        $value .= (string) $t;
                    }
                }

                $rowData[$colIndex] = trim($value);
            }

            $col0 = strtolower($rowData[0]);

            // Temukan baris header NIM di baris manapun
            if (!$headerFound) {
                if ($col0 === 'nim') {
                    $headerFound = true;
                }
                continue;
            }

            // Lewati baris kosong
            if ($rowData[0] === '' && $rowData[1] === '') continue;

            $rows[] = [$rowData[0], $rowData[1]];
        }

        return $rows;
    }

    private function kolomKeIndex(string $col): int
    {
        $col   = strtoupper(trim($col));
        $index = 0;
        for ($i = 0; $i < strlen($col); $i++) {
            $index = $index * 26 + (ord($col[$i]) - ord('A') + 1);
        }
        return $index - 1;
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