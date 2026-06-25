<?php $__env->startSection('title','Nilai & Absensi'); ?>
<?php $__env->startSection('page-title','Pengentrian Nilai & Absensi'); ?>
<?php $__env->startSection('page-subtitle'); ?>
    <?php echo e($mahasiswa->nama_mahasiswa); ?> — <?php echo e($mahasiswa->nim_mahasiswa); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-toolbar">
    <a href="<?php echo e(route('laboran.mahasiswa')); ?>" class="btn btn-outline">← Kembali ke Daftar</a>
    <div class="badge-group">
        <span class="badge badge-primary"><?php echo e($mahasiswa->mataKuliah?->kode_mk); ?></span>
        <span><?php echo e($mahasiswa->mataKuliah?->nama_mk); ?></span>
    </div>
</div>

<form method="POST" action="<?php echo e(route('laboran.mahasiswa.nilai.update', $mahasiswa)); ?>">
<?php echo csrf_field(); ?>

<div class="grid grid-2" style="align-items:start;">

    
    <div style="display:flex;flex-direction:column;gap:16px;">

        
        <div class="card">
            <div class="card-header"><span class="card-title">Nilai Evaluasi</span><span class="badge badge-gray">Bobot 20%</span></div>
            <div class="card-body">
                <div class="grid grid-2">
                    <?php $__currentLoopData = [1,2,3,4]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="form-group">
                        <label class="form-label">Evaluasi <?php echo e($i); ?></label>
                        <input type="number" name="nilai_evaluasi<?php echo e($i); ?>" class="form-control"
                            min="0" max="100" step="0.01"
                            value="<?php echo e($nilaiEvaluasi->{'nilai_evaluasi'.$i} ?? ''); ?>"
                            placeholder="0–100">
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php if($nilaiEvaluasi->rata_rata): ?>
                <div class="info-row"><span class="text-muted fs-13">Rata-rata evaluasi:</span><span class="fw-700 text-primary"><?php echo e($nilaiEvaluasi->rata_rata); ?></span></div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="card">
            <div class="card-header"><span class="card-title">Nilai Asistensi</span><span class="badge badge-gray">Bobot 30%</span></div>
            <div class="card-body">
                <div class="grid grid-3">
                    <?php $__currentLoopData = [1,2,3]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="form-group">
                        <label class="form-label">Asistensi <?php echo e($i); ?></label>
                        <input type="number" name="nilai_asistensi<?php echo e($i); ?>" class="form-control"
                            min="0" max="100" step="0.01"
                            value="<?php echo e($nilaiAsistensi->{'nilai_asistensi'.$i} ?? ''); ?>"
                            placeholder="0–100">
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php if($nilaiAsistensi->rata_rata): ?>
                <div class="info-row"><span class="text-muted fs-13">Rata-rata asistensi:</span><span class="fw-700 text-primary"><?php echo e($nilaiAsistensi->rata_rata); ?></span></div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="card">
            <div class="card-header"><span class="card-title">Nilai Ujian</span></div>
            <div class="card-body">
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Nilai MID <span class="text-muted">(Bobot 20%)</span></label>
                        <input type="number" name="nilai_MID" class="form-control"
                            min="0" max="100" step="0.01"
                            value="<?php echo e($nilaiUjian->nilai_MID ?? ''); ?>" placeholder="0–100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nilai UAS <span class="text-muted">(Bobot 30%)</span></label>
                        <input type="number" name="nilai_UAS" class="form-control"
                            min="0" max="100" step="0.01"
                            value="<?php echo e($nilaiUjian->nilai_UAS ?? ''); ?>" placeholder="0–100">
                    </div>
                </div>
            </div>
        </div>

        
        <?php if($rekap): ?>
        <div class="card" style="border-color:var(--primary);background:var(--primary-pale);">
            <div class="card-body">
                <div class="rekap-grid">
                    <div class="rekap-item"><div class="rekap-label">Nilai Praktikum</div><div class="rekap-value"><?php echo e($rekap->nilai_praktikum ?? '—'); ?></div></div>
                    <div class="rekap-item"><div class="rekap-label">Nilai Asistensi</div><div class="rekap-value"><?php echo e($rekap->nilai_asistensi ?? '—'); ?></div></div>
                    <div class="rekap-item"><div class="rekap-label">MID</div><div class="rekap-value"><?php echo e($rekap->nilai_MID ?? '—'); ?></div></div>
                    <div class="rekap-item"><div class="rekap-label">UAS</div><div class="rekap-value"><?php echo e($rekap->nilai_UAS ?? '—'); ?></div></div>
                </div>
                <div style="text-align:center;margin-top:12px;padding-top:12px;border-top:1px solid var(--primary-soft);">
                    <div class="fs-12 text-muted">Total Keseluruhan Nilai</div>
                    <div style="font-size:2rem;font-weight:800;color:var(--primary);"><?php echo e($rekap->total_keseluruhan_nilai ?? '—'); ?></div>
                    <?php if($rekap->nilai_huruf): ?><span class="grade-badge badge-<?php echo e(strtolower($rekap->nilai_huruf)); ?>"><?php echo e($rekap->nilai_huruf); ?></span><?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    
    <div class="card">
        <div class="card-header">
            <span class="card-title">Presensi per Pertemuan</span>
            <?php $totalAlpa = $presensiList->where('status_kehadiran','A')->count(); ?>
            <?php if($totalAlpa >= 4): ?>
            <span class="badge badge-danger"><?php echo e($totalAlpa); ?> Alpha</span>
            <?php endif; ?>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead><tr><th>Pertemuan</th><th style="text-align:center;">Status</th><th>Catatan</th></tr></thead>
                <tbody>
                <?php for($i = 1; $i <= $jumlahPertemuan; $i++): ?>
                <?php $p = $presensiList->firstWhere('pertemuan_ke', $i); ?>
                <tr>
                    <td class="fw-500">Pertemuan <?php echo e($i); ?></td>
                    <td style="text-align:center;">
                        <?php if($p): ?>
                        <select name="presensi[<?php echo e($p->id); ?>][status_kehadiran]" class="form-select form-select-sm">
                            <?php $__currentLoopData = ['H'=>'Hadir','I'=>'Izin','S'=>'Sakit','A'=>'Alpha']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php echo e($p->status_kehadiran === $val ? 'selected' : ''); ?>><?php echo e($lbl); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php else: ?>
                        <span class="text-muted fs-12">Belum diisi</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($p?->catatan ?? '—'); ?></td>
                </tr>
                <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div style="margin-top:20px;display:flex;gap:10px;justify-content:flex-end;">
    <a href="<?php echo e(route('laboran.mahasiswa')); ?>" class="btn btn-outline">Batal</a>
    <button type="submit" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
        Simpan Nilai & Presensi
    </button>
</div>

</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\asistio\resources\views/laboran/mahasiswa/nilai.blade.php ENDPATH**/ ?>