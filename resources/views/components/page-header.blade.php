@props(['title' => '', 'withBackButton' => false])

<div>
  <div {{ $attributes->merge(['class' => 'd-flex justify-content-between w-full align-items-center']) }}>
    <div class="d-flex align-items-center gap-2">
      @if ($withBackButton)
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-link text-decoration-none"><i class="bi bi-arrow-left"></i>
          Kembali</a>
      @endif
      <h5 class="mb-0 fw-bold">{{ $title }}</h5>
    </div>
    <div class="d-none d-md-block">
      {{ $slot }}
    </div>
  </div>

  <div class="d-block d-md-none mb-3">
    {{ $slot }}
  </div>
</div>
