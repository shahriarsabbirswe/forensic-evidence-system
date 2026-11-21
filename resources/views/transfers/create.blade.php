<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Request Transfer</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @include('transfers.partials.flash')

            <div class="mb-4 rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-2 text-sm font-semibold uppercase text-gray-500">Item</h3>
                <p class="text-gray-900">
                    <span class="font-semibold">{{ $evidence->evidence_number }}</span>
                    ({{ $evidence->device_type ?? ucfirst($evidence->kind) }})
                </p>
                <p class="text-sm text-gray-600">
                    Currently held by {{ $evidence->currentHolder->name ?? 'Not recorded' }}
                </p>
            </div>

            <form method="POST" action="{{ route('transfers.store', $evidence) }}" class="space-y-4 rounded-lg bg-white p-6 shadow-sm">
                @csrf

                <div>
                    <label for="to_user_id" class="block text-sm font-medium text-gray-700">Transfer to</label>
                    <select id="to_user_id" name="to_user_id" required class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="">Select a person</option>
                        @foreach ($recipients as $person)
                            <option value="{{ $person->id }}" @selected(old('to_user_id') == $person->id)>
                                {{ $person->name }} ({{ $person->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose</label>
                    <textarea id="purpose" name="purpose" rows="3" required maxlength="500"
                              class="mt-1 block w-full rounded-md border-gray-300">{{ old('purpose') }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        Request Transfer
                    </button>
                    <a href="{{ $evidence->showUrl() }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
