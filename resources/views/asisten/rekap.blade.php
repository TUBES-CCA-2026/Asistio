@extends('layouts.app')
@section('title','Rekap Presensi')
@section('page-title','Rekap Presensi')
@section('page-subtitle') {{ $praktikum->mataKuliah?->nama_mk }} — {{ $praktikum->nama_kelas }} @endsection
@section('content')
<div class="page-toolbar"><a href="{{ route('asisten.dashboard') }}" class="btn btn-outline">← Kembali</a></div>
<div class="card mb-4">
    <div class="card-header"><span class="card-title">Rekap Nilai Akhir</span></div>
    <div class="table-wrapper" style="scrollbar-width:none; -ms-overflow-style:none;"><table class="table">
        <thead><tr><th>NIM</th><th>Nama</th><th style="text-align:center;">Eval</th><th style="text-align:center;">Asist</th><th style="text-align:center;">MID</th><th style="text-align:center;">UAS</th><th style="text-align:center;">Nilai Akhir</th><th style="text-align:center;">Huruf</th><th style="text-align:center;">Kehadiran</th></tr></thead>
        <tbody>
        @forelse($mahasiswaList as $m)
        @php
            $r = $rekapNilaiMap[$m->id] ?? null;
            $jumlahAlpaPertemuan = $pp->where('status_kehadiran','A')->count();
            $alpaTinggi = $alpa >= \App\Models\Mahasiswa::BATAS_ALPA;
        @endphp
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
    <div style="overflow-x:auto; scrollbar-width:none; -ms-overflow-style:none;"><table class="table" style="min-width:800px;">
        <thead><tr><th>NIM</th><th>Nama</th>@for($i=1;$i<=14;$i++)<th style="text-align:center;width:32px;">P{{ $i }}</th>@endfor<th>H</th><th>A</th><th style="text-align:center;">Bukti</th></tr></thead>
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
                    <span class="badge-alpa-alert" title="Alpa {{ $jumlahAlpaPertemuan }}x di kelas ini — sudah mencapai batas {{ \App\Models\Mahasiswa::BATAS_ALPA }}x">⚠ Alpa {{ $jumlahAlpaPertemuan }}×</span>
                @endif
            </td>
            @for($j=1;$j<=14;$j++)@php $ps=$pp[$j]??null; @endphp
            <td style="text-align:center;padding:4px 2px;">@if($ps)<span class="status-chip status-chip-{{ $ps->status_kehadiran }}">{{ $ps->status_kehadiran }}</span>@else<span class="status-chip status-chip-empty">—</span>@endif</td>
            @endfor
            <td style="font-weight:700;color:var(--status-h);">{{ $pp->where('status_kehadiran','H')->count() }}</td>
            <td style="font-weight:700;color:var(--status-a);">{{ $pp->where('status_kehadiran','A')->count() }}</td>
            <td style="text-align:center;padding:4px;">
                @php $fotoList = $pp->whereIn('status_kehadiran',['S','I'])->filter(fn($x)=>$x->bukti_foto); @endphp
                @if($fotoList->count())
                    <button type="button" onclick="bukaModal('{{ $m->nama_mahasiswa }}',{{ json_encode($fotoList->map(fn($x)=>['p'=>$x->pertemuan_ke,'s'=>$x->status_kehadiran,'url'=>route('asisten.presensi.bukti.lihat',$x)])->values()) }})"
                            class="btn btn-sm btn-outline" style="font-size:11px;padding:3px 8px;">
                        📎 {{ $fotoList->count() }}
                    </button>
                @else
                    <span style="color:#9ca3af;font-size:12px;">—</span>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table></div>
</div>
 
<div class="card">
    <div class="card-header"><span class="card-title">Rekap Absensi Asistensi</span></div>
    <div style="overflow-x:auto; scrollbar-width:none; -ms-overflow-style:none;"><table class="table" style="min-width:400px;">
        <thead><tr><th>NIM</th><th>Nama</th><th style="text-align:center;width:90px;">Asistensi 1</th><th style="text-align:center;width:90px;">Asistensi 2</th><th style="text-align:center;width:90px;">Asistensi 3</th></tr></thead>
        <tbody>
        @forelse($mahasiswaList as $m)
        @php $pa = $presensiAsistensiAll[$m->id] ?? collect(); @endphp
        <tr>
            <td style="font-family:monospace;font-size:11px;">{{ $m->nim_mahasiswa }}</td>
            <td class="fw-500">{{ $m->nama_mahasiswa }}</td>
            @for($k=1;$k<=3;$k++)
            @php $pas = $pa[$k] ?? null; @endphp
            <td style="text-align:center;padding:4px 2px;">
                @if(!$pas)<span class="status-chip status-chip-empty">—</span>
                @elseif($pas->hadir)<span class="status-chip status-chip-H">H</span>
                @else<span class="status-chip status-chip-A">A</span>
                @endif
            </td>
            @endfor
        </tr>
        @empty<tr><td colspan="5"><div class="empty-state"><p>Belum ada mahasiswa di kelas ini.</p></div></td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
{{-- Modal Bukti Foto --}}
<div id="fotoModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.6);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:24px 20px;max-width:460px;width:95%;max-height:88vh;overflow-y:auto;position:relative;">
        <button onclick="tutupModal()" style="position:absolute;top:10px;right:14px;background:none;border:none;font-size:22px;cursor:pointer;color:#6b7280;">✕</button>
        <p id="modalNama" style="margin:0 0 14px;font-weight:600;font-size:14px;color:#1e293b;"></p>
        <div id="modalIsi"></div>
    </div>
</div>
<script>
function bukaModal(nama, items) {
    document.getElementById('modalNama').textContent = '📎 Bukti Foto — ' + nama;
    var isi = document.getElementById('modalIsi');
    isi.innerHTML = '';
    items.forEach(function(item) {
        var badge = item.s === 'S' ? '🤒 Sakit' : '📝 Izin';
        var div = document.createElement('div');
        div.style.cssText = 'margin-bottom:14px;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;';
        div.innerHTML = '<div style="padding:7px 12px;background:#f8fafc;font-size:12px;font-weight:600;border-bottom:1px solid #e5e7eb;">Pertemuan ' + item.p + ' · ' + badge + '</div>'
            + '<div style="padding:10px;text-align:center;">'
            + '<a href="' + item.url + '" target="_blank">'
            + '<img src="' + item.url + '" style="max-width:100%;max-height:260px;border-radius:6px;object-fit:contain;" onerror="this.style.display=\'none\'">'
            + '</a>'
            + '<div style="margin-top:6px;"><a href="' + item.url + '" target="_blank" style="font-size:11px;color:#3b82f6;">Buka di tab baru ↗</a></div>'
            + '</div>';
        isi.appendChild(div);
    });
    var m = document.getElementById('fotoModal');
    m.style.display = 'flex';
}
function tutupModal() { document.getElementById('fotoModal').style.display = 'none'; }
document.getElementById('fotoModal').addEventListener('click', function(e) { if(e.target===this) tutupModal(); });
document.addEventListener('keydown', function(e) { if(e.key==='Escape') tutupModal(); });
</script>

@endsection
