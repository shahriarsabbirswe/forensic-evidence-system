<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $evidence->device_type }}
                </h2>
                <p class="font-mono text-xs text-gray-500">
                    {{ $evidence->evidence_number }}
                    &middot;
                    <a href="{{ route('cases.show', $evidence->investigationCase) }}" class="underline">
                        {{ $evidence->investigationCase->case_number }}
                    </a>
                </p>
            </div>
            <div class="flex gap-2">
                @can('evidence.edit')
                    <a href="{{ route('evidence.edit', $evidence) }}"
                       class="rounded-md border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50">Edit</a>
                @endcan
                <a href="{{ route('cases.show', $evidence->investigationCase) }}"
                   class="rounded-md border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50">Back to Case</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            {{-- Integrity, first because it is the point of the system --}}
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Integrity</h3>

                @if ($evidence->sha256_hash)
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs text-gray-500">
                                {{ strtoupper($evidence->hash_algorithm ?? 'sha256') }} baseline
                            </dt>
                            <dd class="mt-1 break-all rounded bg-gray-50 p-3 font-mono text-xs text-gray-900">
                                {{ $evidence->sha256_hash }}
                            </dd>
                        </div>
                        <div class="flex gap-10">
                            <div>
                                <dt class="text-xs text-gray-500">Hashed at</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $evidence->hashed_at?->format('d M Y, H:i') ?? 'Not recorded' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">File size</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $evidence->file_size ? number_format($evidence->file_size / 1024, 1) . ' KB' : 'Not recorded' }}
                                </dd>
                            </div>
                        </div>
                    </dl>

                    <p class="mt-4 text-xs text-gray-500">
                        To confirm this value on Windows, run
                        <code class="rounded bg-gray-100 px-1">certutil -hashfile yourfile SHA256</code>
                        against the same file. A match shows the bytes are unchanged. It does not
                        show who created the data or why it exists.
                    </p>
                @elseif ($evidence->isDigital())
                    <div class="rounded-md bg-amber-50 p-4 text-sm text-amber-800">
                        No hash has been recorded for this item. FR3 is not implemented yet,
                        so nothing is calculated at intake. Until it is, this item has no
                        baseline and cannot be verified.
                    </div>
                @else
                    <p class="text-sm text-gray-500">
                        Physical exhibit. Integrity is maintained through the seal and the
                        custody record rather than a hash.
                    </p>
                @endif

                @if ($evidence->file_path)
                    <div class="mt-4">
                        <a href="{{ route('evidence.download', $evidence) }}"
                           class="text-sm text-indigo-600 hover:underline">
                            Download {{ $evidence->original_filename }}
                        </a>
                    </div>
                @endif
            </div>

            {{-- Item and seizure --}}
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">
                    Item and Seizure
                </h3>
                <dl class="grid gap-5 sm:grid-cols-3">
                    <div>
                        <dt class="text-xs text-gray-500">Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($evidence->kind) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Make and Model</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $evidence->make_model ?: 'Not recorded' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Serial Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $evidence->serial_number ?: 'Not recorded' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Seized At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $evidence->seized_at?->format('d M Y, H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Seizure Location</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $evidence->seizure_location }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Seizing Officer</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $evidence->seizingOfficer?->name ?? 'Unknown' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Source</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $evidence->source }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Witness</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $evidence->witness_name ?: 'None recorded' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Preservation Expires (s.36)</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $evidence->preservation_expires_at?->format('d M Y') ?? 'Not set' }}
                        </dd>
                    </div>
                    @if ($evidence->storage_locker)
                        <div>
                            <dt class="text-xs text-gray-500">Storage Locker</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $evidence->storage_locker }}</dd>
                        </div>
                    @endif
                    <div class="sm:col-span-3">
                        <dt class="text-xs text-gray-500">Description</dt>
                        <dd class="mt-1 whitespace-pre-line text-sm text-gray-900">{{ $evidence->description }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Acquisition --}}
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Acquisition</h3>

                @if (! $evidence->acquisition_tool && ! $evidence->write_blocker && ! $evidence->acquisition_method)
                    <p class="text-sm text-gray-500">No acquisition details recorded for this item.</p>
                @else
                    <dl class="grid gap-5 sm:grid-cols-3">
                        <div>
                            <dt class="text-xs text-gray-500">Source State</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $evidence->source_state ?: 'Not recorded' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Write Blocker</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $evidence->write_blocker ?: 'Not recorded' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Method and Scope</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $evidence->acquisition_method ? ucfirst($evidence->acquisition_method) : 'Not recorded' }},
                                {{ $evidence->acquisition_scope ? ucfirst($evidence->acquisition_scope) : 'not recorded' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Image Format</dt>
                            <dd class="mt-1 text-sm uppercase text-gray-900">{{ $evidence->image_format ?: 'Not recorded' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Tool</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $evidence->acquisition_tool ?: 'Not recorded' }}
                                {{ $evidence->acquisition_tool_version }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Examiner</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $evidence->acquiredBy?->name ?? 'Not recorded' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Started</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $evidence->acquisition_started_at?->format('d M Y, H:i') ?? 'Not recorded' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Completed</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $evidence->acquisition_completed_at?->format('d M Y, H:i') ?? 'Not recorded' }}
                                @if ($evidence->acquisitionDuration())
                                    <span class="text-gray-500">({{ $evidence->acquisitionDuration() }})</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Time Zone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $evidence->acquisition_timezone }}</dd>
                        </div>
                        @if ($evidence->acquisition_notes)
                            <div class="sm:col-span-3">
                                <dt class="text-xs text-gray-500">Errors and Limitations</dt>
                                <dd class="mt-1 whitespace-pre-line text-sm text-gray-900">
                                    {{ $evidence->acquisition_notes }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                @endif
            </div>

            {{-- Registration footer --}}
            <p class="text-xs text-gray-500">
                Registered by {{ $evidence->registeredBy?->name ?? 'Unknown' }}
                on {{ $evidence->created_at?->format('d M Y, H:i') }}.
            </p>
        </div>
    </div>
</x-app-layout>
