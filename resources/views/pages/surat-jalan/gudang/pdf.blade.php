@extends('layouts.pdf-layout')
@push('styles')
  <style>
    table td {
      border-collapse: collapse;
      /* Menggabungkan border */
      padding: 0;
      /* Hilangkan padding */
      margin: 0;
      /* Hilangkan margin */
      line-height: 1;
      /* Kurangi tinggi baris */

      .table-sm {
        font-size: 9px;
        /* Atur ukuran font di sini */
      }

      .table-sm td,
      .table-sm th {
        font-size: 9px;
        /* Atur ukuran font untuk setiap sel */
      }
    }
  </style>
@endpush

@section('content')
  <div class="p-4">
    <div style="position: absolute; ">
      <img src="{{ asset('logo.png') }}" alt="apm" style="width: 50px;">
    </div>

    <div class="text-center pb-2 mb-1" style="border-bottom: 2px solid black;">
      <p class="fw-bold mb-1" style="font-size: 15px;">PT. ANNA PUTRA MANDIRI</p>
      <p style="font-size: 10px">Jl. Nusa indah, RT 14, RW 06, Oepura, Maulafa, Kota Kupang, Nusa Tenggara Timur. (Telp.
        0380-8443905)</p>
      <p class="fw-bold" style="position: absolute; right: 18px; top: 40px;">SURAT JALAN</p>
    </div>

    <div class="mb-1">
      <div style="border: 1px solid black; float: left; width: 47%; margin-right: 6%; min-height: 75px;" class="p-2">
        <table class="table table-compact" style="border-spacing: 0; border-collapse: collapse;">
          <tbody>
            <tr>
              <td width="70" style="padding: 1;">No. Surat Jalan</td>
              <td style="padding: 1;">: {{ $surat_jalan->nomor_surat_jalan }}</td>
            </tr>
            <tr>
              <td style="padding: 1;">Tanggal</td>
              <td style="padding: 1;">:
                {{ $surat_jalan->tgl_surat_jalan ? \Carbon\Carbon::parse($surat_jalan->tgl_surat_jalan)->format('d/m/Y') : '-' }}
              </td>
            </tr>
            <tr>
              <td style="padding: 1;">Kendaraan</td>
              <td style="padding: 1;">: {{ $surat_jalan->kendaraan->nama }}</td>
            </tr>
            <tr>
              <td style="padding: 1;">No. Kendaraan</td>
              <td style="padding: 1;">:</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div style="border: 1px solid black; float: right; width: 47%; min-height: 75px;" class="p-2">
        <p style="margin: 0;">Kepada Yth.</p>
        <p class="fw-bold mb-1" style="margin: 0;">{{ $surat_jalan->pelanggan->nama }}</p>

        <table class="table table-compact" style="border-spacing: 0; border-collapse: collapse;">
          <tbody>
            <tr>
              <td width="45" style="padding: 0;">Alamat</td>
              <td style="padding: 0;">: {{ $surat_jalan->alamat_kirim ?? $surat_jalan->pelanggan->alamat }}</td>
            </tr>
            <tr>
              <td style="padding: 0;">Kota</td>
              <td style="padding: 0;">: {{ $surat_jalan->kota ?? $surat_jalan->pelanggan->kota }}</td>
            </tr>
            <tr>
              <td style="padding: 0;">Up Nama</td>
              <td style="padding: 0;">: {{ $surat_jalan->contact_person ?? $surat_jalan->pelanggan->contact_person }}</td>
            </tr>
            <tr>
              <td style="padding: 0;">No HP</td>
              <td style="padding: 0;">: {{ $surat_jalan->contact_phone ?? $surat_jalan->pelanggan->contact_phone }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Clear Floats -->
      <div style="clear: both;"></div>
    </div>

    <p class="mb-2" style="font-size: 10px">
      Bersama ini kami kirimkan barang-barang berikut dengan No. Faktur:
      {{ $fakturs }}
    </p>

    @php
      $baris = count($surat_jalan_details);
      $minHeight = $baris <= 10 ? '65mm' : '200mm';
    @endphp

    <div class="table-detail-container" style="min-height: {{ $minHeight }};">
      <table class="table table-bordered table-sm mb-1">
        <thead>
          <th>No</th>
          <th>Nama Barang</th>
          <th>Jumlah</th>
          <th>Satuan</th>
          <th>Batch</th>
          <th>Expired</th>
          <th>NIE</th>
        </thead>
        <tbody>
          @foreach ($surat_jalan_details as $item)
            <tr>
              <td width="5" class="text-center">{{ $loop->iteration }}.</td>
              <td width="200">{{ $item->barang->nama }}</td>
              <td width="10" class="text-center">{{ $item->jumlah_barang_dikirim }}</td>
              <td width="30" class="text-center">{{ $item->barang->satuan }}</td>
              <td width="30" class="text-center">{{ $item->jualDetail->batch }}</td>
              <td width="30" class="text-center">
                {{ $item->jualDetail->tgl_expired ? \Carbon\Carbon::parse($item->jualDetail->tgl_expired)->format('d/m/Y') : '-' }}
              </td>
              <td width="50" class="text-center">{{ $item->barang->nie }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3">
              <i style="font-size: 9px">
                Note: -Mohon diperiksa terlebih dahulu sebelum menerimanya.
              </i>
              <br>
              <i style="font-size: 9px">-Batas waktu complain 1x24 jam setelah menerima barang.</i>
            </td>
            <td colspan="4">
              Jumlah koli yang dikirim: <strong>{{ $surat_jalan->koli }} Koli</strong>
            </td>
          </tr>
          @if (!empty($surat_jalan->keterangan))
            <tr>
              <td colspan="7" style="font-size: 9px;">
                Keterangan: {{ $surat_jalan->keterangan }}
              </td>
            </tr>
          @endif
        </tfoot>
      </table>
    </div>


    <table class="table">
      <tbody class="text-center">
        <tr>
          <td>Mengetahui PJ</td>
          <td>Driver/Ekspedisi</td>
          <td>Penerima</td>
        </tr>
        <tr>
          <td height="15" colspan="3"></td>
        </tr>
        <tr>
          <td>
            <span class="d-block">Egenius Tonce</span>
            <span class="d-block mx-auto my-1" style="background-color: black; width: 200px; height: 0;"></span>
          </td>
          <td>
            <span class="d-block" style="visibility: hidden">Egenius Tonce</span>
            <span class="d-block mx-auto my-1" style="background-color: black; width: 200px; height: 0;"></span>
          </td>
          <td>
            <span class="d-block" style="visibility: hidden">Egenius TonceEgenius Tonce</span>
            <span class="d-block mx-auto my-1" style="background-color: black; width: 200px; height: 0;"></span>
          </td>
        </tr>
      </tbody>
    </table>

  </div>
@endsection
