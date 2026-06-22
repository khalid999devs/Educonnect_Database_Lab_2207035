@props(['compact' => false, 'href' => route('home')])

<a {{ $attributes->class(['brand-lockup', 'brand-lockup--compact' => $compact]) }} href="{{ $href }}" aria-label="EduConnect home">
    <x-brand.mark :size="$compact ? 'compact' : 'default'" />
    <span class="brand-lockup__name">EduConnect</span>
</a>
