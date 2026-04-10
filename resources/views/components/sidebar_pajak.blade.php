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
      [
          'title' => 'Surat Jalan',
          'icon' => '"bi bi-card-text',
          'route' => 'pajak.surat-jalan.index',
      ],
  ];

@endphp

<x-sidebar-menu :menu="$navigation" />
