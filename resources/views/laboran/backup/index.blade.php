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
                <button class="btn btn-outline btn-block" type="button"
                    data-konfirm="Upload dan langsung pulihkan database dari file ini? SELURUH data saat ini akan digantikan."
                    data-konfirm-judul="Pulihkan dari Upload"
                    data-konfirm-ya="Ya, Pulihkan">
                    Upload &amp; Pulihkan
                </button>
            </form>
        </div>
    </div>

</div>

{{-- Daftar Backup --}}
<div class="card">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <div style="display:flex;align-items:center;gap:8px;">
            <span class="card-title">Daftar Backup Tersimpan</span>
            <span class="badge badge-primary">{{ $files->count() }} file</span>
        </div>
        @if($files->isNotEmpty())
        <div style="display:flex;gap:6px;align-items:center;">
            <a href="{{ route('laboran.backup.unduh-semua') }}"
                class="btn btn-sm btn-outline"
                style="display:inline-flex;align-items:center;gap:5px;text-decoration:none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Unduh Semua (.zip)
            </a>
            <button class="btn btn-sm btn-danger" data-modal-open="modalHapusSemuaBackup"
                style="display:inline-flex;align-items:center;gap:5px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round"
                    d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6M10 11v6M14 11v6M9 6V4h6v2"/>
                </svg>
                Hapus Semua
            </button>
        </div>
        @endif
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
                        <button class="btn btn-sm btn-primary" type="button"
                            data-konfirm="Pulihkan database dari {{ $f['nama'] }}? SELURUH data saat ini akan digantikan."
                            data-konfirm-judul="Pulihkan Backup"
                            data-konfirm-ya="Ya, Pulihkan">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.51"/></svg>
                            Pulihkan
                        </button>
                    </form>
                    {{-- Hapus --}}
                    <form method="POST" action="{{ route('laboran.backup.hapus', $f['nama']) }}">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" type="button"
                            data-konfirm="Hapus file backup {{ $f['nama'] }}?"
                            data-konfirm-judul="Hapus Backup">
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
{{-- Modal Hapus Semua Backup --}}
@if($files->isNotEmpty())
<div id="modalHapusSemuaBackup" class="modal-overlay"><div class="modal" style="max-width:440px;">
    <div class="modal-header" style="background:#FEF2F2;border-bottom:1px solid #FECACA;">
        <span class="modal-title" style="color:#B91C1C;">⚠ Hapus Semua Backup</span>
        <button data-modal-close="modalHapusSemuaBackup" class="modal-close">✕</button>
    </div>
    <div class="modal-body">
        <p style="font-size:14px;color:#374151;margin:0 0 12px;">
            Tindakan ini akan menghapus <strong>{{ $files->count() }} file backup</strong> dari server secara permanen.
        </p>
        <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:6px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#B91C1C;">
            <strong>Tindakan ini tidak dapat dibatalkan.</strong> Pastikan sudah mengunduh backup yang dibutuhkan sebelum melanjutkan.
        </div>
        <p style="font-size:13px;color:#374151;margin:0 0 8px;">Ketik <strong>HAPUS SEMUA</strong> untuk konfirmasi:</p>
        <input type="text" id="konfirmasiHapusBackup" class="form-control" placeholder="HAPUS SEMUA" autocomplete="off">
    </div>
    <div style="display:flex;gap:8px;justify-content:flex-end;padding:16px;">
        <button type="button" data-modal-close="modalHapusSemuaBackup" class="btn btn-outline">Batal</button>
        <form method="POST" action="{{ route('laboran.backup.hapus-semua') }}" id="formHapusSemuaBackup">
            @csrf @method('DELETE')
            <button type="submit" id="btnHapusSemuaBackup" class="btn btn-danger" disabled>Hapus Semua</button>
        </form>
    </div>
</div></div>
@push('scripts')
<script>
(function () {
    const input = document.getElementById('konfirmasiHapusBackup');
    const btn   = document.getElementById('btnHapusSemuaBackup');
    const form  = document.getElementById('formHapusSemuaBackup');
    if (!input) return;
    input.addEventListener('input', () => {
        btn.disabled = input.value.trim() !== 'HAPUS SEMUA';
    });
    form.addEventListener('submit', function (e) {
        if (input.value.trim() !== 'HAPUS SEMUA') { e.preventDefault(); return; }
        btn.disabled = true; btn.textContent = 'Menghapus…';
    });
})();
</script>
@endpush
@endif

@endsection