@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />

    <x-page-header title="Retur Barang sampel masuk" class="mb-3" />

    <div class="card mb-3">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold text-capitalize">Data barang sampel masuk</p>
      </div>

      <div class="card-body p-2">
        <table class="table mb-0">
          <tr>
            <td width="250">Nomor Sampel</td>
            <td>
              @if ($sample_in_detail->samplein->nomor_sample > 0)
                <strong>
                  {{ $sample_in_detail->samplein->nomor_sample }}
                </strong>
              @else
                -
              @endif
            </td>
          </tr>
          <tr>
            <td>Brand</td>
            <td>
              {{ $sample_in_detail->sampleBarang->barang->brand->nama }}
            </td>
          </tr>
          <tr>
            <td>Nama barang</td>
            <td>
              {{ $sample_in_detail->sampleBarang->barang->nama }}
            </td>
          </tr>
          <tr>
            <td>Jumlah barang dipesan</td>
            <td>
              @if ($sample_in_detail->jumlah_barang_dipesan > 0)
                {{ $sample_in_detail->jumlah_barang_dipesan }} {{ $sample_in_detail->sampleBarang->satuan }}
              @else
                -
              @endif
            </td>
          </tr>
          <tr>
            <td>Jumlah barang masuk</td>
            <td>
              @if ($sample_in_detail->jumlah_barang_masuk > 0)
                {{ $sample_in_detail->jumlah_barang_masuk }} {{ $sample_in_detail->sampleBarang->satuan }}
              @else
                -
              @endif
            </td>
          </tr>
          <tr>
            <td>Batch</td>
            <td>
              {{ $sample_in_detail->batch ?: '-' }}
            </td>
          </tr>
          <tr>
            <td>Tanggal expired</td>
            <td>
              {{ $sample_in_detail->tgl_expired ? \Carbon\Carbon::parse($sample_in_detail->tgl_expired)->format('d/m/Y') : '-' }}
            </td>
          </tr>
          </tr>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold text-capitalize">Form retur barang sampel masuk</p>
      </div>

      <div class="card-body">
        <form
          action="{{ route('gudang.sample-in.retur-update', ['sample_in_detail_id' => $sample_in_detail->id, 'id' => $sample_in_detail->samplein->id]) }}"
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
              - Riwayat retur dapat dilihat pada halaman retur barang masuk/keluar atau pada detail faktur.
            </p>
            <p class="mb-0">
              - <strong>Retur tidak diganti</strong> akan secara otomatis mengurangi jumlah barang masuk dan dipesan.
            </p>
            <p class="mb-0">
              - <strong>Retur diganti dengan batch/expired yang sama</strong>, akan secara otomatis mengurangi jumlah
              barang masuk dan dipesan.
            </p>
            <p class="mb-0">- <strong>Retur diganti dengan batch/expired yang berbeda</strong>, hubungi fakturis
              untuk menyesuaikan faktur pembelian.
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
