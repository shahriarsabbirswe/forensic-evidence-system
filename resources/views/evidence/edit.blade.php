<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit {{ $evidence->evidence_number }}
            </h2>
            <p class="text-sm text-gray-500">
                Case {{ $case->case_number }} &middot; {{ $case->title }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="mb-4 rounded-md bg-amber-50 p-4 text-sm text-amber-800">
                Metadata can be corrected here. The stored file, its hash and the evidence
                number cannot be changed by anyone.
            </div>

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('evidence.update', $evidence) }}">
                    @method('PUT')
                    @include('evidence.form')

                    <div class="mt-8 flex items-center gap-3 border-t border-gray-100 pt-6">
                        <button class="rounded-md bg-gray-800 px-5 py-2 text-sm font-medium text-white hover:bg-gray-700">
                            Save Changes
                        </button>
                        <a href="{{ route('evidence.show', $evidence) }}" class="text-sm text-gray-600 underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
