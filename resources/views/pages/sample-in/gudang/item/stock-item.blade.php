@extends('layouts.main-layout')

@section('content')
  <div class="container px-4">
    <x-alert.session-alert />
    <x-page-header title="Barang sampel masuk" class="mb-3" />

    <div class="card mb-3">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold text-capitalize">Data sampel barang masuk</p>
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
            <td>Jumlah barang masuk</td>
            <td>
              @if ($sample_in_detail->jumlah_barang_dipesan > 0)
                {{ $sample_in_detail->jumlah_barang_dipesan }} {{ $sample_in_detail->sampleBarang->satuan }}
              @else
                -
              @endif
            </td>
          </tr>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold text-capitalize">Form ubah sampel barang masuk</p>
      </div>

      <div class="card-body">
        <form
          action="{{ route('gudang.sample-in.stock-item-update', ['id' => $sample_in_detail->samplein->id, 'sample_in_detail_id' => $sample_in_detail->id]) }}"
          method="post" autocomplete="off">

          @csrf
          @method('PATCH')

          <div class="mb-3">
            <x-form.label value="Batch" />
            <x-form.input name="batch" :value="$sample_in_detail->batch"
              placeholder="Isi dengan nomor batch. Gunakan (-) bila tidak memiliki batch" />
          </div>


          <div class="mb-3">
            <x-form.label value="Tanggal Expired" optional />
            <x-form.input name="tgl_expired" type="date" :value="$sample_in_detail->tgl_expired
                ? \Carbon\Carbon::parse($sample_in_detail->tgl_expired)->format('Y-m-d')
                : null" />
          </div>

          <div class="mb-3">
            <x-form.label value="Jumlah barang masuk" />
            <x-form.input name="jumlah_barang_masuk" type="number" :value="$sample_in_detail->jumlah_barang_masuk" />
          </div>

          <div class="alert alert-info small">
            INFO: Pastikan jumlah barang masuk sudah sesuai
          </div>

          <button type="submit" class="btn btn-primary">
            Simpan
          </button>
        </form>
      </div>
    </div>
  </div>
@endsection
