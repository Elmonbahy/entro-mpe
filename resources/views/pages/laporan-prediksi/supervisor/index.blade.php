@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Prediksi Pengadaan Barang (Metode MPE)" class="mb-3">
    </x-page-header>

    <div class="card mb-3">
      <div class="p-3 card-header d-flex align-items-center">
        <p class="mb-0 fw-semibold me-2">Form Laporan Prediksi Pengadaan Barang</p>
        </p>
      </div>

      <div class="p-3">
        <form method="GET" action="{{ route('supervisor.laporan-prediksi.index') }}" autocomplete="off">
          <div class="row">
            <div class="col-md-4 mb-3">
              <x-form.label value="Brand" />
              <x-form.select name="brand_id" placeholder="Cari atau pilih brand" :options="$listBrand" valueKey="id"
                labelKey="nama" :selected="$brand_id" />
            </div>
            <div class="col-md-4 mb-3">
              <x-form.label value="Barang" />
              <x-form.select name="barang_id" placeholder="Cari atau pilih barang" :options="$listBarang" valueKey="id"
                labelKey="nama" :selected="$barang_id" />
            </div>
          </div>

          <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Lihat Laporan
          </button>
          <a href="{{ route('supervisor.laporan-prediksi.index') }}" class="btn btn-outline-danger">Reset</a>

        </form>
      </div>
    </div>

    @if ($data->isEmpty())
      <div class="card">
        <div class="p-3 card-header">
          <p class="mb-0 fw-semibold">Tabel Laporan Faktur Jual</p>
        </div>
        <div class="card-body">
          <div class="d-flex gap-2 text-info-emphasis">
            <i class="bi bi-info-circle-fill"></i>
            <p class="mb-0">Data tidak tersedia</p>
          </div>
        </div>
      </div>
    @endif

    @if ($data->isNotEmpty())
      <div class="card">
        <div class="p-3 card-header d-flex justify-content-between align-items-center">
          <p class="mb-0 fw-semibold">Tabel Laporan Prediksi Pengadaan Barang</p>
        </div>

        <div class="card-body">
          <x-scroll-buttons />
          <div class="table-responsive">
            <table class="table table-bordered small text-center">
              <thead class="text-nowrap">
                <tr>
                  <th>Nama Barang</th>
                  <th>Stok Fisik</th>
                  <th>Prediksi Keluar</th>
                  <th>Status</th>
                  <th>Saran Pengadaan</th>
                  <th>Chart</th>
                </tr>
              </thead>

              <tbody>
                @foreach ($data as $index => $item)
                  <tr>
                    <td class="text-start">
                      <strong>{{ $item['barang_nama'] }}</strong><br>
                      <small class="text-muted">Satuan: {{ $item['satuan'] }}</small>
                    </td>
                    <td class="text-center">{{ $item['stok_saat_ini'] }}</td>
                    <td class="text-center text-primary"><strong>{{ $item['prediksi_keluar'] }}</strong></td>
                    <td class="text-center">
                      @if ($item['status'] == 'KRITIS')
                        <span class="badge bg-danger">KRITIS</span>
                      @elseif($item['status'] == 'DATA KOSONG')
                        <span class="badge bg-secondary">DATA TIDAK CUKUP</span>
                      @else
                        <span class="badge bg-success">AMAN</span>
                      @endif
                    </td>
                    <td class="text-center">
                      @if ($item['status'] == 'DATA KOSONG')
                        <small class="text-muted italic">Butuh riwayat mutasi</small>
                      @elseif ($item['saran_beli'] > 0)
                        <b class="text-danger">+ {{ $item['saran_beli'] }}</b>
                      @else
                        -
                      @endif
                    </td>
                    <td>
                      <canvas id="chart-{{ $index }}" height="80"></canvas>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    @endif
  </div>
@endsection

@push('scripts')
  <!-- Script Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    @foreach ($data as $index => $item)
      new Chart(document.getElementById('chart-{{ $index }}'), {
        type: 'line',
        data: {
          labels: {!! json_encode($item['labels']) !!},
          datasets: [{
            data: {!! json_encode(array_merge($item['tren_data'], [$item['prediksi_keluar']])) !!},
            borderColor: '{{ $item['status'] == 'KRITIS' ? '#dc3545' : '#28a745' }}',
            fill: false,
            tension: 0.1
          }]
        },
        options: {
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              display: false
            },
            x: {
              display: true
            }
          }
        }
      });
    @endforeach

    document.addEventListener('DOMContentLoaded', function() {
      new TomSelect('#brand_id');
      new TomSelect('#barang_id');
    });
  </script>
@endpush
