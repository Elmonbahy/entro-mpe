@php
  $navigation = [
      [
          'title' => 'Master Data',
          'icon' => 'bi bi-database',
          'items' => [
              ['title' => 'Data Pelanggan', 'route' => 'keuangan.pelanggan.index'],
              ['title' => 'Data Supplier', 'route' => 'keuangan.supplier.index'],
          ],
      ],
      [
          'title' => 'Transaksi',
          'icon' => 'bi bi-cash',
          'items' => [
              ['title' => 'Pembelian', 'route' => 'keuangan.beli.index'],
              ['title' => 'Penjualan', 'route' => 'keuangan.jual.index'],
          ],
      ],
      [
          'title' => 'Laporan Beli',
          'icon' => 'bi bi-file-earmark',
          'items' => [
              ['title' => 'Faktur', 'route' => 'keuangan.laporan-beli.index'],
              ['title' => 'List Faktur', 'route' => 'keuangan.laporan-list-faktur-beli.index'],
          ],
      ],
      [
          'title' => 'Laporan Jual',
          'icon' => 'bi bi-file-earmark',
          'items' => [
              ['title' => 'Fakur', 'route' => 'keuangan.laporan-jual-faktur.index'],
              ['title' => 'List Fakur', 'route' => 'keuangan.laporan-list-faktur-jual.index'],
          ],
      ],
      [
          'title' => 'Cetak',
          'icon' => 'bi bi-printer-fill',
          'items' => [['title' => 'Titip Faktur', 'route' => 'keuangan.titip-faktur.index']],
      ],
  ];

@endphp

<x-sidebar-menu :menu="$navigation" />
