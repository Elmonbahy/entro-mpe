@php
  $navigation = [
      [
          'title' => 'Master Data',
          'icon' => 'bi bi-database',
          'items' => [
              ['title' => 'Data Barang', 'route' => 'fakturis.barang.index'],
              ['title' => 'Data Brand', 'route' => 'fakturis.brand.index'],
              ['title' => 'Data Group', 'route' => 'fakturis.group.index'],
              ['title' => 'Data Pelanggan', 'route' => 'fakturis.pelanggan.index'],
              ['title' => 'Data Salesman', 'route' => 'fakturis.salesman.index'],
              ['title' => 'Data Supplier', 'route' => 'fakturis.supplier.index'],
          ],
      ],
      [
          'title' => 'Transaksi',
          'icon' => 'bi bi-cart3',
          'items' => [
              // ['title' => 'SP Pembelian', 'route' => 'fakturis.spbeli.index'],
              // ['title' => 'SP Penjualan', 'route' => 'fakturis.spjual.index'],
              ['title' => 'Pembelian', 'route' => 'fakturis.beli.index'],
              ['title' => 'Penjualan', 'route' => 'fakturis.jual.index'],
          ],
      ],
      [
          'title' => 'Stock',
          'icon' => 'bi bi-box-seam',
          'items' => [
              // ['title' => 'Mutasi Stock', 'route' => '#'],
              ['title' => 'Kartu Stock', 'route' => 'fakturis.mutation.kartu-stock'],
              ['title' => 'Stock Barang', 'route' => 'fakturis.stock.index'],
              ['title' => 'Penyesuaian', 'route' => 'fakturis.stock-awal.index'],
              ['title' => 'Barang Rusak', 'route' => 'fakturis.barang-rusak.index'],
              ['title' => 'Mutasi', 'route' => 'fakturis.mutation.index'],
              ['title' => 'Persediaan', 'route' => 'fakturis.persediaan.index'],
          ],
      ],
      [
          'title' => 'Surat Jalan',
          'icon' => '"bi bi-card-text',
          'route' => 'fakturis.surat-jalan.index',
      ],
      [
          'title' => 'Laporan Beli',
          'icon' => 'bi bi-file-earmark',
          'items' => [
              ['title' => 'Faktur', 'route' => 'fakturis.laporan-beli.index'],
              ['title' => 'List Faktur', 'route' => 'fakturis.laporan-list-faktur-beli.index'],
          ],
      ],
      [
          'title' => 'Laporan Jual',
          'icon' => 'bi bi-file-earmark',
          'items' => [
              ['title' => 'Profit', 'route' => 'fakturis.laporan-jual.index'],
              ['title' => 'Faktur', 'route' => 'fakturis.laporan-jual-faktur.index'],
              ['title' => 'List Faktur', 'route' => 'fakturis.laporan-list-faktur-jual.index'],
          ],
      ],
      [
          'title' => 'Laporan Lainya',
          'icon' => 'bi bi-file-earmark',
          'items' => [['title' => 'Slow Moving', 'route' => 'fakturis.slow-moving.index']],
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
