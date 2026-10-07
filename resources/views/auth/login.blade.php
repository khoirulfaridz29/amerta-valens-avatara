@php
    $hero = asset('images/aset.webp');
    $lines = ['Catat semua aktivitas', 'arus kas', 'dan posisi alat'];
    $ticker = [
        'Gunakan APD lengkap sebelum bekerja',
        'Cek kondisi alat sebelum dioperasikan',
        'Utamakan keselamatan, bukan kecepatan',
        'Jaga jarak aman dari alat berat yang beroperasi',
        'Matikan mesin sebelum melakukan perawatan',
        'Laporkan setiap bahaya ke pengawas',
    ];
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — Amerta Valens Avatara</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes riseIn { from { opacity: 0; transform: translateY(26px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes lineIn { from { transform: translateY(112%); } to { transform: translateY(0); } }
        .rise { animation: riseIn .7s cubic-bezier(.22,1,.36,1) both; }
        .kline { display: block; overflow: hidden; padding-bottom: .25rem; }
        .kline > span { display: block; animation: lineIn .95s cubic-bezier(.22,1,.36,1) both; }
    </style>
</head>
<body class="min-h-screen bg-mandau-bg">
    <div id="login-slides"
         class="flex h-screen snap-x snap-mandatory overflow-x-auto overflow-y-hidden no-scrollbar overscroll-x-contain lg:grid lg:h-auto lg:min-h-screen lg:grid-cols-[1.15fr_1fr] lg:overflow-visible">
        {{-- Slide 1 — Hero --}}
        <section class="relative flex h-screen w-screen shrink-0 snap-center flex-col overflow-hidden bg-mandau-navy lg:h-auto lg:min-h-screen lg:w-auto" data-testid="login-hero">
            <img src="{{ $hero }}" alt="Amerta Valens Avatara" class="absolute inset-0 h-full w-full object-cover opacity-60">
            <div class="absolute inset-0 bg-gradient-to-br from-[#0B1E4B]/85 via-[#0B1E4B]/55 to-[#2D6FF2]/40"></div>
            <div class="grain absolute inset-0"></div>

            <div class="relative z-10 p-8 sm:p-10"><x-logo light /></div>

            <div class="relative z-10 mt-auto px-8 pb-8 sm:px-10 sm:pb-12">
                <p class="rise mb-5 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-blue-100 backdrop-blur">
                    rekap rental alat berat
                </p>
                <h1 class="font-display text-4xl font-extrabold leading-[1.05] tracking-tight text-white sm:text-5xl xl:text-6xl">
                    @foreach ($lines as $i => $l)
                        <span class="kline"><span style="animation-delay: {{ 0.3 + $i * 0.15 }}s">{{ $l }}</span></span>
                    @endforeach
                </h1>
                <p class="rise mt-5 max-w-md text-base leading-relaxed text-blue-100/85" style="animation-delay:.85s">
                    Laporan harian operator, posisi alat, buku kas, dan service terangkum otomatis tanpa perlu menanyai operator satu per satu.
                </p>

                <button type="button" id="to-login"
                        class="mt-7 inline-flex items-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-bold text-mandau-blue-deep shadow-lg transition hover:scale-[1.02] active:scale-95 lg:hidden">
                    Masuk ke form <span aria-hidden="true">→</span>
                </button>
            </div>

            <div class="relative z-10 overflow-hidden border-t border-white/10 bg-white/5 backdrop-blur" aria-hidden="true">
                <div class="flex w-max animate-marquee py-3.5">
                    @foreach ([0, 1] as $half)
                        <div class="flex items-center">
                            @foreach ($ticker as $t)
                                <span class="mx-7 flex items-center gap-2.5 whitespace-nowrap text-xs font-bold uppercase tracking-[0.14em] text-blue-100/80">
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#7EA6FF]"></span> {{ $t }}
                                </span>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Slide 2 — Form --}}
        <section class="relative flex h-screen w-screen shrink-0 snap-center flex-col overflow-y-auto bg-mandau-bg lg:h-auto lg:min-h-screen lg:w-auto lg:overflow-visible">
            <button type="button" id="to-hero"
                    class="absolute left-4 top-4 z-10 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 shadow-sm lg:hidden">
                <span aria-hidden="true">←</span> Kembali
            </button>

            <div class="m-auto w-full max-w-md px-6 py-16 lg:py-10">
                <div class="mb-5 flex justify-center lg:hidden"><x-logo /></div>
                <div class="rise rounded-[28px] bg-white p-7 shadow-float sm:p-9">
                    <h1 class="font-display text-2xl font-extrabold tracking-tight text-slate-900">Masuk ke Avatara</h1>
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-500">Pantau alat, kas, dan proyek dari satu tempat.</p>

                    @if ($errors->any())
                        <div class="mt-4 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
                    @endif

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4" novalidate>
                        @csrf
                        <div class="space-y-1.5">
                            <label for="email" class="text-sm font-bold text-slate-700">Email</label>
                            <input id="email" name="email" type="email" inputmode="email" autocomplete="username" required
                                   value="{{ old('email') }}" placeholder="nama@perusahaan.id"
                                   class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base text-slate-900 placeholder:text-slate-400 focus:border-mandau-blue focus:bg-white focus:outline-none">
                        </div>
                        <div class="space-y-1.5">
                            <label for="password" class="text-sm font-bold text-slate-700">Password</label>
                            <div class="relative">
                                <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="••••••••"
                                       class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 pr-12 text-base text-slate-900 placeholder:text-slate-400 focus:border-mandau-blue focus:bg-white focus:outline-none">
                                <button type="button" id="toggle-pass" aria-label="Tampilkan password"
                                        class="absolute right-3 top-1/2 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-full text-slate-400 hover:text-slate-600">
                                    <x-icon name="eye" id="icon-eye" class="h-[18px] w-[18px]" />
                                    <x-icon name="eyeoff" id="icon-eyeoff" class="hidden h-[18px] w-[18px]" />
                                </button>
                            </div>
                        </div>
                        <label class="flex items-center gap-2 text-sm text-slate-500">
                            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300">
                            Ingat saya
                        </label>

                        <button type="submit"
                                class="flex h-12 w-full items-center justify-center gap-2 rounded-full bg-mandau-blue text-base font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-[0.98] disabled:opacity-60">
                            <x-icon name="login" class="h-[18px] w-[18px]" />
                            Masuk
                        </button>
                    </form>

                    <div class="my-4 flex items-center gap-3">
                        <span class="h-px flex-1 bg-slate-200"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">atau</span>
                        <span class="h-px flex-1 bg-slate-200"></span>
                    </div>

                    @if (config('services.google.client_id'))
                        <a href="{{ route('auth.google') }}"
                           class="flex h-12 w-full items-center justify-center gap-2 rounded-full border border-slate-200 bg-white text-base font-bold text-slate-700 transition hover:border-mandau-blue hover:text-mandau-blue">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.27-4.74 3.27-8.1Z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23Z"/><path fill="#FBBC05" d="M5.84 14.1a6.6 6.6 0 0 1 0-4.2V7.06H2.18a11 11 0 0 0 0 9.88l3.66-2.84Z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1A11 11 0 0 0 2.18 7.06l3.66 2.84C6.71 7.31 9.14 5.38 12 5.38Z"/></svg>
                            Masuk dengan Google
                        </a>
                    @else
                        <button type="button"
                                onclick="avaAlert({ title: 'Login Google', message: 'Fitur login dengan Google belum tersedia. Sementara silakan masuk memakai email dan password.', cancelText: 'Tutup', confirmText: 'Mengerti' })"
                                class="flex h-12 w-full items-center justify-center gap-2 rounded-full border border-slate-200 bg-white text-base font-bold text-slate-700 transition hover:border-mandau-blue hover:text-mandau-blue">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.27-4.74 3.27-8.1Z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23Z"/><path fill="#FBBC05" d="M5.84 14.1a6.6 6.6 0 0 1 0-4.2V7.06H2.18a11 11 0 0 0 0 9.88l3.66-2.84Z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1A11 11 0 0 0 2.18 7.06l3.66 2.84C6.71 7.31 9.14 5.38 12 5.38Z"/></svg>
                            Masuk dengan Google
                        </button>
                    @endif

        </div>
    </div>
        </section>
    </div>

    <script>
        (function () {
            var sc = document.getElementById('login-slides');
            var toLogin = document.getElementById('to-login');
            var toHero = document.getElementById('to-hero');
            function goForm() { sc.scrollTo({ left: sc.clientWidth, behavior: 'smooth' }); }
            function goHero() { sc.scrollTo({ left: 0, behavior: 'smooth' }); }
            if (toLogin) toLogin.addEventListener('click', goForm);
            if (toHero) toHero.addEventListener('click', goHero);

        document.getElementById('toggle-pass').addEventListener('click', function () {
                var p = document.getElementById('password');
                var show = p.type === 'password';
                p.type = show ? 'text' : 'password';
                document.getElementById('icon-eye').classList.toggle('hidden', show);
                document.getElementById('icon-eyeoff').classList.toggle('hidden', !show);
            });
        })();
    </script>

    <x-alert-modal />
</body>
</html>
