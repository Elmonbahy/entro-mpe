@props(['title' => '', 'withBackButton' => false])

<div>
  <div {{ $attributes->merge(['class' => 'd-flex justify-content-between w-full align-items-center']) }}>
    <h5 class="mb-0 fw-bold">{{ $title }}</h5>
    <div class="d-none d-md-block">
      {{ $slot }}
    </div>
  </div>

  <div class="d-block d-md-none mb-3">
    {{ $slot }}
  </div>
</div>
