@php
  $navigation = [
      [
          'title' => 'Master Data',
          'icon' => 'bi bi-database',
          'items' => [
              ['title' => 'Data Barang', 'route' => 'gudang.barang.index'],
              ['title' => 'Data Pelanggan', 'route' => 'gudang.pelanggan.index'],
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
          'title' => 'Retur',
          'icon' => 'bi bi-cart3',
          'items' => [
              ['title' => 'Barang Masuk', 'route' => 'gudang.retur.beli'],
              ['title' => 'Barang Keluar', 'route' => 'gudang.retur.jual'],
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
          ],
      ],
  ];

@endphp

<x-sidebar-menu :menu="$navigation" />
