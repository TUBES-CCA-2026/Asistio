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
                @if($punyaFoto)
                    <a href="{{ route('asisten.presensi.bukti.lihat', $p) }}" target="_blank"
                       class="btn btn-sm btn-outline" style="font-size:11px;padding:3px 8px;">📎 Lihat</a>
                    <label class="btn btn-sm btn-outline" style="font-size:11px;padding:3px 8px;cursor:pointer;margin-left:2px;" title="Ganti foto">
                        ✏️ Ganti
                        <input type="file" name="foto_{{ $m->id }}" accept="image/*"
                               class="bukti-input" data-mid="{{ $m->id }}" style="display:none;">
                    </label>
                @else
                    <label class="btn btn-sm btn-outline bukti-label" id="blabel-{{ $m->id }}"
                           style="font-size:11px;padding:3px 8px;cursor:pointer;{{ in_array($status,['S','I']) ? 'background:#fef2f2;border-color:#f87171;color:#dc2626;' : '' }}"
                           title="Wajib untuk Sakit/Izin">
                        📷 {{ in_array($status,['S','I']) ? 'Wajib Upload' : 'Upload Foto' }}
                        <input type="file" name="foto_{{ $m->id }}" accept="image/*"
                               class="bukti-input" data-mid="{{ $m->id }}"
                               style="display:none;" {{ in_array($status,['S','I']) ? 'required' : '' }}>
                    </label>
                    <span id="bname-{{ $m->id }}" style="display:none;font-size:10px;color:#16a34a;display:block;margin-top:2px;"></span>
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
    var form     = document.querySelector('form[action*="presensi"][action*="simpan"]');
    var DRAFT_KEY = 'draft_presensi_{{ $praktikum->id }}_p{{ $pertemuan }}';
    if (!form) return;

    var navType  = (performance.getEntriesByType('navigation')[0] || {}).type || 'navigate';
    var isReload = navType === 'reload';

    // ── Baca seluruh state form ke objek ─────────────────────────────
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

    // ── Baca state DB (dari value awal HTML) ──────────────────────────
    function bacaStateDB() {
        var state = {};
        // Status: cek radio yang awalnya checked (dari PHP)
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

    // ── Cek apakah ada perubahan dari DB ─────────────────────────────
    function adaPerubahan() {
        var now = bacaState();
        return Object.keys(now).some(function (id) {
            var db  = stateDB[id] || {};
            var cur = now[id] || {};
            return cur.status !== db.status || (cur.catatan || '') !== (db.catatan || '');
        });
    }

    // ── Simpan ke sessionStorage ──────────────────────────────────────
    function simpanDraft() {
        try { sessionStorage.setItem(DRAFT_KEY, JSON.stringify(bacaState())); } catch (e) {}
        updateIndikator();
    }

    // ── Restore dari sessionStorage ───────────────────────────────────
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

    // ── Hapus draft ───────────────────────────────────────────────────
    function hapusDraft() {
        try { sessionStorage.removeItem(DRAFT_KEY); } catch (e) {}
        updateIndikator();
    }

    // ── Indikator "Belum disimpan" di card-header ─────────────────────
    var indEl = null;
    var cardHeader = form.querySelector('.card-header');
    if (cardHeader) {
        cardHeader.style.position = 'relative';
        indEl = document.createElement('span');
        indEl.style.cssText = 'display:none;align-items:center;gap:5px;font-size:12px;font-weight:500;color:#f59e0b;background:#fffbeb;border:1px solid #fde68a;border-radius:999px;padding:3px 10px;';indEl.style.cssText = 'display:none;align-items:center;gap:5px;font-size:12px;font-weight:500;color:#f59e0b;background:#fffbeb;border:1px solid #fde68a;border-radius:999px;padding:3px 10px;position:absolute;left:50%;transform:translateX(-50%);';
        indEl.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Belum disimpan';
        cardHeader.appendChild(indEl);
    }

    function updateIndikator() {
        var raw; try { raw = sessionStorage.getItem(DRAFT_KEY); } catch (e) {}
        var ada = !!raw && adaPerubahan();
        if (indEl) indEl.style.display = ada ? 'inline-flex' : 'none';
    }

    // ── Init ──────────────────────────────────────────────────────────
    pulihkanDraft();

    // ── Dengarkan perubahan ───────────────────────────────────────────
    form.querySelectorAll('input[type="radio"][name*="status_kehadiran"]').forEach(function (r) {
        r.addEventListener('change', simpanDraft);
    });
    form.querySelectorAll('input[name*="[catatan]"]').forEach(function (inp) {
        var t;
        inp.addEventListener('input', function () {
            clearTimeout(t); t = setTimeout(simpanDraft, 600);
        });
    });
    // Tombol "Tandai semua" juga trigger simpan
    document.querySelectorAll('.status-btn-bulk').forEach(function (btn) {
        btn.addEventListener('click', function () { setTimeout(simpanDraft, 50); });
    });

    // ── Submit → hapus draft ──────────────────────────────────────────
    form.addEventListener('submit', hapusDraft);

    // ── Navigasi keluar → hapus draft ────────────────────────────────
    window.addEventListener('pagehide', function () {
        var nav = (performance.getEntriesByType('navigation')[0] || {}).type;
        if (nav !== 'reload') hapusDraft();
    });
})();

// ── Bukti Foto: dinamis label & validasi ─────────────────────────
(function () {
    // Radio S/I dipilih → ubah warna tombol upload jadi merah
    document.querySelectorAll('input[type="radio"][name*="status_kehadiran"]').forEach(function (r) {
        r.addEventListener('change', function () {
            var id  = r.name.match(/\[(\d+)\]/)?.[1];
            if (!id) return;
            var lbl = document.getElementById('blabel-' + id);
            var inp = lbl ? lbl.querySelector('input[type="file"]') : null;
            if (!lbl || !inp) return;
            var merah = (r.value === 'S' || r.value === 'I');
            lbl.style.background   = merah ? '#fef2f2' : '';
            lbl.style.borderColor  = merah ? '#f87171' : '';
            lbl.style.color        = merah ? '#dc2626' : '';
            lbl.childNodes[0].textContent = merah ? '📷 Wajib Upload' : '📷 Upload Foto';
            inp.required = merah;
        });
    });

    // File dipilih → tampilkan nama
    document.querySelectorAll('.bukti-input').forEach(function (inp) {
        inp.addEventListener('change', function () {
            var id   = inp.dataset.mid;
            var span = document.getElementById('bname-' + id);
            var lbl  = document.getElementById('blabel-' + id);
            if (inp.files && inp.files[0]) {
                if (span) { span.textContent = '✓ ' + inp.files[0].name; span.style.display = 'block'; }
                if (lbl)  { lbl.style.borderColor = '#16a34a'; lbl.style.color = '#16a34a'; }
            }
        });
    });

    // Validasi submit: cegah jika S/I tanpa foto
    var formPres = document.querySelector('form[enctype="multipart/form-data"]');
    if (formPres) {
        formPres.addEventListener('submit', function (e) {
            var kurang = [];
            formPres.querySelectorAll('input[type="radio"][name*="status_kehadiran"]:checked').forEach(function (r) {
                if (r.value !== 'S' && r.value !== 'I') return;
                var id  = r.name.match(/\[(\d+)\]/)?.[1];
                if (!id) return;
                var inp = formPres.querySelector('input[type="file"][name="foto_' + id + '"]');
                if (inp && (!inp.files || inp.files.length === 0)) kurang.push(id);
            });
            if (kurang.length) {
                e.preventDefault();
                alert('⚠ ' + kurang.length + ' mahasiswa dengan Sakit/Izin belum ada bukti foto.\nSilakan upload terlebih dahulu.');
                // Scroll ke baris pertama yang bermasalah
                var inp1 = formPres.querySelector('input[type="file"][name="foto_' + kurang[0] + '"]');
                if (inp1) inp1.closest('tr').scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
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