@props(['status' => 'idle'])

@php
    $map = [
        'aktif' => ['label' => 'Aktif', 'cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        'perbaikan' => ['label' => 'Perbaikan', 'cls' => 'bg-amber-50 text-amber-700 border-amber-200'],
        'idle' => ['label' => 'Idle', 'cls' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
    ];
    $s = $map[$status] ?? $map['idle'];
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-bold {{ $s['cls'] }}" data-testid="status-badge-{{ $status }}">
    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $s['label'] }}
</span>
