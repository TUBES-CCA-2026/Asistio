@extends('layouts.app')
@section('title','Input Nilai')
@section('page-title','Input Nilai')
@section('page-subtitle') {{ $praktikum->mataKuliah?->nama_mk }} — {{ $praktikum->nama_kelas }} @endsection
@section('content')
<div class="page-toolbar"><a href="{{ route('asisten.dashboard') }}" class="btn btn-outline">← Kembali</a></div>

{{-- Form tunggal membungkus seluruh tabel --}}
<form method="POST" action="{{ route('asisten.nilai.simpan-semua', $praktikum) }}"
      data-bobot-kegiatan="{{ $praktikum->bobot_kegiatan ?? 50 }}"
      data-bobot-evaluasi="{{ $praktikum->bobot_evaluasi_praktikum ?? 50 }}">
@csrf
<div class="card"><div class="table-wrapper" style="overflow-x:auto;">
    <table class="table" style="min-width:1900px;">
        <thead>
        {{-- Baris 1: label kolom --}}
        <tr>
            <th>Mahasiswa</th>
            @for($i = 1; $i <= 14; $i++)
            <th colspan="3" style="text-align:center;border-left:2px solid var(--border);">P{{ $i }}</th>
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
            <th style="text-align:center;padding:1px;border-left:2px solid var(--border);">
                <button type="button" class="btn-reset-mini"
                    data-reset-field="p{{ $i }}_kegiatan"
                    title="Set semua nilai Kegiatan P{{ $i }} menjadi kosong (belum tersimpan)">Keg</button>
            </th>
            <th style="text-align:center;padding:1px;">
                <button type="button" class="btn-reset-mini"
                    data-reset-field="p{{ $i }}_evaluasi"
                    title="Set semua nilai Evaluasi P{{ $i }} menjadi kosong (belum tersimpan)">Eval</button>
            </th>
            <th style="text-align:center;padding:2px 1px;font-size:10px;font-weight:500;color:var(--text-muted);">Nilai</th>
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
        @php
            $n        = $nilaiMap[$m->id];
            $jmlAlpa  = $alpaMap[$m->id] ?? 0;
            $adaAlpa  = $jmlAlpa >= $batasAlpa;
        @endphp
        <tr class="{{ $adaAlpa ? 'row-alpa-alert' : '' }}">
            <td>
                <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                    <span class="fw-600 fs-13">{{ $m->nama_mahasiswa }}</span>
                    @if($adaAlpa)
                        <span class="badge-alpa-alert" title="{{ $jmlAlpa }}x Alpha — melebihi batas {{ $batasAlpa }}">
                            ⚠ {{ $jmlAlpa }}x Alpa
                        </span>
                    @endif
                </div>
                <div class="fs-11 text-muted">{{ $m->nim_mahasiswa }}</div>
            </td>
            {{-- Nilai P1–P14: Kegiatan | Evaluasi | Nilai (read-only) --}}
            @for($i = 1; $i <= 14; $i++)
            @php
                $kegiatan = $n['evaluasi']->{'p'.$i.'_kegiatan'} ?? null;
                $evaluasi = $n['evaluasi']->{'p'.$i.'_evaluasi'} ?? null;
                $bobotKeg = ($praktikum->bobot_kegiatan ?? 50) / 100;
                $bobotEval= ($praktikum->bobot_evaluasi_praktikum ?? 50) / 100;
                // Hitung nilai P dari kegiatan dan evaluasi — kosong jika keduanya null
                if ($kegiatan !== null || $evaluasi !== null) {
                    // Kosong = 0, selalu kalkulasi dengan bobot
                    $nilaiP = round((($kegiatan ?? 0) * $bobotKeg) + (($evaluasi ?? 0) * $bobotEval), 2);
                } else {
                    $nilaiP = null;
                }
            @endphp
            <td class="td-nilai" style="border-left:2px solid var(--border);">
                <input type="text" name="nilai[{{ $m->id }}][p{{ $i }}_kegiatan]"
                    class="form-control form-control-xs input-nilai input-sub-nilai"
                    inputmode="decimal"
                    data-mhs="{{ $m->id }}" data-pertemuan="{{ $i }}" data-sub="kegiatan"
                    data-asal="{{ $n['evaluasi']->{'p'.$i.'_kegiatan'} ?? '' }}"
                    value="{{ $n['evaluasi']->{'p'.$i.'_kegiatan'} ?? '' }}"
                    placeholder="—">
            </td>
            <td class="td-nilai">
                <input type="text" name="nilai[{{ $m->id }}][p{{ $i }}_evaluasi]"
                    class="form-control form-control-xs input-nilai input-sub-nilai"
                    inputmode="decimal"
                    data-mhs="{{ $m->id }}" data-pertemuan="{{ $i }}" data-sub="evaluasi"
                    data-asal="{{ $n['evaluasi']->{'p'.$i.'_evaluasi'} ?? '' }}"
                    value="{{ $n['evaluasi']->{'p'.$i.'_evaluasi'} ?? '' }}"
                    placeholder="—">
            </td>
            <td class="td-nilai" style="background:var(--bg-page);">
                <input type="text"
                    id="nilai_p{{ $i }}_{{ $m->id }}"
                    class="form-control form-control-xs"
                    style="background:transparent;cursor:default;text-align:center;font-weight:600;"
                    readonly
                    value="{{ $nilaiP !== null ? number_format($nilaiP, 2) : '' }}"
                    placeholder="—"
                    tabindex="-1">
            </td>
            @endfor
            {{-- Nilai Asistensi 1–3 --}}
            @foreach([1,2,3] as $i)
            <td class="td-nilai">
                <input type="text" name="nilai[{{ $m->id }}][nilai_asistensi{{ $i }}]"
                    class="form-control form-control-xs input-nilai"
                    inputmode="decimal"
                    data-asal="{{ $n['asistensi']->{'nilai_asistensi'.$i} ?? '' }}"
                    value="{{ $n['asistensi']->{'nilai_asistensi'.$i} ?? '' }}"
                    placeholder="—">
            </td>
            @endforeach
            {{-- MID & UAS --}}
            <td class="td-nilai">
                <input type="text" name="nilai[{{ $m->id }}][nilai_MID]"
                    class="form-control form-control-xs input-nilai"
                    inputmode="decimal"
                    data-asal="{{ $n['ujian']->nilai_MID ?? '' }}"
                    value="{{ $n['ujian']->nilai_MID ?? '' }}"
                    placeholder="—">
            </td>
            <td class="td-nilai">
                <input type="text" name="nilai[{{ $m->id }}][nilai_UAS]"
                    class="form-control form-control-xs input-nilai"
                    inputmode="decimal"
                    data-asal="{{ $n['ujian']->nilai_UAS ?? '' }}"
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
        <tr><td colspan="{{ 1 + 14*3 + 5 + 1 }}"><div class="empty-state"><p>Belum ada mahasiswa.</p></div></td></tr>
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
    <button type="submit" id="btnSimpanNilai"
    style="box-shadow:0 4px 16px rgba(0,0,0,.18);
           display:flex;align-items:center;gap:8px;
           padding:12px 20px;font-size:14px;border-radius:999px;
           transition:background .25s,color .25s;
           background:var(--primary);color:#fff;border:none;cursor:pointer;">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
    <span class="btn-label">Simpan Semua Nilai</span>
</button>

{{-- Indikator autosave --}}
<div id="autosaveStatus"
     style="position:fixed;bottom:88px;right:28px;z-index:300;
            font-size:12px;color:var(--text-muted);text-align:right;
            transition:opacity .3s;opacity:0;">
</div>
</div>

</form>

<script>
(function () {
    var form  = document.querySelector('form[action*="simpan-semua"]');
    var btn   = document.getElementById('btnSimpanNilai');
    var badge = document.getElementById('dirtyBadge');
    var info  = document.getElementById('dirtyInfo');
    var hintEl= document.getElementById('dirtyHint');
    if (!form) return;

    // Kunci unik per kelas
    var DRAFT_KEY = 'draft_nilai_{{ $praktikum->id }}';

    // Deteksi jenis navigasi SEBELUM apapun dijalankan
    var navType  = (performance.getEntriesByType('navigation')[0] || {}).type || 'navigate';
    var isReload = navType === 'reload';

    // ── Simpan draft — hanya field yang BERBEDA dari nilai server ─────
    function simpanDraft() {
        var draft = {};
        form.querySelectorAll('.input-nilai').forEach(function (el) {
            if (!el.name) return;
            var val  = el.value === '—' ? '' : el.value;
            var asal = el.dataset.asal || '';
            // Hanya simpan yang benar-benar berubah
            if (val !== asal) draft[el.name] = val;
        });
        try {
            if (Object.keys(draft).length > 0) {
                sessionStorage.setItem(DRAFT_KEY, JSON.stringify(draft));
            } else {
                sessionStorage.removeItem(DRAFT_KEY);
            }
        } catch (e) {}
    }

    // ── Pulihkan draft setelah refresh ───────────────────────────────
    function pulihkanDraft() {
        var raw;
        try { raw = sessionStorage.getItem(DRAFT_KEY); } catch (e) { return; }
        if (!raw) return;

        var draft;
        try { draft = JSON.parse(raw); } catch (e) { return; }

        var jumlahDraft = 0;
        Object.keys(draft).forEach(function (name) {
            var el = form.querySelector('[name="' + name + '"]');
            if (!el || !el.classList.contains('input-nilai')) return;

            var nilaiDraft = draft[name];
            var asal       = el.dataset.asal || '';

            // Hanya pulihkan jika masih berbeda dari nilai server saat ini
            if (nilaiDraft === asal) return;

            if (nilaiDraft === '') {
                el.value = '—';
                el.classList.add('nilai-kosong');
            } else {
                el.value = nilaiDraft;
                el.classList.remove('nilai-kosong');
            }
            el.classList.add('is-draft', 'nilai-dirty');
            jumlahDraft++;
        });

        updateUI(jumlahDraft);
    }

    // ── Hapus draft ───────────────────────────────────────────────────
    function hapusDraft() {
        try { sessionStorage.removeItem(DRAFT_KEY); } catch (e) {}
        form.querySelectorAll('.input-nilai').forEach(function (el) {
            el.classList.remove('is-draft', 'nilai-dirty');
        });
        updateUI(0);
    }

    // ── Update indikator UI ───────────────────────────────────────────
    function updateUI(jumlah) {
        var ada    = jumlah > 0;
        var revert = document.getElementById('btnRevert');
        if (badge)  badge.classList.toggle('show', ada);
        if (hintEl) hintEl.classList.toggle('show', ada);
        if (revert) revert.classList.toggle('show', ada);
        if (btn) {
            btn.style.background = ada ? '#F59E0B' : '';
            var label = btn.querySelector('.btn-label');
            if (label) label.textContent = ada ? jumlah + ' nilai belum disimpan' : 'Simpan Semua Nilai';
        }
    }

    function hitungDirty() {
        return form.querySelectorAll('.input-nilai.is-draft').length;
    }

    // ── Hitung ulang kolom Nilai P dari kegiatan & evaluasi ───────────
    function hitungSemuaNilaiP() {
        var bobotKeg  = parseFloat(form.dataset.bobotKegiatan  || 50) / 100;
        var bobotEval = parseFloat(form.dataset.bobotEvaluasi  || 50) / 100;

        form.querySelectorAll('.input-sub-nilai[data-sub="kegiatan"]').forEach(function (elKeg) {
            var mhs       = elKeg.dataset.mhs;
            var pertemuan = elKeg.dataset.pertemuan;
            var elEval    = form.querySelector('.input-sub-nilai[data-mhs="' + mhs + '"][data-pertemuan="' + pertemuan + '"][data-sub="evaluasi"]');
            var elNilai   = document.getElementById('nilai_p' + pertemuan + '_' + mhs);
            if (!elNilai) return;

            var keg  = elKeg.value  === '—' ? '' : elKeg.value.trim();
            var eval_ = elEval ? (elEval.value === '—' ? '' : elEval.value.trim()) : '';

            if (keg === '' && eval_ === '') {
                elNilai.value = '';
                return;
            }
            var kegN  = keg  !== '' ? parseFloat(keg)  : null;
            var evalN = eval_ !== '' ? parseFloat(eval_) : null;

            var hasil;
            if (kegN !== null && evalN !== null) {
                hasil = Math.round(((kegN * bobotKeg) + (evalN * bobotEval)) * 100) / 100;
            } else if (kegN !== null) {
                hasil = Math.round((kegN * bobotKeg) * 100) / 100;
            } else {
                hasil = Math.round((evalN * bobotEval) * 100) / 100;
            }
            elNilai.value = isNaN(hasil) ? '' : hasil.toFixed(2);
        });
    }

    // ── Init ──────────────────────────────────────────────────────────
    if (isReload) {
        pulihkanDraft();
    } else {
        hapusDraft();
    }
    // Selalu hitung ulang kolom Nilai P saat load — baik refresh maupun
    // navigasi biasa — agar tampilan konsisten dengan sub-kolom di DB
    hitungSemuaNilaiP();

    // Jalankan initNilaiDisplay SETELAH draft dipulihkan
    // agar asalNilai dibaca dari data-asal (server), bukan dari el.value
    // yang sudah terisi draft
    if (window._initNilaiDisplay) {
        window._initNilaiDisplay();
        window._updateDirtyHint();
    }

    // ── Event input: tandai dirty + simpan draft + hitung Nilai P ─────
    form.querySelectorAll('.input-nilai').forEach(function (el) {
        el.addEventListener('input', function () {
            var sekarang = this.value === '—' ? '' : this.value;
            var asal     = this.dataset.asal || '';
            if (sekarang !== asal) {
                this.classList.add('is-draft', 'nilai-dirty');
            } else {
                this.classList.remove('is-draft', 'nilai-dirty');
                if (sekarang === '') {
                    this.value = '—';
                    this.classList.add('nilai-kosong');
                }
            }
            // Hitung ulang kolom Nilai P jika yang diubah adalah kegiatan/evaluasi
            if (this.classList.contains('input-sub-nilai')) {
                var mhs       = this.dataset.mhs;
                var pertemuan = this.dataset.pertemuan;
                var bobotKeg  = parseFloat(form.dataset.bobotKegiatan || 50) / 100;
                var bobotEval = parseFloat(form.dataset.bobotEvaluasi || 50) / 100;
                var elKeg  = form.querySelector('.input-sub-nilai[data-mhs="' + mhs + '"][data-pertemuan="' + pertemuan + '"][data-sub="kegiatan"]');
                var elEval = form.querySelector('.input-sub-nilai[data-mhs="' + mhs + '"][data-pertemuan="' + pertemuan + '"][data-sub="evaluasi"]');
                var elNilai = document.getElementById('nilai_p' + pertemuan + '_' + mhs);
                if (elNilai && elKeg && elEval) {
                    var keg   = elKeg.value  === '—' ? '' : elKeg.value.trim();
                    var eval_ = elEval.value === '—' ? '' : elEval.value.trim();
                    if (keg === '' && eval_ === '') {
                        elNilai.value = '';
                    } else {
                        var kegN  = keg   !== '' ? parseFloat(keg)   : null;
                        var evalN = eval_ !== '' ? parseFloat(eval_)  : null;
                        var hasil;
                        if (kegN !== null && evalN !== null) {
                            hasil = Math.round(((kegN * bobotKeg) + (evalN * bobotEval)) * 100) / 100;
                        } else if (kegN !== null) {
                            hasil = Math.round((kegN * bobotKeg) * 100) / 100;
                        } else {
                            hasil = Math.round((evalN * bobotEval) * 100) / 100;
                        }
                        elNilai.value = isNaN(hasil) ? '' : hasil.toFixed(2);
                    }
                }
            }
            simpanDraft();
            updateUI(hitungDirty());
        });
    });

    // ── Submit → hapus draft ──────────────────────────────────────────
    form.addEventListener('submit', function () {
        isReload = true; // cegah beforeunload dialog saat redirect
        hapusDraft();
    });

    // ── Batalkan Perubahan ────────────────────────────────────────────
    // Konfirmasi ditangani oleh modal universal di app.js (data-konfirm)
    // Blade hanya perlu mendengarkan event 'konfirm-revert' yang dikirim modal
    document.addEventListener('konfirm-revert', function () {
        form.querySelectorAll('.input-nilai').forEach(function (el) {
            var asal = el.dataset.asal || '';
            el.classList.remove('is-draft', 'nilai-dirty');
            if (asal === '') {
                el.value = '—';
                el.classList.add('nilai-kosong');
            } else {
                el.value = asal;
                el.classList.remove('nilai-kosong');
            }
        });
        hapusDraft();
        hitungSemuaNilaiP();   // ← hitung ulang kolom Nilai P ke nilai asli setelah revert
    });

    // ── beforeunload: dialog HANYA saat navigasi keluar, bukan refresh ─
    window.addEventListener('beforeunload', function (e) {
        // isReload = true jika: halaman baru saja di-reload, atau submit terjadi
        if (isReload)         return;
        if (hitungDirty() === 0) return;
        e.preventDefault();
        e.returnValue = 'Ada nilai yang belum disimpan. Yakin ingin meninggalkan halaman?';
    });

    // Set isReload = true saat user tekan tombol refresh
    // (keyboard shortcut maupun tombol browser)
    document.addEventListener('keydown', function (e) {
        if (e.key === 'F5' || (e.ctrlKey && e.key === 'r') || (e.metaKey && e.key === 'r')) {
            isReload = true;
        }
    });
})();
</script>
@endsection