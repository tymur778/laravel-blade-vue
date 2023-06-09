<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <!-- test -->

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Elements/{$page['component']}.vue"])
        @vite(['resources/js/plugins/prism.js', "resources/css/plugins/prism.css"])
        @inertiaHead
        @csrf
    </head>
    <body class="font-monospace antialiased">
        @inertia
    </body>
</html>
