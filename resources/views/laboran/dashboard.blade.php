@extends('layouts.app')
@section('title','Dashboard Laboran')
@section('page-title','Dashboard')
@section('page-subtitle','Selamat datang di Asistio — ICo Labs UMI')
@section('content')
<div class="stats-grid">
    <div class="stat-card"><div class="stat-icon stat-icon-primary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg></div><div class="stat-body"><div class="stat-value">{{ $totalMK }}</div><div class="stat-label">Mata Kuliah</div></div></div>
    <div class="stat-card"><div class="stat-icon stat-icon-green"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><div class="stat-body"><div class="stat-value">{{ $totalMahasiswa }}</div><div class="stat-label">Mahasiswa</div></div></div>
    <div class="stat-card"><div class="stat-icon stat-icon-orange"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/></svg></div><div class="stat-body"><div class="stat-value">{{ $totalAsisten }}</div><div class="stat-label">Asisten</div></div></div>
    <div class="stat-card"><div class="stat-icon stat-icon-blue"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg></div><div class="stat-body"><div class="stat-value">{{ $totalDosen }}</div><div class="stat-label">Dosen</div></div></div>
</div>
<div class="card mt-5">
    <div class="card-header"><span class="card-title">Daftar Mata Kuliah</span><a href="{{ route('laboran.mata-kuliah') }}" class="btn btn-sm btn-outline">Kelola →</a></div>
    <div class="table-wrapper">
        <table class="table">
            <thead><tr><th>Kode MK</th><th>Nama Mata Kuliah</th><th style="text-align:center;">Mahasiswa</th><th style="text-align:center;">Kelas</th></tr></thead>
            <tbody>
            @forelse($mataKuliah as $mk)
            <tr>
                <td><span class="badge badge-primary">{{ $mk->kode_mk }}</span></td>
                <td class="fw-600">{{ $mk->nama_mk }}</td>
                <td style="text-align:center;">{{ $mk->mahasiswa_count }}</td>
                <td style="text-align:center;">{{ $mk->praktikum_count }}</td>
            </tr>
            @empty
            <tr><td colspan="4"><div class="empty-state"><p>Belum ada mata kuliah. <a href="{{ route('laboran.mata-kuliah') }}">Tambahkan sekarang</a>.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
