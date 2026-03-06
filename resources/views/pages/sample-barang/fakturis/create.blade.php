@extends('layouts.main-layout')

@section('title')
  Tambah barang
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Tambah data barang sampel" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form input data barang sampel</p>
      </div>

      <div class="p-3">
        <form action="{{ route('fakturis.sample-barang.store') }}" method="post" autocomplete="off">
          @csrf
          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Barang" />
              <x-form.select name="barang_id" placeholder="Cari atau pilih barang" :options="$barangs" :selected="old('barang_id')"
                valueKey="id" labelKey="nama" />
            </div>
            <div class="mb-3 col-md">
              <x-form.label value="Satuan" />
              <x-form.select name="satuan" placeholder="Cari atau pilih satuan" :options="$satuans" :selected="old('satuan')" />
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
