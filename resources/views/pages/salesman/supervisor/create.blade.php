@extends('layouts.main-layout')

@section('title')
  Tambah salesman
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Tambah data salesman" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form input data salesman</p>
      </div>

      <div class="p-3">
        <form action="{{ route('supervisor.salesman.store') }}" method="post" autocomplete="off">
          @csrf
          <div class="mb-3">
            <x-form.label value="Nama salesman" />
            <x-form.input name="nama" placeholder="Input nama salesman..." />
          </div>
          <button type="submit" class="btn btn-primary">
            Simpan
          </button>
        </form>
      </div>
    </div>
  </div>
@endsection
