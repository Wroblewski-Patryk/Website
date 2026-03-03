<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        @php
            $themeConfig = isset($page['props']['theme_config']) ? $page['props']['theme_config'] : null;
            if (is_string($themeConfig)) $themeConfig = json_decode($themeConfig, true);
            $headingFont = $themeConfig['globals']['fonts']['heading'] ?? 'Inter';
            $bodyFont = $themeConfig['globals']['fonts']['body'] ?? 'Inter';
            $fontUrl = "https://fonts.googleapis.com/css2?family=" . str_replace(' ', '+', $headingFont) . ":wght@300;400;500;600;700;800;900&family=" . str_replace(' ', '+', $bodyFont) . ":wght@300;400;500;600;700;800;900&display=swap";
        @endphp
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="{{ $fontUrl }}" rel="stylesheet">

        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
