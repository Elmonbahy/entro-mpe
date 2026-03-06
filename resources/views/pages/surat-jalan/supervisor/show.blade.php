@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Surat Jalan" class="mb-3">
    </x-page-header>

    <div class="mb-3">
      <x-card.surat-jalan-detail :data="$surat_jalan" />
    </div>

    <div class="card mt-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar Penjualan Barang</p>
      </div>

      <div class="card-body">
        @if ($surat_jalan_details->isEmpty())
          <p class="text-center m-0">Tidak ada data barang.</p>
        @else
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead>
                <th>Nomor Faktur</th>
                <th>Nama Barang</th>
                <th>Jumlah Barang Keluar</th>
                <th>Jumlah Barang Dikirim</th>
                <th>Satuan</th>
              </thead>
              <tbody>
                @foreach ($surat_jalan_details as $item)
                  <tr>
                    <td>{{ $item->jual->nomor_faktur }}</td>
                    <td>{{ $item->barang->nama }}</td>
                    <td>{{ number_format($item->jualDetail->jumlah_barang_keluar, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->jumlah_barang_dikirim, 0, ',', '.') }}</td>
                    <td>{{ $item->barang->satuan }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>

  </div>
@endsection
