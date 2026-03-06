<table>
  <!-- Header Row -->
  <thead>
    <tr>
      <th>Nomor Surat Jalan</th>
      <th>Tanggal Surat Jalan</th>
      <th>Nomor Faktur</th>
      <th>Tanggal Faktur</th>
      <th>Pelanggan</th>
      <th>Sales</th>
      <th>Status Kirim</th>
      <th>Rayon</th>
      <th>Nama PJ</th>
      <th>Kendaraan</th>
      <th>Koli</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($data as $index => $row)
      <tr>
        <td>{{ $row['nomor_surat_jalan'] }}</td>
        <td>{{ $row['tgl_surat_jalan'] }}</td>
        <td>{{ $row['nomor_faktur'] }}</td>
        <td>{{ $row['tgl_faktur'] }}</td>
        <td>{{ $row['pelanggan'] }}</td>
        <td>{{ $row['sales'] }}</td>
        <td>{{ $row['status_kirim'] }}</td>
        <td>{{ $row['rayon'] }}</td>
        <td>{{ $row['staf_logistik'] }}</td>
        <td>{{ $row['kendaraan'] }}</td>
        <td>{{ $row['koli'] }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
