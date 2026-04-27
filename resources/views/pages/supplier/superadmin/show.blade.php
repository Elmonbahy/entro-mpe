@extends('layouts.main-layout')

@section('title')
  Detail data supplier
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Data supplier" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Detail data supplier</p>
      </div>

      <div class="p-3">
        <table class="table mb-0">
          <tbody>
            <tr>
              <td width="200">Nama supplier </td>
              <td>{{ $supplier->nama }}</td>
            </tr>
            <tr>
              <td>Kota </td>
              <td>{{ $supplier->kota }}</td>
            </tr>
            <tr>
              <td>Alamat</td>
              <td>{{ $supplier->alamat }}</td>
            </tr>
            <tr>
              <td>Person</td>
              <td>{{ $supplier->contact_person }}</td>
            </tr>
            <tr>
              <td>Telepon</td>
              <td>{{ $supplier->contact_phone }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
