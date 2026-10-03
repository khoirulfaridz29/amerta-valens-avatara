@php use App\Support\Format; @endphp

<x-app-layout title="Akun Operator">
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Akun Operator</h1>
                <p class="mt-1 text-sm text-slate-500">Setiap akun punya PIN sendiri untuk masuk</p>
            </div>
            <button type="button" data-open-operator
                    class="flex h-11 items-center gap-2 rounded-full bg-mandau-blue px-5 text-sm font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-95">
                <x-icon name="plus" class="h-4 w-4" /> Tambah Operator
            </button>
        </div>

        @if (count($items) === 0)
            <x-empty-state icon="users" title="Belum ada operator" sub="Buat akun operator agar mereka bisa mengirim laporan harian." />
        @else
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($items as $op)
                    <div class="rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-100 {{ ! $op['active'] ? 'opacity-60' : '' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-gradient-to-br from-[#2D6FF2] to-[#1E429F] text-sm font-extrabold text-white">
                                    {{ Format::initials($op['name']) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate font-bold text-slate-800">{{ $op['name'] }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $op['email'] }}</p>
                                    @if ($op['phone']) <p class="text-xs text-slate-400">{{ $op['phone'] }}</p> @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" aria-label="Ubah {{ $op['name'] }}" data-edit-operator='@json($op)'
                                        class="grid h-9 w-9 place-items-center rounded-full border border-slate-200 text-slate-500 transition hover:border-mandau-blue hover:text-mandau-blue">
                                    <x-icon name="pencil" class="h-4 w-4" />
                                </button>
                                <form method="POST" action="{{ route('operator.update', $op['id']) }}">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="active" value="{{ $op['active'] ? 0 : 1 }}">
                                    <button type="submit" role="switch" aria-checked="{{ $op['active'] ? 'true' : 'false' }}" aria-label="{{ $op['active'] ? 'Nonaktifkan akun' : 'Aktifkan akun' }}"
                                            class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition {{ $op['active'] ? 'bg-mandau-blue' : 'bg-slate-300' }}">
                                        <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition {{ $op['active'] ? 'translate-x-5' : 'translate-x-0.5' }}"></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <p class="mt-4 border-t border-slate-100 pt-3 text-xs font-semibold text-slate-400">{{ $op['laporan_count'] }} laporan terkirim</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div id="operator-modal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" data-close></div>
        <div class="relative w-full max-w-md rounded-3xl bg-white p-6 shadow-float">
            <h2 id="operator-modal-title" class="font-display text-xl font-extrabold">Tambah Akun Operator</h2>
            <form id="operator-form" method="POST" action="{{ route('operator.store') }}" class="mt-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="operator-method" value="POST">
                <div class="space-y-1.5">
                    <label for="op-nama" class="text-sm font-bold text-slate-700">Nama lengkap</label>
                    <input id="op-nama" name="name" placeholder="cth. Andi Pratama" required
                           class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                </div>
                <div class="space-y-1.5">
                    <label for="op-email" class="text-sm font-bold text-slate-700">Email</label>
                    <input id="op-email" name="email" type="email" placeholder="operator@email.com" required
                           class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none disabled:opacity-60">
                </div>
                <div class="space-y-1.5">
                    <label for="op-phone" class="text-sm font-bold text-slate-700">No. HP (opsional)</label>
                    <input id="op-phone" name="phone" type="tel" placeholder="0812-xxxx-xxxx"
                           class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                </div>
                <div class="space-y-1.5">
                    <label for="op-pin" id="op-pin-label" class="text-sm font-bold text-slate-700">PIN</label>
                    <input id="op-pin" name="pin" type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="off" placeholder="4–8 digit angka"
                           class="font-num h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base tracking-widest focus:border-mandau-blue focus:bg-white focus:outline-none">
                    <p id="op-pin-hint" class="text-xs text-slate-400">4–8 digit, dipakai operator untuk masuk</p>
                </div>
                <button type="submit" class="h-12 w-full rounded-full bg-mandau-blue text-base font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-[0.98]">Simpan Akun</button>
            </form>
        </div>
    </div>

    <script>
        (function () {
            var modal = document.getElementById('operator-modal');
            var form = document.getElementById('operator-form');
            var title = document.getElementById('operator-modal-title');
            var method = document.getElementById('operator-method');
            var email = document.getElementById('op-email');
            var pin = document.getElementById('op-pin');
            var storeUrl = @json(route('operator.store'));
            var updateUrl = @json(route('operator.update', ['user' => 'ID']));

            function open(data) {
                form.reset();
                document.getElementById('op-nama').value = data.name || '';
                email.value = data.email || '';
                document.getElementById('op-phone').value = data.phone || '';
                if (data.id) {
                    title.textContent = 'Ubah Operator'; form.action = updateUrl.replace('ID', data.id); method.value = 'PUT';
                    email.disabled = true; pin.required = false;
                    document.getElementById('op-pin-label').textContent = 'PIN baru (opsional)';
                    document.getElementById('op-pin-hint').textContent = 'Kosongkan bila tidak diganti';
                } else {
                    title.textContent = 'Tambah Akun Operator'; form.action = storeUrl; method.value = 'POST';
                    email.disabled = false; pin.required = true;
                    document.getElementById('op-pin-label').textContent = 'PIN';
                    document.getElementById('op-pin-hint').textContent = '4–8 digit, dipakai operator untuk masuk';
                }
                modal.classList.remove('hidden'); modal.classList.add('flex');
            }
            function close() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

            document.querySelectorAll('[data-open-operator]').forEach(function (b) { b.addEventListener('click', function () { open({}); }); });
            document.querySelectorAll('[data-edit-operator]').forEach(function (b) { b.addEventListener('click', function () { open(JSON.parse(b.dataset.editOperator)); }); });
            document.querySelectorAll('[data-close]').forEach(function (b) { b.addEventListener('click', close); });
        })();
    </script>
</x-app-layout>
