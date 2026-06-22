@props(['size' => 'default'])

<span {{ $attributes->class(['brand-mark', 'brand-mark--compact' => $size === 'compact']) }} aria-hidden="true">E</span>
