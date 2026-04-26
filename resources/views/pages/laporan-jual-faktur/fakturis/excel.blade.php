<table class="table table-bordered small">
  <!-- Header Row -->
  <thead class="text-nowrap">
    <tr>
      <th>Pelanggan</th>
      <th>Nomor Faktur</th>
      <th>Sales</th>
      <th>Tanggal</th>
      <th>No</th>
      <th>Nama Barang</th>
      <th>Jumlah</th>
      <th>Satuan</th>
      <th>Harga Jual</th>
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
          <td>{{ $index + 1 }}</td> {{-- nomor urut --}}
          <td>{{ $item['barang_nama'] }}</td> {{-- nama barang --}}
          <td>{{ $item['jumlah_barang_keluar'] }}</td> {{-- jumlah barang keluar --}}
          <td>{{ $item['barang_satuan'] }}</td> {{-- nama barang --}}
          <td>{{ $item['harga_jual'] }}</td> {{-- harga jual --}}
        </tr>
      @endforeach
    @endforeach
  </tbody>
</table>
