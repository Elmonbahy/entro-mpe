@extends('layouts.pdf-layout')

@section('content')
  <div class="p-4">
    <div style="position: absolute; ">
      <img src="{{ asset('logo.png') }}" alt="apm" style="width: 50px;">
    </div>

    <div class="text-center pb-2 mb-2" style="border-bottom: 2px solid black;">
      <p class="fw-bold mb-1" style="font-size: 15px;">PT. ANNA PUTRA MANDIRI</p>
      <p style="font-size: 10px;">Jl. Nusa indah, RT 014, RW 006, Oepura, Maulafa, Kota Kupang, Nusa Tenggara Timur.
        (Telp.
        0380-834696)</p>
    </div>

    <div class="mb-2">
      <p>Kepada Yth.</p>
      <p>Bagian keuangan</p>
      <p class="fw-bold">{{ $pelanggan->nama }}</p>
    </div>

    <div class="mb-1">
      <p>Dengan hormat,</p>
      <p>Bersama ini kami titipkan faktur asli sebagai lampiran tagihan dengan perincian seperti dibawah
        ini:</p>
    </div>

    <div class="mb-1">
      @include('pages.titip-faktur.keuangan.table', ['juals' => $juals])
    </div>

    <p class="mb-1">Demikian, atas perhatian dan kerja samanya yang baik, kami ucapkan terimakasih.</p>

    <p class="text-end mb-1">Kupang, {{ now()->format('d/m/Y') }}</p>

    <table class="table">
      <tbody class="text-center">
        <tr>
          <td>Penerima</td>
          <td style="visibility: hidden">Penerima</td>
          <td>Hormat kami</td>
        </tr>

        <tr>
          <td height="20" colspan="3"></td>
        </tr>
        <tr>
          <td>
            <span style="visibility: hidden">Penerima</span>
            <span class="d-block mx-auto my-1" style="background-color: black; width: 200px; height: 0  ;"></span>
            <span></span>
          </td>
          <td style="visibility: hidden">
            <span>Egenius Tonce</span>
            <span class="d-block mx-auto my-1" style="background-color: black; width: 200px; height: 0  ;"></span>
            <span>xxx</span>
          </td>
          <td>
            <span>Mirna Malesi</span>
            <span class="d-block mx-auto my-1" style="background-color: black; width: 200px; height: 0;"></span>
            <span>Inkaso</span>
          </td>
        </tr>
      </tbody>
    </table>

  </div>
@endsection
