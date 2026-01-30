<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <div x-data="globalCalculator()" class="fixed bottom-4 right-4 z-50 flex flex-col items-end font-sans">

            <div x-show="open"
                 style="display: none;"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="bg-gray-900 p-4 rounded-xl shadow-2xl mb-4 w-72 border border-gray-700">

                <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-700">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">History</span>
                    <button @click="logs = []" x-show="logs.length > 0" class="text-xs text-red-400 hover:text-red-300">Hapus Semua</button>
                </div>

                <div class="mb-3 max-h-32 overflow-y-auto flex flex-col-reverse space-y-1 space-y-reverse pr-1 custom-scrollbar">
                    <template x-for="(log, index) in logs" :key="index">
                        <div class="flex justify-between text-xs group hover:bg-gray-800 p-1 rounded transition">
                            <span class="text-gray-400" x-text="log.exp"></span>
                            <span class="font-bold text-brand-300" x-text="'= ' + log.res"></span>
                        </div>
                    </template>
                    <div x-show="logs.length === 0" class="text-center py-4 text-gray-600 text-xs italic">
                        Belum ada riwayat hitungan
                    </div>
                </div>

                <div class="bg-gray-200 rounded-lg p-3 mb-3 text-right border-4 border-gray-300">
                    <div class="text-xs text-gray-500 h-4 font-mono" x-text="history"></div>
                    <div class="text-3xl font-mono font-bold text-gray-900 overflow-hidden truncate" x-text="display">0</div>
                </div>

                <div class="grid grid-cols-4 gap-2">
                    <button @click="clear()" class="bg-red-600 hover:bg-red-500 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">C</button>
                    <button @click="backspace()" class="bg-gray-600 hover:bg-gray-500 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">⌫</button>
                    <button @click="appendOperator('%')" class="bg-gray-700 hover:bg-gray-600 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">%</button>
                    <button @click="appendOperator('/')" class="bg-brand-600 hover:bg-brand-500 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">÷</button>

                    <button @click="appendNumber('7')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">7</button>
                    <button @click="appendNumber('8')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">8</button>
                    <button @click="appendNumber('9')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">9</button>
                    <button @click="appendOperator('*')" class="bg-brand-600 hover:bg-brand-500 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">×</button>

                    <button @click="appendNumber('4')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">4</button>
                    <button @click="appendNumber('5')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">5</button>
                    <button @click="appendNumber('6')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">6</button>
                    <button @click="appendOperator('-')" class="bg-brand-600 hover:bg-brand-500 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">-</button>

                    <button @click="appendNumber('1')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">1</button>
                    <button @click="appendNumber('2')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">2</button>
                    <button @click="appendNumber('3')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">3</button>
                    <button @click="appendOperator('+')" class="bg-brand-600 hover:bg-brand-500 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">+</button>

                    <button @click="appendNumber('0')" class="col-span-2 bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">0</button>
                    <button @click="appendNumber('.')" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">.</button>
                    <button @click="calculate()" class="bg-green-500 hover:bg-green-400 text-white p-3 rounded-lg font-bold shadow active:scale-95 transition">=</button>
                </div>
            </div>

            <button @click="open = !open"
                    class="bg-brand-900 hover:bg-black text-white p-4 rounded-full shadow-lg border-4 border-brand-500 transition transform hover:scale-110 flex items-center justify-center focus:outline-none">
                <svg x-show="!open" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                <svg x-show="open" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <script>
            function globalCalculator() {
                return {
                    open: false,
                    display: '0',
                    history: '',
                    resetNext: false,
                    logs: [], // Array untuk menyimpan history

                    appendNumber(num) {
                        if (this.display === '0' || this.resetNext) {
                            this.display = num;
                            this.resetNext = false;
                        } else {
                            // Batasi panjang angka agar tidak merusak layout
                            if (this.display.length < 12) {
                                this.display += num;
                            }
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
                            // Hindari eval kosong
                            if (!this.history) return;

                            // Ganti operator visual dengan operator JS jika perlu (misal % jadi /100)
                            // Tapi untuk sederhana kita pakai eval standard
                            let result = eval(fullExpression);

                            // Format hasil (hilangkan desimal panjang)
                            result = parseFloat(result.toFixed(4)).toString();

                            // Simpan ke Log History (Paling atas = paling baru)
                            this.logs.unshift({
                                exp: fullExpression,
                                res: result
                            });

                            // Batasi history maksimal 20 baris
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
        </script>
    </body>
</html>
