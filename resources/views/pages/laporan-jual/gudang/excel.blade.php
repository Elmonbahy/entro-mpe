<table>
  <!-- Header Row -->
  <thead>
    <tr>
      <th>Pelanggan</th>
      <th>Nomor Faktur</th>
      <th>Tanggal Faktur</th>
      <th>Status Faktur</th>
      <th>No</th>
      <th>Brand</th>
      <th>Barang</th>
      <th>Jumlah Pesan</th>
      <th>Jumlah Keluar</th>
      <th>Satuan</th>
      <th>Status Barang</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($data as $faktur)
      @foreach ($faktur['jual_details'] as $item)
        <tr>
          <td>
            {{ $faktur['pelanggan_nama'] }}
          </td>
          <td>
            {{ $faktur['nomor_faktur'] }}
          </td>
          <td>
            {{ $faktur['tgl_faktur'] }}
          </td>
          <td>
            {{ $faktur['status_faktur_label'] }}
          </td>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item['brand_nama'] }}</td>
          <td>{{ $item['barang_nama'] }}</td>
          <td>{{ $item['jumlah_barang_dipesan'] }}</td>
          <td>{{ $item['jumlah_barang_keluar'] }}</td>
          <td>{{ $item['barang_satuan'] }}</td>
          <td>{{ $item['status_barang_keluar_label'] }}</td>
        </tr>
      @endforeach
    @endforeach
  </tbody>
</table>
