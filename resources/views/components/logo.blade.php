@props(['light' => false])

<span class="inline-flex items-center gap-2.5">
    <img src="{{ asset('images/logo.webp') }}" alt="Amerta Valens Avatara"
         class="h-9 w-9 shrink-0 rounded-xl object-cover shadow-lg shadow-blue-900/25">
    <span class="font-display text-sm font-extrabold leading-tight tracking-tight {{ $light ? 'text-white' : 'text-slate-900' }}">Amerta Valens Avatara</span>
</span>
