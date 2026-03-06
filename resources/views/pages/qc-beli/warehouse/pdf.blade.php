@extends('layouts.pdf-layout')

<style>
  .qc-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    margin-top: 20px;
  }

  .qc-table td,
  .qc-table th {
    border: 1px solid #000;
    padding: 6px;
    vertical-align: top;
  }

  .checkbox-label {
    display: inline-block;
    margin-right: 12px;
  }

  .checkbox-box {
    display: inline-block;
    width: 14px;
    height: 14px;
    border: 1px solid #000;
    margin-right: 5px;
    vertical-align: middle;
  }

  .section-title {
    font-weight: bold;
  }

  .multi-box {
    display: flex;
    gap: 20px;
  }
</style>

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
      <p class="fw-bold mb-0" style="font-size: 15px;">LAPORAN HASIL QC</p>
      <p class="text-center">No:
        {{ $beli->nomor_faktur }}
      </p>
    </div>

    <table style="width: 100%; font-family: sans-serif; font-size: 12px; text-align: center; margin: 0 auto;">
      <tr>
        <td style="padding: 4px;">
          <span
            style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; vertical-align: middle; margin-right: 4px;"></span>
          <span style="vertical-align: middle;">QC Barang Masuk</span>
        </td>
        <td style="padding: 4px;">
          <span
            style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; vertical-align: middle; margin-right: 4px;"></span>
          <span style="vertical-align: middle;">QC Barang Keluar</span>
        </td>
        <td style="padding: 4px;">
          <span
            style="display: inline-block; width: 12px; height: 12px; border: 1px solid #000; vertical-align: middle; margin-right: 4px;"></span>
          <span style="vertical-align: middle;">QC Barang Retur</span>
        </td>
      </tr>
    </table>

    {{-- @foreach ($beli->beliDetails as $item) --}}
    <table style="font-family: sans-serif; font-size: 13px; width: 100%;" class="mt-1">
      <tr>
        <td style="width: 150px;"><strong>Nama Barang</strong></td>
        <td style="width: 10px;">:</td>
        <td style="border-bottom: 1px dotted #000;">
          <strong>{{ $beli_detail->barang->nama ?? '' }}</strong>
        </td>
      </tr>
      <tr>
        <td><strong>Type</td></strong>
        <td>:</td>
        <td style="border-bottom: 1px dotted #000;">&nbsp;</td>
      </tr>
      <tr>
        <td><strong>Serial Number</td></strong>
        <td>:</td>
        <td style="border-bottom: 1px dotted #000;">
          <strong>{{ $beli_detail->batch ?? '' }}</strong>
        </td>
      </tr>
      <tr>
        <td><strong>Expired Date </strong><sup>*)</sup></td>
        <td>:</td>
        <td style="border-bottom: 1px dotted #000;">
          <strong>
            {{ $beli_detail->tgl_expired ? \Carbon\Carbon::parse($beli_detail->tgl_expired)->format('d/m/Y') : '' }}</strong>
        </td>
      </tr>
    </table>

    {{-- @endforeach --}}


    <table class="qc-table">
      <tr>
        <td class="section-title">Jenis Pengecekan</td>
        <td colspan="3">
          <div class="multi-box">
            <div class="checkbox-label">
              <div class="checkbox-box"></div> Uji Fungsi
            </div>
            <div class="checkbox-label">
              <div class="checkbox-box"></div> Uji Software
            </div>
            <div class="checkbox-label">
              <div class="checkbox-box"></div> Visual
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <td class="section-title">Label Penandaan</td>
        <td colspan="3">
          <div class="multi-box">
            <div class="checkbox-label">
              <div class="checkbox-box"></div> Ada / Sesuai
            </div>
            <div class="checkbox-label">
              <div class="checkbox-box"></div> Tidak ada / Tidak Sesuai
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <td class="section-title">Analisa Pengecekan</td>
        <td colspan="3" style="height: 40px;"></td>
      </tr>
      <tr>
        <td class="section-title">Kesimpulan</td>
        <td colspan="3">
          <div class="multi-box">
            <div class="checkbox-label">
              <div class="checkbox-box"></div> OK
            </div>
            <div class="checkbox-label">
              <div class="checkbox-box"></div> TIDAK OK
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <td class="section-title">Tindak Lanjut Perbaikan</td>
        <td colspan="3" style="height: 40px;"></td>
      </tr>
    </table>

    <p class="text-start mt-1">
      Kupang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
    </p>

    <table style="width: 50%; border-collapse: collapse; margin-top: 8px;">
      <tr>
        <!-- TTD -->
        <td style="width: 50%; vertical-align: top;">
          <table style="width: 100%; border-collapse: collapse;">
            <tr>
              <td style="width: 50%; border: 1px solid black; text-align: center;"><strong>Petugas QC</strong></td>
              <td style="width: 50%; border: 1px solid black; text-align: center;"><strong>PJT</strong></td>
              <td style="width: 50%; border: 1px solid black; text-align: center;"><strong>Gudang</strong></td>
            </tr>
            <tr>
              <td style="border: 1px solid black; height: 80px;"></td>
              <td style="border: 1px solid black;"></td>
              <td style="border: 1px solid black;"></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <p class="text-end" style="font-size: 10px">F.APM/TK-001 R.00/301123</p>
    <div style="position: absolute; top: 14.85cm; width: 100%;">
      <hr style="border-top: 0.5px dashed #5a5a5a; width: 100%;">
    </div>
  @endsection
