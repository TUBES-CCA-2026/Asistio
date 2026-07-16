@extends('layouts.app')
@section('title','Asisten')
@section('page-title','Manajemen Asisten')
@section('content')
<div class="page-toolbar"><button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Asisten</button></div>
<div class="card">
    {{-- Search server-side --}}
    <div class="table-toolbar">
        <div class="table-search-wrap">
            <i class="ti ti-search" aria-hidden="true"></i>
            <input type="text" id="searchAsisten"
                   value="{{ $q }}"
                   class="table-search" placeholder="Cari nama atau NIM asisten..."
                   autocomplete="off">
        </div>
        <span class="table-count" style="flex-shrink:0;">
            {{ $asistenAll->total() }} asisten
        </span>
    </div>
    <script>
    (function () {
        const input = document.getElementById('searchAsisten');
        const base  = '{{ route('laboran.asisten') }}';
        let timer;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                const q      = input.value.trim();
                const params = new URLSearchParams();
                if (q)                              params.set('q', q);
                if ('{{ $sort }}')                  params.set('sort', '{{ $sort }}');
                if ('{{ $dir }}' !== 'asc')         params.set('dir', '{{ $dir }}');
                window.location.href = params.toString() ? base + '?' + params.toString() : base;
            }, 400);
        });
        input.setSelectionRange(input.value.length, input.value.length);
        input.focus();
    })();
    </script>

    <div class="table-wrapper"><table class="table">
    @php
        $sortUrl = fn(string $kolom) => route('laboran.asisten', array_filter([
            'q'    => $q ?: null,
            'sort' => $kolom,
            'dir'  => ($sort === $kolom && $dir === 'asc') ? 'desc' : 'asc',
        ]));
        $isAktif = fn(string $kolom) => $sort === $kolom;
    @endphp
    <thead><tr>
        <th data-col="nama"
            class="{{ $isAktif('nama_asisten') ? ($dir === 'asc' ? 'sort-asc' : 'sort-desc') : '' }}"
            style="cursor:pointer;user-select:none;white-space:nowrap;"
            onclick="window.location='{{ $sortUrl('nama_asisten') }}'">
            Nama Asisten <span class="sort-icon" aria-hidden="true">{{ $isAktif('nama_asisten') ? ($dir === 'asc' ? '↑' : '↓') : '⇅' }}</span>
        </th>
        <th data-col="nim"
            class="{{ $isAktif('nim') ? ($dir === 'asc' ? 'sort-asc' : 'sort-desc') : '' }}"
            style="cursor:pointer;user-select:none;white-space:nowrap;"
            onclick="window.location='{{ $sortUrl('nim') }}'">
            NIM <span class="sort-icon" aria-hidden="true">{{ $isAktif('nim') ? ($dir === 'asc' ? '↑' : '↓') : '⇅' }}</span>
        </th>
        <th>Kelas Didampingi</th>
        <th>Username</th>
        <th>Aksi</th>
    </tr></thead>
    <tbody>
    @forelse($asistenAll as $a)
    <tr>
        <td><div style="display:flex;align-items:center;gap:8px;"><div class="avatar avatar-sm">{{ strtoupper(substr($a->nama_asisten,0,2)) }}</div><span class="fw-600">{{ $a->nama_asisten }}</span></div></td>
        <td style="font-family:monospace;">{{ $a->nim ?? '—' }}</td>
        @php
            $kelasDampingi = $a->praktikum->merge($a->praktikumSebagaiAsisten2)->unique('id')->sortBy('nama_kelas');
        @endphp
        <td class="fs-12">
            @forelse($kelasDampingi as $p)
                <span style="display:block;">
                    {{ $p->mataKuliah?->kode_mk }} — {{ $p->nama_kelas }}
                    <span style="color:var(--text-muted);">({{ $p->asisten_id == $a->id ? 'A1' : 'A2' }})</span>
                </span>
            @empty —
            @endforelse
        </td>
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
    @empty
    <tr><td colspan="5"><div class="empty-state"><p>{{ $q ? 'Tidak ada asisten dengan nama atau NIM "'.$q.'".' : 'Belum ada asisten.' }}</p></div></td></tr>
    @endforelse
    </tbody>
    </table></div>

    {{-- Pagination --}}
    @if($asistenAll->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--border);">
        {{ $asistenAll->links() }}
    </div>
    @endif
</div>

{{-- Modal Tambah --}}
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

{{-- Modal Edit & Reset — hanya untuk asisten di halaman ini --}}
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