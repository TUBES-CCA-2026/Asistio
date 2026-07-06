/* ═══════════════════════════════════════════════════════════════
   Asistio — app.js
   Sidebar toggle | Modal open/close | Alert dismiss
═══════════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', function () {

    // ── Sidebar mobile toggle ──────────────────────────────────
    const sidebar  = document.getElementById('sidebar');
    const toggle   = document.getElementById('menuToggle');
    const overlay  = document.getElementById('sidebarOverlay');

    function openSidebar()  { sidebar?.classList.add('open'); overlay?.classList.add('open'); }
    function closeSidebar() { sidebar?.classList.remove('open'); overlay?.classList.remove('open'); }

    toggle?.addEventListener('click', () => {
        sidebar?.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    overlay?.addEventListener('click', closeSidebar);

    // ── Modal open/close ───────────────────────────────────────
    // Buka modal: data-modal-open="namaId"
    document.querySelectorAll('[data-modal-open]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-modal-open');
            document.getElementById(id)?.classList.add('open');
            document.body.style.overflow = 'hidden';
        });
    });

    // ── Dropdown toggle ──────────────────────────────────────────
    document.querySelectorAll('[data-dropdown-toggle]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const menu = document.getElementById(btn.getAttribute('data-dropdown-toggle'));
            if (!menu) return;
            const isOpen = menu.classList.contains('open');
            document.querySelectorAll('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
            if (!isOpen) {
                menu.classList.add('open');
                const rect = btn.getBoundingClientRect();
                let left = rect.right - menu.offsetWidth;
                if (left < 8) left = 8;
                menu.style.top  = (rect.bottom + 6) + 'px';
                menu.style.left = left + 'px';
            }
        });
    });
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
    });
    window.addEventListener('scroll', () => {
        document.querySelectorAll('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
    }, true);

    // Tutup modal: data-modal-close="namaId" atau klik overlay
    document.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-modal-close');
            document.getElementById(id)?.classList.remove('open');
            document.body.style.overflow = '';
        });
    });

    // Klik di luar modal (pada overlay) juga menutup
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) {
                this.classList.remove('open');
                document.body.style.overflow = '';
            }
        });
    });

    // ESC menutup semua modal
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.open').forEach(m => {
                m.classList.remove('open');
            });
            document.body.style.overflow = '';
        }
    });

    // ── Alert dismiss ──────────────────────────────────────────
    document.querySelectorAll('.alert-close').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.alert')?.remove();
        });
    });

    // Auto-dismiss flash setelah 5 detik
    setTimeout(() => {
        document.querySelectorAll('.flash-container .alert').forEach(a => {
            a.style.transition = 'opacity .4s';
            a.style.opacity = '0';
            setTimeout(() => a.remove(), 400);
        });
    }, 5000);

    // ── Toggle password visibility ─────────────────────────────
    document.getElementById('togglePassword')?.addEventListener('click', function () {
        const input = document.getElementById('password');
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
        this.style.color = input.type === 'text' ? 'var(--primary)' : '';
    });

    // ── Bulk presensi status ───────────────────────────────────
    // Tombol "Tandai semua Hadir / Alpha"
    document.querySelectorAll('.status-btn-bulk').forEach(btn => {
        btn.addEventListener('click', () => {
            const status = btn.getAttribute('data-status');
            document.querySelectorAll(`input[type="radio"][value="${status}"]`).forEach(r => {
                r.checked = true;
            });
        });
    });


    // ── SEARCH + DROPDOWN SYNC — Tambah Praktikan ke Kelas ──────────
    const cariMhs   = document.getElementById('cariMhs');
    const previewMhs = document.getElementById('previewMhs');
    const selectMhs = document.getElementById('selectMhs');

    if (cariMhs && selectMhs && previewMhs) {
        const semuaOpt = Array.from(selectMhs.options).filter(o => o.value);

        // Tampilkan preview hasil pencarian
        function posisikanPreview() {
            const rect = cariMhs.getBoundingClientRect();
            previewMhs.style.top   = (rect.bottom + 4) + 'px';
            previewMhs.style.left  = rect.left + 'px';
            previewMhs.style.width = rect.width + 'px';
        }

        function tampilkanPreview(q) {
            previewMhs.innerHTML = '';
            if (!q) { previewMhs.classList.remove('open'); return; }

            const cocok = semuaOpt.filter(o =>
                o.dataset.cari.includes(q.toLowerCase())
            ).slice(0, 30);

            if (cocok.length === 0) {
                previewMhs.innerHTML = '<div class="search-result-empty">Tidak ditemukan.</div>';
            } else {
                cocok.forEach(opt => {
                    const [nim, ...namaParts] = opt.dataset.label.split(' — ');
                    const nama = namaParts.join(' — ');
                    const item = document.createElement('div');
                    item.className = 'search-result-item';
                    item.innerHTML =
                        '<span class="search-result-nim">' + nim + '</span>' +
                        '<span class="search-result-nama">' + nama + '</span>';
                    item.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        // Klik preview → update dropdown + isi textfield
                        selectMhs.value = opt.value;
                        cariMhs.value   = opt.dataset.label;
                        previewMhs.classList.remove('open');
                    });
                    previewMhs.appendChild(item);
                });
            }
            posisikanPreview();
            previewMhs.classList.add('open');
        }

        // Ketik di textfield → tampilkan preview
        cariMhs.addEventListener('input', function () {
            tampilkanPreview(this.value.trim());
        });
        cariMhs.addEventListener('focus', function () {
            if (this.value.trim()) tampilkanPreview(this.value.trim());
        });
        cariMhs.addEventListener('blur', function () {
            setTimeout(() => previewMhs.classList.remove('open'), 150);
        });

        // Pilih dari dropdown → isi textfield secara otomatis
        selectMhs.addEventListener('change', function () {
            const opt = this.selectedOptions[0];
            cariMhs.value = opt?.dataset.label || '';
            previewMhs.classList.remove('open');
        });

        // Klik di luar → tutup preview
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.search-combobox') && e.target !== selectMhs) {
                previewMhs.classList.remove('open');
            }
        });

        // Tutup preview saat scroll agar posisi tidak basi
        window.addEventListener('scroll', function () {
            previewMhs.classList.remove('open');
        }, true);
    }

    // ── SEARCH + DROPDOWN SYNC — Asisten 1 ──────────────────────────
    const cariA1    = document.getElementById('cariA1');
    const previewA1 = document.getElementById('previewA1');
    const selectA1  = document.getElementById('selectA1');

    if (cariA1 && selectA1 && previewA1) {
        const optsA1 = Array.from(selectA1.options).filter(o => o.value);

        function posisiA1() {
            const r = cariA1.getBoundingClientRect();
            previewA1.style.top   = (r.bottom + 4) + 'px';
            previewA1.style.left  = r.left + 'px';
            previewA1.style.width = r.width + 'px';
        }

        function tampilA1(q) {
            previewA1.innerHTML = '';
            if (!q) { previewA1.classList.remove('open'); return; }
            const cocok = optsA1.filter(o => o.dataset.cari.includes(q.toLowerCase())).slice(0, 30);
            if (cocok.length === 0) {
                previewA1.innerHTML = '<div class="search-result-empty">Tidak ditemukan.</div>';
            } else {
                cocok.forEach(opt => {
                    const [nim, ...namaParts] = opt.dataset.label.split(' — ');
                    const item = document.createElement('div');
                    item.className = 'search-result-item';
                    item.innerHTML =
                        '<span class="search-result-nim">' + nim + '</span>' +
                        '<span class="search-result-nama">' + namaParts.join(' — ') + '</span>';
                    item.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        selectA1.value = opt.value;
                        cariA1.value   = opt.dataset.label;
                        previewA1.classList.remove('open');
                    });
                    previewA1.appendChild(item);
                });
            }
            posisiA1();
            previewA1.classList.add('open');
        }

        cariA1.addEventListener('input', function () { tampilA1(this.value.trim()); });
        cariA1.addEventListener('focus', function () { if (this.value.trim()) tampilA1(this.value.trim()); });
        cariA1.addEventListener('blur',  function () { setTimeout(() => previewA1.classList.remove('open'), 150); });
        selectA1.addEventListener('change', function () {
            const opt = this.selectedOptions[0];
            cariA1.value = opt?.dataset.label || '';
            previewA1.classList.remove('open');
        });
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.search-combobox') && e.target !== selectA1) {
                previewA1.classList.remove('open');
            }
        });
        window.addEventListener('scroll', function () { previewA1.classList.remove('open'); }, true);
    }

    // ── SEARCH + DROPDOWN SYNC — Asisten 2 ──────────────────────────
    const cariA2    = document.getElementById('cariA2');
    const previewA2 = document.getElementById('previewA2');
    const selectA2  = document.getElementById('selectA2');

    if (cariA2 && selectA2 && previewA2) {
        const optsA2 = Array.from(selectA2.options).filter(o => o.value);

        function posisiA2() {
            const r = cariA2.getBoundingClientRect();
            previewA2.style.top   = (r.bottom + 4) + 'px';
            previewA2.style.left  = r.left + 'px';
            previewA2.style.width = r.width + 'px';
        }

        function tampilA2(q) {
            previewA2.innerHTML = '';
            if (!q) { previewA2.classList.remove('open'); return; }
            const cocok = optsA2.filter(o => o.dataset.cari.includes(q.toLowerCase())).slice(0, 30);
            if (cocok.length === 0) {
                previewA2.innerHTML = '<div class="search-result-empty">Tidak ditemukan.</div>';
            } else {
                cocok.forEach(opt => {
                    const [nim, ...namaParts] = opt.dataset.label.split(' — ');
                    const item = document.createElement('div');
                    item.className = 'search-result-item';
                    item.innerHTML =
                        '<span class="search-result-nim">' + nim + '</span>' +
                        '<span class="search-result-nama">' + namaParts.join(' — ') + '</span>';
                    item.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        selectA2.value = opt.value;
                        cariA2.value   = opt.dataset.label;
                        previewA2.classList.remove('open');
                    });
                    previewA2.appendChild(item);
                });
            }
            posisiA2();
            previewA2.classList.add('open');
        }

        cariA2.addEventListener('input', function () { tampilA2(this.value.trim()); });
        cariA2.addEventListener('focus', function () { if (this.value.trim()) tampilA2(this.value.trim()); });
        cariA2.addEventListener('blur',  function () { setTimeout(() => previewA2.classList.remove('open'), 150); });
        selectA2.addEventListener('change', function () {
            const opt = this.selectedOptions[0];
            cariA2.value = opt?.dataset.label || '';
            previewA2.classList.remove('open');
        });
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.search-combobox') && e.target !== selectA2) {
                previewA2.classList.remove('open');
            }
        });
        window.addEventListener('scroll', function () { previewA2.classList.remove('open'); }, true);
    }
});
