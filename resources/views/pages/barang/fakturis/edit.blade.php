@extends('layouts.main-layout')

@section('title')
  Ubah barang
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Ubah data barang" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form ubah data barang</p>
      </div>

      <div class="p-3">
        <form action="{{ route('fakturis.barang.update', ['barang' => $barang->id]) }}" method="post" autocomplete="off">
          @csrf
          @method('PATCH')
          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Brand" />
              <x-form.select name="brand" placeholder="Cari atau pilih brand" :options="$brands" :selected="old('brand') ?? $barang->brand_id"
                valueKey="id" labelKey="nama" />
            </div>
            <div class="mb-3 col-md">
              <x-form.label value="kode barang" optional />
              <x-form.input name="kode" placeholder="Input kode barang..." :value="$barang->kode" />
            </div>
          </div>

          <div class="mb-3">
            <x-form.label value="Nama barang" />
            <x-form.input name="nama" placeholder="Input nama barang..." :value="$barang->nama" />
          </div>

          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Satuan" />
              <x-form.select name="satuan" placeholder="Cari atau pilih satuan" :options="$satuans" :selected="old('satuan') ?? $barang->satuan" />
            </div>

            <div class="mb-3 col-md">
              <x-form.label value="NIE" optional />
              <x-form.input name="nie" placeholder="Input NIE..." :value="$barang->nie" />
            </div>
          </div>

          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Supplier" optional />
              <x-form.select name="supplier" placeholder="Cari atau pilih supplier" :options="$suppliers" :selected="old('supplier') ?? $barang->supplier_id"
                valueKey="id" labelKey="nama" />
            </div>

            <div class="mb-3 col-md">
              <x-form.label value="Group" optional />
              <x-form.select name="group" placeholder="Cari atau pilih group" :options="$groups" :selected="old('group') ?? $barang->group_id"
                valueKey="id" labelKey="nama" />
            </div>
          </div>

          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Harga jual pemerintah" />
              <x-form.input name="harga_jual_pemerintah" id="harga_jual_pemerintah"
                placeholder="Input harga jual pemerintah..." :value="number_format($barang->harga_jual_pemerintah, 2, ',', '.')" oninput="formatRupiah(this)" />
            </div>

            <div class="mb-3 col-md">
              <x-form.label value="Harga jual swasta" />
              <x-form.input name="harga_jual_swasta" id="harga_jual_swasta" placeholder="Input harga jual swasta..."
                :value="number_format($barang->harga_jual_swasta, 2, ',', '.')" oninput="formatRupiah(this)" />
            </div>

            <div class="mb-3 col-md">
              <x-form.label value="Harga beli" />
              <x-form.input name="harga_beli" id="harga_beli" placeholder="Input harga beli..." :value="number_format($barang->harga_beli, 2, ',', '.')"
                oninput="formatRupiah(this)" />
            </div>

          </div>

          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Kegunaan" optional />
              <textarea name="kegunaan" class="form-control" placeholder="Input kegunaan...">{{ old('kegunaan', $barang->kegunaan) }}</textarea>
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
