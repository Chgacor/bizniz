<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('System Configuration') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-brand-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <form action="{{ route('settings.update') }}" method="POST">
                @csrf
                <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-brand-200">

                    @foreach($settings as $group => $items)
                        <div class="px-4 py-5 sm:px-6 bg-brand-100 border-b border-brand-200">
                            <h3 class="text-lg leading-6 font-bold text-brand-900 uppercase tracking-wide">
                                {{ $group }} Settings
                            </h3>
                        </div>

                        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                            <dl class="sm:divide-y sm:divide-gray-200">
                                @foreach($items as $setting)
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-gray-50 transition">
                                        <dt class="text-sm font-medium text-gray-500 capitalize flex items-center">
                                            {{ str_replace('_', ' ', $setting->key) }}
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            <input type="text"
                                                   name="{{ $setting->key }}"
                                                   value="{{ $setting->value }}"
                                                   class="max-w-lg block w-full shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md">
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    @endforeach

                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 border-t border-gray-200">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-brand-900 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition">
                            Save Configuration
                        </button>
                    </div>
                </div>
            </form>

            <div class="bg-white shadow sm:rounded-lg border-l-4 border-brand-900">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Database Backup</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>Download a complete SQL copy of your business data (Customers, Inventory, Transactions). Save this file to an external drive or cloud storage weekly to prevent data loss.</p>
                    </div>
                    <div class="mt-5">
                        <a href="{{ route('settings.backup') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent font-medium rounded-md text-brand-900 bg-brand-100 hover:bg-brand-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 sm:text-sm transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download Backup (.sql)
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
