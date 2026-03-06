@extends('layouts.main-layout')

@section('title')
  Data Kendaraan
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-alert.session-alert />
    <x-page-header title="Data kendaraan" class="mb-3">
      <a href="{{ route('gudang.kendaraan.create') }}" class="btn btn-primary">
        Tambah data
      </a>
    </x-page-header>

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Tabel data kendaraan</p>
      </div>

      <div class="p-3">
        @livewire('table.kendaraan-table')
      </div>
    </div>
  </div>
@endsection
