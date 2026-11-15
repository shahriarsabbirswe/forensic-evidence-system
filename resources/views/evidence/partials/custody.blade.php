@php
    $holder      = $evidence->currentHolder;
    $open        = $evidence->openTransfer();
    $canRequest  = auth()->user()->can('transfer.request') && (int) $evidence->current_holder_id === auth()->id();
    $recent      = $evidence->transfers()->with(['fromUser', 'toUser'])->limit(5)->get();
@endphp

<div class="mt-6 rounded-lg bg-white p-6 shadow-sm">
    <h3 class="mb-3 text-lg font-semibold text-gray-800">Custody</h3>

    @if (session('error'))
        <div class="mb-3 rounded-md border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <p class="text-sm text-gray-600">
        Currently held by
        <span class="font-semibold text-gray-900">{{ $holder->name ?? 'Not recorded' }}</span>
    </p>

    <div class="mt-3">
        @if ($open)
            <p class="text-sm text-amber-700">
                A transfer to {{ $open->toUser->name ?? '-' }} is in progress ({{ ucfirst($open->status) }}).
                <a href="{{ route('transfers.show', $open) }}" class="text-indigo-600 hover:underline">View</a>
            </p>
        @elseif ($canRequest)
            <a href="{{ route('transfers.create', $evidence) }}"
               class="inline-block rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Request Transfer
            </a>
        @endif
    </div>

    @if ($recent->isNotEmpty())
        <h4 class="mt-5 mb-2 text-sm font-semibold uppercase text-gray-500">Recent transfers</h4>
        <ul class="divide-y divide-gray-100 text-sm">
            @foreach ($recent as $t)
                <li class="flex items-center justify-between py-2">
                    <span>{{ $t->fromUser->name ?? '-' }} to {{ $t->toUser->name ?? '-' }}
                        <span class="text-gray-400">({{ ucfirst($t->status) }}, {{ $t->created_at->format('d M Y') }})</span>
                    </span>
                    <a href="{{ route('transfers.show', $t) }}" class="text-indigo-600 hover:underline">View</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
