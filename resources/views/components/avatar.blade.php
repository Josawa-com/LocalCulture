@props(['name', 'path' => null, 'size' => 'md'])

@php
$dimensions = match ($size) {
    'xs' => 'h-7 w-7 text-xs',
    'sm' => 'h-9 w-9 text-sm',
    'lg' => 'h-16 w-16 text-xl',
    'xl' => 'h-24 w-24 text-3xl',
    default => 'h-11 w-11 text-base',
};
@endphp

@if($path)
    <img src="{{ Storage::disk('public')->url($path) }}" alt="Foto profil {{ $name }}"
         {{ $attributes->merge(['class' => "$dimensions shrink-0 rounded-full border border-bark-200 object-cover"]) }}>
@else
    <span {{ $attributes->merge(['class' => "$dimensions grid shrink-0 place-items-center rounded-full bg-bark-800 font-semibold text-white"]) }}
          aria-hidden="true">{{ strtoupper(mb_substr($name, 0, 1)) }}</span>
@endif
