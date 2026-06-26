@extends('layouts.app')
@section('title','Kelas Praktikum')
@section('page-title','Kelas Praktikum')
@section('content')
<div class="page-toolbar"><button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Kelas</button></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>Kelas</th><th>Mata Kuliah</th><th>Jadwal</th><th>Ruangan</th><th>Dosen</th><th>Asisten 1</th><th>Asisten 2</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($kelasAll as $k)
    <tr>
        <td class="fw-600">{{ $k->nama_kelas }}</td>
        <td>{{ $k->mataKuliah?->nama_mk }}</td>
        <td class="fs-12">{{ $k->jadwal ?? '—' }}</td>
        <td class="fs-12">{{ $k->ruangan?->nama_ruangan ?? '—' }}</td>
        <td class="fs-12">{{ $k->dosen?->nama_dosen ?? '—' }}</td>
        <td class="fs-12">{{ $k->asisten?->nama_asisten ?? '—' }}</td>
        <td class="fs-12">{{ $k->asisten2?->nama_asisten ?? '—' }}</td>
        <td>
            <div style="display:flex;gap:6px;">
            <a href="{{ route('laboran.kelas.show',$k) }}" class="btn btn-sm btn-outline">Edit</a>
            <form method="POST" action="{{ route('laboran.kelas.destroy',$k) }}">@csrf @method('DELETE')
            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus kelas ini?')">Hapus</button></form>
            </div>
        </td>
    </tr>
    @empty<tr><td colspan="8"><div class="empty-state"><p>Belum ada kelas praktikum.</p></div></td></tr>
    @endforelse
    </tbody>
</table></div></div>
<div id="modalTambah" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Kelas Praktikum</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.kelas.store') }}">@csrf
    <div class="grid grid-2">
        <div class="form-group"><label class="form-label required">Mata Kuliah</label><select name="mata_kuliah_id" class="form-select" required><option value="">Pilih...</option>@foreach($mataKuliah as $mk)<option value="{{ $mk->id }}">{{ $mk->kode_mk }} — {{ $mk->nama_mk }}</option>@endforeach</select></div>
        <div class="form-group"><label class="form-label required">Kelas / Frekuensi</label><input name="nama_kelas" class="form-control" required placeholder="cth: Kelas A"></div>
        <div class="form-group"><label class="form-label">Jadwal</label><input name="jadwal" class="form-control" placeholder="cth: Senin, 08:00–10:00"></div>
        <div class="form-group"><label class="form-label">Ruangan</label><select name="ruangan_id" class="form-select"><option value="">Pilih...</option>@foreach($ruanganAll as $r)<option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>@endforeach</select></div>
        <div class="form-group"><label class="form-label">Dosen</label><select name="dosen_id" class="form-select"><option value="">Pilih...</option>@foreach($dosenAll as $d)<option value="{{ $d->id }}">{{ $d->nama_dosen }}</option>@endforeach</select></div>
        <div class="form-group"><label class="form-label">Asisten 1</label><select name="asisten_id" class="form-select"><option value="">Pilih...</option>@foreach($asistenAll as $a)<option value="{{ $a->id }}">{{ $a->nama_asisten }}</option>@endforeach</select></div>
        <div class="form-group"><label class="form-label">Asisten 2</label><select name="asisten2_id" class="form-select"><option value="">Pilih...</option>@foreach($asistenAll as $a)<option value="{{ $a->id }}">{{ $a->nama_asisten }}</option>@endforeach</select></div>
    </div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div></div>
@endsection
