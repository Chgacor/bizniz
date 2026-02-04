<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Bizniz.IO') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
<div class="min-h-screen">
    @include('layouts.navigation')

    @isset($header)
        <header class="bg-white shadow-sm border-b border-gray-100">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main>
        {{ $slot }}
    </main>
</div>

<script>
    // LOGIC SWEETALERT GLOBAL (Dipanggil saat halaman loading)
    // Menangani pesan sukses/error dari Controller (Session Flash)
    document.addEventListener('DOMContentLoaded', function() {

        // Cek Session 'success'
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#fb923c', // Soft Orange (Brand Color)
            iconColor: '#fb923c',
            timer: 3000
        });
        @endif

        // Cek Session 'error'
        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') }}",
            confirmButtonColor: '#ef4444', // Red
        });
        @endif

        // Cek Validasi Error (Misal input kosong)
        @if($errors->any())
        let errorList = '';
        @foreach($errors->all() as $error)
            errorList += '<li>- {{ $error }}</li>';
        @endforeach

        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            html: '<ul style="text-align: left;">' + errorList + '</ul>',
            confirmButtonColor: '#fb923c',
        });
        @endif
    });
</script>
</body>
</html>
