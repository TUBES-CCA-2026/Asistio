@extends('layouts.app')
@section('title','Asisten')
@section('page-title','Manajemen Asisten')
@section('content')
<div class="page-toolbar"><button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Asisten</button></div>
<div class="card"><div class="table-wrapper"><table class="table">
    <thead><tr><th>Nama Asisten</th><th>NIM</th><th>Username</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($asistenAll as $a)
    <tr>
        <td><div style="display:flex;align-items:center;gap:8px;"><div class="avatar avatar-sm">{{ strtoupper(substr($a->nama_asisten,0,2)) }}</div><span class="fw-600">{{ $a->nama_asisten }}</span></div></td>
        <td style="font-family:monospace;">{{ $a->nim ?? '—' }}</td>
        <td>{{ $a->user?->username ?? '—' }}</td>
        <td>
            <div class="action-group">
            <form method="POST" action="{{ route('laboran.asisten.destroy',$a) }}">@csrf @method('DELETE')
            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus asisten {{ $a->nama_asisten }}?')">Hapus</button></form>
            <div class="dropdown">
                <button type="button" class="dropdown-toggle" data-dropdown-toggle="dd{{ $a->id }}" title="Opsi lain">&#8942;</button>
                <div id="dd{{ $a->id }}" class="dropdown-menu">
                    <button type="button" class="dropdown-item" data-modal-open="modalEditAsisten{{ $a->id }}">Edit Data</button>
                    <button type="button" class="dropdown-item" data-modal-open="modalReset{{ $a->id }}">Ganti Password</button>
                </div>
            </div>
            </div>
        </td>
    </tr>
    @empty<tr><td colspan="4"><div class="empty-state"><p>Belum ada asisten.</p></div></td></tr>
    @endforelse
    </tbody>
</table></div></div>
<div id="modalTambah" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Asisten</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.asisten.store') }}">@csrf
    <div class="form-group"><label class="form-label required">Nama Asisten</label><input name="nama_asisten" class="form-control" required></div>
    <div class="form-group">
        <label class="form-label required">NIM</label>
        <input name="nim" id="inputNimTambah"
            class="form-control {{ $errors->has('nim') && old('_form') === 'tambah' ? 'is-invalid' : '' }}"
            value="{{ old('_form') === 'tambah' ? old('nim') : '' }}"
            inputmode="numeric" autocomplete="off" required>
        @if($errors->has('nim') && old('_form') === 'tambah')
            <div class="form-error">{{ $errors->first('nim') }}</div>
        @endif
    </div>
    <div class="form-group"><label class="form-label required">Username (untuk login)</label><input name="username" class="form-control" required></div>
    <div class="form-group"><label class="form-label required">Password</label><input type="password" name="password" class="form-control" required minlength="6"></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div></div>
@foreach($asistenAll as $a)
<div id="modalEditAsisten{{ $a->id }}" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Edit Data — {{ $a->nama_asisten }}</span><button data-modal-close="modalEditAsisten{{ $a->id }}" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.asisten.update',$a) }}">@csrf @method('PATCH')
    <div class="form-group"><label class="form-label required">Nama Asisten</label><input name="nama_asisten" class="form-control" value="{{ $a->nama_asisten }}" required></div>
    <div class="form-group">
        <label class="form-label required">NIM</label>
        <input name="nim" data-nim-input="edit"
            class="form-control {{ $errors->has('nim') && old('_form') === 'edit-asisten-'.$a->id ? 'is-invalid' : '' }}"
            value="{{ old('_form') === 'edit-asisten-'.$a->id ? old('nim') : $a->nim }}"
            inputmode="numeric" autocomplete="off" required>
        @if($errors->has('nim') && old('_form') === 'edit-asisten-'.$a->id)
            <div class="form-error">{{ $errors->first('nim') }}</div>
        @endif
    </div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalEditAsisten{{ $a->id }}" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div></div>
@endforeach
@foreach($asistenAll as $a)
<div id="modalReset{{ $a->id }}" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Reset Password — {{ $a->nama_asisten }}</span><button data-modal-close="modalReset{{ $a->id }}" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.asisten.reset-password',$a) }}">@csrf @method('PATCH')
    <p style="margin:0 0 12px;color:var(--text-muted);font-size:14px;">Password lama tidak diperlukan. Asisten akan otomatis logout dari sesi aktifnya.</p>
    <div class="form-group"><label class="form-label required">Password Baru</label><input type="password" name="password" class="form-control" required minlength="6"></div>
    <div class="form-group"><label class="form-label required">Konfirmasi Password</label><input type="password" name="password_confirmation" class="form-control" required minlength="6"></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalReset{{ $a->id }}" class="btn btn-outline">Batal</button><button class="btn btn-primary">Reset</button></div>
    </form></div>
</div></div>
@endforeach
@endsection
