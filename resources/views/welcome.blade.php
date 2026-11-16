<!DOCTYPE html>
<html lang="en">
<head>
    <title>DFEMS — Digital Forensic Evidence Management System</title>
<meta name="description" content="DFEMS (Digital Forensic Evidence Management System) — Secure chain-of-custody tracking, SHA-256 evidence integrity verification, and investigation case management platform.">
<meta name="keywords" content="DFEMS, Digital Forensic Evidence Management System, Chain of Custody, Forensic Evidence Tracking, SHA-256 Verification">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://dfems.tech/">

<!-- সোশ্যাল মিডিয়া ও সার্চ প্রিভিউ (Open Graph) -->
<meta property="og:title" content="DFEMS — Digital Forensic Evidence Management System">
<meta property="og:description" content="Enterprise-grade Digital Forensic Evidence Management System with tamper-evident chain-of-custody and role-based access control.">
<meta property="og:url" content="https://dfems.tech/">
<meta property="og:type" content="website">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DFEMS — Digital Forensic Evidence Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 antialiased selection:bg-cyan-500 selection:text-slate-950">

    <!-- Navbar -->
    <header class="border-b border-slate-800/80 bg-slate-950/80 backdrop-blur sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 font-bold">
                    DF
                </div>
                <span class="font-semibold tracking-tight text-lg">DFEMS</span>
            </div>

            <nav class="flex items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-4 py-2 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-medium text-sm transition">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-slate-300 hover:text-white text-sm font-medium transition">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-medium text-sm transition">
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="max-w-6xl mx-auto px-6 py-20 md:py-28">
        <div class="max-w-3xl">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 mb-6">
                Secure Chain-of-Custody Platform
            </span>
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-white leading-tight">
                Digital Forensic Evidence Management System
            </h1>
            <p class="mt-5 text-lg text-slate-400 leading-relaxed">
                A centralized, tamper-evident platform for collecting, preserving, tracking, and auditing digital forensic artifacts with complete chain-of-custody integrity.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-6 py-3 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-semibold text-sm transition">
                        Open Case Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-3 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-semibold text-sm transition">
                        Investigator Portal Login
                    </a>
                @endauth
                <a href="#features" class="px-6 py-3 rounded-lg border border-slate-800 hover:border-slate-700 text-slate-300 text-sm font-medium transition">
                    System Capabilities
                </a>
            </div>
        </div>

        <!-- Core Features -->
        <section id="features" class="mt-24 grid md:grid-cols-3 gap-6">
            <div class="p-6 rounded-xl bg-slate-900/60 border border-slate-800">
                <h3 class="text-base font-semibold text-white">Chain of Custody Tracking</h3>
                <p class="mt-2 text-sm text-slate-400 leading-relaxed">
                    Every evidence transfer, access, and status update is logged with timestamps and investigator attribution.
                </p>
            </div>

            <div class="p-6 rounded-xl bg-slate-900/60 border border-slate-800">
                <h3 class="text-base font-semibold text-white">Cryptographic Integrity</h3>
                <p class="mt-2 text-sm text-slate-400 leading-relaxed">
                    Verify artifact authenticity using SHA-256 hash records to ensure zero alteration from seizure to court presentation.
                </p>
            </div>

            <div class="p-6 rounded-xl bg-slate-900/60 border border-slate-800">
                <h3 class="text-base font-semibold text-white">Automated Forensic Reports</h3>
                <p class="mt-2 text-sm text-slate-400 leading-relaxed">
                    Generate standardized PDF custody logs and case summaries ready for legal review and documentation.
                </p>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-900 py-8 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} DFEMS &mdash; Digital Forensic Evidence Management System. All rights reserved.
    </footer>

</body>
</html>