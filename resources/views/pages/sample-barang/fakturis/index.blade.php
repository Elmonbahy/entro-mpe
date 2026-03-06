@extends('layouts.main-layout')

@section('title')
  Data barang
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Data barang sampel" class="mb-3">
      <a href="{{ route('fakturis.sample-barang.create') }}" class="btn btn-primary">
        Tambah data
      </a>
    </x-page-header>

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Tabel data barang sampel</p>
      </div>

      <div class="p-3">
        @livewire('table.sample-barang-table')
      </div>
    </div>
  </div>
@endsection
