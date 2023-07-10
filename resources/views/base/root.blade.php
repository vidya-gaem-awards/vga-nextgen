<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#2C3E50">

    <title>Vidya Gaem Awards</title>

    <script src="https://kit.fontawesome.com/a7a6918ba5.js" crossorigin="anonymous"></script>

    @vite('resources/js/app.js')

    @stack('css')

    @yield('head')
</head>

<body>
    @yield('body')

    @stack('js')
</body>
