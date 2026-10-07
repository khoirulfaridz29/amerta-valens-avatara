@php use App\Support\Format; @endphp

<x-app-layout title="Data Alat">
    @php $isBos = auth()->user()->isBos(); @endphp
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Data Alat</h1>
                <p class="mt-1 text-sm text-slate-500">{{ collect($items)->where('active', true)->count() }} alat aktif dari {{ count($items) }} total</p>
            </div>
            @if ($isBos)
                <button type="button" data-open-alat
                        class="flex h-11 items-center gap-2 rounded-full bg-mandau-blue px-5 text-sm font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-95">
                    <x-icon name="plus" class="h-4 w-4" /> Tambah Alat
                </button>
            @endif
        </div>

        @if (count($items) === 0)
            <x-empty-state icon="truck" title="Belum ada alat" :sub="$isBos ? 'Tambahkan alat pertama agar operator bisa mengirim laporan.' : 'Belum ada alat aktif. Hubungi Bos.'" />
        @else
            {{-- Desktop table --}}
            <div class="hidden overflow-hidden rounded-3xl bg-white shadow-soft ring-1 ring-slate-100 md:block">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-4">Alat</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Posisi / Proyek</th>
                            <th class="px-5 py-4">Operator</th>
                            <th class="px-5 py-4">Update</th>
                            @if ($isBos) <th class="px-5 py-4 text-right">Aksi</th> @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $a)
                            <tr class="border-t border-slate-100 transition hover:bg-[#EFF4FF]/50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="font-num rounded-lg px-2.5 py-1 text-xs font-bold {{ $a['active'] ? 'bg-[#EFF4FF] text-mandau-blue' : 'bg-slate-100 text-slate-400' }}">{{ $a['kode'] ?: 'EX-??' }}</span>
                                        <div>
                                            <p class="font-bold text-slate-800">{{ $a['nama'] }}</p>
                                            <p class="text-xs text-slate-500">{{ $a['jenis'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4"><x-status-badge :status="$a['status']" /></td>
                                <td class="px-5 py-4 text-slate-600">{!! $a['proyek_nama'] ? e($a['proyek_nama']) : '<span class="italic text-slate-400">Belum ada lokasi</span>' !!}</td>
                                <td class="px-5 py-4 text-slate-600">{!! $a['operator_nama'] ? e($a['operator_nama']) : '<span class="italic text-slate-400">-</span>' !!}</td>
                                <td class="px-5 py-4 text-xs text-slate-500">{{ Format::tgl($a['updated_at']) }}</td>
                                @if ($isBos)
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" aria-label="Ubah {{ $a['nama'] }}" data-edit-alat='@json($a)'
                                                    class="grid h-9 w-9 place-items-center rounded-full border border-slate-200 text-slate-500 transition hover:border-mandau-blue hover:text-mandau-blue">
                                                <x-icon name="pencil" class="h-4 w-4" />
                                            </button>
                                            <form method="POST" action="{{ route('alat.destroy', $a['id']) }}" onsubmit="return confirm('Hapus alat ini?')">
    @csrf @method('DELETE')
    <button type="submit" aria-label="Hapus {{ $a['nama'] }}" class="grid h-9 w-9 place-items-center rounded-full border border-slate-200 text-red-500 transition hover:border-red-400">
        <x-icon name="logout" class="h-4 w-4" />
    </button>
</form>
<form method="POST" action="{{ route('alat.active', $a['id']) }}">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="active" value="{{ $a['active'] ? 0 : 1 }}">
                                                <button type="submit" role="switch" aria-checked="{{ $a['active'] ? 'true' : 'false' }}" aria-label="{{ $a['active'] ? 'Nonaktifkan alat' : 'Aktifkan alat' }}"
                                                        class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition {{ $a['active'] ? 'bg-mandau-blue' : 'bg-slate-300' }}">
                                                    <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition {{ $a['active'] ? 'translate-x-5' : 'translate-x-0.5' }}"></span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="space-y-3 md:hidden">
                @foreach ($items as $a)
                    <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-100">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2.5">
                                <span class="font-num rounded-lg bg-[#EFF4FF] px-2.5 py-1 text-xs font-bold text-mandau-blue">{{ $a['kode'] ?: 'EX-??' }}</span>
                                <div>
                                    <h3 class="font-display font-extrabold text-slate-900">{{ $a['nama'] }}</h3>
                                    <p class="text-xs text-slate-500">{{ $a['jenis'] }}</p>
                                </div>
                            </div>
                            <x-status-badge :status="$a['status']" />
                        </div>
                        <div class="mt-3 text-sm text-slate-600">
                            <p>Posisi: {!! $a['proyek_nama'] ? e($a['proyek_nama']) : '<span class="italic text-slate-400">Belum ada lokasi</span>' !!}</p>
                            <p class="mt-1">Operator: {!! $a['operator_nama'] ? e($a['operator_nama']) : '<span class="italic text-slate-400">-</span>' !!}</p>
                        </div>
                        <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                            <p class="text-xs font-semibold text-slate-400">Update: {{ Format::tgl($a['updated_at']) }}</p>
                            @if ($isBos)
                                <div class="flex items-center gap-3">
                                    <button type="button" aria-label="Ubah {{ $a['nama'] }}" data-edit-alat='@json($a)'
                                            class="grid h-9 w-9 place-items-center rounded-full border border-slate-200 text-slate-500">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </button>
                                    <form method="POST" action="{{ route('alat.destroy', $a['id']) }}" onsubmit="return confirm('Hapus alat ini?')">
    @csrf @method('DELETE')
    <button type="submit" aria-label="Hapus {{ $a['nama'] }}" class="grid h-9 w-9 place-items-center rounded-full border border-slate-200 text-red-500 transition hover:border-red-400">
        <x-icon name="logout" class="h-4 w-4" />
    </button>
</form>
<form method="POST" action="{{ route('alat.active', $a['id']) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="active" value="{{ $a['active'] ? 0 : 1 }}">
                                        <button type="submit" role="switch" aria-checked="{{ $a['active'] ? 'true' : 'false' }}" aria-label="{{ $a['active'] ? 'Nonaktifkan alat' : 'Aktifkan alat' }}"
                                                class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition {{ $a['active'] ? 'bg-mandau-blue' : 'bg-slate-300' }}">
                                            <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition {{ $a['active'] ? 'translate-x-5' : 'translate-x-0.5' }}"></span>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if ($isBos)
        <div id="alat-modal" class="fixed inset-0 z-60 hidden items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" data-close></div>
            <div class="relative w-full max-w-lg rounded-3xl bg-white p-6 shadow-float">
                <h2 id="alat-modal-title" class="font-display text-xl font-extrabold">Tambah Alat</h2>
                <form id="alat-form" method="POST" action="{{ route('alat.store') }}" class="mt-5 space-y-4">
                    @csrf
                    <input type="hidden" name="_method" id="alat-method" value="POST">
                    <div class="space-y-1.5">
                        <label for="alat-nama" class="text-sm font-bold text-slate-700">Nama alat</label>
                        <input id="alat-nama" name="nama" placeholder="cth. CAT 320" required
                               class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label for="alat-jenis" class="text-sm font-bold text-slate-700">Jenis</label>
                            <input id="alat-jenis" name="jenis" value="Excavator"
                                   class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                        </div>
                        <div class="space-y-1.5">
                            <label for="alat-kode" class="text-sm font-bold text-slate-700">Kode / No. lambung</label>
                            <input id="alat-kode" name="kode" placeholder="EX-04"
                                   class="font-num h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base uppercase focus:border-mandau-blue focus:bg-white focus:outline-none">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label for="alat-status" class="text-sm font-bold text-slate-700">Status</label>
                        <select id="alat-status" name="status" class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                            <option value="aktif">Aktif</option>
                            <option value="perbaikan">Perbaikan</option>
                            <option value="idle">Idle</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label for="alat-proyek" class="text-sm font-bold text-slate-700">Posisi / Proyek</label>
                            <select id="alat-proyek" name="proyek_id" class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                                <option value="">— Belum ditugaskan —</option>
                                @foreach ($proyek as $p) <option value="{{ $p->id }}">{{ $p->nama }}</option> @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label for="alat-operator" class="text-sm font-bold text-slate-700">Operator</label>
                            <select id="alat-operator" name="operator_id" class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                                <option value="">— Belum ada —</option>
                                @foreach ($operators as $o) <option value="{{ $o->id }}">{{ $o->name }}</option> @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="h-12 w-full rounded-full bg-mandau-blue text-base font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-[0.98]">Simpan Alat</button>
                </form>
            </div>
        </div>

        <script>
            (function () {
                var modal = document.getElementById('alat-modal');
                var form = document.getElementById('alat-form');
                var title = document.getElementById('alat-modal-title');
                var method = document.getElementById('alat-method');
                var storeUrl = @json(route('alat.store'));
                var updateUrl = @json(route('alat.update', ['alat' => 'ID']));

                function open(data) {
                    form.reset();
                    document.getElementById('alat-nama').value = data.nama || '';
                    document.getElementById('alat-jenis').value = data.jenis || '';
                    document.getElementById('alat-kode').value = data.kode || '';
                    document.getElementById('alat-status').value = data.status || 'aktif';
                    document.getElementById('alat-proyek').value = data.proyek_id || '';
                    document.getElementById('alat-operator').value = data.operator_id || '';
                    if (data.id) { title.textContent = 'Ubah Data Alat'; form.action = updateUrl.replace('ID', data.id); method.value = 'PUT'; }
                    else { title.textContent = 'Tambah Alat'; form.action = storeUrl; method.value = 'POST'; }
                    modal.classList.remove('hidden'); modal.classList.add('flex');
                }
                function close() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

                document.querySelectorAll('[data-open-alat]').forEach(function (b) { b.addEventListener('click', function () { open({}); }); });
                document.querySelectorAll('[data-edit-alat]').forEach(function (b) { b.addEventListener('click', function () { open(JSON.parse(b.dataset.editAlat)); }); });
                document.querySelectorAll('[data-close]').forEach(function (b) { b.addEventListener('click', close); });
                document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
            })();
        </script>
    @endif
</x-app-layout>
