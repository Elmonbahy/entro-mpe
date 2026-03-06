@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Laporan List Pengiriman" class="mb-3">
    </x-page-header>

    <div class="card mb-3">
      <div class="p-3 card-header d-flex align-items-center">
        <p class="mb-0 fw-semibold me-2">Form Laporan List Pengiriman</p>
        <x-info-tooltip
          message="• Pilih tanggal awal & akhir untuk laporan.
• Jika tidak memilih pelanggan, rentang maksimal 1 bulan.
• Jika memilih pelanggan, rentang bisa lebih 1 bulan." />
      </div>

      <div class="p-3">
        <form method="GET" action="{{ route('gudang.laporan-list-pengiriman.index') }}" autocomplete="off">
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
              <x-form.label value="Pelanggan" />
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
          <p class="mb-0 fw-semibold">Tabel Laporan List Pengiriman</p>
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
          <p class="mb-0 fw-semibold">Tabel Laporan List Pengiriman</p>
          <a href="{{ route('gudang.laporan-list-pengiriman.excel') }}?tgl_awal={{ $tgl_awal }}&tgl_akhir={{ $tgl_akhir }}&pelanggan_id={{ $pelanggan_id }}"
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
                  <th>Nomor Faktur</th>
                  <th>Tanggal Faktur</th>
                  <th>Pelanggan</th>
                  <th>Sales</th>
                  <th>Status Kirim</th>
                  <th>Rayon</th>
                  <th>Nama PJ</th>
                  <th>Kendaraan</th>
                  <th>Koli</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($data as $index => $row)
                  <tr>
                    <td>{{ $row['nomor_surat_jalan'] }}</td>
                    <td>{{ $row['tgl_surat_jalan'] }}</td>
                    <td>{{ $row['nomor_faktur'] }}</td>
                    <td>{{ $row['tgl_faktur'] }}</td>
                    <td class="text-start">{{ $row['pelanggan'] }}</td>
                    <td class="text-start">{{ $row['sales'] }}</td>
                    <td>{{ $row['status_kirim'] }}</td>
                    <td>{{ $row['rayon'] }}</td>
                    <td class="text-start">{{ $row['staf_logistik'] }}</td>
                    <td class="text-start">{{ $row['kendaraan'] }}</td>
                    <td>{{ $row['koli'] }}</td>
                  </tr>
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
