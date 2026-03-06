@php
  $navigation = [
      [
          'title' => 'Master Data',
          'icon' => 'bi bi-database',
          'items' => [['title' => 'Data Barang', 'route' => 'accounting.barang.index']],
      ],
      [
          'title' => 'Stock',
          'icon' => 'bi bi-box-seam',
          'items' => [
              ['title' => 'Kartu Stock', 'route' => 'accounting.mutation.kartu-stock'],
              ['title' => 'Penyesuaian', 'route' => 'accounting.stock-awal.index'],
              ['title' => 'Barang Rusak', 'route' => 'accounting.barang-rusak.index'],
          ],
      ],
      [
          'title' => 'Transaksi',
          'icon' => 'bi bi-cash',
          'items' => [
              ['title' => 'Pembelian', 'route' => 'accounting.beli.index'],
              ['title' => 'Penjualan', 'route' => 'accounting.jual.index'],
          ],
      ],
      [
          'title' => 'Surat Jalan',
          'icon' => '"bi bi-card-text',
          'route' => 'accounting.surat-jalan.index',
      ],
      [
          'title' => 'Laporan Beli',
          'icon' => 'bi bi-file-earmark',
          'items' => [
              ['title' => 'Faktur', 'route' => 'accounting.laporan-beli.index'],
              ['title' => 'List Faktur', 'route' => 'accounting.laporan-list-faktur-beli.index'],
          ],
      ],
      [
          'title' => 'Laporan Jual',
          'icon' => 'bi bi-file-earmark',
          'items' => [
              ['title' => 'Faktur', 'route' => 'accounting.laporan-jual-faktur.index'],
              ['title' => 'List Fakur', 'route' => 'accounting.laporan-list-faktur-jual.index'],
          ],
      ],
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
