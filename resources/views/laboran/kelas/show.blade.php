@extends('layouts.app')
@section('title','Detail Kelas')
@section('page-title', $kelas->nama_kelas)
@section('page-subtitle') {{ $kelas->mataKuliah?->nama_mk }} @endsection
@section('content')
<div class="page-toolbar"><a href="{{ route('laboran.kelas') }}" class="btn btn-outline">← Kembali ke Kelas Praktikum</a></div>

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

<div class="grid grid-2" style="gap:16px;align-items:start;">
    {{-- ── KOLOM KIRI: Form Jadwal, Dosen & Asisten ── --}}
    <div class="card">
        <div class="card-header"><span class="card-title">Dosen & Asisten Kelas</span></div>
        <div class="card-body">
            <p style="font-size:12px;color:var(--text-muted);margin:0 0 14px;">
                Tambahkan Asisten 2, ganti asisten yang bertugas, atau kosongkan kembali — cukup pilih lalu simpan.
            </p>
            <form method="POST" action="{{ route('laboran.kelas.update',$kelas) }}">
                @csrf @method('PATCH')
                <div class="form-group">
                    <label class="form-label required">Hari</label>
                    <div class="search-combobox">
                        <input type="text" id="cariHari" class="form-control {{ $errors->has('hari') ? 'is-invalid' : '' }}"
                            placeholder="Klik untuk pilih hari..." autocomplete="off"
                            value="{{ old('hari', $kelas->hari ?? '') }}">
                        <input type="hidden" name="hari" id="hidHari" value="{{ old('hari', $kelas->hari ?? '') }}">
                        <div class="search-results" id="previewHari"></div>
                    </div>
                    @error('hari')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label required">Jam Mulai</label>
                    <div class="search-combobox">
                        <input type="text" id="cariJamMulai" class="form-control {{ $errors->has('jam_mulai') ? 'is-invalid' : '' }}"
                            placeholder="Klik untuk pilih jam mulai..." autocomplete="off"
                            value="{{ old('jam_mulai', $kelas->jam_mulai ?? '') }}">
                        <input type="hidden" name="jam_mulai" id="hidJamMulai" value="{{ old('jam_mulai', $kelas->jam_mulai ?? '') }}">
                        <div class="search-results" id="previewJamMulai"></div>
                    </div>
                    @error('jam_mulai')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label required">Jam Selesai</label>
                    <div class="search-combobox">
                        <input type="text" id="cariJamSelesai" class="form-control {{ $errors->has('jam_selesai') ? 'is-invalid' : '' }}"
                            placeholder="Klik untuk pilih jam selesai..." autocomplete="off"
                            value="{{ old('jam_selesai', $kelas->jam_selesai ?? '') }}">
                        <input type="hidden" name="jam_selesai" id="hidJamSelesai" value="{{ old('jam_selesai', $kelas->jam_selesai ?? '') }}">
                        <div class="search-results" id="previewJamSelesai"></div>
                    </div>
                    @error('jam_selesai')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label required">Ruangan</label>
                    <div class="search-combobox">
                        <input type="text" id="cariRuangan" class="form-control {{ $errors->has('ruangan_id') ? 'is-invalid' : '' }}"
                            placeholder="Klik untuk pilih atau ketik untuk cari..." autocomplete="off"
                            value="{{ old('ruangan_id') ? $ruanganAll->find(old('ruangan_id'))?->nama_ruangan : ($kelas->ruangan?->nama_ruangan ?? '') }}">
                        <input type="hidden" name="ruangan_id" id="hidRuangan" value="{{ old('ruangan_id', $kelas->ruangan_id ?? '') }}">
                        <div class="search-results" id="previewRuangan"></div>
                    </div>
                    @error('ruangan_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label required">Dosen</label>
                    <div class="search-combobox">
                        <input type="text" id="cariDosen" class="form-control {{ $errors->has('dosen_id') ? 'is-invalid' : '' }}"
                            placeholder="Klik untuk pilih atau ketik untuk cari..." autocomplete="off"
                            value="{{ old('dosen_id') ? ($dosenAll->find(old('dosen_id')) ? $dosenAll->find(old('dosen_id'))->nidn . ' — ' . $dosenAll->find(old('dosen_id'))->nama_dosen : '') : ($kelas->dosen ? $kelas->dosen->nidn . ' — ' . $kelas->dosen->nama_dosen : '') }}">
                        <input type="hidden" name="dosen_id" id="hidDosen" value="{{ old('dosen_id', $kelas->dosen_id ?? '') }}">
                        <div class="search-results" id="previewDosen"></div>
                    </div>
                    @error('dosen_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label required">Asisten 1</label>
                    <div class="search-combobox">
                        <input type="text" id="cariA1" class="form-control {{ $errors->has('asisten_id') ? 'is-invalid' : '' }}"
                            placeholder="Klik untuk pilih atau ketik untuk cari..." autocomplete="off"
                            value="{{ old('asisten_id') ? ($asistenAll->find(old('asisten_id')) ? $asistenAll->find(old('asisten_id'))->nim . ' — ' . $asistenAll->find(old('asisten_id'))->nama_asisten : '') : ($kelas->asisten ? $kelas->asisten->nim . ' — ' . $kelas->asisten->nama_asisten : '') }}">
                        <input type="hidden" name="asisten_id" id="hidA1" value="{{ old('asisten_id', $kelas->asisten_id ?? '') }}">
                        <div class="search-results" id="previewA1"></div>
                    </div>
                    @error('asisten_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Asisten 2</label>
                    <div class="search-combobox">
                        <input type="text" id="cariA2" class="form-control"
                            placeholder="Klik untuk pilih atau ketik untuk cari..." autocomplete="off"
                            value="{{ $kelas->asisten2 ? $kelas->asisten2->nim . ' — ' . $kelas->asisten2->nama_asisten : '' }}">
                        <input type="hidden" name="asisten2_id" id="hidA2" value="{{ $kelas->asisten2_id ?? '' }}">
                        <div class="search-results" id="previewA2"></div>
                    </div>
                </div>
                <button class="btn btn-primary btn-block">Simpan</button>

                <select id="__dataHari" hidden>
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                    <option value="{{ $h }}" data-cari="{{ strtolower($h) }}" data-label="{{ $h }}"></option>
                    @endforeach
                </select>
                <select id="__dataJamMulai" hidden>
                    @foreach(['07:00','08:00','09:00','09:40','10:00','10:30','11:00','13:00','14:00','14:30','15:00','15:40','16:00'] as $j)
                    <option value="{{ $j }}" data-cari="{{ $j }}" data-label="{{ $j }}"></option>
                    @endforeach
                </select>
                <select id="__dataJamSelesai" hidden>
                    @foreach(['08:40','09:30','10:20','11:20','12:00','12:10','14:20','15:00','15:20','15:30','16:20','17:00','18:10','18:20'] as $j)
                    <option value="{{ $j }}" data-cari="{{ $j }}" data-label="{{ $j }}"></option>
                    @endforeach
                </select>
                <select id="__dataRuangan" hidden>
                    @foreach($ruanganAll as $r)
                    <option value="{{ $r->id }}" data-cari="{{ strtolower($r->nama_ruangan) }}" data-label="{{ $r->nama_ruangan }}"></option>
                    @endforeach
                </select>
                <select id="__dataDosen" hidden>
                    @foreach($dosenAll as $d)
                    <option value="{{ $d->id }}" data-cari="{{ $d->nidn }} {{ strtolower($d->nama_dosen) }}" data-label="{{ $d->nidn }} — {{ $d->nama_dosen }}"></option>
                    @endforeach
                </select>
                <select id="__dataAsisten" hidden>
                    @foreach($asistenAll as $a)
                    <option value="{{ $a->id }}" data-cari="{{ $a->nim }} {{ strtolower($a->nama_asisten) }}" data-label="{{ $a->nim }} — {{ $a->nama_asisten }}"></option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- ── KOLOM KANAN: Tambah Praktikan ── --}}
    <div class="card">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
            <span class="card-title">Tambah Praktikan ke Kelas Ini</span>
            @if($mahasiswaBelumKelas->isNotEmpty())
                <span style="font-size:12px;color:var(--text-muted);">
                    {{ $mahasiswaBelumKelas->count() }} mahasiswa tersedia
                </span>
            @endif
        </div>
        <div class="card-body">
            @if($mahasiswaBelumKelas->isEmpty())
                <p style="font-size:13px;color:var(--text-muted);">
                    Semua mahasiswa sudah terdaftar di kelas ini atau belum ada mahasiswa.
                    Tambah mahasiswa baru lewat menu <strong>Mahasiswa</strong>.
                </p>
            @else
                {{-- Search filter --}}
                <input type="text" id="cariMhsBanyak" class="form-control"
                    placeholder="Cari NIM atau nama mahasiswa..."
                    autocomplete="off"
                    style="margin-bottom:10px;">

                {{-- Pilih semua --}}
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                    <div style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;user-select:none;"
                        onclick="document.getElementById('checkSemuaMhs').click()">
                        <input type="checkbox" id="checkSemuaMhs"
                            style="width:15px;height:15px;min-width:15px;margin:0;cursor:pointer;"
                            onclick="event.stopPropagation()">
                        <span>Pilih Semua</span>
                    </div>
                    <span id="jumlahDipilih" style="font-size:12px;color:var(--text-muted);"></span>
                </div>

                {{-- Form daftar checkbox --}}
                <form method="POST" action="{{ route('laboran.kelas.mahasiswa.enroll-banyak', $kelas) }}" id="formEnrollBanyak">
                    @csrf
                    <div id="daftarMhsCheckbox" style="
                        max-height:320px;
                        overflow-y:auto;
                        scrollbar-width:none;
                        -ms-overflow-style:none;
                        border:1px solid var(--border);
                        border-radius:8px;
                        margin-bottom:12px;
                    ">
                        @foreach($mahasiswaBelumKelas as $m)
                        <div class="mhs-checkbox-row"
                            data-cari="{{ strtolower($m->nim_mahasiswa . ' ' . $m->nama_mahasiswa) }}"
                            style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-bottom:1px solid var(--border);cursor:pointer;transition:background .15s;"
                            onclick="this.querySelector('.mhs-cb').click()">
                            <input type="checkbox" name="mahasiswa_ids[]" value="{{ $m->id }}"
                                class="mhs-cb"
                                onclick="event.stopPropagation()"
                                style="width:15px;height:15px;min-width:15px;flex-shrink:0;margin:0;cursor:pointer;">
                            <div style="min-width:0;">
                                <div style="font-size:13px;font-weight:600;line-height:1.3;">{{ $m->nama_mahasiswa }}</div>
                                <div style="font-size:11px;color:var(--text-muted);font-family:monospace;line-height:1.3;">{{ $m->nim_mahasiswa }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @error('mahasiswa_ids')
                        <div style="color:#DC2626;font-size:13px;margin-bottom:8px;">{{ $message }}</div>
                    @enderror

                    <button type="submit" class="btn btn-primary btn-block" id="btnEnroll" disabled>
                        + Tambahkan ke Kelas
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

{{-- ── Daftar Praktikan yang sudah di kelas ── --}}
<div class="card mt-4">
    <div class="card-header"><span class="card-title">Daftar Praktikan di Kelas Ini ({{ $mahasiswaDiKelas->count() }})</span></div>
    <div class="table-wrapper"><table class="table">
        <thead><tr><th>NIM</th><th>Nama</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse($mahasiswaDiKelas as $m)
        <tr>
            <td style="font-family:monospace;font-size:13px;">{{ $m->nim_mahasiswa }}</td>
            <td class="fw-600">{{ $m->nama_mahasiswa }}</td>
            <td>
                <form method="POST" action="{{ route('laboran.kelas.mahasiswa.remove',[$kelas,$m]) }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline" type="button" data-konfirm="Keluarkan {{ $m->nama_mahasiswa }} dari kelas ini?" data-konfirm-judul="Keluarkan Praktikan" data-konfirm-ya="Ya, Keluarkan">Keluarkan</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="3"><div class="empty-state"><p>Belum ada praktikan di kelas ini.</p></div></td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Restore combobox jadwal saat validasi gagal ────────────────────
    const hidHari     = document.getElementById('hidHari');
    const cariHari    = document.getElementById('cariHari');
    const hidMulai    = document.getElementById('hidJamMulai');
    const cariMulai   = document.getElementById('cariJamMulai');
    const hidSelesai  = document.getElementById('hidJamSelesai');
    const cariSelesai = document.getElementById('cariJamSelesai');
    if (hidHari?.value    && !cariHari?.value)    cariHari.value    = hidHari.value;
    if (hidMulai?.value   && !cariMulai?.value)   cariMulai.value   = hidMulai.value;
    if (hidSelesai?.value && !cariSelesai?.value) cariSelesai.value = hidSelesai.value;

    // ── Enroll banyak mahasiswa ────────────────────────────────────────
    const cariInput   = document.getElementById('cariMhsBanyak');
    const checkSemua  = document.getElementById('checkSemuaMhs');
    const btnEnroll   = document.getElementById('btnEnroll');
    const labelJumlah = document.getElementById('jumlahDipilih');

    if (!cariInput) return;

    function getRows()    { return document.querySelectorAll('.mhs-checkbox-row'); }
    function getVisible() { return [...getRows()].filter(r => r.style.display !== 'none'); }

    function updateUI() {
        const dipilih = document.querySelectorAll('.mhs-cb:checked').length;
        btnEnroll.disabled    = dipilih === 0;
        btnEnroll.textContent = dipilih > 0
            ? `+ Tambahkan ${dipilih} Mahasiswa ke Kelas`
            : '+ Tambahkan ke Kelas';
        labelJumlah.textContent = dipilih > 0 ? `${dipilih} dipilih` : '';

        const cbVisible   = getVisible().map(r => r.querySelector('.mhs-cb'));
        const semuaCeklis = cbVisible.length > 0 && cbVisible.every(cb => cb.checked);
        checkSemua.indeterminate = !semuaCeklis && cbVisible.some(cb => cb.checked);
        checkSemua.checked       = semuaCeklis && cbVisible.length > 0;
    }

    // Event checkbox individual — delegasi ke container
    document.getElementById('daftarMhsCheckbox')?.addEventListener('change', function (e) {
        if (e.target.classList.contains('mhs-cb')) updateUI();
    });

    // Pilih semua (hanya yang terlihat)
    checkSemua.addEventListener('change', function () {
        getVisible().forEach(r => {
            r.querySelector('.mhs-cb').checked = this.checked;
        });
        updateUI();
    });

    // Hover styling
    document.getElementById('daftarMhsCheckbox')?.addEventListener('mouseover', function (e) {
        const row = e.target.closest('.mhs-checkbox-row');
        if (row) row.style.background = 'var(--bg-hover, #F9FAFB)';
    });
    document.getElementById('daftarMhsCheckbox')?.addEventListener('mouseout', function (e) {
        const row = e.target.closest('.mhs-checkbox-row');
        if (row) row.style.background = '';
    });

    // Filter pencarian
    cariInput.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        getRows().forEach(row => {
            row.style.display = (!q || row.dataset.cari.includes(q)) ? 'flex' : 'none';
        });
        updateUI();
    });

    // Submit
    document.getElementById('formEnrollBanyak')?.addEventListener('submit', function (e) {
        const dipilih = document.querySelectorAll('.mhs-cb:checked').length;
        if (dipilih === 0) { e.preventDefault(); return; }
        btnEnroll.disabled    = true;
        btnEnroll.textContent = 'Menyimpan…';
    });

    updateUI();
});
</script>
@endpush
@endsection