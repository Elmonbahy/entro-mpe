@props(['status' => null])

@php
  use App\Enums\StatusBarangMasuk;

  $statusValue = $status instanceof StatusBarangMasuk ? $status->value : $status;

  $statusEnum = StatusBarangMasuk::tryFrom($statusValue);

  $statusLabel = $statusEnum ? $statusEnum->label() : 'Tidak ada data';
  $statusClass = match ($statusEnum) {
      StatusBarangMasuk::BELUM_LENGKAP => 'text-warning',
      StatusBarangMasuk::LENGKAP => 'text-success',
      default => 'text-muted',
  };
@endphp

<span class="fw-bold {{ $statusClass }}">
  {{ $statusLabel }}
</span>
