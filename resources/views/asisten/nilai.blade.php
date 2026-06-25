@extends('layouts.app')
@section('title','Input Nilai')
@section('page-title','Input Nilai')
@section('page-subtitle') {{ $praktikum->mataKuliah?->nama_mk }} — {{ $praktikum->nama_kelas }} @endsection
@section('content')
@section('page-subtitle') 
{{ $praktikum->mataKuliah?->nama_mk }} — {{ $praktikum->nama_kelas }}
@if($praktikum->dosen)
<span style="font-weight:400;opacity:.75;font-size:.9em;">· Dosen: {{ $praktikum->dosen->nama_dosen }}</span>
@endif
@endsection
<div class="page-toolbar"><a href="{{ route('asisten.dashboard') }}" class="btn btn-outline">← Kembali</a></div>
<div class="card"><div class="table-wrapper" style="overflow-x:auto;">
    <table class="table" style="min-width:900px;">
        <thead><tr>
            <th>Mahasiswa</th>
            <th style="text-align:center;">Eval 1</th><th style="text-align:center;">Eval 2</th>
            <th style="text-align:center;">Eval 3</th><th style="text-align:center;">Eval 4</th>
            <th style="text-align:center;">Asist 1</th><th style="text-align:center;">Asist 2</th><th style="text-align:center;">Asist 3</th>
            <th style="text-align:center;">MID</th><th style="text-align:center;">UAS</th>
            <th style="text-align:center;">NA</th><th style="text-align:center;">Aksi</th>
        </tr></thead>
        <tbody>
        @forelse($mahasiswaList as $m)
        @php $n = $nilaiMap[$m->id]; @endphp
        <tr>
            <td><div class="fw-600 fs-13">{{ $m->nama_mahasiswa }}</div><div class="fs-11 text-muted">{{ $m->nim_mahasiswa }}</div></td>
            <form method="POST" action="{{ route('asisten.nilai.simpan', [$praktikum, $m]) }}">@csrf
            @foreach([1,2,3,4] as $i)
            <td><input type="number" name="nilai_evaluasi{{ $i }}" class="form-control form-control-xs" min="0" max="100" value="{{ $n['evaluasi']->{'nilai_evaluasi'.$i} ?? '' }}" placeholder="—"></td>
            @endforeach
            @foreach([1,2,3] as $i)
            <td><input type="number" name="nilai_asistensi{{ $i }}" class="form-control form-control-xs" min="0" max="100" value="{{ $n['asistensi']->{'nilai_asistensi'.$i} ?? '' }}" placeholder="—"></td>
            @endforeach
            <td><input type="number" name="nilai_MID" class="form-control form-control-xs" min="0" max="100" value="{{ $n['ujian']->nilai_MID ?? '' }}" placeholder="—"></td>
            <td><input type="number" name="nilai_UAS" class="form-control form-control-xs" min="0" max="100" value="{{ $n['ujian']->nilai_UAS ?? '' }}" placeholder="—"></td>
            <td style="text-align:center;">
                <span class="fw-700 text-primary">{{ $n['rekap']?->nilai_akhir ?? '—' }}</span>
                @if($n['rekap']?->nilai_huruf)<br><span class="grade-badge badge-{{ strtolower($n['rekap']->nilai_huruf) }}">{{ $n['rekap']->nilai_huruf }}</span>@endif
            </td>
            <td><button type="submit" class="btn btn-sm btn-primary">Simpan</button></form></td>
        </tr>
        @empty<tr><td colspan="12"><div class="empty-state"><p>Belum ada mahasiswa.</p></div></td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
@endsection
