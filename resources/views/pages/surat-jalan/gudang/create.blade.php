@extends('layouts.main-layout')

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Surat Jalan" class="mb-3" />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form Buat Surat Jalan</p>
      </div>

      <div class="p-3">
        <form action="{{ route('gudang.surat-jalan.store') }}" method="post" autocomplete="off">
          @csrf


          <div class="col-md mb-3">
            <x-form.label value="Pelanggan" />
            <x-form.select name="pelanggan" placeholder="Cari atau pilih pelanggan" :options="$pelanggans" :selected="old('pelanggan')"
              valueKey="id" labelKey="nama" />
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Kendaraan" />
              <x-form.select name="kendaraan" placeholder="Cari atau pilih kendaraan" :options="$kendaraans" :selected="old('kendaraan')"
                valueKey="id" labelKey="nama" />
            </div>
            <div class="col-md mb-3">
              <x-form.label value="Jumlah koli" />
              <x-form.input name="koli" type="text" placeholder="Input jumlah koli..." />
            </div>
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Tanggal" />
              <x-form.input name="tgl_surat_jalan" type="date" />
            </div>
            <div class="col-md mb-3">
              <x-form.label value="Penanggung jawab" />
              <x-form.select name="staf_logistik" placeholder="Cari atau pilih penanggung jawab" :options="$staf_logistiks"
                :selected="old('staf_logistik')" valueKey="id" labelKey="nama" />
            </div>
          </div>

          <div class="mb-3">
            <x-form.label value="Keterangan" optional />
            <x-form.input name="keterangan" placeholder="Input keterangan..." />
          </div>

          <button type="submit" class="btn btn-primary">
            Buat surat jalan
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
