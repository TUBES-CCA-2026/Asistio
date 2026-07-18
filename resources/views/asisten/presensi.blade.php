@extends('layouts.app')
@section('title','Input Presensi')
@section('page-title','Input Presensi')
@section('page-subtitle') {{ $praktikum->mataKuliah?->nama_mk }} — {{ $praktikum->nama_kelas }} @endsection
@section('content')
<div class="page-toolbar">
    <a href="{{ route('asisten.dashboard') }}" class="btn btn-outline">← Kembali</a>
    <div style="display:flex;align-items:center;gap:8px;">
        <a href="?pertemuan={{ max(1,$pertemuan-1) }}" class="btn btn-outline btn-sm">‹ Sebelumnya</a>
        <span class="fw-600 text-primary">Pertemuan {{ $pertemuan }}</span>
        <a href="?pertemuan={{ $pertemuan+1 }}" class="btn btn-outline btn-sm">Berikutnya ›</a>
        <form method="GET" action="{{ url()->current() }}" style="display:flex;align-items:center;gap:6px;margin-left:8px;padding-left:8px;border-left:1px solid var(--border-color, #e5e7eb);">
            <label for="pertemuan-jump" class="fs-12 text-muted" style="margin:0;">Lompat ke:</label>
            <input type="number" id="pertemuan-jump" name="pertemuan" min="1" max="14" value="{{ $pertemuan }}" class="form-control form-control-sm" style="width:64px;">
            <button type="submit" class="btn btn-outline btn-sm">Lompat</button>
        </form>
    </div>
</div>
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);">
    <div class="stat-card"><div class="stat-body"><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Mahasiswa</div></div></div>
    <div class="stat-card"><div class="stat-body"><div class="stat-value" style="color:var(--status-h)">{{ $stats['hadir'] }}</div><div class="stat-label">Hadir</div></div></div>
    <div class="stat-card"><div class="stat-body"><div class="stat-value" style="color:var(--status-a)">{{ $stats['alpa'] }}</div><div class="stat-label">Alpha</div></div></div>
</div>
<form method="POST" action="{{ route('asisten.presensi.simpan', $praktikum) }}" enctype="multipart/form-data">@csrf
<input type="hidden" name="pertemuan" value="{{ $pertemuan }}">
<div class="card">
    <div class="card-header">
        <span class="card-title">Pertemuan {{ $pertemuan }}</span>
        <div style="display:flex;gap:6px;align-items:center;">
            <span class="fs-12 text-muted">Tandai semua:</span>
            <button type="button" class="btn btn-sm btn-outline status-btn-bulk" data-status="H">Hadir</button>
            <button type="button" class="btn btn-sm btn-outline status-btn-bulk" data-status="A">Alpha</button>
        </div>
    </div>
    <div class="table-wrapper"><table class="table">
        <thead><tr><th>#</th><th>NIM</th><th>Nama</th><th style="text-align:center;">H</th><th style="text-align:center;">I</th><th style="text-align:center;">S</th><th style="text-align:center;">A</th><th>Catatan</th><th style="text-align:center;min-width:140px;">Bukti Foto</th></tr></thead>
        <tbody>
        @forelse($mahasiswaList as $i => $m)
        @php
            $p = $presensiMap[$m->id] ?? null;
            $status = $p?->status_kehadiran;
            $alpaTinggi = $m->melebihiBatasAlpaDiKelas($praktikum->id);
            $punyaFoto = $p && $p->bukti_foto;
        @endphp
        <tr class="{{ $alpaTinggi ? 'row-alpa-alert' : '' }}">
            <td>{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</td>
            <td style="font-family:monospace;font-size:12px;">{{ $m->nim_mahasiswa }}</td>
            <td class="fw-500">
                {{ $m->nama_mahasiswa }}
                @if($alpaTinggi)
                    <span class="badge-alpa-alert" title="Sudah alpa {{ $m->jumlah_alpa }}x — sudah mencapai/melewati batas {{ \App\Models\Mahasiswa::BATAS_ALPA }} pertemuan">⚠ Alpa {{ $m->jumlah_alpa }}×</span>
                @endif
            </td>
            @foreach(['H','I','S','A'] as $s)
            <td style="text-align:center;">
                <label class="radio-circle radio-{{ strtolower($s) }}">
                    <input type="radio" name="presensi[{{ $m->id }}][status_kehadiran]" value="{{ $s }}" {{ $status===$s?'checked':'' }}>
                    <span>{{ $s }}</span>
                </label>
            </td>
            @endforeach
            <td><input type="text" name="presensi[{{ $m->id }}][catatan]" class="form-control form-control-sm" value="{{ $p?->catatan }}" placeholder="—"></td>
            <td style="text-align:center;vertical-align:middle;padding:6px;">
                <input type="file" name="foto_{{ $m->id }}" id="file-input-{{ $m->id }}"
                       accept="image/*" style="display:none;">
                @if($punyaFoto)
                    <a href="{{ route('asisten.presensi.bukti.lihat', $p) }}" target="_blank"
                       class="btn btn-sm btn-outline" style="font-size:11px;padding:3px 8px;">📎 Lihat</a>
                @else
                    <span id="bukti-status-{{ $m->id }}" style="font-size:11px;color:#9ca3af;">—</span>
                @endif
            </td>
        </tr>
        @empty<tr><td colspan="8"><div class="empty-state"><p>Belum ada mahasiswa di kelas ini.</p></div></td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($mahasiswaList->count() > 0)
    <div class="card-footer"><button type="submit" class="btn btn-primary">Simpan Presensi Pertemuan {{ $pertemuan }}</button></div>
    @endif
</div>
<script>
(function () {
    var form      = document.querySelector('form[enctype="multipart/form-data"]');
    var DRAFT_KEY = 'draft_presensi_{{ $praktikum->id }}_p{{ $pertemuan }}';
    if (!form) return;

    function bacaState() {
        var state = {};
        form.querySelectorAll('input[type="radio"]:checked[name*="status_kehadiran"]').forEach(function (r) {
            var id = r.name.match(/\[(\d+)\]/)?.[1];
            if (id) { state[id] = state[id] || {}; state[id].status = r.value; }
        });
        form.querySelectorAll('input[name*="[catatan]"]').forEach(function (inp) {
            var id = inp.name.match(/\[(\d+)\]/)?.[1];
            if (id) { state[id] = state[id] || {}; state[id].catatan = inp.value; }
        });
        return state;
    }

    function bacaStateDB() {
        var state = {};
        form.querySelectorAll('input[type="radio"][name*="status_kehadiran"]').forEach(function (r) {
            var id = r.name.match(/\[(\d+)\]/)?.[1];
            if (!id) return;
            if (!state[id]) state[id] = { status: null, catatan: '' };
            if (r.defaultChecked) state[id].status = r.value;
        });
        form.querySelectorAll('input[name*="[catatan]"]').forEach(function (inp) {
            var id = inp.name.match(/\[(\d+)\]/)?.[1];
            if (!id) return;
            if (!state[id]) state[id] = { status: null, catatan: '' };
            state[id].catatan = inp.defaultValue || '';
        });
        return state;
    }

    var stateDB = bacaStateDB();

    function adaPerubahan() {
        var now = bacaState();
        return Object.keys(now).some(function (id) {
            var db  = stateDB[id] || {};
            var cur = now[id] || {};
            return cur.status !== db.status || (cur.catatan || '') !== (db.catatan || '');
        });
    }

    function simpanDraft() {
        try { sessionStorage.setItem(DRAFT_KEY, JSON.stringify(bacaState())); } catch (e) {}
        updateIndikator();
    }

    function pulihkanDraft() {
        var raw; try { raw = sessionStorage.getItem(DRAFT_KEY); } catch (e) { return; }
        if (!raw) return;
        var draft; try { draft = JSON.parse(raw); } catch (e) { return; }
        Object.keys(draft).forEach(function (id) {
            var d = draft[id];
            if (d.status) {
                var r = form.querySelector('input[type="radio"][name="presensi[' + id + '][status_kehadiran]"][value="' + d.status + '"]');
                if (r) r.checked = true;
            }
            if (d.catatan !== undefined) {
                var inp = form.querySelector('input[name="presensi[' + id + '][catatan]"]');
                if (inp) inp.value = d.catatan;
            }
        });
        updateIndikator();
    }

    function hapusDraft() {
        try { sessionStorage.removeItem(DRAFT_KEY); } catch (e) {}
        updateIndikator();
    }

    var indEl = null;
    var cardHeader = form.querySelector('.card-header');
    if (cardHeader) {
        cardHeader.style.position = 'relative';
        indEl = document.createElement('span');
        indEl.style.cssText = 'display:none;align-items:center;gap:5px;font-size:12px;font-weight:500;color:#f59e0b;background:#fffbeb;border:1px solid #fde68a;border-radius:999px;padding:3px 10px;position:absolute;left:50%;transform:translateX(-50%);';
        indEl.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Belum disimpan';
        cardHeader.appendChild(indEl);
    }

    function updateIndikator() {
        var raw; try { raw = sessionStorage.getItem(DRAFT_KEY); } catch (e) {}
        var ada = !!raw && adaPerubahan();
        if (indEl) indEl.style.display = ada ? 'inline-flex' : 'none';
    }

    pulihkanDraft();

    // ── Foto tersimpan di DB (dari PHP) ──────────────────────────────
    var fotoTersimpan = @json(
        collect($presensiMap)->mapWithKeys(fn($p, $id) => [$id => (bool)$p->bukti_foto])
    );

    // ── State foto pending (belum disubmit) ───────────────────────────
    var fotoPending = {};

    // ── Buat modal upload bukti sekali saja ───────────────────────────
    var modalEl = document.createElement('div');
    modalEl.id  = 'modal-bukti';
    modalEl.className = 'modal-overlay';
    modalEl.innerHTML = [
        '<div class="modal" style="max-width:440px;">',
            '<div class="modal-header" style="background:#F0FDF4;border-bottom:1px solid #BBF7D0;">',
                '<span class="modal-title" style="color:#15803D;" id="modal-bukti-judul">Upload Bukti Foto</span>',
                '<button type="button" class="modal-close" id="modal-bukti-tutup">✕</button>',
            '</div>',
            '<div class="modal-body">',
                '<p id="modal-bukti-nama" style="margin:0 0 8px;font-size:13px;font-weight:600;color:#1e293b;"></p>',
                '<p style="margin:0 0 14px;font-size:13px;color:#64748b;">Upload foto bukti (surat dokter, surat izin, dll.) untuk melanjutkan.</p>',
                '<div id="modal-drop-area" style="border:2px dashed #cbd5e1;border-radius:10px;padding:24px 16px;text-align:center;cursor:pointer;transition:border-color .15s,background .15s;">',
                    '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="1.5" style="margin-bottom:8px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>',
                    '<p style="margin:0 0 4px;font-size:13px;color:#475569;font-weight:500;">Klik atau drag foto ke sini</p>',
                    '<p id="modal-file-name" style="margin:0;font-size:12px;color:#94a3b8;">JPG, PNG, WEBP · Maks 5 MB</p>',
                    '<input id="modal-file-input" type="file" accept="image/*" style="display:none;">',
                '</div>',
                '<div id="modal-preview-wrap" style="display:none;margin-top:12px;text-align:center;">',
                    '<img id="modal-preview-img" src="" style="max-width:100%;max-height:160px;border-radius:8px;object-fit:contain;border:1px solid #e2e8f0;">',
                    '<p id="modal-preview-name" style="margin:6px 0 0;font-size:12px;color:#64748b;"></p>',
                '</div>',
            '</div>',
            '<div style="display:flex;gap:8px;justify-content:flex-end;padding:0 16px 16px;">',
                '<button id="modal-bukti-batal" type="button" class="btn btn-outline">Batal (kosongkan)</button>',
                '<button id="modal-bukti-simpan" type="button" class="btn btn-primary" disabled style="opacity:.5;">Konfirmasi</button>',
            '</div>',
        '</div>',
    ].join('');
    document.body.appendChild(modalEl);

    var judulEl     = modalEl.querySelector('#modal-bukti-judul');
    var namaEl      = modalEl.querySelector('#modal-bukti-nama');
    var tutupBtn    = modalEl.querySelector('#modal-bukti-tutup');
    var dropArea    = modalEl.querySelector('#modal-drop-area');
    var fileInput   = modalEl.querySelector('#modal-file-input');
    var fileNameEl  = modalEl.querySelector('#modal-file-name');
    var previewWrap = modalEl.querySelector('#modal-preview-wrap');
    var previewImg  = modalEl.querySelector('#modal-preview-img');
    var previewName = modalEl.querySelector('#modal-preview-name');
    var batalBtn    = modalEl.querySelector('#modal-bukti-batal');
    var simpanBtn   = modalEl.querySelector('#modal-bukti-simpan');

    var modalMhsId   = null;
    var modalRadioEl = null;

    function bukaModalBukti(mahasiswaId, nama, radioEl) {
        modalMhsId   = mahasiswaId;
        modalRadioEl = radioEl;
        judulEl.textContent = 'Upload Bukti — ' + (radioEl.value === 'S' ? 'Sakit' : 'Izin');
        namaEl.textContent  = nama;
        resetPreviewModal();
        modalEl.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function tutupModalBukti(batalkan) {
        modalEl.classList.remove('open');
        document.body.style.overflow = '';
        if (batalkan && modalRadioEl) {
            modalRadioEl.checked = false;
            delete fotoPending[modalMhsId];
            var inp = document.getElementById('file-input-' + modalMhsId);
            if (inp) inp.value = '';
            updateBuktiStatus(modalMhsId, null);
            simpanDraft();
        }
        modalMhsId   = null;
        modalRadioEl = null;
    }

    function resetPreviewModal() {
        fileInput.value = '';
        fileNameEl.textContent = 'JPG, PNG, WEBP · Maks 5 MB';
        previewWrap.style.display = 'none';
        previewImg.src = '';
        previewName.textContent = '';
        simpanBtn.disabled = true;
        simpanBtn.style.opacity = '.5';
        dropArea.style.borderColor = '#cbd5e1';
        dropArea.style.background  = '';
    }

    function pilihanFile(file) {
        if (!file || !file.type.startsWith('image/')) { alert('File harus berupa gambar.'); return; }
        if (file.size > 5 * 1024 * 1024) { alert('Ukuran file maksimal 5 MB.'); return; }
        fileNameEl.textContent = file.name;
        var reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
            previewName.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
            previewWrap.style.display = 'block';
        };
        reader.readAsDataURL(file);
        simpanBtn.disabled = false;
        simpanBtn.style.opacity = '1';
        dropArea.style.borderColor = '#6366f1';
        dropArea.style.background  = '#f5f3ff';
        fotoPending[modalMhsId] = file;
    }

    dropArea.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () { if (fileInput.files[0]) pilihanFile(fileInput.files[0]); });
    dropArea.addEventListener('dragover', function (e) { e.preventDefault(); dropArea.style.borderColor = '#6366f1'; dropArea.style.background = '#f5f3ff'; });
    dropArea.addEventListener('dragleave', function () { dropArea.style.borderColor = '#cbd5e1'; dropArea.style.background = ''; });
    dropArea.addEventListener('drop', function (e) { e.preventDefault(); if (e.dataTransfer.files[0]) pilihanFile(e.dataTransfer.files[0]); });

    simpanBtn.addEventListener('click', function () {
        if (!fotoPending[modalMhsId]) return;
        var inp = document.getElementById('file-input-' + modalMhsId);
        if (inp) {
            var dt = new DataTransfer();
            dt.items.add(fotoPending[modalMhsId]);
            inp.files = dt.files;
        }
        updateBuktiStatus(modalMhsId, fotoPending[modalMhsId].name);
        simpanDraft();
        modalEl.classList.remove('open');
        document.body.style.overflow = '';
        modalMhsId   = null;
        modalRadioEl = null;
    });

    batalBtn.addEventListener('click', function () { tutupModalBukti(true); });
    tutupBtn.addEventListener('click',  function () { tutupModalBukti(true); });
    modalEl.addEventListener('click',   function (e) { if (e.target === modalEl) tutupModalBukti(true); });

    function updateBuktiStatus(id, namaFile) {
        var el = document.getElementById('bukti-status-' + id);
        if (!el) return;
        if (namaFile) {
            el.innerHTML = '<span style="color:#16a34a;font-size:11px;">✓ ' + namaFile + '</span>';
        } else {
            el.textContent = '—';
            el.style.color = '#9ca3af';
        }
    }

    // ── Intercept radio I dan S → buka modal ─────────────────────────
    form.querySelectorAll('input[type="radio"][name*="status_kehadiran"]').forEach(function (r) {
        r.addEventListener('change', function () {
            if (r.value !== 'I' && r.value !== 'S') { simpanDraft(); return; }
            var id = r.name.match(/\[(\d+)\]/)?.[1];
            if (!id) { simpanDraft(); return; }
            if (fotoTersimpan[id]) { simpanDraft(); return; }
            if (fotoPending[id])   { simpanDraft(); return; }
            var row  = r.closest('tr');
            var nama = row ? (row.querySelectorAll('td')[2]?.textContent?.trim() || '') : '';
            bukaModalBukti(id, nama, r);
        });
    });

    form.querySelectorAll('input[name*="[catatan]"]').forEach(function (inp) {
        var t;
        inp.addEventListener('input', function () { clearTimeout(t); t = setTimeout(simpanDraft, 600); });
    });

    document.querySelectorAll('.status-btn-bulk').forEach(function (btn) {
        btn.addEventListener('click', function () { setTimeout(simpanDraft, 50); });
    });

    // ── Validasi submit ───────────────────────────────────────────────
    form.addEventListener('submit', function (e) {
        var kurang = [];
        form.querySelectorAll('input[type="radio"][name*="status_kehadiran"]:checked').forEach(function (r) {
            if (r.value !== 'S' && r.value !== 'I') return;
            var id = r.name.match(/\[(\d+)\]/)?.[1];
            if (!id) return;
            if (fotoTersimpan[id] || fotoPending[id]) return;
            var inp = form.querySelector('input[type="file"][name="foto_' + id + '"]');
            if (inp && (!inp.files || inp.files.length === 0)) kurang.push(id);
        });
        if (kurang.length) {
            e.preventDefault();
            alert('⚠ ' + kurang.length + ' mahasiswa dengan Sakit/Izin belum ada bukti foto.');
            return;
        }
        hapusDraft();
    });

    window.addEventListener('pagehide', function () {
        var nav = (performance.getEntriesByType('navigation')[0] || {}).type;
        if (nav !== 'reload') hapusDraft();
    });
})();
</script>
</form>

<div class="card" style="margin-top:20px;">
    <div class="card-header">
        <span class="card-title">Absensi Asistensi</span>
    </div>
    <div class="card-body" style="padding:0;">
        <div style="display:flex;gap:8px;padding:14px 16px 0;">
            @foreach([1,2,3] as $ke)
            <button type="button" class="btn btn-sm {{ $ke===1 ? 'btn-primary' : 'btn-outline' }} asistensi-tab-btn" data-asistensi="{{ $ke }}">Asistensi {{ $ke }}</button>
            @endforeach
        </div>
        @foreach([1,2,3] as $ke)
        <div class="asistensi-tab-panel" data-asistensi-panel="{{ $ke }}" style="{{ $ke!==1 ? 'display:none;' : '' }}">
            <form method="POST" action="{{ route('asisten.presensi.asistensi.simpan', $praktikum) }}">
                @csrf
                <input type="hidden" name="asistensi_ke" value="{{ $ke }}">
                <div class="table-wrapper">
                    <table class="table">
                        <thead><tr><th>#</th><th>NIM</th><th>Nama</th><th style="text-align:center;">Hadir</th></tr></thead>
                        <tbody>
                        @forelse($mahasiswaList as $i => $m)
                        @php $hadir = ($presensiAsistensiMap[$m->id] ?? null)?->get($ke)?->hadir ?? false; @endphp
                        <tr>
                            <td>{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</td>
                            <td style="font-family:monospace;font-size:12px;">{{ $m->nim_mahasiswa }}</td>
                            <td class="fw-500">{{ $m->nama_mahasiswa }}</td>
                            <td style="text-align:center;">
                                <input type="checkbox" name="presensi[{{ $m->id }}][hadir]" value="1" {{ $hadir ? 'checked' : '' }}>
                            </td>
                        </tr>
                        @empty<tr><td colspan="4"><div class="empty-state"><p>Belum ada mahasiswa di kelas ini.</p></div></td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($mahasiswaList->count() > 0)
                <div class="card-footer"><button type="submit" class="btn btn-primary">Simpan Absensi Asistensi {{ $ke }}</button></div>
                @endif
            </form>
        </div>
        @endforeach
    </div>
</div>
<script>
(function () {
    var PRAKTIKUM_ID = {{ $praktikum->id }};
    var navType  = (performance.getEntriesByType('navigation')[0] || {}).type || 'navigate';
    var isReload = navType === 'reload';

    function draftKey(ke) { return 'draft_asistensi_' + PRAKTIKUM_ID + '_ke' + ke; }

    // ── State awal dari DB (defaultChecked) ───────────────────────────
    function stateDB(ke) {
        var panel = document.querySelector('[data-asistensi-panel="' + ke + '"]');
        var state = {};
        if (!panel) return state;
        panel.querySelectorAll('input[type="checkbox"][name*="[hadir]"]').forEach(function (cb) {
            var id = cb.name.match(/\[(\d+)\]/)?.[1];
            if (id) state[id] = cb.defaultChecked;
        });
        return state;
    }

    function simpanDraft(ke) {
        var panel = document.querySelector('[data-asistensi-panel="' + ke + '"]');
        if (!panel) return;
        var state = {};
        panel.querySelectorAll('input[type="checkbox"][name*="[hadir]"]').forEach(function (cb) {
            var id = cb.name.match(/\[(\d+)\]/)?.[1];
            if (id) state[id] = cb.checked;
        });
        try { sessionStorage.setItem(draftKey(ke), JSON.stringify(state)); } catch (e) {}
        updateIndikator(ke);
    }

    function pulihkanDraft(ke) {
        var raw; try { raw = sessionStorage.getItem(draftKey(ke)); } catch (e) { return; }
        if (!raw) return;
        var draft; try { draft = JSON.parse(raw); } catch (e) { return; }
        var panel = document.querySelector('[data-asistensi-panel="' + ke + '"]');
        if (!panel) return;
        Object.keys(draft).forEach(function (id) {
            var cb = panel.querySelector('input[type="checkbox"][name="presensi[' + id + '][hadir]"]');
            if (cb) cb.checked = draft[id];
        });
        updateIndikator(ke);
    }

    function hapusDraft(ke) {
        try { sessionStorage.removeItem(draftKey(ke)); } catch (e) {}
        updateIndikator(ke);
    }

    function adaPerubahanAsistensi(ke) {
        var raw; try { raw = sessionStorage.getItem(draftKey(ke)); } catch (e) { return false; }
        if (!raw) return false;
        try { var draft = JSON.parse(raw); return Object.keys(draft).length > 0; } catch (e) { return false; }
    }

    // ── Indikator per tab & footer ────────────────────────────────────
    function updateIndikator(ke) {
        var ada = adaPerubahanAsistensi(ke);

        // Titik di tombol tab
        var btn = document.querySelector('.asistensi-tab-btn[data-asistensi="' + ke + '"]');
        if (btn) {
            var dot = btn.querySelector('.draft-dot');
            if (ada && !dot) {
                dot = document.createElement('span');
                dot.className = 'draft-dot';
                dot.style.cssText = 'display:inline-block;width:6px;height:6px;border-radius:50%;background:#f59e0b;margin-left:4px;vertical-align:middle;';
                btn.appendChild(dot);
            } else if (!ada && dot) {
                dot.remove();
            }
        }

        // Teks di footer
        var ind = document.getElementById('asistensiInd' + ke);
        if (ind) ind.style.display = ada ? 'inline-flex' : 'none';
    }

    // Inject indikator ke footer tiap panel
    [1, 2, 3].forEach(function (ke) {
        var panel  = document.querySelector('[data-asistensi-panel="' + ke + '"]');
        var footer = panel?.querySelector('.card-footer');
        if (!footer) return;
        footer.style.position = 'relative';
        var ind = document.createElement('span');
        ind.id = 'asistensiInd' + ke;
        ind.style.cssText = 'display:none;align-items:center;gap:5px;font-size:12px;font-weight:500;color:#f59e0b;position:absolute;left:50%;transform:translateX(-50%);';
        ind.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Belum disimpan';
        footer.insertBefore(ind, footer.firstChild);
    });

    // ── Init ──────────────────────────────────────────────────────────
    // Selalu restore draft saat load — hapus hanya saat navigasi masuk
    // dari halaman lain (pagehide sudah handle itu)
    [1, 2, 3].forEach(function (ke) {
        pulihkanDraft(ke);
    });

    // ── Dengarkan perubahan checkbox ──────────────────────────────────
    [1, 2, 3].forEach(function (ke) {
        var panel = document.querySelector('[data-asistensi-panel="' + ke + '"]');
        if (!panel) return;
        panel.querySelectorAll('input[type="checkbox"]').forEach(function (cb) {
            cb.addEventListener('change', function () { simpanDraft(ke); });
        });
        var formAsist = panel.querySelector('form');
        if (formAsist) formAsist.addEventListener('submit', function () { hapusDraft(ke); });
    });

    // ── Navigasi keluar → hapus semua draft ───────────────────────────
    window.addEventListener('pagehide', function () {
        var nav = (performance.getEntriesByType('navigation')[0] || {}).type;
        if (nav !== 'reload') [1, 2, 3].forEach(hapusDraft);
    });
})();
</script>
<script>
document.querySelectorAll('.asistensi-tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var target = btn.dataset.asistensi;
        document.querySelectorAll('.asistensi-tab-btn').forEach(function(b) {
            b.classList.toggle('btn-primary', b === btn);
            b.classList.toggle('btn-outline', b !== btn);
        });
        document.querySelectorAll('.asistensi-tab-panel').forEach(function(panel) {
            panel.style.display = (panel.dataset.asistensiPanel === target) ? '' : 'none';
        });
    });
});
</script>
@endsection