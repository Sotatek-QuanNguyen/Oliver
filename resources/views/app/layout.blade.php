<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>@yield('title')</title>
    <script src="js/jquery.min.js"></script>
    <link type="text/css" rel="stylesheet" href="css/style.css" /> 
    <link rel="stylesheet" href="css/icon.css">
    <link rel="stylesheet" href="css/material.indigo-pink.min.css">
    <script defer src="js/material.min.js"></script>
      <link rel="stylesheet" href="css/bootstrap.min.css">
  <script src="js/bootstrap.min.js"></script>
    @yield('header')
    @yield('script')
</head>

<body>
<div class="intro">
    @include('app.header')
    @yield('page_content')
    @include('app.footer')
</div>
</body>
</html>
