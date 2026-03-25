@php
  $navigation = [
      [
          'title' => 'Master Data',
          'icon' => 'bi bi-database',
          'items' => [
              ['title' => 'Data User', 'route' => 'user.index'],
              ['title' => 'Kontrol Akses', 'route' => 'superadmin.manage-access.index'],
          ],
      ],
  ];

@endphp

<x-sidebar-menu :menu="$navigation" />
