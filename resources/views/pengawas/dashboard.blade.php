@extends('layouts.app')
@section('title','Dashboard Pengawas')
@section('page-title','Monitoring Praktikum')
@section('content')
@if($errors->any())
<script>document.addEventListener('DOMContentLoaded',()=>{ const o=document.getElementById('modalGantiPassword'); if(o) o.classList.add('is-open'); });</script>
@endif
<div class="hero-banner">
    <h1 class="hero-title">Selamat datang, {{ $dosen?->nama_dosen ?? auth()->user()->username }}!</h1>
    <p class="hero-subtitle">Pantau rekap nilai dan presensi mahasiswa per kelas.</p>
</div>
@if($kelasList->isEmpty())
<div class="card"><div class="empty-state"><p>Belum ada kelas yang ditugaskan. Hubungi laboran.</p></div></div>
@else
<div class="grid grid-2">
@foreach($kelasList as $kelas)
<div class="course-card">
    <div class="course-card-header">
        <div class="course-card-code">{{ $kelas->mataKuliah?->kode_mk }}</div>
        <div class="course-card-name">{{ $kelas->mataKuliah?->nama_mk }}</div>
        <div class="course-card-meta">
            <span class="fw-600">{{ $kelas->nama_kelas }}</span>
            @if($kelas->jadwal)<span>{{ $kelas->jadwal }}</span>@endif
            <span>{{ $kelas->mahasiswa_count }} mahasiswa</span>
            @if($kelas->asisten)<span>Asisten: {{ $kelas->asisten->nama_asisten }}</span>@endif
            @if($kelas->asisten)<span>Asisten 1: {{ $kelas->asisten->nama_asisten }}</span>@endif
            @if($kelas->asisten2)<span>Asisten 2: {{ $kelas->asisten2->nama_asisten }}</span>@endif
        </div>
    </div>
    <div class="course-card-footer">
        <a href="{{ route('pengawas.rekap', $kelas) }}" class="btn btn-primary btn-sm">Lihat Rekap →</a>
    </div>
</div>
@endforeach
</div>
@endif
<div id="modalGantiPassword" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">...</div>
        <div class="modal-body">
            <form method="POST" action="{{ route('asisten.ganti-password.update') }}">
                @csrf
                {{-- input password_lama, password_baru, konfirmasi --}}
            </form>
        </div>
    </div>
</div>
@endsection
