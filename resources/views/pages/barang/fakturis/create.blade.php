@extends('layouts.main-layout')

@section('title')
  Tambah barang
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Tambah data barang" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form input data barang</p>
      </div>

      <div class="p-3">
        <form action="{{ route('fakturis.barang.store') }}" method="post" autocomplete="off">
          @csrf
          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Brand" />
              <x-form.select name="brand" placeholder="Cari atau pilih brand" :options="$brands" :selected="old('brand')"
                valueKey="id" labelKey="nama" />
            </div>
            <div class="mb-3 col-md">
              <x-form.label value="Kode barang" />
              <x-form.input name="kode" placeholder="Input kode barang..." />
            </div>
          </div>

          <div class="mb-3">
            <x-form.label value="Nama barang" />
            <x-form.input name="nama" placeholder="Input nama barang..." />
          </div>

          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Satuan" />
              <x-form.select name="satuan" placeholder="Cari atau pilih satuan" :options="$satuans" :selected="old('satuan')" />
            </div>
            <div class="mb-3 col-md">
              <x-form.label value="NIE" optional />
              <x-form.input name="nie" placeholder="Input NIE..." />
            </div>
          </div>

          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Supplier" optional />
              <x-form.select name="supplier" placeholder="Cari atau pilih supplier" :options="$suppliers" :selected="old('supplier')"
                valueKey="id" labelKey="nama" />
            </div>

            <div class="mb-3 col-md">
              <x-form.label value="Group" optional />
              <x-form.select name="group" placeholder="Cari atau pilih group" :options="$groups" :selected="old('group')"
                valueKey="id" labelKey="nama" />
            </div>
          </div>

          <button type="submit" class="btn btn-primary">
            Simpan
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

    function formatRupiah(input) {
      let value = input.value.replace(/[^0-9,]/g, '');

      let parts = value.split(',');
      let integerPart = parts[0].replace(/\./g, '');
      let decimalPart = parts.length > 1 ? ',' + parts[1] : '';

      integerPart = new Intl.NumberFormat('id-ID').format(integerPart);

      input.value = integerPart + decimalPart;
    }
  </script>
@endpush
