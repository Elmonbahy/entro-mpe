@php
  $navigation = [
      [
          'title' => 'Master Data',
          'icon' => 'bi bi-database',
          'items' => [
              ['title' => 'Data User', 'route' => 'user.index'],
              ['title' => 'Data Barang', 'route' => 'superadmin.barang.index'],
              ['title' => 'Data Brand', 'route' => 'superadmin.brand.index'],
              ['title' => 'Data Group', 'route' => 'superadmin.group.index'],
              ['title' => 'Data Pelanggan', 'route' => 'superadmin.pelanggan.index'],
              ['title' => 'Data Salesman', 'route' => 'superadmin.salesman.index'],
              ['title' => 'Kontrol Akses', 'route' => 'superadmin.manage-access.index'],
          ],
      ],
  ];

@endphp

<x-sidebar-menu :menu="$navigation" />
