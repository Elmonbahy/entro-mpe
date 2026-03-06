@props(['value', 'optional' => false])

<label {{ $attributes->merge(['class' => 'form-label']) }}>
  {{ $value ?? $slot }}

  @if ($optional)
    <span class="text-info small">(opsional)</span>
  @endif
</label>
