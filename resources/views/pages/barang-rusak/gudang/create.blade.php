@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Tambah data" class="mb-3" />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Form tambah barang rusak</p>
      </div>
      <div class="p-3">
        <livewire:barang-rusak.create-form />
      </div>
    </div>
  </div>
@endsection
