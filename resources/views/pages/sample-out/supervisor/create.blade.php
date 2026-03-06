@extends('layouts.main-layout')

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Tambah data sampel keluar" class="mb-3" withBackButton />
    <x-alert.session-alert />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form buat saampel keluar</p>
      </div>

      <div class="p-3">
        <form action="{{ route('supervisor.sample-out.store') }}" method="post" autocomplete="off">
          @csrf

          <div class="mb-3 col-md">
            <x-form.label value="Pelanggan" />
            <x-form.select name="pelanggan" placeholder="Cari atau pilih pelanggan" :options="$pelanggans" :selected="old('pelanggan')"
              valueKey="id" labelKey="nama" />
          </div>

          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Salesman" />
              <x-form.select name="salesman" placeholder="Cari atau pilih salesman" :options="$salesmans" :selected="old('salesman')"
                valueKey="id" labelKey="nama" />
            </div>
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Tanggal" />
              <x-form.input name="tanggal" type="date" />
            </div>
          </div>

          <div class="mb-3">
            <x-form.label value="Keterangan" optional />
            <x-form.input name="keterangan" placeholder="Input keterangan..." />
          </div>

          <div class="alert alert-info small">
            INFO: Setelah membuat sampel keluar, anda akan diarahkan untuk menambah barang pada sampel yang baru saja
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
