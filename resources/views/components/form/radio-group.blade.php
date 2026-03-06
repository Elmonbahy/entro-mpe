@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'inline' => false,
    'required' => false,
    'columns' => 1, // New prop for number of columns
])

<div class="form-group">
  @if ($label)
    <label class="d-block mb-2">{{ $label }}</label>
  @endif

  <div class="row">
    @foreach ($options as $optionValue => $optionLabel)
      <div class="col-md-{{ 12 / $columns }}">
        <div class="custom-control custom-radio {{ $inline ? 'd-inline-block mr-3' : 'mb-2' }}">
          <input type="radio" id="{{ $name }}_{{ $optionValue }}" name="{{ $name }}"
            value="{{ $optionValue }}" {{ $value == $optionValue ? 'checked' : '' }} {{ $required ? 'required' : '' }}
            class="custom-control-input @error($name) is-invalid @enderror">
          <label class="custom-control-label" for="{{ $name }}_{{ $optionValue }}">
            {{ $optionLabel }}
          </label>
        </div>
      </div>
    @endforeach
  </div>

  @error($name)
    <div class="invalid-feedback d-block mt-2">
      {{ $message }}
    </div>
  @enderror
</div>
