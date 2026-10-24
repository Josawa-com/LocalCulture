@props(['type' => 'info'])

@php
$styles = [
    'success' => ['bg-emerald-50 border-emerald-200 text-emerald-900', '✓'],
    'error' => ['bg-rose-50 border-rose-200 text-rose-900', '⚠️'],
    'info' => ['bg-sky-50 border-sky-200 text-sky-900', 'ℹ️'],
];
[$bg, $icon] = $styles[$type] ?? $styles['info'];
@endphp

<div {{ $attributes->merge(['class' => "flex items-start gap-2 rounded-lg border px-4 py-3 text-sm font-medium $bg"]) }}>
    <span>{{ $icon }}</span>
    <div>{{ $slot }}</div>
</div>
