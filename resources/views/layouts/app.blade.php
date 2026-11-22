<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DFEMS') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 font-sans antialiased text-slate-800">
        @php
            $user = auth()->user();
            $role = $user->getRoleNames()->first();

            // Only links whose route exists and that the user may use are shown.
            // When a teammate adds a route (for example the custody ledger or the audit log),
            // add one line here with the route name and a permission name.
            $links = [
                ['group' => 'Overview',      'label' => 'Dashboard',         'route' => 'dashboard',       'active' => 'dashboard',  'can' => null],
                ['group' => 'Investigation', 'label' => 'Cases',             'route' => 'cases.index',     'active' => 'cases.*',    'can' => 'case.view'],
                ['group' => 'Investigation', 'label' => 'Search',            'route' => 'search.index',    'active' => 'search.*',   'can' => null],
                ['group' => 'Investigation', 'label' => 'Custody transfers', 'route' => 'transfers.index', 'active' => 'transfers.*', 'can' => 'transfer.view'],
                ['group' => 'Investigation', 'label' => 'Retention & Lifecycle', 'route' => 'retention.index', 'active' => 'retention.*', 'can' => null],
                ['group' => 'Account',       'label' => 'Profile',           'route' => 'profile.edit',    'active' => 'profile.*',  'can' => null],
            ];

            $groups = collect($links)
                ->filter(fn ($l) => \Illuminate\Support\Facades\Route::has($l['route']) && (! $l['can'] || $user->can($l['can'])))
                ->groupBy('group');
        @endphp

        <div x-data="{ open: false }" class="min-h-screen lg:flex">

            {{-- Mobile backdrop --}}
            <div x-show="open" x-on:click="open = false" class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"></div>

            {{-- Sidebar --}}
            <aside class="fixed inset-y-0 left-0 z-40 w-64 transform bg-slate-900 text-slate-300 transition-transform lg:static lg:translate-x-0"
                   x-bind:class="open ? 'translate-x-0' : '-translate-x-full'">
                <div class="flex h-16 items-center border-b border-slate-800 px-5">
                    <div>
                        <div class="text-base font-semibold tracking-wide text-white">DFEMS</div>
                        <div class="text-[11px] text-slate-400">Digital Forensic Evidence System</div>
                    </div>
                </div>

                <nav class="space-y-5 px-3 py-5 text-sm">
                    @foreach ($groups as $groupName => $items)
                        <div>
                            <div class="mb-2 px-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ $groupName }}</div>
                            <ul class="space-y-1">
                                @foreach ($items as $item)
                                    @php $active = request()->routeIs($item['active']); @endphp
                                    <li>
                                        <a href="{{ route($item['route']) }}"
                                           class="block rounded-md px-3 py-2 {{ $active ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                                            {{ $item['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </nav>
            </aside>

            {{-- Main column --}}
            <div class="flex min-w-0 flex-1 flex-col">
                {{-- Top bar --}}
                <header class="flex h-16 items-center gap-3 border-b border-slate-200 bg-white px-4 sm:px-6">
                    <button type="button" class="rounded p-2 text-slate-600 hover:bg-slate-100 lg:hidden" x-on:click="open = true" aria-label="Open menu">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    @if (\Illuminate\Support\Facades\Route::has('search.index'))
                        {{-- Check the real query name in SearchController (it may not be "q"). --}}
                        <form method="GET" action="{{ route('search.index') }}" class="hidden max-w-md flex-1 sm:block">
                            <input type="text" name="q" value="{{ request('q') }}"
                                   placeholder="Search case, EV number or SHA-256 hash"
                                   class="w-full rounded-md border-slate-300 text-sm">
                        </form>
                    @endif

                    <div class="ml-auto flex items-center gap-3 text-sm">
                        <div class="text-right leading-tight">
                            <div class="font-medium text-slate-800">{{ $user->name }}</div>
                            @if ($role)
                                <div class="text-xs text-slate-500">{{ $role }}</div>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="rounded-md border border-slate-300 px-3 py-1.5 text-slate-700 hover:bg-slate-100">Log out</button>
                        </form>
                    </div>
                </header>

                @isset($header)
                    <div class="border-b border-slate-200 bg-white">
                        <div class="px-4 py-5 sm:px-6">{{ $header }}</div>
                    </div>
                @endisset

                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
