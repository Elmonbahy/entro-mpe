<table>
  <!-- Header Row -->
  <thead>
    <tr>
      <th>Pelanggan</th>
      <th>Nomor Faktur</th>
      <th>Sales</th>
      <th>Area</th>
      <th>Tanggal Faktur</th>
      <th>Jatuh Tempo</th>
      <th>Status Bayar</th>
      <th>Tipe Bayar</th>
      <th>Tanggal Bayar</th>
      <th>Metode Bayar</th>
      <th>Pungut PPN</th>
      <th>Terbayar</th>
      <th>Total Tagihan</th>
      <th>Sisa Tagihan</th>
      <th>Total Faktur</th>
      <th>Total DPP</th>
      <th>Total PPN</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($data as $pelanggan)
      @foreach ($pelanggan['fakturs'] as $faktur)
        <tr>
          <td>{{ $pelanggan['pelanggan_nama'] }}</td>
          <td>{{ $faktur['nomor_faktur'] }}</td>
          <td>{{ $faktur['sales_nama'] }}</td>
          <td>{{ $faktur['area'] }}</td>
          <td>{{ $faktur['tgl_faktur'] }}</td>
          <td>{{ $faktur['jatuh_tempo'] }}</td>
          <td>{{ $faktur['status_bayar_label'] }}</td>
          <td>{{ $faktur['tipe_bayar'] }}</td>
          <td>{{ $faktur['tgl_bayar'] }}</td>
          <td>{{ $faktur['metode_bayar'] }}</td>
          <td>{{ $faktur['is_pungut_ppn'] }}</td>
          <td>'{{ $faktur['terbayar'] }}'</td>
          <td>{{ $faktur['total_tagihan'] }}</td>
          <td>{{ $faktur['sisa_tagihan'] }}</td>
          <td>{{ $faktur['total_faktur'] }}</td>
          <td>{{ $faktur['total_dpp'] }}</td>
          <td>{{ $faktur['total_ppn'] }}</td>
        </tr>
      @endforeach
    @endforeach
  </tbody>
</table>
