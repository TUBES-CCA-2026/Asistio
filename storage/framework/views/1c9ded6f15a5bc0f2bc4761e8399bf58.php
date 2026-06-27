<?php $role = auth()->user()?->role_name; ?>
<aside class="sidebar" id="sidebar">
    
    <div class="sidebar-brand">
        <img src="<?php echo e(asset('images/logo_2.png')); ?>" alt="ICLABS FIKOM UMI Logo" class="sidebar-logo-img">
        <span class="sidebar-brand-name">Asistio</span>
    </div>

    <nav class="sidebar-nav">
        
        <?php if($role === 'laboran'): ?>
            <a href="<?php echo e(route('laboran.dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('laboran.dashboard') ? 'active' : ''); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Dashboard</span>
            </a>
            <div class="nav-section-label">Data Master</div>
            <a href="<?php echo e(route('laboran.mata-kuliah')); ?>" class="nav-item <?php echo e(request()->routeIs('laboran.mata-kuliah*') ? 'active' : ''); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>
                <span>Mata Kuliah</span>
            </a>
            <a href="<?php echo e(route('laboran.kelas')); ?>" class="nav-item <?php echo e(request()->routeIs('laboran.kelas*') ? 'active' : ''); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>Kelas Praktikum</span>
            </a>
            <a href="<?php echo e(route('laboran.ruangan')); ?>" class="nav-item <?php echo e(request()->routeIs('laboran.ruangan*') ? 'active' : ''); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>Ruangan</span>
            </a>
            <div class="nav-section-label">Kelola Pengguna</div>
            <a href="<?php echo e(route('laboran.asisten')); ?>" class="nav-item <?php echo e(request()->routeIs('laboran.asisten*') ? 'active' : ''); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                <span>Asisten</span>
            </a>
            <a href="<?php echo e(route('laboran.dosen')); ?>" class="nav-item <?php echo e(request()->routeIs('laboran.dosen*') ? 'active' : ''); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
                <span>Dosen</span>
            </a>
            <a href="<?php echo e(route('laboran.mahasiswa')); ?>" class="nav-item <?php echo e(request()->routeIs('laboran.mahasiswa*') ? 'active' : ''); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <span>Mahasiswa</span>
            </a>
        <?php endif; ?>

        
        <?php if($role === 'asisten'): ?>
            <a href="<?php echo e(route('asisten.dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('asisten.dashboard') ? 'active' : ''); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Dashboard</span>
            </a>
        <?php endif; ?>

        
        <?php if($role === 'dosen'): ?>
            <a href="<?php echo e(route('pengawas.dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('pengawas.dashboard') ? 'active' : ''); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Dashboard</span>
            </a>
        <?php endif; ?>
    </nav>

    
        <?php if($role === 'asisten'): ?>
            <a href="<?php echo e(route('asisten.ganti-password')); ?>" class="nav-item sidebar-footer-link <?php echo e(request()->routeIs('asisten.ganti-password*') ? 'active' : ''); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                <span>Ganti Password</span>
            </a>
        <?php elseif($role === 'dosen'): ?>
            <a href="<?php echo e(route('dosen.ganti-password')); ?>" class="nav-item sidebar-footer-link <?php echo e(request()->routeIs('asisten.ganti-password*') ? 'active' : ''); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                <span>Ganti Password</span>
            </a>
        <?php endif; ?>
        
            
    

    
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar"><?php echo e(auth()->user()->initials); ?></div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name"><?php echo e(auth()->user()->nama); ?></div>
                <div class="sidebar-user-role"><?php echo e(ucfirst($role)); ?></div>
            </div>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="sidebar-logout" title="Keluar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<?php /**PATH C:\Users\ACER\Asistio\resources\views/layouts/partials/sidebar.blade.php ENDPATH**/ ?>