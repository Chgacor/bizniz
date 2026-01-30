<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('Manajemen Staf') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-brand-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <p class="text-gray-600">Kelola akses masuk untuk karyawan Anda.</p>
                <a href="{{ route('users.create') }}" class="bg-brand-900 hover:bg-black text-white font-bold py-2 px-4 rounded shadow">
                    + Tambah Karyawan
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-brand-200">
                <table class="min-w-full divide-y divide-brand-200">
                    <thead class="bg-brand-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-brand-900 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-brand-900 uppercase tracking-wider">Email (Login)</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-brand-900 uppercase tracking-wider">Jabatan (Role)</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-brand-900 uppercase tracking-wider">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-brand-100">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-800">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $user->hasRole('Owner') ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $user->getRoleNames()->first() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akses karyawan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                Belum ada karyawan lain. Anda bekerja sendirian! 💪
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
