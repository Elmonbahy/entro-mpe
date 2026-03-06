@extends('layouts.main-layout')

@section('title')
  Ubah barang
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Ubah data barang sampel" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form ubah data barang sample</p>
      </div>

      <div class="p-3">
        <form action="{{ route('fakturis.sample-barang.update', ['sample_barang' => $sample_barang->id]) }}" method="post"
          autocomplete="off">
          @csrf
          @method('PATCH')
          <div class="mb-3">
            <x-form.label value="Nama barang" />
            <x-form.input placeholder="Nama barang..." :value="$sample_barang->barang->nama" readonly />
            <input type="hidden" name="barang_id" value="{{ $sample_barang->barang_id }}">
          </div>


          <div class="row">
            <div class="mb-3 col-md">
              <x-form.label value="Satuan" />
              <x-form.select name="satuan" placeholder="Cari atau pilih satuan" :options="$satuans" :selected="$sample_barang->satuan" />
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
    })
  </script>
@endpush
