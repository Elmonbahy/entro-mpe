@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />

    <x-page-header title="Penjualan" class="mb-3">
    </x-page-header>

    <div class="mb-3">
      <x-card.faktur-jual-detail :jual="$jual" />
    </div>

    <div class="mb-3">
      <x-card.faktur-jual-surat-jalan :jual="$jual" />
    </div>

    <div class="mb-3">
      <x-card.faktur-jual-detail-bayar :jual="$jual" />
    </div>

    <div class="card mt-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar Penjualan Barang</p>
      </div>

      <div class="card-body">
        @if ($jual_details->isEmpty())
          <p class="mb-0 text-center">Belum ada barang.</p>
        @else
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead>
                <th>Brand</th>
                <th>Nama</th>
                <th>Pesanan</th>
                <th>Keluar</th>
                <th>Satuan</th>
                <th>Diskon1</th>
                <th>Diskon2</th>
                <th>Batch</th>
                <th>Tgl Expired</th>
                <th>Harga Jual</th>
                <th>Total</th>
              </thead>
              <tbody>
                @foreach ($jual_details as $item)
                  <tr>
                    <td>{{ $item->barang->brand->nama }}</td>
                    <td>{{ $item->barang->nama }}</td>
                    <td>{{ number_format($item->jumlah_barang_dipesan, 0, ',', '.') }}</td>
                    <td>{{ $item->jumlah_barang_keluar ? number_format($item->jumlah_barang_keluar, 0, ',', '.') : '-' }}
                    </td>
                    <td>{{ $item->barang->satuan }}</td>
                    <td>{{ $item->diskon1 }}</td>
                    <td>{{ $item->diskon2 }}</td>
                    <td>{{ $item->batch ?? '-' }}</td>
                    <td>
                      @if ($item->tgl_expired)
                        {{ \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') }}
                      @else
                        -
                      @endif
                    </td>
                    <td>{{ formatCurrencyDinamis($item->harga_jual) }}</td>
                    <td>{{ Number::currency($item->total_tagihan, in: 'IDR', locale: 'id_ID') }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="10" class="fw-bold text-end">
                    Total Faktur
                  </td>
                  <td class="fw-bold" colspan="3">
                    {{ Number::currency(round($jual_details->sum('total_tagihan')), in: 'IDR', locale: 'id_ID') }}</td>
                </tr>
              </tfoot>
            </table>

          </div>
        @endif
      </div>
    </div>

    <x-card.retur-list :returs="$returs" :jual="$jual" />
  </div>
@endsection
