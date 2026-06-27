<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Rekap {{ $praktikum->nama_kelas }}</title>
<style>
    @page { margin: 28px 32px; }
    * { box-sizing: border-box; }
    body { font-family: "Helvetica", Arial, sans-serif; color: #1F2937; font-size: 11px; }

    .header { margin-bottom: 14px; border-bottom: 2px solid #4F46E5; padding-bottom: 10px; }
    .header h1 { font-size: 16px; margin: 0 0 2px 0; color: #111827; }
    .header .subtitle { font-size: 12px; color: #4F46E5; font-weight: 700; margin: 0 0 6px 0; }
    .meta-table { width: 100%; font-size: 10.5px; color: #4B5563; }
    .meta-table td { padding: 1px 0; }
    .meta-table td.label { width: 110px; color: #6B7280; }

    h2.section-title {
        font-size: 12.5px;
        color: #111827;
        margin: 18px 0 8px 0;
        padding-bottom: 4px;
        border-bottom: 1px solid #E5E7EB;
    }

    table.data { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
    table.data th, table.data td {
        border: 1px solid #E5E7EB;
        padding: 5px 6px;
        font-size: 10px;
    }
    table.data th {
        background: #EEF2FF;
        color: #3730A3;
        font-weight: 700;
        text-align: center;
    }
    table.data td.nim { font-family: "Courier New", monospace; font-size: 9.5px; }
    table.data td.nama { font-weight: 600; }
    table.data td.center { text-align: center; }
    table.data td.akhir { text-align: center; font-weight: 700; color: #4F46E5; }

    .grade-badge { display: inline-block; padding: 2px 7px; border-radius: 3px; font-size: 9.5px; font-weight: 700; }
    .badge-a, .badge-ab { background: #DCFCE7; color: #15803D; }
    .badge-b, .badge-bc { background: #DBEAFE; color: #1D4ED8; }
    .badge-c { background: #FEF3C7; color: #B45309; }
    .badge-d { background: #FED7AA; color: #C2410C; }
    .badge-e { background: #FEE2E2; color: #DC2626; }

    .chip { display: inline-block; width: 16px; height: 16px; line-height: 16px; border-radius: 50%; font-size: 8px; font-weight: 700; text-align: center; }
    .chip-H { background: #DCFCE7; color: #15803D; }
    .chip-I { background: #FEF3C7; color: #B45309; }
    .chip-S { background: #DBEAFE; color: #1D4ED8; }
    .chip-A { background: #FEE2E2; color: #DC2626; }
    .chip-empty { color: #D1D5DB; }

    .footer-note { margin-top: 18px; font-size: 9px; color: #9CA3AF; text-align: right; }
    table.presensi td, table.presensi th { padding: 3px 2px; }
</style>
</head>
<body>

<div class="header">
    <h1>Rekap Nilai &amp; Presensi Praktikan</h1>
    <p class="subtitle">{{ $praktikum->mataKuliah?->nama_mk }} — {{ $praktikum->nama_kelas }}</p>
    <table class="meta-table">
        <tr>
            <td class="label">Mata Kuliah</td>
            <td>: {{ $praktikum->mataKuliah?->kode_mk }} — {{ $praktikum->mataKuliah?->nama_mk }}</td>
            <td class="label">Dosen Pengampu</td>
            <td>: {{ $praktikum->dosen?->nama_dosen ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td>: {{ $praktikum->nama_kelas }}</td>
            <td class="label">Tanggal Export</td>
            <td>: {{ \Illuminate\Support\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
        <tr>
            <td class="label">Jumlah Praktikan</td>
            <td>: {{ $mahasiswaList->count() }} orang</td>
            <td class="label">Ruangan</td>
            <td>: {{ $praktikum->ruangan?->nama_ruangan ?? '—' }}</td>
        </tr>
    </table>
</div>

<h2 class="section-title">Rekap Nilai Akhir</h2>
<table class="data">
    <thead>
        <tr>
            <th style="width:14%;">NIM</th>
            <th style="width:26%;">Nama</th>
            <th>Eval</th>
            <th>Asist</th>
            <th>MID</th>
            <th>UAS</th>
            <th>Nilai Akhir</th>
            <th>Huruf</th>
            <th>Kehadiran</th>
        </tr>
    </thead>
    <tbody>
    @forelse($mahasiswaList as $m)
        @php $r = $m->rekap->firstWhere('praktikum_id', $praktikum->id); $alpa = $m->jumlahAlpaDiKelas($praktikum->id); @endphp
        <tr>
            <td class="nim">{{ $m->nim_mahasiswa }}</td>
            <td class="nama">{{ $m->nama_mahasiswa }}</td>
            <td class="center">{{ $r?->nilai_praktikum ?? '—' }}</td>
            <td class="center">{{ $r?->nilai_asistensi ?? '—' }}</td>
            <td class="center">{{ $r?->nilai_MID ?? '—' }}</td>
            <td class="center">{{ $r?->nilai_UAS ?? '—' }}</td>
            <td class="akhir">{{ $r?->nilai_akhir ?? '—' }}</td>
            <td class="center">
                @if($r?->nilai_huruf)
                    <span class="grade-badge badge-{{ strtolower($r->nilai_huruf) }}">{{ $r->nilai_huruf }}</span>
                @else — @endif
            </td>
            <td class="center">
                {{ $m->persentaseHadirDiKelas($praktikum->id) }}
                @if($alpa >= \App\Models\Mahasiswa::BATAS_ALPA) <span style="color:#DC2626;font-weight:700;">({{ $alpa }}&alpha;)</span>@endif
            </td>
        </tr>
    @empty
        <tr><td colspan="9" style="text-align:center;color:#9CA3AF;">Belum ada data nilai.</td></tr>
    @endforelse
    </tbody>
</table>

<h2 class="section-title">Rekap Presensi (per Pertemuan)</h2>
<table class="data presensi">
    <thead>
        <tr>
            <th style="width:13%;">NIM</th>
            <th style="width:19%;">Nama</th>
            @for($i = 1; $i <= 14; $i++)<th style="width:3.8%;">P{{ $i }}</th>@endfor
            <th style="width:4%;">H</th>
            <th style="width:4%;">A</th>
        </tr>
    </thead>
    <tbody>
    @foreach($mahasiswaList as $m)
        @php $pp = $presensiAll[$m->id] ?? collect(); @endphp
        <tr>
            <td class="nim">{{ $m->nim_mahasiswa }}</td>
            <td class="nama">{{ $m->nama_mahasiswa }}</td>
            @for($j = 1; $j <= 14; $j++)
                @php $ps = $pp[$j] ?? null; @endphp
                <td class="center">
                    @if($ps)
                        <span class="chip chip-{{ $ps->status_kehadiran }}">{{ $ps->status_kehadiran }}</span>
                    @else
                        <span class="chip chip-empty">–</span>
                    @endif
                </td>
            @endfor
            <td class="center" style="font-weight:700;color:#15803D;">{{ $pp->where('status_kehadiran','H')->count() }}</td>
            <td class="center" style="font-weight:700;color:#DC2626;">{{ $pp->where('status_kehadiran','A')->count() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2 class="section-title">Rekap Absensi Asistensi</h2>
<table class="data">
    <thead>
        <tr>
            <th style="width:14%;">NIM</th>
            <th style="width:26%;">Nama</th>
            <th style="width:15%;">Asistensi 1</th>
            <th style="width:15%;">Asistensi 2</th>
            <th style="width:15%;">Asistensi 3</th>
            <th style="width:15%;">Jumlah Hadir</th>
        </tr>
    </thead>
    <tbody>
    @forelse($mahasiswaList as $m)
        @php
            $pa = $presensiAsistensiAll[$m->id] ?? collect();
            $hadirAsistensi = $pa->where('hadir', true)->count();
        @endphp
        <tr>
            <td class="nim">{{ $m->nim_mahasiswa }}</td>
            <td class="nama">{{ $m->nama_mahasiswa }}</td>
            @for($k = 1; $k <= 3; $k++)
                @php $pas = $pa[$k] ?? null; @endphp
                <td class="center">
                    @if(!$pas)
                        <span class="chip chip-empty">–</span>
                    @elseif($pas->hadir)
                        <span class="chip chip-H">H</span>
                    @else
                        <span class="chip chip-A">A</span>
                    @endif
                </td>
            @endfor
            <td class="center" style="font-weight:700;color:#15803D;">{{ $hadirAsistensi }}/3</td>
        </tr>
    @empty
        <tr><td colspan="6" style="text-align:center;color:#9CA3AF;">Belum ada data absensi asistensi.</td></tr>
    @endforelse
    </tbody>
</table>

<p class="footer-note">Dokumen ini dibuat otomatis oleh sistem Asistio.</p>

</body>
</html>