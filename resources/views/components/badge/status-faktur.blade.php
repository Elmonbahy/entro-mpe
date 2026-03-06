@props(['status' => null])

@php
  use App\Enums\StatusFaktur;

  $statusValue = $status instanceof StatusFaktur ? $status->value : $status;

  $statusEnum = StatusFaktur::tryFrom($statusValue);

  $statusLabel = $statusEnum ? $statusEnum->label() : 'Tidak ada data';
  $statusClass = match ($statusEnum) {
      StatusFaktur::NEW => 'text-dark',
      StatusFaktur::PROCESS_FAKTUR => 'text-warning',
      StatusFaktur::PROCESS_GUDANG => 'text-info',
      StatusFaktur::DONE => 'text-success',
      default => 'text-muted',
  };
@endphp

<span class="fw-bold {{ $statusClass }}">
  {{ $statusLabel }}
</span>
