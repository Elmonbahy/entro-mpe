@extends('layouts.pdf-layout')

@section('content')
  <div class="p-4">
    <div class="pb-2"
      style="position: relative; border-bottom: 2px solid black; display: flex; align-items: center; padding-bottom: 5px;">
      <!-- Logo -->
      <div style="margin-right: 10px;">
        <img src="{{ asset('logo.png') }}" alt="apm" style="width: 50px;">
      </div>

      <!-- Teks -->
      <div class="text-left" style="margin-left: 70px; margin-top: -50px;">
        <p class="fw-bold mb-0" style="font-size: 15px;">PT. ANNA PUTRA MANDIRI</p>
        <p style="font-size: 10px">IPAK. 91200152321820001|Telp. 0380-834696|Email. pt.annaputramandiri.com</p>
        <p style="font-size: 10px">Jl. Nusa indah, RT 014, RW 006, Oepura, Maulafa, Kota Kupang, Nusa Tenggara Timur</p>
      </div>

    </div>

    <div class="text-center pb-3 mt-2">
      <p class="fw-bold mb-0" style="font-size: 15px;">SURAT PESANAN</p>
      <p>Nomor Pemesanan : {{ $spbeli->nomor_pemesanan }}</p>
      <p class="text-end">Tanggal: {{ $spbeli->tgl_sp ? \Carbon\Carbon::parse($spbeli->tgl_sp)->format('d/m/Y') : '-' }}
      </p>
    </div>

    <div class="py-2">
      <p>Kepada Yth.</p>
      <p class="fw-bold">{{ $spbeli->supplier->nama }}</p>
      <p>{{ $spbeli->supplier->kota }}</p>
      <p>{{ $spbeli->supplier->alamat }}</p>
    </div>

    <!-- Clear Floats -->
    <div style="clear: both;"></div>

    <p class="mb-2">
      Mohon segera dapat dilayani pesanan kami sebagai berikut:
    </p>

    <table class="table table-bordered table-sm mb-2">
      <thead>
        <th>No</th>
        <th>Nama Barang</th>
        <th>Jumlah</th>
        <th>Satuan</th>
        <th>Keterangan</th>
      </thead>
      <tbody>
        @foreach ($spbeli_detail as $item)
          <tr>
            <td width="20" class="text-center">{{ $loop->iteration }}.</td>
            <td width="200">{{ $item->barang->nama }}</td>
            <td width="30" class="text-center">{{ $item->jumlah_barang_dipesan }}</td>
            <td width="60" class="text-center">{{ $item->barang->satuan }}</td>
            <td width="60" class="text-left">{{ $item->keterangan }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="5">
            <i style="font-size: 10px">Note: - Mohon barang pesanan yang dikirm expired minimal 3 tahun.</i>
            <br>
            <i style="font-size: 10px">- Pada saat pengiriman barang mohon disertai fakturnya</i>
          </td>
        </tr>
      </tfoot>
    </table>


    <table class="table">
      <tbody class="text-center">
        <tr class="fw-bold">
          <td>Mengetahui</td>
          <td style="visibility: hidden">Penanggung Jawab</td>
          <td>Hormat Kami</td>
        </tr>
        <tr>
          <td height="30" colspan="3"></td>
        </tr>
        <tr>
          <td>
            <span>Fransiskus B. Gatin</span>
            <span class="d-block mx-auto my-1" style="background-color: black; width: 200px; height: 0  ;"></span>
            <span>Direktur</span>
          </td>
          <td style="visibility: hidden">
            <span>Egenius Tonce</span>
            <span class="d-block mx-auto my-1" style="background-color: black; width: 200px; height: 0  ;"></span>
            <span>xxx</span>
          </td>
          <td>
            <span>Egenius Tonce</span>
            <span class="d-block mx-auto my-1" style="background-color: black; width: 200px; height: 0;"></span>
            <span>DPMPTSP.69/SIK-TTK/V/202</span>
          </td>
        </tr>
      </tbody>
    </table>

  </div>
@endsection
