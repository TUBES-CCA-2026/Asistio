<?php $__env->startSection('title','Mata Kuliah'); ?>
<?php $__env->startSection('page-title','Mata Kuliah'); ?>
<?php $__env->startSection('content'); ?>
<div class="page-toolbar">
    <button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Mata Kuliah</button>
</div>
<div class="card">
    <div class="table-wrapper"><table class="table">
        <thead><tr><th>Kode MK</th><th>Nama Mata Kuliah</th><th style="text-align:center;">Aksi</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $mataKuliahAll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr><td><span class="badge badge-primary"><?php echo e($mk->kode_mk); ?></span></td><td class="fw-600"><?php echo e($mk->nama_mk); ?></td>
        <td style="text-align:center;">
            <form method="POST" action="<?php echo e(route('laboran.mata-kuliah.destroy',$mk)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus mata kuliah ini?')">Hapus</button></form>
        </td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="3"><div class="empty-state"><p>Belum ada mata kuliah.</p></div></td></tr>
        <?php endif; ?>
        </tbody>
    </table></div>
</div>
<div id="modalTambah" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Mata Kuliah</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body">
    <form method="POST" action="<?php echo e(route('laboran.mata-kuliah.store')); ?>"><?php echo csrf_field(); ?>
    <div class="form-group"><label class="form-label required">Kode MK</label><input name="kode_mk" class="form-control" required placeholder="cth: IF-BD-A"></div>
    <div class="form-group"><label class="form-label required">Nama Mata Kuliah</label><input name="nama_mk" class="form-control" required></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\asistio\resources\views/laboran/mata-kuliah/index.blade.php ENDPATH**/ ?>