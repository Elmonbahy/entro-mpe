@extends('layouts.main-layout')

@section('title')
  Tambah group
@endsection

@section('content')
  <div class="container-sm px-4">
    <x-page-header title="Tambah data group" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form input data group</p>
      </div>

      <div class="p-3">
        <form action="{{ route('supervisor.group.store') }}" method="post" autocomplete="off">
          @csrf
          <div class="mb-3">
            <x-form.label value="Nama group" />
            <x-form.input name="nama" placeholder="Input nama group..." />
          </div>
          <button type="submit" class="btn btn-primary">
            Simpan
          </button>
        </form>
      </div>
    </div>
  </div>
@endsection
