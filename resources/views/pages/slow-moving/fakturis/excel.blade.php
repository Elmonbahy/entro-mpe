<table>
  <!-- Header Row -->
  <thead>
    <tr>
      <th>Brand</th>
      <th>Nama Barang</th>
      <th>Pembelian</th>
      <th>Penjualan</th>
      <th>Satuan</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    @forelse($data as $item)
      <tr>
        <td>{{ $item['brand'] }}</td>
        <td>{{ $item['nama'] }}</td>
        <td>{{ $item['total_pembelian'] }}</td>
        <td>{{ $item['total_penjualan'] }}</td>
        <td>{{ $item['satuan'] }}</td>
        <td>{{ $item['status'] }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="6" class="text-center">Tidak ada data</td>
      </tr>
    @endforelse
  </tbody>
</table>
