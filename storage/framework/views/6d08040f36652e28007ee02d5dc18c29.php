<?php $__env->startSection('title','Dashboard Asisten'); ?>
<?php $__env->startSection('page-title','Dashboard Asisten'); ?>
<?php $__env->startSection('content'); ?>
<div class="hero-banner">
    <h1 class="hero-title">Halo, <?php echo e(auth()->user()->nama); ?>! 👋</h1>
    <p class="hero-subtitle">Pilih kelas yang Anda ampu untuk memulai presensi atau pengisian nilai.</p>
</div>
<?php if($kelasList->isEmpty()): ?>
<div class="card"><div class="empty-state"><p>Anda belum ditugaskan ke kelas manapun. Hubungi laboran.</p></div></div>
<?php else: ?>
<div class="grid grid-2">
<?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="course-card">
    <div class="course-card-header">
        <div class="course-card-code"><?php echo e($kelas->mataKuliah?->kode_mk); ?></div>
        <div class="course-card-name"><?php echo e($kelas->mataKuliah?->nama_mk); ?></div>
        <div class="course-card-meta">
            <span class="fw-600"><?php echo e($kelas->nama_kelas); ?></span>
            <?php if($kelas->jadwal): ?><span><?php echo e($kelas->jadwal); ?></span><?php endif; ?>
            <?php if($kelas->ruangan): ?><span><?php echo e($kelas->ruangan->nama_ruangan); ?></span><?php endif; ?>
            <span><?php echo e($kelas->mahasiswa_count); ?> mahasiswa</span>
        </div>
    </div>
    <div class="course-card-footer">
        <a href="<?php echo e(route('asisten.presensi', $kelas)); ?>" class="btn btn-outline btn-sm">Presensi</a>
        <a href="<?php echo e(route('asisten.nilai', $kelas)); ?>" class="btn btn-outline btn-sm">Nilai</a>
        <a href="<?php echo e(route('asisten.rekap', $kelas)); ?>" class="btn btn-primary btn-sm">Rekap →</a>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\asistio\resources\views/asisten/dashboard.blade.php ENDPATH**/ ?>