{{-- Shared by create and edit. Expects $evidence, $case, $officers. --}}
@csrf

@php $isNew = ! $evidence->exists; @endphp

<div x-data="{ kind: '{{ old('kind', $evidence->kind ?? 'digital') }}' }" class="space-y-8">

    {{-- 1. What the item is --}}
    <fieldset>
        <legend class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Item</legend>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">Evidence Type</label>
                @if ($isNew)
                    <select name="kind" x-model="kind" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="digital">Digital (a file is uploaded)</option>
                        <option value="physical">Physical (stored in a locker)</option>
                    </select>
                @else
                    <p class="mt-2 text-sm text-gray-900">{{ ucfirst($evidence->kind) }}</p>
                    <p class="text-xs text-gray-500">The type cannot be changed after registration.</p>
                @endif
                @error('kind') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Device Type</label>
                <input type="text" name="device_type" required
                       value="{{ old('device_type', $evidence->device_type) }}"
                       placeholder="Laptop, mobile phone, USB drive"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('device_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Make and Model</label>
                <input type="text" name="make_model" value="{{ old('make_model', $evidence->make_model) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('make_model') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Serial Number</label>
                <input type="text" name="serial_number" value="{{ old('serial_number', $evidence->serial_number) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('serial_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="3" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $evidence->description) }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </fieldset>

    {{-- 2. Seizure metadata --}}
    <fieldset class="border-t border-gray-100 pt-6">
        <legend class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Seizure Details</legend>

        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Source</label>
                <input type="text" name="source" required value="{{ old('source', $evidence->source) }}"
                       placeholder="Where the item came from, and from whom"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('source') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Seized At</label>
                <input type="datetime-local" name="seized_at" required
                       value="{{ old('seized_at', optional($evidence->seized_at)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <p class="mt-1 text-xs text-gray-500">
                    The 90 day preservation period under Section 36 runs from this moment.
                </p>
                @error('seized_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Seizure Location</label>
                <input type="text" name="seizure_location" required
                       value="{{ old('seizure_location', $evidence->seizure_location) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('seizure_location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Seizing Officer</label>
                <select name="seizing_officer_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Choose an officer</option>
                    @foreach ($officers as $officer)
                        <option value="{{ $officer->id }}"
                            @selected(old('seizing_officer_id', $evidence->seizing_officer_id) == $officer->id)>
                            {{ $officer->name }}
                        </option>
                    @endforeach
                </select>
                @error('seizing_officer_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Witness Name</label>
                <input type="text" name="witness_name" value="{{ old('witness_name', $evidence->witness_name) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('witness_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </fieldset>

    {{-- 3. The file, or the locker --}}
    <fieldset class="border-t border-gray-100 pt-6">
        <legend class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Storage</legend>

        <div x-show="kind === 'digital'" x-cloak>
            @if ($isNew)
                <label class="block text-sm font-medium text-gray-700">Evidence File</label>
                <input type="file" name="evidence_file"
                       class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-sm file:text-white">
                <p class="mt-1 text-xs text-gray-500">
                    A SHA-256 hash is calculated the moment this is saved and cannot be changed afterwards.
                </p>
            @else
                <p class="text-sm text-gray-900">{{ $evidence->original_filename ?? 'No file' }}</p>
                <p class="text-xs text-gray-500">
                    The stored file cannot be replaced. Register a new item instead.
                </p>
            @endif
            @error('evidence_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div x-show="kind === 'physical'" x-cloak>
            <label class="block text-sm font-medium text-gray-700">Storage Locker Reference</label>
            <input type="text" name="storage_locker" value="{{ old('storage_locker', $evidence->storage_locker) }}"
                   placeholder="Cabinet 3, shelf B, bag 014"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            @error('storage_locker') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </fieldset>

    {{-- 4. Acquisition details --}}
    <fieldset class="border-t border-gray-100 pt-6">
        <legend class="mb-1 text-sm font-semibold uppercase tracking-wide text-gray-500">Acquisition Details</legend>
        <p class="mb-4 text-xs text-gray-500">
            Leave blank for a physical exhibit that has not been imaged yet.
        </p>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">Source State</label>
                <input type="text" name="source_state" value="{{ old('source_state', $evidence->source_state) }}"
                       placeholder="Powered off, unlocked, encrypted, PIN locked"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('source_state') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Write Blocker</label>
                <input type="text" name="write_blocker" value="{{ old('write_blocker', $evidence->write_blocker) }}"
                       placeholder="Model, or the software setting used"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('write_blocker') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Acquisition Method</label>
                <select name="acquisition_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Not recorded</option>
                    @foreach (['live' => 'Live', 'dead' => 'Dead'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('acquisition_method', $evidence->acquisition_method) === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('acquisition_method') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Acquisition Scope</label>
                <select name="acquisition_scope" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Not recorded</option>
                    @foreach (['physical' => 'Physical', 'logical' => 'Logical'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('acquisition_scope', $evidence->acquisition_scope) === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('acquisition_scope') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Image Format</label>
                <select name="image_format" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Not recorded</option>
                    @foreach (['raw' => 'RAW / dd', 'e01' => 'E01', 'aff4' => 'AFF4', 'ad1' => 'AD1 / logical container', 'other' => 'Other'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('image_format', $evidence->image_format) === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('image_format') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Examiner</label>
                <select name="acquired_by" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Not recorded</option>
                    @foreach ($officers as $officer)
                        <option value="{{ $officer->id }}" @selected(old('acquired_by', $evidence->acquired_by) == $officer->id)>
                            {{ $officer->name }}
                        </option>
                    @endforeach
                </select>
                @error('acquired_by') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Acquisition Tool</label>
                <input type="text" name="acquisition_tool" value="{{ old('acquisition_tool', $evidence->acquisition_tool) }}"
                       placeholder="FTK Imager, Guymager"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('acquisition_tool') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tool Version</label>
                <input type="text" name="acquisition_tool_version"
                       value="{{ old('acquisition_tool_version', $evidence->acquisition_tool_version) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('acquisition_tool_version') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Acquisition Started</label>
                <input type="datetime-local" name="acquisition_started_at"
                       value="{{ old('acquisition_started_at', optional($evidence->acquisition_started_at)->format('Y-m-d\TH:i')) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('acquisition_started_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Acquisition Completed</label>
                <input type="datetime-local" name="acquisition_completed_at"
                       value="{{ old('acquisition_completed_at', optional($evidence->acquisition_completed_at)->format('Y-m-d\TH:i')) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('acquisition_completed_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Time Zone</label>
                <input type="text" name="acquisition_timezone"
                       value="{{ old('acquisition_timezone', $evidence->acquisition_timezone ?? 'Asia/Dhaka') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <p class="mt-1 text-xs text-gray-500">Recorded explicitly, never assumed.</p>
                @error('acquisition_timezone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">
                    Errors, Limitations and Unavoidable Changes
                </label>
                <textarea name="acquisition_notes" rows="3"
                          placeholder="Bad sectors, tool warnings, collection limits, changes caused by live collection"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('acquisition_notes', $evidence->acquisition_notes) }}</textarea>
                @error('acquisition_notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </fieldset>
</div>
