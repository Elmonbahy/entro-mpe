@php
  $navigation = [
      [
          'title' => 'Master Data',
          'icon' => 'bi bi-database',
          'items' => [
              ['title' => 'Data Barang', 'route' => 'gudang.barang.index'],
              ['title' => 'Data Pelanggan', 'route' => 'gudang.pelanggan.index'],
              'items' => ['title' => 'Data Kendaraan', 'route' => 'gudang.kendaraan.index'],
          ],
      ],
      [
          'title' => 'Transaksi',
          'icon' => 'bi bi-cart3',
          'items' => [
              ['title' => 'Barang Masuk', 'route' => 'gudang.beli.index'],
              ['title' => 'Barang Keluar', 'route' => 'gudang.jual.index'],
          ],
      ],
      [
          'title' => 'Stock',
          'icon' => 'bi bi-box-seam',
          'items' => [
              ['title' => 'Kartu Stock', 'route' => 'gudang.mutation.kartu-stock'],
              ['title' => 'Stock Barang', 'route' => 'gudang.stock.index'],
              ['title' => 'Barang Expired', 'route' => 'gudang.barang-expired.index'],
              ['title' => 'Barang Rusak', 'route' => 'gudang.barang-rusak.index'],
              ['title' => 'Mutasi', 'route' => 'gudang.mutation.index'],
          ],
      ],
      [
          'title' => 'Cetak',
          'icon' => 'bi bi-printer-fill',
          'items' => [['title' => 'Surat Jalan', 'route' => 'gudang.surat-jalan.index']],
      ],
      [
          'title' => 'Laporan',
          'icon' => 'bi bi-file-earmark',
          'items' => [
              ['title' => 'Barang Masuk', 'route' => 'gudang.laporan-beli.index'],
              ['title' => 'Barang Keluar', 'route' => 'gudang.laporan-jual.index'],
              ['title' => 'Pengiriman', 'route' => 'gudang.laporan-pengiriman.index'],
              ['title' => 'List Pengiriman', 'route' => 'gudang.laporan-list-pengiriman.index'],
              ['title' => 'Pending', 'route' => 'gudang.laporan-pending.index'],
          ],
      ],
  ];

@endphp

<x-sidebar-menu :menu="$navigation" />
