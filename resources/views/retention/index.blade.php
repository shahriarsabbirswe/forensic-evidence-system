<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Evidence Retention, Archival & Disposal Lifecycle
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Evidence</th>
                                <th class="px-4 py-3 text-left">Case</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Retention Deadline</th>
                                <th class="px-4 py-3 text-left">Lifecycle Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($evidences as $ev)
                                @php $status = $ev->lifecycle_status ?? 'active'; @endphp
                                <tr>
                                    <td class="px-4 py-3 font-medium">
                                        <a href="{{ route('evidence.show', $ev) }}" class="text-indigo-600 hover:underline">
                                            {{ $ev->evidence_number }}
                                        </a>
                                        <div class="text-xs text-gray-500 capitalize">{{ $ev->kind }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ $ev->investigationCase->case_number ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $status === 'disposed' ? 'bg-red-100 text-red-800' : ($status === 'archived' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                        @if($ev->disposal_reason)
                                            <div class="text-xs text-gray-500 mt-1">Reason: {{ $ev->disposal_reason }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('evidence.retention', $ev) }}" class="flex items-center gap-2">
                                            @csrf
                                            <input type="date" name="retention_until"
                                                   value="{{ $ev->retention_until ? \Carbon\Carbon::parse($ev->retention_until)->format('Y-m-d') : '' }}"
                                                   class="border-gray-300 rounded-md text-xs shadow-sm" required>
                                            <button type="submit" class="px-2.5 py-1.5 bg-slate-800 text-white rounded text-xs hover:bg-slate-700">
                                                Set
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($status !== 'disposed')
                                            <div class="flex flex-wrap items-center gap-2">
                                                @if($status !== 'archived')
                                                    <form method="POST" action="{{ route('evidence.archive', $ev) }}">
                                                        @csrf
                                                        <button type="submit" class="px-2.5 py-1.5 bg-amber-600 text-white rounded text-xs hover:bg-amber-700">
                                                            Archive
                                                        </button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('evidence.dispose', $ev) }}" class="flex items-center gap-1">
                                                    @csrf
                                                    <input type="text" name="disposal_reason" placeholder="Disposal reason"
                                                           class="border-gray-300 rounded-md text-xs w-36" required>
                                                    <button type="submit" class="px-2.5 py-1.5 bg-red-600 text-white rounded text-xs hover:bg-red-700">
                                                        Dispose
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">Disposed ({{ $ev->disposed_at ? \Carbon\Carbon::parse($ev->disposed_at)->format('d M Y') : '' }})</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">No evidence records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
