@extends('layouts.main-layout')

@section('title')
  Data Group
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Data group" class="mb-3">
    </x-page-header>

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Tabel data group</p>
      </div>

      <div class="p-3">
        <a href="{{ route('supervisor.group.export') }}" class="btn btn-secondary" title="Download Excel">
          <i class="bi bi-file-earmark-arrow-down"></i>
        </a>
        @livewire('table.group-table')
      </div>
    </div>
  </div>
@endsection
