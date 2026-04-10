@php
  $navigation = [
      [
          'title' => 'Komersil',
          'icon' => 'bi bi-file-text',
          'items' => [
              ['title' => 'Faktur Masuk', 'route' => 'pajak.beli.index'],
              ['title' => 'Faktur Keluar', 'route' => 'pajak.jual.index'],
          ],
      ],
  ];

@endphp

<x-sidebar-menu :menu="$navigation" />
