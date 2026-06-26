@extends('layouts.app')
@section('title','Input Presensi')
@section('page-title','Input Presensi')
@section('page-subtitle') {{ $praktikum->mataKuliah?->nama_mk }} — {{ $praktikum->nama_kelas }} @endsection
@section('content')
<div class="page-toolbar">
    <a href="{{ route('asisten.dashboard') }}" class="btn btn-outline">← Kembali</a>
    <div style="display:flex;align-items:center;gap:8px;">
        <a href="?pertemuan={{ max(1,$pertemuan-1) }}" class="btn btn-outline btn-sm">‹ Sebelumnya</a>
        <span class="fw-600 text-primary">Pertemuan {{ $pertemuan }}</span>
        <a href="?pertemuan={{ $pertemuan+1 }}" class="btn btn-outline btn-sm">Berikutnya ›</a>
        <form method="GET" action="{{ url()->current() }}" style="display:flex;align-items:center;gap:6px;margin-left:8px;padding-left:8px;border-left:1px solid var(--border-color, #e5e7eb);">
            <label for="pertemuan-jump" class="fs-12 text-muted" style="margin:0;">Lompat ke:</label>
            <input type="number" id="pertemuan-jump" name="pertemuan" min="1" max="14" value="{{ $pertemuan }}" class="form-control form-control-sm" style="width:64px;">
            <button type="submit" class="btn btn-outline btn-sm">Lompat</button>
        </form>
    </div>
</div>
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);">
    <div class="stat-card"><div class="stat-body"><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Mahasiswa</div></div></div>
    <div class="stat-card"><div class="stat-body"><div class="stat-value" style="color:var(--status-h)">{{ $stats['hadir'] }}</div><div class="stat-label">Hadir</div></div></div>
    <div class="stat-card"><div class="stat-body"><div class="stat-value" style="color:var(--status-a)">{{ $stats['alpa'] }}</div><div class="stat-label">Alpha</div></div></div>
</div>
<form method="POST" action="{{ route('asisten.presensi.simpan', $praktikum) }}">@csrf
<input type="hidden" name="pertemuan" value="{{ $pertemuan }}">
<div class="card">
    <div class="card-header">
        <span class="card-title">Pertemuan {{ $pertemuan }}</span>
        <div style="display:flex;gap:6px;align-items:center;">
            <span class="fs-12 text-muted">Tandai semua:</span>
            <button type="button" class="btn btn-sm btn-outline status-btn-bulk" data-status="H">Hadir</button>
            <button type="button" class="btn btn-sm btn-outline status-btn-bulk" data-status="A">Alpha</button>
        </div>
    </div>
    <div class="table-wrapper"><table class="table">
        <thead><tr><th>#</th><th>NIM</th><th>Nama</th><th style="text-align:center;">H</th><th style="text-align:center;">I</th><th style="text-align:center;">S</th><th style="text-align:center;">A</th><th>Catatan</th></tr></thead>
        <tbody>
        @forelse($mahasiswaList as $i => $m)
        @php
            $p = $presensiMap[$m->id] ?? null;
            $status = $p?->status_kehadiran; {{-- null jika belum diisi, agar tidak ada radio yang tercentang otomatis --}}
            $alpaTinggi = $m->melebihiBatasAlpa();
        @endphp
        <tr class="{{ $alpaTinggi ? 'row-alpa-alert' : '' }}">
            <td>{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</td>
            <td style="font-family:monospace;font-size:12px;">{{ $m->nim_mahasiswa }}</td>
            <td class="fw-500">
                {{ $m->nama_mahasiswa }}
                @if($alpaTinggi)
                    <span class="badge-alpa-alert" title="Sudah alpa {{ $m->jumlah_alpa }}x — sudah mencapai/melewati batas {{ \App\Models\Mahasiswa::BATAS_ALPA }} pertemuan">⚠ Alpa {{ $m->jumlah_alpa }}×</span>
                @endif
            </td>
            @foreach(['H','I','S','A'] as $s)
            <td style="text-align:center;">
                <label class="radio-circle radio-{{ strtolower($s) }}">
                    <input type="radio" name="presensi[{{ $m->id }}][status_kehadiran]" value="{{ $s }}" {{ $status===$s?'checked':'' }}>
                    <span>{{ $s }}</span>
                </label>
            </td>
            @endforeach
            <td><input type="text" name="presensi[{{ $m->id }}][catatan]" class="form-control form-control-sm" value="{{ $p?->catatan }}" placeholder="—"></td>
        </tr>
        @empty<tr><td colspan="8"><div class="empty-state"><p>Belum ada mahasiswa di kelas ini.</p></div></td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($mahasiswaList->count() > 0)
    <div class="card-footer"><button type="submit" class="btn btn-primary">Simpan Presensi Pertemuan {{ $pertemuan }}</button></div>
    @endif
</div>
</form>
@endsection