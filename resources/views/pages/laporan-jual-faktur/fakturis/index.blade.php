@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Laporan Faktur Jual" class="mb-3">
    </x-page-header>

    <div class="card mb-3">
      <div class="p-3 card-header d-flex align-items-center">
        <p class="mb-0 fw-semibold me-2">Form Laporan Faktur Jual</p>
      </div>

      <div class="p-3">
        <form method="GET" action="{{ route('fakturis.laporan-jual-faktur.index') }}" autocomplete="off">
          <div class="row">
            <div class="col-md-4 mb-3">
              <x-form.label value="Tanggal awal" />
              <x-form.input name="tgl_awal" wire:model="tgl_awal" type="date" value="{{ $tgl_awal }}" />
            </div>
            <div class="col-md-4 mb-3">
              <x-form.label value="Tanggal akhir" />
              <x-form.input name="tgl_akhir" wire:model="tgl_akhir" type="date" value="{{ $tgl_akhir }}" />
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <x-form.label value="Pelanggan" optional />
              <x-form.select name="pelanggan_id" placeholder="Cari atau pilih pelanggan" :options="$pelanggans" valueKey="id"
                labelKey="nama" :selected="$pelanggan_id" />
            </div>
            <div class="col-md-4 mb-3">
              <x-form.label value="Sales" optional />
              <x-form.select name="sales_id" placeholder="Cari atau pilih sales" :options="$sales" valueKey="id"
                labelKey="nama" :selected="$sales_id" />
            </div>
          </div>

          <button type="submit" class="btn btn-primary">
            Lihat Laporan
          </button>
        </form>
      </div>
    </div>

    @if ($data->isEmpty())
      <div class="card">
        <div class="p-3 card-header">
          <p class="mb-0 fw-semibold">Tabel Laporan Faktur Jual</p>
        </div>
        <div class="card-body">
          <div class="d-flex gap-2 text-info-emphasis">
            <i class="bi bi-info-circle-fill"></i>
            <p class="mb-0">Data tidak tersedia</p>
          </div>
        </div>
      </div>
    @endif

    @if ($data->isNotEmpty())
      <div class="card">
        <div class="p-3 card-header d-flex justify-content-between align-items-center">
          <p class="mb-0 fw-semibold">Tabel Laporan Faktur Jual</p>
          <a href="{{ route('fakturis.laporan-jual-faktur.excel') }}?tgl_awal={{ $tgl_awal }}&tgl_akhir={{ $tgl_akhir }}&pelanggan_id={{ $pelanggan_id }}&sales_id={{ $sales_id }}"
            class="btn btn-primary">
            Export Excel
          </a>
        </div>

        <div class="card-body">
          <x-scroll-buttons />
          <div class="table-responsive">
            <table class="table table-bordered small text-center">
              <!-- Header Row -->
              <thead class="text-nowrap">
                <tr>
                  <th>Pelanggan</th>
                  <th>Nomor Faktur</th>
                  <th>Sales</th>
                  <th>Tanggal</th>
                  <th>No</th>
                  <th>Nama Barang</th>
                  <th>Jumlah</th>
                  <th>Satuan</th>
                  <th>Harga Jual</th>
                </tr>
              </thead>

              <tbody>
                @foreach ($data as $faktur)
                  @php
                    $rowspan = $faktur['jual_details_count'] + 2;
                  @endphp

                  <tr>
                    {{-- rowspan 4: total jual detail + 2 --}}
                    <td rowspan="{{ $rowspan }}" class="text-start">
                      {{ $faktur['pelanggan_nama'] }}
                    </td>
                    <td rowspan="{{ $rowspan }}">
                      {{ $faktur['nomor_faktur'] }}
                    </td>
                    <td rowspan="{{ $rowspan }}">
                      {{ $faktur['sales_nama'] }}
                    </td>
                    <td rowspan="{{ $rowspan }}">
                      {{ $faktur['tgl_faktur'] }}
                    </td>
                  </tr>
                  {{-- end jual --}}

                  {{-- start jual detail --}}
                  @foreach ($faktur['jual_details'] as $item)
                    <tr>
                      <td>{{ $loop->iteration }}</td> {{-- nomor urut --}}
                      <td class="text-start">{{ $item['barang_nama'] }}</td> {{-- nama barang --}}
                      <td>{{ number_format($item['jumlah_barang_keluar'], 0, ',', '.') }}</td> {{-- jumlah barang keluar --}}
                      <td class="text-start">{{ $item['barang_satuan'] }}</td> {{-- nama barang --}}
                      <td class="text-end">{{ number_format($item['harga_jual'], 2, ',', '.') }}</td>
                    </tr>
                  @endforeach
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    @endif

  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      new TomSelect('#pelanggan_id');
      new TomSelect('#sales_id');
    });
  </script>
@endpush
