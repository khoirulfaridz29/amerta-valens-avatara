@php use App\Support\Format; @endphp

<x-app-layout title="Dashboard">
    <div class="space-y-8">
        <div>
            <h1 class="font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Ringkasan Operasional</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ Format::tgl($data['today']) }} · {{ $data['alat_aktif'] }} alat aktif · {{ $data['laporan_hari_ini'] }} laporan masuk hari ini
            </p>
        </div>

        {{-- KPI --}}
        @php
            $kpis = [
                ['label' => 'Saldo Kas', 'value' => Format::rupiah($data['kas']['saldo']), 'tone' => 'navy', 'icon' => 'wallet'],
                ['label' => 'Kas Masuk', 'value' => Format::rupiah($data['kas']['masuk']), 'tone' => 'light', 'icon' => 'arrowupright'],
                ['label' => 'Kas Keluar', 'value' => Format::rupiah($data['kas']['keluar']), 'tone' => 'light', 'icon' => 'arrowdownright'],
                ['label' => 'Laporan Hari Ini', 'value' => $data['laporan_hari_ini'].' laporan', 'tone' => 'light', 'icon' => 'clipboard', 'sub' => 'dari '.$data['alat_aktif'].' alat aktif'],
            ];
        @endphp
        <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
            @foreach ($kpis as $k)
                <div class="rounded-3xl p-5 sm:p-6 {{ $k['tone'] === 'navy' ? 'bg-mandau-blue text-white shadow-float' : 'bg-white text-slate-900 shadow-soft ring-1 ring-slate-100' }}">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-xs font-bold uppercase tracking-wider {{ $k['tone'] === 'navy' ? 'text-blue-200' : 'text-slate-500' }}">{{ $k['label'] }}</p>
                        <span class="grid h-9 w-9 place-items-center rounded-full {{ $k['tone'] === 'navy' ? 'bg-white/15 text-white' : 'bg-[#EFF4FF] text-mandau-blue' }}">
                            <x-icon :name="$k['icon']" class="h-[18px] w-[18px]" />
                        </span>
                    </div>
                    <p class="font-num mt-3 text-xl font-bold sm:text-2xl">{{ $k['value'] }}</p>
                    @if (! empty($k['sub']))
                        <p class="mt-1 text-xs {{ $k['tone'] === 'navy' ? 'text-blue-200' : 'text-slate-500' }}">{{ $k['sub'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Pintasan cepat (mobile) --}}
        <div class="grid grid-cols-4 gap-3 md:hidden">
            @foreach ([
                ['route' => 'proyek.index', 'label' => 'Kontrak', 'icon' => 'mappin'],
                ['route' => 'bon.index', 'label' => 'Bon', 'icon' => 'clipboard'],
                ['route' => 'service.index', 'label' => 'Service', 'icon' => 'fuel'],
                ['route' => 'operator.index', 'label' => 'Operator', 'icon' => 'users'],
            ] as $s)
                <a href="{{ route($s['route']) }}" data-testid="quick-{{ $s['route'] }}"
                   class="flex aspect-square flex-col items-center justify-center gap-2 rounded-3xl bg-white p-2 text-center shadow-soft ring-1 ring-slate-100 transition active:scale-95">
                    <span class="grid h-10 w-10 place-items-center rounded-full bg-[#EFF4FF] text-mandau-blue">
                        <x-icon :name="$s['icon']" class="h-5 w-5" />
                    </span>
                    <span class="text-[11px] font-bold text-slate-600">{{ $s['label'] }}</span>
                </a>
            @endforeach
        </div>

        {{-- Posisi Alat --}}
        <section>
            <div class="mb-4 flex items-end justify-between gap-3">
                <div>
                    <h2 class="font-display text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">Posisi Alat Terkini</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Diperbarui otomatis dari laporan harian operator</p>
                </div>
                <a href="{{ route('alat.index') }}" class="hidden shrink-0 rounded-full border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 transition hover:border-mandau-blue hover:text-mandau-blue sm:block">Lihat semua</a>
            </div>
            @if (count($data['alat']) === 0)
                <x-empty-state icon="mappin" title="Belum ada alat terdaftar" sub="Tambahkan alat pertama di menu Data Alat." />
            @else
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($data['alat'] as $a)
                        <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-100">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <span class="font-num rounded-lg bg-[#EFF4FF] px-2.5 py-1 text-xs font-bold text-mandau-blue">{{ $a['kode'] ?: 'EX-??' }}</span>
                                    <h3 class="font-display font-extrabold text-slate-900">{{ $a['nama'] }}</h3>
                                </div>
                                <x-status-badge :status="$a['status']" />
                            </div>
                            <div class="mt-4 space-y-2 text-sm">
                                <p class="flex items-center gap-2 text-slate-700">
                                    <x-icon name="mappin" class="h-4 w-4 shrink-0 text-mandau-blue" />
                                    <span class="truncate">{{ $a['posisi_nama'] ?: 'Belum ada lokasi' }}</span>
                                </p>
                                <p class="flex items-center gap-2 text-slate-700">
                                    <x-icon name="user" class="h-4 w-4 shrink-0 text-mandau-blue" />
                                    <span class="truncate">{{ $a['posisi_operator'] ?: 'Belum ada operator' }}</span>
                                </p>
                            </div>
                            <p class="mt-3 border-t border-slate-100 pt-3 text-xs font-semibold text-slate-400">
                                Update: {{ $a['posisi_updated'] ? Format::tgl($a['posisi_updated']) : '-' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Kontrak / Lokasi --}}
        <section>
            <div class="mb-4">
                <h2 class="font-display text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">Kontrak / Lokasi Kerja</h2>
                <p class="mt-0.5 text-sm text-slate-500">Daftar lokasi pekerjaan yang sedang berjalan</p>
            </div>
            @if (count($data['proyek']) === 0)
                <x-empty-state icon="mappin" title="Belum ada kontrak / lokasi" sub="Tambahkan di menu Kontrak / Lokasi." />
            @else
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($data['proyek'] as $p)
                        <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-100">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h3 class="truncate font-display font-extrabold text-slate-900">{{ $p['nama'] }}</h3>
                                    <p class="mt-0.5 flex items-center gap-1 text-xs text-slate-500">
                                        <x-icon name="mappin" class="h-3.5 w-3.5" /> {{ $p['lokasi'] ?: 'Lokasi belum diisi' }}
                                    </p>
                                </div>
                                <span class="shrink-0 rounded-full bg-[#EFF4FF] px-2.5 py-1 text-xs font-bold text-mandau-blue">{{ $p['alat_count'] }} alat</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Laporan & Kas terbaru --}}
        <section class="grid gap-4 lg:grid-cols-2">
            <div class="min-w-0">
                <h2 class="mb-4 font-display text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">Laporan Terbaru</h2>
                <div class="flex snap-x gap-3 overflow-x-auto pb-1 no-scrollbar">
                    @forelse ($data['recent_laporan'] as $l)
                        <div class="flex w-[280px] min-w-[280px] shrink-0 snap-start items-center gap-3 rounded-2xl bg-white p-4 shadow-soft ring-1 ring-slate-100">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#EFF4FF] text-mandau-blue">
                                <x-icon name="clipboard" class="h-5 w-5" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-slate-800">{{ $l['alat_kode'] }} · {{ $l['proyek_nama'] }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $l['operator_nama'] }} · {{ Format::tgl($l['tanggal']) }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="font-num text-sm font-bold text-slate-800">{{ $l['hm_awal'] }}–{{ $l['hm_akhir'] }} HM</p>
                                @if ($l['solar_jerigen'] !== null)
                                    <p class="font-num flex items-center justify-end gap-1 text-xs text-slate-500"><x-icon name="fuel" class="h-3 w-3" /> {{ $l['solar_jerigen'] }} jrg ({{ $l['solar_liter'] }} L)</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="w-full"><x-empty-state icon="clipboard" title="Belum ada laporan" sub="Laporan harian operator akan muncul di sini." /></div>
                    @endforelse
                </div>
            </div>
            <div class="min-w-0">
                <h2 class="mb-4 font-display text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">Kas Terbaru</h2>
                <div class="flex snap-x gap-3 overflow-x-auto pb-1 no-scrollbar">
                    @forelse ($data['recent_kas'] as $k)
                        <div class="flex w-[280px] min-w-[280px] shrink-0 snap-start items-center gap-3 rounded-2xl bg-white p-4 shadow-soft ring-1 ring-slate-100">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full {{ $k['jenis'] === 'masuk' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500' }}">
                                <x-icon :name="$k['jenis'] === 'masuk' ? 'arrowupright' : 'arrowdownright'" class="h-5 w-5" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-slate-800">{{ $k['keterangan'] ?: 'Tanpa keterangan' }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $k['proyek_nama'] ?: 'Umum' }} · {{ Format::tgl($k['tanggal']) }}</p>
                            </div>
                            <p class="font-num shrink-0 text-sm font-bold {{ $k['jenis'] === 'masuk' ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $k['jenis'] === 'masuk' ? '+' : '-' }}{{ Format::rupiah($k['nominal']) }}
                            </p>
                        </div>
                    @empty
                        <div class="w-full"><x-empty-state icon="wallet" title="Belum ada transaksi kas" sub="Catat kas masuk/keluar di Buku Kas." /></div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Service terbaru --}}
        <section>
            <h2 class="mb-4 font-display text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">Riwayat Service Terbaru</h2>
            <div class="space-y-3">
                @forelse ($data['recent_service'] as $s)
                    <div class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-soft ring-1 ring-slate-100">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#EFF4FF] text-mandau-blue">
                            <x-icon name="fuel" class="h-5 w-5" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $s['alat_kode'] }} · {{ $s['alat_nama'] }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $s['jenis'] ?: 'Service' }} · {{ Format::tgl($s['tanggal']) }}</p>
                        </div>
                        @if ($s['biaya'] !== null)
                            <p class="font-num shrink-0 text-sm font-bold text-slate-700">{{ Format::rupiah($s['biaya']) }}</p>
                        @endif
                    </div>
                @empty
                    <x-empty-state icon="fuel" title="Belum ada riwayat service" sub="Catat service alat di menu Riwayat Service." />
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
