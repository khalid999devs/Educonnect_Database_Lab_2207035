@props(['compact' => false, 'href' => route('home')])

<a {{ $attributes->class(['brand-lockup', 'brand-lockup--compact' => $compact]) }} href="{{ $href }}" aria-label="Educonnect home">
    <x-brand.mark :size="$compact ? 'compact' : 'default'" />
    <span class="brand-lockup__name">Educonnect</span>
</a>
