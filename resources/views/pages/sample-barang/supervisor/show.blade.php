@extends('layouts.main-layout')

@section('title')
  Detail data barang sampel
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Data barang sampel" class="mb-3" withBackButton />

    <div class="card mb-3">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Detail data barang sampel</p>
      </div>

      <div class="p-3">
        <table class="table mb-0">
          <tbody>
            <tr>
              <td>Brand</td>
              <td>{{ $sample_barang->barang->brand->nama ?? '-' }}</td>
            </tr>
            <tr>
              <td width="150">Kode barang </td>
              <td>{{ $sample_barang->barang->kode ?? '-' }}</td>
            </tr>
            <tr>
              <td width="200">Nama barang </td>
              <td><strong>{{ $sample_barang->barang->nama }}</strong></td>
            </tr>
            <tr>
              <td>Satuan </td>
              <td>{{ $sample_barang->satuan }}</td>
            </tr>
            <tr>
              <td>NIE</td>
              <td>{{ $sample_barang->barang->nie ?? '-' }}</td>
            </tr>
            <tr>
              <td>Group</td>
              <td>{{ $sample_barang->barang->group->nama ?? '-' }}</td>
            </tr>
            <tr>
              <td>Supplier</td>
              <td>{{ $sample_barang->barang->supplier->nama ?? '-' }}</td>
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
                <td>{{ $item->sampleBarang->satuan }}</td>
              </tr>
            @endforeach
          </table>
        @endif
      </div>
    </div>
  </div>
@endsection
