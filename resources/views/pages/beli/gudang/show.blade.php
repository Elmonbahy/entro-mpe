@extends('layouts.main-layout')

@section('title')
  Detail barang masuk
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Barang masuk" class="mb-3" withBackButton />
    <x-alert.session-alert />

    <x-card.faktur-beli-detail :beli="$beli" />

    <div class="card mt-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar pembelian barang</p>

        @if ($beli->status_faktur == \App\Enums\StatusFaktur::PROCESS_GUDANG)
          <form method="POST" action="{{ route('gudang.beli.done', ['id' => $beli->id]) }}"
            onsubmit="return confirm('Apakah barang masuk sudah sesuai?')">
            @csrf
            @method('PATCH')

            <button class="btn btn-primary">
              Selesai
            </button>
          </form>
        @endif
      </div>

      <div class="card-body">
        @if ($beli_details->isEmpty())
          <p class="mb-0 text-center">Tidak ada data.</p>
        @else
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead class="text-nowrap">
                <th>Brand</th>
                <th>Nama</th>
                <th>Pesanan</th>
                <th>Masuk</th>
                <th>Satuan</th>
                <th>Batch</th>
                <th>Tgl Expired</th>
                <th>Status barang masuk</th>
                <th>Action</th>
              </thead>
              <tbody>
                @foreach ($beli_details as $item)
                  <tr wire:key="{{ $item->id }}">
                    <td>{{ $item->barang->brand->nama }}</td>
                    <td>{{ $item->barang->nama }}</td>
                    <td>{{ number_format($item->jumlah_barang_dipesan, 0, ',', '.') }}</td>
                    <td>{{ $item->jumlah_barang_masuk ? number_format($item->jumlah_barang_masuk, 0, ',', '.') : '-' }}
                    </td>
                    <td>{{ $item->barang->satuan }}</td>
                    <td>{{ $item->batch ?? '-' }}</td>
                    <td>{{ $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') : '-' }}</td>
                    <td>
                      <x-badge.status-barang-masuk :status="$item->status_barang_masuk" />
                    </td>

                    <td>
                      <div class="d-flex gap-2">
                        @can('stock', $item)
                          <a href="{{ route('gudang.beli.stock-item', ['beli_detail_id' => $item->id, 'id' => $beli->id]) }}"
                            class="btn btn-primary" title="stock barang masuk">
                            <i class="bi bi-plus-slash-minus"></i>
                          </a>
                        @endcan
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
