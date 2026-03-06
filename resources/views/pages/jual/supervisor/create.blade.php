@extends('layouts.main-layout')

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Tambah data" class="mb-3" withBackButton />
    <x-alert.session-alert />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form Buat Faktur Jual</p>
      </div>

      <div class="p-3">
        <form action="{{ route('supervisor.jual.store') }}" method="post" autocomplete="off">
          @csrf

          <div class="mb-3 col-md">
            <x-form.label value="Pelanggan" />
            <x-form.select name="pelanggan" placeholder="Cari atau pilih pelanggan" :options="$pelanggans" :selected="old('pelanggan')"
              valueKey="id" labelKey="nama" />
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Tipe penjualan" />
              <x-form.select name="tipe_penjualan" placeholder="Cari atau pilih tipe penjualan" :options="$tipe_penjualans"
                :selected="old('tipe_penjualan')" valueKey="id" labelKey="nama" />
            </div>

            <div class="mb-3 col-md">
              <x-form.label value="Salesman" />
              <x-form.select name="salesman" placeholder="Cari atau pilih salesman" :options="$salesmans" :selected="old('salesman')"
                valueKey="id" labelKey="nama" />
            </div>
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Tanggal faktur" />
              <x-form.input name="tgl_faktur" type="date" />
            </div>

            <div class="col-md mb-3">
              <x-form.label value="Nomor pemesanan" optional />
              <x-form.input name="nomor_pemesanan" placeholder="Input nomor pemesanan..." />
            </div>
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Kredit (hari)" optional />
              <x-form.input name="kredit" type="number" placeholder="Input kredit..." value="30" />
            </div>

            <div class="col-md mb-3">
              <x-form.label value="Ongkir" optional />
              <x-form.input name="ongkir" type="number" placeholder="Input ongkir..." value="0" />
            </div>

            <div class="col-md mb-3">
              <x-form.label value="Diskon faktur" optional />
              <x-form.input name="diskon_faktur" type="text" value="0" placeholder="Input diskon ..."
                oninput="this.value = this.value.replace(',', '.')" />
            </div>
          </div>

          <div class="mb-3">
            <x-form.radio-group name="ppn" label="PPN" :options="[
                '0' => 'Tanpa PPN',
                '11' => '11%',
                '12' => '12%',
            ]" :value="old('ppn', 11)" />
          </div>

          <div class="mb-3">
            <x-form.label value="Keterangan" optional />
            <x-form.input name="keterangan_faktur" placeholder="Input keterangan..." />
          </div>

          <div class="alert alert-info small">
            INFO: Setelah membuat faktur jual, anda akan diarahkan untuk menambah barang pada faktur yang baru saja
            dibuat.
          </div>

          <button type="submit" class="btn btn-primary">
            Tambah barang
          </button>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const selectElements = document.querySelectorAll('.form-select');
      selectElements.forEach(element => {
        new TomSelect(`#${element.id}`);
      });
    });
  </script>
@endpush
