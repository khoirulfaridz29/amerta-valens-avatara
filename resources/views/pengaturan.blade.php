@php use App\Support\Format; @endphp

<x-app-layout title="Pengaturan">
    @php $user = auth()->user(); @endphp
    <div class="max-w-2xl space-y-6">
        <div>
            <h1 class="font-display text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Pengaturan Akun</h1>
            <p class="mt-1 text-sm text-slate-500">Ubah data akun Anda. Email dipakai untuk login.</p>
        </div>

        <form method="POST" action="{{ route('pengaturan.update') }}" class="space-y-4 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-100 sm:p-8">
            @csrf
            @method('PUT')
            @if ($errors->any())
                <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif

            <div class="space-y-1.5">
                <label for="name" class="text-sm font-bold text-slate-700">Nama</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" required
                       class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
            </div>
            <div class="space-y-1.5">
                <label for="email" class="text-sm font-bold text-slate-700">Email (untuk login)</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                       class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                <p class="text-xs text-slate-400">Kalau pakai login Google, samakan dengan email Google Anda.</p>
            </div>
            <div class="space-y-1.5">
                <label for="phone" class="text-sm font-bold text-slate-700">No. HP (opsional)</label>
                <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="0812-xxxx-xxxx"
                       class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
            </div>
            <div class="space-y-1.5">
                <label for="password" class="text-sm font-bold text-slate-700">Password baru (opsional)</label>
                <input id="password" name="password" type="password" autocomplete="new-password" placeholder="••••••••"
                       class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-base focus:border-mandau-blue focus:bg-white focus:outline-none">
                <p class="text-xs text-slate-400">Kosongkan bila tidak diganti. Minimal 6 karakter.</p>
            </div>

            <div class="flex items-center gap-3 pt-1">
                <button type="submit" class="h-12 rounded-full bg-mandau-blue px-6 text-base font-bold text-white shadow-lg shadow-blue-500/30 transition hover:bg-mandau-blue-hover active:scale-[0.98]">Simpan</button>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Role: {{ $user->isBos() ? 'Admin' : 'Operator' }}</span>
            </div>
        </form>

        <div class="rounded-3xl border border-blue-100 bg-[#EFF4FF] p-5 text-sm text-slate-700">
            <p class="font-bold text-mandau-blue-deep">Login Google</p>
            <p class="mt-1">
                @if (config('services.google.client_id'))
                    Login Google <b>aktif</b>. Masuk memakai email Google yang <b>sama</b> dengan email akun di atas.
                @else
                    Belum dikonfigurasi (kredensial Google belum diisi). Sementara login memakai email &amp; password.
                @endif
            </p>
        </div>
    </div>
</x-app-layout>
