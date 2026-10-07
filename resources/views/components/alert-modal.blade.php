{{-- Alert modal (gaya ref, warna biru) — panggil via window.avaAlert({...}) --}}
<div id="ava-alert" class="fixed inset-0 z-100 hidden items-center justify-center p-4" role="alertdialog" aria-modal="true" aria-labelledby="ava-alert-title">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" data-alert-close></div>

    <div class="relative w-full max-w-lg overflow-hidden rounded-[28px] bg-gradient-to-br from-[#2D6FF2] to-[#1E429F] p-6 text-white shadow-float sm:p-8">
        <button type="button" data-alert-close aria-label="Tutup"
                class="absolute right-4 top-4 grid h-9 w-9 place-items-center rounded-full bg-white/15 text-white transition hover:bg-white/25">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>
        </button>

        <div class="flex flex-col items-center gap-5 sm:flex-row sm:items-center sm:gap-7">
            <div class="grid h-24 w-24 shrink-0 place-items-center rounded-3xl bg-white/15">
                <svg class="h-12 w-12 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m3 11 18-5v12L3 14v-3z" /><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6" />
                </svg>
            </div>

            <div class="flex-1 text-center sm:text-left">
                <h3 id="ava-alert-title" class="font-display text-2xl font-extrabold tracking-tight">Hey, Wait!!</h3>
                <p id="ava-alert-message" class="mt-2 text-sm leading-relaxed text-blue-100">Pesan.</p>
                <div class="mt-5 flex flex-col gap-2 sm:flex-row">
                    <button type="button" id="ava-alert-cancel" data-alert-close
                            class="h-11 rounded-xl bg-white/90 px-5 text-sm font-bold text-mandau-blue-deep transition hover:bg-white">Tutup</button>
                    <button type="button" id="ava-alert-confirm"
                            class="h-11 rounded-xl border border-white/40 px-5 text-sm font-bold text-white transition hover:bg-white/10">Mengerti</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.avaAlert = function (opts) {
        opts = opts || {};
        var box = document.getElementById('ava-alert');
        if (!box) { alert(opts.message || ''); return; }
        document.getElementById('ava-alert-title').textContent = opts.title || 'Hey, Wait!!';
        document.getElementById('ava-alert-message').textContent = opts.message || '';
        document.getElementById('ava-alert-cancel').textContent = opts.cancelText || 'Tutup';
        document.getElementById('ava-alert-confirm').textContent = opts.confirmText || 'Mengerti';
        var close = function () {
            box.classList.add('hidden');
            box.classList.remove('flex');
            if (typeof opts.onClose === 'function') opts.onClose();
        };
        var confirm = document.getElementById('ava-alert-confirm');
        confirm.onclick = function () { close(); if (typeof opts.onConfirm === 'function') opts.onConfirm(); };
        box.querySelectorAll('[data-alert-close]').forEach(function (el) { el.onclick = close; });
        box.classList.remove('hidden'); box.classList.add('flex');
    };
</script>
