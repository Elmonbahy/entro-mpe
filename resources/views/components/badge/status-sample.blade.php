@props(['status' => null])

@php
  use App\Enums\StatusSample;

  $statusValue = $status instanceof StatusSample ? $status->value : $status;

  $statusEnum = StatusSample::tryFrom($statusValue);

  $statusLabel = $statusEnum ? $statusEnum->label() : 'Tidak ada data';
  $statusClass = match ($statusEnum) {
      StatusSample::NEW => 'text-dark',
      StatusSample::PROCESS_SAMPLE => 'text-warning',
      StatusSample::PROCESS_GUDANG => 'text-info',
      StatusSample::DONE => 'text-success',
      default => 'text-muted',
  };
@endphp

<span class="fw-bold {{ $statusClass }}">
  {{ $statusLabel }}
</span>
