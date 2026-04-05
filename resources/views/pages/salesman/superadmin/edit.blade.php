@extends('layouts.main-layout')

@section('title')
  Ubah salesman
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Tambah data salesman" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form ubah data salesman</p>
      </div>

      <div class="p-3">
        <form action="{{ route('superadmin.salesman.update', ['salesman' => $salesman->id]) }}" method="post"
          autocomplete="off">
          @csrf
          @method('PATCH')
          <div class="mb-3">
            <p>Kode Salesman: <strong>{{ $salesman->kode }}</strong></p>
          </div>
          <div class="mb-3">
            <x-form.label value="Nama salesman" />
            <x-form.input name="nama" placeholder="Input nama salesman..." :value="$salesman->nama" />
          </div>
          <button type="submit" class="btn btn-primary">
            Simpan
          </button>
        </form>
      </div>
    </div>
  </div>
@endsection
