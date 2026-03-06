@props(['status' => null])

@php
  use App\Enums\StatusBayar;

  $statusValue = $status instanceof StatusBayar ? $status->value : $status;

  $statusEnum = StatusBayar::tryFrom($statusValue);

  $statusLabel = $statusEnum ? $statusEnum->label() : 'Tidak ada data';
  $statusClass = match ($statusEnum) {
      StatusBayar::UNPAID => 'text-danger',
      StatusBayar::PAID => 'text-success',
      default => 'text-muted',
  };

  $statusIcon = match ($statusEnum) {
      StatusBayar::UNPAID => 'bi bi-x-circle',
      StatusBayar::PAID => 'bi bi-check-circle',
      default => '',
  };
@endphp

<span class="fw-bold {{ $statusClass }}">
  @if ($statusIcon)
    <i class="{{ $statusIcon }}"></i>
  @endif
  {{ $statusLabel }}
</span>
