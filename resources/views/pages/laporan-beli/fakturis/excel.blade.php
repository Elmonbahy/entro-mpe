<table>
  <!-- Header Row -->
  <thead>
    <tr>
      <th>Supplier</th>
      <th>Nomor Faktur</th>
      <th>Tanggal Faktur</th>
      <th>Tanggal Terima</th>
      <th>No</th>
      <th>Nama Barang</th>
      <th>Jumlah</th>
      <th>Satuan</th>
      <th>Harga Beli</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($data as $faktur)
      @foreach ($faktur['beli_details'] as $item)
        <tr>
          <td>
            {{ $faktur['supplier_nama'] }}
          </td>
          <td>
            '{{ $faktur['nomor_faktur'] }}'
          </td>
          <td>
            {{ $faktur['tgl_faktur'] }}
          </td>
          <td>
            {{ $faktur['tgl_terima_faktur'] }}
          </td>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item['barang_nama'] }}</td>
          <td>{{ $item['jumlah_barang_masuk'] }}</td>
          <td>{{ $item['barang_satuan'] }}</td>
          <td>{{ $item['harga_beli'] }}</td>
        </tr>
      @endforeach
    @endforeach
  </tbody>
</table>
