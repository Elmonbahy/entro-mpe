@extends('layouts.main-layout')

@section('title')
  Data Sampel
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Data sampel masuk" class="mb-3">
      <a href="{{ route('supervisor.sample-in.create') }}" class="btn btn-primary">
        Tambah data
      </a>
    </x-page-header>

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Tabel data sampel masuk</p>
      </div>

      <div class="p-3">
        @livewire('table.sample-in-table')
      </div>
    </div>
  </div>
@endsection
