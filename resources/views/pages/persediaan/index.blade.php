@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Data Persediaan Barang" class="mb-3">
    </x-page-header>

    <div class="card mb-3">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form persediaan</p>
      </div>

      <div class="p-3">
        <form action="#" autocomplete="off">
          <div class="row">
            <div class="col-md-4 mb-3">
              <x-form.label value="Tanggal awal" />
              <x-form.input name="tgl_awal" wire:model="tgl_awal" type="date" :value="$tgl_awal" />
            </div>
            <div class="col-md-4 mb-3">
              <x-form.label value="Tanggal akhir" />
              <x-form.input name="tgl_akhir" wire:model="tgl_akhir" type="date" :value="$tgl_akhir" />
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <x-form.label value="Brand" optional />
              <x-form.select name="brand_id" placeholder="Cari atau pilih brand" :options="$brands" valueKey="id"
                labelKey="nama" :selected="$brand_id" />
            </div>
            <div class="col-md-4 mb-3">
              <x-form.label value="Barang" optional />
              <x-form.select name="barang_id" placeholder="Cari atau pilih barang" :options="$barangs" valueKey="id"
                labelKey="nama" :selected="$barang_id" />
            </div>
          </div>

          <button type="submit" class="btn btn-primary">
            Lihat Persediaan
          </button>
        </form>
      </div>
    </div>

    @if ($data->isEmpty())
      <div class="card">
        <div class="p-3 card-header">
          <p class="mb-0 fw-semibold">Tabel persediaan</p>
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
          <p class="mb-0 fw-semibold">Tabel persediaan</p>
          <a href="{{ route('fakturis.persediaan.excel') }}?tgl_awal={{ $tgl_awal }}&tgl_akhir={{ $tgl_akhir }}&brand_id={{ $brand_id }}&barang_id={{ $barang_id }}"
            class="btn btn-primary">
            Export Excel
          </a>
        </div>
        <div class="card-body">
          <x-scroll-buttons />
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead class="text-nowrap">
                <tr>
                  <th>Brand</th>
                  <th>Id Barang</th>
                  <th>Nama Barang</th>
                  <th>Satuan</th>
                  <th>Stock Ahkir</th>
                  <th>total Stok Masuk</th>
                  <th>total Harga Beli</th>
                  <th>Harga Rata-rata</th>
                  <th>Nilai Persediaan</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($data as $item)
                  <tr>
                    <td>
                      {{ $item['brand'] }}
                    </td>
                    <td>
                      {{ $item['barang_id'] }}
                    </td>
                    <td>
                      {{ $item['barang_nama'] }}
                    </td>
                    <td>
                      {{ $item['satuan'] }}
                    </td>
                    <td>
                      {{ number_format($item['stock_akhir'], 0, ',', '.') }}
                    </td>
                    <td>
                      {{ number_format($item['total_stock_masuk'], 0, ',', '.') }}
                    </td>
                    <td>
                      {{ \Number::currency($item['total_harga_beli'], 'IDR', 'id_ID') }}
                    </td>
                    <td>
                      {{ \Number::currency($item['hpp_avg'], 'IDR', 'id_ID') }}
                    </td>
                    <td>
                      {{ \Number::currency($item['nilai_persedian'], 'IDR', 'id_ID') }}
                    </td>
                  </tr>
                @endforeach
              </tbody>
              <tr class="fw-bold">
                <td colspan="8" class="text-end"><strong>Total</strong></td>
                <td>{{ \Number::currency($total_nilai_persediaan, 'IDR', 'id_ID') }}</td>
              </tr>
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
      new TomSelect('#barang_id');
    });
  </script>
@endpush
