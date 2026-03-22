@php
  $navigation = [
      [
          'title' => 'Master Data',
          'icon' => 'bi bi-database',
          'items' => [
              ['title' => 'Data Pelanggan', 'route' => 'logistik.pelanggan.index'],
              'items' => ['title' => 'Data Kendaraan', 'route' => 'logistik.kendaraan.index'],
          ],
      ],
      [
          'title' => 'Transaksi',
          'icon' => 'bi bi-cart3',
          'items' => [
              ['title' => 'Barang masuk', 'route' => 'logistik.beli.index'],
              ['title' => 'Barang keluar', 'route' => 'logistik.jual.index'],
          ],
      ],
      [
          'title' => 'Cetak',
          'icon' => 'bi bi-printer-fill',
          'items' => [['title' => 'Surat Jalan', 'route' => 'logistik.surat-jalan.index']],
      ],
  ];

@endphp

<x-sidebar-menu :menu="$navigation" />
