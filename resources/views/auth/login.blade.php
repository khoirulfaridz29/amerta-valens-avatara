@php
    $hero = 'https://images.unsplash.com/photo-1590834367872-3297c46273ac?q=80&w=1800&auto=format&fit=crop';
    $roles = [
        'bos' => ['label' => 'Admin', 'pin' => '123456'],
        'operator' => ['label' => 'Operator', 'pin' => '654321'],
    ];
    $lines = ['Posisi alat.', 'Arus kas.', 'Tanpa menebak.'];
    $ticker = [
        'EX-01 CAT 320 — Beroperasi',
        'EX-02 Komatsu PC200 — Beroperasi',
        'EX-03 Hitachi ZX200 — Perbaikan',
        'Kontrak Tol Balikpapan — On Track',
        'Kas Harian — Tercatat',
    ];
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — Amerta Valens Avatara</title>
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
        .pin-dot { letter-spacing: .5em; }
    </style>
</head>
<body class="min-h-screen bg-mandau-navy lg:grid lg:grid-cols-[1.15fr_1fr]">
    {{-- Hero --}}
    <div class="relative hidden overflow-hidden bg-mandau-navy lg:flex lg:flex-col" data-testid="login-hero">
        <img src="{{ $hero }}" alt="Excavator bekerja di lokasi tambang" class="absolute inset-0 h-full w-full object-cover opacity-60">
        <div class="absolute inset-0 bg-gradient-to-br from-[#0B1E4B]/85 via-[#0B1E4B]/55 to-[#2D6FF2]/40"></div>
        <div class="grain absolute inset-0"></div>

        <div class="relative z-10 p-10"><x-logo light /></div>

        <div class="relative z-10 mt-auto px-10 pb-12">
            <p class="rise mb-5 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-blue-100 backdrop-blur">
                Rental Alat Berat · Satu Layar
            </p>
            <h1 class="font-display text-5xl font-extrabold leading-[1.04] tracking-tight text-white xl:text-6xl">
                @foreach ($lines as $i => $l)
                    <span class="kline"><span style="animation-delay: {{ 0.3 + $i * 0.15 }}s">{{ $l }}</span></span>
                @endforeach
            </h1>
            <p class="rise mt-5 max-w-md text-base leading-relaxed text-blue-100/85" style="animation-delay:.85s">
                Laporan harian operator, posisi alat, buku kas, dan service — terangkum otomatis tanpa perlu menanyai operator satu per satu.
            </p>
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
    </div>

    {{-- Form --}}
    <div class="flex min-h-screen flex-col bg-mandau-bg lg:min-h-0 lg:items-center lg:justify-center">
        <div class="p-6 lg:hidden"><x-logo /></div>

        <div class="rise w-full max-w-md rounded-[28px] bg-white p-7 shadow-float sm:p-9 lg:my-10">
            <h1 class="font-display text-2xl font-extrabold tracking-tight text-slate-900">Masuk ke Avatara</h1>
            <p class="mt-1.5 text-sm leading-relaxed text-slate-500">Masukkan PIN akun Anda.</p>

            @if ($errors->any())
                <div class="mt-4 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif

            <div class="mt-6 grid grid-cols-2 gap-2" role="group" aria-label="Isi cepat PIN demo">
                @foreach ($roles as $key => $r)
                    <button type="button" data-testid="role-selector-{{ $key }}" data-pin="{{ $r['pin'] }}"
                            class="role-preset h-11 rounded-full border border-slate-200 bg-slate-50 text-sm font-bold text-slate-600 transition hover:border-mandau-blue/40 hover:text-mandau-blue">
                        {{ $r['label'] }}
                    </button>
                @endforeach
            </div>

            <form method="POST" action="{{ route('login') }}" class="mt-5 space-y-4" novalidate>
                @csrf
                <div class="space-y-1.5">
                    <label for="pin" class="text-sm font-bold text-slate-700">PIN</label>
                    <input id="pin" name="pin" type="password" inputmode="numeric" pattern="[0-9]*" autocomplete="off"
                           required minlength="4" maxlength="8" placeholder="••••••" data-testid="login-pin"
                           class="pin-dot h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-center text-lg font-bold tracking-widest text-slate-900 placeholder:text-slate-400 focus:border-mandau-blue focus:bg-white focus:outline-none">
                </div>

                <button type="submit" data-testid="login-submit-button"
                        class="flex h-12 w-full items-center justify-center gap-2 rounded-full bg-mandau-blue text-base font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-[0.98] disabled:opacity-60">
                    <x-icon name="login" class="h-[18px] w-[18px]" />
                    Masuk
                </button>
            </form>

            @if (config('services.google.client_id'))
                <a href="{{ route('auth.google') }}"
                   class="mt-3 flex h-12 w-full items-center justify-center gap-2 rounded-full border border-slate-200 bg-white text-base font-bold text-slate-700 transition hover:border-mandau-blue hover:text-mandau-blue">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.27-4.74 3.27-8.1Z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23Z"/><path fill="#FBBC05" d="M5.84 14.1a6.6 6.6 0 0 1 0-4.2V7.06H2.18a11 11 0 0 0 0 9.88l3.66-2.84Z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1A11 11 0 0 0 2.18 7.06l3.66 2.84C6.71 7.31 9.14 5.38 12 5.38Z"/></svg>
                    Masuk dengan Google
                </a>
            @endif

            <div class="mt-5 rounded-2xl border border-blue-100 bg-[#EFF4FF] px-4 py-3 text-xs leading-relaxed text-slate-600">
                <span class="font-bold text-mandau-blue-deep">PIN demo:</span> Admin 123456 · Operator 654321
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.role-preset').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('pin').value = btn.dataset.pin;
                document.querySelectorAll('.role-preset').forEach(function (b) {
                    b.className = 'role-preset h-11 rounded-full border border-slate-200 bg-slate-50 text-sm font-bold text-slate-600 transition hover:border-mandau-blue/40 hover:text-mandau-blue';
                });
                btn.className = 'role-preset h-11 rounded-full border border-mandau-blue bg-mandau-blue text-sm font-bold text-white shadow-md shadow-blue-500/25 transition';
            });
        });
    </script>
</body>
</html>
