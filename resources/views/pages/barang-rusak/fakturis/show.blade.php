@extends('layouts.main-layout')

@section('content')
  <div class="container-fluid px-4">
    <x-page-header title="Data barang rusak" class="mb-3" />

    <div class="card">
      <div class="p-3 card-header">
        <p class="mb-0 fw-semibold">Detail data barang rusak</p>
      </div>

      <div class="p-3">
        <table class="table mb-0">
          <tbody>
            <tr>
              <td>Tanggal Input</td>
              <td>{{ $barang_rusak->tgl_rusak ? \Carbon\Carbon::parse($barang_rusak->tgl_rusak)->format('d/m/Y') : '-' }}
              </td>
            </tr>
            <tr>
              <td>Barang</td>
              <td>
                <x-button.link-button href="{{ route('fakturis.barang.show', $barang_rusak->barang->id) }}">
                  {{ $barang_rusak->barang->nama }}
                </x-button.link-button>
              </td>
            </tr>
            <tr>
              <td>Batch</td>
              <td>{{ $barang_rusak->barangStock->batch ?? '-' }}</td>
            </tr>
            <tr>
              <td>Expired</td>
              <td>
                {{ $barang_rusak->barangStock->tgl_expired
                    ? \Carbon\Carbon::parse($barang_rusak->barangStock->tgl_expired)->format('d/m/Y')
                    : '-' }}
              </td>
            </tr>
            <tr>
              <td width="150">Penyebab </td>
              <td>{{ $barang_rusak->penyebab }}</td>
            </tr>
            <tr>
              <td width="200">Tindakan</td>
              <td>{{ $barang_rusak->tindakan }}</td>
            </tr>
            <tr>
              <td>Jumlah barang rusak </td>
              <td>{{ $barang_rusak->jumlah_barang_rusak }} {{ $barang_rusak->barang->satuan }}</td>
            </tr>
            <tr>
              <td>Keterangan</td>
              <td>{{ $barang_rusak->keterangan ?? '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
