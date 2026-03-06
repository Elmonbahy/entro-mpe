<html>


<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="{{ asset('css/normalize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pdf.css') }}">
  @stack('styles')
</head>

<body>
  @yield('content')
</body>

</html>
