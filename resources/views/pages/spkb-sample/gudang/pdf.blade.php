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

    <div class="text-center pb-1 mt-2">
      <p class="fw-bold mb-0" style="font-size: 15px;">SURAT PERINTAH KELUAR BARANG SAMPEL</p>
      <p>Nomor Sampel : <strong>{{ $sample_out->nomor_sample }}</strong></p>
      <p class="text-end">Tanggal:
        {{ $sample_out->tanggal ? \Carbon\Carbon::parse($sample_out->tanggal)->format('d/m/Y') : '-' }}
      </p>
    </div>

    <table style="width: 100%; margin-bottom: 15px;">
      <tr>
        <!-- Kolom kiri: info pelanggan -->
        <td style="vertical-align: top; width: 60%;">
          <p>Pelanggan.</p>
          <p class="fw-bold">{{ $sample_out->pelanggan->nama }}</p>
          <p>{{ $sample_out->pelanggan->kota }}</p>
          <p>{{ $sample_out->pelanggan->alamat }}</p>
          <p>{{ $sample_out->pelanggan->contact_person }}</p>
          <p>{{ $sample_out->pelanggan->contact_phone }}</p>
        </td>

        <!-- Kolom kanan: garis putus-putus -->
        <div style="border: 1px dashed rgb(0, 0, 0); padding: 8px; width: 300px; height: auto;">
          <table style="width: 100%; font-size: 8px;">
            <tr>
              <td style="width: 80px; padding-bottom: 5px;">Sales</td>
              <td style="padding-bottom: 5px;">:</td>
              <td style="padding-bottom: 5px;">{{ $sample_out->salesman->nama }}</td>
            </tr>
            <tr>
              <td style="width: 80px; padding-bottom: 5px;">Picker</td>
              <td style="padding-bottom: 5px;">:</td>
              <td style="padding-bottom: 5px;">__________</td>
            </tr>
            <tr>
              <td style="padding-bottom: 5px;">Waktu Selesai</td>
              <td style="padding-bottom: 5px;">:</td>
              <td style="padding-bottom: 5px;">__________</td>
            </tr>
            <tr>
              <td style="padding-bottom: 5px;">Jumlah Koli</td>
              <td style="padding-bottom: 5px;">:</td>
              <td style="text-align: right; font-size: 8px; padding-bottom: 5px;">TTD</td>
            </tr>
          </table>
        </div>
      </tr>
    </table>

    <!-- Clear Floats -->
    <div style="clear: both;"></div>

    <p class="mb-2">
      Mohon segera dapat disiapkan barang sampel sebagai berikut:
    </p>

    <table class="table table-bordered table-sm mb-2">
      <thead>
        <th>No</th>
        <th>Brand</th>
        <th>Nama Barang</th>
        <th>Kode</th>
        <th>Jumlah</th>
        <th>Satuan</th>
        <th>Batch</th>
        <th>Expired</th>
        <th>NIE</th>
      </thead>
      <tbody>
        @foreach ($sample_out_detail as $item)
          <tr>
            <td width="10" class="text-center">{{ $loop->iteration }}.</td>
            <td width="40">{{ $item->sampleBarang->barang->brand->nama }}</td>
            <td width="150">{{ $item->sampleBarang->barang->nama }}</td>
            <td width="30" class="text-center">{{ $item->sampleBarang->barang->kode ?? '-' }}</td>
            <td width="30" class="text-center">{{ number_format($item->jumlah_barang_dipesan, 0, ',', '.') }}</td>
            <td width="30" class="text-center">{{ $item->sampleBarang->satuan }}</td>
            <td width="30" class="text-center">{{ $item->batch ?? '-' }}</td>
            <td width="30" class="text-center">
              {{ $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') : '-' }}
            </td>
            <td width="30">{{ $item->sampleBarang->barang->nie }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <p class="mt-3 mb-1">Perbaikan</p>
    <table class="table table-bordered table-sm">
      <thead>
        <tr>
          <th style="width: 10px;">No Barang</th>
          <th style="width: 20px;">Jumlah</th>
          <th style="width: 80px;">Batch</th>
          <th style="width: 60px;">Expired</th>
          <th style="width: 150px;">NIE</th>
        </tr>
      </thead>
      <tbody>
        @for ($i = 0; $i < 3; $i++)
          <tr>
            <td style="height: 8;"></td>
            <td style="height: 8;"></td>
            <td style="height: 8;"></td>
            <td style="height: 8;"></td>
            <td style="height: 8;"></td>
          </tr>
        @endfor
      </tbody>
    </table>
    <p style="text-align: right; font-size: 9px; margin-top: 5px;">
      Dicetak pada: {{ $waktu_cetak }}
    </p>
  </div>
@endsection
