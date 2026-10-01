<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $case->title }}</h2>
                <p class="font-mono text-xs text-gray-500">{{ $case->case_number }}</p>
            </div>
            <div class="flex gap-2">
                @can('case.edit')
                    <a href="{{ route('cases.edit', $case) }}"
                       class="rounded-md border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50">Edit</a>
                @endcan
                <a href="{{ route('cases.index') }}"
                   class="rounded-md border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            @php $days = $case->daysRemaining(); @endphp
            @if ($days !== null && $days < 0)
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-800">
                    This investigation passed its Section 32 deadline {{ abs($days) }} days ago.
                    An extension must be recorded, or the case closed.
                </div>
            @elseif ($days !== null && $days <= 14)
                <div class="rounded-md bg-amber-50 p-4 text-sm text-amber-800">
                    {{ $days }} days remain before the Section 32 deadline.
                </div>
            @endif

            {{-- Case details --}}
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Case Details</h3>
                <dl class="grid gap-5 sm:grid-cols-3">
                    <div>
                        <dt class="text-xs text-gray-500">Crime Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $case->crime_type }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Jurisdiction</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $case->jurisdiction }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ ucfirst(str_replace('_',' ',$case->status)) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Opened At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $case->opened_at?->format('d M Y, H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Opened By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $case->openedBy?->name ?? 'Unknown' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Deadline (Section 32)</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $case->investigation_deadline?->format('d M Y') ?? 'Not set' }}
                        </dd>
                    </div>
                    @if ($case->description)
                        <div class="sm:col-span-3">
                            <dt class="text-xs text-gray-500">Description</dt>
                            <dd class="mt-1 whitespace-pre-line text-sm text-gray-900">{{ $case->description }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Assigned team --}}
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Assigned Team</h3>

                @if ($case->assignedUsers->isEmpty())
                    <p class="text-sm text-gray-500">Nobody is assigned to this case yet.</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="text-left text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="py-2">Name</th>
                                <th class="py-2">Role on Case</th>
                                <th class="py-2">Assigned</th>
                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($case->assignedUsers as $member)
                                <tr>
                                    <td class="py-2 font-medium text-gray-900">{{ $member->name }}</td>
                                    <td class="py-2 text-gray-600">{{ $member->pivot->role_in_case }}</td>
                                    <td class="py-2 text-gray-500">
                                        {{ \Illuminate\Support\Carbon::parse($member->pivot->assigned_at)->format('d M Y') }}
                                    </td>
                                    <td class="py-2 text-right">
                                        @can('case.assign')
                                            <form method="POST"
                                                  action="{{ route('cases.unassign', [$case, $member]) }}"
                                                  onsubmit="return confirm('Remove {{ $member->name }} from this case?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600 hover:underline">Remove</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                @can('case.assign')
                    <form method="POST" action="{{ route('cases.assign', $case) }}"
                          class="mt-5 flex flex-wrap items-end gap-3 border-t border-gray-100 pt-5">
                        @csrf
                        <div>
                            <label class="block text-xs text-gray-500">Person</label>
                            <select name="user_id" required
                                    class="mt-1 rounded-md border-gray-300 text-sm shadow-sm">
                                <option value="">Choose a person</option>
                                @foreach ($availableUsers as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }} ({{ $user->getRoleNames()->first() ?? 'No role' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500">Role on this case</label>
                            <input type="text" name="role_in_case" required
                                   placeholder="Lead investigator"
                                   class="mt-1 rounded-md border-gray-300 text-sm shadow-sm">
                        </div>
                        <button class="rounded-md bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-700">
                            Assign
                        </button>

                        @error('user_id') <p class="w-full text-sm text-red-600">{{ $message }}</p> @enderror
                        @error('role_in_case') <p class="w-full text-sm text-red-600">{{ $message }}</p> @enderror
                    </form>
                @endcan
            </div>

{{-- Evidence --}}
<div class="rounded-lg bg-white p-6 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
            Evidence ({{ $case->evidence->count() }})
        </h3>
        @can('evidence.register')
            <a href="{{ route('evidence.create', $case) }}"
               class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                Register Evidence
            </a>
        @endcan
    </div>

    @if ($case->evidence->isEmpty())
        <p class="text-sm text-gray-500">No evidence registered against this case yet.</p>
    @else
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="text-left text-xs uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="py-2">Evidence No.</th>
                    <th class="py-2">Device</th>
                    <th class="py-2">Type</th>
                    <th class="py-2">Seized</th>
                    <th class="py-2">Hash</th>
                    <th class="py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($case->evidence as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 font-mono text-xs">{{ $item->evidence_number }}</td>
                        <td class="py-2 font-medium text-gray-900">{{ $item->device_type }}</td>
                        <td class="py-2 text-gray-600">{{ ucfirst($item->kind) }}</td>
                        <td class="py-2 text-gray-600">{{ $item->seized_at?->format('d M Y') }}</td>
                        <td class="py-2">
                            @if ($item->sha256_hash)
                                <span class="font-mono text-xs text-gray-600"
                                      title="{{ $item->sha256_hash }}">
                                    {{ substr($item->sha256_hash, 0, 12) }}...
                                </span>
                            @elseif ($item->isDigital())
                                <span class="text-xs text-amber-600">No baseline</span>
                            @else
                                <span class="text-xs text-gray-400">Not applicable</span>
                            @endif
                        </td>
                        <td class="py-2 text-right">
                            <a href="{{ route('evidence.show', $item) }}"
                               class="text-indigo-600 hover:underline">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
        </div>
    </div>
</x-app-layout>
