{{-- Shared by create and edit. --}}
@csrf

<div class="grid gap-5 sm:grid-cols-2">

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Case Title</label>
        <input type="text" name="title" value="{{ old('title', $case->title) }}" required
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Crime Type</label>
        <input type="text" name="crime_type" value="{{ old('crime_type', $case->crime_type) }}" required
               placeholder="Online fraud, unauthorised access, harassment"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('crime_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Jurisdiction</label>
        <input type="text" name="jurisdiction" value="{{ old('jurisdiction', $case->jurisdiction) }}" required
               placeholder="Police station or investigating unit"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('jurisdiction') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            @foreach (['open','under_investigation','closed','archived'] as $s)
                <option value="{{ $s }}" @selected(old('status', $case->status ?? 'open') === $s)>
                    {{ ucfirst(str_replace('_',' ',$s)) }}
                </option>
            @endforeach
        </select>
        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Opened At</label>
        <input type="datetime-local" name="opened_at" required
               value="{{ old('opened_at', optional($case->opened_at)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        <p class="mt-1 text-xs text-gray-500">
            The 90 day limit under Section 32 is counted from this moment.
        </p>
        @error('opened_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="4"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $case->description) }}</textarea>
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
