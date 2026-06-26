@extends('layouts.app')
@section('title','Edit Mahasiswa')
@section('page-title','Edit Mahasiswa')
@section('content')
<div style="max-width:600px;">
    <a href="{{ route('laboran.mahasiswa') }}" class="btn btn-outline mb-4">← Kembali</a>
    <div class="card"><div class="card-header"><span class="card-title">Edit Data Mahasiswa</span></div>
    <div class="card-body">
    <form method="POST" action="{{ route('laboran.mahasiswa.update',$mahasiswa) }}">@csrf @method('PATCH')
    <div class="form-group"><label class="form-label required">NIM</label><input name="nim_mahasiswa" class="form-control" value="{{ $mahasiswa->nim_mahasiswa }}" required></div>
    <div class="form-group"><label class="form-label required">Nama</label><input name="nama_mahasiswa" class="form-control" value="{{ $mahasiswa->nama_mahasiswa }}" required></div>
    <div class="form-group"><label class="form-label">Kelas / Praktikum</label>
        <select name="praktikum_id" class="form-select">
            <option value="">— Belum ada kelas —</option>
            @foreach($praktikumAll as $p)
            <option value="{{ $p->id }}" {{ $mahasiswa->praktikum_id==$p->id?'selected':'' }}>
                {{ $p->mataKuliah?->kode_mk }} — {{ $p->mataKuliah?->nama_mk }} ({{ $p->nama_kelas }})
            </option>
            @endforeach
        </select>
    </div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><a href="{{ route('laboran.mahasiswa') }}" class="btn btn-outline">Batal</a><button class="btn btn-primary">Simpan</button></div>
    </form></div></div>
</div>
@endsection
