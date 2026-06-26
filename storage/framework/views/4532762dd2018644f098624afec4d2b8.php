<?php $__env->startSection('title','Dashboard Pengawas'); ?>
<?php $__env->startSection('page-title','Monitoring Praktikum'); ?>
<?php $__env->startSection('content'); ?>
<?php if($errors->any()): ?>
<script>document.addEventListener('DOMContentLoaded',()=>{ const o=document.getElementById('modalGantiPassword'); if(o) o.classList.add('is-open'); });</script>
<?php endif; ?>
<div class="hero-banner">
    <h1 class="hero-title">Selamat datang, <?php echo e($dosen?->nama_dosen ?? auth()->user()->username); ?>!</h1>
    <p class="hero-subtitle">Pantau rekap nilai dan presensi mahasiswa per kelas.</p>
</div>
<?php if($kelasList->isEmpty()): ?>
<div class="card"><div class="empty-state"><p>Belum ada kelas yang ditugaskan. Hubungi laboran.</p></div></div>
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
            <span><?php echo e($kelas->mahasiswa_count); ?> mahasiswa</span>
            <?php if($kelas->asisten): ?><span>Asisten: <?php echo e($kelas->asisten->nama_asisten); ?></span><?php endif; ?>
            <?php if($kelas->asisten): ?><span>Asisten 1: <?php echo e($kelas->asisten->nama_asisten); ?></span><?php endif; ?>
            <?php if($kelas->asisten2): ?><span>Asisten 2: <?php echo e($kelas->asisten2->nama_asisten); ?></span><?php endif; ?>
        </div>
    </div>
    <div class="course-card-footer">
        <a href="<?php echo e(route('pengawas.rekap', $kelas)); ?>" class="btn btn-primary btn-sm">Lihat Rekap →</a>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>
<div id="modalGantiPassword" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">...</div>
        <div class="modal-body">
            <form method="POST" action="<?php echo e(route('asisten.ganti-password.update')); ?>">
                <?php echo csrf_field(); ?>
                
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\asistio\resources\views/pengawas/dashboard.blade.php ENDPATH**/ ?>