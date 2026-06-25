<?php $__env->startSection('title','Dosen'); ?>
<?php $__env->startSection('page-title','Manajemen Dosen'); ?>
<?php $__env->startSection('content'); ?>
<div class="page-toolbar"><button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Dosen</button></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>Nama Dosen</th><th>NIDN</th><th>Mata Kuliah</th><th>Username</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $dosenAll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr>
        <td><div style="display:flex;align-items:center;gap:8px;"><div class="avatar avatar-sm"><?php echo e(strtoupper(substr($d->nama_dosen,0,2))); ?></div><span class="fw-600"><?php echo e($d->nama_dosen); ?></span></div></td>
        <td style="font-family:monospace;"><?php echo e($d->nidn ?? '—'); ?></td>
        <td><?php echo e($d->praktikum->first()?->mataKuliah?->nama_mk ?? '—'); ?></td>
        <td><?php echo e($d->user?->username ?? '—'); ?></td>
        <td>
            <div class="action-group">
            <form method="POST" action="<?php echo e(route('laboran.dosen.destroy',$d)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus dosen <?php echo e($d->nama_dosen); ?>?')">Hapus</button></form>
            <div class="dropdown">
                <button type="button" class="dropdown-toggle" data-dropdown-toggle="dd<?php echo e($d->id); ?>" title="Opsi lain">&#8942;</button>
                <div id="dd<?php echo e($d->id); ?>" class="dropdown-menu">
                    <button type="button" class="dropdown-item" data-modal-open="modalReset<?php echo e($d->id); ?>">Ganti Password</button>
                </div>
            </div>
            </div>
        </td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="5"><div class="empty-state"><p>Belum ada dosen.</p></div></td></tr>
    <?php endif; ?>
    </tbody>
</table></div></div>


<div id="modalTambah" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Dosen</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="<?php echo e(route('laboran.dosen.store')); ?>"><?php echo csrf_field(); ?>
    <div class="form-group"><label class="form-label required">Nama Dosen</label><input name="nama_dosen" class="form-control" required></div>
    <div class="form-group"><label class="form-label">NIDN</label><input name="nidn" class="form-control" placeholder="opsional"></div>
    <div class="form-group"><label class="form-label">Mata Kuliah yang Diampu</label><select name="mata_kuliah_id" class="form-select"><option value="">Pilih Mata Kuliah...</option><?php $__currentLoopData = $mataKuliah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($mk->id); ?>"><?php echo e($mk->kode_mk); ?> — <?php echo e($mk->nama_mk); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
    <div class="form-group"><label class="form-label required">Username (untuk login)</label><input name="username" class="form-control" required></div>
    <div class="form-group"><label class="form-label required">Password</label><input type="password" name="password" class="form-control" required minlength="6"></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div></div>


<?php $__currentLoopData = $dosenAll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div id="modalReset<?php echo e($d->id); ?>" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Reset Password — <?php echo e($d->nama_dosen); ?></span><button data-modal-close="modalReset<?php echo e($d->id); ?>" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="<?php echo e(route('laboran.dosen.reset-password',$d)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
    <p style="margin:0 0 12px;color:var(--text-muted);font-size:14px;">Password lama tidak diperlukan. Dosen akan otomatis logout dari sesi aktifnya.</p>
    <div class="form-group"><label class="form-label required">Password Baru</label><input type="password" name="password" class="form-control" required minlength="6"></div>
    <div class="form-group"><label class="form-label required">Konfirmasi Password</label><input type="password" name="password_confirmation" class="form-control" required minlength="6"></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalReset<?php echo e($d->id); ?>" class="btn btn-outline">Batal</button><button class="btn btn-primary">Reset</button></div>
    </form></div>
</div></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\asistio\resources\views/laboran/dosen/index.blade.php ENDPATH**/ ?>