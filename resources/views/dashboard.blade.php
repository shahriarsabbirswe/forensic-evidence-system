<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4 text-sm text-gray-600">Welcome, {{ auth()->user()->name }}.</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('cases.index') }}"
                           class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Go to Cases</a>

                        @can('case.create')
                            <a href="{{ route('cases.create') }}"
                               class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">New Case</a>
                        @endcan

                        <a href="{{ route('search.index') }}"
                           class="rounded-md bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Search</a>

                        @can('transfer.view')
                            <a href="{{ route('transfers.index') }}"
                               class="rounded-md bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700">Transfers</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>