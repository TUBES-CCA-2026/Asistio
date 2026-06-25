<?php $__env->startSection('title','Ganti Password'); ?>
<?php $__env->startSection('page-title','Ganti Password'); ?>
<?php $__env->startSection('content'); ?>
<div class="card" style="max-width:480px;">
    <div class="card-body" style="padding:24px;">
        <form method="POST" action="<?php echo e(route('asisten.ganti-password.update')); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label class="form-label required" for="password_lama">Password Lama</label>
                <input type="password" id="password_lama" name="password_lama"
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
                <label class="form-label required" for="password_baru">Password Baru</label>
                <input type="password" id="password_baru" name="password_baru"
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
                <label class="form-label required" for="password_baru_confirmation">Konfirmasi Password Baru</label>
                <input type="password" id="password_baru_confirmation" name="password_baru_confirmation"
                    class="form-control" required minlength="6" autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Simpan Password Baru</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\asistio\resources\views/asisten/ganti-password.blade.php ENDPATH**/ ?>