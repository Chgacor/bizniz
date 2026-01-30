<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-900 leading-tight">
            {{ __('Financial Reports') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-brand-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-brand-200">
                <div class="p-6 bg-white border-b border-brand-100">

                    <div class="flex items-center mb-6">
                        <div class="bg-green-100 p-3 rounded-full mr-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Export Sales Data</h3>
                            <p class="text-sm text-gray-500">Download transaction history for accounting and tax purposes.</p>
                        </div>
                    </div>

                    <form action="{{ route('reports.export') }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input type="date" name="start_date" id="start_date"
                                       value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                                       class="mt-1 focus:ring-brand-500 focus:border-brand-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                <input type="date" name="end_date" id="end_date"
                                       value="{{ now()->format('Y-m-d') }}"
                                       class="mt-1 focus:ring-brand-500 focus:border-brand-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-md mt-4 border border-gray-200">
                            <h4 class="text-sm font-bold text-gray-700 mb-2">Preset Ranges:</h4>
                            <div class="flex space-x-2">
                                <button type="button" onclick="setDateRange(0)" class="px-3 py-1 bg-white border border-gray-300 rounded text-xs hover:bg-gray-100">Today</button>
                                <button type="button" onclick="setDateRange(7)" class="px-3 py-1 bg-white border border-gray-300 rounded text-xs hover:bg-gray-100">Last 7 Days</button>
                                <button type="button" onclick="setDateRange(30)" class="px-3 py-1 bg-white border border-gray-300 rounded text-xs hover:bg-gray-100">Last 30 Days</button>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brand-900 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                                Download .CSV Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        function setDateRange(days) {
            const end = new Date();
            const start = new Date();
            start.setDate(end.getDate() - days);

            // Format to YYYY-MM-DD
            document.getElementById('end_date').value = end.toISOString().split('T')[0];
            document.getElementById('start_date').value = start.toISOString().split('T')[0];
        }
    </script>
</x-app-layout>
