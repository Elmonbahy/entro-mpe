<div class="card">
  <div class="card-header p-3 fw-bold d-flex justify-content-between align-items-center">
    Chart Beli Mingguan (Tahun {{ $currentYear }})
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

    {{-- Chart Container --}}
    <div id="beli-chart-container" wire:ignore
      style="height: 350px; width: 100%; display: @if (empty($chartData)) none @else block @endif;">
      <canvas id="purchaseChart"></canvas>
    </div>

    {{-- No Data Message --}}
    <div id="beli-no-data-message" class="text-center" role="alert"
      style="height: 350px; display: @if (empty($chartData)) flex @else none @endif; align-items: center; justify-content: center;">
      <p class="mb-0 fw-bold">Tidak ada data pembelian untuk periode yang dipilih.</p>
    </div>
  </div>
</div>

@push('scripts')
  <script>
    document.addEventListener('livewire:initialized', () => {
      let purchaseChart = null;
      const chartContainer = document.getElementById('beli-chart-container');
      const noDataMessage = document.getElementById('beli-no-data-message');

      function initOrUpdatePurchaseChart(data) {
        const chartData = Array.isArray(data) ? data : [];
        const hasData = chartData.length > 0;

        // Manage Visibility
        if (hasData) {
          chartContainer.style.display = 'block';
          noDataMessage.style.display = 'none';
        } else {
          chartContainer.style.display = 'none';
          noDataMessage.style.display = 'flex';
        }

        // Cleanup
        if (purchaseChart) {
          purchaseChart.destroy();
          purchaseChart = null;
        }

        // Initialize Chart if Data Exists
        if (!hasData) {
          return;
        }

        const valuesTagihan = chartData.map(item => parseFloat(item.total_tagihan) || 0);
        const valuesTerbayar = chartData.map(item => parseFloat(item.total_terbayar) || 0);

        const ctx = document.getElementById('purchaseChart').getContext('2d');

        purchaseChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: chartData.map(item => item.label),
            datasets: [{
              label: 'Total Tagihan Beli',
              data: valuesTagihan,
              borderColor: 'rgb(255, 99, 132)', // Use a distinct color (Red)
              tension: 0.1,
              fill: false
            }, {
              label: 'Total Terbayar Beli',
              data: valuesTerbayar,
              borderColor: 'rgb(54, 162, 235)', // Use a distinct color (Blue)
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
      initOrUpdatePurchaseChart(@json($chartData ?? []));

      // ⭐ Listen for the new unique event
      Livewire.on('updateChartBeli', ({
        data
      }) => {
        initOrUpdatePurchaseChart(data);
      });
    });
  </script>
@endpush
