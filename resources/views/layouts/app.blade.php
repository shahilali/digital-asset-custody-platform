<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 text-slate-800 antialiased">
    <div class="min-h-screen">
        <nav class="bg-white/80 backdrop-blur border-b border-slate-200 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-6">
                        <a href="/" class="flex items-center gap-3 group">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-linear-to-br from-blue-600 to-indigo-600 text-white font-bold shadow-sm">DAC</span>
                            <span class="text-lg font-semibold text-slate-900 group-hover:text-slate-700">Digital Asset Custody</span>
                        </a>
                        <div class="hidden sm:flex sm:items-center sm:gap-1">

                            <a href="{{ route('metals.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('metals.index') ? 'bg-blue-100 text-blue-800' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Metals</a>

                        </div>
                    </div>
                    <div class="hidden sm:flex items-center gap-2">
                        <a href="#" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">New Deposit</a>
                        <a href="#" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">New Withdrawal</a>
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-10 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
