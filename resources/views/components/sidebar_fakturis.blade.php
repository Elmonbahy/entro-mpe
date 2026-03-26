@php
  $navigation = [
      [
          'title' => 'Master Data',
          'icon' => 'bi bi-database',
          'items' => [
              ['title' => 'Data Barang', 'route' => 'fakturis.barang.index'],
              ['title' => 'Data Brand', 'route' => 'fakturis.brand.index'],
              ['title' => 'Data Group', 'route' => 'fakturis.group.index'],
              ['title' => 'Data Pelanggan', 'route' => 'fakturis.pelanggan.index'],
              ['title' => 'Data Salesman', 'route' => 'fakturis.salesman.index'],
              ['title' => 'Data Supplier', 'route' => 'fakturis.supplier.index'],
          ],
      ],
      [
          'title' => 'Transaksi',
          'icon' => 'bi bi-cart3',
          'items' => [
              ['title' => 'Pembelian', 'route' => 'fakturis.beli.index'],
              ['title' => 'Penjualan', 'route' => 'fakturis.jual.index'],
          ],
      ],
      [
          'title' => 'Stock',
          'icon' => 'bi bi-box-seam',
          'items' => [
              // ['title' => 'Mutasi Stock', 'route' => '#'],
              ['title' => 'Kartu Stock', 'route' => 'fakturis.mutation.kartu-stock'],
              ['title' => 'Stock Barang', 'route' => 'fakturis.stock.index'],
              ['title' => 'Penyesuaian', 'route' => 'fakturis.stock-awal.index'],
              ['title' => 'Barang Rusak', 'route' => 'fakturis.barang-rusak.index'],
              ['title' => 'Mutasi', 'route' => 'fakturis.mutation.index'],
              ['title' => 'Persediaan', 'route' => 'fakturis.persediaan.index'],
          ],
      ],
      [
          'title' => 'Surat Jalan',
          'icon' => '"bi bi-card-text',
          'route' => 'fakturis.surat-jalan.index',
      ],
      [
          'title' => 'Laporan Beli',
          'icon' => 'bi bi-file-earmark',
          'items' => [
              ['title' => 'Faktur', 'route' => 'fakturis.laporan-beli.index'],
              ['title' => 'List Faktur', 'route' => 'fakturis.laporan-list-faktur-beli.index'],
          ],
      ],
      [
          'title' => 'Laporan Jual',
          'icon' => 'bi bi-file-earmark',
          'items' => [
              //   ['title' => 'Profit', 'route' => 'fakturis.laporan-jual.index'],
              ['title' => 'Faktur', 'route' => 'fakturis.laporan-jual-faktur.index'],
              ['title' => 'List Faktur', 'route' => 'fakturis.laporan-list-faktur-jual.index'],
          ],
      ],
      [
          'title' => 'Laporan Lainya',
          'icon' => 'bi bi-file-earmark',
          'items' => [['title' => 'Slow Moving', 'route' => 'fakturis.slow-moving.index']],
      ],
  ];

@endphp

<x-sidebar-menu :menu="$navigation" />
