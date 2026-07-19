@php $role = auth()->user()?->role_name; @endphp
<aside class="sidebar" id="sidebar">
    {{-- Brand dengan logo dari file image (poin #5) --}}
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo_2.png') }}" alt="ICLABS FIKOM UMI Logo" class="sidebar-logo-img">
        <span class="sidebar-brand-name">Asistio</span>
    </div>

    <nav class="sidebar-nav">
        {{-- Laboran --}}
        @if($role === 'laboran')
            <a href="{{ route('laboran.dashboard') }}" class="nav-item {{ request()->routeIs('laboran.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Dashboard</span>
            </a>
            <div class="nav-section-label">Data Master</div>
            <a href="{{ route('laboran.kelas') }}" class="nav-item {{ request()->routeIs('laboran.kelas*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>Kelas Praktikum</span>
            </a>
            <a href="{{ route('laboran.mata-kuliah') }}" class="nav-item {{ request()->routeIs('laboran.mata-kuliah*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>
                <span>Mata Kuliah</span>
            </a>
            <a href="{{ route('laboran.ruangan') }}" class="nav-item {{ request()->routeIs('laboran.ruangan*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>Ruangan</span>
            </a>
            <div class="nav-section-label">Kelola Pengguna</div>
            <a href="{{ route('laboran.dosen') }}" class="nav-item {{ request()->routeIs('laboran.dosen*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
                <span>Dosen</span>
            </a>
            <a href="{{ route('laboran.asisten') }}" class="nav-item {{ request()->routeIs('laboran.asisten*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                <span>Asisten</span>
            </a>
            <a href="{{ route('laboran.mahasiswa') }}" class="nav-item {{ request()->routeIs('laboran.mahasiswa*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <span>Mahasiswa</span>
            </a>
            <a href="{{ route('laboran.backup.index') }}" class="nav-item {{ request()->routeIs('laboran.backup*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                <span>Backup & Pemulihan</span>
            </a>
        @endif

        {{-- Asisten --}}
        @if($role === 'asisten')
            <a href="{{ route('asisten.dashboard') }}" class="nav-item {{ request()->routeIs('asisten.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Dashboard</span>
            </a>

            @if($sidebarKelas->isNotEmpty())
            <div class="nav-section-label">Kelas Saya</div>
            @foreach($sidebarKelas as $kelas)
            @php
                $kelasId       = $kelas->id;
                $isKelasAktif  = request()->route('praktikum')?->id == $kelasId;
                $subRoutes     = ['asisten.presensi','asisten.nilai','asisten.rekap'];
                $isSubAktif    = $isKelasAktif && request()->routeIs(...$subRoutes);
            @endphp
            <div class="nav-kelas-item">
                <button type="button"
                    class="nav-kelas-toggle {{ $isKelasAktif ? 'active-kelas' : '' }} {{ $isSubAktif ? 'open' : '' }}"
                    data-kelas-id="{{ $kelasId }}">
                    <span class="nav-kelas-toggle-left">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>
                            <span class="nav-kelas-nama">{{ $kelas->nama_kelas }}</span>
                            <span class="nav-kelas-mk">{{ $kelas->mataKuliah?->kode_mk }}</span>
                        </span>
                    </span>
                    <svg class="nav-kelas-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </button>

                <div class="nav-kelas-sub {{ $isSubAktif ? 'open' : '' }}" id="sub-{{ $kelasId }}">
                    {{-- Presensi --}}
                    <a href="{{ route('asisten.presensi', $kelas) }}"
                       class="{{ request()->routeIs('asisten.presensi') && $isKelasAktif ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                        Absensi
                    </a>
                    {{-- Nilai --}}
                    <a href="{{ route('asisten.nilai', $kelas) }}"
                       class="{{ request()->routeIs('asisten.nilai') && $isKelasAktif ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        Nilai
                    </a>
                    {{-- Bobot --}}
                    <a href="{{ route('asisten.dashboard') }}#bobot-{{ $kelasId }}"
                       class=""
                       id="sidebar-bobot-{{ $kelasId }}"
                       onclick="
                           var modal = document.getElementById('modalBobot{{ $kelasId }}');
                           if (modal) {
                               event.preventDefault();
                               modal.classList.add('open');
                               document.body.style.overflow = 'hidden';
                           }
                       ">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        Bobot
                    </a>
                    {{-- Rekap --}}
                    <a href="{{ route('asisten.rekap', $kelas) }}"
                       class="{{ request()->routeIs('asisten.rekap') && $isKelasAktif ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                        Rekap
                    </a>
                </div>
            </div>
            @endforeach
            @endif
        @endif

        {{-- Dosen/Pengawas --}}
        @if($role === 'dosen')
            <a href="{{ route('pengawas.dashboard') }}" class="nav-item {{ request()->routeIs('pengawas.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Dashboard</span>
            </a>
        @endif
    </nav>

    <div class="sidebar-footer">

        {{-- Ganti Password --}}
        @if($role === 'asisten')
            <a href="{{ route('asisten.ganti-password') }}" class="nav-item {{ request()->routeIs('asisten.ganti-password*') ? 'active' : '' }}" style="justify-content:center;width:100%;box-sizing:border-box;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                <span>Ganti Password</span>
            </a>
        @elseif($role === 'dosen')
            <a href="{{ route('dosen.ganti-password') }}" class="nav-item {{ request()->routeIs('dosen.ganti-password*') ? 'active' : '' }}" style="justify-content:center;width:100%;box-sizing:border-box;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                <span>Ganti Password</span>
            </a>
        @elseif($role === 'laboran')
            <a href="{{ route('laboran.ganti-password') }}" class="nav-item {{ request()->routeIs('laboran.ganti-password*') ? 'active' : '' }}" style="justify-content:center;width:100%;box-sizing:border-box;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                <span>Ganti Password</span>
            </a>
        @endif

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" style="width:100%;">
            @csrf
            <button type="submit" class="nav-item" style="width:100%;background:none;border:none;cursor:pointer;justify-content:center;color:var(--text-muted);transition:background .15s,color .15s;" onmouseover="this.style.background='rgba(255,255,255,.06)';this.style.color='#E2E8F0';" onmouseout="this.style.background='none';this.style.color='var(--text-muted)';">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span>Keluar</span>
            </button>
        </form>

    </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
