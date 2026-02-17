<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="{{asset('apple-touch-icon.png')}}" />
    <link rel="apple-touch-icon" href="{{asset('apple-touch-icon.png')}}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">
{{--    <link href="https://fonts.cdnfonts.com/css/helvetica-neue-9" rel="stylesheet">--}}

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @inertiaHead
</head>
<body class="bg-main-gray">
@inertia
</body>
</html>
