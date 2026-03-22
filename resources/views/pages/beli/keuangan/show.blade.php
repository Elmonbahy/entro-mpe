@extends('layouts.main-layout')


@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Pembelian" class="mb-3" withBackButton />
    <x-alert.session-alert />

    <div class="mb-3">
      <x-card.faktur-beli-detail :beli="$beli" />
    </div>

    <div class="card mb-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar Pembelian Barang</p>
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered mb-0">
            <thead>
              <th>Brand</th>
              <th>Nama</th>
              <th>Jumlah Pesanan</th>
              <th>Jumlah Masuk</th>
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
                  <td>{{ $item->jumlah_barang_masuk ? number_format($item->jumlah_barang_masuk, 0, ',', '.') : '-' }}</td>
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
                  <td>{{ Number::currency($item->harga_beli, in: 'IDR', locale: 'id_ID') }}</td>
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

    @if ($beli->status_bayar === \App\Enums\StatusBayar::UNPAID && $beli->status_faktur == \App\Enums\StatusFaktur::DONE)
      <div class="card mb-3">
        <div class="card-header p-3 d-flex justify-content-between align-items-center">
          <p class="mb-0 fw-semibold">Pembayaran</p>
        </div>

        <div class="card-body">
          <form action="{{ route('keuangan.beli.payment', ['id' => $beli->id]) }}" method="POST"
            x-data="form()">
            @csrf
            @method('PATCH')

            <div class="row">
              <div class="col-md-3 mb-3">
                <x-form.label value="Tanggal Bayar" />
                <x-form.input name="tgl_bayar" type="date" />
              </div>

              <div class="col-md-3 mb-3">
                <x-form.label value="Metode Bayar" />
                <x-form.select name="metode_bayar" :options="$metode_bayars" :selected="old('metode_bayar')"
                  placeholder="Cari atau pilih metode bayar" />
              </div>

              <div class="col-md-3 mb-3">
                <x-form.label value="Tipe Bayar" />
                <x-form.select name="tipe_bayar" :options="$tipe_bayars" :selected="old('tipe_bayar')" x-model="tipe_bayar" />
              </div>

              <div class="col-md-3 mb-3">
                <x-form.label value="Jumlah Bayar" />
                <x-form.input name="terbayar_display" placeholder="Input jumlah bayar..."
                  x-on:input="terbayar = terbayar_display.replace(/\./g, '').replace(/,/g, '.')"
                  x-model="terbayar_display" x-mask:dynamic="$money($input, ',', '.', 2)"
                  x-bind:disabled="tipe_bayar !== '{{ \App\Constants\Bayar::CICIL }}'" />

                <input type="hidden" name="terbayar" x-bind:value="terbayar" />
              </div>
            </div>

            <div class="mb-3">
              <x-form.label value="Keterangan Bayar" optional />
              <x-form.input name="keterangan_bayar" placeholder="Input keterangan bayar..." />
            </div>

            <button class="btn btn-primary">
              Simpan Pembayaran
            </button>
          </form>
        </div>
      </div>
    @endif

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
