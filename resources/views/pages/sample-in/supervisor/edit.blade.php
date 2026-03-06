@extends('layouts.main-layout')

@section('title')
  Ubah sampel masuk
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Ubah data sampel masuk" class="mb-3" withBackButton />
    <x-alert.session-alert />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form ubah sampel masuk</p>
      </div>

      <div class="p-3">
        <form action="{{ route('supervisor.sample-in.update', ['id' => $sample_in->id]) }}" method="post"
          autocomplete="off">
          @csrf
          @method('PATCH')

          <div class="mb-3 col-md">
            <x-form.label value="Supplier" />
            <x-form.select name="supplier" placeholder="Cari atau pilih supplier" :options="$suppliers" :selected="old('supplier') ?? $sample_in->supplier_id"
              valueKey="id" labelKey="nama" />
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Tanggal" />
              <x-form.input name="tanggal" type="date" :value="optional(Carbon\Carbon::parse($sample_in->tanggal))->format('Y-m-d')" />
            </div>
          </div>

          <div class="row">
            <div class="col-md mb-3">
              <x-form.label value="Nomor sampel" />
              <x-form.input name="nomor_sample" placeholder="Input nomor sampel..." :value="$sample_in->nomor_sample" />
            </div>
          </div>

          <div class="mb-3">
            <x-form.label value="Keterangan" optional />
            <x-form.input name="keterangan" placeholder="Input keterangan..." :value="$sample_in->keterangan" />
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
