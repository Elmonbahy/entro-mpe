@extends('layouts.pdf-layout')
@push('styles')
  <style>
    @page {
      margin: 0;
      /* Biarkan CSS body yang mengatur jarak */
    }

    body {
      margin-top: 0cm;
      margin-bottom: 0.5cm;
      margin-left: 0.5cm;
      /* Jangan terlalu lebar agar tidak terpotong di kanan */
      margin-right: 0.3cm;
      width: 20cm;
      /* Batasi lebar maksimal konten (Lebar kertas 21.5cm - margin) */
      font-family: 'Courier', sans-serif;
      font-size: 10pt;
    }

    td span {
      font-size: 10px;
    }

    table.no-border-tbody tbody {
      border: none !important;
    }

    table.no-border-tbody tbody tr td {
      border: none !important;
    }

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

    <div class="mb-0">
      <!-- Bagian Kiri -->
      <div style="float: left; width: 47%; margin-right: 6%; min-height: 50px;" class="p-2">
        <table class="table table-compact">
          <tbody>
            <tr>
              <td class="fw-bold" style="font-size: 14px; display: inline-block;">
                PT. ANNA PUTRA MANDIRI
              </td>
            </tr>
            <tr>
              <td>Jl. Nusa Indah RT.14 RW 06, Kel Oepura, Kec Maulafa</td>
            </tr>
            <tr>
              <td>Telp : 0380-8443905, Kupang - NTT </td>
            </tr>
            <tr>
              <td>IJIN PAK : 91200152321820001</td>
            </tr>
            <tr>
              <td>NPWP : 073.446.107.2-922.000</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Bagian Tengah -->
      <div style="float: left; width: 20%; text-align: center; min-height: 50px; margin-left: -20%;" class="p-2">
        <h4 style="margin: 0; font-size: 14px; border-bottom: 1px solid black;">FAKTUR PENJUALAN</h4>
      </div>

      <!-- Bagian Kanan -->
      <div style="border: 1px solid black; float: right; width: 40%; min-height: 50px;" class="p-2">
        <p>Kepada Yth.</p>
        <p class="fw-bold mb-0">{{ $jual->pelanggan->nama }}</p>

        <table class="table table-compact">
          <tbody>
            <tr>
              <td>{{ $jual->pelanggan->alamat }}</td>
            </tr>
            <tr>
              <td>{{ $jual->pelanggan->kota }}</td>
            </tr>
            <tr>
              <td>NPWP {{ $jual->pelanggan->npwp }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Clear Floats -->
      <div style="clear: both;"></div>
    </div>


    <table class="table table-bordered table-sm mb-1">
      <tr class="text-center">
        <td>
          <span>Nomor Faktur</span>
          <br>
          <span>
            {{ $jual->nomor_faktur }}
          </span>
        </td>
        <td>
          <span>Tanggal</span>
          <br>
          <span>
            {{ $jual->tgl_faktur ? \Carbon\Carbon::parse($jual->tgl_faktur)->format('d/m/Y') : '-' }}
          </span>
        </td>
        <td>
          <span>Jatuh Tempo</span>
          <br>
          <span>
            {{ $jual->tgl_faktur ? \Carbon\Carbon::parse($jual->tgl_faktur)->addDays((int) $jual->kredit)->format('d/m/Y') : '-' }}
          </span>
        </td>
        <td>
          <span>Salesman</span>
          <br>
          <span>
            {{ $jual->salesman->nama }}
          </span>
        </td>
        <td>
          <span>Kode Pelanggan</span>
          <br>
          <span>
            {{ $jual->pelanggan->kode }}
          </span>
        </td>
      </tr>
    </table>

    @php
      $baris = count($jual_detail);
      $minHeight = $baris <= 10 ? '50mm' : '180mm';
    @endphp

    <div class="table-detail-container" style="min-height: {{ $minHeight }};">
      <table class="table table-bordered table-sm mb-1 no-border-tbody">
        <thead>
          <th>No</th>
          <th>Nama Barang</th>
          <th>Kode</th>
          <th>Batch</th>
          <th>Expired</th>
          <th>Jumlah</th>
          <th>Satuan</th>
          <th>Harga/Unit</th>
          <th>Disc</th>
          <th>Nilai</th>
        </thead>
        <tbody>
          @foreach ($jual_detail as $item)
            <tr>
              <td width="10">{{ $loop->iteration }}.</td>
              <td width="190">{{ $item->barang->nama }}</td>
              <td width="20" class="text-center">{{ $item->barang->kode }}</td>
              <td width="40" class="text-center">{{ $item->batch ?? '-' }}</td>
              <td width="30" class="text-center">
                {{ $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired)->format('d/m/Y') : '-' }}
              </td>
              <td width="30" class="text-center">{{ number_format($item->jumlah_barang_keluar, 0, ',', '.') }}</td>
              <td width="20" class="text-center">{{ $item->barang->satuan }}</td>
              <td width="20" class="text-end">{{ number_format($item->harga_jual, 2, ',', '.') }}</td>
              <td width="20" class="text-center">{{ $item->diskon1 }}</td>
              <td width="30" class="text-end">{{ number_format($item->sub_nilai, 0, ',', '.') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <table class="table table-bordered table-sm">
      <thead>
        <th>Jumlah</th>
        <th>Disc 1</th>
        <th>Nilai</th>
        <th>Disc 2</th>
        <th>Total</th>
        <th>PPN</th>
        <th>Tagihan</th>
      </thead>
      <tbody>
        <tr>
          <td width="30" style="text-align: right;">{{ number_format($sum_sub_nilai, 0, ',', '.') }}</td>
          <td width="30" style="text-align: right;">{{ number_format($sum_harga_diskon1, 0, ',', '.') }}</td>
          <td width="30" style="text-align: right;">{{ number_format($sum_nilai_diskon1, 0, ',', '.') }}</td>
          <td width="30" style="text-align: right;">{{ number_format($sum_harga_diskon2, 0, ',', '.') }}</td>
          <td width="30" style="text-align: right;">{{ number_format($sum_total, 0, ',', '.') }}</td>
          <td width="30" style="text-align: right;">{{ number_format($sum_harga_ppn, 0, ',', '.') }}</td>
          <td width="30" style="text-align: right;">{{ number_format($sum_total_tagihan, 0, ',', '.') }}</td>
        </tr>
      </tbody>
      <td>Terbilang</td>
      <td colspan="6" style="text-align: left;">
        {{ terbilang_rupiah(round($sum_total_tagihan)) }}
      </td>
    </table>

    <table class="table">
      <tbody class="text-center">
        <tr class="fw-bold">
          <td>Penerima</td>
          <td>Penanggung Jawab</td>
          <td>Hormat Kami</td>
        </tr>
        <tr>
          <td height="10" colspan="3"></td>
        </tr>
        <tr>
          <td>
            <span style="visibility: hidden"></span>
            <span class="d-block mx-auto my-1" style="background-color: black; width: 200px; height: 0;"></span>
          </td>
          <td>
            <span>Egenius Tonce</span>
            <span class="d-block mx-auto my-1" style="background-color: black; width: 200px; height: 0  ;"></span>
            <span>DPMPTSP.69/SIK-TTK/V/2022</span>
          </td>
          <td>
            <span>Fransiskus B. Gatin</span>
            <span class="d-block mx-auto my-1" style="background-color: black; width: 200px; height: 0;"></span>
            <span>Direktur</span>
          </td>
        </tr>
      </tbody>
    </table>
    <p style="font-size: 9px">Pembayaran Dengan Cheque/Giro/Wesel diangap sah setelah kliring/diuangkan. Barang yang
      telah dibeli tidak dapat
      dikembalikan kecuali dengan perjanjian. pembayaran Faktur ini dapat ditransfer melalui Bank kami
      BANK BCA CABANG KUPANG NO REK: 3149240587, BANK NTT NO REK: 001.01.13.008211-3.</p>
    <p style="font-size: 9px">KETERANGAN : {{ $jual->keterangan_faktur }}</p>
  </div>
@endsection
