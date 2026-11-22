<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>DFEMS | Digital Forensic Evidence Management System</title>
    <meta name="description" content="DFEMS is a digital forensic evidence management system with chain-of-custody tracking, SHA-256 evidence integrity and role-based access control.">
    <meta name="keywords" content="DFEMS, digital forensic evidence management, chain of custody, forensic evidence tracking, SHA-256 verification">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#020617">
    <link rel="canonical" href="https://dfems.tech/">

    {{-- Open Graph: preview when the link is shared --}}
    <meta property="og:site_name" content="DFEMS">
    <meta property="og:title" content="DFEMS | Digital Forensic Evidence Management System">
    <meta property="og:description" content="Chain-of-custody tracking, SHA-256 evidence integrity and role-based access for digital forensic investigations.">
    <meta property="og:url" content="https://dfems.tech/">
    <meta property="og:type" content="website">

    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="DFEMS | Digital Forensic Evidence Management System">
    <meta name="twitter:description" content="Chain-of-custody tracking, SHA-256 evidence integrity and role-based access for digital forensic investigations.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 font-sans text-slate-300 antialiased">

@php
    $problems = [
        ['Paper registers', 'Seizure and handover notes are written late, edited or lost, so the record cannot be trusted in court.'],
        ['No proof of integrity', 'Without a fingerprint of each file there is no way to show that evidence is unchanged since seizure.'],
        ['Slow retrieval', 'Finding one exhibit or rebuilding its custody history means searching physical files.'],
    ];

    $steps = [
        ['1', 'Open a case', 'An officer opens a case with a generated number, crime type and jurisdiction, then a supervisor assigns the team.'],
        ['2', 'Register evidence', 'Digital files and physical exhibits are recorded with device details, seizure time and place, officer and witness.'],
        ['3', 'Hash at intake', 'The system calculates a SHA-256 hash of the stored file. This becomes the baseline that never changes.'],
        ['4', 'Transfer with sign-off', 'A handover needs supervisor approval, then confirmation from the releasing and the receiving person.'],
    ];

    $features = [
        ['Case management', 'Create, organize and track forensic cases, and assign authorized personnel with specific roles.'],
        ['Evidence registration', 'Complete seizure metadata for digital files and physical exhibits, including acquisition details.'],
        ['SHA-256 at intake', 'Automatic cryptographic hash generated at intake and locked as an immutable integrity baseline.'],
        ['Instant search & filters', 'Locate cases and exhibits rapidly by EV number, case ID, status, officer, date or SHA-256 hash.'],
        ['Dual sign-off transfers', 'Multi-step custody approval workflow requiring confirmation from both releasing and receiving parties.'],
        ['Role-based access control', 'Six granular user roles enforced server-side to prevent unauthorized viewing or modification.'],
        ['Chain-of-custody ledger', 'Chronological, tamper-evident record of every evidence handover along with seal conditions.'],
        ['Hash-chained audit log', 'Every system action is cryptographically linked to the previous entry to expose unauthorized edits.'],
        ['Court-ready reporting', 'Structured case documentation containing exhibit metadata, custody timeline and hash verification.'],
    ];

    $roles = [
        ['Investigating Officer', 'Opens cases, registers and seizes evidence.'],
        ['Forensic Analyst', 'Examines evidence and records findings.'],
        ['Custody Officer', 'Receives, stores and transfers exhibits.'],
        ['Supervisor', 'Approves transfers and oversees the case team.'],
        ['Prosecutor', 'Reads cases and evidence, cannot change them.'],
        ['Administrator', 'Manages accounts and access rights.'],
    ];
@endphp

{{-- Header --}}
<header class="sticky top-0 z-40 border-b border-slate-800 bg-slate-950/80 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-5">
        <a href="#top" class="flex items-center gap-2 font-semibold text-white">
            <span class="flex h-8 w-8 items-center justify-center rounded-md border border-cyan-500/30 bg-cyan-500/10 text-xs font-bold text-cyan-300">DF</span>
            DFEMS
        </a>
        <nav class="hidden items-center gap-7 text-sm md:flex">
            <a href="#problem" class="hover:text-white">Problem</a>
            <a href="#workflow" class="hover:text-white">How it works</a>
            <a href="#features" class="hover:text-white">Features</a>
            <a href="#integrity" class="hover:text-white">Integrity</a>
            <a href="#roles" class="hover:text-white">Roles</a>
        </nav>
        @if (Route::has('login'))
            @auth
                <a href="{{ route('dashboard') }}" class="rounded-md bg-cyan-500 px-4 py-2 text-sm font-medium text-slate-950 hover:bg-cyan-400">Go to Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="rounded-md bg-cyan-500 px-4 py-2 text-sm font-medium text-slate-950 hover:bg-cyan-400">Sign in</a>
            @endauth
        @endif
    </div>
</header>

<main id="top">

    {{-- Hero --}}
    <section class="relative overflow-hidden border-b border-slate-800">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(60%_50%_at_70%_0%,rgba(6,182,212,0.12),transparent)]"></div>
        <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-5 py-20 lg:grid-cols-2 lg:py-28">
            <div>
                <span class="inline-block rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 text-xs font-medium text-cyan-300">
                    Secure chain-of-custody platform
                </span>
                <h1 class="mt-5 text-4xl font-bold leading-tight text-white sm:text-5xl">
                    Digital Forensic Evidence Management System
                </h1>
                <p class="mt-5 max-w-xl text-lg text-slate-400">
                    Register evidence, fingerprint every file with SHA-256 and record each handover with approval from both sides,
                    from seizure to the courtroom.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    @if (Route::has('login'))
                        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}"
                           class="rounded-md bg-cyan-500 px-5 py-3 text-sm font-medium text-slate-950 hover:bg-cyan-400">
                            {{ auth()->check() ? 'Open Dashboard' : 'Sign in to the system' }}
                        </a>
                    @endif
                    <a href="#workflow" class="rounded-md border border-slate-700 px-5 py-3 text-sm font-medium text-slate-200 hover:bg-slate-900">
                        See how it works
                    </a>
                </div>

                <dl class="mt-12 grid max-w-lg grid-cols-3 gap-4 border-t border-slate-800 pt-6 text-center">
                    <div><dt class="text-xs text-slate-500">Integrity Standard</dt><dd class="mt-1 text-xl font-semibold text-white">SHA-256</dd></div>
                    <div><dt class="text-xs text-slate-500">Access Control</dt><dd class="mt-1 text-xl font-semibold text-white">6 Roles</dd></div>
                    <div><dt class="text-xs text-slate-500">Custody Flow</dt><dd class="mt-1 text-xl font-semibold text-white">Dual Sign-off</dd></div>
                </dl>
            </div>

            {{-- Sample record card --}}
            <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5 shadow-2xl shadow-cyan-950/30">
                <div class="mb-4 flex items-center justify-between text-xs">
                    <span class="font-mono text-slate-400">EV-2026-0001</span>
                    <span class="rounded-md border border-emerald-500/30 bg-emerald-500/10 px-2 py-1 text-emerald-300">Immutable record, SHA-256 protected</span>
                </div>
                <div class="text-sm font-semibold text-white">Server log archive</div>
                <div class="mt-1 text-xs text-slate-500">Chain-of-custody verification record</div>

                <div class="mt-4 rounded-md bg-slate-950 p-3">
                    <div class="text-[11px] uppercase tracking-wide text-slate-500">SHA-256 baseline</div>
                    <div class="mt-1 break-all font-mono text-xs text-cyan-300">9f2c4b7a1d0e83c5a6b1f7e2d4c9a08b3e5f61d7c2a94b0e8f3d6a15c7b2e9d4</div>
                </div>

                <ol class="relative ml-2 mt-5 space-y-4 border-l border-slate-700 pl-5 text-sm">
                    @foreach ([['Requested', 'Investigating Officer'], ['Approved', 'Supervisor'], ['Released', 'Investigating Officer'], ['Received', 'Forensic Analyst']] as [$label, $who])
                        <li class="relative">
                            <span class="absolute -left-[26px] top-1.5 h-2.5 w-2.5 rounded-full bg-emerald-400 ring-4 ring-slate-900"></span>
                            <span class="font-medium text-white">{{ $label }}</span>
                            <span class="text-slate-500">by {{ $who }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    {{-- Problem --}}
    <section id="problem" class="border-b border-slate-800 py-20">
        <div class="mx-auto max-w-6xl px-5">
            <p class="text-sm font-medium text-cyan-400">The problem</p>
            <h2 class="mt-2 max-w-2xl text-3xl font-bold text-white">Evidence is worth nothing in court if its history cannot be proved</h2>
            <div class="mt-10 grid gap-5 md:grid-cols-3">
                @foreach ($problems as [$title, $text])
                    <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-6">
                        <h3 class="font-semibold text-white">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-400">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Workflow --}}
    <section id="workflow" class="border-b border-slate-800 py-20">
        <div class="mx-auto max-w-6xl px-5">
            <p class="text-sm font-medium text-cyan-400">How it works</p>
            <h2 class="mt-2 text-3xl font-bold text-white">From seizure to handover in four steps</h2>
            <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                @foreach ($steps as [$n, $title, $text])
                    <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-6">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full border border-cyan-500/40 bg-cyan-500/10 text-sm font-semibold text-cyan-300">{{ $n }}</div>
                        <h3 class="mt-4 font-semibold text-white">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-400">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="border-b border-slate-800 py-20">
        <div class="mx-auto max-w-6xl px-5">
            <p class="text-sm font-medium text-cyan-400">Core capabilities</p>
            <h2 class="mt-2 text-3xl font-bold text-white">Built for forensic accountability</h2>
            <p class="mt-3 max-w-2xl text-sm text-slate-400">End-to-end controls to preserve digital evidence integrity from initial acquisition through legal proceedings.</p>
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($features as [$title, $text])
                    <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-6">
                        <h3 class="font-semibold text-white">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-400">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Integrity --}}
    <section id="integrity" class="border-b border-slate-800 py-20">
        <div class="mx-auto grid max-w-6xl items-center gap-12 px-5 lg:grid-cols-2">
            <div>
                <p class="text-sm font-medium text-cyan-400">Integrity by design</p>
                <h2 class="mt-2 text-3xl font-bold text-white">Any later edit breaks the chain</h2>
                <p class="mt-4 text-slate-400">
                    Each audit entry stores the cryptographic hash of the entry before it. Altering or deleting a historical record invalidates every subsequent hash, making tampering immediately detectable.
                </p>
                <ul class="mt-6 space-y-2.5 text-sm text-slate-400">
                    <li class="flex items-start gap-2">
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-cyan-400"></span>
                        <span>Evidence files are stored outside the public directory and served strictly through authenticated routes.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-cyan-400"></span>
                        <span>Exhibit number, file binary and baseline SHA-256 hash are locked permanently upon registration.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-cyan-400"></span>
                        <span>Every custody transfer requires explicit sign-off from both the releasing and receiving officers.</span>
                    </li>
                </ul>
            </div>
            <div class="space-y-3 font-mono text-xs">
                @foreach ([['Entry 41', '00000000', 'a91f03c2'], ['Entry 42', 'a91f03c2', '7c3e58d1'], ['Entry 43', '7c3e58d1', '02bd9e46']] as [$entry, $prev, $hash])
                    <div class="rounded-md border border-slate-800 bg-slate-900/60 p-4">
                        <div class="text-slate-200">{{ $entry }}</div>
                        <div class="mt-1 text-slate-500">previous: {{ $prev }}</div>
                        <div class="text-cyan-300">hash: {{ $hash }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Roles --}}
    <section id="roles" class="py-20">
        <div class="mx-auto max-w-6xl px-5">
            <p class="text-sm font-medium text-cyan-400">Access governance</p>
            <h2 class="mt-2 text-3xl font-bold text-white">Six roles, each limited to its own scope</h2>
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($roles as [$role, $text])
                    <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-6">
                        <h3 class="font-semibold text-white">{{ $role }}</h3>
                        <p class="mt-2 text-sm text-slate-400">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</main>

<footer class="border-t border-slate-800 py-10">
    <div class="mx-auto max-w-6xl px-5 text-center text-xs text-slate-500">
        <p class="font-medium text-slate-400">Developed by Md. Abu Shahriar Sabbir, Jarif Hossain &amp; Sabbir Ahmed</p>
        <p class="mt-1">Daffodil International University</p>
        <p class="mt-4 text-slate-600">&copy; 2026 DFEMS — Digital Forensic Evidence Management System. All rights reserved.</p>
    </div>
</footer>

</body>
</html>