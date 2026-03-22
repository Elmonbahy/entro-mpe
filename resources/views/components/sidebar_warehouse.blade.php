@php
  $navigation = [
      [
          'title' => 'Master Data',
          'icon' => 'bi bi-database',
          'items' => [['title' => 'Data Barang', 'route' => 'warehouse.barang.index']],
      ],
      [
          'title' => 'Transaksi',
          'icon' => 'bi bi-cart3',
          'items' => [
              ['title' => 'Barang Masuk', 'route' => 'warehouse.beli.index'],
              ['title' => 'Barang Keluar', 'route' => 'warehouse.jual.index'],
          ],
      ],
      [
          'title' => 'Surat Jalan',
          'icon' => '"bi bi-card-text',
          'route' => 'warehouse.surat-jalan.index',
      ],
  ];

@endphp

<x-sidebar-menu :menu="$navigation" />
