@php
  $navigation = [
      [
          'title' => 'Master Data',
          'icon' => 'bi bi-database',
          'items' => [
              ['title' => 'Data Barang', 'route' => 'supervisor.barang.index'],
              ['title' => 'Data Brand', 'route' => 'supervisor.brand.index'],
              ['title' => 'Data Group', 'route' => 'supervisor.group.index'],
              ['title' => 'Data Pelanggan', 'route' => 'supervisor.pelanggan.index'],
              ['title' => 'Data Salesman', 'route' => 'supervisor.salesman.index'],
              ['title' => 'Data Supplier', 'route' => 'supervisor.supplier.index'],
          ],
      ],
      [
          'title' => 'Transaksi',
          'icon' => 'bi bi-cart3',
          'items' => [
              ['title' => 'Pembelian', 'route' => 'supervisor.beli.index'],
              ['title' => 'Penjualan', 'route' => 'supervisor.jual.index'],
          ],
      ],
      [
          'title' => 'Stock',
          'icon' => 'bi bi-box-seam',
          'items' => [
              ['title' => 'Kartu Stock', 'route' => 'supervisor.mutation.kartu-stock'],
              ['title' => 'Stock Barang', 'route' => 'supervisor.stock.index'],
              ['title' => 'Mutasi', 'route' => 'supervisor.mutation.index'],
          ],
      ],
      [
          'title' => 'Surat Jalan',
          'icon' => '"bi bi-card-text',
          'route' => 'supervisor.surat-jalan.index',
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
