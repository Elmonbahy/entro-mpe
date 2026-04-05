@extends('layouts.main-layout')

@section('title')
  Ubah Supplier
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Tambah data supplier" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form ubah data supplier</p>
      </div>

      <div class="p-3">
        <form action="{{ route('superadmin.supplier.update', ['supplier' => $supplier->id]) }}" method="post"
          autocomplete="off">
          @csrf
          @method('PATCH')

          <div class="mb-3">
            <p>Kode Supplier: <strong>{{ $supplier->kode }}</strong></p>
          </div>

          <div class="row">
            <div class="mb-3 col">
              <x-form.label value="Nama supplier" />
              <x-form.input name="nama" placeholder="Input nama supplier..." :value="$supplier->nama" />
            </div>

            <div class="mb-3 col">
              <x-form.label value="NPWP" optional />
              <x-form.input name="npwp" placeholder="Input npwp..." :value="$supplier->npwp" />
            </div>
          </div>

          <div class="row">
            <div class="mb-3 col">
              <x-form.label value="Kota supplier" optional />
              <x-form.input name="kota" placeholder="Input alamat supplier..." :value="$supplier->kota" />
            </div>
            <div class="mb-3 col">
              <x-form.label value="Alamat supplier" optional />
              <x-form.input name="alamat" placeholder="Input alamat supplier..." :value="$supplier->alamat" />
            </div>
          </div>


          <div class="row">
            <div class="mb-3 col">
              <x-form.label value="Contact person" optional />
              <x-form.input name="contact_person" placeholder="Input contact person..." :value="$supplier->contact_person" />
            </div>
            <div class="mb-3 col">
              <x-form.label value="Contact phone" optional />
              <x-form.input name="contact_phone" placeholder="Input contact phone supplier..." :value="$supplier->contact_phone" />
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
