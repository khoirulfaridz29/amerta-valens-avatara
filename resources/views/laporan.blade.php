@php use App\Support\Format; @endphp

<x-app-layout title="Laporan Harian">
    @php
        $isBos = auth()->user()->isBos();
        $tab = $isBos ? 'riwayat' : request('tab', 'lapor');
        $fAlat = request('alat_id', 'all');
        $fProyek = request('proyek_id', 'all');
        $fBulan = request('bulan', '');
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                    {{ $isBos ? 'Laporan Harian Operator' : 'Lapor Harian' }}
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $isBos ? 'Pantau laporan per hari & per beberapa bulan' : 'Selesai dalam waktu kurang dari 1 menit' }}
                </p>
            </div>
            @if ($isBos)
                <a href="{{ route('export.laporan') }}"
                   class="flex h-11 items-center gap-2 rounded-full border border-slate-200 bg-white px-4 text-sm font-bold text-slate-600 transition hover:border-mandau-blue hover:text-mandau-blue">
                    <x-icon name="download" class="h-4 w-4" /> CSV
                </a>
            @endif
        </div>

        @unless ($isBos)
            <div class="flex rounded-full bg-slate-100 p-1" role="tablist">
                <a href="{{ route('laporan.index', ['tab' => 'lapor']) }}" role="tab"
                   class="flex h-11 flex-1 items-center justify-center rounded-full text-sm font-bold transition {{ $tab === 'lapor' ? 'bg-white text-mandau-blue shadow-md' : 'text-slate-500' }}">Lapor Hari Ini</a>
                <a href="{{ route('laporan.index', ['tab' => 'riwayat']) }}" role="tab"
                   class="flex h-11 flex-1 items-center justify-center rounded-full text-sm font-bold transition {{ $tab === 'riwayat' ? 'bg-white text-mandau-blue shadow-md' : 'text-slate-500' }}">Riwayat Saya</a>
            </div>
        @endunless

        @if (! $isBos && $tab === 'lapor')
            <form id="lap-form" method="POST" action="{{ route('laporan.store') }}" enctype="multipart/form-data" class="space-y-4 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-100 sm:p-8">
                @csrf
                @if ($errors->any())
                    <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
                @endif
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label for="lap-tanggal" class="text-sm font-bold text-slate-700">Tanggal *</label>
                        <input id="lap-tanggal" name="tanggal" type="date" required value="{{ old('tanggal', Format::hariIni()) }}"
                               class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label for="lap-alat" class="text-sm font-bold text-slate-700">Alat *</label>
                        <select id="lap-alat" name="alat_id" required class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                            <option value="">Pilih alat</option>
                            @foreach ($alat as $a) <option value="{{ $a->id }}" @selected(old('alat_id') == $a->id)>{{ $a->kode }} — {{ $a->nama }}</option> @endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label for="lap-proyek" class="text-sm font-bold text-slate-700">Lokasi / Kontrak *</label>
                    <select id="lap-proyek" name="proyek_id" required class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                        <option value="">Pilih lokasi / kontrak</option>
                        @foreach ($proyek as $p) <option value="{{ $p->id }}" @selected(old('proyek_id') == $p->id)>{{ $p->nama }}</option> @endforeach
                    </select>
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="space-y-1.5">
                        <label for="lap-hm-awal" class="text-sm font-bold text-slate-700">HM Awal *</label>
                        <input id="lap-hm-awal" name="hm_awal" type="number" min="0" step="0.1" required value="{{ old('hm_awal') }}" placeholder="cth. 1200"
                               class="font-num h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label for="lap-hm-akhir" class="text-sm font-bold text-slate-700">HM Akhir *</label>
                        <input id="lap-hm-akhir" name="hm_akhir" type="number" min="0" step="0.1" required value="{{ old('hm_akhir') }}" placeholder="cth. 1208.5"
                               class="font-num h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label for="lap-solar" class="text-sm font-bold text-slate-700">Solar (Jerigen)</label>
                        <input id="lap-solar" name="solar_jerigen" type="number" min="0" step="0.1" value="{{ old('solar_jerigen') }}" placeholder="1 jerigen = 35 L" oninput="perbaruiLiter()"
                               class="font-num h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                        <p class="text-xs text-slate-400">Total: <span id="liter" class="font-bold text-mandau-blue">0</span> liter</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach ([['foto_hm_awal', 'Foto HM Awal'], ['foto_hm_akhir', 'Foto HM Selesai'], ['foto_lokasi', 'Foto Lokasi Kerja']] as [$field, $label])
                        <div class="space-y-1.5">
                            <label for="{{ $field }}" class="text-sm font-bold text-slate-700">{{ $label }} *</label>
                            <input id="{{ $field }}" name="{{ $field }}" type="file" accept="image/*" capture="environment" required
                                   class="block w-full rounded-2xl border border-slate-200 bg-slate-50 p-2.5 text-sm text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-[#EFF4FF] file:px-4 file:py-2 file:text-sm file:font-bold file:text-mandau-blue">
                        </div>
                    @endforeach
                </div>

                <div class="space-y-1.5">
                    <label for="lap-ket" class="text-sm font-bold text-slate-700">Keterangan / Kendala</label>
                    <textarea id="lap-ket" name="keterangan" rows="2" placeholder="cth. Hujan siang, pekerjaan dilanjutkan sore"
                              class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">{{ old('keterangan') }}</textarea>
                </div>
                <button type="submit" id="lap-submit" class="flex h-14 w-full items-center justify-center gap-2 rounded-full bg-mandau-blue text-base font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-[0.98] disabled:opacity-70">
                    <x-icon name="send" class="h-5 w-5" /> <span id="lap-submit-text">Kirim Laporan Harian</span>
                </button>
            </form>
        @endif

        @if ($isBos || $tab === 'riwayat')
            @if ($isBos)
                <form method="GET" action="{{ route('laporan.index') }}" class="grid grid-cols-3 gap-2 rounded-3xl bg-white p-4 shadow-soft ring-1 ring-slate-100" id="lap-filter">
                    <select name="alat_id" onchange="document.getElementById('lap-filter').submit()" class="h-10 w-full rounded-full border border-slate-200 bg-slate-50 px-3 text-sm text-slate-600 focus:border-mandau-blue focus:outline-none">
                        <option value="all" @selected($fAlat === 'all')>Semua Alat</option>
                        @foreach ($alat as $a) <option value="{{ $a->id }}" @selected((string) $fAlat === (string) $a->id)>{{ $a->kode }}</option> @endforeach
                    </select>
                    <select name="proyek_id" onchange="document.getElementById('lap-filter').submit()" class="h-10 w-full rounded-full border border-slate-200 bg-slate-50 px-3 text-sm text-slate-600 focus:border-mandau-blue focus:outline-none">
                        <option value="all" @selected($fProyek === 'all')>Semua Kontrak</option>
                        @foreach ($proyek as $p) <option value="{{ $p->id }}" @selected((string) $fProyek === (string) $p->id)>{{ $p->nama }}</option> @endforeach
                    </select>
                    <input type="month" name="bulan" value="{{ $fBulan }}" aria-label="Bulan" onchange="document.getElementById('lap-filter').submit()"
                           class="h-10 w-full rounded-full border border-slate-200 bg-slate-50 px-3 text-sm text-slate-600 focus:border-mandau-blue focus:outline-none">
                </form>
            @endif

            @if (count($items) === 0)
                <x-empty-state icon="clipboard" title="Belum ada laporan" :sub="$isBos ? 'Laporan operator akan muncul di sini setiap hari.' : 'Kirim laporan pertamamu di tab Lapor Hari Ini.'" />
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
                                <span class="rounded-full bg-slate-100 px-3 py-1 font-bold text-slate-700">HM {{ $l['hm_awal'] }}–{{ $l['hm_akhir'] }}</span>
                                @if ($l['solar_jerigen'] !== null)
                                    <span class="flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 font-bold text-slate-700"><x-icon name="fuel" class="h-3 w-3" /> {{ $l['solar_jerigen'] }} jrg ({{ $l['solar_liter'] }} L)</span>
                                @endif
                            </div>
                            @if ($l['keterangan'])
                                <p class="mt-3 border-t border-slate-100 pt-3 text-sm text-slate-600">{{ $l['keterangan'] }}</p>
                            @endif

                            <div class="mt-3 grid grid-cols-3 gap-2">
                                @foreach ([['foto_hm_awal', 'HM Awal'], ['foto_hm_akhir', 'HM Selesai'], ['foto_lokasi', 'Lokasi']] as [$f, $cap])
                                    @if ($l[$f])
                                        <a href="{{ $l[$f] }}" target="_blank" class="group block">
                                            <img src="{{ $l[$f] }}" alt="{{ $cap }}" class="h-20 w-full rounded-xl object-cover ring-1 ring-slate-200">
                                            <span class="mt-1 block text-center text-[10px] font-bold text-slate-400">{{ $cap }}</span>
                                        </a>
                                    @else
                                        <div class="grid h-20 w-full place-items-center rounded-xl bg-slate-50 text-[10px] font-bold text-slate-300 ring-1 ring-slate-200">{{ $cap }}</div>
                                    @endif
                                @endforeach
                            </div>

                            <p class="mt-3 text-xs text-slate-400">Operator: {{ $l['operator_nama'] ?: '-' }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

    @unless ($isBos)
        <script>
            function perbaruiLiter() {
                var j = parseFloat(document.getElementById('lap-solar').value) || 0;
                document.getElementById('liter').textContent = (j * 35).toLocaleString('id-ID');
            }
            perbaruiLiter();

            var lapForm = document.getElementById('lap-form');
            if (lapForm) {
                lapForm.addEventListener('submit', function () {
                    var btn = document.getElementById('lap-submit');
                    btn.disabled = true;
                    document.getElementById('lap-submit-text').textContent = 'Mengirim…';
                });
            }
        </script>
    @endunless
</x-app-layout>
