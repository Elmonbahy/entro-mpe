@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Laporan Faktur Jual" class="mb-3">
    </x-page-header>

    <div class="card mb-3">
      <div class="p-3 card-header d-flex align-items-center">
        <p class="mb-0 fw-semibold me-2">Form Laporan Faktur Jual</p>
        <x-info-tooltip
          message="• Pilih tanggal awal & akhir untuk laporan.
• Jika tidak memilih pelanggan, rentang maksimal 1 bulan.
• Jika memilih pelanggan, rentang bisa lebih 1 bulan.
• Sales dan Status bayar opsional." />
      </div>

      <div class="p-3">
        <form method="GET" action="{{ route('keuangan.laporan-jual-faktur.index') }}" autocomplete="off">
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
            <div class="col-md-4 mb-3">
              <x-form.label value="Status Bayar" optional />
              <select name="status_bayar" class="form-control">
                <option value="" {{ request('status_bayar') == '' ? 'selected' : '' }}>Semua Status</option>
                <option value="PAID" {{ request('status_bayar') == 'PAID' ? 'selected' : '' }}>Lunas</option>
                <option value="UNPAID" {{ request('status_bayar') == 'UNPAID' ? 'selected' : '' }}>Belum Lunas</option>
              </select>
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
          <a href="{{ route('keuangan.laporan-jual-faktur.excel') }}?tgl_awal={{ $tgl_awal }}&tgl_akhir={{ $tgl_akhir }}&pelanggan_id={{ $pelanggan_id }}&sales_id={{ $sales_id }}&status_bayar={{ request('status_bayar') }}"
            class="btn btn-primary">
            Export Excel
          </a>
        </div>

        <div class="card-body">
          <div class="table-responsive">
            <x-scroll-buttons />
            <table class="table table-bordered small text-center">
              <!-- Header Row -->
              <thead class="text-nowrap">
                <tr>
                  <th>Pelanggan</th>
                  <th>Nomor Faktur</th>
                  <th>Sales</th>
                  <th>Tanggal</th>
                  <th>Status Bayar</th>
                  <th>Tipe Bayar</th>
                  <th>Tanggal Bayar</th>
                  <th>No</th>
                  <th>Nama Barang</th>
                  <th>Jumlah</th>
                  <th>Satuan</th>
                  <th>Disc</th>
                  <th>DiscRp</th>
                  <th>Disc2</th>
                  <th>Disc2Rp</th>
                  <th>Harga Jual</th>
                  <th>Total</th>
                  <th>DPP</th>
                  <th>PPN</th>
                </tr>
              </thead>

              <tbody>
                @foreach ($data as $faktur)
                  @php
                    $rowspan = $faktur['jual_details_count'] + 2;
                  @endphp

                  {{-- Start jual --}}
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
                    <td rowspan="{{ $rowspan }}">
                      {{ $faktur['status_bayar_label'] }}
                    </td>
                    <td rowspan="{{ $rowspan }}">
                      {{ $faktur['tipe_bayar'] }}
                    </td>
                    <td rowspan="{{ $rowspan }}">
                      {{ $faktur['tgl_bayar'] ?? '-' }}
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
                      <td>{{ $item['diskon1'] }}</td> {{-- diskon 1 --}}
                      <td class="text-end">{{ number_format($item['harga_diskon1'], 0, ',', '.') }}</td>
                      {{-- harga diskon 1  --}}
                      <td>{{ $item['diskon2'] }}</td> {{-- diskon 2 --}}
                      <td class="text-end">{{ number_format($item['harga_diskon2'], 0, ',', '.') }}</td>
                      {{-- harga diskon 2 --}}
                      <td class="text-end">{{ number_format($item['harga_jual'], 2, ',', '.') }}</td>
                      <td class="text-end">{{ number_format($item['total_tagihan'], 0, ',', '.') }}</td>
                      {{-- tagihan --}}
                      <td class="text-end">{{ number_format($item['dpp'], 0, ',', '.') }}</td>
                      {{-- HNA jual --}}
                      <td class="text-end">{{ number_format($item['harga_ppn'], 0, ',', '.') }}</td>
                      {{-- PPN jual --}}
                    </tr>
                  @endforeach
                  {{-- end jual detail --}}

                  {{-- start total jual detail --}}
                  <tr class="fw-bold text-end">
                    <td colspan="9"><strong>Total Faktur</strong></td>
                    <td>{{ number_format($faktur['total_tagihan'], 0, ',', '.') }}</td>
                    <td>{{ number_format($faktur['total_dpp'], 0, ',', '.') }}</td>
                    <td>{{ number_format($faktur['total_harga_ppn'], 0, ',', '.') }}</td>
                  </tr>

                  {{-- end total jual detail --}}
                @endforeach
              </tbody>
              <tfoot>
                <tr class="fw-bold text-end">
                  <td colspan="16"><strong>Grand Total</strong></td>
                  <td>{{ number_format(collect($data)->sum('total_tagihan'), 0, ',', '.') }}</td>
                  <td>{{ number_format(collect($data)->sum('total_dpp'), 0, ',', '.') }}</td>
                  <td>{{ number_format(collect($data)->sum('total_harga_ppn'), 0, ',', '.') }}</td>
                </tr>
              </tfoot>
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
