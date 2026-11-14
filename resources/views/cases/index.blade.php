<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Investigation Cases
            </h2>
            @can('case.create')
                <a href="{{ route('cases.create') }}"
                   class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                    New Case
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Search and filter --}}
            <form method="GET" action="{{ route('cases.index') }}"
                  class="mb-4 flex flex-wrap gap-3 rounded-lg bg-white p-4 shadow-sm">
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Case number, title or crime type"
                       class="w-72 rounded-md border-gray-300 text-sm shadow-sm">

                <select name="status" class="rounded-md border-gray-300 text-sm shadow-sm">
                    <option value="">All statuses</option>
                    @foreach (['open','under_investigation','closed','archived'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>
                            {{ ucfirst(str_replace('_',' ',$s)) }}
                        </option>
                    @endforeach
                </select>

                <button class="rounded-md bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-700">
                    Filter
                </button>

                @if (request()->hasAny(['q','status']))
                    <a href="{{ route('cases.index') }}"
                       class="px-3 py-2 text-sm text-gray-600 underline">Clear</a>
                @endif
            </form>

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Case No.</th>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Crime Type</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Evidence</th>
                            <th class="px-4 py-3">Deadline (s.32)</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($cases as $case)
                            @php $days = $case->daysRemaining(); @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-xs">{{ $case->case_number }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $case->title }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $case->crime_type }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs
                                        @class([
                                            'bg-blue-100 text-blue-800'   => $case->status === 'open',
                                            'bg-amber-100 text-amber-800' => $case->status === 'under_investigation',
                                            'bg-gray-200 text-gray-700'   => $case->status === 'closed',
                                            'bg-gray-100 text-gray-500'   => $case->status === 'archived',
                                        ])">
                                        {{ ucfirst(str_replace('_',' ',$case->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $case->evidence_count }}</td>
                                <td class="px-4 py-3">
                                    @if ($days === null)
                                        <span class="text-gray-400">Not set</span>
                                    @elseif ($days < 0)
                                        <span class="font-medium text-red-600">{{ abs($days) }} days overdue</span>
                                    @elseif ($days <= 14)
                                        <span class="font-medium text-amber-600">{{ $days }} days left</span>
                                    @else
                                        <span class="text-gray-600">{{ $days }} days left</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('cases.show', $case) }}"
                                       class="text-indigo-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                    No cases found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $cases->links() }}</div>
        </div>
    </div>
</x-app-layout>
