<?php $__env->startSection('title','Edit Mahasiswa'); ?>
<?php $__env->startSection('page-title','Edit Mahasiswa'); ?>
<?php $__env->startSection('content'); ?>
<div style="max-width:600px;">
    <a href="<?php echo e(route('laboran.mahasiswa')); ?>" class="btn btn-outline mb-4">← Kembali</a>
    <div class="card"><div class="card-header"><span class="card-title">Edit Data Mahasiswa</span></div>
    <div class="card-body">
    <form method="POST" action="<?php echo e(route('laboran.mahasiswa.update',$mahasiswa)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
    <div class="form-group"><label class="form-label required">NIM</label><input name="nim_mahasiswa" class="form-control" value="<?php echo e($mahasiswa->nim_mahasiswa); ?>" required></div>
    <div class="form-group"><label class="form-label required">Nama</label><input name="nama_mahasiswa" class="form-control" value="<?php echo e($mahasiswa->nama_mahasiswa); ?>" required></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><a href="<?php echo e(route('laboran.mahasiswa')); ?>" class="btn btn-outline">Batal</a><button class="btn btn-primary">Simpan</button></div>
    </form></div></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tes1\resources\views/laboran/mahasiswa/edit.blade.php ENDPATH**/ ?>