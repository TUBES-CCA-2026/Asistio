@extends('layouts.app')
@section('title','Ganti Password')
@section('page-title','Ganti Password')
@section('content')
<div class="card" style="max-width:480px;">
    <div class="card-body" style="padding:24px;">
        <form method="POST" action="{{ route('laboran.ganti-password.update') }}">
            @csrf

            <div class="form-group">
                <label class="form-label required" for="password_lama">Password Lama</label>
                <input type="password" id="password_lama" name="password_lama"
                    class="form-control {{ $errors->has('password_lama') ? 'is-invalid' : '' }}"
                    required autocomplete="current-password">
                @error('password_lama')<p class="invalid-feedback">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label required" for="password_baru">Password Baru</label>
                <input type="password" id="password_baru" name="password_baru"
                    class="form-control {{ $errors->has('password_baru') ? 'is-invalid' : '' }}"
                    required minlength="6" autocomplete="new-password">
                @error('password_baru')<p class="invalid-feedback">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label required" for="password_baru_confirmation">Konfirmasi Password Baru</label>
                <input type="password" id="password_baru_confirmation" name="password_baru_confirmation"
                    class="form-control" required minlength="6" autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Simpan Password Baru</button>
        </form>
    </div>
</div>
@endsection