@extends('layouts.app')
@section('title','Detail Kelas')
@section('page-title', $kelas->nama_kelas)
@section('page-subtitle') {{ $kelas->mataKuliah?->nama_mk }} @endsection
@section('content')
<div class="page-toolbar"><a href="{{ route('laboran.kelas') }}" class="btn btn-outline">← Kembali ke Kelas Praktikum</a></div>

<div class="grid grid-2" style="gap:16px;align-items:start;">
    <div class="card">
        <div class="card-header"><span class="card-title">Asisten Kelas</span></div>
        <div class="card-body">
            <p style="font-size:12px;color:var(--text-muted);margin:0 0 14px;">
                Tambahkan Asisten 2, ganti asisten yang bertugas, atau kosongkan kembali — cukup pilih lalu simpan.
            </p>
            <form method="POST" action="{{ route('laboran.kelas.update',$kelas) }}">
                @csrf @method('PATCH')
                <div class="form-group">
                    <label class="form-label">Asisten 1</label>
                    <select name="asisten_id" class="form-select">
                        <option value="">— Tidak ada —</option>
                        @foreach($asistenAll as $a)
                        <option value="{{ $a->id }}" {{ $kelas->asisten_id == $a->id ? 'selected' : '' }}>{{ $a->nama_asisten }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Asisten 2</label>
                    <select name="asisten2_id" class="form-select">
                        <option value="">— Tidak ada —</option>
                        @foreach($asistenAll as $a)
                        <option value="{{ $a->id }}" {{ $kelas->asisten2_id == $a->id ? 'selected' : '' }}>{{ $a->nama_asisten }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary btn-block">Simpan Asisten</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Tambah Praktikan ke Kelas Ini</span></div>
        <div class="card-body">
            @if($mahasiswaBelumKelas->isEmpty())
                <p style="font-size:13px;color:var(--text-muted);">Semua mahasiswa sudah memiliki kelas. Tambah mahasiswa baru lewat menu <strong>Mahasiswa</strong>, lalu kembali ke sini untuk memasukkannya ke kelas ini.</p>
            @else
                <form method="POST" action="{{ route('laboran.kelas.mahasiswa.add',$kelas) }}" style="display:flex;gap:8px;">
                    @csrf
                    <select name="mahasiswa_id" class="form-select" required>
                        <option value="">Tambah Mahasiswa...</option>
                        @foreach($mahasiswaBelumKelas as $m)
                        <option value="{{ $m->id }}">{{ $m->nim_mahasiswa }} — {{ $m->nama_mahasiswa }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-primary" style="white-space:nowrap;">+ Tambah</button>
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