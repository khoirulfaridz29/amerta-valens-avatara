@props(['icon' => 'mappin', 'title' => 'Kosong', 'sub' => null])

<div class="grid place-items-center rounded-3xl border border-dashed border-slate-300 bg-slate-50/60 px-6 py-12 text-center">
    <x-icon :name="$icon" class="mb-3 h-8 w-8 text-slate-400" />
    <p class="font-bold text-slate-700">{{ $title }}</p>
    @if ($sub)
        <p class="mt-1 max-w-xs text-sm text-slate-500">{{ $sub }}</p>
    @endif
    @if (trim($slot ?? '') !== '')
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>
