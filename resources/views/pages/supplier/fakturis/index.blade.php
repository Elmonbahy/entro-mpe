@extends('layouts.main-layout')

@section('title')
  Data Supplier
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Data supplier" class="mb-3">
      <a href="{{ route('fakturis.supplier.create') }}" class="btn btn-primary">
        Tambah data
      </a>
    </x-page-header>

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Tabel data supplier</p>
      </div>

      <div class="p-3">
        <a href="{{ route('fakturis.supplier.export') }}" class="btn btn-secondary" title="Download Excel">
          <i class="bi bi-file-earmark-arrow-down"></i>
        </a>
        @livewire('table.supplier-table')
      </div>
    </div>
  </div>
@endsection
