@extends('layouts.main-layout')

@php
  function stringToColor($batch, $expired)
  {
      $batch = trim($batch ?? '');
      $expired = $expired ? \Carbon\Carbon::parse($expired)->format('Y-m-d') : '';

      $combined = crc32($batch) + crc32($expired);
      $hue = $combined % 360;
      return "hsl($hue, 60%, 85%)";
  }
@endphp

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Data Kartu Stock" class="mb-3">
    </x-page-header>

    <div class="card mb-3">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form kartu stock</p>
      </div>

      <div class="p-3">
        @livewire('accounting.kartu-stock', [
            'brand_id' => $brand_id,
            'barang_id' => $barang_id,
            'tgl_akhir' => $tgl_akhir,
            'tgl_awal' => $tgl_awal,
        ])
      </div>
    </div>

    @if ($mutations->isEmpty())
      <div class="card">
        <div class="p-3 card-header">
          <p class="mb-0 fw-semibold">Tabel kartu stock</p>
        </div>
        <div class="card-body">
          <div class="d-flex gap-2 text-info-emphasis">
            <i class="bi bi-info-circle-fill"></i>
            <p class="mb-0">Data tidak tersedia</p>
          </div>
        </div>
      </div>
    @endif

    @if ($mutations->isNotEmpty())
      <div class="card">
        <x-scroll-buttons />
        <div class="p-3 card-header">
          <p class="mb-0 fw-semibold">Tabel kartu stock</p>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <th>Nomor Faktur</th>
                <th>Tanggal</th>
                <th>Supplier/Pelangan</th>
                <th>Batch</th>
                <th>Expired</th>
                <th>Mutasi</th>
                <th>Posisi</th>
                <th>Arus</th>
                <th>Harga</th>
                <th>Disc1</th>
                <th>Disc2</th>
                <th>Keterangan</th>
              </thead>
              <tbody>
                @foreach ($mutations as $item)
                  @php
                    $expiredRaw =
                        $item->mutation_type === 'BarangRusak'
                            ? optional($item->mutationable?->barangStock)->tgl_expired
                            : optional($item->mutationable)->tgl_expired;
                    $color = stringToColor($item->batch ?? '', $expiredRaw);
                  @endphp
                  <tr>
                    <td>
                      @if ($url = $item->getNomorFakturUrlFor(auth()->user()))
                        <a href="{{ $url }}"
                          class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">
                          <i class="fas fa-file-invoice"></i> {{ $item->nomor_faktur }}
                        </a>
                      @else
                        <span>{{ $item->nomor_faktur }}</span>
                      @endif
                    </td>
                    <td>
                      {{ \Carbon\Carbon::parse($item->tgl_mutation)->format('d/m/Y') }}
                    </td>
                    <td>
                      {{ $item->user }}
                    </td>
                    <td>
                      <span
                        style="background-color: {{ $color }}; color: #222; padding: 2px 6px; border-radius: 4px; display: inline-block;">
                        {{ $item->batch ?? '-' }}
                      </span>
                    </td>
                    <td>
                      <span
                        style="background-color: {{ $color }}; color: #222; padding: 2px 6px; border-radius: 4px; display: inline-block;">
                        {{ $item->expired }}
                      </span>
                    </td>
                    <td>
                      {{ $item->mutasi }}
                    </td>
                    <td>
                      {{ $item->stock_akhir }}
                    </td>
                    <td>
                      {{ $item->arus }}
                    </td>
                    <td>
                      {{ $item->harga }}
                    </td>
                    <td>
                      {{ $item->disc['diskon1'] }}
                    </td>
                    <td>
                      {{ $item->disc['diskon2'] }}
                    </td>
                    <td>
                      {{ $item->keterangan }}
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="card mt-3">
        <div class="p-3 card-header">
          <p class="mb-0 fw-semibold">Data stock barang</p>
        </div>

        <div class="p-3">
          @if ($barang_stocks->isEmpty())
            <p class="mb-0 text-center">
              Tidak ada stock barang
            </p>
          @else
            <table class="table mb-0 text-center">
              <thead>
                <th>Batch</th>
                <th>Expired</th>
                <th>Jumlah Stock</th>
                <th>Satuan</th>
              </thead>
              @foreach ($barang_stocks as $item)
                @php
                  $color = stringToColor($item->batch ?? '', $item->tgl_expired ?? '');
                @endphp

                <tr>
                  <td>
                    <span
                      style="background-color: {{ $color }}; color: #222; padding: 2px 6px; border-radius: 4px; display: inline-block;">
                      {{ $item->batch ?? '-' }}
                    </span>
                  </td>
                  <td>
                    <span
                      style="background-color: {{ $color }}; color: #222; padding: 2px 6px; border-radius: 4px; display: inline-block;">
                      {{ $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') : '-' }}
                    </span>
                  </td>
                  <td>{{ number_format($item->jumlah_stock, 0, ',', '.') }}</td>
                  <td>{{ $item->barang->satuan }}</td>
                </tr>
              @endforeach
              <tfoot>
                <tr>
                  <td colspan="2"></td>
                  <td><strong>{{ number_format($total_jumlah_stock, 0, ',', '.') }}</strong></td>
                  <td>{{ $item->barang->satuan }}</td>
                </tr>
              </tfoot>
            </table>
          @endif
        </div>
      </div>
    @endif

  </div>
@endsection
