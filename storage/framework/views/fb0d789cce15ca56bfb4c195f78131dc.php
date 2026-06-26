<?php $__env->startSection('content'); ?>

<div class="auth-container">
    <div class="auth-card animate-fade-up">

        
        <div class="auth-brand">
            <img src="<?php echo e(asset('images/logo_2.png')); ?>" alt="ICLABS FIKOM UMI Logo" class="auth-logo-img">
            <div class="auth-brand-text">
                <h1 class="auth-brand-name">Asistio</h1>
                <p class="auth-brand-sub">Practicum Management System</p>
            </div>
        </div>

        <h2 class="auth-heading">Selamat datang kembali</h2>
        <p class="auth-subheading">
            Masukkan username dan password Anda. Sistem akan otomatis mendeteksi hak akses Anda.
        </p>

        
        <?php if($errors->any()): ?>
            <div class="alert alert-error" style="margin-bottom:16px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
                <span><?php echo e($errors->first()); ?></span>
            </div>
        <?php endif; ?>
        <?php if(session('success')): ?>
            <div class="alert alert-success" style="margin-bottom:16px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                <span><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-error" style="margin-bottom:16px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
                <span><?php echo e(session('error')); ?></span>
            </div>
        <?php endif; ?>

        
        <form method="POST" action="<?php echo e(route('login.post')); ?>" class="auth-form">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <div class="input-wrapper">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <input type="text" id="username" name="username"
                        class="form-control <?php echo e($errors->has('username') ? 'is-invalid' : ''); ?>"
                        value="<?php echo e(old('username')); ?>"
                        placeholder="Masukkan username Anda"
                        autocomplete="username"
                        required autofocus>
                </div>
                <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="invalid-feedback"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrapper">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    <input type="password" id="password" name="password"
                        class="form-control <?php echo e($errors->has('password') ? 'is-invalid' : ''); ?>"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required>
                    <button type="button" id="togglePassword" class="input-icon-right" title="Tampilkan password">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="invalid-feedback"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group-inline">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" id="remember">
                    <span>Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                Masuk ke Sistem
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </form>

        <p class="auth-footer-note">
            Sistem akan otomatis mengarahkan Anda ke halaman yang sesuai berdasarkan hak akses akun Anda.
        </p>
    </div>

    <p class="auth-copy">© <?php echo e(date('Y')); ?> Asistio — ICLABS FIKOM UMI</p>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('togglePassword')?.addEventListener('click', function() {
    const pwd = document.getElementById('password');
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tes1\resources\views/auth/login.blade.php ENDPATH**/ ?>