@extends('layouts.main-layout')

@section('title')
  Ubah Brand
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Tambah data brand" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form ubah data brand</p>
      </div>

      <div class="p-3">
        <form action="{{ route('supervisor.brand.update', ['brand' => $brand->id]) }}" method="post" autocomplete="off">
          @csrf
          @method('PATCH')
          <div class="mb-3">
            <x-form.label value="Nama brand" />
            <x-form.input name="nama" placeholder="Input nama brand..." :value="$brand->nama" />
          </div>
          <button type="submit" class="btn btn-primary">
            Simpan
          </button>
        </form>
      </div>
    </div>
  </div>
@endsection
