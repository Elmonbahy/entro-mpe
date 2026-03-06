@extends('layouts.main-layout')

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Ubah data barang stock" class="mb-3" withBackButton />
    <x-alert.session-alert />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form ubah data barang stock</p>
      </div>

      <div class="p-3">
        <form action="{{ route('fakturis.stock.update', ['id' => $barang_stock->id]) }}" method="post" autocomplete="off"
          onsubmit="return confirm('Simpan perubahan stock barang?')">
          @csrf
          @method('PATCH')

          <div class="mb-3 col-md">
            <x-form.label value="Brand" />
            <x-form.input name="brand" :readonly="true" :value="$barang_stock->barang->brand->nama" />
          </div>

          <div class="mb-3 col-md">
            <x-form.label value="Nama barang" />
            <x-form.input name="barang" :readonly="true" :value="$barang_stock->barang->nama" />
          </div>

          <div class="mb-3 col-md">
            <x-form.label value="Batch" />
            <x-form.input name="batch" :readonly="true" :value="$barang_stock->batch" />
          </div>

          <div class="mb-3 col-md">
            <x-form.label value="Tanggal Expired" />
            <x-form.input name="barang" :readonly="true" :value="\Carbon\Carbon::parse($barang_stock->tgl_expired)->format('d/m/Y')" />
          </div>

          <div class="mb-3 col-md">
            <x-form.label value="Stock saat ini" />
            <x-form.input name="jumlah_stock" type="number" :value="$barang_stock->jumlah_stock" />
          </div>

          <div class="alert alert-info small">
            INFO: Pastikan jumlah stock sudah sesuai
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
