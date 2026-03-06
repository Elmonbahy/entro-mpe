<!DOCTYPE html><!--
* CoreUI - Free Bootstrap Admin Template
* @version v5.1.1
* @link https://coreui.io/product/free-bootstrap-admin-template/
* Copyright (c) 2024 creativeLabs Łukasz Holeczek
* Licensed under MIT (https://github.com/coreui/coreui-free-bootstrap-admin-template/blob/main/LICENSE)
-->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" <head>
{{-- Here we tell the search engine to not crawl this site --}}
<meta name="robots" content="noindex, nofollow">

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

<title>APM Web</title>

@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
  {{-- Load coreui bundle --}}
  @vite(['resources/vendor/coreui/css/coreui.min.css', 'resources/vendor/coreui/js/coreui.bundle.min.js', 'resources/vendor/coreui/js/color-modes.js'])
  {{-- Load app bundle  --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endif

{{-- Allow additional styles --}}
@stack('styles')
</head>

<body>
  <div class="bg-body-tertiary min-vh-100 d-flex flex-row align-items-center">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6">
          @yield('content')
        </div>
      </div>
    </div>
  </div>

  {{-- Additional scripts  --}}
  @stack('scripts')
</body>

</html>
