<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Register Evidence</h2>
            <p class="text-sm text-gray-500">
                Case {{ $case->case_number }} &middot; {{ $case->title }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <p class="mb-6 text-sm text-gray-600">
                    The evidence number is generated automatically. For digital evidence the
                    SHA-256 hash is calculated at the moment of saving and becomes the
                    permanent baseline for that item.
                </p>

                <form method="POST" action="{{ route('evidence.store', $case) }}" enctype="multipart/form-data">
                    @include('evidence.form')

                    <div class="mt-8 flex items-center gap-3 border-t border-gray-100 pt-6">
                        <button class="rounded-md bg-gray-800 px-5 py-2 text-sm font-medium text-white hover:bg-gray-700">
                            Register Evidence
                        </button>
                        <a href="{{ route('cases.show', $case) }}" class="text-sm text-gray-600 underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
