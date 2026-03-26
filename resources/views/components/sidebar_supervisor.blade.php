@php
  $navigation = [
      [
          'title' => 'Work Progress',
          'icon' => 'bi bi-activity',
          'route' => 'supervisor.work-progres.index',
      ],
      [
          'title' => 'Master Data',
          'icon' => 'bi bi-database',
          'items' => [
              ['title' => 'Data Barang', 'route' => 'supervisor.barang.index'],
              ['title' => 'Data Brand', 'route' => 'supervisor.brand.index'],
              ['title' => 'Data Group', 'route' => 'supervisor.group.index'],
              ['title' => 'Data Pelanggan', 'route' => 'supervisor.pelanggan.index'],
              ['title' => 'Data Salesman', 'route' => 'supervisor.salesman.index'],
              ['title' => 'Data Supplier', 'route' => 'supervisor.supplier.index'],
              ['title' => 'Data User', 'route' => 'supervisor.user.index'],
          ],
      ],
      [
          'title' => 'Transaksi',
          'icon' => 'bi bi-cart3',
          'items' => [
              ['title' => 'Pembelian', 'route' => 'supervisor.beli.index'],
              ['title' => 'Penjualan', 'route' => 'supervisor.jual.index'],
          ],
      ],
      [
          'title' => 'Stock',
          'icon' => 'bi bi-box-seam',
          'items' => [
              ['title' => 'Kartu Stock', 'route' => 'supervisor.mutation.kartu-stock'],
              ['title' => 'Stock Barang', 'route' => 'supervisor.stock.index'],
              ['title' => 'Mutasi', 'route' => 'supervisor.mutation.index'],
          ],
      ],
      [
          'title' => 'Surat Jalan',
          'icon' => '"bi bi-card-text',
          'route' => 'supervisor.surat-jalan.index',
      ],
  ];

@endphp

<x-sidebar-menu :menu="$navigation" />
