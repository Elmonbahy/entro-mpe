@extends('layouts.main-layout')


@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Penjualan" class="mb-3" withBackButton />
    <x-alert.session-alert />

    <div class="mb-3">
      <x-card.faktur-jual-detail :jual="$jual" />
    </div>

    <div class="card mb-3">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Daftar Penjualan Barang</p>
      </div>

      <div class="card-body">
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
                <tr wire:key="{{ $item->id }}">
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
                  <td>{{ Number::currency($item->harga_jual, in: 'IDR', locale: 'id_ID') }}</td>
                  <td>{{ Number::currency(round($item->total_tagihan), in: 'IDR', locale: 'id_ID') }}</td>
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

    @if ($jual->status_bayar === \App\Enums\StatusBayar::UNPAID && $jual->status_faktur == \App\Enums\StatusFaktur::DONE)
      <div class="card mb-3">
        <div class="card-header p-3 d-flex justify-content-between align-items-center">
          <p class="mb-0 fw-semibold">Pembayaran</p>
        </div>

        <div class="card-body border rounded m-3 mb-0">
          <form action="{{ route('keuangan.jual.update', ['id' => $jual->id]) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="col-md mb-3">
              <x-form.label value="Pungut PPN" />
              <select name="is_pungut_ppn" id="is_pungut_ppn" class="form-select">
                <option value="1" {{ old('is_pungut_ppn', $jual->is_pungut_ppn) == 1 ? 'selected' : '' }}>Ya
                </option>
                <option value="0" {{ old('is_pungut_ppn', $jual->is_pungut_ppn) == 0 ? 'selected' : '' }}>Tidak
                </option>
              </select>

              @error('is_pungut_ppn')
                <div class="text-danger mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <x-form.label value="Keterangan Bayar" optional />
              <x-form.input name="keterangan_bayar" placeholder="Input keterangan bayar..." :value="$jual->keterangan_bayar" />
            </div>

            <div class="alert alert-warning">
              <p class="m-0">Mengubah pungut PPN akan menghapus riwayat pembayaran jika ada.</p>
            </div>

            <button class="btn btn-primary">
              Simpan
            </button>
          </form>
        </div>

        <div class="card-body border rounded m-3">
          <form action="{{ route('keuangan.jual.payment', ['id' => $jual->id]) }}" method="POST"
            x-data="form()">
            @csrf
            @method('PATCH')

            <div class="row">
              <div class="col-md mb-3">
                <x-form.label value="Tanggal Bayar" />
                <x-form.input name="tgl_bayar" type="date" />
              </div>

              <div class="col-md mb-3">
                <x-form.label value="Metode Bayar" />
                <x-form.select name="metode_bayar" :options="$metode_bayars" :selected="old('metode_bayar')" placeholder="Pilih metode bayar" />
              </div>

              <div class="col-md mb-3">
                <x-form.label value="Tipe Bayar" />
                <x-form.select name="tipe_bayar" :options="$tipe_bayars" :selected="old('tipe_bayar')" x-model="tipe_bayar" />
              </div>

              <div class="col-md mb-3">
                <x-form.label value="Jumlah Bayar" />
                <x-form.input name="terbayar_display" placeholder="Input jumlah bayar..."
                  x-on:input="terbayar = terbayar_display.replace(/\./g, '').replace(/,/g, '.')"
                  x-model="terbayar_display" x-mask:dynamic="$money($input, ',', '.', 2)"
                  x-bind:disabled="tipe_bayar !== '{{ \App\Constants\Bayar::CICIL }}'" />

                <input type="hidden" name="terbayar" x-bind:value="terbayar" />
              </div>
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
