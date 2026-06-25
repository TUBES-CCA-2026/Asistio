<?php $__env->startSection('title','Kelas Praktikum'); ?>
<?php $__env->startSection('page-title','Kelas Praktikum'); ?>
<?php $__env->startSection('content'); ?>
<div class="page-toolbar"><button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Kelas</button></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>Kelas</th><th>Mata Kuliah</th><th>Jadwal</th><th>Ruangan</th><th>Dosen</th><th>Asisten</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $kelasAll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr>
        <td class="fw-600"><?php echo e($k->nama_kelas); ?></td>
        <td><?php echo e($k->mataKuliah?->nama_mk); ?></td>
        <td class="fs-12"><?php echo e($k->jadwal ?? '—'); ?></td>
        <td class="fs-12"><?php echo e($k->ruangan?->nama_ruangan ?? '—'); ?></td>
        <td class="fs-12"><?php echo e($k->dosen?->nama_dosen ?? '—'); ?></td>
        <td class="fs-12"><?php echo e($k->asisten?->nama_asisten ?? '—'); ?></td>
        <td><form method="POST" action="<?php echo e(route('laboran.kelas.destroy',$k)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus kelas ini?')">Hapus</button></form></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="7"><div class="empty-state"><p>Belum ada kelas praktikum.</p></div></td></tr>
    <?php endif; ?>
    </tbody>
</table></div></div>
<div id="modalTambah" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Kelas Praktikum</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="<?php echo e(route('laboran.kelas.store')); ?>"><?php echo csrf_field(); ?>
    <div class="grid grid-2">
        <div class="form-group"><label class="form-label required">Mata Kuliah</label><select name="mata_kuliah_id" class="form-select" required><option value="">Pilih...</option><?php $__currentLoopData = $mataKuliah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($mk->id); ?>"><?php echo e($mk->kode_mk); ?> — <?php echo e($mk->nama_mk); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        <div class="form-group"><label class="form-label required">Kelas / Frekuensi</label><input name="nama_kelas" class="form-control" required placeholder="cth: Kelas A"></div>
        <div class="form-group"><label class="form-label">Jadwal</label><input name="jadwal" class="form-control" placeholder="cth: Senin, 08:00–10:00"></div>
        <div class="form-group"><label class="form-label">Ruangan</label><select name="ruangan_id" class="form-select"><option value="">Pilih...</option><?php $__currentLoopData = $ruanganAll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($r->id); ?>"><?php echo e($r->nama_ruangan); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        <div class="form-group"><label class="form-label">Dosen</label><select name="dosen_id" class="form-select"><option value="">Pilih...</option><?php $__currentLoopData = $dosenAll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($d->id); ?>"><?php echo e($d->nama_dosen); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        <div class="form-group"><label class="form-label">Asisten</label><select name="asisten_id" class="form-select"><option value="">Pilih...</option><?php $__currentLoopData = $asistenAll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($a->id); ?>"><?php echo e($a->nama_asisten); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
    </div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\asistio\resources\views/laboran/kelas/index.blade.php ENDPATH**/ ?>