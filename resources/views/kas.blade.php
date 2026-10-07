@php use App\Support\Format; @endphp

<x-app-layout title="Buku Kas">
    @php
        $fJenis = request('jenis', 'all');
        $fProyek = request('proyek_id', 'all');
        $fStart = request('start', '');
        $fEnd = request('end', '');
        $summary = $data['summary'];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Buku Kas</h1>
                <p class="mt-1 text-sm text-slate-500">Debit (kas masuk), kredit (kas keluar) &amp; keterangan</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('export.kas') }}"
                   class="flex h-11 items-center gap-2 rounded-full border border-slate-200 bg-white px-4 text-sm font-bold text-slate-600 transition hover:border-mandau-blue hover:text-mandau-blue">
                    <x-icon name="download" class="h-4 w-4" /> CSV
                </a>
                <button type="button" data-open-kas
                        class="flex h-11 items-center gap-2 rounded-full bg-mandau-blue px-5 text-sm font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-95">
                    <x-icon name="plus" class="h-4 w-4" /> Catat Kas
                </button>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
            <div class="col-span-2 rounded-3xl bg-mandau-blue p-5 text-white shadow-float sm:p-6">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-200">Saldo Kas Saat Ini</p>
                    <x-icon name="wallet" class="h-5 w-5 text-blue-200" />
                </div>
                <p class="font-num mt-3 text-2xl font-bold">{{ Format::rupiah($summary['saldo']) }}</p>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-100">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Kas Masuk (Debit)</p>
                    <x-icon name="arrowupright" class="h-5 w-5 text-emerald-500" />
                </div>
                <p class="font-num mt-3 text-lg font-bold text-emerald-600 sm:text-xl">{{ Format::rupiah($summary['masuk']) }}</p>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-100">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Kas Keluar (Kredit)</p>
                    <x-icon name="arrowdownright" class="h-5 w-5 text-red-500" />
                </div>
                <p class="font-num mt-3 text-lg font-bold text-red-500 sm:text-xl">{{ Format::rupiah($summary['keluar']) }}</p>
            </div>
        </div>

        <form method="GET" action="{{ route('kas.index') }}" class="flex flex-wrap items-center gap-2 rounded-3xl bg-white p-4 shadow-soft ring-1 ring-slate-100" id="kas-filter">
            <select name="jenis" onchange="document.getElementById('kas-filter').submit()" class="h-10 w-[130px] rounded-full border border-slate-200 bg-slate-50 px-3 text-sm text-slate-600 focus:border-mandau-blue focus:outline-none">
                <option value="all" @selected($fJenis === 'all')>Semua Jenis</option>
                <option value="masuk" @selected($fJenis === 'masuk')>Masuk</option>
                <option value="keluar" @selected($fJenis === 'keluar')>Keluar</option>
            </select>
            <select name="proyek_id" onchange="document.getElementById('kas-filter').submit()" class="h-10 w-[170px] rounded-full border border-slate-200 bg-slate-50 px-3 text-sm text-slate-600 focus:border-mandau-blue focus:outline-none">
                <option value="all" @selected($fProyek === 'all')>Semua Kontrak</option>
                @foreach ($proyek as $p)
                    <option value="{{ $p->id }}" @selected((string) $fProyek === (string) $p->id)>{{ $p->nama }}</option>
                @endforeach
            </select>
            <input type="date" name="start" value="{{ $fStart }}" aria-label="Dari tanggal" onchange="document.getElementById('kas-filter').submit()"
                   class="h-10 rounded-full border border-slate-200 bg-slate-50 px-3 text-sm text-slate-600 focus:border-mandau-blue focus:outline-none">
            <input type="date" name="end" value="{{ $fEnd }}" aria-label="Sampai tanggal" onchange="document.getElementById('kas-filter').submit()"
                   class="h-10 rounded-full border border-slate-200 bg-slate-50 px-3 text-sm text-slate-600 focus:border-mandau-blue focus:outline-none">
        </form>

        @if (count($data['items']) === 0)
            <x-empty-state icon="wallet" title="Belum ada transaksi" sub="Catat kas masuk/keluar pertama dengan tombol Catat Kas." />
        @else
            <div class="hidden overflow-hidden rounded-3xl bg-white shadow-soft ring-1 ring-slate-100 md:block">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-4">Tanggal</th>
                            <th class="px-5 py-4">Keterangan</th>
                            <th class="px-5 py-4">Kontrak</th>
                            <th class="px-5 py-4">Jenis</th>
                            <th class="px-5 py-4 text-right">Nominal</th>
                            <th class="px-5 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['items'] as $k)
                            <tr class="border-t border-slate-100 transition hover:bg-[#EFF4FF]/50">
                                <td class="px-5 py-4 text-slate-600">{{ Format::tgl($k['tanggal']) }}</td>
                                <td class="px-5 py-4 font-semibold text-slate-800">{{ $k['keterangan'] ?: 'Tanpa keterangan' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $k['proyek_nama'] ?: 'Umum' }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $k['jenis'] === 'masuk' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }}">
                                        {{ $k['jenis'] === 'masuk' ? 'Debit (Masuk)' : 'Kredit (Keluar)' }}
                                    </span>
                                </td>
                                <td class="font-num px-5 py-4 text-right font-bold {{ $k['jenis'] === 'masuk' ? 'text-emerald-600' : 'text-red-500' }}">
                                    {{ $k['jenis'] === 'masuk' ? '+' : '-' }}{{ Format::rupiah($k['nominal']) }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" aria-label="Ubah" data-edit-kas='@json($k)'
                                                class="grid h-9 w-9 place-items-center rounded-full border border-slate-200 text-slate-500 transition hover:border-mandau-blue hover:text-mandau-blue">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                        </button>
                                        <form method="POST" action="{{ route('kas.destroy', $k['id']) }}" onsubmit="return confirm('Hapus transaksi ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="grid h-9 w-9 place-items-center rounded-full border border-slate-200 text-red-500 transition hover:border-red-400">
                                                <x-icon name="logout" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="space-y-3 md:hidden">
                @foreach ($data['items'] as $k)
                    <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-100">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-bold text-slate-800">{{ $k['keterangan'] ?: 'Tanpa keterangan' }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ Format::tgl($k['tanggal']) }} · {{ $k['proyek_nama'] ?: 'Umum' }}</p>
                            </div>
                            <p class="font-num shrink-0 font-bold {{ $k['jenis'] === 'masuk' ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $k['jenis'] === 'masuk' ? '+' : '-' }}{{ Format::rupiah($k['nominal']) }}
                            </p>
                        </div>
                        <div class="mt-3 flex items-center justify-end gap-2 border-t border-slate-100 pt-3">
                            <button type="button" aria-label="Ubah" data-edit-kas='@json($k)'
                                    class="grid h-9 w-9 place-items-center rounded-full border border-slate-200 text-slate-500">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </button>
                            <form method="POST" action="{{ route('kas.destroy', $k['id']) }}" onsubmit="return confirm('Hapus transaksi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="grid h-9 w-9 place-items-center rounded-full border border-slate-200 text-red-500">
                                    <x-icon name="logout" class="h-4 w-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Klasifikasi keuangan --}}
        <div class="grid gap-3 sm:gap-4 lg:grid-cols-2">
            <section class="min-w-0">
                <h2 class="mb-3 font-display text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">Rekap Bulanan</h2>
                <div class="overflow-hidden rounded-3xl bg-white shadow-soft ring-1 ring-slate-100">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <tr><th class="px-5 py-3">Bulan</th><th class="px-5 py-3 text-right">Masuk</th><th class="px-5 py-3 text-right">Keluar</th><th class="px-5 py-3 text-right">Saldo</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($bulanan as $b)
                                <tr class="border-t border-slate-100">
                                    <td class="px-5 py-3 font-semibold text-slate-700">{{ $b['periode'] }}</td>
                                    <td class="font-num px-5 py-3 text-right text-emerald-600">{{ Format::rupiah($b['masuk']) }}</td>
                                    <td class="font-num px-5 py-3 text-right text-red-500">{{ Format::rupiah($b['keluar']) }}</td>
                                    <td class="font-num px-5 py-3 text-right font-bold text-slate-800">{{ Format::rupiah($b['saldo']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-4 text-center text-slate-400">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="min-w-0">
                <h2 class="mb-3 font-display text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">Rekap Tahunan</h2>
                <div class="overflow-hidden rounded-3xl bg-white shadow-soft ring-1 ring-slate-100">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <tr><th class="px-5 py-3">Tahun</th><th class="px-5 py-3 text-right">Masuk</th><th class="px-5 py-3 text-right">Keluar</th><th class="px-5 py-3 text-right">Saldo</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($tahunan as $t)
                                <tr class="border-t border-slate-100">
                                    <td class="px-5 py-3 font-semibold text-slate-700">{{ $t['periode'] }}</td>
                                    <td class="font-num px-5 py-3 text-right text-emerald-600">{{ Format::rupiah($t['masuk']) }}</td>
                                    <td class="font-num px-5 py-3 text-right text-red-500">{{ Format::rupiah($t['keluar']) }}</td>
                                    <td class="font-num px-5 py-3 text-right font-bold text-slate-800">{{ Format::rupiah($t['saldo']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-4 text-center text-slate-400">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <div id="kas-modal" class="fixed inset-0 z-60 hidden items-center justify-center p-4" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" data-close></div>
        <div class="relative w-full max-w-md rounded-3xl bg-white p-6 shadow-float">
            <h2 id="kas-modal-title" class="font-display text-xl font-extrabold">Catat Transaksi Kas</h2>
            <form method="POST" action="{{ route('kas.store') }}" class="mt-5 space-y-4" id="kas-form">
                @csrf
                <input type="hidden" name="_method" id="kas-method" value="POST">
                <input type="hidden" name="jenis" id="kas-jenis" value="keluar">
                <div class="space-y-1.5">
                    <span class="text-sm font-bold text-slate-700">Jenis transaksi</span>
                    <div class="flex gap-3" role="group">
                        <button type="button" data-jenis="masuk" class="kas-jbtn flex-1 rounded-2xl border-2 border-slate-200 bg-slate-50 p-4 text-left transition">
                            <p class="text-sm font-extrabold text-slate-700">Kas Masuk</p>
                            <p class="mt-0.5 text-xs text-slate-500">Debit / pemasukan</p>
                        </button>
                        <button type="button" data-jenis="keluar" class="kas-jbtn flex-1 rounded-2xl border-2 border-slate-200 bg-slate-50 p-4 text-left transition">
                            <p class="text-sm font-extrabold text-slate-700">Kas Keluar</p>
                            <p class="mt-0.5 text-xs text-slate-500">Kredit / pengeluaran</p>
                        </button>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label for="kas-nominal" class="text-sm font-bold text-slate-700">Nominal (Rp)</label>
                    <input id="kas-nominal" name="nominal" type="number" min="1" step="1" required placeholder="cth. 1250000"
                           class="font-num h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label for="kas-tanggal" class="text-sm font-bold text-slate-700">Tanggal</label>
                        <input id="kas-tanggal" name="tanggal" type="date" required value="{{ Format::hariIni() }}"
                               class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label for="kas-proyek" class="text-sm font-bold text-slate-700">Kontrak (opsional)</label>
                        <select id="kas-proyek" name="proyek_id" class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                            <option value="">— Umum —</option>
                            @foreach ($proyek as $p) <option value="{{ $p->id }}">{{ $p->nama }}</option> @endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label for="kas-bon" class="text-sm font-bold text-slate-700">Bon (opsional, untuk kas masuk)</label>
                    <select id="kas-bon" name="bon_id" class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                        <option value="">— Tanpa bon —</option>
                        @foreach ($bons as $b)
                            <option value="{{ $b['id'] }}">{{ $b['nomor'] }} — {{ $b['customer'] }} (sisa {{ Format::rupiah($b['sisa']) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label for="kas-keterangan" class="text-sm font-bold text-slate-700">Keterangan</label>
                    <textarea id="kas-keterangan" name="keterangan" rows="2" placeholder="cth. Solar 2.000 L untuk EX-01"
                              class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none"></textarea>
                </div>
                <button type="submit" class="h-12 w-full rounded-full bg-mandau-blue text-base font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-[0.98]">Simpan Transaksi</button>
            </form>
        </div>
    </div>

    <script>
        (function () {
            var modal = document.getElementById('kas-modal');
            var form = document.getElementById('kas-form');
            var method = document.getElementById('kas-method');
            var jenisInput = document.getElementById('kas-jenis');
            var storeUrl = @json(route('kas.store'));
            var updateUrl = @json(route('kas.update', ['kas' => 'ID']));
            function paint() {
                document.querySelectorAll('.kas-jbtn').forEach(function (b) {
                    var on = b.dataset.jenis === jenisInput.value;
                    var masuk = b.dataset.jenis === 'masuk';
                    b.className = 'kas-jbtn flex-1 rounded-2xl border-2 p-4 text-left transition ' +
                        (on ? (masuk ? 'border-emerald-500 bg-emerald-50' : 'border-red-500 bg-red-50') : 'border-slate-200 bg-slate-50 hover:border-slate-300');
                    var p = b.querySelector('p');
                    p.className = 'text-sm font-extrabold ' + (on ? (masuk ? 'text-emerald-700' : 'text-red-600') : 'text-slate-700');
                });
            }
            function open(data) {
                data = data || {};
                form.reset();
                if (data.id) {
                    document.getElementById('kas-modal-title').textContent = 'Ubah Transaksi Kas';
                    form.action = updateUrl.replace('ID', data.id); method.value = 'PUT';
                } else {
                    document.getElementById('kas-modal-title').textContent = 'Catat Transaksi Kas';
                    form.action = storeUrl; method.value = 'POST';
                }
                jenisInput.value = data.jenis || 'keluar';
                document.getElementById('kas-nominal').value = data.nominal != null ? data.nominal : '';
                document.getElementById('kas-tanggal').value = data.tanggal || @json(\App\Support\Format::hariIni());
                document.getElementById('kas-proyek').value = data.proyek_id || '';
                document.getElementById('kas-bon').value = data.bon_id || '';
                document.getElementById('kas-keterangan').value = data.keterangan || '';
                paint();
                modal.classList.remove('hidden'); modal.classList.add('flex');
            }
            function close() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

            document.querySelectorAll('.kas-jbtn').forEach(function (b) { b.addEventListener('click', function () { jenisInput.value = b.dataset.jenis; paint(); }); });
            document.querySelectorAll('[data-open-kas]').forEach(function (b) { b.addEventListener('click', function () { open({}); }); });
            document.querySelectorAll('[data-edit-kas]').forEach(function (b) { b.addEventListener('click', function () { open(JSON.parse(b.dataset.editKas)); }); });
            document.querySelectorAll('[data-close]').forEach(function (b) { b.addEventListener('click', close); });
            paint();
            @if (request('add')) open({}); @endif
        })();
    </script>
</x-app-layout>
