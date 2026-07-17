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
    // KECUALI jika klik berasal dari dalam .search-results (dropdown combobox)
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function (e) {
            // Jangan tutup modal jika:
            // 1. Klik bukan tepat pada overlay (klik di dalam modal)
            if (e.target !== this) return;
            // 2. Ada preview combobox yang sedang terbuka
            if (document.querySelector('.search-results.open')) return;
            // 3. Flag global sedangPilihCombobox aktif
            if (window._sedangPilihCombobox) return;
            this.classList.remove('open');
            document.body.style.overflow = '';
        });
    });

    // ESC menutup dropdown dulu, baru modal jika tidak ada dropdown terbuka
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            const adaDropdown = document.querySelector('.search-results.open');
            if (adaDropdown) {
                // biarkan buatCombobox yang tangani tutup dropdown-nya
                return;
            }
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

    // ── SMART COMBOBOX — helper reusable ─────────────────────────────
    window._sedangPilihCombobox = false; // flag global: ada item yang sedang diklik

    function buatCombobox({ data, inputEl, hiddenEl, previewEl, clearable = true }) {
        if (!inputEl || !hiddenEl || !previewEl) return;

        let focusedIdx = -1;

        function posisi() {
            const r = inputEl.getBoundingClientRect();
            previewEl.style.top   = (r.bottom + 4) + 'px';
            previewEl.style.left  = r.left + 'px';
            previewEl.style.width = r.width + 'px';
        }

        function bersihkan(teks) {
            return teks.replace(/\s*—\s*/g, ' ').replace(/\s+/g, ' ').trim().toLowerCase();
        }

        function tutup() {
            previewEl.classList.remove('open');
            focusedIdx = -1;
        }

        function pilih(d) {
            hiddenEl.value = d.value;
            inputEl.value  = d.label;
            tutup();
            // Trigger change agar draft persistence menangkap perubahan
            hiddenEl.dispatchEvent(new Event('change', { bubbles: true }));
            inputEl.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function highlight(idx) {
            const items = previewEl.querySelectorAll('.search-result-item');
            items.forEach(el => el.classList.remove('keyboard-focus'));
            if (idx >= 0 && idx < items.length) {
                items[idx].classList.add('keyboard-focus');
                items[idx].scrollIntoView({ block: 'nearest' });
            }
            focusedIdx = idx;
        }

        function tampil(q) {
            previewEl.innerHTML = '';
            focusedIdx = -1;
            const qBersih = bersihkan(q);
            const list    = qBersih ? data.filter(d => d.cari.includes(qBersih)) : data;
            const slice   = list.slice(0, 50);

            if (slice.length === 0) {
                previewEl.innerHTML = '<div class="search-result-empty">Tidak ditemukan.</div>';
            } else {
                slice.forEach(d => {
                    const item = document.createElement('div');
                    item.className = 'search-result-item';
                    item.innerHTML = d.kolom1
                        ? '<span class="search-result-nim">' + d.kolom1 + '</span><span class="search-result-nama">' + d.kolom2 + '</span>'
                        : '<span class="search-result-nama">' + d.label + '</span>';

                    // mousedown: set flag global agar overlay & blur tahu ada pilihan aktif
                    item.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        window._sedangPilihCombobox = true;
                    });

                    // pointerup: commit pilihan, reset flag
                    item.addEventListener('pointerup', function (e) {
                        e.stopPropagation();
                        pilih(d);
                        window._sedangPilihCombobox = false;
                        inputEl.focus();
                    });

                    previewEl.appendChild(item);
                });
            }
            posisi();
            previewEl.classList.add('open');
        }

        inputEl.addEventListener('keydown', function (e) {
            const isOpen = previewEl.classList.contains('open');
            const items  = previewEl.querySelectorAll('.search-result-item');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!isOpen) { tampil(this.value); return; }
                highlight(Math.min(focusedIdx + 1, items.length - 1));
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (!isOpen) return;
                highlight(Math.max(focusedIdx - 1, 0));
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (isOpen && focusedIdx >= 0 && items[focusedIdx]) {
                    pilih(data.find(d => d.label === items[focusedIdx].textContent.trim()) || { value: hiddenEl.value, label: inputEl.value });
                }
            } else if (e.key === 'Escape') {
                e.stopPropagation(); // jangan sampai ESC tutup modal juga
                tutup();
            }
        });

        inputEl.addEventListener('input', function () {
            if (clearable && this.value === '') hiddenEl.value = '';
            tampil(this.value);
        });

        inputEl.addEventListener('focus', function () {
            const sudahDipilih = hiddenEl.value !== '' && data.find(d => d.label === this.value.trim());
            if (!sudahDipilih) tampil('');
        });

        inputEl.addEventListener('blur', function () {
            // Jika sedang proses klik item, jangan tutup dan jangan reset
            if (window._sedangPilihCombobox) return;

            if (clearable) {
                const cocok = data.find(d => d.label === this.value.trim());
                if (!cocok) {
                    this.value     = '';
                    hiddenEl.value = '';
                }
            }
            setTimeout(function () {
                if (!window._sedangPilihCombobox) tutup();
            }, 300);
        });

        // Klik di luar: tutup preview tapi jangan rambat ke overlay modal
        document.addEventListener('click', function (e) {
            if (window._sedangPilihCombobox) return;
            if (!e.target.closest('.search-combobox') && !previewEl.contains(e.target)) {
                tutup();
            }
        });

        previewEl.addEventListener('wheel', function (e) {
            const atTop    = previewEl.scrollTop === 0;
            const atBottom = previewEl.scrollTop + previewEl.offsetHeight >= previewEl.scrollHeight;
            if ((e.deltaY < 0 && atTop) || (e.deltaY > 0 && atBottom)) e.preventDefault();
            e.stopPropagation();
        }, { passive: false });

        window.addEventListener('scroll', function (e) {
            if (!previewEl.contains(e.target) && e.target !== previewEl) tutup();
        }, true);
    }

    // ── MAHASISWA (tambah praktikan) ─────────────────────────────────
    buatCombobox({
        inputEl   : document.getElementById('cariMhs'),
        hiddenEl  : document.getElementById('hidMhs'),
        previewEl : document.getElementById('previewMhs'),
        data      : Array.from(document.querySelectorAll('#__dataMhs option')).filter(o => o.value).map(o => {
            const [kolom1, ...rest] = o.dataset.label.split(' — ');
            return { value: o.value, label: o.dataset.label, cari: o.dataset.cari, kolom1, kolom2: rest.join(' — ') };
        }),
    });

    // ── RUANGAN ──────────────────────────────────────────────────────
    buatCombobox({
        inputEl   : document.getElementById('cariRuangan'),
        hiddenEl  : document.getElementById('hidRuangan'),
        previewEl : document.getElementById('previewRuangan'),
        data      : Array.from(document.querySelectorAll('#__dataRuangan option')).filter(o => o.value).map(o => ({
            value : o.value,
            label : o.dataset.label,
            cari  : o.dataset.cari,
        })),
    });

    // ── DOSEN ────────────────────────────────────────────────────────
    buatCombobox({
        inputEl   : document.getElementById('cariDosen'),
        hiddenEl  : document.getElementById('hidDosen'),
        previewEl : document.getElementById('previewDosen'),
        data      : Array.from(document.querySelectorAll('#__dataDosen option')).filter(o => o.value).map(o => {
            const [kolom1, ...rest] = o.dataset.label.split(' — ');
            return { value: o.value, label: o.dataset.label, cari: o.dataset.cari, kolom1, kolom2: rest.join(' — ') };
        }),
    });

    // ── ASISTEN 1 ────────────────────────────────────────────────────
    buatCombobox({
        inputEl   : document.getElementById('cariA1'),
        hiddenEl  : document.getElementById('hidA1'),
        previewEl : document.getElementById('previewA1'),
        data      : Array.from(document.querySelectorAll('#__dataAsisten option')).filter(o => o.value).map(o => {
            const [kolom1, ...rest] = o.dataset.label.split(' — ');
            return { value: o.value, label: o.dataset.label, cari: o.dataset.cari, kolom1, kolom2: rest.join(' — ') };
        }),
    });

    // ── ASISTEN 2 ────────────────────────────────────────────────────
    buatCombobox({
        inputEl   : document.getElementById('cariA2'),
        hiddenEl  : document.getElementById('hidA2'),
        previewEl : document.getElementById('previewA2'),
        data      : Array.from(document.querySelectorAll('#__dataAsisten option')).filter(o => o.value).map(o => {
            const [kolom1, ...rest] = o.dataset.label.split(' — ');
            return { value: o.value, label: o.dataset.label, cari: o.dataset.cari, kolom1, kolom2: rest.join(' — ') };
        }),
    });

    // ── HARI ─────────────────────────────────────────────────────────
    buatCombobox({
        inputEl   : document.getElementById('cariHari'),
        hiddenEl  : document.getElementById('hidHari'),
        previewEl : document.getElementById('previewHari'),
        data      : Array.from(document.querySelectorAll('#__dataHari option')).filter(o => o.value).map(o => ({
            value : o.value,
            label : o.dataset.label,
            cari  : o.dataset.cari,
        })),
    });

    // ── JAM MULAI ────────────────────────────────────────────────────
    buatCombobox({
        inputEl   : document.getElementById('cariJamMulai'),
        hiddenEl  : document.getElementById('hidJamMulai'),
        previewEl : document.getElementById('previewJamMulai'),
        data      : Array.from(document.querySelectorAll('#__dataJamMulai option')).filter(o => o.value).map(o => ({
            value : o.value,
            label : o.dataset.label,
            cari  : o.dataset.cari,
        })),
    });

    // ── JAM SELESAI ──────────────────────────────────────────────────
    buatCombobox({
        inputEl   : document.getElementById('cariJamSelesai'),
        hiddenEl  : document.getElementById('hidJamSelesai'),
        previewEl : document.getElementById('previewJamSelesai'),
        data      : Array.from(document.querySelectorAll('#__dataJamSelesai option')).filter(o => o.value).map(o => ({
            value : o.value,
            label : o.dataset.label,
            cari  : o.dataset.cari,
        })),
    });

    // ── TAMBAH KELAS — Mata Kuliah ───────────────────────────────────
    buatCombobox({
        inputEl   : document.getElementById('cariTMK'),
        hiddenEl  : document.getElementById('hidTMK'),
        previewEl : document.getElementById('previewTMK'),
        data      : Array.from(document.querySelectorAll('#__dataTMK option')).filter(o => o.value).map(o => {
            const [kolom1, ...rest] = o.dataset.label.split(' — ');
            return { value: o.value, label: o.dataset.label, cari: o.dataset.cari, kolom1, kolom2: rest.join(' — ') };
        }),
    });

    // ── TAMBAH KELAS — Hari ──────────────────────────────────────────
    buatCombobox({
        inputEl   : document.getElementById('cariTHari'),
        hiddenEl  : document.getElementById('hidTHari'),
        previewEl : document.getElementById('previewTHari'),
        data      : Array.from(document.querySelectorAll('#__dataTHari option')).filter(o => o.value).map(o => ({
            value: o.value, label: o.dataset.label, cari: o.dataset.cari,
        })),
    });

    // ── TAMBAH KELAS — Jam Mulai ─────────────────────────────────────
    buatCombobox({
        inputEl   : document.getElementById('cariTJamMulai'),
        hiddenEl  : document.getElementById('hidTJamMulai'),
        previewEl : document.getElementById('previewTJamMulai'),
        data      : Array.from(document.querySelectorAll('#__dataTJamMulai option')).filter(o => o.value).map(o => ({
            value: o.value, label: o.dataset.label, cari: o.dataset.cari,
        })),
    });

    // ── TAMBAH KELAS — Jam Selesai ───────────────────────────────────
    buatCombobox({
        inputEl   : document.getElementById('cariTJamSelesai'),
        hiddenEl  : document.getElementById('hidTJamSelesai'),
        previewEl : document.getElementById('previewTJamSelesai'),
        data      : Array.from(document.querySelectorAll('#__dataTJamSelesai option')).filter(o => o.value).map(o => ({
            value: o.value, label: o.dataset.label, cari: o.dataset.cari,
        })),
    });

    // ── TAMBAH KELAS — Ruangan ───────────────────────────────────────
    buatCombobox({
        inputEl   : document.getElementById('cariTRuangan'),
        hiddenEl  : document.getElementById('hidTRuangan'),
        previewEl : document.getElementById('previewTRuangan'),
        data      : Array.from(document.querySelectorAll('#__dataTRuangan option')).filter(o => o.value).map(o => ({
            value: o.value, label: o.dataset.label, cari: o.dataset.cari,
        })),
    });

    // ── TAMBAH KELAS — Dosen ─────────────────────────────────────────
    buatCombobox({
        inputEl   : document.getElementById('cariTDosen'),
        hiddenEl  : document.getElementById('hidTDosen'),
        previewEl : document.getElementById('previewTDosen'),
        data      : Array.from(document.querySelectorAll('#__dataTDosen option')).filter(o => o.value).map(o => {
            const [kolom1, ...rest] = o.dataset.label.split(' — ');
            return { value: o.value, label: o.dataset.label, cari: o.dataset.cari, kolom1, kolom2: rest.join(' — ') };
        }),
    });

    // ── TAMBAH KELAS — Asisten 1 ─────────────────────────────────────
    buatCombobox({
        inputEl   : document.getElementById('cariTA1'),
        hiddenEl  : document.getElementById('hidTA1'),
        previewEl : document.getElementById('previewTA1'),
        data      : Array.from(document.querySelectorAll('#__dataTAsisten option')).filter(o => o.value).map(o => {
            const [kolom1, ...rest] = o.dataset.label.split(' — ');
            return { value: o.value, label: o.dataset.label, cari: o.dataset.cari, kolom1, kolom2: rest.join(' — ') };
        }),
    });

    // ── TAMBAH KELAS — Asisten 2 ─────────────────────────────────────
    buatCombobox({
        inputEl   : document.getElementById('cariTA2'),
        hiddenEl  : document.getElementById('hidTA2'),
        previewEl : document.getElementById('previewTA2'),
        data      : Array.from(document.querySelectorAll('#__dataTAsisten option')).filter(o => o.value).map(o => {
            const [kolom1, ...rest] = o.dataset.label.split(' — ');
            return { value: o.value, label: o.dataset.label, cari: o.dataset.cari, kolom1, kolom2: rest.join(' — ') };
        }),
    });

    // ── TABLE SEARCH & SORT ───────────────────────────────────────────
    document.querySelectorAll('table[data-table]').forEach(function (table) {
        const tbody      = table.querySelector('tbody');
        const allRows    = () => Array.from(tbody.querySelectorAll('tr:not(.row-empty)'));
        const searchInput = table.closest('.card')?.querySelector('.table-search');
        const countEl    = table.closest('.card')?.querySelector('.table-count');
        let sortCol = -1, sortDir = 1;

        // ── Inject sort icons ke semua th[data-col] ──
        table.querySelectorAll('th[data-col]').forEach(function (th) {
            th.innerHTML = th.innerHTML + '<span class="sort-icon" aria-hidden="true">⇅</span>';
        });

        function updateCount() {
            if (!countEl) return;
            const visible = allRows().filter(r => !r.classList.contains('row-hidden')).length;
            const total   = allRows().length;
            countEl.textContent = visible === total
                ? total + ' data'
                : visible + ' dari ' + total + ' data';
        }

        function applySearch(q) {
            const kata = q.trim().toLowerCase().replace(/[-–—]/g, ' ').replace(/\s+/g, ' ');
            allRows().forEach(function (tr) {
                const teks = tr.textContent.toLowerCase().replace(/[-–—]/g, ' ').replace(/\s+/g, ' ');
                tr.classList.toggle('row-hidden', kata !== '' && !teks.includes(kata));
            });
            updateCount();
        }

        function applySort(colIdx, dir) {
            const rows = allRows();
            rows.sort(function (a, b) {
                const ta = (a.cells[colIdx]?.dataset.val ?? a.cells[colIdx]?.textContent ?? '').trim().toLowerCase();
                const tb = (b.cells[colIdx]?.dataset.val ?? b.cells[colIdx]?.textContent ?? '').trim().toLowerCase();
                const na = parseFloat(ta.replace(/[^\d.]/g, ''));
                const nb = parseFloat(tb.replace(/[^\d.]/g, ''));
                if (!isNaN(na) && !isNaN(nb)) return (na - nb) * dir;
                return ta.localeCompare(tb, 'id') * dir;
            });
            rows.forEach(r => tbody.appendChild(r));
            updateCount();
        }

        // Klik header → sort
        table.querySelectorAll('th[data-col]').forEach(function (th) {
            th.addEventListener('click', function () {
                const col = parseInt(th.getAttribute('data-col'));
                if (sortCol === col) {
                    sortDir *= -1;
                } else {
                    sortCol = col;
                    sortDir = 1;
                }
                table.querySelectorAll('th[data-col]').forEach(h => {
                    h.classList.remove('sort-asc', 'sort-desc');
                    h.querySelector('.sort-icon').textContent = '⇅';
                });
                th.classList.add(sortDir === 1 ? 'sort-asc' : 'sort-desc');
                th.querySelector('.sort-icon').textContent = sortDir === 1 ? '↑' : '↓';
                applySort(col, sortDir);
            });
        });

        // Ketik di search → filter
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                applySearch(this.value);
            });
        }

        updateCount();
    });

    // ── VALIDASI NIM ASISTEN — hanya angka, strip karakter lain saat ketik ──
    document.querySelectorAll('[data-nim-input], #inputNimTambah').forEach(function (el) {
        el.addEventListener('input', function () {
            const pos = this.selectionStart;
            const cleaned = this.value.replace(/\D/g, '');
            if (this.value !== cleaned) {
                this.value = cleaned;
                // Kembalikan posisi kursor supaya tidak lompat ke akhir
                this.setSelectionRange(pos - 1, pos - 1);
            }
        });
        el.addEventListener('keydown', function (e) {
            // Izinkan: angka, backspace, delete, tab, enter, arrow keys, ctrl+a/c/v/x
            const allowed = [
                'Backspace','Delete','Tab','Enter',
                'ArrowLeft','ArrowRight','ArrowUp','ArrowDown',
                'Home','End',
            ];
            const isCtrl  = e.ctrlKey || e.metaKey;
            const isDigit = /^\d$/.test(e.key);
            if (!isDigit && !allowed.includes(e.key) && !isCtrl) {
                e.preventDefault();
            }
        });
    });

    // ── INPUT NILAI — hanya angka + satu titik desimal, blok semua huruf ──
    document.querySelectorAll('.input-nilai').forEach(function (el) {

        // Bersihkan: hanya boleh digit dan satu titik
        function bersihkan(val) {
            // Buang semua karakter bukan digit dan bukan titik
            let hasil = val.replace(/[^\d.]/g, '');
            // Kalau ada lebih dari satu titik, ambil hanya sampai titik pertama
            const parts = hasil.split('.');
            if (parts.length > 2) {
                hasil = parts[0] + '.' + parts.slice(1).join('');
            }
            return hasil;
        }

        // Blok karakter terlarang saat mengetik
        el.addEventListener('keydown', function (e) {
            const ctrl  = e.ctrlKey || e.metaKey;
            const navigasi = ['Backspace','Delete','Tab','Enter','ArrowLeft','ArrowRight','Home','End'];
            // Izinkan: angka, titik, navigasi, ctrl+a/c/v/x/z
            if (navigasi.includes(e.key) || ctrl) return;
            if (/^\d$/.test(e.key)) return; // angka 0-9
            if (e.key === '.') {
                // Tolak titik kedua
                if (this.value.includes('.')) e.preventDefault();
                return;
            }
            // Blok semua yang lain (huruf, simbol, dll)
            e.preventDefault();
        });

        // Bersihkan saat paste (user bisa paste teks sembarang)
        el.addEventListener('paste', function (e) {
            e.preventDefault();
            const tempel = (e.clipboardData || window.clipboardData).getData('text');
            const bersih = bersihkan(this.value.slice(0, this.selectionStart)
                + tempel
                + this.value.slice(this.selectionEnd));
            this.value = bersih;
        });

        // Bersihkan juga kalau ada karakter aneh yang masuk lewat autofill dll
        el.addEventListener('input', function () {
            const pos    = this.selectionStart;
            const bersih = bersihkan(this.value);
            if (this.value !== bersih) {
                this.value = bersih;
                this.setSelectionRange(pos, pos);
            }
        });
    });

    // ── RESET KOLOM NILAI (client-side saja, belum simpan ke DB) ────────
    document.querySelectorAll('[data-reset-field]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var field = this.getAttribute('data-reset-field');
            document.querySelectorAll('.input-nilai[name*="[' + field + ']"]')
                .forEach(function (input) {
                    var asalVal = input.dataset.asalNilai || '';
                    // Nilai asal bukan nol/kosong → reset ke '' = berubah → dirty
                    input.value = '';
                    cekDirtyInput(input);
                    // Tampilkan "—" visual
                    input.value = '';
                    input.classList.remove('nilai-kosong');
                });
            updateDirtyHint();
        });
    });

    // ── INPUT NILAI — tampilkan "—" saat kosong, hilang saat diklik ─────
    // ── + DIRTY TRACKING (oranye = belum disimpan) ───────────────────────
    function cekDirtyInput(el) {
        var asal    = el.dataset.asalNilai || '';
        var sekarang = el.value === '—' ? '' : el.value;
        if (sekarang !== asal) {
            el.classList.add('nilai-dirty');
            el.classList.remove('nilai-kosong');
        } else {
            el.classList.remove('nilai-dirty');
            if (sekarang === '') {
                el.value = '—';
                el.classList.add('nilai-kosong');
            }
        }
    }

    function pesanError(val) {
        if (val === '' || val === '—') return '';
        var n = parseFloat(val);
        if (isNaN(n))   return 'Bukan angka';
        if (n < 0)      return 'Min 0';
        if (n > 100)    return 'Maks 100';
        return '';
    }

    function setErrorTip(el, pesan) {
        var td = el.parentElement;
        if (!td) return;
        // Hapus tooltip lama
        var tip = td.querySelector('.nilai-error-tip');
        if (pesan) {
            el.classList.add('nilai-error');
            el.classList.remove('nilai-dirty');
            if (!tip) {
                tip = document.createElement('div');
                tip.className = 'nilai-error-tip';
                td.appendChild(tip);
            }
            tip.textContent = pesan;
        } else {
            el.classList.remove('nilai-error');
            if (tip) tip.remove();
        }
    }

    function updateDirtyHint() {
        var ada        = document.querySelector('.input-nilai.nilai-dirty');
        var hint       = document.getElementById('dirtyHint');
        var revert     = document.getElementById('btnRevert');
        var errorHint  = document.getElementById('errorHint');
        var errorCount = document.getElementById('errorHintCount');
        var errors     = document.querySelectorAll('.input-nilai.nilai-error').length;

        if (hint)   hint.classList.toggle('show', !!ada);
        if (revert) revert.classList.toggle('show', !!ada);
        if (errorHint) errorHint.classList.toggle('show', errors > 0);
        if (errorCount) errorCount.textContent = errors;
    }

    // Tombol batalkan perubahan — kembalikan semua ke nilai asal
    var btnRevert = document.getElementById('btnRevert');
    if (btnRevert) {
        btnRevert.addEventListener('click', function () {
            if (!confirm('Batalkan semua perubahan dan kembalikan ke nilai terakhir yang tersimpan?')) return;
            document.querySelectorAll('.input-nilai').forEach(function (el) {
                var asal = el.dataset.asalNilai || '';
                el.classList.remove('nilai-dirty');
                if (asal === '') {
                    el.value = '—';
                    el.classList.add('nilai-kosong');
                } else {
                    el.value = asal;
                    el.classList.remove('nilai-kosong');
                }
            });
            updateDirtyHint();
        });
    }

    function initNilaiDisplay() {
        document.querySelectorAll('.input-nilai').forEach(function (el) {
            // Simpan nilai asal dari server ke dataset
            var asalServer = el.value.trim();
            el.dataset.asalNilai = asalServer;

            // Tampilkan "—" jika kosong
            if (asalServer === '') {
                el.value = '—';
                el.classList.add('nilai-kosong');
            }

            // Saat focus: hapus "—" supaya bisa langsung ketik
            el.addEventListener('focus', function () {
                if (this.value === '—') {
                    this.value = '';
                    this.classList.remove('nilai-kosong');
                }
            });

            // Saat input: cek dirty + error langsung
            el.addEventListener('input', function () {
                var sekarang = this.value === '—' ? '' : this.value;
                var asal     = this.dataset.asalNilai || '';
                var err      = pesanError(this.value);

                setErrorTip(this, err);

                if (!err) {
                    if (sekarang !== asal) {
                        this.classList.add('nilai-dirty');
                        this.classList.remove('nilai-kosong');
                    } else {
                        this.classList.remove('nilai-dirty');
                    }
                }
                updateDirtyHint();
            });

            // Saat blur: validasi + tampilkan "—" jika kosong
            el.addEventListener('blur', function () {
                var sekarang = this.value.trim();
                var asal     = this.dataset.asalNilai || '';
                var err      = pesanError(sekarang);

                setErrorTip(this, err);

                if (!err) {
                    if (sekarang === '') {
                        this.value = '—';
                        if (asal === '') {
                            this.classList.remove('nilai-dirty');
                            this.classList.add('nilai-kosong');
                        } else {
                            this.classList.add('nilai-dirty');
                            this.classList.remove('nilai-kosong');
                        }
                    }
                }
                updateDirtyHint();
            });
        });

        // Sebelum submit: blokir jika ada error, konversi "—" → ""
        var formNilai = document.querySelector('form[action*="simpan-semua"]');
        if (formNilai) {
            formNilai.addEventListener('submit', function (e) {
                var errors = document.querySelectorAll('.input-nilai.nilai-error');
                if (errors.length > 0) {
                    e.preventDefault();
                    // Scroll ke error pertama
                    errors[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    errors[0].focus();
                    return;
                }
                document.querySelectorAll('.input-nilai').forEach(function (el) {
                    if (el.value === '—') el.value = '';
                });
            });
        }
    }
    initNilaiDisplay();
    updateDirtyHint();
// ── LIVE TOTAL BOBOT ─────────────────────────────────────────────
    document.querySelectorAll('[id^="formBobot"]').forEach(function (form) {
        var kelasId    = form.id.replace('formBobot','');
        var totalEl    = document.getElementById('totalBobot' + kelasId);
        var btnSimpan  = document.getElementById('btnSimpanBobot' + kelasId);
        var inputs     = form.querySelectorAll('.bobot-input-' + kelasId);

        function hitungTotal() {
            var total = 0;
            inputs.forEach(function (el) {
                total += parseFloat(el.value) || 0;
            });
            total = Math.round(total * 100) / 100;

            if (totalEl) {
                totalEl.textContent = total + '%';
                var ok = Math.abs(total - 100) < 0.01;
                totalEl.style.color      = ok ? '#22C55E' : '#EF4444';
                totalEl.parentElement.style.borderColor = ok ? '#86EFAC' : '#FECACA';
                totalEl.parentElement.style.background  = ok ? '#F0FDF4' : '#FEF2F2';
                if (btnSimpan) btnSimpan.disabled = !ok;
            }
        }

        inputs.forEach(function (el) {
            el.addEventListener('input', hitungTotal);
        });
        hitungTotal();
    });

    // ── INPUT BOBOT — hanya angka dan satu titik desimal ──────────────
    document.querySelectorAll('.input-bobot').forEach(function (el) {
        el.addEventListener('keydown', function (e) {
            const ctrl     = e.ctrlKey || e.metaKey;
            const navigasi = ['Backspace','Delete','Tab','Enter','ArrowLeft','ArrowRight','Home','End'];
            if (navigasi.includes(e.key) || ctrl) return;
            if (/^\d$/.test(e.key)) return;
            if (e.key === '.' && !this.value.includes('.')) return;
            e.preventDefault();
        });

        el.addEventListener('paste', function (e) {
            e.preventDefault();
            const raw    = (e.clipboardData || window.clipboardData).getData('text');
            const bersih = raw.replace(/[^\d.]/g, '').replace(/^(\d*\.?\d*).*$/, '$1');
            const before = this.value.slice(0, this.selectionStart);
            const after  = this.value.slice(this.selectionEnd);
            const hasil  = (before + bersih + after).replace(/^(\d*\.?\d*).*$/, '$1');
            this.value   = hasil;
            this.dispatchEvent(new Event('input'));
        });

        el.addEventListener('input', function () {
            const bersih = this.value.replace(/[^\d.]/g, '').replace(/^(\d*\.?\d*).*$/, '$1');
            if (this.value !== bersih) this.value = bersih;
        });
    });

    // ── ENTER = PINDAH KE MAHASISWA BERIKUTNYA (kolom sama) ──────────
    document.querySelectorAll('.input-nilai').forEach(function (el) {
        el.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter') return;
            e.preventDefault(); // cegah form submit

            const td = this.closest('td');
            const tr = this.closest('tr');
            if (!td || !tr) return;

            const colIdx = Array.from(tr.parentElement.rows).indexOf(tr) >= 0
                ? Array.from(tr.cells).indexOf(td)
                : -1;
            if (colIdx < 0) return;

            // Cari baris berikutnya yang punya input nilai di kolom yang sama
            let nextTr = tr.nextElementSibling;
            while (nextTr) {
                const nextTd    = nextTr.cells[colIdx];
                const nextInput = nextTd?.querySelector('.input-nilai');
                if (nextInput) {
                    nextInput.focus();
                    // Kalau sedang menampilkan "—", hapus supaya langsung siap diketik
                    if (nextInput.value === '—') nextInput.value = '';
                    nextInput.select();
                    return;
                }
                nextTr = nextTr.nextElementSibling;
            }

            // Sudah di baris terakhir — balik ke baris pertama kolom yang sama
            const firstTr = tr.parentElement.querySelector('tr');
            if (firstTr) {
                const firstInput = firstTr.cells[colIdx]?.querySelector('.input-nilai');
                if (firstInput) {
                    firstInput.focus();
                    if (firstInput.value === '—') firstInput.value = '';
                    firstInput.select();
                }
            }
        });
    });
    // ═══════════════════════════════════════════════════════════════════
    // DRAFT PERSISTENCE
    // ═══════════════════════════════════════════════════════════════════
    (function () {

        const currentPath = window.location.pathname;

        // Konfigurasi field per tipe modal (tambah & edit)
        const PAGE_CONFIGS = {
            'laboran/dosen': {
                tambah : { modalId: 'modalTambah', fields: ['nama_dosen','nidn','username'] },
                edit   : { prefix: 'modalEditDosen', fields: ['nama_dosen','nidn'] },
            },
            'laboran/asisten': {
                tambah : { modalId: 'modalTambah', fields: ['nama_asisten','nim','username'] },
                edit   : { prefix: 'modalEditAsisten', fields: ['nama_asisten','nim'] },
            },
            'laboran/mahasiswa': {
                tambah : { modalId: 'modalTambah', fields: ['nim_mahasiswa','nama_mahasiswa'] },
            },
            'laboran/mata-kuliah': {
                tambah : { modalId: 'modalTambah', fields: ['kode_mk','nama_mk'] },
                edit   : { prefix: 'modalEditMK', fields: ['kode_mk','nama_mk'] },
            },
            'laboran/ruangan': {
                tambah : { modalId: 'modalTambah', fields: ['nama_ruangan'] },
                edit   : { prefix: 'modalEditRuangan', fields: ['nama_ruangan'] },
            },
            'laboran/kelas': {
                tambah : {
                    modalId : 'modalTambah',
                    fields  : ['nama_kelas'],
                    combos  : [
                        { name: 'mata_kuliah_id', visId: 'cariTMK'          },
                        { name: 'hari',           visId: 'cariTHari'         },
                        { name: 'jam_mulai',      visId: 'cariTJamMulai'    },
                        { name: 'jam_selesai',    visId: 'cariTJamSelesai'  },
                        { name: 'ruangan_id',     visId: 'cariTRuangan'     },
                        { name: 'dosen_id',       visId: 'cariTDosen'       },
                        { name: 'asisten_id',     visId: 'cariTA1'          },
                        { name: 'asisten2_id',    visId: 'cariTA2'          },
                    ],
                },
            },
        };

        // Cari config yang cocok dengan URL saat ini
        const pageKey = Object.keys(PAGE_CONFIGS).find(k => currentPath.includes(k));
        if (!pageKey) return;
        const pageCfg = PAGE_CONFIGS[pageKey];

        const adaSuccess = !!document.querySelector('.alert-success, [class*="alert"][class*="success"]');

        // ── Fungsi inti per modal ─────────────────────────────────
        function initModal(modalEl, storageKey, fields, combos) {
            if (!modalEl) return;

            function getInput(name) {
                return modalEl.querySelector('[name="' + name + '"]');
            }

            function saveDraft() {
                var draft = {};
                fields.forEach(function (name) {
                    var el = getInput(name);
                    if (el) draft[name] = el.value;
                });
                if (combos) {
                    combos.forEach(function (c) {
                        var el = getInput(c.name);
                        if (el) draft[c.name] = el.value;
                        var vis = document.getElementById(c.visId);
                        if (vis) draft['_vis_' + c.name] = vis.value;
                    });
                }
                var hasData = Object.values(draft).some(function (v) { return v !== ''; });
                if (hasData) {
                    localStorage.setItem(storageKey, JSON.stringify(draft));
                } else {
                    localStorage.removeItem(storageKey);
                }
            }

            function restoreDraft() {
                var raw = localStorage.getItem(storageKey);
                if (!raw) return false;
                var draft;
                try { draft = JSON.parse(raw); } catch (e) { return false; }
                var hasData = Object.values(draft).some(function (v) { return v !== ''; });
                if (!hasData) return false;
                fields.forEach(function (name) {
                    var el = getInput(name);
                    if (el && draft[name] !== undefined) el.value = draft[name];
                });
                if (combos) {
                    combos.forEach(function (c) {
                        var el = getInput(c.name);
                        if (el && draft[c.name] !== undefined) el.value = draft[c.name];
                        var vis = document.getElementById(c.visId);
                        if (vis && draft['_vis_' + c.name] !== undefined) vis.value = draft['_vis_' + c.name];
                    });
                }
                return true;
            }

            function clearDraft() {
                localStorage.removeItem(storageKey);
            }

            function resetFields() {
                fields.forEach(function (name) {
                    var el = getInput(name);
                    if (el) el.value = '';
                });
                if (combos) {
                    combos.forEach(function (c) {
                        var el = getInput(c.name);
                        if (el) el.value = '';
                        var vis = document.getElementById(c.visId);
                        if (vis) vis.value = '';
                    });
                }
            }

            // Hapus draft jika ada flash success
            if (adaSuccess) { clearDraft(); return; }

            // Auto-save saat user mengetik
            fields.forEach(function (name) {
                var el = getInput(name);
                if (el) {
                    el.addEventListener('input',  saveDraft);
                    el.addEventListener('change', saveDraft);
                }
            });
            if (combos) {
                combos.forEach(function (c) {
                    var el = getInput(c.name);
                    if (el) {
                        el.addEventListener('input',  saveDraft);
                        el.addEventListener('change', saveDraft);
                    }
                    var vis = document.getElementById(c.visId);
                    if (vis) {
                        vis.addEventListener('input',  saveDraft);
                        vis.addEventListener('change', saveDraft);
                    }
                });
            }

            // Hapus draft saat form submit
            var form = modalEl.querySelector('form');
            if (form) form.addEventListener('submit', clearDraft);

            // Restore saat halaman dimuat jika ada draft
            var raw = localStorage.getItem(storageKey);
            if (raw) {
                var draft;
                try { draft = JSON.parse(raw); } catch (e) { draft = {}; }
                var hasData = Object.values(draft).some(function (v) { return v !== ''; });
                if (hasData) {
                    setTimeout(function () {
                        restoreDraft();
                        modalEl.classList.add('open');
                        document.body.style.overflow = 'hidden';
                    }, 100);
                }
            }

            // Hapus draft & reset field saat modal ditutup
            var wasOpen = modalEl.classList.contains('open');
            new MutationObserver(function () {
                var isOpen = modalEl.classList.contains('open');
                if (wasOpen && !isOpen) {
                    clearDraft();
                    resetFields();
                }
                wasOpen = isOpen;
            }).observe(modalEl, { attributes: true, attributeFilter: ['class'] });
        }

        // ── Inisialisasi modal TAMBAH ─────────────────────────────
        if (pageCfg.tambah) {
            var t = pageCfg.tambah;
            initModal(
                document.getElementById(t.modalId),
                'draft_tambah_' + pageKey.split('/').pop(),
                t.fields,
                t.combos || null
            );
        }

        // ── Inisialisasi semua modal EDIT ─────────────────────────
        if (pageCfg.edit) {
            var e = pageCfg.edit;
            document.querySelectorAll('[id^="' + e.prefix + '"]').forEach(function (modalEl) {
                var recordId = modalEl.id.replace(e.prefix, '');
                if (!recordId) return;
                var storageKey = 'draft_edit_' + pageKey.split('/').pop() + '_' + recordId;

                // Simpan nilai asli dari server sebelum apapun terjadi
                var nilaiAsli = {};
                e.fields.forEach(function (name) {
                    var el = modalEl.querySelector('[name="' + name + '"]');
                    if (el) nilaiAsli[name] = el.value;
                });

                initModal(modalEl, storageKey, e.fields, null);

                // Override: saat modal edit ditutup, kembalikan ke nilai asli
                // (bukan kosongkan seperti modal tambah)
                var wasOpenEdit = modalEl.classList.contains('open');
                new MutationObserver(function () {
                    var isOpen = modalEl.classList.contains('open');
                    if (wasOpenEdit && !isOpen) {
                        // Kembalikan ke nilai asli server, bukan kosong
                        e.fields.forEach(function (name) {
                            var el = modalEl.querySelector('[name="' + name + '"]');
                            if (el && nilaiAsli[name] !== undefined) el.value = nilaiAsli[name];
                        });
                    }
                    wasOpenEdit = isOpen;
                }).observe(modalEl, { attributes: true, attributeFilter: ['class'] });
            });
        }

    })();

    // ═══════════════════════════════════════════════════════════════════
    // DRAFT PERSISTENCE — halaman Edit Kelas (show.blade.php)
    // Berbeda dari modal: ini halaman penuh, bukan modal
    // ═══════════════════════════════════════════════════════════════════
    (function () {
        var currentPath = window.location.pathname;
        if (!currentPath.includes('laboran/kelas/')) return;
        // Pastikan ini halaman detail kelas, bukan index
        if (currentPath === '/laboran/kelas' || currentPath === '/laboran/kelas/') return;

        // Key unik per kelas berdasarkan URL (misal: draft_kelas_show_5)
        var storageKey = 'draft_kelas_show_' + currentPath.replace(/.*\/kelas\//, '').replace(/\/$/, '');

        // Semua field combobox di form edit kelas
        var COMBOS = [
            { name: 'hari',        visId: 'cariHari'      },
            { name: 'jam_mulai',   visId: 'cariJamMulai'  },
            { name: 'jam_selesai', visId: 'cariJamSelesai'},
            { name: 'ruangan_id',  visId: 'cariRuangan'   },
            { name: 'dosen_id',    visId: 'cariDosen'     },
            { name: 'asisten_id',  visId: 'cariA1'        },
            { name: 'asisten2_id', visId: 'cariA2'        },
        ];

        // Form edit kelas (PATCH)
        var form = document.querySelector('form[action*="/kelas/"]');
        if (!form) return;

        function getInput(name) {
            return form.querySelector('[name="' + name + '"]');
        }

        // Simpan nilai asli dari server sebelum apapun
        var nilaiAsli = {};
        COMBOS.forEach(function (c) {
            var el  = getInput(c.name);
            var vis = document.getElementById(c.visId);
            if (el)  nilaiAsli[c.name]           = el.value;
            if (vis) nilaiAsli['_vis_' + c.name] = vis.value;
        });

        function saveDraft() {
            var draft = {};
            COMBOS.forEach(function (c) {
                var el  = getInput(c.name);
                var vis = document.getElementById(c.visId);
                if (el)  draft[c.name]           = el.value;
                if (vis) draft['_vis_' + c.name] = vis.value;
            });
            // Simpan selalu — hapus hanya jika semua sama dengan nilai asli
            var adaPerubahan = COMBOS.some(function (c) {
                return draft[c.name] !== nilaiAsli[c.name];
            });
            if (adaPerubahan) {
                localStorage.setItem(storageKey, JSON.stringify(draft));
            } else {
                localStorage.removeItem(storageKey);
            }
        }

        // Pantau perubahan hidden input via MutationObserver
        // karena dispatchEvent('change') kadang tidak cukup untuk semua browser
        COMBOS.forEach(function (c) {
            var el = getInput(c.name);
            if (el) {
                new MutationObserver(saveDraft).observe(el, { attributes: true, attributeFilter: ['value'] });
            }
        });

        function restoreDraft() {
            var raw = localStorage.getItem(storageKey);
            if (!raw) return;
            var draft;
            try { draft = JSON.parse(raw); } catch (e) { return; }
            COMBOS.forEach(function (c) {
                var el  = getInput(c.name);
                var vis = document.getElementById(c.visId);
                if (el  && draft[c.name]           !== undefined) el.value  = draft[c.name];
                if (vis && draft['_vis_' + c.name] !== undefined) vis.value = draft['_vis_' + c.name];
            });
        }

        function clearDraft() {
            localStorage.removeItem(storageKey);
        }

        // Hapus draft jika ada flash success
        if (document.querySelector('.alert-success, [class*="alert"][class*="success"]')) {
            clearDraft();
            return;
        }

        // Auto-save saat user mengubah combobox
        COMBOS.forEach(function (c) {
            var el  = getInput(c.name);
            var vis = document.getElementById(c.visId);
            if (el) {
                el.addEventListener('input',  saveDraft);
                el.addEventListener('change', saveDraft);
            }
            if (vis) {
                vis.addEventListener('input',  saveDraft);
                vis.addEventListener('change', saveDraft);
            }
        });

        // Hapus draft saat form submit
        form.addEventListener('submit', clearDraft);

        // Restore draft saat halaman dimuat — tunda agar combobox selesai init
        setTimeout(function () {
            restoreDraft();
            // Setelah restore, update nilaiAsli agar tidak dianggap "tidak ada perubahan"
            // padahal draft sudah berbeda dari server
            // Jangan update nilaiAsli di sini — biarkan tetap nilai server
            // supaya perbandingan tetap akurat
        }, 200);

    });

    // ═══════════════════════════════════════════════════════════════════
    // DRAFT PERSISTENCE — modal Tambah & Edit (laboran)
    // ═══════════════════════════════════════════════════════════════════
    (function () {

        var currentPath = window.location.pathname;

        var PAGE_CONFIGS = {
            'laboran/dosen': {
                tambah : { modalId: 'modalTambah', fields: ['nama_dosen','nidn','username'] },
                edit   : { prefix: 'modalEditDosen', fields: ['nama_dosen','nidn'] },
            },
            'laboran/asisten': {
                tambah : { modalId: 'modalTambah', fields: ['nama_asisten','nim','username'] },
                edit   : { prefix: 'modalEditAsisten', fields: ['nama_asisten','nim'] },
            },
            'laboran/mahasiswa': {
                tambah : { modalId: 'modalTambah', fields: ['nim_mahasiswa','nama_mahasiswa'] },
            },
            'laboran/mata-kuliah': {
                tambah : { modalId: 'modalTambah', fields: ['kode_mk','nama_mk'] },
                edit   : { prefix: 'modalEditMK', fields: ['kode_mk','nama_mk'] },
            },
            'laboran/ruangan': {
                tambah : { modalId: 'modalTambah', fields: ['nama_ruangan'] },
                edit   : { prefix: 'modalEditRuangan', fields: ['nama_ruangan'] },
            },
            'laboran/kelas': {
                tambah : {
                    modalId : 'modalTambah',
                    fields  : ['nama_kelas'],
                    combos  : [
                        { name: 'mata_kuliah_id', visId: 'cariTMK'         },
                        { name: 'hari',           visId: 'cariTHari'       },
                        { name: 'jam_mulai',      visId: 'cariTJamMulai'   },
                        { name: 'jam_selesai',    visId: 'cariTJamSelesai' },
                        { name: 'ruangan_id',     visId: 'cariTRuangan'    },
                        { name: 'dosen_id',       visId: 'cariTDosen'      },
                        { name: 'asisten_id',     visId: 'cariTA1'         },
                        { name: 'asisten2_id',    visId: 'cariTA2'         },
                    ],
                },
            },
        };

        var pageKey = Object.keys(PAGE_CONFIGS).find(function (k) { return currentPath.includes(k); });
        if (!pageKey) return;
        var pageCfg = PAGE_CONFIGS[pageKey];

        var adaSuccess = !!document.querySelector('.alert-success, [class*="alert"][class*="success"]');

        function initModal(modalEl, storageKey, fields, combos) {
            if (!modalEl) return;

            function getInput(name) {
                return modalEl.querySelector('[name="' + name + '"]');
            }

            function saveDraft() {
                var draft = {};
                fields.forEach(function (name) {
                    var el = getInput(name);
                    if (el) draft[name] = el.value;
                });
                if (combos) {
                    combos.forEach(function (c) {
                        var el = getInput(c.name);
                        if (el) draft[c.name] = el.value;
                        var vis = document.getElementById(c.visId);
                        if (vis) draft['_vis_' + c.name] = vis.value;
                    });
                }
                var hasData = Object.values(draft).some(function (v) { return v !== ''; });
                if (hasData) {
                    localStorage.setItem(storageKey, JSON.stringify(draft));
                } else {
                    localStorage.removeItem(storageKey);
                }
            }

            function restoreDraft() {
                var raw = localStorage.getItem(storageKey);
                if (!raw) return false;
                var draft;
                try { draft = JSON.parse(raw); } catch (e) { return false; }
                var hasData = Object.values(draft).some(function (v) { return v !== ''; });
                if (!hasData) return false;
                fields.forEach(function (name) {
                    var el = getInput(name);
                    if (el && draft[name] !== undefined) el.value = draft[name];
                });
                if (combos) {
                    combos.forEach(function (c) {
                        var el = getInput(c.name);
                        if (el && draft[c.name] !== undefined) el.value = draft[c.name];
                        var vis = document.getElementById(c.visId);
                        if (vis && draft['_vis_' + c.name] !== undefined) vis.value = draft['_vis_' + c.name];
                    });
                }
                return true;
            }

            function clearDraft() {
                localStorage.removeItem(storageKey);
            }

            function resetFields() {
                fields.forEach(function (name) {
                    var el = getInput(name);
                    if (el) el.value = '';
                });
                if (combos) {
                    combos.forEach(function (c) {
                        var el = getInput(c.name);
                        if (el) el.value = '';
                        var vis = document.getElementById(c.visId);
                        if (vis) vis.value = '';
                    });
                }
            }

            if (adaSuccess) { clearDraft(); return; }

            fields.forEach(function (name) {
                var el = getInput(name);
                if (el) {
                    el.addEventListener('input',  saveDraft);
                    el.addEventListener('change', saveDraft);
                }
            });
            if (combos) {
                combos.forEach(function (c) {
                    var el = getInput(c.name);
                    if (el) {
                        el.addEventListener('input',  saveDraft);
                        el.addEventListener('change', saveDraft);
                    }
                    var vis = document.getElementById(c.visId);
                    if (vis) {
                        vis.addEventListener('input',  saveDraft);
                        vis.addEventListener('change', saveDraft);
                    }
                });
            }

            var form = modalEl.querySelector('form');
            if (form) form.addEventListener('submit', clearDraft);

            var raw = localStorage.getItem(storageKey);
            if (raw) {
                var draft;
                try { draft = JSON.parse(raw); } catch (e) { draft = {}; }
                var hasData = Object.values(draft).some(function (v) { return v !== ''; });
                if (hasData) {
                    setTimeout(function () {
                        restoreDraft();
                        modalEl.classList.add('open');
                        document.body.style.overflow = 'hidden';
                    }, 100);
                }
            }

            var wasOpen = modalEl.classList.contains('open');
            new MutationObserver(function () {
                var isOpen = modalEl.classList.contains('open');
                if (wasOpen && !isOpen) {
                    clearDraft();
                    resetFields();
                }
                wasOpen = isOpen;
            }).observe(modalEl, { attributes: true, attributeFilter: ['class'] });
        }

        if (pageCfg.tambah) {
            var t = pageCfg.tambah;
            initModal(
                document.getElementById(t.modalId),
                'draft_tambah_' + pageKey.split('/').pop(),
                t.fields,
                t.combos || null
            );
        }

        if (pageCfg.edit) {
            var cfg = pageCfg.edit;
            document.querySelectorAll('[id^="' + cfg.prefix + '"]').forEach(function (modalEl) {
                var recordId = modalEl.id.replace(cfg.prefix, '');
                if (!recordId) return;
                var storageKey = 'draft_edit_' + pageKey.split('/').pop() + '_' + recordId;

                var nilaiAsli = {};
                cfg.fields.forEach(function (name) {
                    var el = modalEl.querySelector('[name="' + name + '"]');
                    if (el) nilaiAsli[name] = el.value;
                });

                initModal(modalEl, storageKey, cfg.fields, null);

                var wasOpenEdit = modalEl.classList.contains('open');
                new MutationObserver(function () {
                    var isOpen = modalEl.classList.contains('open');
                    if (wasOpenEdit && !isOpen) {
                        cfg.fields.forEach(function (name) {
                            var el = modalEl.querySelector('[name="' + name + '"]');
                            if (el && nilaiAsli[name] !== undefined) el.value = nilaiAsli[name];
                        });
                    }
                    wasOpenEdit = isOpen;
                }).observe(modalEl, { attributes: true, attributeFilter: ['class'] });
            });
        }

    })();

    // ═══════════════════════════════════════════════════════════════════
    // DRAFT PERSISTENCE — halaman Edit Kelas (show.blade.php)
    // Menyimpan: combobox jadwal/dosen/asisten + teks pencarian
    // mahasiswa + daftar checkbox mahasiswa yang sudah dicentang
    // ═══════════════════════════════════════════════════════════════════
    (function () {
        var currentPath = window.location.pathname;
        if (!currentPath.includes('laboran/kelas/')) return;
        if (currentPath === '/laboran/kelas' || currentPath === '/laboran/kelas/') return;

        var kelasSlug  = currentPath.replace(/.*\/kelas\//, '').replace(/\/$/, '');
        var keyJadwal  = 'draft_kelas_jadwal_' + kelasSlug;
        var keyMhs     = 'draft_kelas_mhs_'    + kelasSlug;

        var adaSuccess = !!document.querySelector('.alert-success, [class*="alert"][class*="success"]');

        // ── Bagian 1: Combobox Jadwal / Dosen / Asisten ───────────────────
        var COMBOS = [
            { name: 'hari',        visId: 'cariHari'       },
            { name: 'jam_mulai',   visId: 'cariJamMulai'   },
            { name: 'jam_selesai', visId: 'cariJamSelesai' },
            { name: 'ruangan_id',  visId: 'cariRuangan'    },
            { name: 'dosen_id',    visId: 'cariDosen'      },
            { name: 'asisten_id',  visId: 'cariA1'         },
            { name: 'asisten2_id', visId: 'cariA2'         },
        ];

        var formJadwal = document.querySelector('form[action*="/kelas/"]');

        if (formJadwal) {
            function getInput(name) { return formJadwal.querySelector('[name="' + name + '"]'); }

            var nilaiAsli = {};
            COMBOS.forEach(function (c) {
                var el  = getInput(c.name);
                var vis = document.getElementById(c.visId);
                if (el)  nilaiAsli[c.name]           = el.value;
                if (vis) nilaiAsli['_vis_' + c.name] = vis.value;
            });

            function saveJadwal() {
                var draft = {};
                COMBOS.forEach(function (c) {
                    var el  = getInput(c.name);
                    var vis = document.getElementById(c.visId);
                    if (el)  draft[c.name]           = el.value;
                    if (vis) draft['_vis_' + c.name] = vis.value;
                });
                var adaPerubahan = COMBOS.some(function (c) { return draft[c.name] !== nilaiAsli[c.name]; });
                if (adaPerubahan) {
                    localStorage.setItem(keyJadwal, JSON.stringify(draft));
                } else {
                    localStorage.removeItem(keyJadwal);
                }
            }

            function restoreJadwal() {
                var raw = localStorage.getItem(keyJadwal);
                if (!raw) return;
                var draft;
                try { draft = JSON.parse(raw); } catch (e) { return; }
                COMBOS.forEach(function (c) {
                    var el  = getInput(c.name);
                    var vis = document.getElementById(c.visId);
                    if (el  && draft[c.name]           !== undefined) el.value  = draft[c.name];
                    if (vis && draft['_vis_' + c.name] !== undefined) vis.value = draft['_vis_' + c.name];
                });
            }

            if (adaSuccess) {
                localStorage.removeItem(keyJadwal);
            } else {
                COMBOS.forEach(function (c) {
                    var el  = getInput(c.name);
                    var vis = document.getElementById(c.visId);
                    if (el)  { el.addEventListener('input', saveJadwal);  el.addEventListener('change', saveJadwal); }
                    if (vis) { vis.addEventListener('input', saveJadwal); vis.addEventListener('change', saveJadwal); }
                });
                COMBOS.forEach(function (c) {
                    var el = getInput(c.name);
                    if (el) new MutationObserver(saveJadwal).observe(el, { attributes: true, attributeFilter: ['value'] });
                });
                formJadwal.addEventListener('submit', function () { localStorage.removeItem(keyJadwal); });
                setTimeout(restoreJadwal, 200);
            }
        }

        // ── Bagian 2: Filter Pencarian Mahasiswa + Checkbox ───────────────
        var cariMhsBanyak = document.getElementById('cariMhsBanyak');
        var formEnroll    = document.getElementById('formEnrollBanyak');

        if (!cariMhsBanyak || !formEnroll) return;

        function getAllRows()  { return document.querySelectorAll('.mhs-checkbox-row'); }
        function getVisible() { return [...getAllRows()].filter(function (r) { return r.style.display !== 'none'; }); }

        function triggerUpdateUI() {
            var dipilih     = document.querySelectorAll('.mhs-cb:checked').length;
            var btnEnroll   = document.getElementById('btnEnroll');
            var labelJumlah = document.getElementById('jumlahDipilih');
            var checkSemua  = document.getElementById('checkSemuaMhs');
            if (btnEnroll) {
                btnEnroll.disabled    = dipilih === 0;
                btnEnroll.textContent = dipilih > 0
                    ? '+ Tambahkan ' + dipilih + ' Mahasiswa ke Kelas'
                    : '+ Tambahkan ke Kelas';
            }
            if (labelJumlah) {
                labelJumlah.textContent = dipilih > 0 ? dipilih + ' dipilih' : '';
            }
            if (checkSemua) {
                var cbVisible   = getVisible().map(function (r) { return r.querySelector('.mhs-cb'); });
                var semuaCeklis = cbVisible.length > 0 && cbVisible.every(function (cb) { return cb.checked; });
                checkSemua.indeterminate = !semuaCeklis && cbVisible.some(function (cb) { return cb.checked; });
                checkSemua.checked       = semuaCeklis && cbVisible.length > 0;
            }
        }

        function applyFilter(q) {
            getAllRows().forEach(function (row) {
                row.style.display = (!q || row.dataset.cari.includes(q.toLowerCase().trim()))
                    ? 'flex' : 'none';
            });
        }

        function saveMhs() {
            var ids = [];
            document.querySelectorAll('.mhs-cb:checked').forEach(function (cb) { ids.push(cb.value); });
            var q   = cariMhsBanyak.value;
            if (q !== '' || ids.length > 0) {
                localStorage.setItem(keyMhs, JSON.stringify({ q: q, ids: ids }));
            } else {
                localStorage.removeItem(keyMhs);
            }
        }

        function restoreMhs() {
            var raw = localStorage.getItem(keyMhs);
            if (!raw) return;
            var draft;
            try { draft = JSON.parse(raw); } catch (e) { return; }
            if (draft.q) {
                cariMhsBanyak.value = draft.q;
                applyFilter(draft.q);
            }
            if (Array.isArray(draft.ids) && draft.ids.length > 0) {
                var idSet = new Set(draft.ids);
                document.querySelectorAll('.mhs-cb').forEach(function (cb) {
                    if (idSet.has(cb.value)) cb.checked = true;
                });
            }
            triggerUpdateUI();
        }

        if (adaSuccess) {
            localStorage.removeItem(keyMhs);
        } else {
            cariMhsBanyak.addEventListener('input', saveMhs);

            document.getElementById('daftarMhsCheckbox')?.addEventListener('change', function (e) {
                if (e.target.classList.contains('mhs-cb')) saveMhs();
            });

            var checkSemua = document.getElementById('checkSemuaMhs');
            if (checkSemua) checkSemua.addEventListener('change', saveMhs);

            formEnroll.addEventListener('submit', function () { localStorage.removeItem(keyMhs); });

            // Saat navigasi keluar: hapus draft pencarian mahasiswa (keyMhs saja,
            // keyJadwal dibiarkan agar perubahan dosen/asisten yang belum disimpan tetap ada)
            window.addEventListener('pagehide', function () {
                localStorage.removeItem(keyMhs);
            });

            // Bedakan refresh vs navigasi masuk:
            // - refresh: PerformanceNavigationTiming.type === 'reload'
            // - navigasi masuk dari halaman lain: type === 'navigate'
            // Hanya restore draft pencarian jika ini adalah refresh
            var navType = (performance.getEntriesByType('navigation')[0] || {}).type;
            if (navType === 'reload') {
                setTimeout(restoreMhs, 150);
            }
            // Jika 'navigate' (masuk dari halaman lain): tidak restore, tidak perlu hapus
            // karena pagehide di kunjungan sebelumnya sudah menghapusnya
        }

    })();

});
