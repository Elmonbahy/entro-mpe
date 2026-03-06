@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Laporan Slow Moving" class="mb-3">
    </x-page-header>

    <div class="card mb-3">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form Slow Moving</p>
      </div>

      <div class="p-3">
        <form method="GET" action="{{ route('fakturis.slow-moving.index') }}" autocomplete="off">
          <div class="row">
            <div class="col-md-4 mb-3">
              <x-form.label value="Tanggal awal" />
              <x-form.input name="tgl_awal" wire:model="tgl_awal" type="date" value="{{ $tgl_awal }}" />
            </div>
            <div class="col-md-4 mb-3">
              <x-form.label value="Tanggal akhir" />
              <x-form.input name="tgl_akhir" wire:model="tgl_akhir" type="date" value="{{ $tgl_akhir }}" />
            </div>
            <div class="col-md-4 mb-3">
              <x-form.label value="Brand" optional />
              <x-form.select name="brand_id" placeholder="Cari atau pilih brand" :options="$brands" valueKey="id"
                labelKey="nama" :selected="$brand_id" />
            </div>
          </div>

          <button type="submit" class="btn btn-primary">
            Lihat Laporan
          </button>
        </form>
      </div>
    </div>

    @if ($data->isEmpty())
      <div class="card">
        <div class="p-3 card-header">
          <p class="mb-0 fw-semibold">Tabel Slow Moving</p>
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
          <p class="mb-0 fw-semibold">Tabel Laporan Slow Moving</p>
          <a href="{{ route('fakturis.slow-moving.excel') }}?tgl_awal={{ $tgl_awal }}&tgl_akhir={{ $tgl_akhir }}&brand_id={{ $brand_id }}"
            class="btn btn-primary">
            Export Excel
          </a>
        </div>

        <div class="card-body">
          <x-scroll-buttons />
          <div class="table-responsive">

            <table class="table table-bordered small text-center">
              <!-- Header Row -->
              <thead class="text-nowrap">
                <tr>
                  <th>Brand</th>
                  <th>Nama Barang</th>
                  <th>Pembelian</th>
                  <th>Penjualan</th>
                  <th>Satuan</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($data as $item)
                  <tr>
                    <td class="text-start">{{ $item['brand'] }}</td>
                    <td class="text-start">{{ $item['nama'] }}</td>
                    <td>{{ number_format($item['total_pembelian'], 0, ',', '.') }}</td>
                    <td>{{ number_format($item['total_penjualan'], 0, ',', '.') }}</td>
                    <td class="text-start">{{ $item['satuan'] }}</td>
                    <td>{{ $item['status'] }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center">Tidak ada data</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    @endif

  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      new TomSelect('#brand_id');
    });
  </script>
@endpush
