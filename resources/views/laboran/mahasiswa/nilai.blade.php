@extends('layouts.app')
@section('title','Nilai & Absensi')
@section('page-title','Pengentrian Nilai & Absensi')
@section('page-subtitle')
    {{ $mahasiswa->nama_mahasiswa }} — {{ $mahasiswa->nim_mahasiswa }}
@endsection

@section('content')
<div class="page-toolbar">
    <a href="{{ route('laboran.mahasiswa') }}" class="btn btn-outline">← Kembali ke Daftar</a>
    <div class="badge-group">
        <span class="badge badge-primary">{{ $mahasiswa->mataKuliah?->kode_mk }}</span>
        <span>{{ $mahasiswa->mataKuliah?->nama_mk }}</span>
    </div>
</div>

<form method="POST" action="{{ route('laboran.mahasiswa.nilai.update', $mahasiswa) }}">
@csrf

<div class="grid grid-2" style="align-items:start;">

    {{-- Kiri: Nilai --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Nilai Evaluasi --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Nilai Evaluasi</span><span class="badge badge-gray">Bobot 20%</span></div>
            <div class="card-body">
                <div class="grid grid-2">
                    @foreach([1,2,3,4] as $i)
                    <div class="form-group">
                        <label class="form-label">Evaluasi {{ $i }}</label>
                        <input type="number" name="nilai_evaluasi{{ $i }}" class="form-control"
                            min="0" max="100" step="0.01"
                            value="{{ $nilaiEvaluasi->{'nilai_evaluasi'.$i} ?? '' }}"
                            placeholder="0–100">
                    </div>
                    @endforeach
                </div>
                @if($nilaiEvaluasi->rata_rata)
                <div class="info-row"><span class="text-muted fs-13">Rata-rata evaluasi:</span><span class="fw-700 text-primary">{{ $nilaiEvaluasi->rata_rata }}</span></div>
                @endif
            </div>
        </div>

        {{-- Nilai Asistensi --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Nilai Asistensi</span><span class="badge badge-gray">Bobot 30%</span></div>
            <div class="card-body">
                <div class="grid grid-3">
                    @foreach([1,2,3] as $i)
                    <div class="form-group">
                        <label class="form-label">Asistensi {{ $i }}</label>
                        <input type="number" name="nilai_asistensi{{ $i }}" class="form-control"
                            min="0" max="100" step="0.01"
                            value="{{ $nilaiAsistensi->{'nilai_asistensi'.$i} ?? '' }}"
                            placeholder="0–100">
                    </div>
                    @endforeach
                </div>
                @if($nilaiAsistensi->rata_rata)
                <div class="info-row"><span class="text-muted fs-13">Rata-rata asistensi:</span><span class="fw-700 text-primary">{{ $nilaiAsistensi->rata_rata }}</span></div>
                @endif
            </div>
        </div>

        {{-- Nilai Ujian --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Nilai Ujian</span></div>
            <div class="card-body">
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Nilai MID <span class="text-muted">(Bobot 20%)</span></label>
                        <input type="number" name="nilai_MID" class="form-control"
                            min="0" max="100" step="0.01"
                            value="{{ $nilaiUjian->nilai_MID ?? '' }}" placeholder="0–100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nilai UAS <span class="text-muted">(Bobot 30%)</span></label>
                        <input type="number" name="nilai_UAS" class="form-control"
                            min="0" max="100" step="0.01"
                            value="{{ $nilaiUjian->nilai_UAS ?? '' }}" placeholder="0–100">
                    </div>
                </div>
            </div>
        </div>

        {{-- Rekap / Total --}}
        @if($rekap)
        <div class="card" style="border-color:var(--primary);background:var(--primary-pale);">
            <div class="card-body">
                <div class="rekap-grid">
                    <div class="rekap-item"><div class="rekap-label">Nilai Praktikum</div><div class="rekap-value">{{ $rekap->nilai_praktikum ?? '—' }}</div></div>
                    <div class="rekap-item"><div class="rekap-label">Nilai Asistensi</div><div class="rekap-value">{{ $rekap->nilai_asistensi ?? '—' }}</div></div>
                    <div class="rekap-item"><div class="rekap-label">MID</div><div class="rekap-value">{{ $rekap->nilai_MID ?? '—' }}</div></div>
                    <div class="rekap-item"><div class="rekap-label">UAS</div><div class="rekap-value">{{ $rekap->nilai_UAS ?? '—' }}</div></div>
                </div>
                <div style="text-align:center;margin-top:12px;padding-top:12px;border-top:1px solid var(--primary-soft);">
                    <div class="fs-12 text-muted">Total Keseluruhan Nilai</div>
                    <div style="font-size:2rem;font-weight:800;color:var(--primary);">{{ $rekap->total_keseluruhan_nilai ?? '—' }}</div>
                    @if($rekap->nilai_huruf)<span class="grade-badge badge-{{ strtolower($rekap->nilai_huruf) }}">{{ $rekap->nilai_huruf }}</span>@endif
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Kanan: Presensi per Pertemuan --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Presensi per Pertemuan</span>
            @php $totalAlpa = $presensiList->where('status_kehadiran','A')->count(); @endphp
            @if($totalAlpa >= 4)
            <span class="badge badge-danger">{{ $totalAlpa }} Alpha</span>
            @endif
        </div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead><tr><th>Pertemuan</th><th style="text-align:center;">Status</th><th>Catatan</th></tr></thead>
                <tbody>
                @for($i = 1; $i <= $jumlahPertemuan; $i++)
                @php $p = $presensiList->firstWhere('pertemuan_ke', $i); @endphp
                <tr>
                    <td class="fw-500">Pertemuan {{ $i }}</td>
                    <td style="text-align:center;">
                        @if($p)
                        <select name="presensi[{{ $p->id }}][status_kehadiran]" class="form-select form-select-sm">
                            @foreach(['H'=>'Hadir','I'=>'Izin','S'=>'Sakit','A'=>'Alpha'] as $val => $lbl)
                            <option value="{{ $val }}" {{ $p->status_kehadiran === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        @else
                        <span class="text-muted fs-12">Belum diisi</span>
                        @endif
                    </td>
                    <td>{{ $p?->catatan ?? '—' }}</td>
                </tr>
                @endfor
                </tbody>
            </table>
        </div>
    </div>

</div>

<div style="margin-top:20px;display:flex;gap:10px;justify-content:flex-end;">
    <a href="{{ route('laboran.mahasiswa') }}" class="btn btn-outline">Batal</a>
    <button type="submit" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
        Simpan Nilai & Presensi
    </button>
</div>

</form>
@endsection
