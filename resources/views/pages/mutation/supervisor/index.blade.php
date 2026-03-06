@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Data Mutasi Barang" class="mb-3">
    </x-page-header>

    <div class="card mb-3">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form mutasi</p>
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

          <button type="submit" class="btn btn-primary">
            Lihat Mutasi
          </button>
        </form>
      </div>
    </div>

    @if ($mutations->isEmpty())
      <div class="card">
        <div class="p-3 card-header">
          <p class="mb-0 fw-semibold">Tabel mutasi</p>
        </div>
        <div class="card-body">
          <div class="d-flex gap-2 text-info-emphasis">
            <i class="bi bi-info-circle-fill"></i>
            <p class="mb-0">Data tidak tersedia</p>
          </div>
        </div>
      </div>
    @endif

    @if ($mutations->isNotEmpty())
      <div class="card">
        <div class="p-3 card-header d-flex justify-content-between align-items-center">
          <p class="mb-0 fw-semibold">Tabel mutasi</p>
          <a href="{{ route('supervisor.mutation.excel-mutation') }}?tgl_awal={{ $tgl_awal }}&tgl_akhir={{ $tgl_akhir }}"
            class="btn btn-primary">
            Export Excel
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead class="text-nowrap">
                <tr>
                  <th rowspan="2" class="align-middle">Nama Barang</th>
                  <th colspan="8" class="text-center">Total</th>
                  <th rowspan="2" class="align-middle">Sisa Stok</th>
                  <th rowspan="2" class="align-middle">Satuan</th>
                </tr>
                <tr>
                  <th>Stock Awal</th>
                  <th>Stock Masuk</th>
                  <th>Harga Stok Masuk</th>
                  <th>Stok Keluar</th>
                  <th>Harga Stok Keluar</th>
                  <th>Stok Retur Beli</th>
                  <th>Stok Retur Jual</th>
                  <th>Stok Rusak/Expired</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($mutations as $item)
                  <tr>
                    <td>
                      {{ $item['barang_nama'] }}
                    </td>
                    <td>
                      {{ $item['total_stock_awal'] }}
                    </td>
                    <td>
                      {{ $item['total_stock_masuk'] }}
                    </td>
                    <td>
                      {{ \Number::currency($item['total_harga_beli'], 'IDR', 'id_ID') }}
                    </td>
                    <td>
                      {{ $item['total_stock_keluar'] }}
                    </td>
                    <td>
                      {{ \Number::currency($item['total_harga_jual'], 'IDR', 'id_ID') }}
                    </td>
                    <td>
                      {{ $item['total_stock_retur_beli'] }}
                    </td>
                    <td>
                      {{ $item['total_stock_retur_jual'] }}
                    </td>
                    <td>
                      {{ $item['total_stock_rusak'] }}
                    </td>
                    <td>
                      {{ $item['sisa_stock'] }}
                    </td>
                    <td>
                      {{ $item['barang_satuan'] }}
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
