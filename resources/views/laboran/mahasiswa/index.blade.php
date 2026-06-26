@extends('layouts.app')
@section('title','Mahasiswa')
@section('page-title','Manajemen Mahasiswa')
@section('content')
<div class="page-toolbar"><button class="btn btn-primary" data-modal-open="modalTambah">+ Tambah Mahasiswa</button></div>
<div class="card"><div class="table-wrapper"><table class="table">
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
                <a href="{{ route('laboran.mahasiswa.edit', $m) }}" class="btn btn-sm btn-outline">Edit</a>
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
@if($mahasiswaAll->hasPages())<div class="card-footer">{{ $mahasiswaAll->links() }}</div>@endif
</div>
<div id="modalTambah" class="modal-overlay"><div class="modal">
    <div class="modal-header"><span class="modal-title">Tambah Mahasiswa</span><button data-modal-close="modalTambah" class="modal-close">✕</button></div>
    <div class="modal-body"><form method="POST" action="{{ route('laboran.mahasiswa.store') }}">@csrf
    <div class="grid grid-2">
        <div class="form-group"><label class="form-label required">NIM</label><input name="nim_mahasiswa" class="form-control" required></div>
        <div class="form-group"><label class="form-label required">Nama</label><input name="nama_mahasiswa" class="form-control" required></div>
    </div>
    <p style="font-size:12px;color:var(--text-muted);margin:-4px 0 12px;">Kelas belum perlu dipilih sekarang — bisa ditentukan nanti lewat menu <strong>Kelas Praktikum → Edit</strong>.</p>
    <div style="display:flex;gap:8px;justify-content:flex-end;"><button type="button" data-modal-close="modalTambah" class="btn btn-outline">Batal</button><button class="btn btn-primary">Tambah</button></div>
    </form></div>
</div></div>
@endsection
