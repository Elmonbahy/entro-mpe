@extends('layouts.main-layout')

@section('title')
  Detail sampel masuk
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Sampel Masuk" class="mb-3" withBackButton />

    <div class="mb-3">
      <x-card.sample-masuk-detail :sample_in="$sample_in" />
    </div>

    <div class="card mt-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar Barang Sampel Masuk</p>

        <a class="btn btn-primary" href="{{ route('supervisor.sample-in.add-item', ['id' => $sample_in->id]) }}">
          Sesuaikan
        </a>

      </div>

      <div class="card-body">
        @if ($sample_in_details->isEmpty())
          <p class="mb-0 text-center">Belum ada barang, klik sesuaikan untuk menambahkan barang.</p>
        @else
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead>
                <th>Brand</th>
                <th>Kode Barang</th>
                <th>Nama</th>
                <th>Pesanan</th>
                <th>Masuk</th>
                <th>Satuan</th>
                <th>Batch</th>
                <th>Tgl Expired</th>
              </thead>
              <tbody>
                @foreach ($sample_in_details as $item)
                  <tr wire:key="{{ $item->id }}">
                    <td>{{ $item->sampleBarang->barang->brand->nama }}</td>
                    <td>{{ $item->sampleBarang->barang->kode }}</td>
                    <td>{{ $item->sampleBarang->barang->nama }}</td>
                    <td>{{ number_format($item->jumlah_barang_dipesan, 0, ',', '.') }}</td>
                    <td>{{ $item->jumlah_barang_masuk ? number_format($item->jumlah_barang_masuk, 0, ',', '.') : '-' }}
                    </td>
                    <td>{{ $item->sampleBarang->satuan }}</td>
                    <td>{{ $item->batch ?? '-' }}</td>
                    <td>
                      @if ($item->tgl_expired)
                        {{ \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') }}
                      @else
                        -
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>

          </div>
        @endif
      </div>
    </div>


    <div class="card mt-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar barang sampel retur</p>
      </div>

      <div class="card-body">
        @if ($returs->isEmpty())
          <p class="mb-0 text-center">Tidak ada data</p>
        @else
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead class="text-nowrap">
                <th>Brand</th>
                <th>Kode Barang</th>
                <th>Nama</th>
                <th>Retur</th>
                <th>Satuan</th>
                <th>Batch</th>
                <th>Tgl Expired</th>
                <th>Keterangan</th>
                <th>Diganti</th>
              </thead>
              <tbody>
                @foreach ($returs as $item)
                  <tr>
                    <td>{{ $item->sampleBarang->barang->brand->nama }}</td>
                    <td>{{ $item->sampleBarang->barang->kode }}</td>
                    <td>{{ $item->sampleBarang->barang->nama }}</td>
                    <td>{{ $item->jumlah_barang_retur }}</td>
                    <td>{{ $item->sampleBarang->satuan }}</td>
                    <td>{{ $item->returnable->batch ?: '-' }}</td>
                    <td>
                      {{ $item->returnable->tgl_expired ? \Carbon\Carbon::parse($item->returnable->tgl_expired)->format('d/m/Y') : '-' }}
                    </td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                    <td>
                      @if ($item->is_diganti && !$item->diganti_at)
                        <i class="bi bi-check-circle-fill text-warning"></i>
                      @elseif ($item->is_diganti && $item->diganti_at)
                        <i class="bi bi-check-circle-fill text-success"></i>
                      @else
                        <i class="bi bi-dash-circle-fill text-danger"></i>
                      @endif
                    </td>
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
