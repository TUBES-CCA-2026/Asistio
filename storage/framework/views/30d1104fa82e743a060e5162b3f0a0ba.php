<?php $__env->startSection('title','Dashboard Asisten'); ?>
<?php $__env->startSection('page-title','Dashboard Asisten'); ?>
<?php $__env->startSection('content'); ?>


<?php if($errors->any()): ?>
<script>document.addEventListener('DOMContentLoaded',()=>{ const o=document.getElementById('modalGantiPassword'); if(o) o.classList.add('is-open'); });</script>
<?php endif; ?>

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


<div id="modalGantiPassword" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Ganti Password</span>
            <button data-modal-close="modalGantiPassword" class="modal-close">✕</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="<?php echo e(route('asisten.ganti-password.update')); ?>">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label class="form-label required">Password Lama</label>
                    <input type="password" name="password_lama"
                        class="form-control <?php echo e($errors->has('password_lama') ? 'is-invalid' : ''); ?>"
                        required autocomplete="current-password">
                    <?php $__errorArgs = ['password_lama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="invalid-feedback"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="form-group">
                    <label class="form-label required">Password Baru</label>
                    <input type="password" name="password_baru"
                        class="form-control <?php echo e($errors->has('password_baru') ? 'is-invalid' : ''); ?>"
                        required minlength="6" autocomplete="new-password">
                    <?php $__errorArgs = ['password_baru'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="invalid-feedback"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="form-group">
                    <label class="form-label required">Konfirmasi Password Baru</label>
                    <input type="password" name="password_baru_confirmation"
                        class="form-control" required minlength="6" autocomplete="new-password">
                </div>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" data-modal-close="modalGantiPassword" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tes1\resources\views/asisten/dashboard.blade.php ENDPATH**/ ?>