<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>@yield('title')</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link type="text/css" rel="stylesheet" href="css/style.css" /> 
    @yield('header')
    @yield('script')
</head>

<body>
<div class="container">
    @include('app.header')
    @yield('page_content')
    @include('app.footer')
</div>
</body>
</html>
