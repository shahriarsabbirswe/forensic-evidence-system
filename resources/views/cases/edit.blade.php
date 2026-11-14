<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Edit {{ $case->case_number }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('cases.update', $case) }}">
                    @method('PUT')
                    @include('cases.form')

                    <div class="mt-6 flex items-center gap-3">
                        <button class="rounded-md bg-gray-800 px-5 py-2 text-sm font-medium text-white hover:bg-gray-700">
                            Save Changes
                        </button>
                        <a href="{{ route('cases.show', $case) }}" class="text-sm text-gray-600 underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
