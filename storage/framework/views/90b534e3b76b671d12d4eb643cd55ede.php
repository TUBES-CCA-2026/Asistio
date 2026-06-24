<?php $__env->startSection('title','Rekap Presensi'); ?>
<?php $__env->startSection('page-title','Rekap Presensi'); ?>
<?php $__env->startSection('page-subtitle'); ?> <?php echo e($praktikum->mataKuliah?->nama_mk); ?> — <?php echo e($praktikum->nama_kelas); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="page-toolbar"><a href="<?php echo e(route('asisten.dashboard')); ?>" class="btn btn-outline">← Kembali</a></div>
<div class="card"><div style="overflow-x:auto;"><table class="table" style="min-width:<?php echo e(300 + 14*40); ?>px;">
    <thead><tr>
        <th>NIM</th><th>Nama</th>
        <?php for($i=1;$i<=14;$i++): ?><th style="text-align:center;width:36px;">P<?php echo e($i); ?></th><?php endfor; ?>
        <th>H</th><th>A</th>
    </tr></thead>
    <tbody>
    <?php $__currentLoopData = $mahasiswaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $pp = $presensiAll[$m->id] ?? collect(); ?>
    <tr>
        <td style="font-family:monospace;font-size:11px;"><?php echo e($m->nim_mahasiswa); ?></td>
        <td class="fw-500"><?php echo e($m->nama_mahasiswa); ?></td>
        <?php for($j=1;$j<=14;$j++): ?>
        <?php $ps=$pp[$j]??null; ?>
        <td style="text-align:center;padding:4px 2px;">
            <?php if($ps): ?><span class="status-chip status-chip-<?php echo e($ps->status_kehadiran); ?>"><?php echo e($ps->status_kehadiran); ?></span>
            <?php else: ?><span class="status-chip status-chip-empty">—</span><?php endif; ?>
        </td>
        <?php endfor; ?>
        <td style="font-weight:700;color:var(--status-h);"><?php echo e($pp->where('status_kehadiran','H')->count()); ?></td>
        <td style="font-weight:700;color:var(--status-a);"><?php echo e($pp->where('status_kehadiran','A')->count()); ?></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table></div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tes1\resources\views/asisten/rekap.blade.php ENDPATH**/ ?>