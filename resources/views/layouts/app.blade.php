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
        /* Custom Scrollbar untuk History Kalkulator */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #1f2937; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #6b7280; }

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

<div x-data="globalCalculator()" class="fixed bottom-6 right-6 z-50 flex flex-col items-end font-sans">

    <div x-show="open"
         style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="bg-gray-900 p-4 rounded-2xl shadow-2xl mb-4 w-72 border border-gray-700 ring-1 ring-white/10">

        <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-800">
            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">History</span>
            <button @click="logs = []" x-show="logs.length > 0" class="text-[10px] text-red-400 hover:text-red-300 transition">Clear</button>
        </div>

        <div class="mb-3 max-h-32 overflow-y-auto flex flex-col-reverse space-y-1 space-y-reverse pr-1 custom-scrollbar">
            <template x-for="(log, index) in logs" :key="index">
                <div class="flex justify-between text-xs group hover:bg-gray-800 p-1 rounded transition select-text cursor-default">
                    <span class="text-gray-400" x-text="log.exp"></span>
                    <span class="font-bold text-brand-400" x-text="'= ' + log.res"></span>
                </div>
            </template>
            <div x-show="logs.length === 0" class="text-center py-6 text-gray-700 text-xs italic">
                Belum ada riwayat
            </div>
        </div>

        <div class="bg-gray-100 rounded-xl p-3 mb-3 text-right border-4 border-gray-200 shadow-inner">
            <div class="text-xs text-gray-400 h-4 font-mono overflow-hidden" x-text="history"></div>
            <div class="text-3xl font-mono font-bold text-gray-900 overflow-hidden truncate" x-text="display">0</div>
        </div>

        <div class="grid grid-cols-4 gap-2">
            <button @click="clear()" class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-sm">C</button>
            <button @click="backspace()" class="bg-gray-700 hover:bg-gray-600 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-sm">⌫</button>
            <button @click="appendOperator('%')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-sm">%</button>
            <button @click="appendOperator('/')" class="bg-brand-500 hover:bg-brand-600 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-xl">÷</button>

            <button @click="appendNumber('7')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-lg">7</button>
            <button @click="appendNumber('8')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-lg">8</button>
            <button @click="appendNumber('9')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-lg">9</button>
            <button @click="appendOperator('*')" class="bg-brand-500 hover:bg-brand-600 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-xl">×</button>

            <button @click="appendNumber('4')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-lg">4</button>
            <button @click="appendNumber('5')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-lg">5</button>
            <button @click="appendNumber('6')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-lg">6</button>
            <button @click="appendOperator('-')" class="bg-brand-500 hover:bg-brand-600 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-xl">-</button>

            <button @click="appendNumber('1')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-lg">1</button>
            <button @click="appendNumber('2')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-lg">2</button>
            <button @click="appendNumber('3')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-lg">3</button>
            <button @click="appendOperator('+')" class="bg-brand-500 hover:bg-brand-600 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-xl">+</button>

            <button @click="appendNumber('0')" class="col-span-2 bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-lg">0</button>
            <button @click="appendNumber('.')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-lg">.</button>
            <button @click="calculate()" class="bg-green-500 hover:bg-green-600 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition text-xl">=</button>
        </div>
    </div>

    <button @click="open = !open"
            class="bg-brand-600 hover:bg-brand-700 text-white p-4 rounded-full shadow-xl border-4 border-brand-200 transition-all transform hover:scale-110 hover:rotate-3 flex items-center justify-center focus:outline-none">
        <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
        <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>

<script>
    // 1. LOGIC CALCULATOR (ALPINE.JS)
    function globalCalculator() {
        return {
            open: false,
            display: '0',
            history: '',
            resetNext: false,
            logs: [],

            appendNumber(num) {
                if (this.display === '0' || this.resetNext) {
                    this.display = num;
                    this.resetNext = false;
                } else {
                    if (this.display.length < 12) this.display += num;
                }
            },

            appendOperator(op) {
                this.history = this.display + ' ' + op;
                this.display = '0';
            },

            backspace() {
                this.display = this.display.slice(0, -1);
                if (this.display === '') this.display = '0';
            },

            clear() {
                this.display = '0';
                this.history = '';
            },

            calculate() {
                try {
                    let fullExpression = this.history + this.display;
                    if (!this.history) return;

                    // Hitung dengan eval (Hati-hati, tapi aman utk kalkulator klien side sederhana)
                    let result = eval(fullExpression);
                    result = parseFloat(result.toFixed(4)).toString();

                    this.logs.unshift({ exp: fullExpression, res: result });
                    if (this.logs.length > 20) this.logs.pop();

                    this.display = result;
                    this.history = '';
                    this.resetNext = true;
                } catch (e) {
                    this.display = 'Error';
                    this.resetNext = true;
                }
            }
        }
    }

    // 2. LOGIC SWEETALERT GLOBAL (Dipanggil saat halaman loading)
    // Ini untuk menangkap pesan dari Controller (Session Flash)
    document.addEventListener('DOMContentLoaded', function() {

        // Konfigurasi Toast Default
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Cek Session 'success'
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#fb923c', // Soft Orange
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
