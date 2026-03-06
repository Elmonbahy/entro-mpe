@extends('layouts.main-layout')

@section('title')
  Tambah sampel
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Tambah data sampel masuk" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form buat sampel masuk</p>
      </div>

      <div class="p-3">
        <form action="{{ route('supervisor.sample-in.store') }}" method="post" autocomplete="off">
          @csrf

          <div class="mb-3 col-md">
            <x-form.label value="Supplier" />
            <x-form.select name="supplier" placeholder="Cari atau pilih supplier" :options="$suppliers" :selected="old('supplier')"
              valueKey="id" labelKey="nama" />
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Tanggal" />
              <x-form.input name="tanggal" type="date" />
            </div>
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Nomor sampel" />
              <x-form.input name="nomor_sample" placeholder="Input nomor sampel..." />
              <small class="text-muted">
                Boleh dikosongkan — sistem akan membuat nomor otomatis berdasarkan tanggal dan ID.
              </small>
            </div>
          </div>

          <div class="mb-3">
            <x-form.label value="Keterangan" optional />
            <x-form.input name="keterangan" placeholder="Input keterangan..." />
          </div>
          <div class="alert alert-info small">
            INFO: Setelah membuat sampel masuk, anda akan diarahkan untuk menambah barang pada sampel yang baru saja
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
