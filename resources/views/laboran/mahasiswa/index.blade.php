@extends('layouts.app')
@section('title','Mahasiswa')
@section('page-title','Manajemen Mahasiswa')
@section('content')
<div class="page-toolbar" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Mahasiswa</button>
    <button class="btn btn-outline" data-modal-open="modalImportExcel"
        style="display:inline-flex;align-items:center;gap:6px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M8 12l4 4m0 0l4-4m-4 4V4"/>
        </svg>
        Import Excel
    </button>
    <a href="{{ route('laboran.mahasiswa.template-excel') }}"
        class="btn btn-outline"
        style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;" download>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
        </svg>
        Template Excel
    </a>
    <button class="btn btn-danger" data-modal-open="modalHapusSemuaMahasiswa"
        style="display:inline-flex;align-items:center;gap:6px;margin-left:auto;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <polyline points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round"
            d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6M10 11v6M14 11v6M9 6V4h6v2"/>
        </svg>
        Hapus Semua
    </button>
</div>

{{-- Modal Hapus Semua Mahasiswa --}}
<div id="modalHapusSemuaMahasiswa" class="modal-overlay"><div class="modal" style="max-width:440px;">
    <div class="modal-header" style="background:#FEF2F2;border-bottom:1px solid #FECACA;">
        <span class="modal-title" style="color:#B91C1C;">⚠ Hapus Semua Mahasiswa</span>
        <button data-modal-close="modalHapusSemuaMahasiswa" class="modal-close">✕</button>
    </div>
    <div class="modal-body">
        <p style="font-size:14px;color:#374151;margin:0 0 12px;">Tindakan ini akan menghapus <strong>seluruh data mahasiswa</strong> beserta semua data terkait:</p>
        <ul style="font-size:13px;color:#6B7280;margin:0 0 16px;padding-left:20px;line-height:1.8;">
            <li>Semua presensi</li>
            <li>Semua nilai asistensi, ujian & evaluasi</li>
            <li>Semua rekap detail nilai</li>
            <li>Relasi mahasiswa dengan kelas</li>
        </ul>
        <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:6px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#B91C1C;">
            <strong>Tindakan ini tidak dapat dibatalkan.</strong> Data kelas, dosen, dan asisten tidak ikut terhapus.
        </div>
        <p style="font-size:13px;color:#374151;margin:0 0 8px;">Ketik <strong>HAPUS SEMUA</strong> untuk konfirmasi:</p>
        <input type="text" id="konfirmasiHapusMahasiswa" class="form-control" placeholder="HAPUS SEMUA" autocomplete="off">
    </div>
    <div style="display:flex;gap:8px;justify-content:flex-end;padding:16px;">
        <button type="button" data-modal-close="modalHapusSemuaMahasiswa" class="btn btn-outline">Batal</button>
        <form method="POST" action="{{ route('laboran.mahasiswa.hapus-semua') }}" id="formHapusSemuaMahasiswa">
            @csrf @method('DELETE')
            <button type="submit" id="btnHapusSemuaMahasiswa" class="btn btn-danger" disabled>Hapus Semua</button>
        </form>
    </div>
</div></div>
@push('scripts')
<script>
(function () {
    const input = document.getElementById('konfirmasiHapusMahasiswa');
    const btn   = document.getElementById('btnHapusSemuaMahasiswa');
    const form  = document.getElementById('formHapusSemuaMahasiswa');
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
@if(session('import_errors'))
<div class="card" style="border-left:4px solid #F59E0B;margin-bottom:12px;">
    <div style="padding:12px 16px;">
        <strong style="color:#92400E;">⚠ Beberapa baris dilewati:</strong>
        <ul style="margin:8px 0 0 16px;padding:0;font-size:13px;color:#78350F;">
            @foreach(session('import_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif
<div class="card">
    <div class="table-toolbar">
        <div class="table-search-wrap">
            <i class="ti ti-search" aria-hidden="true"></i>
            <input type="text" id="searchMahasiswa"
                   value="{{ $q }}"
                   class="table-search" placeholder="Cari NIM atau nama mahasiswa..."
                   autocomplete="off">
        </div>
        <a href="{{ route('laboran.mahasiswa', array_merge(request()->only('q','sort','dir'), ['error' => $filterError ? null : '1'])) }}"
           style="flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:7px 12px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;white-space:nowrap;transition:all .15s;width:175px;box-sizing:border-box;
                  {{ $filterError ? 'background:#c53030;color:#fff;border:1.5px solid #c53030;' : 'background:#fff5f5;color:#c53030;border:1.5px solid #fc8181;' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r="0.5" fill="currentColor" stroke="none"/></svg>
            {{ $filterError ? 'Semua Mahasiswa' : 'Mahasiswa Error' }}
        </a>
        <span class="table-count" style="flex-shrink:0;">
            {{ $mahasiswaAll->total() }} mahasiswa
        </span>
    </div>
    <script>
    (function () {
        const input  = document.getElementById('searchMahasiswa');
        const base   = '{{ route('laboran.mahasiswa') }}';
        let timer;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                const q = input.value.trim();
                window.location.href = q ? base + '?q=' + encodeURIComponent(q) : base;
            }, 400);
        });
        // Fokus di akhir teks saat halaman load (ada query aktif)
        input.setSelectionRange(input.value.length, input.value.length);
        input.focus();
    })();
    </script>
    <div class="table-wrapper"><table class="table">
    @php
        $sortUrl = fn(string $kolom) => route('laboran.mahasiswa', array_filter([
            'q'    => $q ?: null,
            'sort' => $kolom,
            'dir'  => ($sort === $kolom && $dir === 'asc') ? 'desc' : 'asc',
        ]));
        // Aktif hanya kalau $sort benar-benar dipilih user (bukan default null)
        $isAktif = fn(string $kolom) => $sort === $kolom;
    @endphp
    <thead><tr>
        <th data-col="nim"
            class="{{ $isAktif('nim_mahasiswa') ? ($dir === 'asc' ? 'sort-asc' : 'sort-desc') : '' }}"
            style="cursor:pointer;user-select:none;white-space:nowrap;"
            onclick="window.location='{{ $sortUrl('nim_mahasiswa') }}'">
            NIM <span class="sort-icon" aria-hidden="true">{{ $isAktif('nim_mahasiswa') ? ($dir === 'asc' ? '↑' : '↓') : '⇅' }}</span>
        </th>
        <th data-col="nama"
            class="{{ $isAktif('nama_mahasiswa') ? ($dir === 'asc' ? 'sort-asc' : 'sort-desc') : '' }}"
            style="cursor:pointer;user-select:none;white-space:nowrap;"
            onclick="window.location='{{ $sortUrl('nama_mahasiswa') }}'">
            Nama Mahasiswa <span class="sort-icon" aria-hidden="true">{{ $isAktif('nama_mahasiswa') ? ($dir === 'asc' ? '↑' : '↓') : '⇅' }}</span>
        </th>
        <th>Kelas yang Diikuti</th>
        <th>Aksi</th>
    </tr></thead>
    <tbody>
    @forelse($mahasiswaAll as $m)
    @php $adaAlpa = $m->praktikum->contains(fn($p) => $m->melebihiBatasAlpaDiKelas($p->id)); @endphp
    <tr class="{{ $adaAlpa ? 'row-alpa-alert' : '' }}">
        <td style="font-family:monospace;font-size:13px;">{{ $m->nim_mahasiswa }}</td>
        <td>
            <div style="display:flex;align-items:center;gap:8px;">
                <div class="avatar avatar-sm">{{ $m->initials }}</div>
                <span class="fw-600">{{ $m->nama_mahasiswa }}</span>
                @if($adaAlpa)
                    <span class="badge-alpa-alert" title="Ada kelas dengan alpa ≥ {{ \App\Models\Mahasiswa::BATAS_ALPA }}">⚠ Alpa</span>
                @endif
            </div>
        </td>
        <td>
            {{-- Multi-badge: tampilkan semua kelas --}}
            @forelse($m->praktikum as $p)
                <span class="badge badge-primary" style="margin:2px 2px 2px 0;">
                    {{ $p->mataKuliah?->kode_mk }} — {{ $p->nama_kelas }}
                </span>
            @empty
                <span style="color:var(--text-muted);font-size:12px;">Belum ada kelas</span>
            @endforelse
        </td>
        <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                {{-- Tombol Nilai & Absensi per kelas --}}
                @forelse($m->praktikum as $p)
                    <a href="{{ route('laboran.mahasiswa.nilai', ['mahasiswa' => $m->id, 'praktikum' => $p->id]) }}"
                    class="btn btn-sm btn-primary">
                    {{ $p->nama_kelas }}
                    </a>
                @empty
                    <span class="btn btn-sm btn-outline" style="opacity:.5;cursor:not-allowed;" 
                        title="Tambahkan ke kelas dulu lewat menu Kelas Praktikum">
                        Nilai & Absensi
                    </span>
                @endforelse
                <button type="button" class="btn btn-sm btn-outline" data-modal-open="modalEditMhs{{ $m->id }}">Edit</button>
                <form method="POST" action="{{ route('laboran.mahasiswa.destroy', $m) }}" style="margin:0;">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" type="button" data-konfirm="Hapus mahasiswa ini?" data-konfirm-judul="Hapus Mahasiswa">Hapus</button>
                </form>
            </div>
        </td>
    </tr>
    </tr>
    @empty<tr><td colspan="5"><div class="empty-state"><p>Belum ada mahasiswa.</p></div></td></tr>
    @endforelse
    </tbody>
</table></div>
@if($mahasiswaAll->hasPages())
    <div class="card-footer">
        {{ $mahasiswaAll->appends(array_filter(['q' => $q, 'sort' => $sort !== 'nama_mahasiswa' ? $sort : null, 'dir' => $dir !== 'asc' ? $dir : null]))->links() }}
    </div>
@endif
</div>
<div id="modalTambah" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Mahasiswa</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.mahasiswa.store') }}">@csrf
    <div class="form-group">
        <label class="form-label required">NIM</label>
        <input name="nim_mahasiswa" data-nim-input
            class="form-control {{ $errors->has('nim_mahasiswa') && old('_form') === 'tambah' ? 'is-invalid' : '' }}"
            placeholder="angka saja"
            pattern="\d+" inputmode="numeric"
            value="{{ old('_form') === 'tambah' ? old('nim_mahasiswa') : '' }}" required>
        @if(old('_form') === 'tambah') @error('nim_mahasiswa')<div class="form-error">{{ $message }}</div>@enderror @endif
    </div>
    <div class="form-group">
        <label class="form-label required">Nama</label>
        <input name="nama_mahasiswa" class="form-control {{ $errors->has('nama_mahasiswa') && old('_form') === 'tambah' ? 'is-invalid' : '' }}"
            value="{{ old('_form') === 'tambah' ? old('nama_mahasiswa') : '' }}" required>
        @if(old('_form') === 'tambah') @error('nama_mahasiswa')<div class="form-error">{{ $message }}</div>@enderror @endif
    </div>
    <p style="font-size:12px;color:var(--text-muted);margin:-4px 0 12px;">Kelas belum perlu dipilih sekarang — bisa ditentukan nanti lewat menu <strong>Kelas Praktikum → Edit</strong>.</p>
    <input type="hidden" name="_form" value="tambah">
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button><button class="btn btn-primary">Tambah</button></div>
    </form></div>
</div></div>
{{-- Modal Import Excel --}}
<div id="modalImportExcel" class="modal-overlay"><div class="modal" style="max-width:480px;">
    <div class="modal-header">
        <span class="modal-title">Import Mahasiswa via Excel</span>
        <button data-modal-close="modalImportExcel" class="modal-close">✕</button>
    </div>
    <div class="modal-body">
        @if($errors->has('file_excel'))
            <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:6px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#B91C1C;">
                {{ $errors->first('file_excel') }}
            </div>
        @endif

        <form method="POST" action="{{ route('laboran.mahasiswa.import') }}"
              enctype="multipart/form-data" id="formImportExcel">
            @csrf

            <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#1E40AF;line-height:1.6;">
                <strong>Cara penggunaan:</strong><br>
                1. Unduh template (tombol "Template Excel" di atas)<br>
                2. Isi kolom <strong>NIM</strong> dan <strong>Nama Mahasiswa</strong><br>
                3. Upload file di sini → klik <strong>Import</strong>
            </div>

            <div class="form-group">
                <label class="form-label required">File Excel (.xlsx / .xls)</label>
                <div id="dropZone" style="border:2px dashed #93C5FD;border-radius:8px;padding:24px 16px;text-align:center;cursor:pointer;background:#F8FAFC;transition:border-color .2s,background .2s;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none"
                        viewBox="0 0 24 24" stroke="#60A5FA" stroke-width="1.5" style="margin-bottom:8px;display:block;margin-left:auto;margin-right:auto;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-2m3 2v-4m3 4v-6M3 7l4-4m0 0l4 4M7 3v14M17 3h2a2 2 0 012 2v14a2 2 0 01-2 2h-2"/>
                    </svg>
                    <p id="dropLabel" style="margin:0;font-size:13px;color:#6B7280;">Klik atau seret file Excel ke sini</p>
                    <input type="file" name="file_excel" id="fileExcelInput"
                        accept=".xlsx,.xls" style="position:absolute;opacity:0;width:0;height:0;" required>
                </div>
            </div>

            <p style="font-size:12px;color:var(--text-muted);margin:-8px 0 16px;">
                Maks. 5 MB · Duplikat NIM akan dilewati otomatis.
            </p>

            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" data-modal-close="modalImportExcel" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnImport">Import</button>
            </div>
        </form>
    </div>
</div></div>

@push('scripts')
<script>
(function () {
    const zone   = document.getElementById('dropZone');
    const input  = document.getElementById('fileExcelInput');
    const label  = document.getElementById('dropLabel');
    const btnImp = document.getElementById('btnImport');
    const form   = document.getElementById('formImportExcel');
    if (!zone) return;

    zone.addEventListener('click', () => input.click());

    zone.addEventListener('dragover', e => {
        e.preventDefault();
        zone.style.borderColor = '#2563EB';
        zone.style.background  = '#EFF6FF';
    });
    zone.addEventListener('dragleave', () => {
        zone.style.borderColor = '#93C5FD';
        zone.style.background  = '#F8FAFC';
    });
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.style.borderColor = '#93C5FD';
        zone.style.background  = '#F8FAFC';
        const file = e.dataTransfer.files[0];
        if (file) setFile(file);
    });
    input.addEventListener('change', () => {
        if (input.files[0]) setFile(input.files[0]);
    });

    function setFile(file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        label.innerHTML = '<strong style="color:#1D4ED8;">✓ ' + file.name + '</strong>'
            + '<br><span style="font-size:11px;color:#6B7280;">' + (file.size/1024).toFixed(1) + ' KB</span>';
        zone.style.borderColor = '#34D399';
        zone.style.background  = '#F0FDF4';
    }

    form.addEventListener('submit', function () {
        btnImp.disabled    = true;
        btnImp.textContent = 'Mengimport…';
    });

    @if($errors->has('file_excel'))
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('modalImportExcel')?.classList.add('open');
        document.body.style.overflow = 'hidden';
    });
    @endif
})();
</script>
@endpush
@foreach($mahasiswaAll as $m)
<div id="modalEditMhs{{ $m->id }}" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Edit Data — {{ $m->nama_mahasiswa }}</span><button data-modal-close="modalEditMhs{{ $m->id }}" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.mahasiswa.update',$m) }}">@csrf @method('PATCH')
    <div class="form-group">
        <label class="form-label required">NIM</label>
        <input name="nim_mahasiswa" data-nim-input
            class="form-control {{ $errors->has('nim_mahasiswa') && old('_form') === 'edit-mhs-'.$m->id ? 'is-invalid' : '' }}"
            placeholder="angka saja"
            pattern="\d+" inputmode="numeric"
            value="{{ old('_form') === 'edit-mhs-'.$m->id ? old('nim_mahasiswa') : $m->nim_mahasiswa }}" required>
        @if(old('_form') === 'edit-mhs-'.$m->id) @error('nim_mahasiswa')<div class="form-error">{{ $message }}</div>@enderror @endif
    </div>
    <div class="form-group">
        <label class="form-label required">Nama</label>
        <input name="nama_mahasiswa" class="form-control {{ $errors->has('nama_mahasiswa') && old('_form') === 'edit-mhs-'.$m->id ? 'is-invalid' : '' }}"
            value="{{ old('_form') === 'edit-mhs-'.$m->id ? old('nama_mahasiswa') : $m->nama_mahasiswa }}" required>
        @if(old('_form') === 'edit-mhs-'.$m->id) @error('nama_mahasiswa')<div class="form-error">{{ $message }}</div>@enderror @endif
    </div>
    <input type="hidden" name="_form" value="edit-mhs-{{ $m->id }}">
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalEditMhs{{ $m->id }}" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div></div>
@endforeach
@if($errors->any() && old('_form'))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = '{{ old("_form") }}';
    let modalId = null;
    if (form === 'tambah') modalId = 'modalTambah';
    else if (form.startsWith('edit-mhs-')) modalId = 'modalEditMhs' + form.replace('edit-mhs-', '');
    if (modalId) {
        document.getElementById(modalId)?.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
});
</script>
@endpush
@endif
@endsection
