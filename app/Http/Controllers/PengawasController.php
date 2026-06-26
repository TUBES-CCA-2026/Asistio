<?php
namespace App\Http\Controllers;
use App\Models\{Praktikum,Presensi};
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
        [$mahasiswaList, $presensiAll] = $this->dataRekap($praktikum);
        return view('pengawas.rekap', compact('praktikum','mahasiswaList','presensiAll'));
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
        $mahasiswaList = $praktikum->mahasiswa()->with(['rekap','presensi'])->orderBy('nama_mahasiswa')->get();
        // Set relasi praktikum manual (sudah ada di memori) agar $m->praktikum->jumlah_pertemuan
        // pada getPersentaseHadirAttribute() tidak memicu query N+1 per mahasiswa.
        $mahasiswaList->each(fn($m) => $m->setRelation('praktikum', $praktikum));
        $presensiAll   = Presensi::where('praktikum_id', $praktikum->id)->get()
            ->groupBy('mahasiswa_id')->map(fn($r) => $r->keyBy('pertemuan_ke'));
        return [$mahasiswaList, $presensiAll];
    }

    /** Export rekap nilai & presensi kelas dalam bentuk PDF */
    public function rekapPdf(Praktikum $praktikum): Response {
        $this->authorizeKelas($praktikum);
        $praktikum->load(['mataKuliah','dosen','ruangan']);
        [$mahasiswaList, $presensiAll] = $this->dataRekap($praktikum);

        $namaFile = 'rekap-' . str($praktikum->nama_kelas)->slug() . '-' . str($praktikum->mataKuliah?->kode_mk)->slug() . '.pdf';

        $pdf = Pdf::loadView('pengawas.rekap-pdf', compact('praktikum','mahasiswaList','presensiAll'))
            ->setPaper('a4', 'landscape');

        return $pdf->download($namaFile);
    }

    /** Export rekap nilai & presensi kelas dalam bentuk Excel (.xlsx) */
    public function rekapExcel(Praktikum $praktikum): Response {
        $this->authorizeKelas($praktikum);
        $praktikum->load(['mataKuliah','dosen','ruangan']);
        [$mahasiswaList, $presensiAll] = $this->dataRekap($praktikum);

        $xlsx = new SimpleXlsxWriter();

        $headerNilai = ['NIM','Nama','Eval','Asistensi','MID','UAS','Nilai Akhir','Huruf','Kehadiran (%)','Jumlah Alpha'];
        $rowsNilai = [];
        foreach ($mahasiswaList as $m) {
            $r = $m->rekap;
            $rowsNilai[] = [
                $m->nim_mahasiswa, $m->nama_mahasiswa,
                $r?->nilai_praktikum ?? '', $r?->nilai_asistensi ?? '',
                $r?->nilai_MID ?? '', $r?->nilai_UAS ?? '',
                $r?->nilai_akhir ?? '', $r?->nilai_huruf ?? '',
                rtrim($m->persentase_hadir, '%'), $m->jumlah_alpa,
            ];
        }
        $xlsx->addSheet('Rekap Nilai', $headerNilai, $rowsNilai);

        $headerPresensi = ['NIM','Nama'];
        for ($i = 1; $i <= 14; $i++) { $headerPresensi[] = 'P' . $i; }
        $headerPresensi[] = 'Hadir';
        $headerPresensi[] = 'Alpha';

        $rowsPresensi = [];
        foreach ($mahasiswaList as $m) {
            $pp  = $presensiAll[$m->id] ?? collect();
            $row = [$m->nim_mahasiswa, $m->nama_mahasiswa];
            for ($j = 1; $j <= 14; $j++) {
                $ps    = $pp[$j] ?? null;
                $row[] = $ps?->status_kehadiran ?? '';
            }
            $row[] = $pp->where('status_kehadiran', 'H')->count();
            $row[] = $pp->where('status_kehadiran', 'A')->count();
            $rowsPresensi[] = $row;
        }
        $xlsx->addSheet('Rekap Presensi', $headerPresensi, $rowsPresensi);

        $namaFile = 'rekap-' . str($praktikum->nama_kelas)->slug() . '-' . str($praktikum->mataKuliah?->kode_mk)->slug() . '.xlsx';

        return $xlsx->download($namaFile);
    }
}