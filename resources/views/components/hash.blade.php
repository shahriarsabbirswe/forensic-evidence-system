@props(['value' => null, 'short' => false])

{{-- Usage: <x-hash :value="$evidence->sha256_hash" />  or  <x-hash :value="$case->case_number" short /> --}}
@if ($value)
    <span x-data="{ copied: false }" class="inline-flex flex-wrap items-center gap-2">
        <code class="break-all rounded bg-slate-100 px-2 py-1 font-mono text-xs text-slate-700">{{ $short ? \Illuminate\Support\Str::limit($value, 18, '...') : $value }}</code>
        <button type="button"
                x-on:click="navigator.clipboard.writeText(@js($value)); copied = true; setTimeout(() => copied = false, 1500)"
                class="rounded border border-slate-300 px-2 py-0.5 text-xs text-slate-600 hover:bg-slate-100">
            <span x-text="copied ? 'Copied' : 'Copy'">Copy</span>
        </button>
    </span>
@else
    <span class="text-sm text-slate-400">Not recorded</span>
@endif
