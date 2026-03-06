@props(['status' => null])

@php
  use App\Enums\StatusBarangKeluar;

  $statusValue = $status instanceof StatusBarangKeluar ? $status->value : $status;

  $statusEnum = StatusBarangKeluar::tryFrom($statusValue);

  $statusLabel = $statusEnum ? $statusEnum->label() : 'Tidak ada data';
  $statusClass = match ($statusEnum) {
      StatusBarangKeluar::BELUM_LENGKAP => 'text-warning',
      StatusBarangKeluar::LENGKAP => 'text-success',
      default => 'text-muted',
  };
@endphp

<span class="fw-bold {{ $statusClass }}">
  {{ $statusLabel }}
</span>
