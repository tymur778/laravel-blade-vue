<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }}</title>

    @vite(['resources/css/app.scss'])
</head>
<body class="font-monospace antialiased">
<div class="flex flex-col h-screen justify-between px-5 sm:px-0">
    <div class="container max-w-2xl mx-auto mb-12">
    @include('layout.header')
    </div>
    <div class="container max-w-2xl mx-auto mb-auto">
    @yield('content')
    </div>
    <div class="container max-w-2xl mx-auto mt-12">
    @include('layout.footer')
    </div>
</div>
</body>
