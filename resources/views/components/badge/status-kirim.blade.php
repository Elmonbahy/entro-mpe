@props(['status' => null])

@php
  use App\Enums\StatusKirim;

  $statusValue = $status instanceof StatusKirim ? $status->value : $status;

  $statusEnum = StatusKirim::tryFrom($statusValue);

  $statusLabel = $statusEnum ? $statusEnum->label() : 'Tidak ada data';
  $statusClass = match ($statusEnum) {
      StatusKirim::PENDING => 'text-danger',
      StatusKirim::PARTIAL => 'text-warning',
      StatusKirim::SHIPPED => 'text-success',
      default => 'text-muted',
  };

  $statusIcon = match ($statusEnum) {
      StatusKirim::PENDING => 'bi bi-clock',
      StatusKirim::PARTIAL => 'bi bi-exclamation-circle',
      StatusKirim::SHIPPED => 'bi bi-truck',
      default => '',
  };
@endphp

<span class="fw-bold {{ $statusClass }}">
  @if ($statusIcon)
    <i class="{{ $statusIcon }}"></i>
  @endif
  {{ $statusLabel }}
</span>
