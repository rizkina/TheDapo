<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Dapo - Sistem Integrasi Dapodik & Arsip Cloud</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-white">

    <!-- Header / Navbar -->
    <nav class="fixed top-0 z-50 w-full border-b border-slate-200 bg-white/80 backdrop-blur-md dark:border-slate-800 dark:bg-slate-950/80">
        <div class="mx-auto flex max-w-7xl items-center justify-between p-4 lg:px-8">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold">D</div>
                <span class="text-xl font-extrabold tracking-tight">The Dapo</span>
            </div>
            <div>
                <a href="{{ url('/app/login') }}" class="rounded-full bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all">
                    Masuk ke Sistem
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative isolate overflow-hidden pt-32 pb-24 lg:pt-48">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-6xl bg-gradient-to-r from-indigo-600 to-violet-400 bg-clip-text text-transparent">
                    Integrasi Dapodik Lebih Mudah, Aman, & Cepat
                </h1>
                <p class="mt-6 text-lg leading-8 text-slate-600 dark:text-slate-400">
                    Sistem Manajemen Data Satuan Pendidikan terpadu dengan sinkronisasi otomatis, manajemen arsip berbasis cloud Google Drive, dan kontrol akses cerdas.
                </p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <a href="{{ url('/app/login') }}" class="rounded-xl bg-indigo-600 px-8 py-4 text-lg font-bold text-white shadow-xl hover:bg-indigo-500 hover:-translate-y-1 transition-all">
                        Mulai Sekarang
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Ornamen Background -->
        <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
            <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:w-[72.1875rem]"></div>
        </div>
    </section>

    <!-- Statistik Dinamis -->
    <section class="py-12 bg-white dark:bg-slate-900">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <dl class="grid grid-cols-1 gap-x-8 gap-y-16 text-center lg:grid-cols-3">
                <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                    <dt class="text-base leading-7 text-slate-600 dark:text-slate-400 font-semibold uppercase tracking-widest">Peserta Didik</dt>
                    <dd class="order-first text-5xl font-extrabold tracking-tight text-indigo-600 sm:text-6xl">{{ number_format($totalSiswa) }}</dd>
                </div>
                <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                    <dt class="text-base leading-7 text-slate-600 dark:text-slate-400 font-semibold uppercase tracking-widest">Guru & Tendik</dt>
                    <dd class="order-first text-5xl font-extrabold tracking-tight text-indigo-600 sm:text-6xl">{{ number_format($totalPtk) }}</dd>
                </div>
                <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                    <dt class="text-base leading-7 text-slate-600 dark:text-slate-400 font-semibold uppercase tracking-widest">Rombongan Belajar</dt>
                    <dd class="order-first text-5xl font-extrabold tracking-tight text-indigo-600 sm:text-6xl">{{ number_format($totalRombel) }}</dd>
                </div>
            </dl>
        </div>
    </section>

    <!-- Keunggulan Section -->
    <section class="py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
                <div class="p-8 rounded-3xl bg-white border border-slate-100 shadow-sm dark:bg-slate-900 dark:border-slate-800">
                    <div class="mb-4 h-12 w-12 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-600 dark:bg-indigo-900/30">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold">Sinkronisasi Instan</h3>
                    <p class="mt-4 text-slate-600 dark:text-slate-400">Tarik data dari Web Service Dapodik lokal secara massal menggunakan teknologi Redis Queue.</p>
                </div>
                <div class="p-8 rounded-3xl bg-white border border-slate-100 shadow-sm dark:bg-slate-900 dark:border-slate-800">
                    <div class="mb-4 h-12 w-12 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-600 dark:bg-indigo-900/30">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold">Google Drive Storage</h3>
                    <p class="mt-4 text-slate-600 dark:text-slate-400">Penyimpanan berkas scan (Ijazah, KK, KTP) langsung ke cloud secara otomatis dan terstruktur.</p>
                </div>
                <div class="p-8 rounded-3xl bg-white border border-slate-100 shadow-sm dark:bg-slate-900 dark:border-slate-800">
                    <div class="mb-4 h-12 w-12 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-600 dark:bg-indigo-900/30">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold">Otorisasi Cerdas</h3>
                    <p class="mt-4 text-slate-600 dark:text-slate-400">Sistem cerdas yang membedakan hak akses Admin, Wali Kelas, Guru Mapel, hingga Siswa secara presisi.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 dark:bg-slate-950 dark:border-slate-800">
        <div class="mx-auto max-w-7xl px-6 py-12 lg:px-8 text-center text-sm text-slate-500">
            &copy; 2026 The Dapo Application. Dikelola oleh Tim IT Sekolah.
        </div>
    </footer>

</body>
</html>