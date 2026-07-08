@extends('layouts.app')
@section('title','Input Nilai')
@section('page-title','Input Nilai')
@section('page-subtitle') {{ $praktikum->mataKuliah?->nama_mk }} — {{ $praktikum->nama_kelas }} @endsection
@section('content')
<div class="page-toolbar"><a href="{{ route('asisten.dashboard') }}" class="btn btn-outline">← Kembali</a></div>
<div class="card"><div class="table-wrapper" style="overflow-x:auto;">
    <table class="table" style="min-width:1900px;">
        <thead>
        {{-- Baris 1: label kolom biasa --}}
        <tr>
            <th>Mahasiswa</th>
            @for($i = 1; $i <= 14; $i++)
            <th style="text-align:center;">P{{ $i }}</th>
            @endfor
            <th style="text-align:center;">Asist 1</th><th style="text-align:center;">Asist 2</th><th style="text-align:center;">Asist 3</th>
            <th style="text-align:center;">MID</th><th style="text-align:center;">UAS</th>
            <th style="text-align:center;">NA</th><th style="text-align:center;">Aksi</th>
        </tr>
        {{-- Baris 2: tombol reset per pertemuan --}}
        <tr>
            <th></th>
            @for($i = 1; $i <= 14; $i++)
            <th style="text-align:center;padding:4px 2px;">
                <form method="POST"
                      action="{{ route('asisten.nilai.reset-pertemuan', [$praktikum, $i]) }}"
                      onsubmit="return confirm('Reset semua nilai P{{ $i }} menjadi 0?')">
                    @csrf
                    <button type="submit"
                            class="btn btn-sm btn-danger"
                            style="font-size:10px;padding:2px 6px;line-height:1.4;">
                        Reset
                    </button>
                </form>
            </th>
            @endfor
            {{-- Kolom Asist 1/2/3, MID, UAS, NA, Aksi tidak punya reset --}}
            <th colspan="7"></th>
        </tr>
        </thead>
        <tbody>
        @forelse($mahasiswaList as $m)
        @php $n = $nilaiMap[$m->id]; @endphp
        <tr>
            <td><div class="fw-600 fs-13">{{ $m->nama_mahasiswa }}</div><div class="fs-11 text-muted">{{ $m->nim_mahasiswa }}</div></td>
            <form method="POST" action="{{ route('asisten.nilai.simpan', [$praktikum, $m]) }}">@csrf
            @for($i = 1; $i <= 14; $i++)
            <td><input type="number" name="p{{ $i }}" class="form-control form-control-xs" min="0" max="100" value="{{ $n['evaluasi']->{'p'.$i} ?? '' }}" placeholder="—"></td>
            @endfor
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
        @empty<tr><td colspan="22"><div class="empty-state"><p>Belum ada mahasiswa.</p></div></td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
@endsection