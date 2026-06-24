@extends('layouts.app')
@section('title','Rekap Presensi')
@section('page-title','Rekap Presensi')
@section('page-subtitle') {{ $praktikum->mataKuliah?->nama_mk }} — {{ $praktikum->nama_kelas }} @endsection
@section('content')
<div class="page-toolbar"><a href="{{ route('asisten.dashboard') }}" class="btn btn-outline">← Kembali</a></div>
<div class="card"><div style="overflow-x:auto;"><table class="table" style="min-width:{{ 300 + 14*40 }}px;">
    <thead><tr>
        <th>NIM</th><th>Nama</th>
        @for($i=1;$i<=14;$i++)<th style="text-align:center;width:36px;">P{{ $i }}</th>@endfor
        <th>H</th><th>A</th>
    </tr></thead>
    <tbody>
    @foreach($mahasiswaList as $m)
    @php $pp = $presensiAll[$m->id] ?? collect(); @endphp
    <tr>
        <td style="font-family:monospace;font-size:11px;">{{ $m->nim_mahasiswa }}</td>
        <td class="fw-500">{{ $m->nama_mahasiswa }}</td>
        @for($j=1;$j<=14;$j++)
        @php $ps=$pp[$j]??null; @endphp
        <td style="text-align:center;padding:4px 2px;">
            @if($ps)<span class="status-chip status-chip-{{ $ps->status_kehadiran }}">{{ $ps->status_kehadiran }}</span>
            @else<span class="status-chip status-chip-empty">—</span>@endif
        </td>
        @endfor
        <td style="font-weight:700;color:var(--status-h);">{{ $pp->where('status_kehadiran','H')->count() }}</td>
        <td style="font-weight:700;color:var(--status-a);">{{ $pp->where('status_kehadiran','A')->count() }}</td>
    </tr>
    @endforeach
    </tbody>
</table></div></div>
@endsection
