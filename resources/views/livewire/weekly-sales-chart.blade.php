<div class="card" x-data="{ salesChart: null, hasData: @js(!empty($chartData)) }">
  <div class="card-header p-3 fw-bold d-flex justify-content-between align-items-center">
    Chart Jual Mingguan (Tahun {{ $currentYear }})
    <div class="d-flex gap-2">
      <div>
        <select wire:model.live="startMonth" class="form-select form-select-sm">
          @foreach ($availableMonths as $key => $month)
            <option value="{{ $key }}">{{ $month }}</option>
          @endforeach
        </select>
      </div>
      <span>s.d.</span>
      <div>
        <select wire:model.live="endMonth" class="form-select form-select-sm">
          @foreach ($availableMonths as $key => $month)
            <option value="{{ $key }}">{{ $month }}</option>
          @endforeach
        </select>
      </div>
    </div>
  </div>
  <div class="card-body">

    {{-- 1. Chart Container: Always rendered, managed by JS --}}
    <div id="chart-container" wire:ignore
      style="height: 350px; width: 100%; display: @if (empty($chartData)) none @else block @endif;">
      <canvas id="salesChart"></canvas>
    </div>

    {{-- 2. No Data Message: Always rendered, managed by JS --}}
    <div id="no-data-message" class="text-center" role="alert"
      style="height: 350px; display: @if (empty($chartData)) flex @else none @endif; align-items: center; justify-content: center;">
      <p class="mb-0 fw-bold">Tidak ada data penjualan untuk periode yang dipilih.</p>
    </div>
  </div>
</div>
@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    document.addEventListener('livewire:initialized', () => {
      let salesChart = null;
      const chartContainer = document.getElementById('chart-container');
      const noDataMessage = document.getElementById('no-data-message');

      function initOrUpdateChart(data) {
        const chartData = Array.isArray(data) ? data : [];
        const hasData = chartData.length > 0;

        // --- Step 1: Manage Visibility ---
        if (hasData) {
          chartContainer.style.display = 'block';
          noDataMessage.style.display = 'none';
        } else {
          chartContainer.style.display = 'none';
          noDataMessage.style.display = 'flex';
        }

        // --- Step 2: Cleanup ---
        if (salesChart) {
          salesChart.destroy();
          salesChart = null;
        }

        // --- Step 3: Initialize Chart if Data Exists ---
        if (!hasData) {
          return;
        }

        const valuesTagihan = chartData.map(item => parseFloat(item.total_tagihan) || 0);
        const valuesTerbayar = chartData.map(item => parseFloat(item.total_terbayar) || 0);
        const labels = chartData.map(item => item.label);

        // We must re-select the canvas to get its context after ensuring the container is visible
        const ctx = document.getElementById('salesChart').getContext('2d');

        // Initialize new chart
        salesChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Total Tagihan',
              data: valuesTagihan,
              borderColor: 'rgb(5, 180, 192)',
              tension: 0.1,
              fill: false
            }, {
              label: 'Total Terbayar',
              data: valuesTerbayar,
              borderColor: 'rgb(75, 192, 80)',
              tension: 0.1,
              fill: false
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  callback: function(value) {
                    return 'Rp ' + value.toLocaleString('id-ID');
                  }
                }
              },
              x: {
                ticks: {
                  maxRotation: 45,
                  minRotation: 45
                }
              }
            },
            plugins: {
              tooltip: {
                callbacks: {
                  label: function(context) {
                    return 'Total: Rp ' + context.raw.toLocaleString('id-ID');
                  }
                }
              }
            }
          }
        });
      }

      // Initial data load when Livewire component mounts
      initOrUpdateChart(@json($chartData ?? []));

      // Listen for the 'updateChart' event dispatched from the Livewire backend
      Livewire.on('updateChart', ({
        data
      }) => {
        // Because the 'updateChart' event is the only trigger after mount, 
        // we call the same function to update visibility, cleanup, and redraw.
        initOrUpdateChart(data);
      });
    });
  </script>
@endpush
