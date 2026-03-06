@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Data pembelian" class="mb-3">
    </x-page-header>

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Tabel data Pembelian</p>
      </div>

      <div class="p-3">
        @livewire('table.beli-table')
      </div>
    </div>
  </div>
@endsection
