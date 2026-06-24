@extends('layouts.app')
@section('title','Ruangan')
@section('page-title','Ruangan Lab')
@section('content')
<div class="page-toolbar"><button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Ruangan</button></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>#</th><th>Nama Ruangan</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($ruanganAll as $i => $r)
    <tr><td>{{ $i+1 }}</td><td class="fw-500">{{ $r->nama_ruangan }}</td>
    <td><form method="POST" action="{{ route('laboran.ruangan.destroy',$r) }}">@csrf @method('DELETE')
    <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus ruangan ini?')">Hapus</button></form></td></tr>
    @empty<tr><td colspan="3"><div class="empty-state"><p>Belum ada ruangan.</p></div></td></tr>
    @endforelse
    </tbody>
</table></div></div>
<div id="modalTambah" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Ruangan</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.ruangan.store') }}">@csrf
    <div class="form-group"><label class="form-label required">Nama Ruangan</label><input name="nama_ruangan" class="form-control" required placeholder="cth: Lab Komputer 1"></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div></div>
@endsection
