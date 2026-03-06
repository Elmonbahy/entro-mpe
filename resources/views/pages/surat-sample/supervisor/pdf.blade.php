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
      <p class="fw-bold mb-0" style="font-size: 15px;">SURAT PENGIRIMAN SAMPEL BARANG</p>
      <p>Nomor Sampel : <strong>{{ $sample_out->nomor_sample }}</strong></p>
      <p class="text-end">Tanggal:
        {{ $sample_out->tanggal ? \Carbon\Carbon::parse($sample_out->tanggal)->format('d/m/Y') : '-' }}
      </p>
    </div>

    <table style="width: 100%; margin-bottom: 15px;">
      <tr>
        <!-- Kolom kiri: info pelanggan -->
        <td style="vertical-align: top; width: 60%;">
          <p>Kepada Yth.</p>
          <p class="fw-bold">{{ $sample_out->pelanggan->nama }}</p>
          <p>{{ $sample_out->pelanggan->kota }}</p>
          <p>{{ $sample_out->pelanggan->alamat }}</p>
          <p>di Tempat</p>
        </td>
      </tr>
    </table>

    <!-- Clear Floats -->
    <div style="clear: both;"></div>

    <p class="mb-2">
      Dengan hormat,
    </p>
    <p class="mb-2">
      Sehubungan dengan permintaan Bapak/Ibu untuk mendapatkan contoh produk/sampel, bersama ini kami kirimkan sampel
      barang dari PT. Mathio Jaya Pharma sebagai berikut:
    </p>

    <table class="table table-bordered table-sm mb-2">
      <thead>
        <th>No</th>
        <th>Brand</th>
        <th>Nama Barang</th>
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
            <td width="30" class="text-center">{{ number_format($item->jumlah_barang_keluar, 0, ',', '.') }}</td>
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
    <table style="width: 100%; margin-top: 30px; text-align: center;">
      <tr>
        <td style="width: 50%;">
          <p class="fw-bold mb-4">Yang Menyerahkan,</p>
          <br><br><br>
          <p style="text-decoration: underline; font-weight: bold;">
            {{ $sample_out->salesman->nama ?? '____________________' }}
          </p>
          <p>Sales</p>
        </td>

        <td style="width: 50%;">
          <p class="fw-bold mb-4">Yang Menerima,</p>
          <br><br><br>
          <p style="text-decoration: underline; font-weight: bold;">
            {{ '____________________' }}
          </p>
          <p>Pelanggan</p>
        </td>
      </tr>
    </table>
    <p class="mt-5" style="font-size: 10px; text-align: center;">
      *Barang sampel ini diberikan untuk tujuan promosi, bukan untuk diperjualbelikan.
    </p>
  </div>
@endsection
