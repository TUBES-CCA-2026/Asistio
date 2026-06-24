<?php $__env->startSection('title','Input Nilai'); ?>
<?php $__env->startSection('page-title','Input Nilai'); ?>
<?php $__env->startSection('page-subtitle'); ?> <?php echo e($praktikum->mataKuliah?->nama_mk); ?> — <?php echo e($praktikum->nama_kelas); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="page-toolbar"><a href="<?php echo e(route('asisten.dashboard')); ?>" class="btn btn-outline">← Kembali</a></div>
<div class="card"><div class="table-wrapper" style="overflow-x:auto;">
    <table class="table" style="min-width:900px;">
        <thead><tr>
            <th>Mahasiswa</th>
            <th style="text-align:center;">Eval 1</th><th style="text-align:center;">Eval 2</th>
            <th style="text-align:center;">Eval 3</th><th style="text-align:center;">Eval 4</th>
            <th style="text-align:center;">Asist 1</th><th style="text-align:center;">Asist 2</th><th style="text-align:center;">Asist 3</th>
            <th style="text-align:center;">MID</th><th style="text-align:center;">UAS</th>
            <th style="text-align:center;">NA</th><th style="text-align:center;">Aksi</th>
        </tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $mahasiswaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php $n = $nilaiMap[$m->id]; ?>
        <tr>
            <td><div class="fw-600 fs-13"><?php echo e($m->nama_mahasiswa); ?></div><div class="fs-11 text-muted"><?php echo e($m->nim_mahasiswa); ?></div></td>
            <form method="POST" action="<?php echo e(route('asisten.nilai.simpan', [$praktikum, $m])); ?>"><?php echo csrf_field(); ?>
            <?php $__currentLoopData = [1,2,3,4]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <td><input type="number" name="nilai_evaluasi<?php echo e($i); ?>" class="form-control form-control-xs" min="0" max="100" value="<?php echo e($n['evaluasi']->{'nilai_evaluasi'.$i} ?? ''); ?>" placeholder="—"></td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php $__currentLoopData = [1,2,3]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <td><input type="number" name="nilai_asistensi<?php echo e($i); ?>" class="form-control form-control-xs" min="0" max="100" value="<?php echo e($n['asistensi']->{'nilai_asistensi'.$i} ?? ''); ?>" placeholder="—"></td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <td><input type="number" name="nilai_MID" class="form-control form-control-xs" min="0" max="100" value="<?php echo e($n['ujian']->nilai_MID ?? ''); ?>" placeholder="—"></td>
            <td><input type="number" name="nilai_UAS" class="form-control form-control-xs" min="0" max="100" value="<?php echo e($n['ujian']->nilai_UAS ?? ''); ?>" placeholder="—"></td>
            <td style="text-align:center;">
                <span class="fw-700 text-primary"><?php echo e($n['rekap']?->nilai_akhir ?? '—'); ?></span>
                <?php if($n['rekap']?->nilai_huruf): ?><br><span class="grade-badge badge-<?php echo e(strtolower($n['rekap']->nilai_huruf)); ?>"><?php echo e($n['rekap']->nilai_huruf); ?></span><?php endif; ?>
            </td>
            <td><button type="submit" class="btn btn-sm btn-primary">Simpan</button></form></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="12"><div class="empty-state"><p>Belum ada mahasiswa.</p></div></td></tr>
        <?php endif; ?>
        </tbody>
    </table></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tes1\resources\views/asisten/nilai.blade.php ENDPATH**/ ?>