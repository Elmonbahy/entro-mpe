@extends('layouts.main-layout')

@section('title')
  Detail data barang
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Data barang" class="mb-3" withBackButton />

    <div class="card mb-3">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Detail data barang</p>
      </div>

      <div class="p-3">
        <table class="table mb-0">
          <tbody>
            <tr>
              <td>Brand</td>
              <td>{{ $barang->brand->nama ?? '-' }}</td>
            </tr>
            <tr>
              <td width="150">Kode barang </td>
              <td>{{ $barang->kode ?? '-' }}</td>
            </tr>
            <tr>
              <td width="200">Nama barang </td>
              <td><strong>{{ $barang->nama }}</strong></td>
            </tr>
            <tr>
              <td>Satuan </td>
              <td>{{ $barang->satuan }}</td>
            </tr>
            <tr>
              <td>NIE</td>
              <td>{{ $barang->nie ?? '-' }}</td>
            </tr>
            <tr>
              <td>Harga jual pemerintah</td>
              <td>

                {{ Number::currency($barang->harga_jual_pemerintah, in: 'IDR', locale: 'id_ID') }}

              </td>
            </tr>
            <tr>
              <td>Harga jual swasta</td>
              <td>

                {{ Number::currency($barang->harga_jual_swasta, in: 'IDR', locale: 'id_ID') }}

              </td>
            </tr>
            <tr>
              <td>Harga beli</td>
              <td>

                {{ Number::currency($barang->harga_beli, in: 'IDR', locale: 'id_ID') }}

              </td>
            </tr>
            <tr>
              <td>Group</td>
              <td>{{ $barang->group->nama ?? '-' }}</td>
            </tr>
            <tr>
              <td>Supplier</td>
              <td>{{ $barang->supplier->nama ?? '-' }}</td>
            </tr>
            <tr>
              <td>Kegunaan</td>
              <td>{{ $barang->kegunaan ?? '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>


    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Data stock barang</p>
      </div>

      <div class="p-3">
        @if ($barang_stocks->isEmpty())
          <p class="mb-0 text-center">
            Tidak ada stock barang
          </p>
        @else
          <table class="table mb-0 text-center  ">
            <thead>
              <th>Batch</th>
              <th>Expired</th>
              <th>Jumlah Stock</th>
              <th>Satuan</th>
            </thead>
            @foreach ($barang_stocks as $item)
              <tr>
                <td>{{ $item->batch ?: '-' }}</td>
                <td>{{ $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') : '-' }}</td>
                <td>{{ number_format($item->jumlah_stock, 0, ',', '.') }}</td>
                <td>{{ $item->barang->satuan }}</td>
              </tr>
            @endforeach
          </table>
        @endif
      </div>
    </div>
  </div>
@endsection
