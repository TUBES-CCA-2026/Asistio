@extends('layouts.app')
@section('title','Dashboard Asisten')
@section('page-title','Dashboard Asisten')
@section('content')
@if($errors->any())
<script>document.addEventListener('DOMContentLoaded',()=>{ const o=document.getElementById('modalGantiPassword'); if(o) o.classList.add('is-open'); });</script>
@endif
<div class="hero-banner">
    <h1 class="hero-title">Halo, {{ auth()->user()->nama }}! 👋</h1>
    <p class="hero-subtitle">Pilih kelas yang Anda dampingi untuk memulai presensi atau pengisian nilai.</p>
</div>
@if($kelasList->isEmpty())
<div class="card"><div class="empty-state"><p>Anda belum ditugaskan ke kelas manapun. Hubungi laboran.</p></div></div>
@else
<div class="grid grid-2">
@foreach($kelasList as $kelas)
<div class="course-card">
    <div class="course-card-header">
        <div class="course-card-code">{{ $kelas->mataKuliah?->kode_mk }}</div>
        <div class="course-card-name">{{ $kelas->mataKuliah?->nama_mk }}</div>
        <div class="course-card-meta">
            <span class="fw-600">{{ $kelas->nama_kelas }}</span>
            @if($kelas->jadwal)<span>{{ $kelas->jadwal }}</span>@endif
            @if($kelas->ruangan)<span>{{ $kelas->ruangan->nama_ruangan }}</span>@endif
            <span>{{ $kelas->mahasiswa_count }} mahasiswa</span>
        </div>
    </div>
    <div class="course-card-footer">
        <a href="{{ route('asisten.presensi', $kelas) }}" class="btn btn-outline btn-sm">Presensi</a>
        <a href="{{ route('asisten.nilai', $kelas) }}" class="btn btn-outline btn-sm">Nilai</a>
        <button type="button" class="btn btn-outline btn-sm"
            onclick="document.getElementById('modalBobot{{ $kelas->id }}').classList.add('open');document.body.style.overflow='hidden'">
            Bobot
        </button>
        <a href="{{ route('asisten.rekap', $kelas) }}" class="btn btn-primary btn-sm">Rekap →</a>
    </div>
</div>

{{-- Modal Pembobotan --}}
<div id="modalBobot{{ $kelas->id }}" class="modal-overlay">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <span class="modal-title">Pembobotan — {{ $kelas->nama_kelas }}</span>
            <button type="button" class="modal-close"
                onclick="document.getElementById('modalBobot{{ $kelas->id }}').classList.remove('open');document.body.style.overflow=''">✕</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('asisten.bobot.simpan', $kelas) }}"
                  id="formBobot{{ $kelas->id }}">
                @csrf
                <p style="font-size:12px;color:var(--text-muted);margin-bottom:16px;line-height:1.6;">
                    Masukkan bobot tiap komponen dalam persen. Total harus <strong>100%</strong>.
                    Setelah disimpan, nilai akhir semua mahasiswa dihitung ulang otomatis.
                </p>

                @if($errors->has('bobot'))
                    <div class="alert alert-error" style="margin-bottom:12px;">{{ $errors->first('bobot') }}</div>
                @endif

                {{-- Seksi 1: Bobot sub-komponen praktikum --}}
                <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;padding:12px 14px;margin-bottom:14px;">
                    <div style="font-size:12px;font-weight:600;color:#1E40AF;margin-bottom:10px;">
                        🔬 Komposisi Nilai Praktikum (total harus 100%)
                    </div>
                    @foreach(['bobot_kegiatan'=>['label'=>'Kegiatan Praktikum','icon'=>'⚗️'],'bobot_evaluasi_praktikum'=>['label'=>'Evaluasi Praktikum','icon'=>'📋']] as $field=>$meta)
                    <div class="form-group" style="margin-bottom:8px;">
                        <label class="form-label" style="display:flex;align-items:center;gap:6px;font-size:12px;">
                            <span>{{ $meta['icon'] }}</span><span>{{ $meta['label'] }}</span>
                        </label>
                        <div style="position:relative;">
                            <input type="text" name="{{ $field }}"
                                   class="form-control bobot-sub-{{ $kelas->id }} input-bobot"
                                   inputmode="numeric"
                                   value="{{ old($field, $kelas->$field ?? \App\Models\Praktikum::BOBOT_DEFAULT[$field]) }}"
                                   required>
                            <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px;pointer-events:none;">%</span>
                        </div>
                    </div>
                    @endforeach
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 10px;border-radius:6px;background:var(--bg-page);border:1px solid var(--border);">
                        <span style="font-size:12px;font-weight:600;color:var(--text-secondary);">Total Sub</span>
                        <span id="totalBobotSub{{ $kelas->id }}" style="font-size:14px;font-weight:800;">100%</span>
                    </div>
                </div>

                {{-- Seksi 2: Bobot komponen nilai akhir --}}
                <div style="font-size:12px;font-weight:600;color:var(--text-secondary);margin-bottom:10px;">
                    📊 Komposisi Nilai Akhir (total harus 100%)
                </div>
                @foreach(['bobot_praktikum'=>['label'=>'Praktikum','icon'=>'🔬'],'bobot_asistensi'=>['label'=>'Asistensi','icon'=>'👨‍🏫'],'bobot_mid'=>['label'=>'MID','icon'=>'📝'],'bobot_uas'=>['label'=>'UAS','icon'=>'📋']] as $field=>$meta)
                <div class="form-group" style="margin-bottom:8px;">
                    <label class="form-label" style="display:flex;align-items:center;gap:6px;">
                        <span>{{ $meta['icon'] }}</span><span>{{ $meta['label'] }}</span>
                    </label>
                    <div style="position:relative;">
                        <input type="text" name="{{ $field }}"
                               class="form-control bobot-input-{{ $kelas->id }} input-bobot"
                               inputmode="numeric"
                               value="{{ old($field, $kelas->$field ?? \App\Models\Praktikum::BOBOT_DEFAULT[$field]) }}"
                               required>
                        <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px;pointer-events:none;">%</span>
                    </div>
                </div>
                @endforeach

                {{-- Live total nilai akhir --}}
                <div style="display:flex;align-items:center;justify-content:space-between;
                            padding:10px 14px;border-radius:8px;margin:12px 0;
                            background:var(--bg-page);border:1.5px solid var(--border);">
                    <span style="font-size:13px;font-weight:600;color:var(--text-secondary);">Total Nilai Akhir</span>
                    <span id="totalBobot{{ $kelas->id }}" style="font-size:18px;font-weight:800;">100%</span>
                </div>

                <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:4px;">
                    <button type="button" class="btn btn-outline"
                        onclick="document.getElementById('modalBobot{{ $kelas->id }}').classList.remove('open');document.body.style.overflow=''">
                        Batal
                    </button>
                    <button type="submit" id="btnSimpanBobot{{ $kelas->id }}" class="btn btn-primary">
                        Simpan &amp; Hitung Ulang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
</div>
@endif
<div id="modalGantiPassword" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">...</div>
        <div class="modal-body">
            <form method="POST" action="{{ route('asisten.ganti-password.update') }}">
                @csrf
                {{-- input password_lama, password_baru, konfirmasi --}}
            </form>
        </div>
    </div>
</div>
@endsection
