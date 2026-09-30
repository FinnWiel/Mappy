<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MapMaker</title>
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('mapmaker.css') }}">
</head>
<body class="site-body">
    {{ $slot }}
    @livewireScripts
</body>
</html>
