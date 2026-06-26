<?php $__env->startSection('title','Detail Kelas'); ?>
<?php $__env->startSection('page-title', $kelas->nama_kelas); ?>
<?php $__env->startSection('page-subtitle'); ?> <?php echo e($kelas->mataKuliah?->nama_mk); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="page-toolbar"><a href="<?php echo e(route('laboran.kelas')); ?>" class="btn btn-outline">← Kembali ke Kelas Praktikum</a></div>

<div class="grid grid-2" style="gap:16px;align-items:start;">
    <div class="card">
        <div class="card-header"><span class="card-title">Asisten Kelas</span></div>
        <div class="card-body">
            <p style="font-size:12px;color:var(--text-muted);margin:0 0 14px;">
                Tambahkan Asisten 2, ganti asisten yang bertugas, atau kosongkan kembali — cukup pilih lalu simpan.
            </p>
            <form method="POST" action="<?php echo e(route('laboran.kelas.update',$kelas)); ?>">
                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <div class="form-group">
                    <label class="form-label">Asisten 1</label>
                    <select name="asisten_id" class="form-select">
                        <option value="">— Tidak ada —</option>
                        <?php $__currentLoopData = $asistenAll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($a->id); ?>" <?php echo e($kelas->asisten_id == $a->id ? 'selected' : ''); ?>><?php echo e($a->nama_asisten); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Asisten 2</label>
                    <select name="asisten2_id" class="form-select">
                        <option value="">— Tidak ada —</option>
                        <?php $__currentLoopData = $asistenAll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($a->id); ?>" <?php echo e($kelas->asisten2_id == $a->id ? 'selected' : ''); ?>><?php echo e($a->nama_asisten); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <button class="btn btn-primary btn-block">Simpan Asisten</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Tambah Praktikan ke Kelas Ini</span></div>
        <div class="card-body">
            <?php if($mahasiswaBelumKelas->isEmpty()): ?>
                <p style="font-size:13px;color:var(--text-muted);">Semua mahasiswa sudah memiliki kelas. Tambah mahasiswa baru lewat menu <strong>Mahasiswa</strong>, lalu kembali ke sini untuk memasukkannya ke kelas ini.</p>
            <?php else: ?>
                <form method="POST" action="<?php echo e(route('laboran.kelas.mahasiswa.add',$kelas)); ?>" style="display:flex;gap:8px;">
                    <?php echo csrf_field(); ?>
                    <select name="mahasiswa_id" class="form-select" required>
                        <option value="">Tambah Mahasiswa...</option>
                        <?php $__currentLoopData = $mahasiswaBelumKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($m->id); ?>"><?php echo e($m->nim_mahasiswa); ?> — <?php echo e($m->nama_mahasiswa); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button class="btn btn-primary" style="white-space:nowrap;">+ Tambah</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header"><span class="card-title">Daftar Praktikan di Kelas Ini (<?php echo e($mahasiswaDiKelas->count()); ?>)</span></div>
    <div class="table-wrapper"><table class="table">
        <thead><tr><th>NIM</th><th>Nama</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $mahasiswaDiKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td style="font-family:monospace;font-size:13px;"><?php echo e($m->nim_mahasiswa); ?></td>
            <td class="fw-600"><?php echo e($m->nama_mahasiswa); ?></td>
            <td>
                <form method="POST" action="<?php echo e(route('laboran.kelas.mahasiswa.remove',[$kelas,$m])); ?>">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-sm btn-outline" onclick="return confirm('Keluarkan <?php echo e($m->nama_mahasiswa); ?> dari kelas ini?')">Keluarkan</button>
                </form>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="3"><div class="empty-state"><p>Belum ada praktikan di kelas ini.</p></div></td></tr>
        <?php endif; ?>
        </tbody>
    </table></div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tes1\resources\views/laboran/kelas/show.blade.php ENDPATH**/ ?>