@props(['status'])

{{-- Usage: <x-status-badge :status="$transfer->status" /> --}}
@php
    $map = [
        // transfers
        'requested'           => 'bg-amber-100 text-amber-800 ring-amber-200',
        'approved'            => 'bg-sky-100 text-sky-800 ring-sky-200',
        'released'            => 'bg-indigo-100 text-indigo-800 ring-indigo-200',
        'completed'           => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
        'rejected'            => 'bg-rose-100 text-rose-800 ring-rose-200',
        'cancelled'           => 'bg-slate-200 text-slate-700 ring-slate-300',
        // cases
        'open'                => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
        'under_investigation' => 'bg-amber-100 text-amber-800 ring-amber-200',
        'closed'              => 'bg-slate-200 text-slate-700 ring-slate-300',
        'archived'            => 'bg-slate-200 text-slate-700 ring-slate-300',
        // evidence
        'registered'          => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
        'in_analysis'         => 'bg-sky-100 text-sky-800 ring-sky-200',
        'disposed'            => 'bg-rose-100 text-rose-800 ring-rose-200',
    ];
    $cls = $map[$status] ?? 'bg-slate-100 text-slate-700 ring-slate-200';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset ' . $cls]) }}>
    {{ \Illuminate\Support\Str::of($status)->replace('_', ' ')->title() }}
</span>
