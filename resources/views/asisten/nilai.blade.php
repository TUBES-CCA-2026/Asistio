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
        {{-- Baris 2: tombol reset kolom (hanya ubah nilai di layar, belum simpan ke DB) --}}
        <tr>
            <th></th>
            @for($i = 1; $i <= 14; $i++)
            <th style="text-align:center;padding:4px 2px;">
                <button type="button"
                    class="btn btn-sm btn-danger"
                    style="font-size:10px;padding:2px 6px;line-height:1.4;"
                    data-reset-field="p{{ $i }}"
                    title="Set semua nilai P{{ $i }} menjadi 0 (belum tersimpan)">
                    Reset
                </button>
            </th>
            @endfor
            @foreach(['nilai_asistensi1'=>'Asist 1','nilai_asistensi2'=>'Asist 2','nilai_asistensi3'=>'Asist 3','nilai_MID'=>'MID','nilai_UAS'=>'UAS'] as $kolom => $label)
            <th style="text-align:center;padding:4px 2px;">
                <button type="button"
                    class="btn btn-sm btn-danger"
                    style="font-size:10px;padding:2px 6px;line-height:1.4;"
                    data-reset-field="{{ $kolom }}"
                    title="Set semua nilai {{ $label }} menjadi 0 (belum tersimpan)">
                    Reset
                </button>
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
            <td class="td-nilai">
                <input type="text" name="nilai[{{ $m->id }}][p{{ $i }}]"
                    class="form-control form-control-xs input-nilai"
                    inputmode="decimal"
                    value="{{ $n['evaluasi']->{'p'.$i} ?? '' }}"
                    placeholder="—">
            </td>
            @endfor
            {{-- Nilai Asistensi 1–3 --}}
            @foreach([1,2,3] as $i)
            <td class="td-nilai">
                <input type="text" name="nilai[{{ $m->id }}][nilai_asistensi{{ $i }}]"
                    class="form-control form-control-xs input-nilai"
                    inputmode="decimal"
                    value="{{ $n['asistensi']->{'nilai_asistensi'.$i} ?? '' }}"
                    placeholder="—">
            </td>
            @endforeach
            {{-- MID & UAS --}}
            <td class="td-nilai">
                <input type="text" name="nilai[{{ $m->id }}][nilai_MID]"
                    class="form-control form-control-xs input-nilai"
                    inputmode="decimal"
                    value="{{ $n['ujian']->nilai_MID ?? '' }}"
                    placeholder="—">
            </td>
            <td class="td-nilai">
                <input type="text" name="nilai[{{ $m->id }}][nilai_UAS]"
                    class="form-control form-control-xs input-nilai"
                    inputmode="decimal"
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

{{-- Indikator dirty + Tombol Simpan floating --}}
<div style="position:fixed;bottom:28px;right:28px;z-index:300;display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end;">
    <span id="errorHint" class="simpan-error-hint">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span id="errorHintCount">0</span> nilai tidak valid
    </span>
    <span id="dirtyHint" class="simpan-dirty-hint">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Ada perubahan belum disimpan
    </span>
    <button type="button" id="btnRevert" class="btn-revert">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
        Batalkan Perubahan
    </button>
    <button type="submit"
        class="btn btn-primary"
        style="box-shadow:0 4px 16px rgba(0,0,0,.18);
               display:flex;align-items:center;gap:8px;
               padding:12px 20px;font-size:14px;border-radius:999px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
        Simpan Semua Nilai
    </button>
</div>

</form>
@endsection