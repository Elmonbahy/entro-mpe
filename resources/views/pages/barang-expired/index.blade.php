@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Laporan Barang Expired" class="mb-3">
    </x-page-header>

    <div class="card mb-3">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form Barang Expired</p>
      </div>

      <div class="p-3">
        <form method="GET" action="{{ route('gudang.barang-expired.index') }}" autocomplete="off">
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

          <button type="submit" class="btn btn-primary">
            Lihat Barang Expired
          </button>
        </form>
      </div>
    </div>

    @if ($data->isEmpty())
      <div class="card">
        <div class="p-3 card-header">
          <p class="mb-0 fw-semibold">Tabel Barang Expired</p>
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
          <p class="mb-0 fw-semibold">Tabel Laporan Barang Expired</p>
          <a href="{{ route('gudang.barang-expired.excel') }}?tgl_awal={{ $tgl_awal }}&tgl_akhir={{ $tgl_akhir }}"
            class="btn btn-primary">
            Export Excel
          </a>
        </div>

        <div class="card-body">
          <div class="table-responsive">

            <table class="table table-bordered small text-center">
              <!-- Header Row -->
              <thead class="text-nowrap">
                <tr>
                  <th>Brand</th>
                  <th>Nama Barang</th>
                  <th>Jumlah</th>
                  <th>Satuan</th>
                  <th>Batch</th>
                  <th>Tgl Expired</th>
                  <th>Sisa Hari</th>
                </tr>
              </thead>
              <tbody>
                @forelse($data as $item)
                  <tr>
                    <td class="text-start">{{ $item['brand_nama'] }}</td>
                    <td class="text-start">{{ $item['barang_nama'] }}</td>
                    <td>{{ number_format($item['jumlah_stock']) }}</td>
                    <td class="text-start">{{ $item['satuan'] }}</td>
                    <td>{{ $item['batch'] }}</td>
                    <td>{{ $item['tgl_expired'] }}</td>
                    <td @if (Str::startsWith($item['sisa_waktu'], 'Kadaluarsa')) class="text-danger fw-bold" @endif>
                      {{ $item['sisa_waktu'] }}
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                  </tr>
                @endforelse
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
      new TomSelect('#supplier_id');
    });
  </script>
@endpush
