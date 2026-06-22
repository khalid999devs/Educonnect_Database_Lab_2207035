@props([
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'hint' => null,
])

@php
    $inputId = $attributes->get('id', $name);
    $messageId = $errors->has($name) ? "{$inputId}-error" : ($hint ? "{$inputId}-hint" : null);
@endphp

<div class="field">
    <label class="field__label" for="{{ $inputId }}">{{ $label }}</label>
    <input
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        @if ($messageId) aria-describedby="{{ $messageId }}" @endif
        @error($name) aria-invalid="true" @enderror
        {{ $attributes->except('id')->class(['field__control', 'field__control--invalid' => $errors->has($name)]) }}
    >

    @error($name)
        <p class="field__message field__message--error" id="{{ $inputId }}-error">{{ $message }}</p>
    @elseif ($hint)
        <p class="field__message" id="{{ $inputId }}-hint">{{ $hint }}</p>
    @enderror
</div>
