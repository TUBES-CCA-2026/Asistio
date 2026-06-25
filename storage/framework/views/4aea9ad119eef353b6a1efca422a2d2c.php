<header class="topbar">
    <div class="topbar-left">
        <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
        <div class="topbar-title">
            <h1><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h1>
            <?php if (! empty(trim($__env->yieldContent('page-subtitle')))): ?>
            <p class="topbar-subtitle"><?php echo $__env->yieldContent('page-subtitle'); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="topbar-right">
        <div class="topbar-user">
            <div class="topbar-user-info">
                <span class="topbar-user-name"><?php echo e(auth()->user()->nama); ?></span>
                <span class="topbar-user-role"><?php echo e(ucfirst(auth()->user()->role_name)); ?></span>
            </div>
            <div class="topbar-avatar"><?php echo e(auth()->user()->initials); ?></div>
        </div>
    </div>
</header>
<?php /**PATH C:\xampp\htdocs\asistio\resources\views/layouts/partials/header.blade.php ENDPATH**/ ?>