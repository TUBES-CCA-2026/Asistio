@extends('layouts.app')
@section('title','Rekap Data')
@section('page-title','Rekap Data Praktikan')
@section('page-subtitle') {{ $praktikum->mataKuliah?->nama_mk }} — {{ $praktikum->nama_kelas }} @endsection
@section('content')
<div class="page-toolbar">
    <a href="{{ route('pengawas.dashboard') }}" class="btn btn-outline">← Kembali</a>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('pengawas.rekap.export.pdf', $praktikum) }}" class="btn btn-outline btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Export PDF
        </a>
        <a href="{{ route('pengawas.rekap.export.excel', $praktikum) }}" class="btn btn-outline btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Export Excel
        </a>
    </div>
</div>
<div class="card mb-4">
    <div class="card-header"><span class="card-title">Rekap Nilai Akhir</span></div>
    <div class="table-wrapper"><table class="table">
        <thead><tr><th>NIM</th><th>Nama</th><th style="text-align:center;">Eval</th><th style="text-align:center;">Asist</th><th style="text-align:center;">MID</th><th style="text-align:center;">UAS</th><th style="text-align:center;">Nilai Akhir</th><th style="text-align:center;">Huruf</th><th style="text-align:center;">Kehadiran</th></tr></thead>
        <tbody>
        @forelse($mahasiswaList as $m)
        @php $r = $m->rekap->where('praktikum_id', $praktikum->id)->first(); $alpa = $m->jumlahAlpaDiKelas($praktikum->id); $alpaTinggi = $alpa >= \App\Models\Mahasiswa::BATAS_ALPA; @endphp
        
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
                {{ $m->persentaseHadirDiKelas($praktikum->id) }}
                @if($alpa >= 4)<span class="badge badge-danger ml-1">{{ $alpa }}α</span>@endif
            </td>
        </tr>
        @empty<tr><td colspan="9"><div class="empty-state"><p>Belum ada data.</p></div></td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
<div class="card mb-4">
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
<div class="card">
    <div class="card-header"><span class="card-title">Rekap Absensi Asistensi</span></div>
    <div style="overflow-x:auto;"><table class="table" style="min-width:400px;">
        <thead><tr><th>NIM</th><th>Nama</th><th style="text-align:center;width:100px;">Asistensi 1</th><th style="text-align:center;width:100px;">Asistensi 2</th><th style="text-align:center;width:100px;">Asistensi 3</th><th style="text-align:center;width:80px;">Hadir</th></tr></thead>
        <tbody>
        @forelse($mahasiswaList as $m)
        @php $pa = $presensiAsistensiAll[$m->id] ?? collect(); $hadirAsistensi = $pa->where('hadir', true)->count(); @endphp
        <tr>
            <td style="font-family:monospace;font-size:11px;">{{ $m->nim_mahasiswa }}</td>
            <td class="fw-600">{{ $m->nama_mahasiswa }}</td>
            @for($k=1;$k<=3;$k++)
            @php $pas = $pa[$k] ?? null; @endphp
            <td style="text-align:center;padding:4px 2px;">
                @if(!$pas)<span class="status-chip status-chip-empty">—</span>
                @elseif($pas->hadir)<span class="status-chip status-chip-H">H</span>
                @else<span class="status-chip status-chip-A">A</span>
                @endif
            </td>
            @endfor
            <td style="text-align:center;font-weight:700;color:var(--status-h);">{{ $hadirAsistensi }}/3</td>
        </tr>
        @empty<tr><td colspan="6"><div class="empty-state"><p>Belum ada data absensi asistensi.</p></div></td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
@endsection