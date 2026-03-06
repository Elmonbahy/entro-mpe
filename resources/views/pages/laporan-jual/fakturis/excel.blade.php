<table class="table table-bordered small">
  <!-- Header Row -->
  <thead class="text-nowrap">
    <tr>
      <th>Pelanggan</th>
      <th>Nomor Faktur</th>
      <th>Tanggal Faktur</th>
      <th>No</th>
      <th>Nama Barang</th>
      <th>Jumlah</th>
      <th>Disc</th>
      <th>DiscRp</th>
      <th>Disc2</th>
      <th>Disc2Rp</th>
      <th>Total</th>
      <th>HNA Jual</th>
      <th>PPN Jual</th>
      <th>HNA Beli</th>
      <th>PPN Beli</th>
      <th>Profit</th>
      <th>%</th>
      <th>Ongkir</th>
      <th>Net Profit</th>
    </tr>
  </thead>

  <tbody>
    @foreach ($data as $faktur)
      @php
        $rowspan = $faktur['jual_details_count'] + 2;
      @endphp

      {{-- Start jual --}}
      <tr>
        {{-- rowspan 4: total jual detail + 2 --}}
        <td rowspan="{{ $rowspan }}">
          {{ $faktur['pelanggan_nama'] }}
        </td>
        <td rowspan="{{ $rowspan }}">
          {{ $faktur['nomor_faktur'] }}
        </td>
        <td rowspan="{{ $rowspan }}">
          {{ $faktur['tgl_faktur'] }}
        </td>
        <td colspan="13">
          {{ $faktur['tipe_penjualan'] }}
        </td>
        <td rowspan="{{ $faktur['jual_details_count'] + 1 }}">{{ number_format($faktur['persen'], 0, ',', '.') }}%</td>
        <td rowspan="{{ $rowspan }}">{{ $faktur['ongkir'] }}</td>
        <td rowspan="{{ $rowspan }}"><strong>{{ $faktur['net_profit'] }}</strong></td>
      </tr>
      {{-- end jual --}}

      {{-- start jual detail --}}
      @foreach ($faktur['jual_details'] as $item)
        <tr>
          <td>{{ $loop->iteration }}</td> {{-- nomor urut --}}
          <td>{{ $item['barang_nama'] }}</td> {{-- nama barang --}}
          <td>{{ $item['jumlah_barang_keluar'] }}</td> {{-- jumlah barang keluar --}}
          <td>{{ $item['diskon1'] }}</td> {{-- diskon 1 --}}
          <td>{{ $item['harga_diskon1'] }}</td> {{-- harga diskon 1  --}}
          <td>{{ $item['diskon2'] }}</td> {{-- diskon 2 --}}
          <td>{{ $item['harga_diskon2'] }}</td> {{-- harga diskon 2 --}}
          <td>{{ $item['total_tagihan'] }}</td> {{-- tagihan --}}
          <td>{{ $item['hna_jual'] }}</td> {{-- HNA jual --}}
          <td>{{ $item['ppn_jual'] }}</td> {{-- PPN jual --}}
          <td>{{ $item['hna_beli'] }}</td> {{-- HNA  beli --}}
          <td>{{ $item['ppn_beli'] }}</td> {{-- PPN beli --}}
          <td>{{ $item['profit'] }}</td> {{-- profit --}}
        </tr>
      @endforeach
      {{-- end jual detail --}}

      {{-- start total jual detail --}}
      <tr>
        <td colspan="7"><strong>Total</strong></td>
        <td><strong>{{ $faktur['total_tagihan'] }}</strong></td>
        <td><strong>{{ $faktur['total_hna_jual'] }}</strong></td>
        <td><strong>{{ $faktur['total_ppn_jual'] }}</strong></td>
        <td><strong>{{ $faktur['total_hna_beli'] }}</strong></td>
        <td><strong>{{ $faktur['total_ppn_beli'] }}</strong></td>
        <td><strong>{{ $faktur['total_profit'] }}</strong></td>

      </tr>

      {{-- end total jual detail --}}
    @endforeach
  </tbody>
</table>
