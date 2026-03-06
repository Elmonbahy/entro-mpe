<table class="table table-bordered small">
  <!-- Header Row -->
  <thead class="text-nowrap">
    <tr>
      <th>Brand</th>
      <th>Id Barang</th>
      <th>Nama Barang</th>
      <th>Satuan</th>
      <th>Stock Ahkir</th>
      <th>total Stok Masuk</th>
      <th>total Harga Beli</th>
      <th>Harga Rata-rata</th>
      <th>Nilai Persediaan</th>
    </tr>
  </thead>

  <tbody>
    @foreach ($data as $item)
      <tr>
        <td>
          {{ $item['brand'] }}
        </td>
        <td>
          {{ $item['barang_id'] }}
        </td>
        <td>
          {{ $item['barang_nama'] }}
        </td>
        <td>
          {{ $item['satuan'] }}
        </td>
        <td>
          {{ $item['stock_akhir'] }}
        </td>
        <td>
          {{ $item['total_stock_masuk'] }}
        </td>
        <td>
          {{ $item['total_harga_beli'] }}
        </td>
        <td>
          {{ $item['hpp_avg'] }}
        </td>
        <td>
          {{ $item['nilai_persedian'] }}
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
