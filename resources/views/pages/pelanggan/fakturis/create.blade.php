@extends('layouts.main-layout')

@section('title')
  Tambah pelanggan
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Tambah data pelanggan" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form input data pelanggan</p>
      </div>

      <div class="p-3">
        <form action="{{ route('fakturis.pelanggan.store') }}" method="post" autocomplete="off">
          @csrf
          <div class="mb-3">
            <x-form.label value="Nama pelanggan" />
            <x-form.input name="nama" placeholder="Input nama pelanggan..." />
          </div>

          <div class="row">
            <div class="mb-3 col">
              <x-form.label value="Kota/Kabupaten pelanggan" />
              <x-form.select name="kota" placeholder="Cari atau pilih Kota/Kabupaten" :options="$kotas"
                :selected="old('kota')" />
            </div>
            <div class="mb-3 col">
              <x-form.label value="Alamat pelanggan" />
              <x-form.input name="alamat" placeholder="Input alamat pelanggan..." />
            </div>
          </div>

          <div class="mb-3">
            <x-form.label value="NPWP" />
            <x-form.input name="npwp" placeholder="Input npwp..." />
          </div>

          <div class="row">
            <div class="mb-3 col">
              <x-form.label value="Contact person" />
              <x-form.input name="contact_person" placeholder="Input contact person..." />
            </div>
            <div class="mb-3 col">
              <x-form.label value="Contact phone" />
              <x-form.input name="contact_phone" placeholder="Input contact phone supplier..." />
            </div>
          </div>

          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Tipe pelanggan" />
              <x-form.select name="tipe" placeholder="Cari atau pilih tipe pelangan" :options="$tipePelanggans"
                :selected="old('tipe')" />
            </div>

            <div class="mb-3 col-md">
              <x-form.label value="Tipe harga" />
              @php
                $tipeHargaOptions = ['SWASTA', 'PEMERINTAH'];
              @endphp
              <x-form.select name="tipe_harga" placeholder="Cari atau pilih tipe harga" :options="$tipeHargaOptions"
                :selected="old('tipe_harga')" />
            </div>

            <div class="mb-3 col-md">
              <x-form.label value="Rayon" />
              <x-form.select name="rayon" placeholder="Cari atau pilih rayon" :options="$rayons" :selected="old('rayon')" />
            </div>

            <div class="mb-3 col-md">
              <x-form.label value="Area" />
              <x-form.select name="area" placeholder="Cari atau pilih area" :options="$areas" :selected="old('area')" />
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
