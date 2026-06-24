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

});
