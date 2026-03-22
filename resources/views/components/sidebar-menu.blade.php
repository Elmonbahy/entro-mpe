@props(['menu'])

<li class="nav-title">Main menu</li>

@foreach ($menu as $nav)
  {{-- Cek apakah ini Dropdown atau Single Link --}}
  @if (isset($nav['items']))
    {{-- ======================= --}}
    {{-- LOGIC UNTUK DROPDOWN --}}
    {{-- ======================= --}}
    @php
      $isGroupActive = false;

      // Loop untuk cek apakah salah satu anak aktif
      foreach ($nav['items'] as $item) {
          $itemRoute = $item['route'];
          if ($itemRoute === '#') {
              continue;
          }

          // Tentukan prefix
          // Jika .index, buang akhiran. Jika tidak, pakai nama route utuh.
          if (str_ends_with($itemRoute, '.index')) {
              $baseResource = substr($itemRoute, 0, -6);
          } else {
              $baseResource = $itemRoute;
          }

          // Cek active state
          if (request()->routeIs($itemRoute) || request()->routeIs($baseResource . '.*')) {
              $isGroupActive = true;
              break;
          }
      }
    @endphp

    <li class="nav-group {{ $isGroupActive ? 'active open show' : '' }}">
      <a class="nav-link nav-group-toggle" href="#">
        <span class="nav-icon">
          <i class="{{ $nav['icon'] }}"></i>
        </span>
        {{ $nav['title'] }}
      </a>
      <ul class="nav-group-items compact">
        @foreach ($nav['items'] as $item)
          @php
            $childRoute = $item['route'];
            $isChildActive = false;

            if ($childRoute !== '#') {
                if (str_ends_with($childRoute, '.index')) {
                    $childBase = substr($childRoute, 0, -6);
                } else {
                    $childBase = $childRoute;
                }
                $isChildActive = request()->routeIs($childRoute) || request()->routeIs($childBase . '.*');
            }
          @endphp
          <li class="nav-item">
            <a class="nav-link {{ $isChildActive ? 'active' : '' }}"
              href="{{ $childRoute === '#' ? '#' : route($childRoute) }}">
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
    {{-- ======================= --}}
    {{-- LOGIC UNTUK SINGLE ITEM --}}
    {{-- ======================= --}}
    @php
      $singleRoute = $nav['route'];
      $isSingleActive = false;

      if ($singleRoute !== '#') {
          if (str_ends_with($singleRoute, '.index')) {
              $singleBase = substr($singleRoute, 0, -6);
          } else {
              $singleBase = $singleRoute;
          }
          $isSingleActive = request()->routeIs($singleRoute) || request()->routeIs($singleBase . '.*');
      }
    @endphp

    <li class="nav-item">
      <a class="nav-link {{ $isSingleActive ? 'active' : '' }}"
        href="{{ $singleRoute === '#' ? '#' : route($singleRoute) }}">
        <span class="nav-icon">
          <i class="{{ $nav['icon'] }}"></i>
        </span>
        {{ $nav['title'] }}
      </a>
    </li>
  @endif
@endforeach
