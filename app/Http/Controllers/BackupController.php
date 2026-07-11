<?php
namespace App\Http\Controllers;

use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\Support\Facades\{DB, Storage};
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    private string $folder = 'backups';

    /** Deteksi path mysqldump dan mysql — coba PATH dulu, lalu fallback XAMPP */
    private function binaryPath(string $bin): string {
        $paths = [
            $bin,
            "C:\\xampp\\mysql\\bin\\{$bin}.exe",
            "C:\\xampp\\mysql\\bin\\{$bin}",
            "/usr/bin/{$bin}",
            "/usr/local/bin/{$bin}",
        ];
        foreach ($paths as $p) {
            // Pada Windows, 'where'; pada Unix, 'which'
            $test = PHP_OS_FAMILY === 'Windows'
                ? shell_exec("where {$bin} 2>NUL")
                : shell_exec("which {$bin} 2>/dev/null");
            if ($test) return $bin;           // ada di PATH
            if (file_exists($p)) return $p;   // ada di path absolut
        }
        return $bin; // fallback, biarkan OS yang throw error
    }

    /** Pastikan folder backup ada */
    private function pastikanFolder(): void {
        if (!Storage::exists($this->folder)) {
            Storage::makeDirectory($this->folder);
        }
    }

    /** Validasi nama file — cegah path traversal */
    private function namaSah(string $filename): bool {
        return (bool) preg_match('/^asistio_backup_[\d_-]+\.sql$/', $filename);
    }

    /** Daftar semua backup */
    public function index(): View {
        $this->pastikanFolder();
        $files = collect(Storage::files($this->folder))
            ->map(function ($path) {
                $nama = basename($path);
                return [
                    'nama'    => $nama,
                    'ukuran'  => $this->formatUkuran(Storage::size($path)),
                    'tanggal' => date('d M Y, H:i', Storage::lastModified($path)),
                    'ts'      => Storage::lastModified($path),
                ];
            })
            ->sortByDesc('ts')
            ->values();

        return view('laboran.backup.index', compact('files'));
    }

    /** Buat backup baru menggunakan mysqldump */
    public function buat(): RedirectResponse {
        $this->pastikanFolder();

        $cfg  = config('database.connections.mysql');
        $host = $cfg['host'];
        $port = $cfg['port'];
        $user = $cfg['username'];
        $pass = $cfg['password'];
        $db   = $cfg['database'];

        $dump  = $this->binaryPath('mysqldump');
        $nama  = 'asistio_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $path  = Storage::path("{$this->folder}/{$nama}");

        $passFlag = $pass !== '' ? "-p" . escapeshellarg($pass) : '';
        $cmd = "{$dump} -h {$host} -P {$port} -u " . escapeshellarg($user) . " {$passFlag} " . escapeshellarg($db) . " > " . escapeshellarg($path) . " 2>&1";

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0 || !file_exists($path) || filesize($path) === 0) {
            @unlink($path);
            return back()->with('error', 'Backup gagal. Pastikan mysqldump tersedia dan konfigurasi DB benar. Detail: ' . implode(' ', $output));
        }

        return back()->with('success', "Backup berhasil dibuat: {$nama} (" . $this->formatUkuran(filesize($path)) . ")");
    }

    /** Download file backup */
    public function unduh(string $filename): StreamedResponse|RedirectResponse {
        if (!$this->namaSah($filename) || !Storage::exists("{$this->folder}/{$filename}")) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }
        return Storage::download("{$this->folder}/{$filename}", $filename);
    }

    /** Pulihkan database dari file backup */
    public function pulihkan(string $filename): RedirectResponse {
        if (!$this->namaSah($filename) || !Storage::exists("{$this->folder}/{$filename}")) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        $cfg  = config('database.connections.mysql');
        $host = $cfg['host'];
        $port = $cfg['port'];
        $user = $cfg['username'];
        $pass = $cfg['password'];
        $db   = $cfg['database'];

        $mysql = $this->binaryPath('mysql');
        $path  = Storage::path("{$this->folder}/{$filename}");

        $passFlag = $pass !== '' ? "-p" . escapeshellarg($pass) : '';
        $cmd = "{$mysql} -h {$host} -P {$port} -u " . escapeshellarg($user) . " {$passFlag} " . escapeshellarg($db) . " < " . escapeshellarg($path) . " 2>&1";

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            return back()->with('error', 'Pemulihan gagal: ' . implode(' ', $output));
        }

        return back()->with('success', "Database berhasil dipulihkan dari {$filename}. Semua data sebelumnya telah digantikan.");
    }

    /** Hapus file backup */
    public function hapus(string $filename): RedirectResponse {
        if (!$this->namaSah($filename)) {
            return back()->with('error', 'Nama file tidak valid.');
        }
        Storage::delete("{$this->folder}/{$filename}");
        return back()->with('success', "Backup {$filename} dihapus.");
    }

    /** Upload file .sql untuk dipulihkan */
    public function upload(Request $request): RedirectResponse {
        $request->validate([
            'file_sql' => ['required', 'file', 'mimes:sql,txt', 'max:51200'], // max 50 MB
        ], [
            'file_sql.required' => 'Pilih file SQL terlebih dahulu.',
            'file_sql.mimes'    => 'File harus berekstensi .sql atau .txt.',
            'file_sql.max'      => 'Ukuran file maksimal 50 MB.',
        ]);

        $this->pastikanFolder();
        $nama = 'asistio_backup_' . date('Y-m-d_H-i-s') . '_upload.sql';
        $request->file('file_sql')->storeAs($this->folder, $nama);

        return redirect()->route('laboran.backup.pulihkan', $nama)
            ->with('konfirmasi_restore', true);
    }

    private function formatUkuran(int $bytes): string {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}