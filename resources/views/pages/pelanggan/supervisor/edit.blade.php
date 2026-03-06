@extends('layouts.main-layout')

@section('title')
  Ubah pelanggan
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Ubah data pelanggan" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form ubah data pelanggan</p>
      </div>

      <div class="p-3">
        <form action="{{ route('supervisor.pelanggan.update', ['pelanggan' => $pelanggan->id]) }}" method="post"
          autocomplete="off">
          @csrf
          @method('PATCH')

          <div class="mb-3">
            <p>Kode Pelanggan: <strong>{{ $pelanggan->kode }}</strong></p>
          </div>

          <div class="mb-3">
            <x-form.label value="Nama pelanggan" />
            <x-form.input name="nama" placeholder="Input nama pelanggan..." :value="$pelanggan->nama" />
          </div>

          <div class="row">
            <div class="mb-3 col">
              <x-form.label value="Kota/Kabupaten pelanggan" />
              <x-form.select name="kota" placeholder="Cari atau pilih kota/Kabupaten" :options="$kotas"
                :selected="old('kota') ?? $pelanggan->kota" />
            </div>
            <div class="mb-3 col">
              <x-form.label value="Alamat pelanggan" />
              <x-form.input name="alamat" placeholder="Input alamat pelanggan..." :value="$pelanggan->alamat" />
            </div>
          </div>

          <div class="mb-3">
            <x-form.label value="NPWP" />
            <x-form.input name="npwp" placeholder="Input npwp..." :value="$pelanggan->npwp" />
          </div>

          <div class="row">
            <div class="mb-3 col">
              <x-form.label value="Contact person" />
              <x-form.input name="contact_person" placeholder="Input contact person..." :value="$pelanggan->contact_person" />
            </div>
            <div class="mb-3 col">
              <x-form.label value="Contact phone" />
              <x-form.input name="contact_phone" placeholder="Input contact phone supplier..." :value="$pelanggan->contact_phone" />
            </div>
          </div>

          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Tipe pelanggan" />
              <x-form.select name="tipe" placeholder="Cari atau pilih tipe" :options="$tipePelanggans" :selected="old('tipe') ?? $pelanggan->tipe" />
            </div>

            <div class="mb-3 col-md">
              <x-form.label value="Tipe harga" />
              @php
                $tipeHargaOptions = ['SWASTA', 'PEMERINTAH'];
              @endphp
              <x-form.select name="tipe_harga" placeholder="Cari atau pilih tipe harga" :options="$tipeHargaOptions"
                :selected="old('tipe_harga', $pelanggan->tipe_harga)" />
            </div>

            <div class="mb-3 col-md">
              <x-form.label value="Rayon" />
              <x-form.select name="rayon" placeholder="Cari atau pilih rayon" :options="$rayons" :selected="old('rayon') ?? $pelanggan->rayon" />
            </div>

            <div class="mb-3 col-md">
              <x-form.label value="Area" />
              <x-form.select name="area" placeholder="Cari atau pilih area" :options="$areas" :selected="old('area') ?? $pelanggan->area" />
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
