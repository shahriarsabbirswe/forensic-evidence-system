<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Register a New Case</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <p class="mb-6 text-sm text-gray-600">
                    The case number is generated automatically once the case is saved.
                </p>

                <form method="POST" action="{{ route('cases.store') }}">
                    @include('cases.form')

                    <div class="mt-6 flex items-center gap-3">
                        <button class="rounded-md bg-gray-800 px-5 py-2 text-sm font-medium text-white hover:bg-gray-700">
                            Create Case
                        </button>
                        <a href="{{ route('cases.index') }}" class="text-sm text-gray-600 underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
