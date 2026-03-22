<div class="card border-0 shadow-sm mt-4">
  <div class="card-header bg-white py-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
      <div>
        <h5 class="mb-0 fw-bold text-dark">Rekap Faktur Jual Bulanan</h5>
        <small class="text-muted">
          Pembaruan Terakhir: <span class="fw-semibold text-primary">{{ $lastUpdated ?? '-' }}</span>
        </small>
      </div>

      <div class="d-flex flex-wrap align-items-center gap-2">
        {{-- Spinner Loading --}}
        <div wire:loading wire:target="selectedYear, refreshData">
          <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
        </div>

        {{-- Dropdown Tahun --}}
        <div class="input-group input-group-sm w-auto">
          <span class="input-group-text bg-light"><i class="bi bi-calendar-event"></i></span>
          <select wire:model.live="selectedYear" class="form-select border-start-0" style="min-width: 90px;">
            @foreach ($years as $year)
              <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
          </select>
        </div>

        {{-- Refresh Button --}}
        <button class="btn btn-primary btn-sm px-3 shadow-sm" wire:click="refreshData" wire:loading.attr="disabled">
          <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
      </div>
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-muted text-uppercase small">
          <tr>
            <th class="ps-4 py-3" style="width: 150px;">Bulan</th>
            <th class="text-end py-3">Total Faktur</th>
            <th class="text-end py-3 text-success">Lunas</th>
            <th class="text-end py-3 text-danger">Belum Lunas</th>
            <th class="text-center py-3" style="width: 200px;">Status Pelunasan</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($monthlyData as $row)
            @php
              $percent = $row['persentase_lunas'];
              $color = $percent >= 80 ? 'success' : ($percent >= 50 ? 'warning' : 'danger');
            @endphp
            <tr>
              <td class="ps-4 fw-bold text-dark">{{ $row['month_name'] }}</td>
              <td class="text-end fw-medium text-secondary">
                Rp {{ number_format($row['total_faktur'], 0, ',', '.') }}
              </td>
              <td class="text-end fw-bold text-success">
                Rp {{ number_format($row['total_lunas'], 0, ',', '.') }}
              </td>
              <td class="text-end fw-bold text-danger">
                Rp {{ number_format($row['total_belum_lunas'], 0, ',', '.') }}
              </td>
              <td class="px-4">
                <div class="d-flex align-items-center gap-2">
                  <div class="progress flex-grow-1" style="height: 8px; border-radius: 10px;">
                    <div class="progress-bar bg-{{ $color }} progress-bar-striped progress-bar-animated"
                      role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"
                      style="width: 0%; transition: width 1s ease-in-out;">
                    </div>
                  </div>
                  <span class="badge rounded-pill text-bg-{{ $color }} shadow-sm" style="min-width: 45px;">
                    {{ $percent }}%
                  </span>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-5">
                <div class="text-muted">
                  <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                  <span class="fw-bold">Tidak ada data untuk tahun {{ $selectedYear }}.</span>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>

        @if (!empty($monthlyData))
          <tfoot class="bg-light border-top-2">
            <tr class="fw-bold text-dark">
              <td class="ps-4 text-center">TOTAL</td>
              <td class="text-end">Rp {{ number_format($grandTotal['total_faktur'], 0, ',', '.') }}</td>
              <td class="text-end text-success">Rp {{ number_format($grandTotal['total_lunas'], 0, ',', '.') }}</td>
              <td class="text-end text-danger">Rp {{ number_format($grandTotal['total_belum_lunas'], 0, ',', '.') }}
              </td>
              <td></td>
            </tr>
          </tfoot>
        @endif
      </table>
    </div>
  </div>
</div>

@push('scripts')
  <script>
    function animateBars() {
      const bars = document.querySelectorAll('.progress-bar');
      bars.forEach(bar => {
        const percent = bar.getAttribute('aria-valuenow');
        // Reset dulu ke 0 agar animasi terlihat
        bar.style.width = '0%';
        setTimeout(() => {
          bar.style.width = percent + '%';
        }, 150);
      });
    }

    // Jalankan saat halaman pertama kali dimuat
    document.addEventListener('DOMContentLoaded', animateBars);

    // Jalankan setelah Livewire selesai melakukan update DOM (Livewire v3)
    document.addEventListener('livewire:navigated', animateBars);

    // Fallback untuk Livewire v2
    document.addEventListener('livewire:load', animateBars);

    // Hook untuk setiap kali data berubah (interaksi dropdown/refresh)
    if (typeof Livewire !== 'undefined') {
      Livewire.hook('message.processed', (message, component) => {
        animateBars();
      });
    }
  </script>
@endpush
