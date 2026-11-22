<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-slate-800">Dashboard</h2>
    </x-slot>

    @php
        $hasTransfers = \Illuminate\Support\Facades\Schema::hasTable('evidence_transfers');

        $activeCases = \App\Models\InvestigationCase::whereIn('status', ['open', 'under_investigation'])->count();
        $evidenceCount = \App\Models\Evidence::count();
        $pendingTransfers = $hasTransfers
            ? \App\Models\EvidenceTransfer::whereIn('status', \App\Models\EvidenceTransfer::IN_FLIGHT)->count()
            : 0;
        // Preservation dates come from the evidence table (preservation_expires_at).
        $expiringSoon = \App\Models\Evidence::whereNotNull('preservation_expires_at')
            ->whereBetween('preservation_expires_at', [now()->toDateString(), now()->addDays(15)->toDateString()])
            ->count();

        $recent = ($hasTransfers && auth()->user()->can('transfer.view_all'))
            ? \App\Models\EvidenceTransfer::with(['evidence', 'fromUser', 'toUser'])->latest()->limit(5)->get()
            : collect();
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

            <p class="px-4 text-sm text-slate-500 sm:px-0">
                Welcome, {{ auth()->user()->name }}
                @if (auth()->user()->getRoleNames()->isNotEmpty())
                    <span class="ml-2 rounded bg-slate-200 px-2 py-0.5 text-xs font-medium text-slate-700">
                        {{ auth()->user()->getRoleNames()->first() }}
                    </span>
                @endif
            </p>

            {{-- KPI cards --}}
            <div class="grid grid-cols-2 gap-4 px-4 sm:px-0 lg:grid-cols-4">
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="text-xs font-medium uppercase text-slate-500">Active cases</div>
                    <div class="mt-2 text-3xl font-semibold text-slate-900">{{ $activeCases }}</div>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="text-xs font-medium uppercase text-slate-500">Registered evidence</div>
                    <div class="mt-2 text-3xl font-semibold text-slate-900">{{ $evidenceCount }}</div>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="text-xs font-medium uppercase text-slate-500">Transfers in progress</div>
                    <div class="mt-2 text-3xl font-semibold {{ $pendingTransfers > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $pendingTransfers }}</div>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="text-xs font-medium uppercase text-slate-500">Preservation expiring, 15 days</div>
                    <div class="mt-2 text-3xl font-semibold {{ $expiringSoon > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $expiringSoon }}</div>
                </div>
            </div>

            {{-- Quick links --}}
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('cases.index') }}"
                       class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Go to Cases</a>

                    @can('case.create')
                        <a href="{{ route('cases.create') }}"
                           class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">New Case</a>
                    @endcan

                    <a href="{{ route('search.index') }}"
                       class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Search</a>

                    @can('transfer.view')
                        <a href="{{ route('transfers.index') }}"
                           class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Transfers</a>
<a href="{{ route('retention.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('retention.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Retention & Lifecycle</a>
                    @endcan
                </div>
            </div>

            {{-- Recent transfers (supervisor, admin, prosecutor) --}}
            @if ($recent->isNotEmpty())
                <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700">Recent transfers</div>
                    <ul class="divide-y divide-slate-100 text-sm">
                        @foreach ($recent as $t)
                            <li class="flex items-center justify-between px-6 py-3">
                                <span>
                                    <span class="font-mono text-xs">{{ $t->evidence->evidence_number ?? '-' }}</span>
                                    <span class="text-slate-500">{{ $t->fromUser->name ?? '-' }} to {{ $t->toUser->name ?? '-' }}</span>
                                </span>
                                <span class="flex items-center gap-3">
                                    <x-status-badge :status="$t->status" />
                                    <a href="{{ route('transfers.show', $t) }}" class="text-indigo-600 hover:underline">View</a>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
