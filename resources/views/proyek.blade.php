<x-app-layout title="Kontrak / Lokasi">
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Kontrak / Lokasi Kerja</h1>
                <p class="mt-1 text-sm text-slate-500">Daftar kontrak &amp; lokasi pekerjaan</p>
            </div>
            <button type="button" data-open-kontrak
                    class="flex h-11 items-center gap-2 rounded-full bg-mandau-blue px-5 text-sm font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-95">
                <x-icon name="plus" class="h-4 w-4" /> Tambah Kontrak
            </button>
        </div>

        @if (count($items) === 0)
            <x-empty-state icon="mappin" title="Belum ada kontrak / lokasi" sub="Tambahkan kontrak atau lokasi kerja agar operator bisa memilihnya saat mengirim laporan." />
        @else
            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($items as $p)
                    <div class="rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-100">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate font-display text-lg font-extrabold text-slate-900">{{ $p['nama'] }}</h3>
                                <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-500">
                                    <x-icon name="mappin" class="h-4 w-4 shrink-0" /> {{ $p['lokasi'] ?: 'Lokasi belum diisi' }}
                                </p>
                            </div>
                            <button type="button" aria-label="Ubah {{ $p['nama'] }}" data-edit-kontrak='@json($p)'
                                    class="grid h-10 w-10 shrink-0 place-items-center rounded-full border border-slate-200 text-slate-500 transition hover:border-mandau-blue hover:text-mandau-blue">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </button>
                        </div>
                        <p class="mt-4 border-t border-slate-100 pt-3 text-xs font-semibold text-slate-400">{{ $p['alat_count'] }} alat terdaftar</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div id="kontrak-modal" class="fixed inset-0 z-60 hidden items-center justify-center p-4" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" data-close></div>
        <div class="relative w-full max-w-md rounded-3xl bg-white p-6 shadow-float">
            <h2 id="kontrak-modal-title" class="font-display text-xl font-extrabold">Tambah Kontrak</h2>
            <form id="kontrak-form" method="POST" action="{{ route('proyek.store') }}" class="mt-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="kontrak-method" value="POST">
                <div class="space-y-1.5">
                    <label for="kontrak-nama" class="text-sm font-bold text-slate-700">Nama kontrak / pekerjaan</label>
                    <input id="kontrak-nama" name="nama" placeholder="cth. Tol Balikpapan - Samarinda" required
                           class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                </div>
                <div class="space-y-1.5">
                    <label for="kontrak-lokasi" class="text-sm font-bold text-slate-700">Lokasi</label>
                    <input id="kontrak-lokasi" name="lokasi" placeholder="cth. KM 34, Balikpapan"
                           class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                </div>
                <button type="submit" class="h-12 w-full rounded-full bg-mandau-blue text-base font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-[0.98]">Simpan Kontrak</button>
            </form>
        </div>
    </div>

    <script>
        (function () {
            var modal = document.getElementById('kontrak-modal');
            var form = document.getElementById('kontrak-form');
            var title = document.getElementById('kontrak-modal-title');
            var method = document.getElementById('kontrak-method');
            var storeUrl = @json(route('proyek.store'));
            var updateUrl = @json(route('proyek.update', ['proyek' => 'ID']));

            function open(data) {
                form.reset();
                document.getElementById('kontrak-nama').value = data.nama || '';
                document.getElementById('kontrak-lokasi').value = data.lokasi || '';
                if (data.id) { title.textContent = 'Ubah Kontrak'; form.action = updateUrl.replace('ID', data.id); method.value = 'PUT'; }
                else { title.textContent = 'Tambah Kontrak'; form.action = storeUrl; method.value = 'POST'; }
                modal.classList.remove('hidden'); modal.classList.add('flex');
            }
            function close() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

            document.querySelectorAll('[data-open-kontrak]').forEach(function (b) { b.addEventListener('click', function () { open({}); }); });
            document.querySelectorAll('[data-edit-kontrak]').forEach(function (b) { b.addEventListener('click', function () { open(JSON.parse(b.dataset.editKontrak)); }); });
            document.querySelectorAll('[data-close]').forEach(function (b) { b.addEventListener('click', close); });
        })();
    </script>
</x-app-layout>
