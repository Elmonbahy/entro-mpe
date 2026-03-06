@extends('layouts.main-layout')

@section('title')
  Data pelanggan
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Data pelanggan" class="mb-3">
      <a href="{{ route('supervisor.pelanggan.create') }}" class="btn btn-primary">
        Tambah data
      </a>
    </x-page-header>

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Tabel data pelanggan</p>
      </div>

      <div class="p-3">
        <a href="{{ route('supervisor.pelanggan.export') }}" class="btn btn-secondary" title="Download Excel">
          <i class="bi bi-file-earmark-arrow-down"></i>
        </a>
        @livewire('table.pelanggan-table')
      </div>
    </div>
  </div>
@endsection
