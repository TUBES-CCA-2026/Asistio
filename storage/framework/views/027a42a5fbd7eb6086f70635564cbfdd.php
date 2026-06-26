<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> — Asistio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo_2.png')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>


<div class="app-shell">

    
    <?php echo $__env->make('layouts.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="main-wrapper">

        
        <?php echo $__env->make('layouts.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <div class="flash-container">
            <?php if(session('success')): ?>
                <div class="alert alert-success" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <span><?php echo e(session('success')); ?></span>
                    <button class="alert-close" type="button">✕</button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-error" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span><?php echo e(session('error')); ?></span>
                    <button class="alert-close" type="button">✕</button>
                </div>
            <?php endif; ?>
        </div>

        
        <main class="page-content" role="main">
            <?php echo $__env->yieldContent('content'); ?>
        </main>

        
        <?php echo $__env->make('layouts.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    </div>

</div>

<script src="<?php echo e(asset('js/app.js')); ?>" defer></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\tes1\resources\views/layouts/app.blade.php ENDPATH**/ ?>