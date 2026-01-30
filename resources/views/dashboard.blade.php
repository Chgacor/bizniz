<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('Operational Center') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-brand-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(auth()->user()->unreadNotifications->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-500 p-6">
                    <h3 class="text-lg font-bold text-red-700 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m9-9h.01"></path></svg>
                        Operational Alerts ({{ auth()->user()->unreadNotifications->count() }})
                    </h3>
                    <div class="mt-4 space-y-2">
                        @foreach(auth()->user()->unreadNotifications as $notification)
                            <div class="flex justify-between items-center bg-red-50 p-3 rounded">
                                <span class="text-red-800">{{ $notification->data['message'] }}</span>
                                <div class="flex space-x-2">
                                    <a href="{{ $notification->data['action_url'] }}" class="text-sm font-bold underline text-red-700">View Stock</a>
                                    <a href="{{ route('notifications.read', $notification->id) }}" class="text-sm text-gray-500 hover:text-gray-700">Dismiss</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-green-100 p-6 rounded-lg shadow-sm border border-green-200 flex items-center">
                    <span class="text-green-800 font-bold">✅ All systems normal. No operational alerts.</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="font-bold text-brand-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('pos.index') }}" class="block p-4 bg-brand-500 text-white text-center rounded hover:bg-brand-600 font-bold">Open POS</a>
                        <a href="{{ route('warehouse.index') }}" class="block p-4 bg-gray-800 text-white text-center rounded hover:bg-gray-900 font-bold">Stock In</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
