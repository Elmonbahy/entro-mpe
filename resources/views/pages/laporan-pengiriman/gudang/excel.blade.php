<table>
  <!-- Header Row -->
  <thead>
    <tr>
      <th>Nomor Surat Jalan</th>
      <th>Tanggal Surat Jalan</th>
      <th>Pelanggan</th>
      <th>Kendaraan</th>
      <th>Koli</th>
      <th>Nama PJ</th>
      <th>No</th>
      <th>Nomor Faktur</th>
      <th>Status Kirim</th>
      <th>Tanggal faktur</th>
      <th>Brand</th>
      <th>Nama Barang</th>
      <th>Jumlah Keluar</th>
      <th>Jumlah kirim</th>
      <th>Satuan</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($data as $surat_jalan)
      @foreach ($surat_jalan['sj_details'] as $item)
        <tr>
          <td>
            {{ $surat_jalan['nomor_surat_jalan'] }}
          </td>
          <td>
            {{ $surat_jalan['tgl_surat_jalan'] }}
          </td>
          <td>
            {{ $surat_jalan['pelanggan_nama'] }}
          </td>
          <td>
            {{ $surat_jalan['kendaraan'] }}
          </td>
          <td>
            {{ $surat_jalan['koli'] }}
          </td>
          <td>
            {{ $surat_jalan['staf_logistik'] }}
          </td>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item['nomor_faktur'] }}</td>
          <td>{{ $item['status_kirim'] }}</td>
          <td>{{ $item['tgl_faktur'] }}</td>
          <td>{{ $item['brand'] }}</td>
          <td>{{ $item['barang_nama'] }}</td>
          <td>{{ $item['jumlah_barang_keluar'] }}</td>
          <td>{{ $item['jumlah_barang_dikirim'] }}</td>
          <td>{{ $item['satuan'] }}</td>
        </tr>
      @endforeach
    @endforeach
  </tbody>
</table>
