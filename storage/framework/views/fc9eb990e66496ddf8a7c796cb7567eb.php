<?php $__env->startSection('title','Input Presensi'); ?>
<?php $__env->startSection('page-title','Input Presensi'); ?>
<?php $__env->startSection('page-subtitle'); ?> <?php echo e($praktikum->mataKuliah?->nama_mk); ?> — <?php echo e($praktikum->nama_kelas); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="page-toolbar">
    <a href="<?php echo e(route('asisten.dashboard')); ?>" class="btn btn-outline">← Kembali</a>
    <div style="display:flex;align-items:center;gap:8px;">
        <a href="?pertemuan=<?php echo e(max(1,$pertemuan-1)); ?>" class="btn btn-outline btn-sm">‹ Sebelumnya</a>
        <span class="fw-600 text-primary">Pertemuan <?php echo e($pertemuan); ?></span>
        <a href="?pertemuan=<?php echo e($pertemuan+1); ?>" class="btn btn-outline btn-sm">Berikutnya ›</a>
        <form method="GET" action="<?php echo e(url()->current()); ?>" style="display:flex;align-items:center;gap:6px;margin-left:8px;padding-left:8px;border-left:1px solid var(--border-color, #e5e7eb);">
            <label for="pertemuan-jump" class="fs-12 text-muted" style="margin:0;">Lompat ke:</label>
            <input type="number" id="pertemuan-jump" name="pertemuan" min="1" max="14" value="<?php echo e($pertemuan); ?>" class="form-control form-control-sm" style="width:64px;">
            <button type="submit" class="btn btn-outline btn-sm">Lompat</button>
        </form>
    </div>
</div>
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);">
    <div class="stat-card"><div class="stat-body"><div class="stat-value"><?php echo e($stats['total']); ?></div><div class="stat-label">Mahasiswa</div></div></div>
    <div class="stat-card"><div class="stat-body"><div class="stat-value" style="color:var(--status-h)"><?php echo e($stats['hadir']); ?></div><div class="stat-label">Hadir</div></div></div>
    <div class="stat-card"><div class="stat-body"><div class="stat-value" style="color:var(--status-a)"><?php echo e($stats['alpa']); ?></div><div class="stat-label">Alpha</div></div></div>
</div>
<form method="POST" action="<?php echo e(route('asisten.presensi.simpan', $praktikum)); ?>"><?php echo csrf_field(); ?>
<input type="hidden" name="pertemuan" value="<?php echo e($pertemuan); ?>">
<div class="card">
    <div class="card-header">
        <span class="card-title">Pertemuan <?php echo e($pertemuan); ?></span>
        <div style="display:flex;gap:6px;align-items:center;">
            <span class="fs-12 text-muted">Tandai semua:</span>
            <button type="button" class="btn btn-sm btn-outline status-btn-bulk" data-status="H">Hadir</button>
            <button type="button" class="btn btn-sm btn-outline status-btn-bulk" data-status="A">Alpha</button>
        </div>
    </div>
    <div class="table-wrapper"><table class="table">
        <thead><tr><th>#</th><th>NIM</th><th>Nama</th><th style="text-align:center;">H</th><th style="text-align:center;">I</th><th style="text-align:center;">S</th><th style="text-align:center;">A</th><th>Catatan</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $mahasiswaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $p = $presensiMap[$m->id] ?? null;
            $status = $p?->status_kehadiran; // null jika belum diisi, agar tidak ada radio yang tercentang otomatis
            $alpaTinggi = $m->melebihiBatasAlpa();
        ?>
        <tr class="<?php echo e($alpaTinggi ? 'row-alpa-alert' : ''); ?>">
            <td><?php echo e(str_pad($i+1,2,'0',STR_PAD_LEFT)); ?></td>
            <td style="font-family:monospace;font-size:12px;"><?php echo e($m->nim_mahasiswa); ?></td>
            <td class="fw-500">
                <?php echo e($m->nama_mahasiswa); ?>

                <?php if($alpaTinggi): ?>
                    <span class="badge-alpa-alert" title="Sudah alpa <?php echo e($m->jumlah_alpa); ?>x — sudah mencapai/melewati batas <?php echo e(\App\Models\Mahasiswa::BATAS_ALPA); ?> pertemuan">⚠ Alpa <?php echo e($m->jumlah_alpa); ?>×</span>
                <?php endif; ?>
            </td>
            <?php $__currentLoopData = ['H','I','S','A']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <td style="text-align:center;">
                <label class="radio-circle radio-<?php echo e(strtolower($s)); ?>">
                    <input type="radio" name="presensi[<?php echo e($m->id); ?>][status_kehadiran]" value="<?php echo e($s); ?>" <?php echo e($status===$s?'checked':''); ?>>
                    <span><?php echo e($s); ?></span>
                </label>
            </td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <td><input type="text" name="presensi[<?php echo e($m->id); ?>][catatan]" class="form-control form-control-sm" value="<?php echo e($p?->catatan); ?>" placeholder="—"></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="8"><div class="empty-state"><p>Belum ada mahasiswa di kelas ini.</p></div></td></tr>
        <?php endif; ?>
        </tbody>
    </table></div>
    <?php if($mahasiswaList->count() > 0): ?>
    <div class="card-footer"><button type="submit" class="btn btn-primary">Simpan Presensi Pertemuan <?php echo e($pertemuan); ?></button></div>
    <?php endif; ?>
</div>
</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\tes1\resources\views/asisten/presensi.blade.php ENDPATH**/ ?>