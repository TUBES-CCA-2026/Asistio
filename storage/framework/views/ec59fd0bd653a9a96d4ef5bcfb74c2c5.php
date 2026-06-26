<?php $__env->startSection('title','Ruangan'); ?>
<?php $__env->startSection('page-title','Ruangan Lab'); ?>
<?php $__env->startSection('content'); ?>
<div class="page-toolbar"><button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Ruangan</button></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>#</th><th>Nama Ruangan</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $ruanganAll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr><td><?php echo e($i+1); ?></td><td class="fw-500"><?php echo e($r->nama_ruangan); ?></td>
    <td><form method="POST" action="<?php echo e(route('laboran.ruangan.destroy',$r)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
    <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus ruangan ini?')">Hapus</button></form></td></tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="3"><div class="empty-state"><p>Belum ada ruangan.</p></div></td></tr>
    <?php endif; ?>
    </tbody>
</table></div></div>
<div id="modalTambah" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Ruangan</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="<?php echo e(route('laboran.ruangan.store')); ?>"><?php echo csrf_field(); ?>
    <div class="form-group"><label class="form-label required">Nama Ruangan</label><input name="nama_ruangan" class="form-control" required placeholder="cth: Lab Komputer 1"></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\asistio\resources\views/laboran/ruangan/index.blade.php ENDPATH**/ ?>