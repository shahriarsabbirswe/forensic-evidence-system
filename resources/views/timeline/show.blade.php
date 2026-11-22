<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Investigation Timeline: {{ $evidence->evidence_number }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Chronological reconstruction of seizure, intake, integrity, custody, and lifecycle events
                </p>
            </div>
            <a href="{{ route('evidence.show', $evidence) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                Back to Evidence
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-6 pb-4 border-b border-gray-200 flex flex-wrap gap-6 text-sm">
                    <div><span class="text-gray-500">Evidence ID:</span> <span class="font-semibold">{{ $evidence->evidence_number }}</span></div>
                    <div><span class="text-gray-500">Type:</span> <span class="font-semibold capitalize">{{ $evidence->kind }}</span></div>
                    <div><span class="text-gray-500">Total Events:</span> <span class="font-semibold">{{ $timeline->count() }}</span></div>
                </div>

                @if($timeline->isEmpty())
                    <p class="text-gray-500 text-sm">No timeline events recorded for this evidence item.</p>
                @else
                    <div class="relative border-l-2 border-indigo-200 ml-3 space-y-6">
                        @foreach($timeline as $event)
                            <div class="ml-6 relative">
                                <span class="absolute -left-8 top-1.5 w-3.5 h-3.5 rounded-full bg-indigo-600 ring-4 ring-white"></span>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $event['category'] }}
                                        </span>
                                        <span class="text-xs text-gray-500 font-mono">
                                            {{ \Carbon\Carbon::parse($event['timestamp'])->format('d M Y, H:i:s') }}
                                        </span>
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-900">{{ $event['title'] }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $event['details'] }}</p>
                                    <p class="text-xs text-gray-500 mt-2">Recorded by: <span class="font-medium text-gray-700">{{ $event['actor'] }}</span></p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
