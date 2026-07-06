@extends('layouts.app')
@section('title','Detail Kelas')
@section('page-title', $kelas->nama_kelas)
@section('page-subtitle') {{ $kelas->mataKuliah?->nama_mk }} @endsection
@section('content')
<div class="page-toolbar"><a href="{{ route('laboran.kelas') }}" class="btn btn-outline">← Kembali ke Kelas Praktikum</a></div>

<div class="grid grid-2" style="gap:16px;align-items:start;">
    <div class="card">
        <div class="card-header"><span class="card-title">Dosen & Asisten Kelas</span></div>
        <div class="card-body">
            <p style="font-size:12px;color:var(--text-muted);margin:0 0 14px;">
                Tambahkan Asisten 2, ganti asisten yang bertugas, atau kosongkan kembali — cukup pilih lalu simpan.
            </p>
            <form method="POST" action="{{ route('laboran.kelas.update',$kelas) }}">
                @csrf @method('PATCH')
                {{-- ── RUANGAN ─────────────────────────────────────────── --}}
                <div class="form-group">
                    <label class="form-label">Ruangan</label>
                    <div class="search-combobox">
                        <input type="text" id="cariRuangan" class="form-control"
                            placeholder="Klik untuk pilih atau ketik untuk cari..."
                            autocomplete="off"
                            value="{{ $kelas->ruangan ? $kelas->ruangan->nama_ruangan : '' }}">
                        <input type="hidden" name="ruangan_id" id="hidRuangan"
                            value="{{ $kelas->ruangan_id ?? '' }}">
                        <div class="search-results" id="previewRuangan"></div>
                    </div>
                </div>
                {{-- ── DOSEN ──────────────────────────────────────────── --}}
                <div class="form-group">
                    <label class="form-label">Dosen</label>
                    <div class="search-combobox">
                        <input type="text" id="cariDosen" class="form-control"
                            placeholder="Klik untuk pilih atau ketik untuk cari..."
                            autocomplete="off"
                            value="{{ $kelas->dosen ? $kelas->dosen->nidn . ' — ' . $kelas->dosen->nama_dosen : '' }}">
                        <input type="hidden" name="dosen_id" id="hidDosen"
                            value="{{ $kelas->dosen_id ?? '' }}">
                        <div class="search-results" id="previewDosen"></div>
                    </div>
                </div>
                {{-- ── ASISTEN 1 ──────────────────────────────────────── --}}
                <div class="form-group">
                    <label class="form-label">Asisten 1</label>
                    <div class="search-combobox">
                        <input type="text" id="cariA1" class="form-control"
                            placeholder="Klik untuk pilih atau ketik untuk cari..."
                            autocomplete="off"
                            value="{{ $kelas->asisten ? $kelas->asisten->nim . ' — ' . $kelas->asisten->nama_asisten : '' }}">
                        <input type="hidden" name="asisten_id" id="hidA1"
                            value="{{ $kelas->asisten_id ?? '' }}">
                        <div class="search-results" id="previewA1"></div>
                    </div>
                </div>
                {{-- ── ASISTEN 2 ──────────────────────────────────────── --}}
                <div class="form-group">
                    <label class="form-label">Asisten 2</label>
                    <div class="search-combobox">
                        <input type="text" id="cariA2" class="form-control"
                            placeholder="Klik untuk pilih atau ketik untuk cari..."
                            autocomplete="off"
                            value="{{ $kelas->asisten2 ? $kelas->asisten2->nim . ' — ' . $kelas->asisten2->nama_asisten : '' }}">
                        <input type="hidden" name="asisten2_id" id="hidA2"
                            value="{{ $kelas->asisten2_id ?? '' }}">
                        <div class="search-results" id="previewA2"></div>
                    </div>
                </div>
                <button class="btn btn-primary btn-block">Simpan</button>

                {{-- Data store tersembunyi — hanya dibaca JS, tidak tampil ke user --}}
                <select id="__dataRuangan" hidden>
                    @foreach($ruanganAll as $r)
                    <option value="{{ $r->id }}"
                        data-cari="{{ strtolower($r->nama_ruangan) }}"
                        data-label="{{ $r->nama_ruangan }}">
                    </option>
                    @endforeach
                </select>
                <select id="__dataDosen" hidden>
                    @foreach($dosenAll as $d)
                    <option value="{{ $d->id }}"
                        data-cari="{{ $d->nidn }} {{ strtolower($d->nama_dosen) }}"
                        data-label="{{ $d->nidn }} — {{ $d->nama_dosen }}">
                    </option>
                    @endforeach
                </select>
                <select id="__dataAsisten" hidden>
                    @foreach($asistenAll as $a)
                    <option value="{{ $a->id }}"
                        data-cari="{{ $a->nim }} {{ strtolower($a->nama_asisten) }}"
                        data-label="{{ $a->nim }} — {{ $a->nama_asisten }}">
                    </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Tambah Praktikan ke Kelas Ini</span></div>
        <div class="card-body">
            @if($mahasiswaBelumKelas->isEmpty())
                <p style="font-size:13px;color:var(--text-muted);">Semua mahasiswa sudah terdaftar di kelas ini atau kelas lain. Tambah mahasiswa baru lewat menu <strong>Mahasiswa</strong>.</p>
            @else
                <form method="POST" action="{{ route('laboran.kelas.mahasiswa.add',$kelas) }}">
                    @csrf
                    <div class="search-combobox" style="margin-bottom:8px;">
                        <input type="text" id="cariMhs" class="form-control"
                            placeholder="Klik untuk pilih atau ketik untuk cari..."
                            autocomplete="off">
                        <input type="hidden" name="mahasiswa_id" id="hidMhs">
                        <div class="search-results" id="previewMhs"></div>
                    </div>
                    <button class="btn btn-primary btn-block">+ Tambah Praktikan</button>

                    {{-- Data store tersembunyi — hanya dibaca JS --}}
                    <select id="__dataMhs" hidden>
                        @foreach($mahasiswaBelumKelas as $m)
                        <option value="{{ $m->id }}"
                            data-cari="{{ $m->nim_mahasiswa }} {{ strtolower($m->nama_mahasiswa) }}"
                            data-label="{{ $m->nim_mahasiswa }} — {{ $m->nama_mahasiswa }}">
                        </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>
    </div>
</div>

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
                    <button class="btn btn-sm btn-outline" onclick="return confirm('Keluarkan {{ $m->nama_mahasiswa }} dari kelas ini?')">Keluarkan</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="3"><div class="empty-state"><p>Belum ada praktikan di kelas ini.</p></div></td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
@endsection