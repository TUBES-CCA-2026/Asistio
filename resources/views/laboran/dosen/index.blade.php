@extends('layouts.app')
@section('title','Dosen')
@section('page-title','Manajemen Dosen')
@section('content')
<div class="page-toolbar"><button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Dosen</button></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>Nama Dosen</th><th>NIDN</th><th>Mata Kuliah</th><th>Username</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($dosenAll as $d)
    <tr>
        <td><div style="display:flex;align-items:center;gap:8px;"><div class="avatar avatar-sm">{{ strtoupper(substr($d->nama_dosen,0,2)) }}</div><span class="fw-600">{{ $d->nama_dosen }}</span></div></td>
        <td style="font-family:monospace;">{{ $d->nidn ?? '—' }}</td>
        <td>{{ $d->mataKuliah?->nama_mk ?? '—' }}</td>
        <td>{{ $d->user?->username ?? '—' }}</td>
        <td><form method="POST" action="{{ route('laboran.dosen.destroy',$d) }}">@csrf @method('DELETE')
        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus dosen {{ $d->nama_dosen }}?')">Hapus</button></form></td>
    </tr>
    @empty<tr><td colspan="5"><div class="empty-state"><p>Belum ada dosen.</p></div></td></tr>
    @endforelse
    </tbody>
</table></div></div>
<div id="modalTambah" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Dosen</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.dosen.store') }}">@csrf
    <div class="form-group"><label class="form-label required">Nama Dosen</label><input name="nama_dosen" class="form-control" required></div>
    <div class="form-group"><label class="form-label">NIDN</label><input name="nidn" class="form-control" placeholder="opsional"></div>
    <div class="form-group"><label class="form-label">Mata Kuliah yang Diampu</label><select name="mata_kuliah_id" class="form-select"><option value="">Pilih Mata Kuliah...</option>@foreach($mataKuliah as $mk)<option value="{{ $mk->id }}">{{ $mk->kode_mk }} — {{ $mk->nama_mk }}</option>@endforeach</select></div>
    <div class="form-group"><label class="form-label required">Username (untuk login)</label><input name="username" class="form-control" required></div>
    <div class="form-group"><label class="form-label required">Password</label><input type="password" name="password" class="form-control" required minlength="6"></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div></div>
@endsection
