<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Asistio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    {{-- Favicon dari file image --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    @stack('styles')
</head>
<body>

{{-- Struktur: sidebar kiri | (header atas + konten tengah + footer bawah) --}}
<div class="app-shell">

    {{-- Sidebar — partial terpisah --}}
    @include('layouts.partials.sidebar')

    {{-- Wrapper kanan: header di atas, konten di tengah, footer di bawah --}}
    <div class="main-wrapper">

        {{-- Header / Topbar — partial terpisah, posisi di ATAS --}}
        @include('layouts.partials.header')

        {{-- Flash messages --}}
        <div class="flash-container">
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>{{ session('success') }}</span>
                    <button class="alert-close" type="button">✕</button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>{{ session('error') }}</span>
                    <button class="alert-close" type="button">✕</button>
                </div>
            @endif
        </div>

        {{-- Konten Halaman --}}
        <main class="page-content" role="main">
            @yield('content')
        </main>

        {{-- Footer — partial terpisah, posisi di BAWAH --}}
        @include('layouts.partials.footer')

    </div>{{-- /main-wrapper --}}

</div>{{-- /app-shell --}}

<script src="{{ asset('js/app.js') }}" defer></script>
@stack('scripts')
</body>
</html>
