@extends('layouts.main-layout')

@section('content')
  <div class="container px-4">
    <x-alert.session-alert />
    <x-page-header title="Barang masuk" class="mb-3" />

    <div class="card mb-3">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold text-capitalize">Data barang masuk</p>
      </div>

      <div class="card-body p-2">
        <table class="table mb-0">
          <tr>
            <td width="250">Nomor pemesanan</td>
            <td>
              <strong>
                {{ $beli_detail->beli->nomor_pemesanan }}
              </strong>
            </td>
          </tr>
          <tr>
            <td>Nomor Faktur</td>
            <td>
              @if ($beli_detail->beli->nomor_faktur > 0)
                <strong>
                  {{ $beli_detail->beli->nomor_faktur }}
                </strong>
              @else
                -
              @endif
            </td>
          </tr>
          <tr>
            <td>Brand</td>
            <td>
              {{ $beli_detail->barang->brand->nama }}
            </td>
          </tr>
          <tr>
            <td>Nama barang</td>
            <td>
              {{ $beli_detail->barang->nama }}
            </td>
          </tr>
          <tr>
            <td>Jumlah barang masuk</td>
            <td>
              @if ($beli_detail->jumlah_barang_dipesan > 0)
                {{ $beli_detail->jumlah_barang_dipesan }} {{ $beli_detail->barang->satuan }}
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
        <p class="mb-0 fw-semibold text-capitalize">Form ubah barang masuk</p>
      </div>

      <div class="card-body">
        <form
          action="{{ route('gudang.beli.stock-item-update', ['id' => $beli_detail->beli->id, 'beli_detail_id' => $beli_detail->id]) }}"
          method="post" autocomplete="off">

          @csrf
          @method('PATCH')

          <div class="mb-3">
            <x-form.label value="Batch" />
            <x-form.input name="batch" :value="$beli_detail->batch"
              placeholder="Isi dengan nomor batch. Gunakan (-) bila tidak memiliki batch" />
          </div>


          <div class="mb-3">
            <x-form.label value="Tanggal Expired" optional />
            <x-form.input name="tgl_expired" type="date" :value="$beli_detail->tgl_expired
                ? \Carbon\Carbon::parse($beli_detail->tgl_expired)->format('Y-m-d')
                : null" />
          </div>

          <div class="mb-3">
            <x-form.label value="Jumlah barang masuk" />
            <x-form.input name="jumlah_barang_masuk" type="number" :value="$beli_detail->jumlah_barang_masuk" />
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
