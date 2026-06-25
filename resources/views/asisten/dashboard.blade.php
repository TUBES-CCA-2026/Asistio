@extends('layouts.app')
@section('title','Dashboard Asisten')
@section('page-title','Dashboard Asisten')
@section('content')
<div class="hero-banner">
    <h1 class="hero-title">Halo, {{ auth()->user()->nama }}! 👋</h1>
    <p class="hero-subtitle">Pilih kelas yang Anda ampu untuk memulai presensi atau pengisian nilai.</p>
</div>
@if($kelasList->isEmpty())
<div class="card"><div class="empty-state"><p>Anda belum ditugaskan ke kelas manapun. Hubungi laboran.</p></div></div>
@else
<div class="grid grid-2">
@foreach($kelasList as $kelas)
<div class="course-card">
    <div class="course-card-header">
        <div class="course-card-code">{{ $kelas->mataKuliah?->kode_mk }}</div>
        <div class="course-card-name">{{ $kelas->mataKuliah?->nama_mk }}</div>
        <div class="course-card-meta">
            <span class="fw-600">{{ $kelas->nama_kelas }}</span>
            @if($kelas->dosen)<span>{{ $kelas->dosen->nama_dosen }}</span>@endif
            @if($kelas->jadwal)<span>{{ $kelas->jadwal }}</span>@endif
            @if($kelas->ruangan)<span>{{ $kelas->ruangan->nama_ruangan }}</span>@endif
            <span>{{ $kelas->mahasiswa_count }} mahasiswa</span>
        </div>
    </div>
    <div class="course-card-footer">
        <a href="{{ route('asisten.presensi', $kelas) }}" class="btn btn-outline btn-sm">Presensi</a>
        <a href="{{ route('asisten.nilai', $kelas) }}" class="btn btn-outline btn-sm">Nilai</a>
        <a href="{{ route('asisten.rekap', $kelas) }}" class="btn btn-primary btn-sm">Rekap →</a>
    </div>
</div>
@endforeach
</div>
@endif
@endsection
