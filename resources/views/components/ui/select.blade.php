@props([
    'name',
    'label',
    'hint' => null,
])

@php
    $selectId = $attributes->get('id', $name);
    $messageId = $errors->has($name) ? "{$selectId}-error" : ($hint ? "{$selectId}-hint" : null);
@endphp

<div class="field">
    <label class="field__label" for="{{ $selectId }}">{{ $label }}</label>
    <select
        id="{{ $selectId }}"
        name="{{ $name }}"
        @if ($messageId) aria-describedby="{{ $messageId }}" @endif
        @error($name) aria-invalid="true" @enderror
        {{ $attributes->except('id')->class(['field__control field__control--select', 'field__control--invalid' => $errors->has($name)]) }}
    >
        {{ $slot }}
    </select>

    @error($name)
        <p class="field__message field__message--error" id="{{ $selectId }}-error">{{ $message }}</p>
    @elseif ($hint)
        <p class="field__message" id="{{ $selectId }}-hint">{{ $hint }}</p>
    @enderror
</div>
