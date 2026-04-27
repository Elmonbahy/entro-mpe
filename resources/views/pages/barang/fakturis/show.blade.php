@extends('layouts.main-layout')

@section('title')
  Detail data barang
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Data barang" class="mb-3" withBackButton />

    <div class="row">
      <div class="col-xl-6 col-md-5">
        <div class="card">
          <div class="p-3 card-header">
            <p class="mb-0 fw-semibold">Detail data barang</p>
          </div>
          <div class="card-body p-0">
            <table class="table mb-0">
              <tbody>
                <tr>
                  <td class="ps-3 fw-bold text-muted fw-bold" width="40%">ID Barang</td>
                  <td class="pe-3 fw-bold">{{ $barang->id ?? '-' }}</td>
                </tr>
                <tr>
                  <td class="ps-3 fw-bold text-muted">Kode Barang</td>
                  <td class="pe-3">{{ $barang->kode ?? '-' }}</td>
                </tr>
                <tr>
                  <td class="ps-3 fw-bold text-muted">Nama Barang</td>
                  <td class="pe-3 fw-bold">{{ $barang->nama }}</td>
                </tr>
                <tr>
                  <td class="ps-3 fw-bold text-muted">Brand</td>
                  <td class="pe-3">{{ $barang->brand->nama ?? '-' }}</td>
                </tr>
                <tr>
                  <td class="ps-3 fw-bold text-muted">Satuan</td>
                  <td class="pe-3"><span class="badge bg-info text-dark">{{ $barang->satuan }}</span></td>
                </tr>
                <tr>
                  <td class="ps-3 fw-bold text-muted">Supplier</td>
                  <td class="pe-3">{{ $barang->supplier->nama ?? '-' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="card-footer bg-body-tertiary border-0">
            <small class="text-muted">NIE: {{ $barang->nie ?? '-' }}</small>
          </div>
        </div>
      </div>

      <div class="col-xl-6 col-md-7">
        <div class="card">
          <div class="p-3 card-header">
            <div class="d-flex justify-content-between align-items-center">
              <p class="mb-0 fw-semibold">Data stock barang</p>
            </div>
          </div>

          <div class="card-body">
            <div class="table-responsive">
              <table class="table align-middle">
                <thead class="bg-light text-center small text-uppercase fw-bold">
                  <tr>
                    <th class="py-2">Batch</th>
                    <th class="py-2">Expired</th>
                    <th class="py-2">Stok</th>
                    <th class="py-2">Satuan</th>
                  </tr>
                </thead>
                <tbody class="text-center">
                  @forelse ($barang_stocks as $item)
                    <tr>
                      <td>{{ $item->batch ?: '-' }}</td>
                      <td>
                        @php
                          $isExpired = $item->tgl_expired && \Carbon\Carbon::parse($item->tgl_expired)->isPast();
                        @endphp
                        <span class="{{ $isExpired ? 'text-danger fw-bold' : '' }}">
                          {{ $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') : '-' }}
                        </span>
                      </td>
                      <td>{{ number_format($item->jumlah_stock, 0, ',', '.') }}</td>
                      <td>{{ $barang->satuan }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="py-4 text-muted small">Tidak ada stok tersedia.</td>
                    </tr>
                  @endforelse
                </tbody>
                @if ($barang_stocks->count() > 0)
                  <tfoot class="bg-dark text-white text-center">
                    <tr>
                      <td colspan="2"></td>
                      <td class="fw-bold py-2">
                        {{ number_format($barang_stocks->sum('jumlah_stock'), 0, ',', '.') }}
                      </td>
                      <td class="py-2">{{ $barang->satuan }}</td>
                    </tr>
                  </tfoot>
                @endif
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
