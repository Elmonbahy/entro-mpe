@php
  $navigation = [
      [
          'title' => 'Master Data',
          'icon' => 'bi bi-database',
          'items' => [['title' => 'Data Barang', 'route' => 'warehouse.barang.index']],
      ],
      [
          'title' => 'Transaksi',
          'icon' => 'bi bi-cart3',
          'items' => [
              ['title' => 'Barang Masuk', 'route' => 'warehouse.beli.index'],
              ['title' => 'Barang Keluar', 'route' => 'warehouse.jual.index'],
          ],
      ],
      [
          'title' => 'Surat Jalan',
          'icon' => '"bi bi-card-text',
          'route' => 'warehouse.surat-jalan.index',
      ],
      // [
      //     'title' => 'Stock',
      //     'icon' => 'bi bi-box-seam',
      //     'items' => [
      //         ['title' => 'Kartu Stock', 'route' => 'gudang.mutation.kartu-stock'],
      //         ['title' => 'Stock Barang', 'route' => 'gudang.stock.index'],
      //         ['title' => 'Barang Expired', 'route' => 'gudang.barang-expired.index'],
      //         ['title' => 'Barang Rusak', 'route' => 'gudang.barang-rusak.index'],
      //         ['title' => 'Mutasi', 'route' => 'gudang.mutation.index'],
      //     ],
      // ],
      // [
      //     'title' => 'Cetak',
      //     'icon' => 'bi bi-printer-fill',
      //     'items' => [['title' => 'Surat Jalan', 'route' => 'gudang.surat-jalan.index']],
      // ],
      // [
      //     'title' => 'Laporan',
      //     'icon' => 'bi bi-file-earmark',
      //     'items' => [
      //         ['title' => 'Barang Masuk', 'route' => 'gudang.laporan-beli.index'],
      //         ['title' => 'Barang Keluar', 'route' => 'gudang.laporan-jual.index'],
      //         ['title' => 'Pengiriman', 'route' => 'gudang.laporan-pengiriman.index'],
      //         ['title' => 'Pending', 'route' => 'gudang.laporan-pending.index'],
      //     ],
      // ],
  ];

@endphp

<li class="nav-title">Main menu</li>

@foreach ($navigation as $nav)
  @if (isset($nav['items']))
    {{-- Menu dropdown --}}
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
  @else
    {{-- Menu langsung --}}
    <li class="nav-item">
      <a class="nav-link" href="{{ $nav['route'] === '#' ? '#' : route($nav['route']) }}">
        <span class="nav-icon">
          <i class="{{ $nav['icon'] }}"></i>
        </span>
        {{ $nav['title'] }}
      </a>
    </li>
  @endif
@endforeach
