@php
  $navigation = [
      [
          'title' => 'Master Data',
          'icon' => 'bi bi-database',
          'items' => [['title' => 'Data User', 'route' => 'user.index']],
      ],
  ];

@endphp

<x-sidebar-menu :menu="$navigation" />
