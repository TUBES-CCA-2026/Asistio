@extends('layouts.app')
@section('title','Kelas Praktikum')
@section('page-title','Kelas Praktikum')
@section('content')
<div class="page-toolbar"><button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Kelas</button></div>
<div class="card">
    <div class="table-toolbar">
        <div class="table-search-wrap">
            <i class="ti ti-search" aria-hidden="true"></i>
            <input type="text" class="table-search" placeholder="Cari kelas, mata kuliah, dosen...">
        </div>
        <span class="table-count"></span>
    </div>
    <div class="table-wrapper"><table class="table" data-table>
    <thead><tr>
        <th data-col="0">Kelas</th>
        <th data-col="1">Mata Kuliah</th>
        <th data-col="2">Jadwal</th>
        <th data-col="3">Ruangan</th>
        <th data-col="4">Dosen</th>
        <th data-col="5">Asisten 1</th>
        <th data-col="6">Asisten 2</th>
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
</table></div></div>
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
@endsection
