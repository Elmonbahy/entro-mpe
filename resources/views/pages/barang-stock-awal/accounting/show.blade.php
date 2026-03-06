@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Data barang penyesuaian" class="mb-3" />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Detail data barang penyesuaian</p>
      </div>

      <div class="p-3">
        <table class="table mb-0">
          <tbody>
            <tr>
              <td width="200">Perubahan </td>
              <td>{{ $barang_stock_awal->jenis_perubahan }}</td>
            </tr>
            <tr>
              <td>Tanggal Penyesuaian</td>
              <td>
                {{ $barang_stock_awal->tgl_stock ? \Carbon\Carbon::parse($barang_stock_awal->tgl_stock)->format('d/m/Y') : '-' }}
              </td>
            </tr>
            <tr>
              <td>Brand </td>
              <td>{{ $barang_stock_awal->barang->brand->nama }}</td>
            </tr>
            <tr>
              <td>ID Barang </td>
              <td><strong>{{ $barang_stock_awal->barang->id }}</strong></td>
            </tr>
            <tr>
              <td>Barang</td>
              <td>
                <x-button.link-button href="{{ route('accounting.barang.show', $barang_stock_awal->barang->id) }}">
                  {{ $barang_stock_awal->barang->nama }}
                </x-button.link-button>
              </td>
            </tr>
            <tr>
              <td>Batch</td>
              <td>{{ $barang_stock_awal->batch ?? '-' }}</td>
            </tr>
            <tr>
              <td>Expired</td>
              <td>
                {{ $barang_stock_awal->tgl_expired
                    ? \Carbon\Carbon::parse($barang_stock_awal->tgl_expired)->format('d/m/Y')
                    : '-' }}
              </td>
            </tr>
            <tr>
              <td>Jumlah stock </td>
              <td>{{ $barang_stock_awal->jumlah_stock }} {{ $barang_stock_awal->barang->satuan }}</td>
            </tr>
            <tr>
              <td>Keterangan</td>
              <td>{{ $barang_stock_awal->keterangan ?? '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
