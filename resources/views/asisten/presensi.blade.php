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
        <thead><tr><th>#</th><th>NIM</th><th>Nama</th><th style="text-align:center;">H</th><th style="text-align:center;">I</th><th style="text-align:center;">S</th><th style="text-align:center;">A</th><th>Catatan</th></tr></thead>
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
            <input type="file" name="foto_{{ $m->id }}" id="file-input-{{ $m->id }}"
                   accept="image/*" style="display:none;">
        </tr>
        @empty<tr><td colspan="8"><div class="empty-state"><p>Belum ada mahasiswa di kelas ini.</p></div></td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($mahasiswaList->count() > 0)
    <div class="card-footer"><button type="submit" class="btn btn-primary">Simpan Presensi Pertemuan {{ $pertemuan }}</button></div>
    @endif
</div>
</form>

<script id="foto-data-json" type="application/json">{!! json_encode($fotoData) !!}</script>
<script>
(function () {
    var form      = document.querySelector('form[enctype="multipart/form-data"]');
    var DRAFT_KEY = 'draft_presensi_{{ $praktikum->id }}_p{{ $pertemuan }}';
    var UPLOAD_URL = '{{ route('asisten.presensi.bukti.upload', $praktikum) }}';
    var CSRF       = document.querySelector('meta[name="csrf-token"]')?.content;
    var PERTEMUAN  = {{ $pertemuan }};
    if (!form) return;

    // ── Data foto dari DB: { "mahasiswaId": { ada, url, presensi_id } } ─
    var fotoData = JSON.parse(document.getElementById('foto-data-json').textContent);

    // ── Draft helpers ─────────────────────────────────────────────────
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
            var cur = now[id]     || {};
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

    // ── Indikator "Belum disimpan" di card-header ─────────────────────
    var indEl      = null;
    var cardHeader = form.querySelector('.card-header');
    if (cardHeader) {
        cardHeader.style.position = 'relative';
        indEl = document.createElement('span');
        indEl.style.cssText = 'display:none;align-items:center;gap:5px;font-size:12px;font-weight:500;color:#f59e0b;background:#fffbeb;border:1px solid #fde68a;border-radius:999px;padding:3px 10px;position:absolute;left:50%;transform:translateX(-50%);';
        indEl.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Belum disimpan';
        cardHeader.appendChild(indEl);
    }

    function updateIndikator() {
        var ada = !!sessionStorage.getItem(DRAFT_KEY) && adaPerubahan();
        if (indEl) indEl.style.display = ada ? 'inline-flex' : 'none';
    }

    pulihkanDraft();

    // ── Badge foto di tiap baris ──────────────────────────────────────
    // Inject kolom "Bukti" ke thead — hanya tampil jika ada yg temporary
    var thead = form.querySelector('table thead tr');
    var thBukti = null;
    if (thead) {
        thBukti = document.createElement('th');
        thBukti.style.cssText = 'text-align:center;width:56px;';
        thBukti.textContent = 'Bukti';
        thBukti.style.display = 'none'; // sembunyikan dulu, tampil jika ada temporary
        thead.appendChild(thBukti);
    }

    function adaFotoTemporary() {
        return Object.values(fotoData).some(function (d) { return d && d.ada && d.temporary; });
    }

    function updateKolomBukti() {
        var ada = adaFotoTemporary();
        if (thBukti) thBukti.style.display = ada ? '' : 'none';
        form.querySelectorAll('.td-bukti').forEach(function (td) {
            td.style.display = ada ? '' : 'none';
        });
    }

    function renderBadgeFoto(id, nama) {
        var td = form.querySelector('tr[data-mhs-id="' + id + '"] .td-bukti');
        if (!td) return;
        var d = fotoData[id];
        td.innerHTML = '';
        // Hanya tampilkan tombol "Lihat" jika foto masih temporary (belum di-save)
        if (d && d.ada && d.url && d.temporary) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.title = 'Lihat bukti foto (belum disimpan)';
            btn.style.cssText = 'background:#fef9c3;border:1px solid #fde68a;color:#92400e;border-radius:6px;padding:2px 7px;font-size:11px;cursor:pointer;';
            btn.textContent = '📎 Lihat*';
            btn.addEventListener('click', function () {
                var status = form.querySelector('input[name="presensi[' + id + '][status_kehadiran]"]:checked')?.value || 'I';
                bukaModalLihat(id, nama, status, d.url);
            });
            td.appendChild(btn);
        } else if (d && d.ada && d.url && !d.temporary) {
            // Sudah permanen — tidak tampilkan tombol (kolom tersembunyi)
            td.innerHTML = '';
        } else {
            td.innerHTML = '<span style="color:#9ca3af;font-size:11px;">—</span>';
        }
        updateKolomBukti();
    }

    // Inject td bukti ke tiap baris tbody
    form.querySelectorAll('tbody tr').forEach(function (tr) {
        var radio = tr.querySelector('input[type="radio"][name*="status_kehadiran"]');
        if (!radio) return;
        var id   = radio.name.match(/\[(\d+)\]/)?.[1];
        var nama = tr.querySelectorAll('td')[2]?.textContent?.trim() || '';
        tr.setAttribute('data-mhs-id', id);
        var tdBukti = document.createElement('td');
        tdBukti.className = 'td-bukti';
        tdBukti.style.cssText = 'text-align:center;padding:4px;display:none;';
        tr.appendChild(tdBukti);
        renderBadgeFoto(id, nama);
    });
    updateKolomBukti();

    // ── Modal LIHAT foto ──────────────────────────────────────────────
    var modalLihat = document.createElement('div');
    modalLihat.className = 'modal-overlay';
    modalLihat.innerHTML = [
        '<div class="modal" style="max-width:420px;">',
            '<div class="modal-header">',
                '<span class="modal-title" id="modal-lihat-judul">Bukti Foto</span>',
                '<button type="button" class="modal-close" id="modal-lihat-tutup">✕</button>',
            '</div>',
            '<div class="modal-body" style="text-align:center;">',
                '<p id="modal-lihat-nama" style="margin:0 0 12px;font-size:13px;font-weight:600;color:#1e293b;"></p>',
                '<img id="modal-lihat-img" src="" style="max-width:100%;max-height:300px;border-radius:8px;object-fit:contain;border:1px solid #e2e8f0;">',
                '<div style="margin-top:10px;">',
                    '<a id="modal-lihat-link" href="" target="_blank" style="font-size:12px;color:#3b82f6;">Buka di tab baru ↗</a>',
                '</div>',
            '</div>',
            '<div style="display:flex;gap:8px;justify-content:flex-end;padding:0 16px 16px;">',
                '<button id="modal-lihat-ganti" type="button" class="btn btn-outline">✏️ Ganti Foto</button>',
                '<button id="modal-lihat-tutup2" type="button" class="btn btn-primary">Tutup</button>',
            '</div>',
        '</div>',
    ].join('');
    document.body.appendChild(modalLihat);

    var lihatJudul  = modalLihat.querySelector('#modal-lihat-judul');
    var lihatNama   = modalLihat.querySelector('#modal-lihat-nama');
    var lihatImg    = modalLihat.querySelector('#modal-lihat-img');
    var lihatLink   = modalLihat.querySelector('#modal-lihat-link');
    var lihatTutup  = modalLihat.querySelector('#modal-lihat-tutup');
    var lihatTutup2 = modalLihat.querySelector('#modal-lihat-tutup2');
    var lihatGanti  = modalLihat.querySelector('#modal-lihat-ganti');
    var lihatMhsId  = null;
    var lihatNamaStr = '';

    function bukaModalLihat(id, nama, status, url) {
        lihatMhsId   = id;
        lihatNamaStr = nama;
        lihatJudul.textContent = 'Bukti Foto — ' + (status === 'S' ? 'Sakit' : 'Izin');
        lihatNama.textContent  = nama;
        lihatImg.src           = url;
        lihatLink.href         = url;
        modalLihat.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function tutupModalLihat() {
        modalLihat.classList.remove('open');
        document.body.style.overflow = '';
        lihatMhsId = null;
    }

    lihatTutup.addEventListener('click',  tutupModalLihat);
    lihatTutup2.addEventListener('click', tutupModalLihat);
    modalLihat.addEventListener('click',  function (e) { if (e.target === modalLihat) tutupModalLihat(); });

    lihatGanti.addEventListener('click', function () {
        var id   = lihatMhsId;
        var nama = lihatNamaStr;
        tutupModalLihat();
        var radioEl = form.querySelector('input[name="presensi[' + id + '][status_kehadiran]"]:checked');
        setTimeout(function () { bukaModalUpload(id, nama, radioEl, true); }, 100);
    });

    // ── Modal UPLOAD foto (AJAX — langsung ke server) ─────────────────
    var modalUpload = document.createElement('div');
    modalUpload.className = 'modal-overlay';
    modalUpload.innerHTML = [
        '<div class="modal" style="max-width:440px;">',
            '<div class="modal-header" style="background:#F0FDF4;border-bottom:1px solid #BBF7D0;">',
                '<span class="modal-title" style="color:#15803D;" id="modal-upload-judul">Upload Bukti Foto</span>',
                '<button type="button" class="modal-close" id="modal-upload-tutup">✕</button>',
            '</div>',
            '<div class="modal-body">',
                '<p id="modal-upload-nama" style="margin:0 0 8px;font-size:13px;font-weight:600;color:#1e293b;"></p>',
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
                '<div id="modal-upload-progress" style="display:none;margin-top:12px;">',
                    '<div style="height:4px;background:#e2e8f0;border-radius:4px;overflow:hidden;">',
                        '<div id="modal-progress-bar" style="height:100%;background:#22c55e;width:0%;transition:width .3s;"></div>',
                    '</div>',
                    '<p id="modal-upload-status" style="margin:6px 0 0;font-size:12px;color:#64748b;text-align:center;">Mengupload…</p>',
                '</div>',
            '</div>',
            '<div style="display:flex;gap:8px;justify-content:flex-end;padding:0 16px 16px;">',
                '<button id="modal-upload-batal" type="button" class="btn btn-outline">Batal</button>',
                '<button id="modal-upload-simpan" type="button" class="btn btn-primary" disabled style="opacity:.5;">Upload & Konfirmasi</button>',
            '</div>',
        '</div>',
    ].join('');
    document.body.appendChild(modalUpload);

    var uploadJudul    = modalUpload.querySelector('#modal-upload-judul');
    var uploadNama     = modalUpload.querySelector('#modal-upload-nama');
    var uploadTutup    = modalUpload.querySelector('#modal-upload-tutup');
    var dropArea       = modalUpload.querySelector('#modal-drop-area');
    var fileInput      = modalUpload.querySelector('#modal-file-input');
    var fileNameEl     = modalUpload.querySelector('#modal-file-name');
    var previewWrap    = modalUpload.querySelector('#modal-preview-wrap');
    var previewImg     = modalUpload.querySelector('#modal-preview-img');
    var previewName    = modalUpload.querySelector('#modal-preview-name');
    var progressWrap   = modalUpload.querySelector('#modal-upload-progress');
    var progressBar    = modalUpload.querySelector('#modal-progress-bar');
    var progressStatus = modalUpload.querySelector('#modal-upload-status');
    var uploadBatal    = modalUpload.querySelector('#modal-upload-batal');
    var uploadSimpan   = modalUpload.querySelector('#modal-upload-simpan');

    var uploadMhsId   = null;
    var uploadNamaStr = '';
    var uploadRadioEl = null;
    var uploadIsGanti = false;
    var uploadFile    = null;

    function bukaModalUpload(id, nama, radioEl, isGanti) {
        uploadMhsId   = id;
        uploadNamaStr = nama;
        uploadRadioEl = radioEl;
        uploadIsGanti = !!isGanti;
        uploadFile    = null;
        uploadJudul.textContent = 'Upload Bukti — ' + (radioEl?.value === 'S' ? 'Sakit' : 'Izin');
        uploadNama.textContent  = nama;
        resetUpload();
        modalUpload.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function tutupModalUpload(batalkan) {
        modalUpload.classList.remove('open');
        document.body.style.overflow = '';
        if (batalkan && !uploadIsGanti && uploadRadioEl) {
            // Kembalikan radio ke kondisi semula
            uploadRadioEl.checked = false;
            simpanDraft();
        }
        uploadMhsId   = null;
        uploadNamaStr = '';
        uploadRadioEl = null;
        uploadIsGanti = false;
        uploadFile    = null;
    }

    function resetUpload() {
        fileInput.value = '';
        fileNameEl.textContent = 'JPG, PNG, WEBP · Maks 5 MB';
        previewWrap.style.display    = 'none';
        progressWrap.style.display   = 'none';
        previewImg.src   = '';
        previewName.textContent = '';
        progressBar.style.width  = '0%';
        uploadSimpan.disabled    = true;
        uploadSimpan.style.opacity = '.5';
        dropArea.style.borderColor = '#cbd5e1';
        dropArea.style.background  = '';
    }

    function pilihanFile(file) {
        if (!file || !file.type.startsWith('image/')) { alert('File harus berupa gambar.'); return; }
        if (file.size > 5 * 1024 * 1024) { alert('Ukuran file maksimal 5 MB.'); return; }
        uploadFile = file;
        fileNameEl.textContent = file.name;
        var reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
            previewName.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
            previewWrap.style.display = 'block';
        };
        reader.readAsDataURL(file);
        uploadSimpan.disabled      = false;
        uploadSimpan.style.opacity = '1';
        dropArea.style.borderColor = '#6366f1';
        dropArea.style.background  = '#f5f3ff';
    }

    dropArea.addEventListener('click',    function () { fileInput.click(); });
    fileInput.addEventListener('change',  function () { if (fileInput.files[0]) pilihanFile(fileInput.files[0]); });
    dropArea.addEventListener('dragover', function (e) { e.preventDefault(); dropArea.style.borderColor = '#6366f1'; dropArea.style.background = '#f5f3ff'; });
    dropArea.addEventListener('dragleave',function ()  { dropArea.style.borderColor = '#cbd5e1'; dropArea.style.background = ''; });
    dropArea.addEventListener('drop',     function (e) { e.preventDefault(); if (e.dataTransfer.files[0]) pilihanFile(e.dataTransfer.files[0]); });

    // ── Klik "Upload & Konfirmasi" → AJAX ke server ───────────────────
    uploadSimpan.addEventListener('click', function () {
        if (!uploadFile || !uploadMhsId) return;

        var id   = uploadMhsId;
        var nama = uploadNamaStr;

        uploadSimpan.disabled      = true;
        uploadSimpan.style.opacity = '.5';
        uploadBatal.disabled       = true;
        progressWrap.style.display = 'block';
        progressBar.style.width    = '30%';
        progressStatus.textContent = 'Mengupload…';

        var fd = new FormData();
        fd.append('_token',       CSRF);
        fd.append('mahasiswa_id', id);
        fd.append('pertemuan_ke', PERTEMUAN);
        fd.append('foto',         uploadFile);

        fetch(UPLOAD_URL, { method: 'POST', body: fd })
            .then(function (res) {
                if (!res.ok && res.headers.get('content-type')?.includes('text/html')) {
                    throw new Error('Server error ' + res.status + '. Pastikan migration sudah dijalankan (php artisan migrate).');
                }
                return res.json();
            })
            .then(function (json) {
                if (!json.success) throw new Error(json.pesan || 'Upload gagal');

                progressBar.style.width    = '100%';
                progressStatus.textContent = '✓ Berhasil diupload!';
                progressStatus.style.color = '#15803d';

                // Update fotoData lokal — tandai sebagai temporary sampai form di-submit
                fotoData[id] = { ada: true, url: json.url, temporary: true };

                // Render ulang badge di baris
                renderBadgeFoto(id, nama);

                setTimeout(function () {
                    modalUpload.classList.remove('open');
                    document.body.style.overflow = '';
                    uploadMhsId   = null;
                    uploadNamaStr = '';
                    uploadRadioEl = null;
                    uploadIsGanti = false;
                    uploadFile    = null;
                    simpanDraft();
                }, 700);
            })
            .catch(function (err) {
                progressBar.style.width    = '100%';
                progressBar.style.background = '#ef4444';
                progressStatus.textContent = '✗ ' + (err.message || 'Gagal upload. Coba lagi.');
                progressStatus.style.color = '#ef4444';
                uploadSimpan.disabled      = false;
                uploadSimpan.style.opacity = '1';
                uploadBatal.disabled       = false;
            });
    });

    uploadBatal.addEventListener('click', function () { tutupModalUpload(true); });
    uploadTutup.addEventListener('click', function () { tutupModalUpload(true); });
    modalUpload.addEventListener('click', function (e) { if (e.target === modalUpload) tutupModalUpload(true); });

    // ── Modal konfirmasi ganti status dari I/S ke status lain ────────
    var modalKonfirmGanti = document.createElement('div');
    modalKonfirmGanti.className = 'modal-overlay';
    modalKonfirmGanti.innerHTML = [
        '<div class="modal" style="max-width:400px;">',
            '<div class="modal-header" style="background:#FEF2F2;border-bottom:1px solid #FECACA;">',
                '<span class="modal-title" style="color:#991B1B;">⚠ Ganti Status</span>',
                '<button type="button" class="modal-close" id="konfirm-ganti-tutup">✕</button>',
            '</div>',
            '<div class="modal-body">',
                '<p id="konfirm-ganti-pesan" style="margin:0;font-size:14px;color:#374151;line-height:1.6;"></p>',
            '</div>',
            '<div style="display:flex;gap:8px;justify-content:flex-end;padding:0 16px 16px;">',
                '<button id="konfirm-ganti-batal" type="button" class="btn btn-outline">Batal</button>',
                '<button id="konfirm-ganti-ya" type="button" class="btn" style="background:#ef4444;color:#fff;border:none;">Ya, Ganti Status</button>',
            '</div>',
        '</div>',
    ].join('');
    document.body.appendChild(modalKonfirmGanti);

    var konfirmGantiYa    = modalKonfirmGanti.querySelector('#konfirm-ganti-ya');
    var konfirmGantiBatal = modalKonfirmGanti.querySelector('#konfirm-ganti-batal');
    var konfirmGantiTutup = modalKonfirmGanti.querySelector('#konfirm-ganti-tutup');
    var konfirmGantiPesan = modalKonfirmGanti.querySelector('#konfirm-ganti-pesan');
    var konfirmGantiCallback = null;
    var konfirmGantiBatalCallback = null;

    function bukaModalKonfirmGanti(pesan, onYa, onBatal) {
        konfirmGantiPesan.innerHTML = pesan;
        konfirmGantiCallback       = onYa;
        konfirmGantiBatalCallback  = onBatal;
        modalKonfirmGanti.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function tutupModalKonfirmGanti(isBatal) {
        modalKonfirmGanti.classList.remove('open');
        document.body.style.overflow = '';
        if (isBatal && konfirmGantiBatalCallback) konfirmGantiBatalCallback();
        konfirmGantiCallback       = null;
        konfirmGantiBatalCallback  = null;
    }

    konfirmGantiYa.addEventListener('click',    function () { var cb = konfirmGantiCallback; tutupModalKonfirmGanti(false); if (cb) cb(); });
    konfirmGantiBatal.addEventListener('click', function () { tutupModalKonfirmGanti(true); });
    konfirmGantiTutup.addEventListener('click', function () { tutupModalKonfirmGanti(true); });
    modalKonfirmGanti.addEventListener('click', function (e) { if (e.target === modalKonfirmGanti) tutupModalKonfirmGanti(true); });

    // ── Intercept klik radio — semua status ───────────────────────────
    form.querySelectorAll('input[type="radio"][name*="status_kehadiran"]').forEach(function (r) {
        r.addEventListener('change', function () {
            var id = r.name.match(/\[(\d+)\]/)?.[1];
            if (!id) { simpanDraft(); return; }

            var row      = r.closest('tr');
            var nama     = row ? (row.querySelectorAll('td')[2]?.textContent?.trim() || '') : '';
            var adaFoto  = fotoData[id] && fotoData[id].ada && fotoData[id].url;
            var statusLama = null;
            row.querySelectorAll('input[type="radio"][name*="status_kehadiran"]').forEach(function (rr) {
                if (rr !== r && rr.defaultChecked) statusLama = rr.value;
            });

            // Dari I/S ke H/A — perlu konfirmasi karena ada/akan ada bukti
            if ((statusLama === 'I' || statusLama === 'S') && (r.value === 'H' || r.value === 'A')) {
                var radioSebelum = r;
                // Kembalikan dulu ke status lama secara visual
                row.querySelectorAll('input[type="radio"][name*="status_kehadiran"]').forEach(function (rr) {
                    rr.checked = (rr.value === statusLama);
                });
                bukaModalKonfirmGanti(
                    'Status mahasiswa <strong>' + nama + '</strong> akan diubah dari <strong>' + (statusLama === 'I' ? 'Izin' : 'Sakit') + '</strong> ke <strong>' + (r.value === 'H' ? 'Hadir' : 'Alpha') + '</strong>.<br><br>' +
                    (adaFoto ? '⚠ Bukti foto yang sudah diupload <strong>akan dihapus</strong>.' : ''),
                    function () {
                        // User setuju → ganti status, hapus foto dari tampilan
                        radioSebelum.checked = true;
                        if (adaFoto) {
                            // Hapus foto dari fotoData lokal (server bersihkan saat submit)
                            fotoData[id] = { ada: false, temporary: false, url: null };
                            renderBadgeFoto(id, nama);
                        }
                        simpanDraft();
                    },
                    function () {
                        // User batal → tidak ada perubahan, sudah dikembalikan ke statusLama
                    }
                );
                return;
            }

            // Dari status lain ke I/S
            if (r.value === 'I' || r.value === 'S') {
                // Sudah ada foto (temporary atau permanen) → tampilkan modal lihat
                if (adaFoto) {
                    bukaModalLihat(id, nama, r.value, fotoData[id].url);
                    simpanDraft();
                    return;
                }
                // Belum ada foto → minta upload
                bukaModalUpload(id, nama, r, false);
                return;
            }

            // H atau A tanpa riwayat I/S → simpan draft biasa
            simpanDraft();
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
            if (fotoData[id] && fotoData[id].ada) return;
            kurang.push(id);
        });
        if (kurang.length) {
            e.preventDefault();
            alert('⚠ ' + kurang.length + ' mahasiswa dengan Sakit/Izin belum ada bukti foto.');
            return;
        }
        // Setelah submit: semua foto jadi permanen — sembunyikan kolom Bukti
        Object.keys(fotoData).forEach(function (id) {
            if (fotoData[id]) fotoData[id].temporary = false;
        });
        updateKolomBukti();
        hapusDraft();
    });

    // Catatan: draft sengaja TIDAK dihapus otomatis saat pagehide/navigasi keluar.
    // sessionStorage sudah otomatis hilang sendiri saat tab ditutup, dan kalau
    // user balik lagi ke pertemuan yang sama, draft yang belum sempat disimpan
    // akan otomatis dipulihkan — termasuk saat refresh (F5) tidak sengaja.
    // Draft hanya dihapus secara eksplisit saat submit berhasil (di atas).
})();
</script>

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
    // Selalu restore draft saat load, termasuk setelah refresh tidak sengaja
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

    // Catatan: draft sengaja TIDAK dihapus otomatis saat pagehide/navigasi
    // keluar (lihat penjelasan di script presensi utama di atas) — supaya
    // refresh (F5) tidak sengaja tidak menghapus draft yang belum disimpan.
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