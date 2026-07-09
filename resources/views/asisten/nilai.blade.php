@extends('layouts.app')
@section('title','Input Nilai')
@section('page-title','Input Nilai')
@section('page-subtitle') {{ $praktikum->mataKuliah?->nama_mk }} — {{ $praktikum->nama_kelas }} @endsection
@section('content')
<div class="page-toolbar"><a href="{{ route('asisten.dashboard') }}" class="btn btn-outline">← Kembali</a></div>

{{-- Form tunggal membungkus seluruh tabel --}}
<form method="POST" action="{{ route('asisten.nilai.simpan-semua', $praktikum) }}">
@csrf
<div class="card"><div class="table-wrapper" style="overflow-x:auto;">
    <table class="table" style="min-width:1900px;">
        <thead>
        {{-- Baris 1: label kolom --}}
        <tr>
            <th>Mahasiswa</th>
            @for($i = 1; $i <= 14; $i++)
            <th style="text-align:center;">P{{ $i }}</th>
            @endfor
            <th style="text-align:center;">Asist 1</th>
            <th style="text-align:center;">Asist 2</th>
            <th style="text-align:center;">Asist 3</th>
            <th style="text-align:center;">MID</th>
            <th style="text-align:center;">UAS</th>
            <th style="text-align:center;">NA</th>
        </tr>
        {{-- Baris 2: tombol reset per kolom --}}
        <tr>
            <th></th>
            {{-- Reset P1–P14 --}}
            @for($i = 1; $i <= 14; $i++)
            <th style="text-align:center;padding:4px 2px;">
                <form method="POST"
                      action="{{ route('asisten.nilai.reset-pertemuan', [$praktikum, $i]) }}"
                      onsubmit="return confirm('Reset semua nilai P{{ $i }} ke 0?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger"
                            style="font-size:10px;padding:2px 6px;line-height:1.4;">
                        Reset
                    </button>
                </form>
            </th>
            @endfor
            {{-- Reset Asist 1, 2, 3, MID, UAS --}}
            @foreach([
                'nilai_asistensi1' => 'Asist 1',
                'nilai_asistensi2' => 'Asist 2',
                'nilai_asistensi3' => 'Asist 3',
                'nilai_MID'        => 'MID',
                'nilai_UAS'        => 'UAS',
            ] as $kolom => $label)
            <th style="text-align:center;padding:4px 2px;">
                <form method="POST"
                      action="{{ route('asisten.nilai.reset-kolom', [$praktikum, $kolom]) }}"
                      onsubmit="return confirm('Reset semua nilai {{ $label }} ke 0?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger"
                            style="font-size:10px;padding:2px 6px;line-height:1.4;">
                        Reset
                    </button>
                </form>
            </th>
            @endforeach
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse($mahasiswaList as $m)
        @php $n = $nilaiMap[$m->id]; @endphp
        <tr>
            <td>
                <div class="fw-600 fs-13">{{ $m->nama_mahasiswa }}</div>
                <div class="fs-11 text-muted">{{ $m->nim_mahasiswa }}</div>
            </td>
            {{-- Nilai Evaluasi P1–P14 --}}
            @for($i = 1; $i <= 14; $i++)
            <td>
                <input type="number" name="nilai[{{ $m->id }}][p{{ $i }}]"
                    class="form-control form-control-xs"
                    min="0" max="100" step="0.01"
                    value="{{ $n['evaluasi']->{'p'.$i} ?? '' }}"
                    placeholder="—">
            </td>
            @endfor
            {{-- Nilai Asistensi 1–3 --}}
            @foreach([1,2,3] as $i)
            <td>
                <input type="number" name="nilai[{{ $m->id }}][nilai_asistensi{{ $i }}]"
                    class="form-control form-control-xs"
                    min="0" max="100" step="0.01"
                    value="{{ $n['asistensi']->{'nilai_asistensi'.$i} ?? '' }}"
                    placeholder="—">
            </td>
            @endforeach
            {{-- MID & UAS --}}
            <td>
                <input type="number" name="nilai[{{ $m->id }}][nilai_MID]"
                    class="form-control form-control-xs"
                    min="0" max="100" step="0.01"
                    value="{{ $n['ujian']->nilai_MID ?? '' }}"
                    placeholder="—">
            </td>
            <td>
                <input type="number" name="nilai[{{ $m->id }}][nilai_UAS]"
                    class="form-control form-control-xs"
                    min="0" max="100" step="0.01"
                    value="{{ $n['ujian']->nilai_UAS ?? '' }}"
                    placeholder="—">
            </td>
            {{-- Nilai Akhir (read-only) --}}
            <td style="text-align:center;">
                <span class="fw-700 text-primary">{{ $n['rekap']?->nilai_akhir ?? '—' }}</span>
                @if($n['rekap']?->nilai_huruf)
                <br><span class="grade-badge badge-{{ strtolower($n['rekap']->nilai_huruf) }}">{{ $n['rekap']->nilai_huruf }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="21"><div class="empty-state"><p>Belum ada mahasiswa.</p></div></td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>

{{-- SATU tombol Simpan untuk semua mahasiswa --}}
<div style="margin-top:16px;display:flex;justify-content:flex-end;">
    <button type="submit" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
        Simpan Semua Nilai
    </button>
</div>

</form>
@endsection