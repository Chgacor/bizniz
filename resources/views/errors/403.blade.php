<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied - Bizniz.IO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#fff8f1] text-[#1c1917] font-sans antialiased h-screen flex flex-col justify-center items-center">

<div class="bg-white p-8 rounded-lg shadow-xl border border-[#fcd5c2] max-w-md text-center">
    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
        <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </div>

    <h1 class="text-3xl font-bold text-gray-900 mb-2">Access Denied</h1>
    <p class="text-gray-600 mb-6">
        Your role (<strong>{{ auth()->user()->getRoleNames()->first() }}</strong>) does not have permission to access this secure area. This event has been logged.
    </p>

    <a href="{{ url('/dashboard') }}" class="inline-block bg-[#fdba74] hover:bg-[#fb923c] text-white font-bold py-3 px-6 rounded transition duration-200">
        Return to Safety
    </a>
</div>

<div class="mt-8 text-xs text-gray-400">
    ERROR: 403_FORBIDDEN | IP: {{ request()->ip() }}
</div>
</body>
</html>
