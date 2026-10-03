@props([
    'code' => '500',
    'title' => 'Terjadi kesalahan',
    'message' => 'Ada yang tidak beres. Coba lagi sebentar lagi.',
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} — {{ $title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative grid min-h-screen place-items-center overflow-hidden bg-mandau-bg p-4 text-slate-800">
    {{-- Dekorasi latar --}}
    <div class="pointer-events-none absolute -left-24 -top-24 h-80 w-80 rounded-full bg-mandau-blue/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-32 -right-20 h-96 w-96 rounded-full bg-[#1E429F]/20 blur-3xl"></div>
    <div class="pointer-events-none absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(#1E429F 1px, transparent 1px); background-size: 22px 22px;"></div>

    <main class="relative w-full max-w-lg">
        <div class="rounded-[28px] border border-white/70 bg-white p-8 text-center shadow-float sm:p-10">
            <div class="mb-6 flex justify-center"><x-logo /></div>

            <p class="font-num bg-gradient-to-br from-[#2D6FF2] to-[#0B1E4B] bg-clip-text text-7xl font-extrabold leading-none tracking-tight text-transparent sm:text-8xl">{{ $code }}</p>

            <h1 class="mt-4 font-display text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">{{ $title }}</h1>
            <p class="mx-auto mt-2 max-w-sm text-sm leading-relaxed text-slate-500">{{ $message }}</p>

            <div class="mt-7 flex flex-col justify-center gap-2 sm:flex-row">
                <a href="{{ url('/') }}"
                   class="inline-flex h-12 items-center justify-center gap-2 rounded-full bg-mandau-blue px-6 text-sm font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-[0.98]">
                    <x-icon name="dashboard" class="h-4 w-4" /> Kembali ke Beranda
                </a>
                <button type="button" onclick="history.back()"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-full border border-slate-200 bg-white px-6 text-sm font-bold text-slate-600 transition hover:border-mandau-blue hover:text-mandau-blue">
                    Halaman Sebelumnya
                </button>
            </div>
        </div>

        <p class="mt-5 text-center text-xs text-slate-400">Amerta Valens Avatara · Sistem Administrasi Rental Alat Berat</p>
    </main>
</body>
</html>
