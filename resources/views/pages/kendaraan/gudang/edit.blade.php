@extends('layouts.main-layout')

@section('title')
  Ubah Kendaraan
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Tambah data kendaraan" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form ubah data kendaraan</p>
      </div>

      <div class="p-3">
        <form action="{{ route('gudang.kendaraan.update', ['kendaraan' => $kendaraan->id]) }}" method="post"
          autocomplete="off">
          @csrf
          @method('PATCH')

          <div class="row">
            <div class="mb-3 col">
              <x-form.label value="Nama kendaraan" />
              <x-form.input name="nama" placeholder="Input nama kendaraan..." :value="$kendaraan->nama" />
            </div>
          </div>

          <div class="row">
            <div class="mb-3 col">
              <x-form.label value="Alamat" optional />
              <x-form.input name="alamat" placeholder="Input alamat..." :value="$kendaraan->alamat" />
            </div>
          </div>


          <div class="row">
            <div class="mb-3 col">
              <x-form.label value="Contact person" optional />
              <x-form.input name="contact_person" placeholder="Input contact person..." :value="$kendaraan->contact_person" />
            </div>
            <div class="mb-3 col">
              <x-form.label value="Contact phone" optional />
              <x-form.input name="contact_phone" placeholder="Input contact phone kendaraan..." :value="$kendaraan->contact_phone" />
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
