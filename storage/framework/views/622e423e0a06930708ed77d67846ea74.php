<?php $__env->startSection('title','Rekap Data'); ?>
<?php $__env->startSection('page-title','Rekap Data Praktikan'); ?>
<?php $__env->startSection('page-subtitle'); ?> <?php echo e($praktikum->mataKuliah?->nama_mk); ?> — <?php echo e($praktikum->nama_kelas); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="page-toolbar">
    <a href="<?php echo e(route('pengawas.dashboard')); ?>" class="btn btn-outline">← Kembali</a>
    <div style="display:flex;gap:8px;">
        <a href="<?php echo e(route('pengawas.rekap.export.pdf', $praktikum)); ?>" class="btn btn-outline btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Export PDF
        </a>
        <a href="<?php echo e(route('pengawas.rekap.export.excel', $praktikum)); ?>" class="btn btn-outline btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Export Excel
        </a>
    </div>
</div>
<div class="card mb-4">
    <div class="card-header"><span class="card-title">Rekap Nilai Akhir</span></div>
    <div class="table-wrapper"><table class="table">
        <thead><tr><th>NIM</th><th>Nama</th><th style="text-align:center;">Eval</th><th style="text-align:center;">Asist</th><th style="text-align:center;">MID</th><th style="text-align:center;">UAS</th><th style="text-align:center;">Nilai Akhir</th><th style="text-align:center;">Huruf</th><th style="text-align:center;">Kehadiran</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $mahasiswaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php $r = $m->rekap; $alpa = $m->jumlah_alpa; $alpaTinggi = $alpa >= \App\Models\Mahasiswa::BATAS_ALPA; ?>
        
        <tr class="<?php echo e($alpaTinggi ? 'row-alpa-alert' : ''); ?>">
            <td style="font-family:monospace;font-size:12px;"><?php echo e($m->nim_mahasiswa); ?></td>
            <td class="fw-600"><?php echo e($m->nama_mahasiswa); ?></td>
            <td style="text-align:center;"><?php echo e($r?->nilai_praktikum ?? '—'); ?></td>
            <td style="text-align:center;"><?php echo e($r?->nilai_asistensi ?? '—'); ?></td>
            <td style="text-align:center;"><?php echo e($r?->nilai_MID ?? '—'); ?></td>
            <td style="text-align:center;"><?php echo e($r?->nilai_UAS ?? '—'); ?></td>
            <td style="text-align:center;font-weight:700;color:var(--primary);"><?php echo e($r?->nilai_akhir ?? '—'); ?></td>
            <td style="text-align:center;"><?php if($r?->nilai_huruf): ?><span class="grade-badge badge-<?php echo e(strtolower($r->nilai_huruf)); ?>"><?php echo e($r->nilai_huruf); ?></span><?php else: ?>—<?php endif; ?></td>
            <td style="text-align:center;">
                <?php echo e($m->persentase_hadir); ?>

                <?php if($alpa >= 4): ?><span class="badge badge-danger ml-1"><?php echo e($alpa); ?>α</span><?php endif; ?>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="9"><div class="empty-state"><p>Belum ada data.</p></div></td></tr>
        <?php endif; ?>
        </tbody>
    </table></div>
</div>
<div class="card">
    <div class="card-header"><span class="card-title">Rekap Presensi</span></div>
    <div style="overflow-x:auto;"><table class="table" style="min-width:800px;">
        <thead><tr><th>NIM</th><th>Nama</th><?php for($i=1;$i<=14;$i++): ?><th style="text-align:center;width:32px;">P<?php echo e($i); ?></th><?php endfor; ?><th>H</th><th>A</th></tr></thead>
        <tbody>
        <?php $__currentLoopData = $mahasiswaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $pp = $presensiAll[$m->id] ?? collect();
            $jumlahAlpaPertemuan = $pp->where('status_kehadiran','A')->count();
            $alpaTinggiPertemuan = $jumlahAlpaPertemuan >= \App\Models\Mahasiswa::BATAS_ALPA;
        ?>
        <tr class="<?php echo e($alpaTinggiPertemuan ? 'row-alpa-alert' : ''); ?>">
            <td style="font-family:monospace;font-size:11px;"><?php echo e($m->nim_mahasiswa); ?></td>
            <td>
                <?php echo e($m->nama_mahasiswa); ?>

                <?php if($alpaTinggiPertemuan): ?>
                    <span class="badge-alpa-alert" title="Sudah alpa <?php echo e($jumlahAlpaPertemuan); ?>x — sudah mencapai/melewati batas <?php echo e(\App\Models\Mahasiswa::BATAS_ALPA); ?> pertemuan">⚠ Alpa <?php echo e($jumlahAlpaPertemuan); ?>×</span>
                <?php endif; ?>
            </td>
            <?php for($j=1;$j<=14;$j++): ?><?php $ps=$pp[$j]??null; ?>
            <td style="text-align:center;padding:4px 2px;"><?php if($ps): ?><span class="status-chip status-chip-<?php echo e($ps->status_kehadiran); ?>"><?php echo e($ps->status_kehadiran); ?></span><?php else: ?><span class="status-chip status-chip-empty">—</span><?php endif; ?></td>
            <?php endfor; ?>
            <td style="font-weight:700;color:var(--status-h);"><?php echo e($pp->where('status_kehadiran','H')->count()); ?></td>
            <td style="font-weight:700;color:var(--status-a);"><?php echo e($pp->where('status_kehadiran','A')->count()); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\asistio\resources\views/pengawas/rekap.blade.php ENDPATH**/ ?>