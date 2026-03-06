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

<li class="nav-title">Main menu</li>

@foreach ($navigation as $nav)
  <li class="nav-group">
    <a class="nav-link nav-group-toggle" href="#">
      <span class="nav-icon">
        <i class="{{ $nav['icon'] }}"></i>
      </span>
      {{ $nav['title'] }}
    </a>
    <ul class="nav-group-items compact">
      @foreach ($nav['items'] as $item)
        <li class="nav-item">
          <a class="nav-link" href="{{ $item['route'] === '#' ? '#' : route($item['route']) }}">
            <span class="nav-icon">
              <span class="nav-icon-bullet"></span>
            </span>
            {{ $item['title'] }}
          </a>
        </li>
      @endforeach
    </ul>
  </li>
@endforeach
