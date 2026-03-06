<table class="table table-bordered small">
  <!-- Header Row -->
  <thead class="text-nowrap">
    <tr>
      <th>Pelanggan</th>
      <th>Nomor Faktur</th>
      <th>Sales</th>
      <th>Tanggal</th>
      <th>Status Bayar</th>
      <th>Tipe Bayar</th>
      <th>Tanggal Bayar</th>
      <th>No</th>
      <th>Nama Barang</th>
      <th>Jumlah</th>
      <th>Satuan</th>
      <th>Disc</th>
      <th>DiscRp</th>
      <th>Disc2</th>
      <th>Disc2Rp</th>
      <th>Harga Jual</th>
      <th>Total</th>
      <th>DPP</th>
      <th>PPN</th>
    </tr>
  </thead>

  <tbody>
    @foreach ($data as $faktur)
      {{-- Looping jual details tanpa rowspan --}}
      @foreach ($faktur['jual_details'] as $index => $item)
        <tr>
          <td>{{ $faktur['pelanggan_nama'] }}</td> {{-- pelanggan --}}
          <td>{{ $faktur['nomor_faktur'] }}</td> {{-- faktur --}}
          <td>{{ $faktur['sales_nama'] }}</td> {{-- faktur --}}
          <td>{{ $faktur['tgl_faktur'] }}</td> {{-- tanggal --}}
          <td>{{ $faktur['status_bayar_label'] }}</td>
          <td>{{ $faktur['tipe_bayar'] }}</td>
          <td>{{ $faktur['tgl_bayar'] ?? '-' }}</td>
          <td>{{ $index + 1 }}</td> {{-- nomor urut --}}
          <td>{{ $item['barang_nama'] }}</td> {{-- nama barang --}}
          <td>{{ $item['jumlah_barang_keluar'] }}</td> {{-- jumlah barang keluar --}}
          <td>{{ $item['barang_satuan'] }}</td> {{-- nama barang --}}
          <td>{{ $item['diskon1'] }}</td> {{-- diskon 1 --}}
          <td>{{ $item['harga_diskon1'] }}</td> {{-- harga diskon 1 --}}
          <td>{{ $item['diskon2'] }}</td> {{-- diskon 2 --}}
          <td>{{ $item['harga_diskon2'] }}</td> {{-- harga diskon 2 --}}
          <td>{{ $item['harga_jual'] }}</td> {{-- harga jual --}}
          <td>{{ $item['total_tagihan'] }}</td> {{-- tagihan --}}
          <td>{{ $item['dpp'] }}</td> {{-- HNA jual --}}
          <td>{{ $item['harga_ppn'] }}</td> {{-- PPN jual --}}
        </tr>
      @endforeach
    @endforeach
  </tbody>
</table>
