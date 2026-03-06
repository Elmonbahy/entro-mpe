@extends('layouts.main-layout')

@section('title')
  Tambah pembelian
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Tambah data pembelian" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form buat faktur pembelian</p>
      </div>

      <div class="p-3">
        <form action="{{ route('fakturis.beli.store') }}" method="post" autocomplete="off">
          @csrf
          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Supplier" />
              <x-form.select name="supplier" placeholder="Cari atau pilih supplier" :options="$suppliers" :selected="old('supplier')"
                valueKey="id" labelKey="nama" />
            </div>
            <div class="col-md mb-3">
              <x-form.label value="Nomor faktur" />
              <x-form.input name="nomor_faktur" placeholder="Input nomor faktur..." />
            </div>
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Tanggal faktur" />
              <x-form.input name="tgl_faktur" type="date" />
            </div>

            <div class="col-md mb-3">
              <x-form.label value="Tanggal terima faktur" />
              <x-form.input name="tgl_terima_faktur" type="date" />

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
              <x-form.label value="Materai" optional />
              <x-form.input name="materai" type="number" placeholder="Input materai..." value="0" />
            </div>

            <div class="col-md mb-3">
              <x-form.label value="Diskon faktur" optional />
              <x-form.input name="diskon_faktur" type="text" value="{{ old('diskon_faktur', '0') }}"
                placeholder="Input diskon ..." oninput="this.value = this.value.replace(',', '.')" />
            </div>

            <div class="col-md mb-3">
              <x-form.label value="Biaya Lainya" optional />
              <x-form.input name="biaya_lainnya" type="number" placeholder="Input biaya lainya..." value="0" />
            </div>
          </div>

          <div class="mb-3">
            <x-form.radio-group name="ppn" label="PPN" :options="[
                '0' => 'Tanpa PPN',
                '11' => '11%',
                '12' => '12%',
            ]" :value="old('ppn') ?? '11'" />
          </div>

          <div class="mb-3">
            <x-form.label value="Keterangan" optional />
            <x-form.input name="keterangan_faktur" placeholder="Input keterangan..." />
          </div>
          <div class="alert alert-info small">
            INFO: Setelah membuat faktur beli, anda akan diarahkan untuk menambah barang pada faktur yang baru saja
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
