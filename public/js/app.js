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
});
