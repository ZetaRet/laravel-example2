<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Laravel Example 2 - @yield('title')</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<link href="{{ asset('css/style.css') }}" rel="stylesheet">
<script src="{{ asset('js/app_example2.js') }}"></script>
</head>
<body>
<div id="main-view" class="main-container">
@yield('content')
</div>
</body>
</html>