@php use App\Support\Format; @endphp

<x-app-layout title="Rekap Aktivitas">
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Rekap Aktivitas</h1>
                <p class="mt-1 text-sm text-slate-500">Ringkasan dari laporan harian Anda</p>
            </div>
            <form method="GET" action="{{ route('rekap.index') }}" class="flex items-center gap-2" id="rekap-filter">
                <input type="month" name="bulan" value="{{ $bulan }}" aria-label="Bulan" onchange="document.getElementById('rekap-filter').submit()"
                       class="h-11 rounded-full border border-slate-200 bg-white px-4 text-sm text-slate-600 focus:border-mandau-blue focus:outline-none">
                @if ($bulan)
                    <a href="{{ route('rekap.index') }}" class="text-sm font-bold text-slate-500 hover:text-mandau-blue">Reset</a>
                @endif
            </form>
        </div>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-3 gap-3 sm:gap-4">
            <div class="rounded-3xl bg-mandau-blue p-4 text-white shadow-float sm:p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-blue-200">Laporan</p>
                <p class="font-num mt-2 text-xl font-bold sm:text-2xl">{{ $ringkasan['laporan'] }}</p>
            </div>
            <div class="rounded-3xl bg-white p-4 shadow-soft ring-1 ring-slate-100 sm:p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total HM Kerja</p>
                <p class="font-num mt-2 text-xl font-bold text-slate-800 sm:text-2xl">{{ rtrim(rtrim(number_format($ringkasan['hm'], 2, '.', ','), '0'), '.') }}</p>
            </div>
            <div class="rounded-3xl bg-white p-4 shadow-soft ring-1 ring-slate-100 sm:p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Solar</p>
                <p class="font-num mt-2 text-lg font-bold text-amber-600 sm:text-xl">{{ rtrim(rtrim(number_format($ringkasan['solar_jerigen'], 2, '.', ','), '0'), '.') }} jrg</p>
                <p class="font-num text-xs text-slate-500">{{ rtrim(rtrim(number_format($ringkasan['solar_liter'], 2, '.', ','), '0'), '.') }} L</p>
            </div>
        </div>

        @if (count($items) === 0)
            <x-empty-state icon="clipboard" title="Belum ada aktivitas" sub="Kirim laporan harian dulu di tab Lapor Harian." />
        @else
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($items as $l)
                    <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-100">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <span class="font-num rounded-lg bg-[#EFF4FF] px-2.5 py-1 text-xs font-bold text-mandau-blue">{{ $l['alat_kode'] ?: 'EX-??' }}</span>
                                <div>
                                    <p class="font-bold text-slate-800">{{ $l['alat_nama'] }}</p>
                                    <p class="text-xs text-slate-500">{{ $l['proyek_nama'] }}</p>
                                </div>
                            </div>
                            <p class="shrink-0 text-xs font-semibold text-slate-400">{{ Format::tgl($l['tanggal']) }}</p>
                        </div>
                        <div class="font-num mt-3 flex flex-wrap gap-2 text-xs">
                            <span class="rounded-full bg-slate-100 px-3 py-1 font-bold text-slate-700">HM {{ $l['hm_awal'] }}–{{ $l['hm_akhir'] }} ({{ rtrim(rtrim(number_format($l['hm_kerja'], 2, '.', ','), '0'), '.') }} jam)</span>
                            @if ($l['solar_jerigen'] !== null)
                                <span class="flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 font-bold text-slate-700"><x-icon name="fuel" class="h-3 w-3" /> {{ $l['solar_jerigen'] }} jrg ({{ $l['solar_liter'] }} L)</span>
                            @endif
                        </div>
                        @if ($l['keterangan'])
                            <p class="mt-3 border-t border-slate-100 pt-3 text-sm text-slate-600">{{ $l['keterangan'] }}</p>
                        @endif
                        @if ($l['foto_lokasi'])
                            <a href="{{ $l['foto_lokasi'] }}" target="_blank" class="mt-3 block">
                                <img src="{{ $l['foto_lokasi'] }}" alt="Foto lokasi" class="h-24 w-full rounded-xl object-cover ring-1 ring-slate-200">
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
