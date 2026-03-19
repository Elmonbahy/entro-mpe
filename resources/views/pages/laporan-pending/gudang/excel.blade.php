<table>
  <!-- Header Row -->
  <thead>
    <tr>
      <th>Nomor Faktur</th>
      <th>Tanggal Faktur</th>
      <th>Pelanggan</th>
      <th>Sales</th>
      <th>Status Kirim</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($data as $faktur)
      <tr>
        <td>{{ $faktur['nomor_faktur'] }}</td>
        <td>{{ $faktur['tgl_faktur'] }} </td>
        <td>{{ $faktur['pelanggan_nama'] }} </td>
        <td>{{ $faktur['sales'] }}</td>
        <td>{{ $faktur['status_kirim'] }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
