@extends('layouts.app')
@section('title','Backup & Pemulihan')
@section('page-title','Backup & Pemulihan Data')
@section('content')

<div class="grid grid-2" style="gap:16px;margin-bottom:16px;align-items:start;">

    {{-- Buat Backup --}}
    <div class="card">
        <div class="card-header"><span class="card-title">Buat Backup Baru</span></div>
        <div class="card-body">
            <p style="font-size:13px;color:var(--text-muted);margin:0 0 16px;">
                Membuat snapshot seluruh database sekarang. File disimpan di server dan bisa diunduh kapan saja.
            </p>
            <form method="POST" action="{{ route('laboran.backup.buat') }}">@csrf
                <button class="btn btn-primary btn-block">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Buat Backup Sekarang
                </button>
            </form>
        </div>
    </div>

    {{-- Upload & Restore dari file --}}
    <div class="card">
        <div class="card-header"><span class="card-title">Pulihkan dari File</span></div>
        <div class="card-body">
            <p style="font-size:13px;color:var(--text-muted);margin:0 0 16px;">
                Upload file <code>.sql</code> dari komputer lain atau backup lama, lalu pulihkan langsung.
            </p>
            <form method="POST" action="{{ route('laboran.backup.upload') }}" enctype="multipart/form-data">@csrf
                <div class="form-group">
                    <input type="file" name="file_sql" accept=".sql,.txt" class="form-control" required>
                </div>
                <button class="btn btn-outline btn-block"
                    onclick="return confirm('Upload dan langsung pulihkan database dari file ini?\n\nSELURUH data saat ini akan digantikan!')">
                    Upload &amp; Pulihkan
                </button>
            </form>
        </div>
    </div>

</div>

{{-- Daftar Backup --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Backup Tersimpan</span>
        <span class="badge badge-primary">{{ $files->count() }} file</span>
    </div>
    <div class="table-wrapper"><table class="table">
        <thead><tr>
            <th>Nama File</th>
            <th>Ukuran</th>
            <th>Dibuat</th>
            <th>Aksi</th>
        </tr></thead>
        <tbody>
        @forelse($files as $f)
        <tr>
            <td style="font-family:monospace;font-size:12px;">{{ $f['nama'] }}</td>
            <td>{{ $f['ukuran'] }}</td>
            <td>{{ $f['tanggal'] }}</td>
            <td>
                <div class="action-group">
                    {{-- Download --}}
                    <a href="{{ route('laboran.backup.unduh', $f['nama']) }}" class="btn btn-sm btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Unduh
                    </a>
                    {{-- Restore --}}
                    <form method="POST" action="{{ route('laboran.backup.pulihkan', $f['nama']) }}">@csrf
                        <button class="btn btn-sm btn-primary"
                            onclick="return confirm('Pulihkan database dari:\n{{ $f['nama'] }}\n\nSELURUH data saat ini akan digantikan dengan data dari backup ini.\n\nLanjutkan?')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.51"/></svg>
                            Pulihkan
                        </button>
                    </form>
                    {{-- Hapus --}}
                    <form method="POST" action="{{ route('laboran.backup.hapus', $f['nama']) }}">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"
                            onclick="return confirm('Hapus file backup {{ $f['nama'] }}?')">
                            Hapus
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="4"><div class="empty-state"><p>Belum ada backup. Klik "Buat Backup Sekarang" untuk membuat yang pertama.</p></div></td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>

<div class="card" style="margin-top:16px;border:1px solid #f59e0b;background:#fffbeb;">
    <div class="card-body" style="padding:14px 16px;">
        <p style="margin:0;font-size:13px;color:#92400e;">
            <strong>⚠ Catatan penting:</strong>
            Operasi <strong>Pulihkan</strong> akan <strong>menghapus dan menggantikan seluruh data yang ada sekarang</strong> dengan data dari backup yang dipilih.
            Pastikan kamu sudah membuat backup terbaru sebelum melakukan pemulihan.
            Proses ini tidak dapat dibatalkan.
        </p>
    </div>
</div>
@endsection