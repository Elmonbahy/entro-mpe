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
      <form action="{{ route('keuangan.pelanggan.update', ['pelanggan' => $pelanggan->id]) }}" method="post"
        autocomplete="off">
        @csrf
        @method('PATCH')

        <div class="mb-3">
          <p>Kode Pelanggan: <strong>{{ $pelanggan->kode }}</strong></p>
        </div>

        <div class="mb-3">
          <x-form.label value="Nama pelanggan" />
          <x-form.input name="nama" :value="$pelanggan->nama" readonly />
        </div>

        <div class="row">
          <div class="mb-3 col">
            <x-form.label value="Kota/Kabupaten pelanggan" />
            <x-form.input name="kota" :value="$pelanggan->kota" readonly />
          </div>
          <div class="mb-3 col">
            <x-form.label value="Alamat pelanggan" />
            <x-form.input name="alamat" :value="$pelanggan->alamat" readonly />
          </div>
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
            <x-form.label value="Area" />
            <x-form.select name="area" placeholder="Cari atau pilih area" :options="$areas" :selected="old('area') ?? $pelanggan->area" />
          </div>
          <div class="mb-3 col-md">
            <x-form.label value="Limit hutang (hari)" />
            <x-form.input name="limit_hari" type="number" placeholder="Input limit hutang..." :value="$pelanggan->limit_hari" />
          </div>
        </div>

        <div class="mb-3 col">
          <x-form.label value="Plafon hutang" />
          <x-form.input name="plafon_hutang" type="number" placeholder="Input plfaon hutang..." :value="$pelanggan->plafon_hutang"
            wire:model.live.debounce.300ms="plafon_hutang" />
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