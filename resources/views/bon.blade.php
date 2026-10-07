@php use App\Support\Format; @endphp

<x-app-layout title="Bon / Tagihan">
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Bon / Tagihan</h1>
                <p class="mt-1 text-sm text-slate-500">Catatan tagihan customer &amp; sisa cicilan</p>
            </div>
            <button type="button" data-open-bon
                    class="flex h-11 items-center gap-2 rounded-full bg-mandau-blue px-5 text-sm font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-95">
                <x-icon name="plus" class="h-4 w-4" /> Tambah Bon
            </button>
        </div>

        {{-- Total piutang --}}
        <div class="rounded-3xl bg-mandau-blue p-5 text-white shadow-float sm:p-6">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-blue-200">Total Piutang (belum lunas)</p>
                <x-icon name="wallet" class="h-5 w-5 text-blue-200" />
            </div>
            <p class="font-num mt-3 text-2xl font-bold">{{ Format::rupiah($piutang) }}</p>
        </div>

        @if (count($items) === 0)
            <x-empty-state icon="clipboard" title="Belum ada bon" sub="Buat bon untuk customer yang membayar menyicil." />
        @else
            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($items as $b)
                    <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-100">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2.5">
                                    <span class="font-num rounded-lg bg-[#EFF4FF] px-2.5 py-1 text-xs font-bold text-mandau-blue">{{ $b['nomor'] }}</span>
                                    @if ($b['lunas'])
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Lunas</span>
                                    @else
                                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-600">Sisa</span>
                                    @endif
                                </div>
                                <p class="mt-2 font-display font-extrabold text-slate-900">{{ $b['customer'] }}</p>
                                <p class="text-xs text-slate-500">
                                    {{ $b['proyek_nama'] ?: 'Umum' }} · {{ Format::tgl($b['tanggal']) }}
                                    @if ($b['jatuh_tempo']) · JT {{ Format::tgl($b['jatuh_tempo']) }} @endif
                                </p>
                                @if ($b['keterangan']) <p class="mt-1 text-xs text-slate-500">{{ $b['keterangan'] }}</p> @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" aria-label="Ubah {{ $b['nomor'] }}" data-edit-bon='@json($b)'
                                        class="grid h-9 w-9 place-items-center rounded-full border border-slate-200 text-slate-500 transition hover:border-mandau-blue hover:text-mandau-blue">
                                    <x-icon name="pencil" class="h-4 w-4" />
                                </button>
                                <form method="POST" action="{{ route('bon.destroy', $b['id']) }}" onsubmit="return confirm('Hapus bon ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" aria-label="Hapus {{ $b['nomor'] }}"
                                            class="grid h-9 w-9 place-items-center rounded-full border border-slate-200 text-red-500 transition hover:border-red-400">
                                        <x-icon name="logout" class="h-4 w-4" />
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="font-num mt-4 space-y-1 border-t border-slate-100 pt-3 text-sm">
                            <div class="flex justify-between"><span class="text-slate-500">Total</span><b class="text-slate-800">{{ Format::rupiah($b['total']) }}</b></div>
                            <div class="flex justify-between"><span class="text-slate-500">Dibayar</span><b class="text-emerald-600">{{ Format::rupiah($b['dibayar']) }}</b></div>
                            <div class="flex justify-between"><span class="text-slate-500">Sisa</span>
                                <b class="{{ $b['lunas'] ? 'text-emerald-600' : 'text-red-500' }}">{{ Format::rupiah($b['sisa']) }}</b>
                            </div>
                        </div>

                        @if (count($b['pembayaran']) > 0)
                            <div class="mt-3 space-y-1 border-t border-slate-100 pt-3">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Riwayat pembayaran</p>
                                @foreach ($b['pembayaran'] as $p)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500">{{ Format::tgl($p['tanggal']) }}{{ $p['keterangan'] ? ' · ' . $p['keterangan'] : '' }}</span>
                                        <span class="font-num font-bold text-emerald-600">+{{ Format::rupiah($p['nominal']) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @unless ($b['lunas'])
                            <p class="mt-3 rounded-xl bg-[#EFF4FF] px-3 py-2 text-[11px] font-semibold text-mandau-blue-deep">
                                Bayar cicilan via <b>Buku Kas → Catat Kas</b> (Kas Masuk) lalu pilih bon ini.
                            </p>
                        @endunless
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div id="bon-modal" class="fixed inset-0 z-60 hidden items-center justify-center p-4" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" data-close></div>
        <div class="relative w-full max-w-md rounded-3xl bg-white p-6 shadow-float">
            <h2 id="bon-modal-title" class="font-display text-xl font-extrabold">Tambah Bon</h2>
            <form id="bon-form" method="POST" action="{{ route('bon.store') }}" class="mt-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="bon-method" value="POST">
                <div class="space-y-1.5">
                    <label for="bon-customer" class="text-sm font-bold text-slate-700">Nama customer</label>
                    <input id="bon-customer" name="customer" placeholder="cth. PT Karya Bengalon" required
                           class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                </div>
                <div class="space-y-1.5">
                    <label for="bon-proyek" class="text-sm font-bold text-slate-700">Kontrak / Lokasi (opsional)</label>
                    <select id="bon-proyek" name="proyek_id" class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                        <option value="">— Belum terkait —</option>
                        @foreach ($proyek as $p) <option value="{{ $p->id }}">{{ $p->nama }}</option> @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label for="bon-tanggal" class="text-sm font-bold text-slate-700">Tanggal</label>
                        <input id="bon-tanggal" name="tanggal" type="date" required value="{{ Format::hariIni() }}"
                               class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label for="bon-jatuh-tempo" class="text-sm font-bold text-slate-700">Jatuh tempo</label>
                        <input id="bon-jatuh-tempo" name="jatuh_tempo" type="date"
                               class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label for="bon-total" class="text-sm font-bold text-slate-700">Total tagihan (Rp)</label>
                    <input id="bon-total" name="total" type="number" min="1" step="1" required placeholder="cth. 45000000"
                           class="font-num h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                </div>
                <div class="space-y-1.5">
                    <label for="bon-keterangan" class="text-sm font-bold text-slate-700">Keterangan (opsional)</label>
                    <textarea id="bon-keterangan" name="keterangan" rows="2" placeholder="cth. Sewa EX-01 10 hari"
                              class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none"></textarea>
                </div>
                <button type="submit" class="h-12 w-full rounded-full bg-mandau-blue text-base font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-[0.98]">Simpan Bon</button>
            </form>
        </div>
    </div>

    <script>
        (function () {
            var modal = document.getElementById('bon-modal');
            var form = document.getElementById('bon-form');
            var title = document.getElementById('bon-modal-title');
            var method = document.getElementById('bon-method');
            var storeUrl = @json(route('bon.store'));
            var updateUrl = @json(route('bon.update', ['bon' => 'ID']));

            function open(data) {
                form.reset();
                document.getElementById('bon-customer').value = data.customer || '';
                document.getElementById('bon-proyek').value = data.proyek_id || '';
                document.getElementById('bon-tanggal').value = data.tanggal || @json(\App\Support\Format::hariIni());
                document.getElementById('bon-jatuh-tempo').value = data.jatuh_tempo || '';
                document.getElementById('bon-total').value = data.total != null ? data.total : '';
                document.getElementById('bon-keterangan').value = data.keterangan || '';
                if (data.id) { title.textContent = 'Ubah Bon'; form.action = updateUrl.replace('ID', data.id); method.value = 'PUT'; }
                else { title.textContent = 'Tambah Bon'; form.action = storeUrl; method.value = 'POST'; }
                modal.classList.remove('hidden'); modal.classList.add('flex');
            }
            function close() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

            document.querySelectorAll('[data-open-bon]').forEach(function (b) { b.addEventListener('click', function () { open({}); }); });
            document.querySelectorAll('[data-edit-bon]').forEach(function (b) { b.addEventListener('click', function () { open(JSON.parse(b.dataset.editBon)); }); });
            document.querySelectorAll('[data-close]').forEach(function (b) { b.addEventListener('click', close); });
        })();
    </script>
</x-app-layout>
