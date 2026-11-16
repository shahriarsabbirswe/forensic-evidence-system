<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Transfer of {{ $transfer->evidence->evidence_number ?? 'evidence' }}
        </h2>
    </x-slot>

    @php
        $user       = auth()->user();
        $uid        = $user->id;
        $isFrom     = $uid === (int) $transfer->from_user_id;
        $isTo       = $uid === (int) $transfer->to_user_id;
        $isRequester = $uid === (int) $transfer->requested_by;
        $canApprove = $user->can('transfer.approve') && ! $isFrom && ! $isTo;
        $current    = $transfer->step();
        $steps = [
            1 => ['Requested', optional($transfer->requester)->name, $transfer->created_at],
            2 => ['Approved', optional($transfer->approver)->name, $transfer->approved_at],
            3 => ['Released', optional($transfer->fromUser)->name, $transfer->released_at],
            4 => ['Received', optional($transfer->toUser)->name, $transfer->received_at],
        ];
    @endphp

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('transfers.partials.flash')

            {{-- Progress --}}
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold uppercase text-gray-500">Progress</h3>
                <ol class="grid grid-cols-4 gap-2 text-center text-sm">
                    @foreach ($steps as $n => [$label, $who, $when])
                        @php $done = $current >= $n; @endphp
                        <li>
                            <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-full
                                        {{ $done ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                                {{ $done ? '✓' : $n }}
                            </div>
                            <div class="mt-1 font-medium {{ $done ? 'text-green-700' : 'text-gray-500' }}">{{ $label }}</div>
                            @if ($done && $who)
                                <div class="text-xs text-gray-500">{{ $who }}</div>
                            @endif
                            @if ($done && $when)
                                <div class="text-xs text-gray-400">{{ $when->format('d M Y H:i') }}</div>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- Status banners --}}
            @if ($transfer->status === 'completed')
                <div class="rounded-md border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
                    Transfer complete. {{ $transfer->toUser->name }} now holds this item.
                </div>
            @elseif ($transfer->status === 'rejected')
                <div class="rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
                    Rejected. Reason: {{ $transfer->closed_reason }}
                </div>
            @elseif ($transfer->status === 'cancelled')
                <div class="rounded-md border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                    {{ $transfer->closed_reason }}
                </div>
            @endif

            {{-- Details --}}
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold uppercase text-gray-500">Details</h3>
                <dl class="grid grid-cols-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-gray-500">Evidence</dt>
                        <dd class="font-medium">
                            @if ($transfer->evidence)
                                <a href="{{ $transfer->evidence->showUrl() }}" class="text-indigo-600 hover:underline">
                                    {{ $transfer->evidence->evidence_number }}
                                </a>
                            @else - @endif
                        </dd></div>
                    <div><dt class="text-gray-500">Currently held by</dt>
                        <dd class="font-medium">{{ $transfer->evidence->currentHolder->name ?? '-' }}</dd></div>
                    <div><dt class="text-gray-500">From</dt><dd class="font-medium">{{ $transfer->fromUser->name }}</dd></div>
                    <div><dt class="text-gray-500">To</dt><dd class="font-medium">{{ $transfer->toUser->name }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-gray-500">Purpose</dt><dd>{{ $transfer->purpose }}</dd></div>
                    @if ($transfer->approval_note)
                        <div class="sm:col-span-2"><dt class="text-gray-500">Supervisor note</dt><dd>{{ $transfer->approval_note }}</dd></div>
                    @endif
                    @if ($transfer->release_seal_condition)
                        <div class="sm:col-span-2"><dt class="text-gray-500">Seal condition at release</dt><dd>{{ $transfer->release_seal_condition }}</dd></div>
                    @endif
                    @if ($transfer->receipt_seal_condition)
                        <div class="sm:col-span-2"><dt class="text-gray-500">Seal condition at receipt</dt><dd>{{ $transfer->receipt_seal_condition }}</dd></div>
                    @endif
                </dl>
            </div>

            {{-- Step 2: supervisor decision --}}
            @if ($transfer->status === 'requested')
                @if ($canApprove)
                    <div class="grid gap-4 sm:grid-cols-2">
                        <form method="POST" action="{{ route('transfers.approve', $transfer) }}" class="rounded-lg bg-white p-6 shadow-sm">
                            @csrf
                            <h3 class="mb-3 font-semibold">Supervisor Decision: Approve</h3>
                            <label for="approval_note" class="block text-sm text-gray-600">Note (optional)</label>
                            <input id="approval_note" name="approval_note" maxlength="500" class="mt-1 mb-3 block w-full rounded-md border-gray-300">
                            <button class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">Approve Transfer</button>
                        </form>

                        <form method="POST" action="{{ route('transfers.reject', $transfer) }}" class="rounded-lg bg-white p-6 shadow-sm">
                            @csrf
                            <h3 class="mb-3 font-semibold">Or reject</h3>
                            <label for="closed_reason" class="block text-sm text-gray-600">Reason</label>
                            <input id="closed_reason" name="closed_reason" required maxlength="500" class="mt-1 mb-3 block w-full rounded-md border-gray-300">
                            <button class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Reject Transfer</button>
                        </form>
                    </div>
                @else
                    <div class="rounded-md border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        Waiting on Supervisor approval.
                    </div>
                @endif
            @endif

            {{-- Step 3: release (current holder only) --}}
            @if ($transfer->status === 'approved')
                @if ($isFrom)
                    <form method="POST" action="{{ route('transfers.release', $transfer) }}" class="rounded-lg bg-white p-6 shadow-sm">
                        @csrf
                        <h3 class="mb-3 font-semibold">Your Confirmation: Release</h3>
                        <label for="seal_release" class="block text-sm text-gray-600">Seal condition</label>
                        <input id="seal_release" name="seal_condition" required maxlength="255"
                               class="mt-1 mb-3 block w-full rounded-md border-gray-300"
                               placeholder="e.g. Sealed in tamper evident bag, seal intact">
                        <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Confirm Release</button>
                    </form>
                @else
                    <div class="rounded-md border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        Waiting on {{ $transfer->fromUser->name }} to confirm release.
                    </div>
                @endif
            @endif

            {{-- Step 4: receipt (receiver only) --}}
            @if ($transfer->status === 'released')
                @if ($isTo)
                    <form method="POST" action="{{ route('transfers.receive', $transfer) }}" class="rounded-lg bg-white p-6 shadow-sm">
                        @csrf
                        <h3 class="mb-3 font-semibold">Your Confirmation: Receipt</h3>
                        <label for="seal_receipt" class="block text-sm text-gray-600">Seal condition on arrival</label>
                        <input id="seal_receipt" name="seal_condition" required maxlength="255"
                               class="mt-1 mb-3 block w-full rounded-md border-gray-300"
                               placeholder="e.g. Seal intact, no sign of tampering">
                        <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Confirm Receipt</button>
                    </form>
                @else
                    <div class="rounded-md border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        Waiting on {{ $transfer->toUser->name }} to confirm receipt.
                    </div>
                @endif
            @endif

            {{-- Cancel before release --}}
            @if (in_array($transfer->status, ['requested', 'approved'], true) && ($isFrom || $isRequester))
                <form method="POST" action="{{ route('transfers.cancel', $transfer) }}"
                      onsubmit="return confirm('Cancel this transfer?');">
                    @csrf
                    <button class="text-sm text-red-600 hover:underline">Cancel this transfer</button>
                </form>
            @endif

            <div>
                <a href="{{ route('transfers.index') }}" class="text-sm text-gray-600 hover:underline">Back to all transfers</a>
            </div>
        </div>
    </div>
</x-app-layout>
