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
    <div class="form-group"><label class="form-label required">Kelas / Praktikum</label>
        <select name="praktikum_id" class="form-select" required>
            <?php $__currentLoopData = $praktikumAll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($p->id); ?>" <?php echo e($mahasiswa->praktikum_id==$p->id?'selected':''); ?>>
                <?php echo e($p->mataKuliah?->kode_mk); ?> — <?php echo e($p->mataKuliah?->nama_mk); ?> (<?php echo e($p->nama_kelas); ?>)
            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><a href="<?php echo e(route('laboran.mahasiswa')); ?>" class="btn btn-outline">Batal</a><button class="btn btn-primary">Simpan</button></div>
    </form></div></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tes1\resources\views/laboran/mahasiswa/edit.blade.php ENDPATH**/ ?>