<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Search Cases and Evidence</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" action="{{ route('search.index') }}" class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-3">
                        <label class="block text-sm text-gray-600">Search text</label>
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Case number, evidence number, title, device, make/model, serial number"
                               class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600">Case</label>
                        <select name="case_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">All cases</option>
                            @foreach ($cases as $case)
                                <option value="{{ $case->id }}" @selected(request('case_id') == $case->id)>
                                    {{ $case->case_number }} - {{ $case->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600">Evidence status</label>
                        <select name="status" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Any</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600">Case status</label>
                        <select name="case_status" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Any</option>
                            @foreach ($caseStatuses as $status)
                                <option value="{{ $status }}" @selected(request('case_status') == $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600">Seized from</label>
                        <input type="date" name="from" value="{{ request('from') }}"
                               class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600">Seized to</label>
                        <input type="date" name="to" value="{{ request('to') }}"
                               class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600">Officer / assigned analyst</label>
                        <select name="officer_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Anyone</option>
                            @foreach ($officers as $officer)
                                <option value="{{ $officer->id }}" @selected(request('officer_id') == $officer->id)>
                                    {{ $officer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-sm text-gray-600">SHA-256 hash (full or partial)</label>
                        <input type="text" name="hash" value="{{ request('hash') }}"
                               placeholder="e.g. 9f86d081884c7d65..."
                               class="mt-1 w-full border-gray-300 rounded-md shadow-sm font-mono text-sm">
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-4">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">Search</button>
                    <a href="{{ route('search.index') }}" class="text-sm text-gray-600 underline">Clear filters</a>
                </div>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-x-auto">
                <p class="text-sm text-gray-500 mb-3">{{ $results->total() }} result(s)</p>
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-4">Evidence</th>
                            <th class="py-2 pr-4">Case</th>
                            <th class="py-2 pr-4">Device</th>
                            <th class="py-2 pr-4">Serial no.</th>
                            <th class="py-2 pr-4">Seized</th>
                            <th class="py-2 pr-4">Seizing officer</th>
                            <th class="py-2 pr-4">Status</th>
                            <th class="py-2 pr-4">Hash</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($results as $item)
                            <tr class="border-b">
                                <td class="py-2 pr-4">
                                    <a href="{{ url('/evidence/' . $item->id) }}" class="text-indigo-600 underline">
                                        {{ $item->evidence_number }}
                                    </a>
                                </td>
                                <td class="py-2 pr-4">
                                    <a href="{{ url('/cases/' . $item->investigation_case_id) }}" class="text-indigo-600 underline">
                                        {{ $caseNumbers[$item->investigation_case_id] ?? '-' }}
                                    </a>
                                </td>
                                <td class="py-2 pr-4">{{ $item->device_type }} {{ $item->make_model }}</td>
                                <td class="py-2 pr-4">{{ $item->serial_number ?? '-' }}</td>
                                <td class="py-2 pr-4">{{ $item->seized_at ? \Illuminate\Support\Carbon::parse($item->seized_at)->format('d M Y, H:i') : '-' }}</td>
                                <td class="py-2 pr-4">{{ $userNames[$item->seizing_officer_id] ?? '-' }}</td>
                                <td class="py-2 pr-4">{{ ucfirst($item->status ?? '-') }}</td>
                                <td class="py-2 pr-4 font-mono text-xs">
                                    {{ $item->sha256_hash ? substr($item->sha256_hash, 0, 12) . '...' : 'none' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-6 text-center text-gray-500">No results found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $results->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>