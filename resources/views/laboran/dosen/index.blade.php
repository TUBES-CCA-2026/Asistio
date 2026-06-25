@extends('layouts.app')
@section('title','Dosen')
@section('page-title','Manajemen Dosen')
@section('content')
<div class="page-toolbar"><button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Dosen</button></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>Nama Dosen</th><th>NIDN</th><th>Kelas Diampu</th><th>Username</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($dosenAll as $d)
    <tr>
        <td><div style="display:flex;align-items:center;gap:8px;"><div class="avatar avatar-sm">{{ strtoupper(substr($d->nama_dosen,0,2)) }}</div><span class="fw-600">{{ $d->nama_dosen }}</span></div></td>
        <td style="font-family:monospace;">{{ $d->nidn ?? '—' }}</td>
        <td class="fs-12">
            @forelse($d->praktikum as $p)
                <span style="display:block;">{{ $p->mataKuliah?->kode_mk }} — {{ $p->nama_kelas }}</span>
            @empty
                —
            @endforelse
        </td>
        <td>{{ $d->user?->username ?? '—' }}</td>
        <td>
            <div class="action-group">
            <form method="POST" action="{{ route('laboran.dosen.destroy',$d) }}">@csrf @method('DELETE')
            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus dosen {{ $d->nama_dosen }}?')">Hapus</button></form>
            <div class="dropdown">
                <button type="button" class="dropdown-toggle" data-dropdown-toggle="dd{{ $d->id }}" title="Opsi lain">&#8942;</button>
                <div id="dd{{ $d->id }}" class="dropdown-menu">
                    <button type="button" class="dropdown-item" data-modal-open="modalReset{{ $d->id }}">Ganti Password</button>
                </div>
            </div>
            </div>
        </td>
    </tr>
    @empty<tr><td colspan="5"><div class="empty-state"><p>Belum ada dosen.</p></div></td></tr>
    @endforelse
    </tbody>
</table></div></div>

{{-- Modal Tambah Dosen --}}
<div id="modalTambah" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Dosen</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.dosen.store') }}">@csrf
    <div class="form-group"><label class="form-label required">Nama Dosen</label><input name="nama_dosen" class="form-control" required></div>
    <div class="form-group"><label class="form-label">NIDN</label><input name="nidn" class="form-control" placeholder="opsional"></div>
    <div class="form-group"><label class="form-label required">Username (untuk login)</label><input name="username" class="form-control" required></div>
    <div class="form-group"><label class="form-label required">Password</label><input type="password" name="password" class="form-control" required minlength="6"></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div></div>

{{-- Modal Reset Password per Dosen --}}
@foreach($dosenAll as $d)
<div id="modalReset{{ $d->id }}" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Reset Password — {{ $d->nama_dosen }}</span><button data-modal-close="modalReset{{ $d->id }}" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.dosen.reset-password',$d) }}">@csrf @method('PATCH')
    <p style="margin:0 0 12px;color:var(--text-muted);font-size:14px;">Password lama tidak diperlukan. Dosen akan otomatis logout dari sesi aktifnya.</p>
    <div class="form-group"><label class="form-label required">Password Baru</label><input type="password" name="password" class="form-control" required minlength="6"></div>
    <div class="form-group"><label class="form-label required">Konfirmasi Password</label><input type="password" name="password_confirmation" class="form-control" required minlength="6"></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalReset{{ $d->id }}" class="btn btn-outline">Batal</button><button class="btn btn-primary">Reset</button></div>
    </form></div>
</div></div>
@endforeach

@endsection
