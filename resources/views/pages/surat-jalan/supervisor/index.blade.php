@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Data surat jalan" class="mb-3">
    </x-page-header>

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Tabel surat jalan</p>
      </div>

      <div class="p-3">
        <a href="{{ route('supervisor.surat-jalan.excel') }}" class="btn btn-secondary" title="Download Excel">
          <i class="bi bi-file-earmark-arrow-down"></i>
        </a>
        @livewire('table.surat-jalan-table')
      </div>
    </div>
  </div>
@endsection
