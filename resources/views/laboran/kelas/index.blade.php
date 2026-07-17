@extends('layouts.app')
@section('title','Kelas Praktikum')
@section('page-title','Kelas Praktikum')
@section('content')
<div class="page-toolbar" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Kelas</button>
    <button class="btn btn-outline" data-modal-open="modalImportKelas"
        style="display:inline-flex;align-items:center;gap:6px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M8 12l4 4m0 0l4-4m-4 4V4"/>
        </svg>
        Import Excel
    </button>
    <a href="{{ route('laboran.kelas.template-excel') }}"
        class="btn btn-outline"
        style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;" download>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
        </svg>
        Template Excel
    </a>
</div>
@if(session('import_errors_kelas'))
<div class="card" style="border-left:4px solid #F59E0B;margin-bottom:12px;">
    <div style="padding:12px 16px;">
        <strong style="color:#92400E;">⚠ Beberapa baris dilewati:</strong>
        <ul style="margin:8px 0 0 16px;padding:0;font-size:13px;color:#78350F;">
            @foreach(session('import_errors_kelas') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif
@if(session('error_tabrakan'))
<div class="alert alert-error" style="margin-bottom:16px;">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="13"/>
        <circle cx="12" cy="16.5" r="0.5" fill="currentColor" stroke="none"/>
    </svg>
    <span>{!! session('error_tabrakan') !!}</span>
</div>
@endif
<div class="card">
    <div class="table-toolbar">
        <div class="table-search-wrap">
            <i class="ti ti-search" aria-hidden="true"></i>
            <input type="text" id="searchKelas"
                   value="{{ $q }}"
                   class="table-search" placeholder="Cari kelas, mata kuliah, dosen..."
                   autocomplete="off">
        </div>
        <span class="table-count" style="white-space:nowrap;font-size:13px;color:var(--text-muted);">
            {{ $kelasAll->total() }} kelas
        </span>
    </div>
    <script>
    (function () {
        const input = document.getElementById('searchKelas');
        const base  = '{{ route('laboran.kelas') }}';
        let timer;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                const q = input.value.trim();
                window.location.href = q ? base + '?q=' + encodeURIComponent(q) : base;
            }, 400);
        });
        input.setSelectionRange(input.value.length, input.value.length);
        input.focus();
    })();
    </script>
    <div class="table-wrapper"><table class="table">
    <thead><tr>
        <th>Kelas</th>
        <th>Mata Kuliah</th>
        <th>Jadwal</th>
        <th>Ruangan</th>
        <th>Dosen</th>
        <th>Asisten 1</th>
        <th>Asisten 2</th>
        <th>Aksi</th>
    </tr></thead>
    <tbody>
    @forelse($kelasAll as $k)
    <tr>
        <td class="fw-600">{{ $k->nama_kelas }}</td>
        <td>{{ $k->mataKuliah?->nama_mk }}</td>
        <td class="fs-12">{{ $k->jadwal ?? '—' }}</td>
        <td class="fs-12">{{ $k->ruangan?->nama_ruangan ?? '—' }}</td>
        <td class="fs-12">{{ $k->dosen?->nama_dosen ?? '—' }}</td>
        <td class="fs-12">{{ $k->asisten?->nama_asisten ?? '—' }}</td>
        <td class="fs-12">{{ $k->asisten2?->nama_asisten ?? '—' }}</td>
        <td>
            <div style="display:flex;gap:6px;">
            <a href="{{ route('laboran.kelas.show',$k) }}" class="btn btn-sm btn-outline">Edit</a>
            <form method="POST" action="{{ route('laboran.kelas.destroy',$k) }}">@csrf @method('DELETE')
            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus kelas ini?')">Hapus</button></form>
            </div>
        </td>
    </tr>
    @empty<tr><td colspan="8"><div class="empty-state"><p>Belum ada kelas praktikum.</p></div></td></tr>
    @endforelse
    </tbody>
</table></div>
@if($kelasAll->hasPages())
    <div class="card-footer">
        {{ $kelasAll->appends(array_filter(['q' => $q]))->links() }}
    </div>
@endif
</div>
<div id="modalTambah" class="modal-overlay"><div class="modal" style="max-width:680px;">
    <div class="modal-header"><span class="modal-title">Tambah Kelas Praktikum</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.kelas.store') }}">@csrf
    <div class="grid grid-2">

        {{-- Mata Kuliah --}}
        <div class="form-group">
            <label class="form-label required">Mata Kuliah</label>
            <div class="search-combobox">
                <input type="text" id="cariTMK" class="form-control" placeholder="Klik untuk pilih atau ketik untuk cari..." autocomplete="off">
                <input type="hidden" name="mata_kuliah_id" id="hidTMK">
                <div class="search-results" id="previewTMK"></div>
            </div>
        </div>

        {{-- Nama Kelas --}}
        <div class="form-group">
            <label class="form-label required">Nama Kelas</label>
            <input name="nama_kelas" class="form-control" required placeholder="cth: A1">
        </div>

        {{-- Hari --}}
        <div class="form-group">
            <label class="form-label">Hari</label>
            <div class="search-combobox">
                <input type="text" id="cariTHari" class="form-control" placeholder="Klik untuk pilih hari..." autocomplete="off">
                <input type="hidden" name="hari" id="hidTHari">
                <div class="search-results" id="previewTHari"></div>
            </div>
        </div>

        {{-- Jam Mulai --}}
        <div class="form-group">
            <label class="form-label">Jam Mulai</label>
            <div class="search-combobox">
                <input type="text" id="cariTJamMulai" class="form-control" placeholder="Klik untuk pilih jam mulai..." autocomplete="off">
                <input type="hidden" name="jam_mulai" id="hidTJamMulai">
                <div class="search-results" id="previewTJamMulai"></div>
            </div>
        </div>

        {{-- Jam Selesai --}}
        <div class="form-group">
            <label class="form-label">Jam Selesai</label>
            <div class="search-combobox">
                <input type="text" id="cariTJamSelesai" class="form-control" placeholder="Klik untuk pilih jam selesai..." autocomplete="off">
                <input type="hidden" name="jam_selesai" id="hidTJamSelesai">
                <div class="search-results" id="previewTJamSelesai"></div>
            </div>
        </div>

        {{-- Ruangan --}}
        <div class="form-group">
            <label class="form-label">Ruangan</label>
            <div class="search-combobox">
                <input type="text" id="cariTRuangan" class="form-control" placeholder="Klik untuk pilih atau ketik untuk cari..." autocomplete="off">
                <input type="hidden" name="ruangan_id" id="hidTRuangan">
                <div class="search-results" id="previewTRuangan"></div>
            </div>
        </div>

        {{-- Dosen --}}
        <div class="form-group">
            <label class="form-label">Dosen</label>
            <div class="search-combobox">
                <input type="text" id="cariTDosen" class="form-control" placeholder="Klik untuk pilih atau ketik untuk cari..." autocomplete="off">
                <input type="hidden" name="dosen_id" id="hidTDosen">
                <div class="search-results" id="previewTDosen"></div>
            </div>
        </div>

        {{-- Asisten 1 --}}
        <div class="form-group">
            <label class="form-label">Asisten 1</label>
            <div class="search-combobox">
                <input type="text" id="cariTA1" class="form-control" placeholder="Klik untuk pilih atau ketik untuk cari..." autocomplete="off">
                <input type="hidden" name="asisten_id" id="hidTA1">
                <div class="search-results" id="previewTA1"></div>
            </div>
        </div>

        {{-- Asisten 2 --}}
        <div class="form-group">
            <label class="form-label">Asisten 2</label>
            <div class="search-combobox">
                <input type="text" id="cariTA2" class="form-control" placeholder="Klik untuk pilih atau ketik untuk cari..." autocomplete="off">
                <input type="hidden" name="asisten2_id" id="hidTA2">
                <div class="search-results" id="previewTA2"></div>
            </div>
        </div>

    </div>
    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;">
        <button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button>
        <button class="btn btn-primary">Simpan</button>
    </div>
    </form>

    {{-- Data store tersembunyi — hanya dibaca JS, tidak dikirim ke server --}}
    <select id="__dataTMK" hidden>
        @foreach($mataKuliah as $mk)
        <option value="{{ $mk->id }}"
            data-cari="{{ strtolower($mk->kode_mk) }} {{ strtolower($mk->nama_mk) }}"
            data-label="{{ $mk->kode_mk }} — {{ $mk->nama_mk }}"></option>
        @endforeach
    </select>
    <select id="__dataTHari" hidden>
        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
        <option value="{{ $h }}" data-cari="{{ strtolower($h) }}" data-label="{{ $h }}"></option>
        @endforeach
    </select>
    <select id="__dataTJamMulai" hidden>
        @foreach(['07:00','08:00','09:00','09:40','10:00','10:30','11:00','13:00','14:00','14:30','15:00','15:40','16:00'] as $j)
        <option value="{{ $j }}" data-cari="{{ $j }}" data-label="{{ $j }}"></option>
        @endforeach
    </select>
    <select id="__dataTJamSelesai" hidden>
        @foreach(['08:40','09:30','10:20','11:20','12:00','12:10','14:20','15:00','15:20','15:30','16:20','17:00','18:10','18:20'] as $j)
        <option value="{{ $j }}" data-cari="{{ $j }}" data-label="{{ $j }}"></option>
        @endforeach
    </select>
    <select id="__dataTRuangan" hidden>
        @foreach($ruanganAll as $r)
        <option value="{{ $r->id }}"
            data-cari="{{ strtolower($r->nama_ruangan) }}"
            data-label="{{ $r->nama_ruangan }}"></option>
        @endforeach
    </select>
    <select id="__dataTDosen" hidden>
        @foreach($dosenAll as $d)
        <option value="{{ $d->id }}"
            data-cari="{{ $d->nidn }} {{ strtolower($d->nama_dosen) }}"
            data-label="{{ $d->nidn }} — {{ $d->nama_dosen }}"></option>
        @endforeach
    </select>
    <select id="__dataTAsisten" hidden>
        @foreach($asistenAll as $a)
        <option value="{{ $a->id }}"
            data-cari="{{ $a->nim }} {{ strtolower($a->nama_asisten) }}"
            data-label="{{ $a->nim }} — {{ $a->nama_asisten }}"></option>
        @endforeach
    </select>
    </div>
</div></div>
{{-- Modal Import Excel Kelas --}}
<div id="modalImportKelas" class="modal-overlay"><div class="modal" style="max-width:480px;">
    <div class="modal-header">
        <span class="modal-title">Import Kelas Praktikum via Excel</span>
        <button data-modal-close="modalImportKelas" class="modal-close">✕</button>
    </div>
    <div class="modal-body">
        @if($errors->has('file_excel'))
            <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:6px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#B91C1C;">
                {{ $errors->first('file_excel') }}
            </div>
        @endif

        <form method="POST" action="{{ route('laboran.kelas.import') }}"
              enctype="multipart/form-data" id="formImportKelas">
            @csrf

            <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#1E40AF;line-height:1.6;">
                <strong>Format kolom Excel:</strong><br>
                <strong>A</strong>: Kode MK (harus sudah ada di sistem)<br>
                <strong>B</strong>: Nama Kelas (cth: A1, B2)<br>
                <strong>C</strong>: Hari <em>(opsional)</em> — Senin/Selasa/dst<br>
                <strong>D</strong>: Jam Mulai <em>(opsional)</em> — cth: 08:00<br>
                <strong>E</strong>: Jam Selesai <em>(opsional)</em> — cth: 09:40<br>
                <br>
                Duplikat (Kode MK + Nama Kelas sama) dilewati otomatis.
            </div>

            <div class="form-group">
                <label class="form-label required">File Excel (.xlsx / .xls)</label>
                <div id="dropZoneKelas" style="border:2px dashed #93C5FD;border-radius:8px;padding:24px 16px;text-align:center;cursor:pointer;background:#F8FAFC;transition:border-color .2s,background .2s;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none"
                        viewBox="0 0 24 24" stroke="#60A5FA" stroke-width="1.5" style="margin-bottom:8px;display:block;margin-left:auto;margin-right:auto;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-2m3 2v-4m3 4v-6M3 7l4-4m0 0l4 4M7 3v14M17 3h2a2 2 0 012 2v14a2 2 0 01-2 2h-2"/>
                    </svg>
                    <p id="dropLabelKelas" style="margin:0;font-size:13px;color:#6B7280;">Klik atau seret file Excel ke sini</p>
                    <input type="file" name="file_excel" id="fileExcelKelas"
                        accept=".xlsx,.xls" style="position:absolute;opacity:0;width:0;height:0;" required>
                </div>
            </div>

            <p style="font-size:12px;color:var(--text-muted);margin:-8px 0 16px;">
                Maks. 5 MB · Gunakan tombol "Template Excel" untuk format yang benar.
            </p>

            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" data-modal-close="modalImportKelas" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnImportKelas">Import</button>
            </div>
        </form>
    </div>
</div></div>

@push('scripts')
<script>
(function () {
    const zone   = document.getElementById('dropZoneKelas');
    const input  = document.getElementById('fileExcelKelas');
    const label  = document.getElementById('dropLabelKelas');
    const btn    = document.getElementById('btnImportKelas');
    const form   = document.getElementById('formImportKelas');
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
        btn.disabled    = true;
        btn.textContent = 'Mengimport…';
    });

    @if($errors->has('file_excel'))
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('modalImportKelas')?.classList.add('open');
        document.body.style.overflow = 'hidden';
    });
    @endif
})();
</script>
@endpush
@if(session('error_tabrakan'))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalTambah');
    if (modal) {
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';

        // Restore nilai hidden input yang sudah dipilih sebelumnya
        const fields = [
            { hid: 'hidTMK',       vis: 'cariTMK',       store: '__dataTMK',       key: 'mata_kuliah_id' },
            { hid: 'hidTHari',     vis: 'cariTHari',     store: '__dataTHari',     key: 'hari' },
            { hid: 'hidTJamMulai', vis: 'cariTJamMulai', store: '__dataTJamMulai', key: 'jam_mulai' },
            { hid: 'hidTJamSelesai',vis:'cariTJamSelesai',store: '__dataTJamSelesai',key:'jam_selesai'},
            { hid: 'hidTRuangan',  vis: 'cariTRuangan',  store: '__dataTRuangan',  key: 'ruangan_id' },
            { hid: 'hidTDosen',    vis: 'cariTDosen',    store: '__dataTDosen',    key: 'dosen_id' },
            { hid: 'hidTA1',       vis: 'cariTA1',       store: '__dataTAsisten',  key: 'asisten_id' },
            { hid: 'hidTA2',       vis: 'cariTA2',       store: '__dataTAsisten',  key: 'asisten2_id' },
        ];
        const old = @json(session()->getOldInput() ?? []);
        fields.forEach(function (f) {
            const val = old[f.key];
            if (!val) return;
            const hidEl = document.getElementById(f.hid);
            const visEl = document.getElementById(f.vis);
            const opt   = document.querySelector('#' + f.store + ' option[value="' + val + '"]');
            if (hidEl) hidEl.value = val;
            if (visEl && opt) visEl.value = opt.dataset.label;
        });

        // Restore nama_kelas (input teks biasa)
        const namaKelasEl = document.querySelector('#modalTambah input[name="nama_kelas"]');
        if (namaKelasEl && old['nama_kelas']) namaKelasEl.value = old['nama_kelas'];
    }
});
</script>
@endpush
@endif
@endsection
