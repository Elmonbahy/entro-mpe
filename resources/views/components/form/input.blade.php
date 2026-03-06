@props([
    'value' => '',
    'name' => '',
    'disabled' => false,
    'readonly' => false,
])

@php
  $hasError = $errors->has($name);
@endphp


<input {{ $attributes->merge(['class' => 'form-control' . ($hasError ? ' ' : '')]) }} @disabled($disabled)
  @readonly($readonly) value="{{ old($name, $value) }}" name="{{ $name }}" id="{{ $name }}">

@error($name)
  <div class="text-danger mt-1">{{ $message }}</div>
@enderror
