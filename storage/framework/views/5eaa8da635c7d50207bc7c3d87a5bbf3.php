<?php $__env->startSection('title','Dashboard Laboran'); ?>
<?php $__env->startSection('page-title','Dashboard'); ?>
<?php $__env->startSection('page-subtitle','Selamat datang di Asistio — ICo Labs UMI'); ?>
<?php $__env->startSection('content'); ?>
<div class="stats-grid">
    <div class="stat-card"><div class="stat-icon stat-icon-primary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg></div><div class="stat-body"><div class="stat-value"><?php echo e($totalMK); ?></div><div class="stat-label">Mata Kuliah</div></div></div>
    <div class="stat-card"><div class="stat-icon stat-icon-green"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><div class="stat-body"><div class="stat-value"><?php echo e($totalMahasiswa); ?></div><div class="stat-label">Mahasiswa</div></div></div>
    <div class="stat-card"><div class="stat-icon stat-icon-orange"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/></svg></div><div class="stat-body"><div class="stat-value"><?php echo e($totalAsisten); ?></div><div class="stat-label">Asisten</div></div></div>
    <div class="stat-card"><div class="stat-icon stat-icon-blue"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg></div><div class="stat-body"><div class="stat-value"><?php echo e($totalDosen); ?></div><div class="stat-label">Dosen</div></div></div>
</div>
<div class="card mt-5">
    <div class="card-header"><span class="card-title">Daftar Mata Kuliah</span><a href="<?php echo e(route('laboran.mata-kuliah')); ?>" class="btn btn-sm btn-outline">Kelola →</a></div>
    <div class="table-wrapper">
        <table class="table">
            <thead><tr><th>Kode MK</th><th>Nama Mata Kuliah</th><th style="text-align:center;">Mahasiswa</th><th style="text-align:center;">Kelas</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $mataKuliah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><span class="badge badge-primary"><?php echo e($mk->kode_mk); ?></span></td>
                <td class="fw-600"><?php echo e($mk->nama_mk); ?></td>
                <td style="text-align:center;"><?php echo e($mk->mahasiswa_count); ?></td>
                <td style="text-align:center;"><?php echo e($mk->praktikum_count); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4"><div class="empty-state"><p>Belum ada mata kuliah. <a href="<?php echo e(route('laboran.mata-kuliah')); ?>">Tambahkan sekarang</a>.</p></div></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\asistio\resources\views/laboran/dashboard.blade.php ENDPATH**/ ?>