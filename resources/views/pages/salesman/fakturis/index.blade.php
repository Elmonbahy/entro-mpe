@extends('layouts.main-layout')

@section('title')
  Data salesman
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Data salesman" class="mb-3">
      <a href="{{ route('fakturis.salesman.create') }}" class="btn btn-primary">
        Tambah data
      </a>
    </x-page-header>

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Tabel data salesman</p>
      </div>

      <div class="p-3">
        @livewire('table.salesman-table')
      </div>
    </div>
  </div>
@endsection
