@extends('layouts.app')
@section('title','Mahasiswa')
@section('page-title','Manajemen Mahasiswa')
@section('content')
<div class="page-toolbar"><button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Mahasiswa</button></div>
<div class="card">
    <div class="table-toolbar">
        <div class="table-search-wrap">
            <i class="ti ti-search" aria-hidden="true"></i>
            <input type="text" id="searchMahasiswa"
                   value="{{ $q }}"
                   class="table-search" placeholder="Cari NIM atau nama mahasiswa..."
                   autocomplete="off">
        </div>
        <span class="table-count" style="flex-shrink:0;">
            {{ $mahasiswaAll->total() }} mahasiswa
        </span>
    </div>
    <script>
    (function () {
        const input  = document.getElementById('searchMahasiswa');
        const base   = '{{ route('laboran.mahasiswa') }}';
        let timer;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                const q = input.value.trim();
                window.location.href = q ? base + '?q=' + encodeURIComponent(q) : base;
            }, 400);
        });
        // Fokus di akhir teks saat halaman load (ada query aktif)
        input.setSelectionRange(input.value.length, input.value.length);
        input.focus();
    })();
    </script>
    <div class="table-wrapper"><table class="table">
    <thead><tr><th>NIM</th><th>Nama Mahasiswa</th><th>Kelas yang Diikuti</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($mahasiswaAll as $m)
    @php $adaAlpa = $m->praktikum->contains(fn($p) => $m->melebihiBatasAlpaDiKelas($p->id)); @endphp
    <tr class="{{ $adaAlpa ? 'row-alpa-alert' : '' }}">
        <td style="font-family:monospace;font-size:13px;">{{ $m->nim_mahasiswa }}</td>
        <td>
            <div style="display:flex;align-items:center;gap:8px;">
                <div class="avatar avatar-sm">{{ $m->initials }}</div>
                <span class="fw-600">{{ $m->nama_mahasiswa }}</span>
                @if($adaAlpa)
                    <span class="badge-alpa-alert" title="Ada kelas dengan alpa ≥ {{ \App\Models\Mahasiswa::BATAS_ALPA }}">⚠ Alpa</span>
                @endif
            </div>
        </td>
        <td>
            {{-- Multi-badge: tampilkan semua kelas --}}
            @forelse($m->praktikum as $p)
                <span class="badge badge-primary" style="margin:2px 2px 2px 0;">
                    {{ $p->mataKuliah?->kode_mk }} — {{ $p->nama_kelas }}
                </span>
            @empty
                <span style="color:var(--text-muted);font-size:12px;">Belum ada kelas</span>
            @endforelse
        </td>
        <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                {{-- Tombol Nilai & Absensi per kelas --}}
                @forelse($m->praktikum as $p)
                    <a href="{{ route('laboran.mahasiswa.nilai', ['mahasiswa' => $m->id, 'praktikum' => $p->id]) }}"
                    class="btn btn-sm btn-primary">
                    {{ $p->nama_kelas }}
                    </a>
                @empty
                    <span class="btn btn-sm btn-outline" style="opacity:.5;cursor:not-allowed;" 
                        title="Tambahkan ke kelas dulu lewat menu Kelas Praktikum">
                        Nilai & Absensi
                    </span>
                @endforelse
                <button type="button" class="btn btn-sm btn-outline" data-modal-open="modalEditMhs{{ $m->id }}">Edit</button>
                <form method="POST" action="{{ route('laboran.mahasiswa.destroy', $m) }}" style="margin:0;">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus mahasiswa ini?')">Hapus</button>
                </form>
            </div>
        </td>
    </tr>
    </tr>
    @empty<tr><td colspan="5"><div class="empty-state"><p>Belum ada mahasiswa.</p></div></td></tr>
    @endforelse
    </tbody>
</table></div>
@if($mahasiswaAll->hasPages())
    <div class="card-footer">
        {{ $mahasiswaAll->appends(['q' => $q])->links() }}
    </div>
@endif
</div>
<div id="modalTambah" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Mahasiswa</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.mahasiswa.store') }}">@csrf
    <div class="form-group">
        <label class="form-label required">NIM</label>
        <input name="nim_mahasiswa" data-nim-input
            class="form-control {{ $errors->has('nim_mahasiswa') && old('_form') === 'tambah' ? 'is-invalid' : '' }}"
            placeholder="angka saja"
            pattern="\d+" inputmode="numeric"
            value="{{ old('_form') === 'tambah' ? old('nim_mahasiswa') : '' }}" required>
        @if(old('_form') === 'tambah') @error('nim_mahasiswa')<div class="form-error">{{ $message }}</div>@enderror @endif
    </div>
    <div class="form-group">
        <label class="form-label required">Nama</label>
        <input name="nama_mahasiswa" class="form-control {{ $errors->has('nama_mahasiswa') && old('_form') === 'tambah' ? 'is-invalid' : '' }}"
            value="{{ old('_form') === 'tambah' ? old('nama_mahasiswa') : '' }}" required>
        @if(old('_form') === 'tambah') @error('nama_mahasiswa')<div class="form-error">{{ $message }}</div>@enderror @endif
    </div>
    <p style="font-size:12px;color:var(--text-muted);margin:-4px 0 12px;">Kelas belum perlu dipilih sekarang — bisa ditentukan nanti lewat menu <strong>Kelas Praktikum → Edit</strong>.</p>
    <input type="hidden" name="_form" value="tambah">
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button><button class="btn btn-primary">Tambah</button></div>
    </form></div>
</div></div>
@foreach($mahasiswaAll as $m)
<div id="modalEditMhs{{ $m->id }}" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Edit Data — {{ $m->nama_mahasiswa }}</span><button data-modal-close="modalEditMhs{{ $m->id }}" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.mahasiswa.update',$m) }}">@csrf @method('PATCH')
    <div class="form-group">
        <label class="form-label required">NIM</label>
        <input name="nim_mahasiswa" data-nim-input
            class="form-control {{ $errors->has('nim_mahasiswa') && old('_form') === 'edit-mhs-'.$m->id ? 'is-invalid' : '' }}"
            placeholder="angka saja"
            pattern="\d+" inputmode="numeric"
            value="{{ old('_form') === 'edit-mhs-'.$m->id ? old('nim_mahasiswa') : $m->nim_mahasiswa }}" required>
        @if(old('_form') === 'edit-mhs-'.$m->id) @error('nim_mahasiswa')<div class="form-error">{{ $message }}</div>@enderror @endif
    </div>
    <div class="form-group">
        <label class="form-label required">Nama</label>
        <input name="nama_mahasiswa" class="form-control {{ $errors->has('nama_mahasiswa') && old('_form') === 'edit-mhs-'.$m->id ? 'is-invalid' : '' }}"
            value="{{ old('_form') === 'edit-mhs-'.$m->id ? old('nama_mahasiswa') : $m->nama_mahasiswa }}" required>
        @if(old('_form') === 'edit-mhs-'.$m->id) @error('nama_mahasiswa')<div class="form-error">{{ $message }}</div>@enderror @endif
    </div>
    <input type="hidden" name="_form" value="edit-mhs-{{ $m->id }}">
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalEditMhs{{ $m->id }}" class="btn btn-outline">Batal</button><button class="btn btn-primary">Simpan</button></div>
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
    else if (form.startsWith('edit-mhs-')) modalId = 'modalEditMhs' + form.replace('edit-mhs-', '');
    if (modalId) {
        document.getElementById(modalId)?.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
});
</script>
@endpush
@endif
@endsection
