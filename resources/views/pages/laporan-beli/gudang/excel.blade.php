<table>
  <!-- Header Row -->
  <thead>
    <tr>
      <th>Supplier</th>
      <th>Nomor Faktur</th>
      <th>Tanggal Faktur</th>
      <th>Tanggal Terima</th>
      <th>Status Faktur</th>
      <th>No</th>
      <th>Brand</th>
      <th>Barang</th>
      <th>Jumlah Pesan</th>
      <th>Jumlah Masuk</th>
      <th>Satuan</th>
      <th>Status Barang</th>
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
          <td>
            {{ $faktur['status_faktur_label'] }}
          </td>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item['brand_nama'] }}</td>
          <td>{{ $item['barang_nama'] }}</td>
          <td>{{ $item['jumlah_barang_dipesan'] }}</td>
          <td>{{ $item['jumlah_barang_masuk'] }}</td>
          <td>{{ $item['barang_satuan'] }}</td>
          <td>{{ $item['status_barang_masuk_label'] }}</td>
        </tr>
      @endforeach
    @endforeach
  </tbody>
</table>
