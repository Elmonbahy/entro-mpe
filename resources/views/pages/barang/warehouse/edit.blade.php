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
        <form action="{{ route('warehouse.barang.update', ['barang' => $barang->id]) }}" method="post" autocomplete="off">
          @csrf
          @method('PATCH')
          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Brand" />
              <x-form.input name="brand" :value="$barang->brand->nama" readonly />
            </div>
            <div class="mb-3 col-md">
              <x-form.label value="kode barang" />
              <x-form.input name="kode" placeholder="Input kode barang..." :value="$barang->kode" />
            </div>
          </div>

          <div class="mb-3">
            <x-form.label value="Nama barang" />
            <x-form.input name="nama" :value="$barang->nama" readonly />
          </div>

          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Satuan" />
              <x-form.input name="satuan" :value="$barang->satuan" readonly />
            </div>

            <div class="mb-3 col-md">
              <x-form.label value="NIE" />
              <x-form.input name="nie" placeholder="Input NIE..." :value="$barang->nie" />
            </div>
          </div>

          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Supplier" />
              <x-form.select name="supplier" placeholder="Cari atau pilih supplier" :options="$suppliers" :selected="old('supplier') ?? $barang->supplier_id"
                valueKey="id" labelKey="nama" />
            </div>

            <div class="mb-3 col-md">
              <x-form.label value="Group" />
              <x-form.select name="group" placeholder="Cari atau pilih group" :options="$groups" :selected="old('group') ?? $barang->group_id"
                valueKey="id" labelKey="nama" />
            </div>
          </div>

          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Kegunaan" />
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
  </script>
@endpush
