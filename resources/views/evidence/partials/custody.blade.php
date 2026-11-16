@php
    $holder     = $evidence->currentHolder;
    $open       = $evidence->openTransfer();
    $canRequest = auth()->user()->can('transfer.request') && (int) $evidence->current_holder_id === auth()->id();
    $recent     = $evidence->transfers()->with(['fromUser', 'toUser', 'requester', 'approver'])->limit(5)->get();
@endphp

<div class="mt-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h3 class="text-lg font-semibold text-slate-800">Chain of custody</h3>
        <x-immutable-badge :hashed="! empty($evidence->sha256_hash)" />
    </div>

    @if (session('error'))
        <div class="mt-3 rounded-md border border-rose-300 bg-rose-50 px-3 py-2 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

    <p class="mt-3 text-sm text-slate-600">
        Currently held by
        <span class="font-semibold text-slate-900">{{ $holder->name ?? 'Not recorded' }}</span>
    </p>

    <div class="mt-3">
        @if ($open)
            <p class="text-sm text-amber-700">
                A transfer to {{ $open->toUser->name ?? '-' }} is in progress
                <x-status-badge :status="$open->status" class="ml-1" />
                <a href="{{ route('transfers.show', $open) }}" class="ml-2 text-indigo-600 hover:underline">View</a>
            </p>
        @elseif ($canRequest)
            <a href="{{ route('transfers.create', $evidence) }}"
               class="inline-block rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                Request Transfer
            </a>
        @endif
    </div>

    @if ($recent->isNotEmpty())
        <h4 class="mb-3 mt-6 text-sm font-semibold uppercase text-slate-500">Custody timeline</h4>

        @foreach ($recent as $t)
            @php
                $events = array_values(array_filter([
                    ['label' => 'Requested', 'who' => $t->requester->name ?? null, 'at' => $t->created_at, 'note' => $t->purpose, 'tone' => 'ok'],
                    $t->approved_at ? [
                        'label' => $t->status === 'rejected' ? 'Rejected' : 'Approved',
                        'who'   => $t->approver->name ?? null,
                        'at'    => $t->approved_at,
                        'note'  => $t->status === 'rejected' ? $t->closed_reason : $t->approval_note,
                        'tone'  => $t->status === 'rejected' ? 'bad' : 'ok',
                    ] : null,
                    $t->released_at ? [
                        'label' => 'Released',
                        'who'   => $t->fromUser->name ?? null,
                        'at'    => $t->released_at,
                        'note'  => $t->release_seal_condition ? 'Seal: ' . $t->release_seal_condition : null,
                        'tone'  => 'ok',
                    ] : null,
                    $t->received_at ? [
                        'label' => 'Received',
                        'who'   => $t->toUser->name ?? null,
                        'at'    => $t->received_at,
                        'note'  => $t->receipt_seal_condition ? 'Seal: ' . $t->receipt_seal_condition : null,
                        'tone'  => 'ok',
                    ] : null,
                    $t->status === 'cancelled' ? [
                        'label' => 'Cancelled',
                        'who'   => null,
                        'at'    => $t->updated_at,
                        'note'  => $t->closed_reason,
                        'tone'  => 'neutral',
                    ] : null,
                ]));
                $dot = ['ok' => 'bg-emerald-500', 'bad' => 'bg-rose-500', 'neutral' => 'bg-slate-400'];
            @endphp

            <div class="mb-6 rounded-md border border-slate-100 p-4">
                <div class="mb-3 flex items-center justify-between text-sm">
                    <span class="font-medium text-slate-800">
                        {{ $t->fromUser->name ?? '-' }} to {{ $t->toUser->name ?? '-' }}
                    </span>
                    <span class="flex items-center gap-3">
                        <x-status-badge :status="$t->status" />
                        <a href="{{ route('transfers.show', $t) }}" class="text-indigo-600 hover:underline">Details</a>
                    </span>
                </div>

                <ol class="relative ml-3 border-l-2 border-slate-200">
                    @foreach ($events as $e)
                        <li class="mb-4 ml-5 last:mb-0">
                            <span class="absolute -left-[7px] mt-1.5 h-3 w-3 rounded-full ring-4 ring-white {{ $dot[$e['tone']] }}"></span>
                            <div class="text-sm font-medium text-slate-800">
                                {{ $e['label'] }}
                                @if ($e['who'])
                                    <span class="font-normal text-slate-500">by {{ $e['who'] }}</span>
                                @endif
                            </div>
                            <div class="font-mono text-xs text-slate-400">{{ $e['at']->format('d M Y, h:i A') }}</div>
                            @if (! empty($e['note']))
                                <div class="mt-1 text-sm text-slate-600">{{ $e['note'] }}</div>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        @endforeach
    @endif
</div>
