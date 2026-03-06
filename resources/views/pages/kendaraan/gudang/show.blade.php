@extends('layouts.main-layout')

@section('title')
  Detail data kendaraan
@endsection

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Data kendaraan" class="mb-3" withBackButton />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Detail data kendaraan</p>
      </div>

      <div class="p-3">
        <table class="table mb-0">
          <tbody>
            <tr>
              <td width="200">Nama kendaraan </td>
              <td>{{ $kendaraan->nama }}</td>
            </tr>
            <tr>
              <td>Alamat</td>
              <td>{{ $kendaraan->alamat }}</td>
            </tr>
            <td>Person</td>
            <td>{{ $kendaraan->contact_person }}</td>
            </tr>
            <tr>
              <td>Telepon</td>
              <td>{{ $kendaraan->contact_phone }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
