@props(['title' => 'Dashboard'])

@php
    $user = auth()->user();
    $isBos = $user->isBos();

    $bosNav = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'tid' => 'nav-dashboard-link'],
        ['route' => 'alat.index', 'label' => 'Data Alat', 'icon' => 'truck', 'tid' => 'nav-equipment-link'],
        ['route' => 'proyek.index', 'label' => 'Kontrak / Lokasi', 'icon' => 'mappin', 'tid' => 'nav-projects-link'],
        ['route' => 'kas.index', 'label' => 'Buku Kas', 'icon' => 'wallet', 'tid' => 'nav-cashbook-link'],
        ['route' => 'bon.index', 'label' => 'Bon', 'icon' => 'clipboard', 'tid' => 'nav-bon-link'],
        ['route' => 'service.index', 'label' => 'Riwayat Service', 'icon' => 'fuel', 'tid' => 'nav-service-link'],
        ['route' => 'laporan.index', 'label' => 'Laporan Harian', 'icon' => 'file', 'tid' => 'nav-reports-link'],
        ['route' => 'operator.index', 'label' => 'Operator', 'icon' => 'users', 'tid' => 'nav-operators-link'],
        ['route' => 'pengaturan.index', 'label' => 'Pengaturan', 'icon' => 'user', 'tid' => 'nav-settings-link'],
    ];
    $opNav = [
        ['route' => 'laporan.index', 'label' => 'Lapor Harian', 'icon' => 'file', 'tid' => 'nav-reports-link'],
        ['route' => 'rekap.index', 'label' => 'Rekap Aktivitas', 'icon' => 'clipboard', 'tid' => 'nav-rekap-link'],
    ];
    $nav = $isBos ? $bosNav : $opNav;
    $find = fn (string $r) => collect($nav)->firstWhere('route', $r);

    $fabRoute = $isBos ? route('kas.index', ['add' => 1]) : route('laporan.index', ['tab' => 'lapor']);
    $fabLabel = $isBos ? 'Catat Kas' : 'Lapor Harian';

    $dock = $isBos
        ? [$find('dashboard'), $find('alat.index'), 'FAB', $find('laporan.index'), $find('kas.index')]
        : [$find('laporan.index'), 'FAB', $find('rekap.index')];
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — Amerta Valens Avatara</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-mandau-bg">
    {{-- Sidebar — desktop --}}
    <aside class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col bg-mandau-blue-deep p-6 md:flex">
        <a href="{{ route('dashboard') }}" class="w-fit" aria-label="Amerta Valens Avatara — beranda">
            <x-logo light />
        </a>
        <a href="{{ $fabRoute }}" data-testid="sidebar-quick-action"
           class="mt-8 flex h-11 shrink-0 items-center justify-center gap-2 rounded-full bg-white text-sm font-bold text-mandau-blue-deep shadow-lg transition hover:scale-[1.02] hover:bg-blue-50 active:scale-95">
            <x-icon name="plus" class="h-4 w-4" /> {{ $fabLabel }}
        </a>
        <nav class="mt-8 flex-1 space-y-1 overflow-y-auto no-scrollbar">
            @foreach ($nav as $n)
                @php $active = request()->routeIs($n['route']); @endphp
                <a href="{{ route($n['route']) }}" data-testid="{{ $n['tid'] }}"
                   class="flex h-11 items-center gap-3 rounded-2xl px-4 text-sm font-semibold transition {{ $active ? 'bg-white/15 text-white' : 'text-blue-100/75 hover:bg-white/10 hover:text-white' }}">
                    <x-icon :name="$n['icon']" class="h-[18px] w-[18px] shrink-0" />
                    {{ $n['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="shrink-0 space-y-2">
            <div class="flex items-center gap-3 rounded-2xl bg-white/10 p-4">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-white/20 text-xs font-extrabold text-white">
                    {{ \App\Support\Format::initials($user->name) }}
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold text-white">{{ $user->name }}</p>
                    <p class="truncate text-xs text-blue-200">{{ $isBos ? 'Admin' : 'Operator' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" data-testid="logout-button"
                        class="flex h-11 w-full items-center justify-center gap-2 rounded-2xl bg-red-500 text-sm font-bold text-white shadow-lg transition hover:bg-red-600 active:scale-95">
                    <x-icon name="logout" class="h-4 w-4" /> Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Header — mobile --}}
    <header class="sticky top-0 z-40 border-b border-white/60 bg-mandau-bg/90 backdrop-blur-md md:hidden">
        <div class="flex items-center justify-between px-4 pb-2 pt-4">
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ \App\Support\Format::tgl(now()) }}</p>
                <h2 class="truncate font-display text-xl font-extrabold tracking-tight text-slate-900">
                    {{ \App\Support\Format::greeting() }}, {{ explode(' ', $user->name)[0] }}
                </h2>
            </div>
            <div class="relative">
                <button type="button" id="profile-btn" aria-label="Profil" aria-expanded="false"
                        class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-gradient-to-br from-[#2D6FF2] to-[#1E429F] text-sm font-extrabold text-white shadow-lg shadow-blue-500/25">
                    {{ \App\Support\Format::initials($user->name) }}
                </button>
                <div id="profile-menu" class="absolute right-0 top-14 z-50 hidden w-60 rounded-2xl border border-slate-200 bg-white p-3 shadow-float">
                    <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-[#2D6FF2] to-[#1E429F] text-xs font-extrabold text-white">
                            {{ \App\Support\Format::initials($user->name) }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $user->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $isBos ? 'Admin' : 'Operator' }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-red-500 text-sm font-bold text-white transition hover:bg-red-600 active:scale-[0.98]">
                            <x-icon name="logout" class="h-4 w-4" /> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Canvas --}}
    <main class="pb-28 md:pb-0 md:pl-64">
        <div class="p-4 md:p-6">
            <div class="mx-auto w-full max-w-6xl md:rounded-[28px] md:bg-white md:shadow-float md:ring-1 md:ring-white/60 md:[padding:2rem]">
                {{ $slot }}
            </div>
        </div>
    </main>

    {{-- Bottom dock — mobile --}}
    <nav class="fixed inset-x-3 bottom-3 z-50 flex h-16 items-center justify-around rounded-full border border-slate-200/80 bg-white/90 px-2 shadow-xl backdrop-blur-md md:hidden" aria-label="Navigasi utama">
        @foreach ($dock as $item)
            @if ($item === 'FAB')
                <a href="{{ $fabRoute }}" aria-label="{{ $isBos ? 'Catat transaksi kas' : 'Tulis laporan harian' }}"
                   class="relative -top-5 grid h-14 w-14 place-items-center rounded-full bg-mandau-blue text-white shadow-lg shadow-blue-500/40 transition hover:scale-105 active:scale-95">
                    <x-icon name="plus" class="h-6 w-6" />
                </a>
            @elseif ($item)
                @php $active = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex w-16 flex-col items-center gap-0.5 rounded-2xl py-1.5 text-[10px] font-bold transition {{ $active ? 'text-mandau-blue' : 'text-slate-400' }}">
                    <x-icon :name="$item['icon']" class="h-5 w-5" />
                    {{ explode(' ', $item['label'])[0] }}
                </a>
            @endif
        @endforeach
    </nav>

    <script>
        (function () {
            var btn = document.getElementById('profile-btn');
            var menu = document.getElementById('profile-menu');
            if (!btn || !menu) return;
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                var open = !menu.classList.toggle('hidden');
                btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
            document.addEventListener('click', function () {
                menu.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') menu.classList.add('hidden');
            });
        })();
    </script>

    <x-alert-modal />

    @if (session('status') || session('error'))
        <script>
            window.addEventListener('load', function () {
                window.avaAlert({
                    title: @json(session('error') ? 'Gagal' : 'Berhasil'),
                    message: @json(session('error') ?: session('status')),
                });
            });
        </script>
    @endif
</body>
</html>
