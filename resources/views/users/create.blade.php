<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('Tambah Karyawan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-brand-50 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-brand-200 p-6">

                <form method="POST" action="{{ route('users.store') }}">
                    @csrf

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required autofocus />
                    </div>

                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700">Email (Untuk Login)</label>
                        <input type="email" name="email" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required />
                    </div>

                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700">Jabatan / Akses</label>
                        <select name="role" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            *Staff = Akses Kasir & Gudang. <br>
                            *Viewer = Akses Laporan saja (Akuntan). <br>
                            *Owner = Akses Penuh.
                        </p>
                    </div>

                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700">Password</label>
                        <input type="password" name="password" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required />
                    </div>

                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" required />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                        <button type="submit" class="bg-brand-900 hover:bg-black text-white font-bold py-2 px-4 rounded shadow">
                            Simpan Karyawan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
