@extends('layouts.main-layout')

@section('title')
  Tambah Supplier
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Tambah data supplier" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form input data supplier</p>
      </div>

      <div class="p-3">
        <form action="{{ route('superadmin.supplier.store') }}" method="post" autocomplete="off">
          @csrf

          <div class="row">
            <div class="mb-3 col">
              <x-form.label value="Nama supplier" />
              <x-form.input name="nama" placeholder="Input nama supplier..." />
            </div>
            <div class="mb-3 col">
              <x-form.label value="NPWP" optional />
              <x-form.input name="npwp" placeholder="Input npwp..." />
            </div>
          </div>

          <div class="row">
            <div class="mb-3 col">
              <x-form.label value="Kota supplier" optional />
              <x-form.input name="kota" placeholder="Input alamat supplier..." />
            </div>
            <div class="mb-3 col">
              <x-form.label value="Alamat supplier" optional />
              <x-form.input name="alamat" placeholder="Input alamat supplier..." />
            </div>
          </div>


          <div class="row">
            <div class="mb-3 col">
              <x-form.label value="Contact person" optional />
              <x-form.input name="contact_person" placeholder="Input contact person..." />
            </div>
            <div class="mb-3 col">
              <x-form.label value="Contact phone" optional />
              <x-form.input name="contact_phone" placeholder="Input contact phone supplier..." />
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
