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
    <table class="table" style="min-width:{{ max(900, 200 + $jumlahPertemuan * 140 + 400) }}px;">
        <thead>
        {{-- Baris 1: label kolom --}}
        <tr>
            <th>Mahasiswa</th>
            @for($i = 1; $i <= $jumlahPertemuan; $i++)
            @php $pi = $pertemuanInfo[$i] ?? null; @endphp
            <th colspan="3" style="text-align:center;border-left:2px solid var(--border);min-width:120px;" data-pertemuan-th="{{ $i }}">
                <div style="display:flex;align-items:center;justify-content:center;gap:4px;">
                    <span style="font-weight:700;">P{{ $i }}</span>
                    @if($i === $jumlahPertemuan)
                    <button type="button"
                        class="btn-hapus-pertemuan"
                        data-pertemuan="{{ $i }}"
                        title="Hapus pertemuan P{{ $i }} beserta nilainya"
                        style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:4px;
                               padding:1px 5px;font-size:10px;cursor:pointer;line-height:1.4;flex-shrink:0;">✕</button>
                    @endif
                </div>
                @if($pi)
                <div style="font-size:10px;color:var(--text-muted);font-weight:400;">{{ $pi->hari }}, {{ $pi->tanggal?->isoFormat('D MMM Y') }}</div>
                @endif
                <input type="text"
                    class="input-materi-pertemuan"
                    data-ke="{{ $i }}"
                    value="{{ $pi?->materi ?? '' }}"
                    placeholder="Tulis materi..."
                    style="width:100%;margin-top:3px;font-size:10px;font-weight:400;
                           border:1px dashed var(--border);border-radius:4px;
                           padding:2px 5px;background:transparent;color:var(--text-muted);
                           text-align:center;outline:none;cursor:text;">
            </th>
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
            @for($i = 1; $i <= $jumlahPertemuan; $i++)
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
                        <span class="badge-alpa-alert" title="{{ $jmlAlpa }}x Tidak Hadir (A/I/S) — melebihi batas {{ $batasAlpa }}">
                            ⚠ {{ $jmlAlpa }}x Tdk Hadir
                        </span>
                    @endif
                </div>
                <div class="fs-11 text-muted">{{ $m->nim_mahasiswa }}</div>
            </td>
            {{-- Nilai P1–jumlahPertemuan: Kegiatan | Evaluasi | Nilai (read-only) --}}
            @for($i = 1; $i <= $jumlahPertemuan; $i++)
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
            {{-- Kolom baru disisipkan sebelum Asistensi via JS --}}

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

{{-- Tombol Tambah Pertemuan dipindah ke dalam tabel — tidak floating --}}

{{-- Tombol floating Reset Semua Nilai (kanan atas — terpisah dari form Simpan) --}}
<button type="button" data-modal-open="modalResetSemuaNilai"
    style="position:fixed;top:88px;right:28px;z-index:300;
           box-shadow:0 4px 16px rgba(0,0,0,.18);
           display:flex;align-items:center;gap:8px;
           padding:12px 20px;font-size:14px;border-radius:999px;
           background:#DC2626;color:#fff;border:none;cursor:pointer;">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <polyline points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round"
        d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6M10 11v6M14 11v6M9 6V4h6v2"/>
    </svg>
    Reset Semua Nilai
</button>

{{-- Modal Reset Semua Nilai --}}
<div id="modalResetSemuaNilai" class="modal-overlay"><div class="modal" style="max-width:440px;">
    <div class="modal-header" style="background:#FEF2F2;border-bottom:1px solid #FECACA;">
        <span class="modal-title" style="color:#B91C1C;">⚠ Reset Semua Nilai — Kelas {{ $praktikum->nama_kelas }}</span>
        <button data-modal-close="modalResetSemuaNilai" class="modal-close">✕</button>
    </div>
    <div class="modal-body">
        <p style="font-size:14px;color:#374151;margin:0 0 12px;">Tindakan ini akan mereset <strong>seluruh nilai mahasiswa</strong> di kelas ini ke 0, meliputi:</p>
        <ul style="font-size:13px;color:#6B7280;margin:0 0 16px;padding-left:20px;line-height:1.8;">
            <li>Nilai Kegiatan & Evaluasi Praktikum, semua pertemuan → <strong>null</strong></li>
            <li>Nilai Asistensi 1, 2, dan 3 → <strong>null</strong></li>
            <li>Nilai MID dan UAS → <strong>null</strong></li>
            <li>Rekap nilai akhir dihitung ulang otomatis</li>
        </ul>
        <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:6px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#B91C1C;">
            <strong>Tindakan ini tidak dapat dibatalkan.</strong> Semua nilai dihapus dari database. Data pertemuan (hari/tanggal/materi) dan presensi tidak ikut terhapus.
        </div>
        <p style="font-size:13px;color:#374151;margin:0 0 8px;">Ketik <strong>RESET SEMUA</strong> untuk konfirmasi:</p>
        <input type="text" id="konfirmasiResetNilai" class="form-control" placeholder="RESET SEMUA" autocomplete="off">
    </div>
    <div style="display:flex;gap:8px;justify-content:flex-end;padding:16px;">
        <button type="button" data-modal-close="modalResetSemuaNilai" class="btn btn-outline">Batal</button>
        <form method="POST" action="{{ route('asisten.nilai.reset-semua', $praktikum) }}" id="formResetSemuaNilai">
            @csrf
            <button type="submit" id="btnKonfirmResetNilai" class="btn btn-danger" disabled>Reset Semua</button>
        </form>
    </div>
</div></div>
<script>
(function () {
    var input = document.getElementById('konfirmasiResetNilai');
    var btn   = document.getElementById('btnKonfirmResetNilai');
    var frm   = document.getElementById('formResetSemuaNilai');
    if (!input) return;
    input.addEventListener('input', function () {
        btn.disabled = input.value.trim() !== 'RESET SEMUA';
    });
    frm.addEventListener('submit', function (e) {
        if (input.value.trim() !== 'RESET SEMUA') { e.preventDefault(); return; }
        btn.disabled = true; btn.textContent = 'Mereset…';
    });
    document.getElementById('modalResetSemuaNilai')?.addEventListener('modal-close', function () {
        input.value = ''; btn.disabled = true;
    });
})();
</script>

<script>
// ── Tambah & Hapus Pertemuan ─────────────────────────────────────────────────
(function () {
    var DRAFT_KEY_P   = 'draft_pertemuan_{{ $praktikum->id }}';
    var jumlahSaatIni = {{ $jumlahPertemuan }};
    var HAPUS_URL_TMPL = '{{ route('asisten.nilai.hapus-pertemuan', ['praktikum' => $praktikum->id, 'pertemuan' => '__P__']) }}';
    var CSRF = document.querySelector('input[name=_token]')?.value || '';

    // ── Tombol Tambah Pertemuan ────────────────────────────────────────
    function updateTombolTambah() {
        var oldBar = document.getElementById('pertemuanActionBar');
        if (oldBar) oldBar.remove();

        if (jumlahSaatIni >= 14) return;

        var tableWrapper = document.querySelector('form .table-wrapper');
        if (!tableWrapper) return;

        var bar = document.createElement('div');
        bar.id = 'pertemuanActionBar';
        bar.style.cssText = 'position:relative;height:38px;';

        var newBtn = document.createElement('button');
        newBtn.type = 'button';
        newBtn.id   = 'btnTambahPertemuan';
        newBtn.style.cssText = 'position:absolute;top:8px;transform:translateX(-50%);'
            + 'background:#cffafe;border:1px solid #67e8f9;color:#0e7490;'
            + 'border-radius:4px;padding:2px 8px;font-size:11px;font-weight:600;'
            + 'cursor:pointer;white-space:nowrap;line-height:1.5;z-index:5;';
        newBtn.textContent = '+';
        newBtn.addEventListener('click', handleTambah);

        bar.appendChild(newBtn);
        tableWrapper.insertAdjacentElement('beforebegin', bar);
        requestAnimationFrame(posisikanTombolTambah);
    }

    function posisikanTombolTambah() {
        var btn = document.getElementById('btnTambahPertemuan');
        var bar = document.getElementById('pertemuanActionBar');
        var tableWrapper = document.querySelector('form .table-wrapper');
        if (!btn || !bar || !tableWrapper) return;

        var theadRow1 = document.querySelector('form table thead tr');
        var thsPertemuan = theadRow1 ? Array.from(theadRow1.querySelectorAll('th[data-pertemuan-th]')) : [];
        var thTerakhir   = thsPertemuan[thsPertemuan.length - 1];

        // Belum ada satu pun kolom pertemuan — taruh tombol di tengah atas
        // (tidak ada th terakhir untuk dijadikan acuan posisi).
        if (!thTerakhir) {
            var barRectKosong = bar.getBoundingClientRect();
            btn.style.display = 'inline-flex';
            btn.style.left    = (barRectKosong.width / 2) + 'px';
            return;
        }

        var thRect   = thTerakhir.getBoundingClientRect();
        var barRect  = bar.getBoundingClientRect();
        var wrapRect = tableWrapper.getBoundingClientRect();

        var terlihat = thRect.right > wrapRect.left && thRect.left < wrapRect.right;
        btn.style.display = terlihat ? 'inline-flex' : 'none';
        if (!terlihat) return;

        var left = thRect.left - barRect.left + (thRect.width / 2);
        left = Math.max(60, Math.min(left, barRect.width - 60));
        btn.style.left = left + 'px';
    }

    // Restore draft pertemuan saat refresh
    var navType = (performance.getEntriesByType('navigation')[0] || {}).type || 'navigate';
    if (navType === 'reload') {
        try {
            var draftP = JSON.parse(sessionStorage.getItem(DRAFT_KEY_P) || 'null');
            if (draftP && draftP.jumlah > jumlahSaatIni) {
                for (var p = jumlahSaatIni + 1; p <= draftP.jumlah; p++) {
                    tambahKolomClient(p, '', '', true);
                }
                jumlahSaatIni = draftP.jumlah;
            }
        } catch (e) {}
    } else {
        try { sessionStorage.removeItem(DRAFT_KEY_P); } catch (e) {}
    }

    updateTombolTambah();
    pasangTombolHapus(); // pasang listener untuk th yang di-render server

    var tableWrapperEl = document.querySelector('form .table-wrapper');
    if (tableWrapperEl) tableWrapperEl.addEventListener('scroll', posisikanTombolTambah);
    window.addEventListener('resize', posisikanTombolTambah);

    // ── Tambah kolom secara client-side ───────────────────────────────
    function tambahKolomClient(nPertemuan, hari, tanggal, isDraft) {
        var theadRows = document.querySelectorAll('form table thead tr');
        var theadRow1 = theadRows[0] || null;
        var theadRow2 = theadRows[1] || null;

        if (theadRow1) {
            var thAsist1 = Array.from(theadRow1.querySelectorAll('th')).find(function(t) {
                return t.textContent.trim() === 'Asist 1'
                    || (t.childNodes[0] && t.childNodes[0].textContent && t.childNodes[0].textContent.trim() === 'Asist 1');
            });
            if (thAsist1) {
                // Hapus tombol hapus dari kolom yang SEBELUMNYA terakhir
                var prevBtnHapus = theadRow1.querySelector('.btn-hapus-pertemuan');
                if (prevBtnHapus) prevBtnHapus.remove();

                var th = document.createElement('th');
                th.colSpan = 3;
                th.setAttribute('data-pertemuan-th', nPertemuan); // ← wajib agar hapusKolomClient bisa menemukannya
                th.style.cssText = 'text-align:center;border-left:2px solid var(--border);min-width:120px;';

                var divJudul = document.createElement('div');
                divJudul.style.cssText = 'display:flex;align-items:center;justify-content:center;gap:4px;';
                var spanLabel = document.createElement('span');
                spanLabel.style.fontWeight = '700';
                spanLabel.textContent = 'P' + nPertemuan;
                divJudul.appendChild(spanLabel);

                // Tombol hapus untuk kolom baru (karena ini pasti jadi kolom terakhir)
                var btnH = document.createElement('button');
                btnH.type = 'button';
                btnH.className = 'btn-hapus-pertemuan';
                btnH.dataset.pertemuan = nPertemuan;
                btnH.title = 'Hapus pertemuan P' + nPertemuan + ' beserta nilainya';
                btnH.style.cssText = 'background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;'
                    + 'border-radius:4px;padding:1px 5px;font-size:10px;cursor:pointer;line-height:1.4;flex-shrink:0;';
                btnH.textContent = '✕';
                divJudul.appendChild(btnH);
                th.appendChild(divJudul);

                if (hari) {
                    var divInfo = document.createElement('div');
                    divInfo.style.cssText = 'font-size:10px;color:var(--text-muted);font-weight:400;';
                    divInfo.textContent = hari + (tanggal ? ', ' + tanggal : '');
                    th.appendChild(divInfo);
                }

                var inpMateri = document.createElement('input');
                inpMateri.type = 'text';
                inpMateri.className = 'input-materi-pertemuan';
                inpMateri.dataset.ke = nPertemuan;
                inpMateri.value = '';
                inpMateri.placeholder = 'Tulis materi...';
                inpMateri.style.cssText = 'width:100%;margin-top:3px;font-size:10px;font-weight:400;'
                    + 'border:1px dashed var(--border);border-radius:4px;padding:2px 5px;'
                    + 'background:transparent;color:var(--text-muted);text-align:center;outline:none;cursor:text;';
                th.appendChild(inpMateri);

                if (isDraft) th.style.opacity = '0.7';
                theadRow1.insertBefore(th, thAsist1);
                pasangTombolHapus(); // pasang listener pada tombol baru
            }
        }

        if (theadRow2) {
            var thResetAsist1 = Array.from(theadRow2.querySelectorAll('th')).find(function(t) {
                return t.querySelector('button[data-reset-field="nilai_asistensi1"]');
            });
            if (thResetAsist1) {
                var thKeg = document.createElement('th');
                thKeg.style.cssText = 'text-align:center;padding:1px;border-left:2px solid var(--border);';
                var btnKeg = document.createElement('button');
                btnKeg.type = 'button';
                btnKeg.className = 'btn-reset-mini';
                btnKeg.dataset.resetField = 'p' + nPertemuan + '_kegiatan';
                btnKeg.title = 'Set semua nilai Kegiatan P' + nPertemuan + ' menjadi kosong';
                btnKeg.textContent = 'Keg';
                thKeg.appendChild(btnKeg);
                theadRow2.insertBefore(thKeg, thResetAsist1);

                var thEval = document.createElement('th');
                thEval.style.cssText = 'text-align:center;padding:1px;';
                var btnEval = document.createElement('button');
                btnEval.type = 'button';
                btnEval.className = 'btn-reset-mini';
                btnEval.dataset.resetField = 'p' + nPertemuan + '_evaluasi';
                btnEval.title = 'Set semua nilai Evaluasi P' + nPertemuan + ' menjadi kosong';
                btnEval.textContent = 'Eval';
                thEval.appendChild(btnEval);
                theadRow2.insertBefore(thEval, thResetAsist1);

                var thNilaiH = document.createElement('th');
                thNilaiH.style.cssText = 'text-align:center;padding:2px 1px;font-size:10px;font-weight:500;color:var(--text-muted);';
                thNilaiH.textContent = 'Nilai';
                theadRow2.insertBefore(thNilaiH, thResetAsist1);
            }
        }

        var tbody = document.querySelector('form table tbody');
        if (!tbody) return;
        tbody.querySelectorAll('tr').forEach(function (tr) {
            var mhsId = tr.querySelector('.input-nilai')?.name?.match(/\[(\d+)\]/)?.[1];
            if (!mhsId) return;

            var tdAsistensi = tr.querySelector('td input[name*="nilai_asistensi1"]')?.closest('td');
            if (!tdAsistensi) return;

            var tdKeg = document.createElement('td');
            tdKeg.className = 'td-nilai';
            tdKeg.style.borderLeft = '2px solid var(--border)';
            var inpKeg = document.createElement('input');
            inpKeg.type = 'text';
            inpKeg.name = 'nilai[' + mhsId + '][p' + nPertemuan + '_kegiatan]';
            inpKeg.className = 'form-control form-control-xs input-nilai input-sub-nilai';
            inpKeg.setAttribute('inputmode', 'decimal');
            inpKeg.dataset.mhs = mhsId;
            inpKeg.dataset.pertemuan = nPertemuan;
            inpKeg.dataset.sub = 'kegiatan';
            inpKeg.dataset.asal = '';
            inpKeg.placeholder = '—';
            tdKeg.appendChild(inpKeg);
            tr.insertBefore(tdKeg, tdAsistensi);

            var tdEval = document.createElement('td');
            tdEval.className = 'td-nilai';
            var inpEval = document.createElement('input');
            inpEval.type = 'text';
            inpEval.name = 'nilai[' + mhsId + '][p' + nPertemuan + '_evaluasi]';
            inpEval.className = 'form-control form-control-xs input-nilai input-sub-nilai';
            inpEval.setAttribute('inputmode', 'decimal');
            inpEval.dataset.mhs = mhsId;
            inpEval.dataset.pertemuan = nPertemuan;
            inpEval.dataset.sub = 'evaluasi';
            inpEval.dataset.asal = '';
            inpEval.placeholder = '—';
            tdEval.appendChild(inpEval);
            tr.insertBefore(tdEval, tdAsistensi);

            var tdNilai = document.createElement('td');
            tdNilai.className = 'td-nilai';
            tdNilai.style.background = 'var(--bg-page)';
            var inpNilai = document.createElement('input');
            inpNilai.type = 'text';
            inpNilai.id = 'nilai_p' + nPertemuan + '_' + mhsId;
            inpNilai.className = 'form-control form-control-xs';
            inpNilai.style.cssText = 'background:transparent;cursor:default;text-align:center;font-weight:600;';
            inpNilai.readOnly = true;
            inpNilai.placeholder = '—';
            inpNilai.tabIndex = -1;
            tdNilai.appendChild(inpNilai);
            tr.insertBefore(tdNilai, tdAsistensi);

            var capturedKeg  = inpKeg;
            var capturedEval = inpEval;
            [capturedKeg, capturedEval].forEach(function (inp) {
                inp.addEventListener('input', function () {
                    var sekarang = this.value === '—' ? '' : this.value;
                    var asal     = this.dataset.asal || '';
                    if (sekarang !== asal) {
                        this.classList.add('is-draft', 'nilai-dirty');
                    } else {
                        this.classList.remove('is-draft', 'nilai-dirty');
                    }
                    var bKeg  = parseFloat(document.querySelector('form[action*="simpan-semua"]')?.dataset.bobotKegiatan || 50) / 100;
                    var bEval = parseFloat(document.querySelector('form[action*="simpan-semua"]')?.dataset.bobotEvaluasi || 50) / 100;
                    var vKeg  = capturedKeg.value  === '—' ? '' : capturedKeg.value.trim();
                    var vEval = capturedEval.value === '—' ? '' : capturedEval.value.trim();
                    var elP   = document.getElementById('nilai_p' + nPertemuan + '_' + mhsId);
                    if (!elP) return;
                    if (vKeg === '' && vEval === '') { elP.value = ''; return; }
                    var kN = vKeg  !== '' ? parseFloat(vKeg)  : null;
                    var eN = vEval !== '' ? parseFloat(vEval) : null;
                    elP.value = (((bKeg * (kN ?? 0)) + (bEval * (eN ?? 0)))).toFixed(2);
                    if (window._simpanDraftNilai) window._simpanDraftNilai();
                    if (window._updateNilaiUI)    window._updateNilaiUI();
                });
            });
        });
    }

    function handleTambah() {
        var nBaru = jumlahSaatIni + 1;
        if (nBaru > 14) return;

        fetch('{{ route('asisten.nilai.tambah-pertemuan', $praktikum) }}', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({}),
        })
        .then(function (r) { return r.json(); })
        .then(function (json) {
            if (!json.success) { alert(json.pesan || 'Gagal menambah pertemuan.'); return; }
            tambahKolomClient(json.jumlah, json.hari, json.tanggal, false);
            jumlahSaatIni = json.jumlah;
            updateTombolTambah();
            setTimeout(function() {
                var inputs = document.querySelectorAll('.input-materi-pertemuan');
                if (inputs.length) inputs[inputs.length - 1].focus();
                posisikanTombolTambah();
            }, 100);
        })
        .catch(function () { alert('Gagal menambah pertemuan. Periksa koneksi.'); });
    }

    // ── Tombol Hapus Pertemuan ─────────────────────────────────────────
    function pasangTombolHapus() {
        document.querySelectorAll('.btn-hapus-pertemuan').forEach(function (btn) {
            if (btn.dataset.listenerPasang) return;
            btn.dataset.listenerPasang = '1';
            btn.addEventListener('click', function () {
                var n = parseInt(btn.dataset.pertemuan);
                if (!confirm('Hapus pertemuan P' + n + ' beserta semua nilainya?\nTindakan ini tidak dapat dibatalkan.')) return;

                btn.disabled    = true;
                btn.textContent = '…';

                fetch(HAPUS_URL_TMPL.replace('__P__', n), {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                })
                .then(function (r) { return r.json(); })
                .then(function (json) {
                    if (!json.success) {
                        alert(json.pesan || 'Gagal menghapus pertemuan.');
                        btn.disabled    = false;
                        btn.textContent = '✕';
                        return;
                    }
                    hapusKolomClient(n);
                    jumlahSaatIni = json.jumlah;
                    updateTombolTambah();
                    posisikanTombolTambah();
                })
                .catch(function () {
                    alert('Gagal menghapus. Periksa koneksi.');
                    btn.disabled    = false;
                    btn.textContent = '✕';
                });
            });
        });
    }

    function hapusKolomClient(nPertemuan) {
        // Hapus th baris 1
        var theadRow1 = document.querySelector('form table thead tr');
        if (theadRow1) {
            var thTarget = Array.from(theadRow1.querySelectorAll('th[data-pertemuan-th]'))
                .find(function (th) { return parseInt(th.getAttribute('data-pertemuan-th')) === nPertemuan; });
            if (thTarget) thTarget.remove();
        }

        // Hapus th baris 2 (Keg, Eval, Nilai)
        var theadRow2 = document.querySelectorAll('form table thead tr')[1];
        if (theadRow2) {
            var thKegTarget = Array.from(theadRow2.querySelectorAll('th')).find(function (th) {
                return th.querySelector('button[data-reset-field="p' + nPertemuan + '_kegiatan"]');
            });
            if (thKegTarget) {
                var next1 = thKegTarget.nextElementSibling;
                var next2 = next1 ? next1.nextElementSibling : null;
                thKegTarget.remove();
                if (next1) next1.remove();
                if (next2 && next2.textContent.trim() === 'Nilai') next2.remove();
            }
        }

        // Hapus td dari setiap baris tbody
        document.querySelectorAll('form table tbody tr').forEach(function (tr) {
            var inpKeg = tr.querySelector('input[name*="[p' + nPertemuan + '_kegiatan]"]');
            if (!inpKeg) return;
            var tdKeg   = inpKeg.closest('td');
            var tdEval  = tdKeg ? tdKeg.nextElementSibling : null;
            var tdNilai = tdEval ? tdEval.nextElementSibling : null;
            if (tdKeg)   tdKeg.remove();
            if (tdEval)  tdEval.remove();
            if (tdNilai && tdNilai.querySelector('input[readonly]')) tdNilai.remove();
        });

        // Tambahkan tombol hapus ke kolom yang kini jadi terakhir
        if (nPertemuan > 1) {
            var row1 = document.querySelector('form table thead tr');
            if (row1) {
                var allThP  = Array.from(row1.querySelectorAll('th[data-pertemuan-th]'));
                var thBaru  = allThP[allThP.length - 1];
                if (thBaru && !thBaru.querySelector('.btn-hapus-pertemuan')) {
                    var nBaru   = parseInt(thBaru.getAttribute('data-pertemuan-th'));
                    var divJudul = thBaru.querySelector('div');
                    if (divJudul) {
                        var btnHapusBaru = document.createElement('button');
                        btnHapusBaru.type = 'button';
                        btnHapusBaru.className = 'btn-hapus-pertemuan';
                        btnHapusBaru.dataset.pertemuan = nBaru;
                        btnHapusBaru.title = 'Hapus pertemuan P' + nBaru + ' beserta nilainya';
                        btnHapusBaru.style.cssText = 'background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;'
                            + 'border-radius:4px;padding:1px 5px;font-size:10px;cursor:pointer;line-height:1.4;flex-shrink:0;';
                        btnHapusBaru.textContent = '✕';
                        divJudul.appendChild(btnHapusBaru);
                        pasangTombolHapus();
                    }
                }
            }
        }
    }

    // ── Simpan materi inline ──────────────────────────────────────────
    document.addEventListener('blur', function(e) {
        if (!e.target.classList.contains('input-materi-pertemuan')) return;
        var ke  = e.target.dataset.ke;
        var val = e.target.value.trim();
        fetch('{{ route('asisten.nilai.pertemuan.materi', ['praktikum' => $praktikum->id, 'ke' => '__KE__']) }}'.replace('__KE__', ke), {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ materi: val }),
        });
    }, true);

    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter' || !e.target.classList.contains('input-materi-pertemuan')) return;
        e.target.blur();
    });

    // Hapus draft pertemuan saat submit atau navigasi keluar
    var formNilai = document.querySelector('form[action*="simpan-semua"]');
    if (formNilai) {
        formNilai.addEventListener('submit', function () {
            try { sessionStorage.removeItem(DRAFT_KEY_P); } catch (e) {}
        });
    }
    window.addEventListener('pagehide', function () {
        var navT = (performance.getEntriesByType('navigation')[0] || {}).type;
        if (navT !== 'reload') { try { sessionStorage.removeItem(DRAFT_KEY_P); } catch (e) {} }
    });
})();
</script>

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
    window._simpanDraftNilai = simpanDraft;

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

    window._updateNilaiUI = function () { updateUI(hitungDirty()); };

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

    // Ekspos agar handler reset di app.js bisa memanggilnya
    window._hitungNilaiPertemuan = hitungSemuaNilaiP;
    window._hitungNASemua        = hitungSemuaNilaiP;

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