<?php
namespace App\Http\Controllers;
use App\Models\{Praktikum,Presensi,PresensiAsistensi,NilaiEvaluasi,NilaiAsistensi};
use App\Support\SimpleXlsxWriter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

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
        $this->authorizeKelas($praktikum);
        [$mahasiswaList, $presensiAll, $presensiAsistensiAll] = $this->dataRekap($praktikum);
        return view('pengawas.rekap', compact('praktikum','mahasiswaList','presensiAll','presensiAsistensiAll'));
    }

    /**
     * Pastikan dosen yang login adalah pemilik (pengampu) kelas praktikum ini.
     * Mencegah dosen mengakses/mengeksport rekap kelas dosen lain lewat URL.
     */
    private function authorizeKelas(Praktikum $praktikum): void {
        $dosen = Auth::user()->dosen;
        abort_unless($dosen && $praktikum->dosen_id === $dosen->id, 403, 'Anda tidak memiliki akses ke kelas ini.');
    }

    /** Ambil data mahasiswa + presensi yang sama dipakai baik untuk halaman web, PDF, maupun Excel */
    private function dataRekap(Praktikum $praktikum): array {
        $mahasiswaList = $praktikum->mahasiswa()
            ->with(['rekap', 'praktikum', 'presensi' => fn($q) => $q->where('praktikum_id', $praktikum->id)])
            ->orderBy('nama_mahasiswa')->get();
        $presensiAll   = Presensi::where('praktikum_id', $praktikum->id)->get()
            ->groupBy('mahasiswa_id')->map(fn($r) => $r->keyBy('pertemuan_ke'));
        // Absensi sesi Asistensi 1/2/3, dikelompokkan per mahasiswa lalu per sesi
        $presensiAsistensiAll = PresensiAsistensi::where('praktikum_id', $praktikum->id)
            ->get()->groupBy('mahasiswa_id')->map(fn($rows) => $rows->keyBy('asistensi_ke'));
        return [$mahasiswaList, $presensiAll, $presensiAsistensiAll];
    }

    /** Export rekap nilai & presensi kelas dalam bentuk PDF */
    public function rekapPdf(Praktikum $praktikum): Response {
        $this->authorizeKelas($praktikum);
        $praktikum->load(['mataKuliah','dosen','ruangan']);
        [$mahasiswaList, $presensiAll, $presensiAsistensiAll] = $this->dataRekap($praktikum);

        $namaFile = 'rekap-' . str($praktikum->nama_kelas)->slug() . '-' . str($praktikum->mataKuliah?->kode_mk)->slug() . '.pdf';

        $pdf = Pdf::loadView('pengawas.rekap-pdf', compact('praktikum','mahasiswaList','presensiAll','presensiAsistensiAll'))
            ->setPaper('a4', 'landscape');

        return $pdf->download($namaFile);
    }

    /**
     * Export rekap nilai & presensi kelas dalam bentuk Excel (.xlsx).
     *
     * Sheet yang dihasilkan:
     *  1. Rekap Lengkap   — kehadiran per pertemuan, nilai per pertemuan,
     *                       kehadiran & nilai per sesi asistensi, MID, UAS,
     *                       lalu rangkuman di kolom paling kanan.
     *  2. Rekap Presensi  — detail H/I/S/A setiap pertemuan + total + %.
     *  3. Rekap Evaluasi  — nilai P1–P14 per pertemuan + rata-rata.
     *  4. Rekap Asistensi — hadir & nilai tiap sesi asistensi + total/rata.
     *  5. Rekap Nilai     — ringkasan komponen nilai akhir (seperti semula).
     */
    public function rekapExcel(Praktikum $praktikum): Response
    {
        $this->authorizeKelas($praktikum);
        $praktikum->load(['mataKuliah', 'dosen', 'ruangan']);
        [$mahasiswaList, $presensiAll, $presensiAsistensiAll] = $this->dataRekap($praktikum);

        // Ambil nilai evaluasi dan asistensi per mahasiswa
        $nilaiEvaluasiAll = NilaiEvaluasi::where('praktikum_id', $praktikum->id)
            ->get()->keyBy('mahasiswa_id');

        $nilaiAsistensiAll = NilaiAsistensi::where('praktikum_id', $praktikum->id)
            ->get()->keyBy('mahasiswa_id');

        $jumlahPertemuan = $praktikum->jumlah_pertemuan;

        $xlsx = new SimpleXlsxWriter();

        // ── Sheet 1: Rekap Lengkap ────────────────────────────────────────────────
        $xlsx->addSheet(
            'Rekap Lengkap',
            $this->buildHeaderLengkap($jumlahPertemuan),
            $this->buildRowsLengkap(
                $mahasiswaList, $presensiAll, $nilaiEvaluasiAll,
                $presensiAsistensiAll, $nilaiAsistensiAll, $praktikum
            )
        );

        // ── Sheet 2: Rekap Presensi ───────────────────────────────────────────────
        $headerPresensi = ['No', 'NIM', 'Nama'];
        for ($i = 1; $i <= $jumlahPertemuan; $i++) {
            $headerPresensi[] = 'P' . $i;
        }
        $headerPresensi[] = 'Total Hadir';
        $headerPresensi[] = 'Total Izin';
        $headerPresensi[] = 'Total Sakit';
        $headerPresensi[] = 'Total Alpha';
        $headerPresensi[] = '% Kehadiran';

        $rowsPresensi = [];
        foreach ($mahasiswaList as $idx => $m) {
            $pp  = $presensiAll[$m->id] ?? collect();
            $row = [$idx + 1, $m->nim_mahasiswa, $m->nama_mahasiswa];
            for ($j = 1; $j <= $jumlahPertemuan; $j++) {
                $ps    = $pp[$j] ?? null;
                $row[] = $ps?->status_kehadiran ?? '-';
            }
            $hadir  = $pp->where('status_kehadiran', 'H')->count();
            $izin   = $pp->where('status_kehadiran', 'I')->count();
            $sakit  = $pp->where('status_kehadiran', 'S')->count();
            $alpha  = $pp->where('status_kehadiran', 'A')->count();
            $persen = $jumlahPertemuan > 0 ? round(min($hadir / $jumlahPertemuan, 1) * 100, 1) : 0;
            $row[]  = $hadir;
            $row[]  = $izin;
            $row[]  = $sakit;
            $row[]  = $alpha;
            $row[]  = $persen . '%';
            $rowsPresensi[] = $row;
        }
        $xlsx->addSheet('Rekap Presensi', $headerPresensi, $rowsPresensi);

        // ── Sheet 3: Rekap Evaluasi ───────────────────────────────────────────────
        $headerEvaluasi = ['No', 'NIM', 'Nama'];
        for ($i = 1; $i <= 14; $i++) {
            $headerEvaluasi[] = 'Pertemuan ' . $i;
        }
        $headerEvaluasi[] = 'Rata-rata Evaluasi';

        $rowsEvaluasi = [];
        foreach ($mahasiswaList as $idx => $m) {
            $ne  = $nilaiEvaluasiAll[$m->id] ?? null;
            $row = [$idx + 1, $m->nim_mahasiswa, $m->nama_mahasiswa];
            $vals = [];
            for ($i = 1; $i <= 14; $i++) {
                $v     = $ne ? ($ne->{'p' . $i} ?? null) : null;
                $row[] = $v !== null ? (float) $v : '-';
                if ($v !== null) $vals[] = (float) $v;
            }
            $row[]          = count($vals) > 0 ? round(array_sum($vals) / count($vals), 2) : '-';
            $rowsEvaluasi[] = $row;
        }
        $xlsx->addSheet('Rekap Evaluasi', $headerEvaluasi, $rowsEvaluasi);

        // ── Sheet 4: Rekap Asistensi ──────────────────────────────────────────────
        $headerAsistensi = [
            'No', 'NIM', 'Nama',
            'Hadir Asist 1', 'Nilai Asist 1',
            'Hadir Asist 2', 'Nilai Asist 2',
            'Hadir Asist 3', 'Nilai Asist 3',
            'Total Hadir Asistensi',
            'Rata-rata Nilai Asistensi',
        ];

        $rowsAsistensi = [];
        foreach ($mahasiswaList as $idx => $m) {
            $pa  = $presensiAsistensiAll[$m->id] ?? collect();
            $na  = $nilaiAsistensiAll[$m->id] ?? null;
            $row = [$idx + 1, $m->nim_mahasiswa, $m->nama_mahasiswa];
            $jumlahHadir = 0;
            $nilaiVals   = [];
            for ($k = 1; $k <= 3; $k++) {
                $pas = $pa[$k] ?? null;
                if (!$pas) {
                    $row[] = '-';
                } elseif ($pas->hadir) {
                    $row[] = 'H';
                    $jumlahHadir++;
                } else {
                    $row[] = 'A';
                }
                $nilaiKey = 'nilai_asistensi' . $k;
                $nv       = $na ? ($na->$nilaiKey ?? null) : null;
                $row[]    = $nv !== null ? (float) $nv : '-';
                if ($nv !== null) $nilaiVals[] = (float) $nv;
            }
            $row[]           = $jumlahHadir;
            $row[]           = count($nilaiVals) > 0 ? round(array_sum($nilaiVals) / count($nilaiVals), 2) : '-';
            $rowsAsistensi[] = $row;
        }
        $xlsx->addSheet('Rekap Asistensi', $headerAsistensi, $rowsAsistensi);

        // ── Sheet 5: Rekap Nilai (ringkasan seperti semula) ───────────────────────
        $headerNilai = [
            'No', 'NIM', 'Nama',
            'Nilai Evaluasi', 'Nilai Asistensi', 'Nilai MID', 'Nilai UAS',
            'Nilai Akhir', 'Huruf',
            '% Kehadiran', 'Jumlah Alpha',
        ];
        $rowsNilai = [];
        foreach ($mahasiswaList as $idx => $m) {
            $r      = $m->rekap->firstWhere('praktikum_id', $praktikum->id);
            $pp     = $presensiAll[$m->id] ?? collect();
            $hadir  = $pp->where('status_kehadiran', 'H')->count();
            $persen = $jumlahPertemuan > 0 ? round(min($hadir / $jumlahPertemuan, 1) * 100, 1) : 0;
            $rowsNilai[] = [
                $idx + 1,
                $m->nim_mahasiswa,
                $m->nama_mahasiswa,
                $r?->nilai_praktikum ?? '-',
                $r?->nilai_asistensi ?? '-',
                $r?->nilai_MID       ?? '-',
                $r?->nilai_UAS       ?? '-',
                $r?->nilai_akhir     ?? '-',
                $r?->nilai_huruf     ?? '-',
                $persen . '%',
                $m->jumlahAlpaDiKelas($praktikum->id),
            ];
        }
        $xlsx->addSheet('Rekap Nilai', $headerNilai, $rowsNilai);

        $namaFile = 'rekap-' . str($praktikum->nama_kelas)->slug()
            . '-' . str($praktikum->mataKuliah?->kode_mk)->slug() . '.xlsx';

        return $xlsx->download($namaFile);
    }

    // ── Helper: header sheet "Rekap Lengkap" ─────────────────────────────────────
    private function buildHeaderLengkap(int $jumlahPertemuan): array
    {
        $header = ['No', 'NIM', 'Nama'];

        for ($i = 1; $i <= $jumlahPertemuan; $i++) {
            $header[] = 'P' . $i . ' (Hadir)';
        }
        for ($i = 1; $i <= 14; $i++) {
            $header[] = 'P' . $i . ' (Nilai)';
        }
        for ($k = 1; $k <= 3; $k++) {
            $header[] = 'Asist' . $k . ' (Hadir)';
            $header[] = 'Asist' . $k . ' (Nilai)';
        }

        $header[] = 'Nilai MID';
        $header[] = 'Nilai UAS';

        // Rangkuman di kolom paling kanan
        $header[] = '--- RANGKUMAN ---';
        $header[] = 'Rata-rata Evaluasi';
        $header[] = 'Rata-rata Asistensi';
        $header[] = 'Nilai Akhir';
        $header[] = 'Huruf';
        $header[] = 'Total Hadir';
        $header[] = 'Total Alpha';
        $header[] = '% Kehadiran';

        return $header;
    }

    // ── Helper: baris data sheet "Rekap Lengkap" ─────────────────────────────────
    private function buildRowsLengkap(
        $mahasiswaList,
        $presensiAll,
        $nilaiEvaluasiAll,
        $presensiAsistensiAll,
        $nilaiAsistensiAll,
        Praktikum $praktikum
    ): array {
        $jumlahPertemuan = $praktikum->jumlah_pertemuan;
        $rows = [];

        foreach ($mahasiswaList as $idx => $m) {
            $pp = $presensiAll[$m->id]            ?? collect();
            $ne = $nilaiEvaluasiAll[$m->id]       ?? null;
            $pa = $presensiAsistensiAll[$m->id]   ?? collect();
            $na = $nilaiAsistensiAll[$m->id]       ?? null;
            $r  = $m->rekap->firstWhere('praktikum_id', $praktikum->id);

            $row = [$idx + 1, $m->nim_mahasiswa, $m->nama_mahasiswa];

            // Kehadiran per pertemuan
            for ($j = 1; $j <= $jumlahPertemuan; $j++) {
                $ps    = $pp[$j] ?? null;
                $row[] = $ps?->status_kehadiran ?? '-';
            }

            // Nilai evaluasi per pertemuan (P1–P14)
            $evalVals = [];
            for ($i = 1; $i <= 14; $i++) {
                $v     = $ne ? ($ne->{'p' . $i} ?? null) : null;
                $row[] = $v !== null ? (float) $v : '-';
                if ($v !== null) $evalVals[] = (float) $v;
            }

            // Asistensi: hadir + nilai per sesi
            $asistNilaiVals = [];
            for ($k = 1; $k <= 3; $k++) {
                $pas = $pa[$k] ?? null;
                if (!$pas) {
                    $row[] = '-';
                } elseif ($pas->hadir) {
                    $row[] = 'H';
                } else {
                    $row[] = 'A';
                }
                $nilaiKey = 'nilai_asistensi' . $k;
                $nv       = $na ? ($na->$nilaiKey ?? null) : null;
                $row[]    = $nv !== null ? (float) $nv : '-';
                if ($nv !== null) $asistNilaiVals[] = (float) $nv;
            }

            // MID & UAS
            $row[] = $r?->nilai_MID ?? '-';
            $row[] = $r?->nilai_UAS ?? '-';

            // Rangkuman
            $totalHadir = $pp->where('status_kehadiran', 'H')->count();
            $totalAlpha = $pp->where('status_kehadiran', 'A')->count();
            $persen     = $jumlahPertemuan > 0
                ? round(min($totalHadir / $jumlahPertemuan, 1) * 100, 1)
                : 0;

            $row[] = ''; // kolom pemisah "--- RANGKUMAN ---"
            $row[] = count($evalVals)       > 0 ? round(array_sum($evalVals)       / count($evalVals), 2)       : '-';
            $row[] = count($asistNilaiVals) > 0 ? round(array_sum($asistNilaiVals) / count($asistNilaiVals), 2) : '-';
            $row[] = $r?->nilai_akhir ?? '-';
            $row[] = $r?->nilai_huruf ?? '-';
            $row[] = $totalHadir;
            $row[] = $totalAlpha;
            $row[] = $persen . '%';

            $rows[] = $row;
        }

        return $rows;
    }
}