<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#131f25">
    <title>MapMaker — Atlas of worlds</title>
    <link rel="stylesheet" href="{{ asset('editor/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('mapmaker.css') }}">
    @livewireStyles
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
</html>
