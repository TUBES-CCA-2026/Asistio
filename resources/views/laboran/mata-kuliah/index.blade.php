@extends('layouts.app')
@section('title','Mata Kuliah')
@section('page-title','Mata Kuliah')
@section('content')
<div class="page-toolbar">
    <button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Mata Kuliah</button>
</div>
<div class="card">
    <div class="table-wrapper"><table class="table">
        <thead><tr><th>Kode MK</th><th>Nama Mata Kuliah</th><th style="text-align:center;">Aksi</th></tr></thead>
        <tbody>
        @forelse($mataKuliahAll as $mk)
        <tr>
            <td><span class="badge badge-primary">{{ $mk->kode_mk }}</span></td>
            <td class="fw-600">{{ $mk->nama_mk }}</td>
            <td style="text-align:center;">
                <div class="action-group" style="justify-content:center;">
                <button type="button" class="btn btn-sm btn-outline" data-modal-open="modalEditMK{{ $mk->id }}">Edit</button>
                <form method="POST" action="{{ route('laboran.mata-kuliah.destroy',$mk) }}">@csrf @method('DELETE')
                <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus mata kuliah ini?')">Hapus</button></form>
                </div>
            </td>
        </tr>
        @empty<tr><td colspan="3"><div class="empty-state"><p>Belum ada mata kuliah.</p></div></td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>

{{-- Modal Tambah --}}
<div id="modalTambah" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Mata Kuliah</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body">
    <form method="POST" action="{{ route('laboran.mata-kuliah.store') }}">@csrf
    <div class="form-group"><label class="form-label required">Kode MK</label><input name="kode_mk" class="form-control" required placeholder="cth: IF-BD-A"></div>
    <div class="form-group"><label class="form-label required">Nama Mata Kuliah</label><input name="nama_mk" class="form-control" required></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div></div>

{{-- Modal Edit per Mata Kuliah --}}
@foreach($mataKuliahAll as $mk)
<div id="modalEditMK{{ $mk->id }}" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Edit — {{ $mk->nama_mk }}</span><button data-modal-close="modalEditMK{{ $mk->id }}" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.mata-kuliah.update',$mk) }}">@csrf @method('PATCH')
    <div class="form-group"><label class="form-label required">Kode MK</label><input name="kode_mk" class="form-control" value="{{ $mk->kode_mk }}" required></div>
    <div class="form-group"><label class="form-label required">Nama Mata Kuliah</label><input name="nama_mk" class="form-control" value="{{ $mk->nama_mk }}" required></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalEditMK{{ $mk->id }}" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div></div>
@endforeach
@endsection