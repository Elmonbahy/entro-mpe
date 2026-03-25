@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Data stock barang" class="mb-3" />

    <div class="card">
      <div class="p-3 card-header d-flex justify-content-between align-items-center">
        <p class="mb-0 fw-semibold">Tabel data stock barang</p>
      </div>

      <div class="p-3">
        @livewire('table.barang-stock-table')
      </div>
    </div>
  </div>
@endsection
