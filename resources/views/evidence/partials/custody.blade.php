@php
    $holder        = $evidence->currentHolder;
    $open          = $evidence->openTransfer();
    $canRequest    = auth()->user()->can('transfer.request') && (int) $evidence->current_holder_id === auth()->id();
    $recent        = $evidence->transfers()->with(['fromUser', 'toUser', 'requester', 'approver'])->limit(5)->get();
    $ledgerEntries = $evidence->custodyEntries()->with(['fromUser', 'toUser', 'recorder'])->get();
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

    {{-- Official Immutable Custody Ledger (FR5) --}}
    <div class="mt-8 border-t border-slate-200 pt-6">
        <div class="mb-3 flex items-center justify-between">
            <h4 class="text-sm font-semibold uppercase tracking-wide text-slate-600">
                Official Custody Ledger (Append-Only)
            </h4>
            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-mono text-xs text-slate-600">
                {{ $ledgerEntries->count() }} {{ Str::plural('entry', $ledgerEntries->count()) }}
            </span>
        </div>

        @if ($ledgerEntries->isEmpty())
            <p class="text-sm text-slate-500">No ledger entries recorded yet for this item.</p>
        @else
            <div class="overflow-x-auto rounded-md border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2.5">#</th>
                            <th class="px-4 py-2.5">Timestamp</th>
                            <th class="px-4 py-2.5">Action</th>
                            <th class="px-4 py-2.5">Custody Movement</th>
                            <th class="px-4 py-2.5">Seal Verification</th>
                            <th class="px-4 py-2.5">Purpose / Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach ($ledgerEntries as $index => $entry)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-400">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-600">
                                    {{ $entry->occurred_at?->format('d M Y, h:i A') }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-medium capitalize text-indigo-700 ring-1 ring-inset ring-indigo-600/20">
                                        {{ $entry->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    @if ($entry->fromUser)
                                        <span class="font-medium">{{ $entry->fromUser->name }}</span>
                                        <span class="mx-1 text-slate-400">&rarr;</span>
                                    @endif
                                    <span class="font-semibold text-slate-900">{{ $entry->toUser->name ?? 'System' }}</span>
                                    <div class="text-xs text-slate-400">Recorded by: {{ $entry->recorder->name ?? 'System' }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if ($entry->release_seal_condition)
                                        <div><span class="text-slate-400">Out:</span> {{ $entry->release_seal_condition }}</div>
                                    @endif
                                    @if ($entry->receipt_seal_condition)
                                        <div class="font-medium text-emerald-700"><span class="text-slate-400 font-normal">In:</span> {{ $entry->receipt_seal_condition }}</div>
                                    @endif
                                    @if (! $entry->release_seal_condition && ! $entry->receipt_seal_condition)
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-600">
                                    {{ $entry->reason ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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