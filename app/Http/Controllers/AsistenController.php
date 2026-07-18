<?php
namespace App\Http\Controllers;
use App\Models\{Praktikum,Mahasiswa,Presensi,PresensiAsistensi,NilaiAsistensi,NilaiUjian,NilaiEvaluasi,RekapDetailNilai};
use Illuminate\Http\{Request, RedirectResponse, JsonResponse};
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
        ;
        // Absensi sesi Asistensi 1/2/3, dikelompokkan per mahasiswa lalu per sesi (asistensi_ke)
        $presensiAsistensiMap = PresensiAsistensi::where('praktikum_id', $praktikum->id)
            ->get()->groupBy('mahasiswa_id')->map(fn($rows) => $rows->keyBy('asistensi_ke'));
        return view('asisten.presensi', compact('praktikum','mahasiswaList','presensiMap','pertemuan','stats','presensiAsistensiMap'));
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

    /** Simpan absensi sesi Asistensi (1, 2, atau 3) — terpisah dari presensi praktikum P1-P14 */
    public function presensiAsistensiSimpan(Request $request, Praktikum $praktikum): RedirectResponse
    {
        abort_unless($this->isAuthorizedForKelas($praktikum), 403, 'Anda tidak berwenang mengakses kelas ini.');
 
        $v = $request->validate([
            'asistensi_ke'   => ['required','integer','in:1,2,3'],
            'presensi'       => ['array'],
            'presensi.*.hadir' => ['nullable'],
        ]);
        $asistensiKe = (int) $v['asistensi_ke'];
        foreach ($request->input('presensi', []) as $mahasiswaId => $data) {
            PresensiAsistensi::updateOrCreate(
                ['mahasiswa_id'=>$mahasiswaId,'praktikum_id'=>$praktikum->id,'asistensi_ke'=>$asistensiKe],
                ['hadir'=>!empty($data['hadir'])]
            );
        }
        return back()->with('success',"Absensi Asistensi {$asistensiKe} disimpan.");
    }

    /** Nilai per kelas (Praktikum) */
    public function nilai(Praktikum $praktikum): View
    {
        abort_unless($this->isAuthorizedForKelas($praktikum), 403, 'Anda tidak berwenang mengakses kelas ini.');

        $mahasiswaList = $praktikum->mahasiswa()->orderBy('nama_mahasiswa')->get();
        $nilaiMap  = [];
        $alpaMap   = [];
        foreach ($mahasiswaList as $m) {
            $nilaiMap[$m->id] = [
                'evaluasi'  => NilaiEvaluasi::firstOrCreate(['mahasiswa_id'=>$m->id,'praktikum_id'=>$praktikum->id]),
                'asistensi' => NilaiAsistensi::firstOrCreate(['mahasiswa_id'=>$m->id,'praktikum_id'=>$praktikum->id]),
                'ujian'     => NilaiUjian::firstOrCreate(['mahasiswa_id'=>$m->id,'praktikum_id'=>$praktikum->id]),
                'rekap'     => RekapDetailNilai::where(['mahasiswa_id'=>$m->id,'praktikum_id'=>$praktikum->id])->first(),
            ];
            $alpaMap[$m->id] = $m->jumlahAlpaDiKelas($praktikum->id);
        }
        $batasAlpa = Mahasiswa::BATAS_ALPA;
        return view('asisten.nilai', compact('praktikum','mahasiswaList','nilaiMap','alpaMap','batasAlpa'));
    }

    /** Simpan bobot penilaian untuk satu kelas */
    public function bobotSimpan(Request $request, Praktikum $praktikum): RedirectResponse
    {
        abort_unless($this->isAuthorizedForKelas($praktikum), 403);

        $v = $request->validate([
            'bobot_kegiatan'            => ['required','numeric','min:0','max:100'],
            'bobot_evaluasi_praktikum'  => ['required','numeric','min:0','max:100'],
            'bobot_praktikum'           => ['required','numeric','min:0','max:100'],
            'bobot_asistensi'           => ['required','numeric','min:0','max:100'],
            'bobot_mid'                 => ['required','numeric','min:0','max:100'],
            'bobot_uas'                 => ['required','numeric','min:0','max:100'],
        ]);

        // Sub-bobot praktikum (kegiatan + evaluasi) harus = 100
        $totalSub = $v['bobot_kegiatan'] + $v['bobot_evaluasi_praktikum'];
        if (abs($totalSub - 100) > 0.01) {
            return back()->withErrors(['bobot' => "Total bobot Kegiatan + Evaluasi Praktikum harus 100%. Saat ini: {$totalSub}%"]);
        }

        // Bobot komponen nilai akhir harus = 100
        $total = $v['bobot_praktikum'] + $v['bobot_asistensi'] + $v['bobot_mid'] + $v['bobot_uas'];
        if (abs($total - 100) > 0.01) {
            return back()->withErrors(['bobot' => "Total bobot Praktikum + Asistensi + MID + UAS harus 100%. Saat ini: {$total}%"]);
        }

        $praktikum->update(array_merge($v, ['bobot_kehadiran' => 0]));

        // Hitung ulang nilai pertemuan semua mahasiswa dengan bobot baru
        foreach ($praktikum->mahasiswa as $m) {
            $eval = \App\Models\NilaiEvaluasi::where([
                'mahasiswa_id' => $m->id,
                'praktikum_id' => $praktikum->id,
            ])->first();
            if ($eval) {
                $eval->hitungDanSimpanNilaiPertemuan(
                    $v['bobot_kegiatan'],
                    $v['bobot_evaluasi_praktikum']
                );
            }
            RekapDetailNilai::hitungDanSimpan($m->id, $praktikum->id);
        }

        // Hitung ulang rekap semua mahasiswa di kelas ini dengan bobot baru
        foreach ($praktikum->mahasiswa as $m) {
            RekapDetailNilai::hitungDanSimpan($m->id, $praktikum->id);
        }

        return back()->with('success', 'Pembobotan disimpan dan nilai akhir dihitung ulang.');
    }

    public function nilaiSimpan(Request $request, Praktikum $praktikum, Mahasiswa $mahasiswa): RedirectResponse
    {
        abort_unless($this->isAuthorizedForKelas($praktikum), 403, 'Anda tidak berwenang mengakses kelas ini.');

        $v = $request->validate([
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
        ]);
        NilaiEvaluasi::updateOrCreate(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$praktikum->id],
            array_filter(array_intersect_key($v, array_flip(['p1','p2','p3','p4','p5','p6','p7','p8','p9','p10','p11','p12','p13','p14'])),fn($v)=>$v!==null));
        NilaiAsistensi::updateOrCreate(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$praktikum->id],
            array_filter(array_intersect_key($v, array_flip(['nilai_asistensi1','nilai_asistensi2','nilai_asistensi3'])),fn($v)=>$v!==null));
        NilaiUjian::updateOrCreate(['mahasiswa_id'=>$mahasiswa->id,'praktikum_id'=>$praktikum->id],
            array_filter(array_intersect_key($v, array_flip(['nilai_MID','nilai_UAS'])),fn($v)=>$v!==null));
        RekapDetailNilai::hitungDanSimpan($mahasiswa->id, $praktikum->id);
        return back()->with('success','Nilai disimpan.');
    }

    /** Simpan nilai SEMUA mahasiswa dalam satu submit */
    public function nilaiSimpanSemua(Request $request, Praktikum $praktikum): RedirectResponse
    {
        abort_unless($this->isAuthorizedForKelas($praktikum), 403, 'Anda tidak berwenang mengakses kelas ini.');

        $kolom = array_merge(
            array_map(fn($i) => "p{$i}", range(1, 14)),
            ['nilai_asistensi1','nilai_asistensi2','nilai_asistensi3','nilai_MID','nilai_UAS']
        );

        foreach ($request->input('nilai', []) as $mahasiswaId => $v) {
            $mahasiswaId = (int) $mahasiswaId;
            // Pastikan mahasiswa ini memang ada di kelas
            if (!$praktikum->mahasiswa->contains($mahasiswaId)) continue;

            // Filter hanya field yang diisi
            // Konversi string kosong → null (bukan dibuang) supaya field yang dikosongkan ikut tersimpan
            $toNull = fn($x) => ($x === '' || $x === null) ? null : (float) $x;
            // Kolom evaluasi: p1_kegiatan, p1_evaluasi, ... p14_kegiatan, p14_evaluasi
            $evalKeys = [];
            for ($i = 1; $i <= 14; $i++) {
                $evalKeys[] = "p{$i}_kegiatan";
                $evalKeys[] = "p{$i}_evaluasi";
            }
            $eval = array_map($toNull, array_intersect_key($v, array_flip($evalKeys)));
            $asst = array_map($toNull, array_intersect_key($v, array_flip(['nilai_asistensi1','nilai_asistensi2','nilai_asistensi3'])));
            $ujn  = array_map($toNull, array_intersect_key($v, array_flip(['nilai_MID','nilai_UAS'])));

            if ($eval) NilaiEvaluasi::updateOrCreate(['mahasiswa_id'=>$mahasiswaId,'praktikum_id'=>$praktikum->id], $eval);
            if ($asst) NilaiAsistensi::updateOrCreate(['mahasiswa_id'=>$mahasiswaId,'praktikum_id'=>$praktikum->id], $asst);
            if ($ujn)  NilaiUjian::updateOrCreate(['mahasiswa_id'=>$mahasiswaId,'praktikum_id'=>$praktikum->id], $ujn);

            RekapDetailNilai::hitungDanSimpan($mahasiswaId, $praktikum->id);
        }

        return back()->with('success', 'Nilai semua mahasiswa berhasil disimpan.');
    }
    /**
     * Autosave nilai via AJAX — identik dengan nilaiSimpanSemua
     * tapi return JSON (tidak redirect) sehingga halaman tidak reload.
     * Dipanggil otomatis oleh JS setiap 2 detik setelah perubahan terakhir.
     */
    public function nilaiAutosave(Request $request, Praktikum $praktikum): JsonResponse
    {
        abort_unless($this->isAuthorizedForKelas($praktikum), 403);

        $toNull = fn($x) => ($x === '' || $x === null) ? null : (float) $x;

        foreach ($request->input('nilai', []) as $mahasiswaId => $v) {
            $mahasiswaId = (int) $mahasiswaId;
            if (!$praktikum->mahasiswa->contains($mahasiswaId)) continue;

            // Kolom evaluasi: p1_kegiatan, p1_evaluasi, ... p14_kegiatan, p14_evaluasi
            $evalKeys = [];
            for ($i = 1; $i <= 14; $i++) {
                $evalKeys[] = "p{$i}_kegiatan";
                $evalKeys[] = "p{$i}_evaluasi";
            }
            $eval = array_map($toNull, array_intersect_key($v, array_flip($evalKeys)));
            $asst = array_map($toNull, array_intersect_key($v, array_flip(['nilai_asistensi1','nilai_asistensi2','nilai_asistensi3'])));
            $ujn  = array_map($toNull, array_intersect_key($v, array_flip(['nilai_MID','nilai_UAS'])));

            if ($eval) {
                $evalModel = NilaiEvaluasi::updateOrCreate(
                    ['mahasiswa_id'=>$mahasiswaId,'praktikum_id'=>$praktikum->id],
                    $eval
                );
                // Hitung ulang nilai p1..p14 dari sub-kolom berdasarkan bobot kelas
                $evalModel->hitungDanSimpanNilaiPertemuan(
                    (float) ($praktikum->bobot_kegiatan ?? 50),
                    (float) ($praktikum->bobot_evaluasi_praktikum ?? 50)
                );
            }
            if ($asst) NilaiAsistensi::updateOrCreate(['mahasiswa_id'=>$mahasiswaId,'praktikum_id'=>$praktikum->id], $asst);
            if ($ujn)  NilaiUjian::updateOrCreate(['mahasiswa_id'=>$mahasiswaId,'praktikum_id'=>$praktikum->id], $ujn);

            RekapDetailNilai::hitungDanSimpan($mahasiswaId, $praktikum->id);
        }

        return response()->json([
            'success'   => true,
            'pesan'     => 'Tersimpan otomatis pukul ' . now()->format('H:i'),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Autosave satu baris presensi via AJAX.
     * Dipanggil segera saat radio button status kehadiran diubah.
     */
    public function presensiAutosave(Request $request, Praktikum $praktikum): JsonResponse
    {
        abort_unless($this->isAuthorizedForKelas($praktikum), 403);

        $mahasiswaId = (int) $request->input('mahasiswa_id');
        $pertemuan   = (int) $request->input('pertemuan_ke');
        $status      = $request->input('status_kehadiran');
        $catatan     = $request->input('catatan', '') ?: null;

        if (!in_array($status, ['H','I','S','A'])) {
            return response()->json(['success' => false, 'pesan' => 'Status tidak valid.'], 422);
        }
        if (!$praktikum->mahasiswa->contains($mahasiswaId)) {
            return response()->json(['success' => false, 'pesan' => 'Mahasiswa tidak ada di kelas ini.'], 403);
        }

        Presensi::updateOrCreate(
            [
                'mahasiswa_id' => $mahasiswaId,
                'praktikum_id' => $praktikum->id,
                'pertemuan_ke' => $pertemuan,
            ],
            ['status_kehadiran' => $status, 'catatan' => $catatan]
        );

        return response()->json([
            'success' => true,
            'pesan'   => 'Disimpan ' . now()->format('H:i'),
        ]);
    }

    /** Reset satu kolom (asist1/2/3, MID, UAS) ke 0 untuk semua mahasiswa di kelas */
    public function nilaiResetKolom(Praktikum $praktikum, string $kolom): RedirectResponse
    {
        abort_unless($this->isAuthorizedForKelas($praktikum), 403, 'Anda tidak berwenang mengakses kelas ini.');

        $kolomValid = ['nilai_asistensi1','nilai_asistensi2','nilai_asistensi3','nilai_MID','nilai_UAS'];
        abort_unless(in_array($kolom, $kolomValid), 422, 'Kolom tidak valid.');

        $label = [
            'nilai_asistensi1' => 'Asist 1',
            'nilai_asistensi2' => 'Asist 2',
            'nilai_asistensi3' => 'Asist 3',
            'nilai_MID'        => 'MID',
            'nilai_UAS'        => 'UAS',
        ][$kolom];

        if (in_array($kolom, ['nilai_asistensi1','nilai_asistensi2','nilai_asistensi3'])) {
            NilaiAsistensi::where('praktikum_id', $praktikum->id)->update([$kolom => 0]);
        } else {
            NilaiUjian::where('praktikum_id', $praktikum->id)->update([$kolom => 0]);
        }

        $praktikum->mahasiswa->each(fn($m) => RekapDetailNilai::hitungDanSimpan($m->id, $praktikum->id));

        return back()->with('success', "Kolom {$label} semua mahasiswa direset ke 0.");
    }

    /** Reset nilai satu kolom pertemuan (p1–p14) untuk semua mahasiswa di kelas */
    public function nilaiResetPertemuan(Praktikum $praktikum, int $pertemuan): RedirectResponse
    {
        abort_unless($this->isAuthorizedForKelas($praktikum), 403, 'Anda tidak berwenang mengakses kelas ini.');
        abort_unless($pertemuan >= 1 && $pertemuan <= 14, 422, 'Nomor pertemuan tidak valid.');

        $kolom = 'p' . $pertemuan;

        // Nol-kan kolom pertemuan pada semua mahasiswa yang punya record NilaiEvaluasi
        NilaiEvaluasi::where('praktikum_id', $praktikum->id)
            ->update([$kolom => 0]);

        // Hitung ulang rekap semua mahasiswa di kelas ini
        $praktikum->mahasiswa->each(function ($m) use ($praktikum) {
            RekapDetailNilai::hitungDanSimpan($m->id, $praktikum->id);
        });

        return back()->with('success', "Nilai pertemuan {$pertemuan} semua mahasiswa direset ke 0.");
    }

    /** Rekap nilai, presensi, dan absensi asistensi per kelas */
    public function rekap(Praktikum $praktikum): View   
    {
        abort_unless($this->isAuthorizedForKelas($praktikum), 403, 'Anda tidak berwenang mengakses kelas ini.');
 
        $mahasiswaList = $praktikum->mahasiswa()->orderBy('nim_mahasiswa')->get();
        $presensiAll   = Presensi::where('praktikum_id', $praktikum->id)->get()
            ->groupBy('mahasiswa_id')->map(fn($r) => $r->keyBy('pertemuan_ke'));
        // Rekap nilai akhir per mahasiswa, khusus untuk kelas (praktikum) ini
        $rekapNilaiMap = RekapDetailNilai::where('praktikum_id', $praktikum->id)
            ->get()->keyBy('mahasiswa_id');
        // Absensi sesi Asistensi 1/2/3, dikelompokkan per mahasiswa lalu per sesi (asistensi_ke)
        $presensiAsistensiAll = PresensiAsistensi::where('praktikum_id', $praktikum->id)
            ->get()->groupBy('mahasiswa_id')->map(fn($rows) => $rows->keyBy('asistensi_ke'));
        return view('asisten.rekap', compact('praktikum','mahasiswaList','presensiAll','rekapNilaiMap','presensiAsistensiAll'));
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