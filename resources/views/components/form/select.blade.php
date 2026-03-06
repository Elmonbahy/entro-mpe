@props([
    'name',
    'placeholder' => '',
    'options' => [],
    'selected' => null,
    'multiple' => false,
    'disabled' => false,
    'valueKey' => 'value', // for object options
    'labelKey' => 'label', // for object options
])

<select name="{{ $name }}" id="{{ $name }}" class="form-select @error($name) is-invalid @enderror"
  {{ $attributes }} placeholder="{{ $placeholder }}" {{ $multiple ? 'multiple' : '' }}
  {{ $disabled ? 'disabled' : '' }}>
  <option value=""></option>

  @foreach ($options as $option)
    @if (is_string($option))
      <!-- If it's a string, use it for both value and label -->
      <option value="{{ $option }}"
        {{ (is_array($selected) && in_array($option, $selected)) || $selected == $option ? 'selected' : '' }}>
        {{ $option }}
      </option>
    @elseif(is_object($option))
      <!-- If it's an object, use the provided value and label keys -->
      <option value="{{ $option->{$valueKey} }}"
        {{ (is_array($selected) && in_array($option->{$valueKey}, $selected)) || $selected == $option->{$valueKey} ? 'selected' : '' }}>
        {{ $option->{$labelKey} }}
      </option>
    @endif
  @endforeach
</select>

<!-- Show error message if any -->
@error($name)
  <div class="text-danger mt-1">{{ $message }}</div>
@enderror
