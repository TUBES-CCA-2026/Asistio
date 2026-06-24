<?php $__env->startSection('title','Asisten'); ?>
<?php $__env->startSection('page-title','Manajemen Asisten'); ?>
<?php $__env->startSection('content'); ?>
<div class="page-toolbar"><button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Asisten</button></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>Nama Asisten</th><th>NIM</th><th>Username</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $asistenAll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr>
        <td><div style="display:flex;align-items:center;gap:8px;"><div class="avatar avatar-sm"><?php echo e(strtoupper(substr($a->nama_asisten,0,2))); ?></div><span class="fw-600"><?php echo e($a->nama_asisten); ?></span></div></td>
        <td style="font-family:monospace;"><?php echo e($a->nim ?? '—'); ?></td>
        <td><?php echo e($a->user?->username ?? '—'); ?></td>
        <td><form method="POST" action="<?php echo e(route('laboran.asisten.destroy',$a)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus asisten <?php echo e($a->nama_asisten); ?>?')">Hapus</button></form></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="4"><div class="empty-state"><p>Belum ada asisten.</p></div></td></tr>
    <?php endif; ?>
    </tbody>
</table></div></div>
<div id="modalTambah" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Asisten</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="<?php echo e(route('laboran.asisten.store')); ?>"><?php echo csrf_field(); ?>
    <div class="form-group"><label class="form-label required">Nama Asisten</label><input name="nama_asisten" class="form-control" required></div>
    <div class="form-group"><label class="form-label required">NIM</label><input name="nim" class="form-control" required></div>
    <div class="form-group"><label class="form-label required">Username (untuk login)</label><input name="username" class="form-control" required></div>
    <div class="form-group"><label class="form-label required">Password</label><input type="password" name="password" class="form-control" required minlength="6"></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tes1\resources\views/laboran/asisten/index.blade.php ENDPATH**/ ?>