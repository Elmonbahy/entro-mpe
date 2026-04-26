@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Barang Keluar" class="mb-3">
    </x-page-header>
    <x-alert.session-alert />

    <div class="mb-3">
      <x-card.faktur-jual-detail :jual="$jual" />
    </div>

    <div class="card mt-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar penjualan barang</p>

        @if ($jual->status_faktur == \App\Enums\StatusFaktur::PROCESS_GUDANG)
          <form method="POST" action="{{ route('gudang.jual.done', ['id' => $jual->id]) }}"
            onsubmit="return confirm('Apakah barang keluar sudah sesuai?')">
            @csrf
            @method('PATCH')

            <button class="btn btn-primary">
              Selesai
            </button>
          </form>
        @endif
      </div>

      <div class="card-body">
        @if ($jual_detail->isEmpty())
          <p class="mb-0 text-center">Tidak ada data.</p>
        @else
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead class="text-nowrap">
                <th>Brand</th>
                <th>Nama</th>
                <th>Batch</th>
                <th>Tgl Expired</th>
                <th>Pesanan</th>
                <th>Keluar</th>
                <th>Satuan</th>
                <th>Status barang keluar</th>
                <th>Action</th>
              </thead>
              <tbody>
                @foreach ($jual_detail as $item)
                  <tr>
                    <td>{{ $item->barang->brand->nama }}</td>
                    <td>{{ $item->barang->nama }}</td>
                    <td>{{ $item->batch ?? '-' }}</td>
                    <td>{{ $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') : '-' }}</td>
                    <td>{{ number_format($item->jumlah_barang_dipesan, 0, ',', '.') }}</td>
                    <td>{{ $item->jumlah_barang_keluar ? number_format($item->jumlah_barang_keluar, 0, ',', '.') : '-' }}
                    </td>
                    <td>{{ $item->barang->satuan }}</td>
                    <td>
                      <x-badge.status-barang-keluar :status="$item->status_barang_keluar" />
                    </td>

                    <td>
                      <div class="d-flex gap-2">
                        @unless ($jual->status_bayar === \App\Enums\StatusBayar::PAID)
                          @can('stock', $item)
                            <a href="{{ route('gudang.jual.stock-item', ['jual_detail_id' => $item->id, 'id' => $jual->id]) }}"
                              class="btn btn-primary" title="stock barang keluar">
                              <i class="bi bi-plus-slash-minus"></i>
                            </a>
                          @endcan
                        @endunless
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>

          </div>
        @endif
      </div>
    </div>

    <x-card.retur-list :returs="$returs" />
  </div>
@endsection
