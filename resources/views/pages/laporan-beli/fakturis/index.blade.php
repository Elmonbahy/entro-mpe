@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Laporan Faktur Beli" class="mb-3">
    </x-page-header>

    <div class="card mb-3">
      <div class="p-3 card-header d-flex align-items-center">
        <p class="mb-0 fw-semibold me-2">Form Laporan Faktur Beli</p>
      </div>

      <div class="p-3">
        <form method="GET" action="{{ route('fakturis.laporan-beli.index') }}" autocomplete="off">
          <div class="row">
            <div class="col-md-4 mb-3">
              <x-form.label value="Filter Berdasarkan" />
              <select name="filter_berdasarkan" class="form-control">
                <option value="tgl_faktur" {{ request('filter_berdasarkan') == 'tgl_faktur' ? 'selected' : '' }}>Tanggal
                  Faktur</option>
                <option value="tgl_terima" {{ request('filter_berdasarkan') == 'tgl_terima' ? 'selected' : '' }}>Tanggal
                  Terima Faktur</option>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <x-form.label value="Tanggal awal" />
              <x-form.input name="tgl_awal" wire:model="tgl_awal" type="date" value="{{ $tgl_awal }}" />
            </div>
            <div class="col-md-4 mb-3">
              <x-form.label value="Tanggal akhir" />
              <x-form.input name="tgl_akhir" wire:model="tgl_akhir" type="date" value="{{ $tgl_akhir }}" />
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <x-form.label value="Supplier" optional />
              <x-form.select name="supplier_id" placeholder="Cari atau pilih supplier" :options="$suppliers" valueKey="id"
                labelKey="nama" :selected="$supplier_id" />
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
          <p class="mb-0 fw-semibold">Tabel Laporan Faktur Beli</p>
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
          <p class="mb-0 fw-semibold">Tabel Laporan Faktur Beli</p>
          <a href="{{ route('fakturis.laporan-beli.excel') }}?tgl_awal={{ request('tgl_awal') }}&tgl_akhir={{ request('tgl_akhir') }}&supplier_id={{ request('supplier_id') }}&filter_berdasarkan={{ request('filter_berdasarkan') }}"
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
                  <th>Supplier</th>
                  <th>Nomor Faktur</th>
                  <th>Tanggal Faktur</th>
                  <th>Tanggal Terima</th>
                  <th>No</th>
                  <th>Nama Barang</th>
                  <th>Jumlah</th>
                  <th>Satuan</th>
                  <th>Harga Beli</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($data as $faktur)
                  @php
                    $rowspan = $faktur['beli_details_count'] + 2;
                  @endphp

                  <tr>
                    <td rowspan="{{ $rowspan }}" class="text-start">
                      {{ $faktur['supplier_nama'] }}
                    </td>
                    <td rowspan="{{ $rowspan }}">
                      {{ $faktur['nomor_faktur'] }}
                    </td>
                    <td rowspan="{{ $rowspan }}">
                      {{ $faktur['tgl_faktur'] }}
                    </td>
                    <td rowspan="{{ $rowspan }}">
                      {{ $faktur['tgl_terima_faktur'] }}
                    </td>
                  </tr>
                  @foreach ($faktur['beli_details'] as $item)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td class="text-start">{{ $item['barang_nama'] }}</td>
                      <td>{{ number_format($item['jumlah_barang_masuk'], 0, ',', '.') }}</td>
                      <td class="text-start">{{ $item['barang_satuan'] }}</td>
                      <td class="text-end">{{ number_format($item['harga_beli'], 2, ',', '.') }}</td>
                    </tr>
                  @endforeach
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
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      new TomSelect('#supplier_id');
    });
  </script>
@endpush
