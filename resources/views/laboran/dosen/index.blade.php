@extends('layouts.app')
@section('title','Dosen')
@section('page-title','Manajemen Dosen')
@section('content')
<div class="page-toolbar"><button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Dosen</button></div>
<div class="card">
    <div class="table-toolbar">
        <div class="table-search-wrap">
            <i class="ti ti-search" aria-hidden="true"></i>
            <input type="text" id="searchDosen"
                   value="{{ $q }}"
                   class="table-search" placeholder="Cari nama, NIDN, username, atau kelas..."
                   autocomplete="off">
        </div>
        <span class="table-count" style="flex-shrink:0;">
            {{ $dosenAll->total() }} dosen
        </span>
    </div>
    <script>
    (function () {
        const input = document.getElementById('searchDosen');
        const base  = '{{ route('laboran.dosen') }}';
        let timer;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                const q      = input.value.trim();
                const params = new URLSearchParams();
                if (q)                          params.set('q', q);
                if ('{{ $sort }}')              params.set('sort', '{{ $sort }}');
                if ('{{ $dir }}' !== 'asc')     params.set('dir', '{{ $dir }}');
                window.location.href = params.toString() ? base + '?' + params.toString() : base;
            }, 400);
        });
        input.setSelectionRange(input.value.length, input.value.length);
    })();
    </script>
    @php
        $sortUrl = fn(string $kolom) => route('laboran.dosen', array_filter([
            'q'    => $q ?: null,
            'sort' => $kolom,
            'dir'  => ($sort === $kolom && $dir === 'asc') ? 'desc' : 'asc',
        ]));
        $isAktif = fn(string $kolom) => $sort === $kolom;
    @endphp
    <div class="table-wrapper"><table class="table">
    <thead><tr>
        <th style="cursor:pointer;user-select:none;white-space:nowrap;"
            class="{{ $isAktif('nama_dosen') ? ($dir === 'asc' ? 'sort-asc' : 'sort-desc') : '' }}"
            onclick="window.location='{{ $sortUrl('nama_dosen') }}'">
            Nama Dosen <span class="sort-icon">{{ $isAktif('nama_dosen') ? ($dir === 'asc' ? '↑' : '↓') : '⇅' }}</span>
        </th>
        <th style="cursor:pointer;user-select:none;white-space:nowrap;"
            class="{{ $isAktif('nidn') ? ($dir === 'asc' ? 'sort-asc' : 'sort-desc') : '' }}"
            onclick="window.location='{{ $sortUrl('nidn') }}'">
            NIDN <span class="sort-icon">{{ $isAktif('nidn') ? ($dir === 'asc' ? '↑' : '↓') : '⇅' }}</span>
        </th>
        <th>Kelas Diampu</th>
        <th>Username</th>
        <th>Aksi</th>
    </tr></thead>
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
                    <button type="button" class="dropdown-item" data-modal-open="modalEditDosen{{ $d->id }}">Edit Data</button>
                    <button type="button" class="dropdown-item" data-modal-open="modalReset{{ $d->id }}">Ganti Password</button>
                </div>
            </div>
            </div>
        </td>
    </tr>
    @empty
    <tr><td colspan="5"><div class="empty-state"><p>{{ $q ? 'Tidak ada dosen dengan nama, NIDN, atau kelas "'.$q.'".' : 'Belum ada dosen.' }}</p></div></td></tr>
    @endforelse
    </tbody>
    </table></div>

    {{-- Pagination --}}
    @if($dosenAll->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--border);">
        {{ $dosenAll->appends(['q' => $q, 'sort' => $sort, 'dir' => $dir])->links() }}
    </div>
    @endif
</div>

{{-- Modal Tambah Dosen --}}
<div id="modalTambah" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Dosen</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.dosen.store') }}">@csrf
    <div class="form-group">
        <label class="form-label required">Nama Dosen</label>
        <input name="nama_dosen" class="form-control {{ $errors->hasAny(['nama_dosen']) && !$errors->has('nidn') && old('_form') === 'tambah' ? 'is-invalid' : '' }}"
            value="{{ old('_form') === 'tambah' ? old('nama_dosen') : '' }}" required>
        @if(old('_form') === 'tambah') @error('nama_dosen')<div class="form-error">{{ $message }}</div>@enderror @endif
    </div>
    <div class="form-group">
        <label class="form-label">NIDN</label>
        <input name="nidn" data-nim-input
            class="form-control {{ $errors->has('nidn') && old('_form') === 'tambah' ? 'is-invalid' : '' }}"
            placeholder="opsional (angka saja)"
            pattern="\d*" inputmode="numeric"
            value="{{ old('_form') === 'tambah' ? old('nidn') : '' }}">
        @if(old('_form') === 'tambah') @error('nidn')<div class="form-error">{{ $message }}</div>@enderror @endif
    </div>
    <div class="form-group">
        <label class="form-label required">Username (untuk login)</label>
        <input name="username" class="form-control {{ $errors->has('username') && old('_form') === 'tambah' ? 'is-invalid' : '' }}"
            value="{{ old('_form') === 'tambah' ? old('username') : '' }}" required>
        @if(old('_form') === 'tambah') @error('username')<div class="form-error">{{ $message }}</div>@enderror @endif
    </div>
    <div class="form-group">
        <label class="form-label required">Password</label>
        <input type="password" name="password" class="form-control" required minlength="6">
    </div>
    <input type="hidden" name="_form" value="tambah">
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div></div>

{{-- Modal Edit & Reset per Dosen --}}
@foreach($dosenAll as $d)
<div id="modalEditDosen{{ $d->id }}" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Edit Data — {{ $d->nama_dosen }}</span><button data-modal-close="modalEditDosen{{ $d->id }}" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.dosen.update',$d) }}">@csrf @method('PATCH')
    <div class="form-group">
        <label class="form-label required">Nama Dosen</label>
        <input name="nama_dosen" class="form-control {{ $errors->has('nama_dosen') && old('_form') === 'edit-dosen-'.$d->id ? 'is-invalid' : '' }}"
            value="{{ old('_form') === 'edit-dosen-'.$d->id ? old('nama_dosen') : $d->nama_dosen }}" required>
        @if(old('_form') === 'edit-dosen-'.$d->id) @error('nama_dosen')<div class="form-error">{{ $message }}</div>@enderror @endif
    </div>
    <div class="form-group">
        <label class="form-label">NIDN</label>
        <input name="nidn" data-nim-input
            class="form-control {{ $errors->has('nidn') && old('_form') === 'edit-dosen-'.$d->id ? 'is-invalid' : '' }}"
            placeholder="opsional (angka saja)"
            pattern="\d*" inputmode="numeric"
            value="{{ old('_form') === 'edit-dosen-'.$d->id ? old('nidn') : $d->nidn }}">
        @if(old('_form') === 'edit-dosen-'.$d->id) @error('nidn')<div class="form-error">{{ $message }}</div>@enderror @endif
    </div>
    <input type="hidden" name="_form" value="edit-dosen-{{ $d->id }}">
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalEditDosen{{ $d->id }}" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form></div>
</div></div>
@endforeach
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
@if($errors->any() && old('_form'))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = '{{ old("_form") }}';
    let modalId = null;
    if (form === 'tambah') modalId = 'modalTambah';
    else if (form.startsWith('edit-dosen-')) modalId = 'modalEditDosen' + form.replace('edit-dosen-', '');
    if (modalId) {
        document.getElementById(modalId)?.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
});
</script>
@endpush
@endif
@endsection
