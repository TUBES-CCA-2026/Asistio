<?php $__env->startSection('title','Mahasiswa'); ?>
<?php $__env->startSection('page-title','Manajemen Mahasiswa'); ?>
<?php $__env->startSection('content'); ?>
<div class="page-toolbar"><button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Mahasiswa</button></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>NIM</th><th>Nama Mahasiswa</th><th>Kelas yang Diikuti</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $mahasiswaAll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php $adaAlpa = $m->praktikum->contains(fn($p) => $m->melebihiBatasAlpaDiKelas($p->id)); ?>
    <tr class="<?php echo e($adaAlpa ? 'row-alpa-alert' : ''); ?>">
        <td style="font-family:monospace;font-size:13px;"><?php echo e($m->nim_mahasiswa); ?></td>
        <td>
            <div style="display:flex;align-items:center;gap:8px;">
                <div class="avatar avatar-sm"><?php echo e($m->initials); ?></div>
                <span class="fw-600"><?php echo e($m->nama_mahasiswa); ?></span>
                <?php if($adaAlpa): ?>
                    <span class="badge-alpa-alert" title="Ada kelas dengan alpa ≥ <?php echo e(\App\Models\Mahasiswa::BATAS_ALPA); ?>">⚠ Alpa</span>
                <?php endif; ?>
            </div>
        </td>
        <td>
            
            <?php $__empty_2 = true; $__currentLoopData = $m->praktikum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                <span class="badge badge-primary" style="margin:2px 2px 2px 0;">
                    <?php echo e($p->mataKuliah?->kode_mk); ?> — <?php echo e($p->nama_kelas); ?>

                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                <span style="color:var(--text-muted);font-size:12px;">Belum ada kelas</span>
            <?php endif; ?>
        </td>
        <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                
                <?php $__empty_2 = true; $__currentLoopData = $m->praktikum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                    <a href="<?php echo e(route('laboran.mahasiswa.nilai', ['mahasiswa' => $m->id, 'praktikum' => $p->id])); ?>"
                    class="btn btn-sm btn-primary">
                    <?php echo e($p->nama_kelas); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                    <span class="btn btn-sm btn-outline" style="opacity:.5;cursor:not-allowed;" 
                        title="Tambahkan ke kelas dulu lewat menu Kelas Praktikum">
                        Nilai & Absensi
                    </span>
                <?php endif; ?>
                <a href="<?php echo e(route('laboran.mahasiswa.edit', $m)); ?>" class="btn btn-sm btn-outline">Edit</a>
                <form method="POST" action="<?php echo e(route('laboran.mahasiswa.destroy', $m)); ?>" style="margin:0;">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus mahasiswa ini?')">Hapus</button>
                </form>
            </div>
        </td>
    </tr>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="5"><div class="empty-state"><p>Belum ada mahasiswa.</p></div></td></tr>
    <?php endif; ?>
    </tbody>
</table></div>
<?php if($mahasiswaAll->hasPages()): ?>
    <div class="card-footer">
        <?php echo e($mahasiswaAll->links()); ?>

    </div>
<?php endif; ?>
</div>
<div id="modalTambah" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Mahasiswa</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="<?php echo e(route('laboran.mahasiswa.store')); ?>"><?php echo csrf_field(); ?>
    <div class="grid grid-2">
        <div class="form-group"><label class="form-label required">NIM</label><input name="nim_mahasiswa" class="form-control" required></div>
        <div class="form-group"><label class="form-label required">Nama</label><input name="nama_mahasiswa" class="form-control" required></div>
    </div>
    <p style="font-size:12px;color:var(--text-muted);margin:-4px 0 12px;">Kelas belum perlu dipilih sekarang — bisa ditentukan nanti lewat menu <strong>Kelas Praktikum → Edit</strong>.</p>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button><button class="btn btn-primary">Tambah</button></div>
    </form></div>
</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tes1\resources\views/laboran/mahasiswa/index.blade.php ENDPATH**/ ?>