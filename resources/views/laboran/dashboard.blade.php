@extends('layouts.app')
@section('title','Dashboard Laboran')
@section('page-title','Dashboard')
@section('page-subtitle','Selamat datang di Asistio — ICLABS FIKOM UMI')
@section('content')

{{-- ── Baris 1: 6 Stat Card ──────────────────────────────────────── --}}
<div class="stats-grid" style="grid-template-columns:repeat(6,1fr);">
    <div class="stat-card">
        <div class="stat-icon stat-icon-primary"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg></div>
        <div class="stat-body"><div class="stat-value">{{ $totalMK }}</div><div class="stat-label">Mata Kuliah</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-purple"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg></div>
        <div class="stat-body"><div class="stat-value">{{ $totalKelas }}</div><div class="stat-label">Kelas</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
        <div class="stat-body"><div class="stat-value">{{ $totalMahasiswa }}</div><div class="stat-label">Mahasiswa</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-orange"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></div>
        <div class="stat-body"><div class="stat-value">{{ $totalAsisten }}</div><div class="stat-label">Asisten</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
        <div class="stat-body"><div class="stat-value">{{ $totalDosen }}</div><div class="stat-label">Dosen</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-teal"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
        <div class="stat-body"><div class="stat-value">{{ $totalRuangan }}</div><div class="stat-label">Ruangan</div></div>
    </div>
</div>

{{-- ── Baris 2: 3 kartu info ──────────────────────────────────────── --}}
<div class="info-grid">

    {{-- Kelengkapan Kelas --}}
    <div class="card">
        <div class="card-header"><span class="card-title">Kelengkapan Kelas</span><a href="{{ route('laboran.kelas') }}" class="btn btn-sm btn-outline">Lihat →</a></div>
        <div style="padding:4px 20px 16px;">
            @php $pctDosen    = $kelasTotal ? round((($kelasTotal-$kelasTanpaDosen)/$kelasTotal)*100) : 0; @endphp
            @php $pctAsisten  = $kelasTotal ? round((($kelasTotal-$kelasTanpaAsisten)/$kelasTotal)*100) : 0; @endphp
            @php $pctRuangan  = $kelasTotal ? round((($kelasTotal-$kelasTanpaRuangan)/$kelasTotal)*100) : 0; @endphp
            @php $pctMhs      = $kelasTotal ? round((($kelasTotal-$kelasTanpaMahasiswa)/$kelasTotal)*100) : 0; @endphp

            <div class="info-list-item">
                <span class="info-list-label">Sudah ada Dosen</span>
                <span class="info-list-value">{{ $kelasTotal-$kelasTanpaDosen }}/{{ $kelasTotal }}</span>
            </div>
            <div class="progress-bar-wrap"><div class="progress-bar-fill {{ $pctDosen==100?'green':($pctDosen>=50?'orange':'red') }}" style="width:{{ $pctDosen }}%"></div></div>

            <div class="info-list-item" style="margin-top:8px;">
                <span class="info-list-label">Sudah ada Asisten</span>
                <span class="info-list-value">{{ $kelasTotal-$kelasTanpaAsisten }}/{{ $kelasTotal }}</span>
            </div>
            <div class="progress-bar-wrap"><div class="progress-bar-fill {{ $pctAsisten==100?'green':($pctAsisten>=50?'orange':'red') }}" style="width:{{ $pctAsisten }}%"></div></div>

            <div class="info-list-item" style="margin-top:8px;">
                <span class="info-list-label">Sudah ada Ruangan</span>
                <span class="info-list-value">{{ $kelasTotal-$kelasTanpaRuangan }}/{{ $kelasTotal }}</span>
            </div>
            <div class="progress-bar-wrap"><div class="progress-bar-fill {{ $pctRuangan==100?'green':($pctRuangan>=50?'orange':'red') }}" style="width:{{ $pctRuangan }}%"></div></div>

            <div class="info-list-item" style="margin-top:8px;">
                <span class="info-list-label">Sudah ada Mahasiswa</span>
                <span class="info-list-value">{{ $kelasTotal-$kelasTanpaMahasiswa }}/{{ $kelasTotal }}</span>
            </div>
            <div class="progress-bar-wrap"><div class="progress-bar-fill {{ $pctMhs==100?'green':($pctMhs>=50?'orange':'red') }}" style="width:{{ $pctMhs }}%"></div></div>
        </div>
    </div>

    {{-- Rekap Presensi --}}
    <div class="card">
        <div class="card-header"><span class="card-title">Rekap Presensi</span></div>
        <div style="padding:4px 20px 16px;">
            @php $pctHadir = $totalPresensi ? round($totalHadir/$totalPresensi*100) : 0; @endphp
            <div class="info-list-item">
                <span class="info-list-label">Total Catatan</span>
                <span class="info-list-value">{{ number_format($totalPresensi) }}</span>
            </div>
            <div class="info-list-item">
                <span class="info-list-label">✅ Hadir</span>
                <span class="info-list-value" style="color:#22C55E;">{{ number_format($totalHadir) }}</span>
            </div>
            <div class="progress-bar-wrap"><div class="progress-bar-fill green" style="width:{{ $pctHadir }}%"></div></div>
            <div class="info-list-item" style="margin-top:8px;">
                <span class="info-list-label">📋 Izin</span>
                <span class="info-list-value">{{ number_format($totalIzin) }}</span>
            </div>
            <div class="info-list-item">
                <span class="info-list-label">🤒 Sakit</span>
                <span class="info-list-value">{{ number_format($totalSakit) }}</span>
            </div>
            <div class="info-list-item">
                <span class="info-list-label">❌ Alpha</span>
                <span class="info-list-value" style="color:#EF4444;">{{ number_format($totalAlpa) }}</span>
            </div>
            <div class="info-list-item" style="border-top:2px solid var(--border);margin-top:4px;padding-top:12px;">
                <span class="info-list-label">⚠ Mahasiswa ≥ {{ \App\Models\Mahasiswa::BATAS_ALPA }}x Alpha</span>
                <span class="info-list-value" style="color:#EF4444;">{{ $mahasiswaAlpa }}</span>
            </div>
        </div>
    </div>

    {{-- Peringatan --}}
    <div class="card">
        <div class="card-header"><span class="card-title">Perlu Perhatian</span></div>
        <div style="padding:4px 20px 16px;">
            <div class="info-list-item">
                <span class="info-list-label">Kelas tanpa Dosen</span>
                <span class="info-list-value" style="color:{{ $kelasTanpaDosen>0?'#EF4444':'#22C55E' }};">{{ $kelasTanpaDosen }}</span>
            </div>
            <div class="info-list-item">
                <span class="info-list-label">Kelas tanpa Asisten</span>
                <span class="info-list-value" style="color:{{ $kelasTanpaAsisten>0?'#EF4444':'#22C55E' }};">{{ $kelasTanpaAsisten }}</span>
            </div>
            <div class="info-list-item">
                <span class="info-list-label">Kelas tanpa Ruangan</span>
                <span class="info-list-value" style="color:{{ $kelasTanpaRuangan>0?'#EF4444':'#22C55E' }};">{{ $kelasTanpaRuangan }}</span>
            </div>
            <div class="info-list-item">
                <span class="info-list-label">Kelas tanpa Mahasiswa</span>
                <span class="info-list-value" style="color:{{ $kelasTanpaMahasiswa>0?'#F59E0B':'#22C55E' }};">{{ $kelasTanpaMahasiswa }}</span>
            </div>
            <div class="info-list-item">
                <span class="info-list-label">Asisten tanpa Kelas</span>
                <span class="info-list-value" style="color:{{ $asistenTanpaKelas>0?'#F59E0B':'#22C55E' }};">{{ $asistenTanpaKelas }}</span>
            </div>
            <div class="info-list-item" style="border-top:2px solid var(--border);margin-top:4px;padding-top:12px;">
                <span class="info-list-label">Mahasiswa Alpha Berlebih</span>
                <span class="info-list-value" style="color:{{ $mahasiswaAlpa>0?'#EF4444':'#22C55E' }};">{{ $mahasiswaAlpa }}</span>
            </div>
        </div>
    </div>

</div>

{{-- ── Baris 3: Kelas Terbesar + Mata Kuliah ─────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 2fr;gap:16px;">

    <div class="card">
        <div class="card-header"><span class="card-title">Kelas Terbesar</span></div>
        <div class="table-wrapper">
            <table class="table">
                <thead><tr><th>Kelas</th><th>Mata Kuliah</th><th style="text-align:center;">Mhs</th></tr></thead>
                <tbody>
                @forelse($kelasTerbesar as $k)
                <tr>
                    <td class="fw-600">{{ $k->nama_kelas }}</td>
                    <td><span class="badge badge-primary">{{ $k->mataKuliah?->kode_mk }}</span></td>
                    <td style="text-align:center;">{{ $k->mahasiswa_count }}</td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state"><p>Belum ada kelas.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Daftar Mata Kuliah</span><a href="{{ route('laboran.mata-kuliah') }}" class="btn btn-sm btn-outline">Kelola →</a></div>
        <div class="table-wrapper">
            <table class="table">
                <thead><tr><th>Kode MK</th><th>Nama Mata Kuliah</th><th style="text-align:center;">Mahasiswa</th><th style="text-align:center;">Kelas</th></tr></thead>
                <tbody>
                @forelse($mataKuliah as $mk)
                <tr>
                    <td><span class="badge badge-primary">{{ $mk->kode_mk }}</span></td>
                    <td class="fw-600">{{ $mk->nama_mk }}</td>
                    <td style="text-align:center;">{{ $mk->mahasiswa_count ?? 0 }}</td>
                    <td style="text-align:center;">{{ $mk->praktikum_count }}</td>
                </tr>
                @empty
                <tr><td colspan="4"><div class="empty-state"><p>Belum ada mata kuliah. <a href="{{ route('laboran.mata-kuliah') }}">Tambahkan sekarang</a>.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection