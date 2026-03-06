<table>
  <!-- Header Row -->
  <thead>
    <tr>
      <th>Supplier</th>
      <th>Nomor Faktur</th>
      <th>Tanggal Faktur</th>
      <th>Tanggal Terima</th>
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
      <th>Harga Beli</th>
      <th>Total</th>
      <th>DPP</th>
      <th>PPN</th>
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
            {{ $faktur['status_bayar_label'] }}
          </td>
          <td>
            {{ $faktur['tipe_bayar'] ?? '-' }}
          </td>
          <td>
            {{ $faktur['tgl_bayar'] ?? '-' }}
          </td>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item['barang_nama'] }}</td>
          <td>{{ $item['jumlah_barang_masuk'] }}</td>
          <td>{{ $item['barang_satuan'] }}</td>
          <td>{{ $item['diskon1'] }}</td>
          <td>{{ $item['harga_diskon1'] }}</td>
          <td>{{ $item['diskon2'] }}</td>
          <td>{{ $item['harga_diskon2'] }}</td>
          <td>{{ $item['harga_beli'] }}</td>
          <td>{{ $item['total_tagihan'] }}</td>
          <td>{{ $item['dpp'] }}</td>
          <td>{{ $item['harga_ppn'] }}</td>
        </tr>
      @endforeach
    @endforeach
  </tbody>
</table>
