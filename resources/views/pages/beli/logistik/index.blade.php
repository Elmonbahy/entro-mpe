@extends('layouts.main-layout')

@section('title')
  Data barang masuk
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Data barang masuk" class="mb-3">
    </x-page-header>

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Tabel data barang masuk</p>
      </div>

      <div class="p-3">
        @livewire('table.beli-table')
      </div>
    </div>
  </div>
@endsection
