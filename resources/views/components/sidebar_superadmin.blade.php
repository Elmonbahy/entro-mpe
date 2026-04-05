@php
  $navigation = [
      [
          'title' => 'Master Data',
          'icon' => 'bi bi-database',
          'items' => [
              ['title' => 'Data User', 'route' => 'user.index'],
              ['title' => 'Data Barang', 'route' => 'superadmin.barang.index'],
              ['title' => 'Kontrol Akses', 'route' => 'superadmin.manage-access.index'],
          ],
      ],
  ];

@endphp

<x-sidebar-menu :menu="$navigation" />
