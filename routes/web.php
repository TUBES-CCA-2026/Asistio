<?php
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LaboranController;
use App\Http\Controllers\AsistenController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\PengawasController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ── Laboran ────────────────────────────────────────────────────────────────
Route::prefix('laboran')->middleware(['auth','role:laboran'])->name('laboran.')->group(function () {
    Route::get('/dashboard',                  [LaboranController::class,'dashboard'])->name('dashboard');
    // Mata Kuliah
    Route::get('/mata-kuliah',                [LaboranController::class,'mataKuliah'])->name('mata-kuliah');
    Route::post('/mata-kuliah',               [LaboranController::class,'mataKuliahStore'])->name('mata-kuliah.store');
    Route::patch('/mata-kuliah/{mataKuliah}', [LaboranController::class,'mataKuliahUpdate'])->name('mata-kuliah.update');
    Route::delete('/mata-kuliah/{mataKuliah}',[LaboranController::class,'mataKuliahDestroy'])->name('mata-kuliah.destroy');
    // Ruangan
    Route::get('/ruangan',                    [LaboranController::class,'ruangan'])->name('ruangan');
    Route::post('/ruangan',                   [LaboranController::class,'ruanganStore'])->name('ruangan.store');
    Route::patch('/ruangan/{ruangan}',        [LaboranController::class,'ruanganUpdate'])->name('ruangan.update');
    Route::delete('/ruangan/{ruangan}',       [LaboranController::class,'ruanganDestroy'])->name('ruangan.destroy');
    // Kelas Praktikum
    Route::get('/kelas',                      [LaboranController::class,'kelas'])->name('kelas');
    Route::post('/kelas',                     [LaboranController::class,'kelasStore'])->name('kelas.store');
    Route::get('/kelas/{praktikum}',           [LaboranController::class,'kelasShow'])->name('kelas.show');
    Route::patch('/kelas/{praktikum}',         [LaboranController::class,'kelasUpdate'])->name('kelas.update');
    Route::post('/kelas/{praktikum}/mahasiswa', [LaboranController::class,'kelasTambahMahasiswa'])->name('kelas.mahasiswa.add');
    Route::delete('/kelas/{praktikum}/mahasiswa/{mahasiswa}', [LaboranController::class,'kelasHapusMahasiswa'])->name('kelas.mahasiswa.remove');
    Route::delete('/kelas/{praktikum}',       [LaboranController::class,'kelasDestroy'])->name('kelas.destroy');
    // Asisten
    Route::get('/asisten',                    [LaboranController::class,'asisten'])->name('asisten');
    Route::post('/asisten',                   [LaboranController::class,'asistenStore'])->name('asisten.store');
    Route::patch('/asisten/{asisten}',        [LaboranController::class,'asistenUpdate'])->name('asisten.update');
    Route::patch('/asisten/{asisten}/reset-password', [LaboranController::class,'asistenResetPassword'])->name('asisten.reset-password');
    Route::delete('/asisten/{asisten}',       [LaboranController::class,'asistenDestroy'])->name('asisten.destroy');
    // Dosen
    Route::get('/dosen',                      [LaboranController::class,'dosen'])->name('dosen');
    Route::post('/dosen',                     [LaboranController::class,'dosenStore'])->name('dosen.store');
    Route::patch('/dosen/{dosen}',            [LaboranController::class,'dosenUpdate'])->name('dosen.update');
    Route::patch('/dosen/{dosen}/reset-password', [LaboranController::class,'dosenResetPassword'])->name('dosen.reset-password');
    Route::delete('/dosen/{dosen}',           [LaboranController::class,'dosenDestroy'])->name('dosen.destroy');
    // Mahasiswa
    Route::get('/mahasiswa',                  [LaboranController::class,'mahasiswa'])->name('mahasiswa');
    Route::post('/mahasiswa',                 [LaboranController::class,'mahasiswaStore'])->name('mahasiswa.store');
    Route::get('/mahasiswa/{mahasiswa}/edit', [LaboranController::class,'mahasiswaEdit'])->name('mahasiswa.edit');
    Route::patch('/mahasiswa/{mahasiswa}',    [LaboranController::class,'mahasiswaUpdate'])->name('mahasiswa.update');
    Route::delete('/mahasiswa/{mahasiswa}',   [LaboranController::class,'mahasiswaDestroy'])->name('mahasiswa.destroy');
    Route::get('/mahasiswa/{mahasiswa}/nilai/{praktikum}', [LaboranController::class,'mahasiswaNilai'])->name('mahasiswa.nilai');
    Route::post('/mahasiswa/{mahasiswa}/nilai/{praktikum}',[LaboranController::class,'mahasiswaNilaiUpdate'])->name('mahasiswa.nilai.update');});

// ── Asisten ────────────────────────────────────────────────────────────────
Route::prefix('asisten')->middleware(['auth','role:asisten'])->name('asisten.')->group(function () {
    Route::get('/dashboard',                                          [AsistenController::class,'dashboard'])->name('dashboard');
    // Presensi per kelas (Praktikum)
    Route::get('/presensi/{praktikum}',                               [AsistenController::class,'presensi'])->name('presensi');
    Route::post('/presensi/{praktikum}/simpan',                       [AsistenController::class,'presensiSimpan'])->name('presensi.simpan');
    Route::post('/presensi/{praktikum}/asistensi', [AsistenController::class,'presensiAsistensiSimpan'])->name('presensi.asistensi.simpan');
    // Nilai per kelas (Praktikum)
    Route::get('/nilai/{praktikum}',                                  [AsistenController::class,'nilai'])->name('nilai');
    Route::post('/nilai/{praktikum}/mahasiswa/{mahasiswa}',           [AsistenController::class,'nilaiSimpan'])->name('nilai.simpan');
    Route::post('/bobot/{praktikum}',                                 [AsistenController::class,'bobotSimpan'])->name('bobot.simpan');
    Route::post('/nilai/{praktikum}/reset-pertemuan/{pertemuan}',     [AsistenController::class,'nilaiResetPertemuan'])->name('nilai.reset-pertemuan');
    Route::post('/nilai/{praktikum}/simpan-semua',                    [AsistenController::class,'nilaiSimpanSemua'])->name('nilai.simpan-semua');
    Route::post('/nilai/{praktikum}/reset-kolom/{kolom}',             [AsistenController::class,'nilaiResetKolom'])->name('nilai.reset-kolom');
    // Rekap per kelas
    Route::get('/rekap/{praktikum}',                                  [AsistenController::class,'rekap'])->name('rekap');
    // Ganti password
    Route::get('/ganti-password',  [AsistenController::class,'gantiPassword'])->name('ganti-password');
    Route::post('/ganti-password', [AsistenController::class,'gantiPasswordUpdate'])->name('ganti-password.update');
});

// ── Dosen (Pengawas) ────────────────────────────────────────────────────────
Route::prefix('pengawas')->middleware(['auth','role:dosen'])->name('pengawas.')->group(function () {
    Route::get('/dashboard',            [PengawasController::class,'dashboard'])->name('dashboard');
    Route::get('/rekap/{praktikum}',    [PengawasController::class,'rekap'])->name('rekap');
    Route::get('/rekap/{praktikum}/export/pdf',   [PengawasController::class,'rekapPdf'])->name('rekap.export.pdf');
    Route::get('/rekap/{praktikum}/export/excel', [PengawasController::class,'rekapExcel'])->name('rekap.export.excel');
});

// ── Dosen — Ganti Password ─────────────────────────────────────────────────
Route::prefix('dosen')->middleware(['auth','role:dosen'])->name('dosen.')->group(function () {
    Route::get('/ganti-password',  [DosenController::class,'gantiPassword'])->name('ganti-password');
    Route::post('/ganti-password', [DosenController::class,'gantiPasswordUpdate'])->name('ganti-password.update');
});

// ── Laboran — Ganti Password ───────────────────────────────────────────────
Route::prefix('laboran')->middleware(['auth','role:laboran'])->name('laboran.')->group(function () {
    Route::get('/ganti-password',  [LaboranController::class,'gantiPassword'])->name('ganti-password');
    Route::post('/ganti-password', [LaboranController::class,'gantiPasswordUpdate'])->name('ganti-password.update');
    
    // ── Backup & Restore ─────────────────────────────────────────────────
    Route::prefix('backup')->name('backup.')->group(function () {
        Route::get('/',                             [\App\Http\Controllers\BackupController::class,'index'])->name('index');
        Route::post('/buat',                        [\App\Http\Controllers\BackupController::class,'buat'])->name('buat');
        Route::get('/unduh/{filename}',             [\App\Http\Controllers\BackupController::class,'unduh'])->name('unduh');
        Route::post('/pulihkan/{filename}',         [\App\Http\Controllers\BackupController::class,'pulihkan'])->name('pulihkan');
        Route::delete('/hapus/{filename}',          [\App\Http\Controllers\BackupController::class,'hapus'])->name('hapus');
        Route::post('/upload',                      [\App\Http\Controllers\BackupController::class,'upload'])->name('upload');
    });
});