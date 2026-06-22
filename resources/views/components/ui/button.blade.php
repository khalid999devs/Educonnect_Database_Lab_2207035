@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'block' => false,
])

@php
    $classes = [
        'button',
        "button--{$variant}",
        'button--block' => $block,
    ];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
