@php use App\Support\Format; @endphp

<x-app-layout title="Riwayat Service">
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Riwayat Service</h1>
                <p class="mt-1 text-sm text-slate-500">Catatan perawatan &amp; perbaikan alat</p>
            </div>
            <button type="button" data-open-service
                    class="flex h-11 items-center gap-2 rounded-full bg-mandau-blue px-5 text-sm font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-95">
                <x-icon name="plus" class="h-4 w-4" /> Tambah Service
            </button>
        </div>

        @if (count($items) === 0)
            <x-empty-state icon="fuel" title="Belum ada riwayat service" sub="Catat service alat agar riwayat perawatan tersimpan." />
        @else
            <div class="hidden overflow-hidden rounded-3xl bg-white shadow-soft ring-1 ring-slate-100 md:block">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-4">Tanggal</th>
                            <th class="px-5 py-4">HM</th>
                            <th class="px-5 py-4">Alat</th>
                            <th class="px-5 py-4">Jenis</th>
                            <th class="px-5 py-4">Keterangan</th>
                            <th class="px-5 py-4 text-right">Biaya</th>
                            <th class="px-5 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $s)
                            <tr class="border-t border-slate-100 transition hover:bg-[#EFF4FF]/50">
                                <td class="px-5 py-4 text-slate-600">{{ Format::tgl($s['tanggal']) }}</td>
                                <td class="font-num px-5 py-4 font-bold text-slate-700">{{ $s['hm'] !== null ? Format::hm($s['hm']) : '-' }}</td>
                                <td class="px-5 py-4">
                                    <span class="font-num rounded-lg bg-[#EFF4FF] px-2.5 py-1 text-xs font-bold text-mandau-blue">{{ $s['alat_kode'] }}</span>
                                    <span class="ml-2 font-semibold text-slate-800">{{ $s['alat_nama'] }}</span>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $s['jenis'] ?: 'Service' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $s['keterangan'] ?: '-' }}</td>
                                <td class="font-num px-5 py-4 text-right font-bold text-slate-800">{{ $s['biaya'] !== null ? Format::rupiah($s['biaya']) : '-' }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" aria-label="Ubah" data-edit-service='@json($s)'
                                                class="grid h-9 w-9 place-items-center rounded-full border border-slate-200 text-slate-500 transition hover:border-mandau-blue hover:text-mandau-blue">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                        </button>
                                        <form method="POST" action="{{ route('service.destroy', $s['id']) }}" onsubmit="return confirm('Hapus riwayat service ini?')">
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
                @foreach ($items as $s)
                    <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-100">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-bold text-slate-800">{{ $s['alat_kode'] }} · {{ $s['alat_nama'] }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $s['jenis'] ?: 'Service' }} · {{ Format::tgl($s['tanggal']) }}{{ $s['hm'] !== null ? ' · ' . Format::hm($s['hm']) : '' }}</p>
                                @if ($s['keterangan']) <p class="mt-2 text-sm text-slate-600">{{ $s['keterangan'] }}</p> @endif
                            </div>
                            @if ($s['biaya'] !== null)
                                <p class="font-num shrink-0 font-bold text-slate-800">{{ Format::rupiah($s['biaya']) }}</p>
                            @endif
                        </div>
                        <div class="mt-3 flex items-center justify-end gap-2 border-t border-slate-100 pt-3">
                            <button type="button" aria-label="Ubah" data-edit-service='@json($s)'
                                    class="grid h-9 w-9 place-items-center rounded-full border border-slate-200 text-slate-500">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </button>
                            <form method="POST" action="{{ route('service.destroy', $s['id']) }}" onsubmit="return confirm('Hapus riwayat service ini?')">
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
    </div>

    <div id="service-modal" class="fixed inset-0 z-60 hidden items-center justify-center p-4" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" data-close></div>
        <div class="relative w-full max-w-md rounded-3xl bg-white p-6 shadow-float">
            <h2 id="service-modal-title" class="font-display text-xl font-extrabold">Tambah Riwayat Service</h2>
            <form id="service-form" method="POST" action="{{ route('service.store') }}" class="mt-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="service-method" value="POST">
                <div class="space-y-1.5">
                    <label for="sv-alat" class="text-sm font-bold text-slate-700">Alat</label>
                    <select id="sv-alat" name="alat_id" required class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                        <option value="">Pilih alat</option>
                        @foreach ($alat as $a) <option value="{{ $a->id }}">{{ $a->kode }} — {{ $a->nama }}</option> @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label for="sv-tanggal" class="text-sm font-bold text-slate-700">Tanggal</label>
                        <input id="sv-tanggal" name="tanggal" type="date" required value="{{ Format::hariIni() }}"
                               class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label for="sv-hm" class="text-sm font-bold text-slate-700">HM Service</label>
                        <input id="sv-hm" name="hm" type="number" min="0" step="0.1" placeholder="cth. 1200"
                               class="font-num h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label for="sv-jenis" class="text-sm font-bold text-slate-700">Jenis</label>
                    <input id="sv-jenis" name="jenis" placeholder="cth. Servis berkala" list="sv-jenis-list"
                           class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                    <datalist id="sv-jenis-list">
                        <option value="Servis berkala"></option><option value="Ganti oli"></option><option value="Perbaikan"></option><option value="Ganti sparepart"></option>
                    </datalist>
                </div>
                <div class="space-y-1.5">
                    <label for="sv-biaya" class="text-sm font-bold text-slate-700">Biaya (Rp)</label>
                    <input id="sv-biaya" name="biaya" type="number" min="0" step="1" placeholder="cth. 1500000"
                           class="font-num h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                </div>
                <div class="space-y-1.5">
                    <label for="sv-keterangan" class="text-sm font-bold text-slate-700">Keterangan</label>
                    <textarea id="sv-keterangan" name="keterangan" rows="2" placeholder="cth. Ganti oli & filter"
                              class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none"></textarea>
                </div>
                <button type="submit" class="h-12 w-full rounded-full bg-mandau-blue text-base font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-[0.98]">Simpan Service</button>
            </form>
        </div>
    </div>

    <script>
        (function () {
            var modal = document.getElementById('service-modal');
            var form = document.getElementById('service-form');
            var title = document.getElementById('service-modal-title');
            var method = document.getElementById('service-method');
            var storeUrl = @json(route('service.store'));
            var updateUrl = @json(route('service.update', ['service' => 'ID']));

            function open(data) {
                form.reset();
                document.getElementById('sv-alat').value = data.alat_id || '';
                document.getElementById('sv-tanggal').value = data.tanggal || @json(\App\Support\Format::hariIni());
                document.getElementById('sv-hm').value = data.hm != null ? data.hm : '';
                document.getElementById('sv-jenis').value = data.jenis || '';
                document.getElementById('sv-biaya').value = data.biaya != null ? data.biaya : '';
                document.getElementById('sv-keterangan').value = data.keterangan || '';
                if (data.id) { title.textContent = 'Ubah Riwayat Service'; form.action = updateUrl.replace('ID', data.id); method.value = 'PUT'; }
                else { title.textContent = 'Tambah Riwayat Service'; form.action = storeUrl; method.value = 'POST'; }
                modal.classList.remove('hidden'); modal.classList.add('flex');
            }
            function close() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

            document.querySelectorAll('[data-open-service]').forEach(function (b) { b.addEventListener('click', function () { open({}); }); });
            document.querySelectorAll('[data-edit-service]').forEach(function (b) { b.addEventListener('click', function () { open(JSON.parse(b.dataset.editService)); }); });
            document.querySelectorAll('[data-close]').forEach(function (b) { b.addEventListener('click', close); });
        })();
    </script>
</x-app-layout>
