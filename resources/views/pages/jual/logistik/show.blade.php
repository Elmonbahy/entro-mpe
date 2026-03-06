@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Barang keluar" class="mb-3" withBackButton />
    <x-alert.session-alert />

    <x-card.faktur-jual-detail :jual="$jual" />

    <div class="card mt-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar penjualan barang</p>
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered mb-0">
            <thead class="text-nowrap">
              <th>ID</th>
              <th>Brand</th>
              <th>Nama</th>
              <th>Pesanan</th>
              <th>Keluar</th>
              <th>Satuan</th>
              <th>Batch</th>
              <th>Tgl Expired</th>
              <th>Status barang keluar</th>
            </thead>
            <tbody>
              @foreach ($jual_detail as $item)
                <tr wire:key="{{ $item->id }}">
                  <td>{{ $item->id }}</td>
                  <td>{{ $item->barang->brand->nama }}</td>
                  <td>{{ $item->barang->nama }}</td>
                  <td>{{ number_format($item->jumlah_barang_dipesan, 0, ',', '.') }}</td>
                  <td>{{ $item->jumlah_barang_keluar ? number_format($item->jumlah_barang_keluar, 0, ',', '.') : '-' }}
                  </td>
                  <td>{{ $item->barang->satuan }}</td>
                  <td>{{ $item->batch ?? '-' }}</td>
                  <td>{{ $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') : '-' }}</td>
                  <td>
                    <x-badge.status-barang-keluar :status="$item->status_barang_keluar" />
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
