@props([
    'value' => '',
    'name' => '',
    'disabled' => false,
    'readonly' => false,
])

@php
  $hasError = $errors->has($name);
@endphp

<textarea {{ $attributes }} {{ $attributes->class(['form-control', 'is-invalid' => $hasError]) }}
  @disabled($disabled) @readonly($readonly) name="{{ $name }}" id="{{ $name }}">{{ old($name, $value) }}</textarea>

@error($name)
  <div class="text-danger mt-1">{{ $message }}</div>
@enderror
