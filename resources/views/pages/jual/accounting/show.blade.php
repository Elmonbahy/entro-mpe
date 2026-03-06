@extends('layouts.main-layout')


@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Penjualan" class="mb-3" withBackButton />
    <x-alert.session-alert />

    <div class="mb-3">
      <x-card.faktur-jual-detail :jual="$jual" />
    </div>

    <div class="mb-3">
      <x-card.faktur-jual-surat-jalan :jual="$jual" />
    </div>

    <div class="card mb-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar Penjualan Barang</p>
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered mb-0">
            <thead>
              <th>Id</th>
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
                <tr wire:key="{{ $item->id }}">
                  <td>{{ $item->barang->id }}</td>
                  <td class="text-start">{{ $item->barang->brand->nama }}</td>
                  <td class="text-start">{{ $item->barang->nama }}</td>
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
                  <td>{{ Number::currency($item->harga_jual, in: 'IDR', locale: 'id_ID') }}</td>
                  <td>{{ Number::currency(round($item->total_tagihan), in: 'IDR', locale: 'id_ID') }}
                  </td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="11" class="fw-bold text-end">
                  Total
                </td>
                <td class="fw-bold" colspan="3">
                  {{ Number::currency(round($jual_details->sum('total_tagihan')), in: 'IDR', locale: 'id_ID') }}</td>
              </tr>
            </tfoot>
          </table>

        </div>
      </div>
    </div>

    <div class="card mt-3 mb-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar barang retur</p>
      </div>

      <div class="card-body">
        @if ($returs->isEmpty())
          <p class="mb-0 text-center">Tidak ada data</p>
        @else
          <div class="table-responsive">
            <table class="table table-bordered mb-0">
              <thead class="text-nowrap">
                <th>Tanggal Retur</th>
                <th>Id</th>
                <th>Brand</th>
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
                    <td>
                      {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') : '-' }}
                    </td>
                    <td>{{ $item->barang->id }}</td>
                    <td>{{ $item->barang->brand->nama }}</td>
                    <td>{{ $item->barang->nama }}</td>
                    <td>{{ $item->jumlah_barang_retur }}</td>
                    <td>{{ $item->barang->satuan }}</td>
                    <td>{{ $item->returnable->batch ?: '-' }}</td>
                    <td>
                      {{ $item->returnable->tgl_expired ? \Carbon\Carbon::parse($item->returnable->tgl_expired)->format('d/m/Y') : '-' }}
                    </td>
                    <td>{{ $item->keterangan }}</td>
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

    <div class="mb-3">
      <x-card.faktur-jual-detail-bayar :jual="$jual" />
    </div>

    <div class="card mb-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Riwayat Pembayaran</p>
      </div>

      <div class="card-body p-2">
        @if (!$jual->bayar)
          <p class="text-center m-0 p-3">Belum ada riwayat pembayaran.</p>
        @else
          @foreach ($jual->bayar as $item)
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
        tipe_bayar: '{{ $jual->bayar === null ? \App\Constants\Bayar::KONTAN : \App\Constants\Bayar::CICIL }}',
      }));
    });

    document.addEventListener('DOMContentLoaded', function() {
      new TomSelect('#metode_bayar');
      new TomSelect('#tipe_bayar');
      new TomSelect('#is_pungut_ppn');
    });
  </script>
@endpush
