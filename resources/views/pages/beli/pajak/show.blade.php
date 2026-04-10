@extends('layouts.main-layout')

@section('title')
  Detail faktur beli
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />

    <x-page-header title="Pembelian" class="mb-3" withBackButton />

    <div class="mb-3">
      <x-card.faktur-beli-detail :beli="$beli" />
    </div>

    <div class="card mt-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar Pembelian Barang</p>
      </div>

      <div class="card-body">
        @if ($beli_details->isEmpty())
          <p class="mb-0 text-center">Belum ada barang.</p>
        @else
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead>
                <th>Brand</th>
                <th>Nama</th>
                <th>Pesanan</th>
                <th>Masuk</th>
                <th>Satuan</th>
                <th>Diskon1</th>
                <th>Diskon2</th>
                <th>Batch</th>
                <th>Tgl Expired</th>
                <th>Harga Beli</th>
                <th>Total</th>
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
                    <td>{{ formatCurrencyDinamis($item->harga_beli) }}</td>
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
                    {{ Number::currency(round($beli_details->sum('total_tagihan')), in: 'IDR', locale: 'id_ID') }}</td>
                </tr>
              </tfoot>
            </table>

          </div>
        @endif
      </div>
    </div>

    <x-card.retur-list :returs="$returs" />

    <div class="mb-3">
      <x-card.faktur-beli-detail-bayar :beli="$beli" />
    </div>

    <div class="card mb-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Riwayat Pembayaran</p>
      </div>

      <div class="card-body p-2">
        @if (!$beli->bayar)
          <p class="text-center m-0 p-3">Belum ada riwayat pembayaran.</p>
        @else
          @foreach ($beli->bayar as $item)
            @if ($item['tipe_bayar'] === \App\Constants\Bayar::CICIL)
              <p class="px-2 fw-bold m-0">Cicil {{ $item['x_cicil'] }}</p>
            @endif

            <table class="table">
              <tbody>
                <tr>
                  <td width="230">Metode bayar </td>
                  <td>{{ $item['metode_bayar'] }}
                  </td>
                </tr>
                <tr>
                  <td width="230">Tipe bayar </td>
                  <td>{{ $item['tipe_bayar'] }}
                  </td>
                </tr>
                <tr>
                  <td width="230">Tanggal bayar </td>
                  <td>{{ \Carbon\Carbon::parse($item['tgl_bayar'])->format('d/m/Y') }}
                  </td>
                </tr>
                <tr>
                  <td width="230">Jumlah bayar </td>
                  <td>
                    {{ Number::currency($item['terbayar'], in: 'IDR', locale: 'id_ID') }}
                  </td>
                </tr>
              </tbody>
            </table>
          @endforeach
        @endif
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('form', () => ({
        terbayar_display: '',
        terbayar: 0,
        tipe_bayar: '{{ $beli->bayar === null ? \App\Constants\Bayar::KONTAN : \App\Constants\Bayar::CICIL }}',
      }));
    });

    document.addEventListener('DOMContentLoaded', function() {
      new TomSelect('#metode_bayar');
      new TomSelect('#tipe_bayar');
    });
  </script>
@endpush
