@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Laporan Pengiriman Barang" class="mb-3">
    </x-page-header>

    <div class="card mb-3">
      <div class="p-3 card-header d-flex align-items-center">
        <p class="mb-0 fw-semibold me-2">Form Laporan Pengiriman Barang</p>
      </div>

      <div class="p-3">
        <form method="GET" action="{{ route('gudang.laporan-pengiriman.index') }}" autocomplete="off">
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
          <p class="mb-0 fw-semibold">Tabel Laporan Pengiriman Barang</p>
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
          <p class="mb-0 fw-semibold">Tabel Laporan Pengiriman Barang</p>
          <a href="{{ route('gudang.laporan-pengiriman.excel') }}?tgl_awal={{ $tgl_awal }}&tgl_akhir={{ $tgl_akhir }}&pelanggan_id={{ $pelanggan_id }}"
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
                  <th>Nomor Surat Jalan</th>
                  <th>Tanggal Surat Jalan</th>
                  <th>Pelanggan</th>
                  <th>Kendaraan</th>
                  <th>Koli</th>
                  <th>Nama PJ</th>
                  <th>No</th>
                  <th>Nomor Faktur</th>
                  <th>Status Kirim</th>
                  <th>Tanggal faktur</th>
                  <th>Brand</th>
                  <th>Nama Barang</th>
                  <th>Jumlah Keluar</th>
                  <th>Jumlah kirim</th>
                  <th>Satuan</th>
                </tr>
              </thead>

              <tbody>
                @foreach ($data as $surat_jalan)
                  @foreach ($surat_jalan['sj_details'] as $index => $item)
                    <tr>
                      @if ($index === 0)
                        <td rowspan="{{ count($surat_jalan['sj_details']) }}">
                          {{ $surat_jalan['nomor_surat_jalan'] }}
                        </td>
                        <td rowspan="{{ count($surat_jalan['sj_details']) }}">
                          {{ $surat_jalan['tgl_surat_jalan'] }}
                        </td>
                        <td rowspan="{{ count($surat_jalan['sj_details']) }}" class="text-start">
                          {{ $surat_jalan['pelanggan_nama'] }}
                        </td>
                        <td rowspan="{{ count($surat_jalan['sj_details']) }}" class="text-start">
                          {{ $surat_jalan['kendaraan'] }}
                        </td>
                        <td rowspan="{{ count($surat_jalan['sj_details']) }}">
                          {{ $surat_jalan['koli'] }}
                        </td>
                        <td rowspan="{{ count($surat_jalan['sj_details']) }}">
                          {{ $surat_jalan['staf_logistik'] }}
                        </td>
                      @endif

                      <td>{{ $index + 1 }}</td>
                      <td>{{ $item['nomor_faktur'] }}</td>
                      <td>{{ $item['status_kirim'] }}</td>
                      <td>{{ $item['tgl_faktur'] }}</td>
                      <td class="text-start">{{ $item['brand'] }}</td>
                      <td class="text-start">{{ $item['barang_nama'] }}</td>
                      <td>{{ number_format($item['jumlah_barang_keluar'], 0, ',', '.') }}</td>
                      <td>{{ number_format($item['jumlah_barang_dikirim'], 0, ',', '.') }}</td>
                      <td class="text-start">{{ $item['satuan'] }}</td>
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
    });
  </script>
@endpush
