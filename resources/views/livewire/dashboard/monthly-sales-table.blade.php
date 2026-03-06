<div class="card mt-4">
  <div class="card-header p-3 fw-bold d-flex justify-content-between align-items-center">
    <span>Rekap Faktur Jual Bulanan</span>

    <div class="d-flex align-items-center gap-2">

      {{-- Last Update --}}
      <small class="text-muted">
        Last Update: <strong>{{ $lastUpdated ?? '-' }}</strong>
      </small>

      {{-- Spinner selectedYear --}}
      <div wire:loading wire:target="selectedYear" class="me-1">
        <div class="spinner-border spinner-border-sm text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      {{-- Dropdown Tahun --}}
      <select wire:model.live="selectedYear" class="form-select form-select-sm" style="width: 120px;">
        @foreach ($years as $year)
          <option value="{{ $year }}">{{ $year }}</option>
        @endforeach
      </select>

      {{-- Refresh Data --}}
      <button class="btn btn-outline-secondary btn-sm" wire:click="refreshData" wire:loading.attr="disabled"
        style="white-space: nowrap;">
        Refresh
      </button>

      {{-- Spinner Refresh --}}
      <div wire:loading wire:target="refreshData" class="ms-1">
        <div class="spinner-border spinner-border-sm text-primary"></div>
      </div>

    </div>
  </div>

  <div class="card-body table-responsive">
    <table class="table table-bordered table-striped table-sm">
      <thead>
        <tr class="text-center">
          <th>Bulan</th>
          <th>Total Faktur</th>
          <th>Lunas</th>
          <th>Belum Lunas</th>
          <th>% Pelunasan</th>
        </tr>
      </thead>

      <tbody>
        @forelse ($monthlyData as $row)
          <tr>
            <td class="text-start">{{ $row['month_name'] }}</td>
            <td class="text-end">Rp {{ number_format($row['total_faktur'], 0, ',', '.') }}</td>
            <td class="text-end">Rp {{ number_format($row['total_lunas'], 0, ',', '.') }}</td>
            <td class="text-end">Rp {{ number_format($row['total_belum_lunas'], 0, ',', '.') }}</td>
            <td class="text-center">
              @php
                $percent = $row['persentase_lunas'];
                $color = 'danger'; // merah
                if ($percent >= 80) {
                    $color = 'success'; // hijau
                } elseif ($percent >= 50) {
                    $color = 'warning'; // kuning
                }
              @endphp
              <span class="badge bg-{{ $color }}" style="padding: 0.4em 0.6em; font-size: 0.9em;">
                {{ $percent }}%
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center fw-bold py-3">Tidak ada data untuk tahun ini.</td>
          </tr>
        @endforelse
      </tbody>

      @if (!empty($monthlyData))
        <tfoot>
          <tr class="fw-bold">
            <td class="text-center">TOTAL</td>
            <td class="text-end">Rp {{ number_format($grandTotal['total_faktur'], 0, ',', '.') }}</td>
            <td class="text-end">Rp {{ number_format($grandTotal['total_lunas'], 0, ',', '.') }}</td>
            <td class="text-end">Rp {{ number_format($grandTotal['total_belum_lunas'], 0, ',', '.') }}</td>
            <td class="text-center"></td>
          </tr>
        </tfoot>
      @endif
    </table>
  </div>
</div>

@push('scripts')
  <script>
    document.addEventListener('livewire:load', function() {
      // Animasi setiap progress bar
      const bars = document.querySelectorAll('.progress-bar');
      bars.forEach(bar => {
        const percent = bar.getAttribute('aria-valuenow');
        setTimeout(() => {
          bar.style.width = percent + '%';
        }, 100); // delay agar terlihat animasi
      });
    });

    // Jika ada update livewire (ganti tahun), jalankan ulang animasi
    Livewire.hook('message.processed', (message, component) => {
      const bars = document.querySelectorAll('.progress-bar');
      bars.forEach(bar => {
        const percent = bar.getAttribute('aria-valuenow');
        bar.style.width = '0%';
        setTimeout(() => {
          bar.style.width = percent + '%';
        }, 100);
      });
    });
  </script>
@endpush
