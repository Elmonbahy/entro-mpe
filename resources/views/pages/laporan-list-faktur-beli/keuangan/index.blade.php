@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Laporan List Faktur Beli" class="mb-3">
    </x-page-header>

    <div class="card mb-3">
      <div class="p-3 card-header d-flex align-items-center">
        <p class="mb-0 fw-semibold me-2">Form Laporan List Faktur Beli</p>
      </div>

      <div class="p-3">
        <form method="GET" action="{{ route('keuangan.laporan-list-faktur-beli.index') }}" autocomplete="off">
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
              <x-form.label value="Supplier" optional />
              <x-form.select name="supplier_id" placeholder="Cari atau pilih supplier" :options="$suppliers" valueKey="id"
                labelKey="nama" :selected="$supplier_id" />
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
          <p class="mb-0 fw-semibold">Tabel Laporan List Faktur Beli</p>
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
          <p class="mb-0 fw-semibold">Tabel Laporan Beli</p>
          <a href="{{ route('keuangan.laporan-list-faktur-beli.excel') }}?tgl_awal={{ request('tgl_awal') }}&tgl_akhir={{ request('tgl_akhir') }}&supplier_id={{ request('supplier_id') }}&status_bayar={{ request('status_bayar') }}"
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
                  <th>Supplier</th>
                  <th>Nomor Faktur</th>
                  <th>Tanggal Faktur</th>
                  <th>Tanggal Terima</th>
                  <th>Jatuh Tempo</th>
                  <th>Status Bayar</th>
                  <th>Tipe Bayar</th>
                  <th>Tanggal Bayar</th>
                  <th>Metode Bayar</th>
                  <th>Terbayar</th>
                  <th>Total Tagihan</th>
                  <th>Sisa Tagihan</th>
                  <th>Total Faktur</th>
                  <th>Total DPP</th>
                  <th>Total PPN</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($data as $supplier)
                  @php
                    $rowspan = count($supplier['fakturs']);
                  @endphp

                  @foreach ($supplier['fakturs'] as $index => $faktur)
                    <tr>
                      @if ($index === 0)
                        <td rowspan="{{ $rowspan }}" class="text-start">{{ $supplier['supplier_nama'] }}</td>
                      @endif
                      <td>{{ $faktur['nomor_faktur'] }}</td>
                      <td>{{ $faktur['tgl_faktur'] }}</td>
                      <td>{{ $faktur['tgl_terima_faktur'] }}</td>
                      <td>{{ $faktur['jatuh_tempo'] }}</td>
                      <td>{{ $faktur['status_bayar_label'] }}</td>
                      <td>{{ $faktur['tipe_bayar'] }}</td>
                      <td>{{ $faktur['tgl_bayar'] }}</td>
                      <td>{{ $faktur['metode_bayar'] }}</td>
                      <td class="text-end">{{ $faktur['terbayar'] }}</td>
                      <td class="text-end">{{ number_format($faktur['total_tagihan'], 0, ',', '.') }}</td>
                      <td class="text-end">{{ number_format($faktur['sisa_tagihan'], 0, ',', '.') }}</td>
                      <td class="text-end">{{ number_format($faktur['total_faktur'], 0, ',', '.') }}</td>
                      <td class="text-end">{{ number_format($faktur['total_dpp'], 0, ',', '.') }}</td>
                      <td class="text-end">{{ number_format($faktur['total_ppn'], 0, ',', '.') }}</td>
                    </tr>
                  @endforeach
                  <tr class="fw-bold">
                    <td class="text-end" colspan="10">
                      <strong>Total</strong>
                    </td>
                    <td class="text-end">{{ number_format($supplier['total_tagihan_supplier'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($supplier['total_sisa_tagihan_supplier'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($supplier['total_faktur_supplier'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($supplier['total_dpp_supplier'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($supplier['total_ppn_supplier'], 0, ',', '.') }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr class="fw-bold text-end">
                  <td colspan="10">
                    <strong>Grand Total</strong>
                  </td>
                  <td>{{ number_format(collect($data)->sum('total_tagihan_supplier'), 0, ',', '.') }}</td>
                  <td>{{ number_format(collect($data)->sum('total_sisa_tagihan_supplier'), 0, ',', '.') }}</td>
                  <td>{{ number_format(collect($data)->sum('total_faktur_supplier'), 0, ',', '.') }}</td>
                  <td>{{ number_format(collect($data)->sum('total_dpp_supplier'), 0, ',', '.') }}</td>
                  <td>{{ number_format(collect($data)->sum('total_ppn_supplier'), 0, ',', '.') }}</td>
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
      new TomSelect('#supplier_id');
    });
  </script>
@endpush
