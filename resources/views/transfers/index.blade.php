<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Evidence Transfers</h2>
    </x-slot>

    @php
        $badge = [
            'requested' => 'bg-amber-100 text-amber-800',
            'approved'  => 'bg-blue-100 text-blue-800',
            'released'  => 'bg-indigo-100 text-indigo-800',
            'completed' => 'bg-green-100 text-green-800',
            'rejected'  => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-200 text-gray-700',
        ];
    @endphp

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @include('transfers.partials.flash')

            <form method="GET" class="mb-4 flex items-center gap-3">
                <label for="status" class="text-sm text-gray-600">Status</label>
                <select id="status" name="status" class="rounded-md border-gray-300 text-sm">
                    <option value="">All</option>
                    @foreach (['requested', 'approved', 'released', 'completed', 'rejected', 'cancelled'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button class="rounded-md bg-gray-800 px-3 py-2 text-sm text-white">Filter</button>
            </form>

            <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Evidence</th>
                            <th class="px-4 py-3">From</th>
                            <th class="px-4 py-3">To</th>
                            <th class="px-4 py-3">Purpose</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Requested</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($transfers as $t)
                            <tr>
                                <td class="px-4 py-3"><x-hash :value="$t->evidence->evidence_number ?? null" short /></td>                                <td class="px-4 py-3">{{ $t->fromUser->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $t->toUser->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($t->purpose, 40) }}</td>
                                <td class="px-4 py-3">
                                    <x-status-badge :status="$t->status" />
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $t->created_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('transfers.show', $t) }}" class="text-indigo-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">No transfers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $transfers->links() }}</div>
        </div>
    </div>
</x-app-layout>
