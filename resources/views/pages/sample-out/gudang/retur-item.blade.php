@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />

    <x-page-header title="Retur barang sampel keluar" class="mb-3" />


    <div class="card mb-3">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold text-capitalize">Data barang sampel keluar</p>
      </div>

      <div class="card-body p-2">
        <table class="table mb-0">
          <tr>
            <td width="250">Nomor Sampel</td>
            <td>
              @if ($sample_out_detail->sampleout->nomor_sample > 0)
                <strong>
                  {{ $sample_out_detail->sampleout->nomor_sample }}
                </strong>
              @else
                -
              @endif
            </td>
          </tr>
          <tr>
            <td>Brand</td>
            <td>
              {{ $sample_out_detail->sampleBarang->barang->brand->nama }}
            </td>
          </tr>
          <tr>
            <td>Nama barang</td>
            <td>
              {{ $sample_out_detail->sampleBarang->barang->nama }}
            </td>
          </tr>
          <tr>
            <td>Jumlah barang dipesan</td>
            <td>
              @if ($sample_out_detail->jumlah_barang_dipesan > 0)
                {{ $sample_out_detail->jumlah_barang_dipesan }} {{ $sample_out_detail->sampleBarang->satuan }}
              @else
                -
              @endif
            </td>
          </tr>
          <tr>
            <td>Jumlah barang keluar</td>
            <td>
              @if ($sample_out_detail->jumlah_barang_keluar > 0)
                {{ $sample_out_detail->jumlah_barang_keluar }} {{ $sample_out_detail->sampleBarang->satuan }}
              @else
                -
              @endif
            </td>
          </tr>
          <tr>
            <td>Batch</td>
            <td>
              {{ $sample_out_detail->batch ?: '-' }}
            </td>
          </tr>
          <tr>
            <td>Tanggal expired</td>
            <td>
              {{ $sample_out_detail->tgl_expired ? \Carbon\Carbon::parse($sample_out_detail->tgl_expired)->format('Y-m-d') : '-' }}
            </td>
          </tr>
          </tr>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold text-capitalize">Form retur barang sampel keluar</p>
      </div>

      <div class="card-body">
        <form
          action="{{ route('gudang.sample-out.retur-update', ['sample_out_detail_id' => $sample_out_detail->id, 'id' => $sample_out_detail->sampleout->id]) }}"
          method="post" autocomplete="off">
          @csrf
          @method('PATCH')

          <div class="row">
            <div class="col-md-3 mb-3">
              <x-form.label value="Jumlah barang retur" />
              <x-form.input name="jumlah_barang_retur" type="number" />
            </div>
            <div class="col-md-3 mb-3">
              <x-form.label value="Keterangan" />
              <x-form.input name="keterangan" />
            </div>

            <div class="col-md-3 mb-3" wire:ignore>
              <x-form.label value="Jenis Retur" />

              <div>
                <div class="form-check-inline">
                  <input class="form-check-input" type="radio" name="jenis_retur" id="jenis_retur1" checked
                    value="0">
                  <label class="form-check-label" for="jenis_retur1">
                    Tidak Diganti
                  </label>
                </div>
                <div class="form-check-inline">
                  <input class="form-check-input" type="radio" name="jenis_retur" id="jenis_retur2" value="1">
                  <label class="form-check-label" for="jenis_retur2">
                    Diganti
                  </label>
                </div>
              </div>
            </div>
          </div>

          <div class="text-warning-emphasis mb-3">
            Info:
            <p class="mb-0">
              - Riwayat retur dapat dilihat pada halaman retur barang keluar/keluar atau pada detail faktur.
            </p>
            <p class="mb-0">
              - <strong>Retur tidak diganti</strong> akan secara otomatis mengurangi jumlah barang keluar dan dipesan.
            </p>
            <p class="mb-0">
              - <strong>Retur diganti dengan batch/expired yang sama</strong>, akan secara otomatis mengurangi jumlah
              barang masuk dan dipesan.
            </p>
            <p class="mb-0">- <strong>Retur diganti dengan batch/expired yang berbeda</strong>, hubungi fakturis
              untuk menyesuaikan faktur penjualan.
            </p>
          </div>

          <button type="submit" class="btn btn-primary">
            Simpan
          </button>
        </form>
      </div>
    </div>
  </div>
@endsection
