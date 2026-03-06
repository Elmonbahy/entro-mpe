@extends('layouts.main-layout')

@section('title')
  Tambah Kendaraan
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Tambah data kendaraan" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form input data kendaraan</p>
      </div>

      <div class="p-3">
        <form action="{{ route('logistik.kendaraan.store') }}" method="post" autocomplete="off">
          @csrf

          <div class="row">
            <div class="mb-3 col">
              <x-form.label value="Nama kendaraan" />
              <x-form.input name="nama" placeholder="Input nama kendaraan..." />
            </div>
          </div>

          <div class="row">
            <div class="mb-3 col">
              <x-form.label value="Alamat" optional />
              <x-form.input name="alamat" placeholder="Input alamat..." />
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
