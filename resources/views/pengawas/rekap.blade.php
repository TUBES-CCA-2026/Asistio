@extends('layouts.app')
@section('title','Rekap Data')
@section('page-title','Rekap Data Praktikan')
@section('page-subtitle') {{ $praktikum->mataKuliah?->nama_mk }} — {{ $praktikum->nama_kelas }} @endsection
@section('content')
<div class="page-toolbar"><a href="{{ route('pengawas.dashboard') }}" class="btn btn-outline">← Kembali</a></div>
<div class="card mb-4">
    <div class="card-header"><span class="card-title">Rekap Nilai Akhir</span></div>
    <div class="table-wrapper"><table class="table">
        <thead><tr><th>NIM</th><th>Nama</th><th style="text-align:center;">Eval</th><th style="text-align:center;">Asist</th><th style="text-align:center;">MID</th><th style="text-align:center;">UAS</th><th style="text-align:center;">Nilai Akhir</th><th style="text-align:center;">Huruf</th><th style="text-align:center;">Kehadiran</th></tr></thead>
        <tbody>
        @forelse($mahasiswaList as $m)
        @php $r = $m->rekap; $alpa = $m->jumlah_alpa; $alpaTinggi = $alpa >= \App\Models\Mahasiswa::BATAS_ALPA; @endphp
        
        <tr class="{{ $alpaTinggi ? 'row-alpa-alert' : '' }}">
            <td style="font-family:monospace;font-size:12px;">{{ $m->nim_mahasiswa }}</td>
            <td class="fw-600">{{ $m->nama_mahasiswa }}</td>
            <td style="text-align:center;">{{ $r?->nilai_praktikum ?? '—' }}</td>
            <td style="text-align:center;">{{ $r?->nilai_asistensi ?? '—' }}</td>
            <td style="text-align:center;">{{ $r?->nilai_MID ?? '—' }}</td>
            <td style="text-align:center;">{{ $r?->nilai_UAS ?? '—' }}</td>
            <td style="text-align:center;font-weight:700;color:var(--primary);">{{ $r?->nilai_akhir ?? '—' }}</td>
            <td style="text-align:center;">@if($r?->nilai_huruf)<span class="grade-badge badge-{{ strtolower($r->nilai_huruf) }}">{{ $r->nilai_huruf }}</span>@else—@endif</td>
            <td style="text-align:center;">
                {{ $m->persentase_hadir }}
                @if($alpa >= 4)<span class="badge badge-danger ml-1">{{ $alpa }}α</span>@endif
            </td>
        </tr>
        @empty<tr><td colspan="9"><div class="empty-state"><p>Belum ada data.</p></div></td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
<div class="card">
    <div class="card-header"><span class="card-title">Rekap Presensi</span></div>
    <div style="overflow-x:auto;"><table class="table" style="min-width:800px;">
        <thead><tr><th>NIM</th><th>Nama</th>@for($i=1;$i<=14;$i++)<th style="text-align:center;width:32px;">P{{ $i }}</th>@endfor<th>H</th><th>A</th></tr></thead>
        <tbody>
        @foreach($mahasiswaList as $m)
        @php
            $pp = $presensiAll[$m->id] ?? collect();
            $jumlahAlpaPertemuan = $pp->where('status_kehadiran','A')->count();
            $alpaTinggiPertemuan = $jumlahAlpaPertemuan >= \App\Models\Mahasiswa::BATAS_ALPA;
        @endphp
        <tr class="{{ $alpaTinggiPertemuan ? 'row-alpa-alert' : '' }}">
            <td style="font-family:monospace;font-size:11px;">{{ $m->nim_mahasiswa }}</td>
            <td>
                {{ $m->nama_mahasiswa }}
                @if($alpaTinggiPertemuan)
                    <span class="badge-alpa-alert" title="Sudah alpa {{ $jumlahAlpaPertemuan }}x — sudah mencapai/melewati batas {{ \App\Models\Mahasiswa::BATAS_ALPA }} pertemuan">⚠ Alpa {{ $jumlahAlpaPertemuan }}×</span>
                @endif
            </td>
            @for($j=1;$j<=14;$j++)@php $ps=$pp[$j]??null; @endphp
            <td style="text-align:center;padding:4px 2px;">@if($ps)<span class="status-chip status-chip-{{ $ps->status_kehadiran }}">{{ $ps->status_kehadiran }}</span>@else<span class="status-chip status-chip-empty">—</span>@endif</td>
            @endfor
            <td style="font-weight:700;color:var(--status-h);">{{ $pp->where('status_kehadiran','H')->count() }}</td>
            <td style="font-weight:700;color:var(--status-a);">{{ $pp->where('status_kehadiran','A')->count() }}</td>
        </tr>
        @endforeach
        </tbody>
    </table></div>
</div>
@endsection
