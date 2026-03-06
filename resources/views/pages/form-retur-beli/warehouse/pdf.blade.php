@extends('layouts.pdf-layout')

@section('content')
  <div class="p-4">
    <div
      style="position: relative; border-bottom: 2px solid black; display: flex; align-items: center; padding-bottom: 5px;">
      <!-- Logo -->
      <div style="margin-right: 10px;">
        <img src="{{ asset('logo.png') }}" alt="apm" style="width: 50px;">
      </div>

      <!-- Teks -->
      <div class="text-left" style="margin-left: 70px; margin-top: -50px;">
        <p class="fw-bold mb-0" style="font-size: 15px;">PT. ANNA PUTRA MANDIRI</p>
        <p style="font-size: 10px">IPAK. 91200152321820001|Telp. 0380-8443905|Email. pt.annaputramandiri.com</p>
        <p style="font-size: 10px">Jl. Nusa indah, RT 14, RW 06, Oepura, Maulafa, Kota Kupang, Nusa Tenggara Timur</p>
      </div>
    </div>

    <div class="text-start pb-1 mt-2">
      <p class="fw-bold mb-0" style="font-size: 15px;">FORM PERMINTAAN RETUR</p>
      <p class="text-end">No:
        {{ $beli->nomor_faktur }}
      </p>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
      <tr>
        <!-- Kolom Kiri -->
        <td style="padding: 5px; vertical-align: top;">
          <table>
            <tr>
              <td>Nama Supplier</td>
              <td style="padding: 0 5px;">:</td>
              <td>{{ $beli->supplier->nama }}</td>
            </tr>
            <tr>
              <td>No. Surat Jalan</td>
              <td style="padding: 0 5px;">:</td>
              <td></td>
            </tr>
          </table>
        </td>

        <!-- Kolom Kanan -->
        <td style="padding: 5px; vertical-align: top;">
          <table>
            <tr>
              <td>Tanggal Penggembalian</td>
              <td style="padding: 0 5px;">:</td>
              <td></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>


    <!-- Clear Floats -->
    <div style="clear: both;"></div>

    <table class="table table-bordered table-sm mb-2">
      <thead>
        <th>No</th>
        <th>Nama Barang / Type</th>
        <th>SN / Tanggal Kadalruasa</th>
        <th>Qty</th>
        <th>Alasan Retur</th>
      </thead>
      <tbody>
        @foreach ($returs as $item)
          <tr>
            <td width="10" class="text-center">{{ $loop->iteration }}.</td>
            <td width="130">{{ $item->barang->nama }}</td>
            <td width="50" class="text-center">{{ $item->batch }} |
              {{ $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') : '' }}</td>
            <td width="20" class="text-center">{{ number_format($item->jumlah_barang_retur, 0, ',', '.') }}</td>
            <td width="30">{{ $item->keterangan }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
      <tr>
        <!-- NOTE -->
        <td style="width: 50%; vertical-align: top; padding-right: 10px;">
          <table style="width: 100%; border: 1px solid black; border-collapse: collapse;">
            <tr>
              <td style="border-bottom: 1px solid black;"><strong>Note:</strong></td>
            </tr>
            <tr>
              <td style="height: 80px;"></td>
            </tr>
          </table>
        </td>

        <!-- TTD -->
        <td style="width: 50%; vertical-align: top;">
          <table style="width: 100%; border-collapse: collapse;">
            <tr>
              <td style="width: 50%; border: 1px solid black; text-align: center;"><strong>Gudang</strong></td>
              <td style="width: 50%; border: 1px solid black; text-align: center;"><strong>PJT</strong></td>
            </tr>
            <tr>
              <td style="border: 1px solid black; height: 80px;"></td>
              <td style="border: 1px solid black;"></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <p class="text-end mt-1" style="font-size: 10px">F.APM/WH-007 R.00/301123</p>
  </div>
@endsection
