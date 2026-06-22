@props(['variant' => 'info', 'dismissible' => false])

<div {{ $attributes->class(['alert', "alert--{$variant}"]) }} role="status" data-alert>
    <div class="alert__content">{{ $slot }}</div>
    @if ($dismissible)
        <button class="alert__dismiss" type="button" data-alert-dismiss aria-label="Dismiss message">Close</button>
    @endif
</div>
