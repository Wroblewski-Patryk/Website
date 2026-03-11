<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            $appName = $page['props']['seo_settings']['site_name'] ?? config('app.name', 'Featherly');
            if (empty($appName)) {
                $appName = 'Featherly';
            }
            $fullTitle = $page['props']['seo']['full_title'] ?? null;

            // Zapasowy tytuł dla stron panelu (nie ustawiają one właściwości ustrukturyzowanego SEO)
            if (!$fullTitle && isset($page['props']['admin_seo'])) {
                $adminSeo = $page['props']['admin_seo'];
                $parts = [];
                if (!empty($adminSeo['action_label'])) $parts[] = $adminSeo['action_label'];
                if (!empty($adminSeo['module_label'])) $parts[] = $adminSeo['module_label'];
                $parts[] = 'Admin Panel';
                
                // Tłumaczenie appName dla języka jeśli istnieje jako tablica
                if (is_array($appName)) {
                    $appName = $appName[app()->getLocale()] ?? reset($appName) ?? config('app.name', 'Featherly');
                }
                $parts[] = is_string($appName) && !empty($appName) ? $appName : config('app.name', 'Featherly');
                
                $fullTitle = implode(' - ', $parts);
            }
        @endphp
        <title inertia>{{ $fullTitle ?? (is_array($appName) ? ($appName[app()->getLocale()] ?? reset($appName)) : $appName) }}</title>

        <!-- Fonts -->
        @php
            $themeConfig = isset($page['props']['theme_config']) ? $page['props']['theme_config'] : null;
            if (is_string($themeConfig)) $themeConfig = json_decode($themeConfig, true);
            $sansFont = $themeConfig['globals']['fonts']['sans'] ?? 'Inter';
            $serifFont = $themeConfig['globals']['fonts']['serif'] ?? 'Merriweather';
            $monoFont = $themeConfig['globals']['fonts']['mono'] ?? 'Fira Code';
            
            // Build the URL for Google Fonts dynamically
            $fontsToLoad = array_unique([$sansFont, $serifFont, $monoFont]);
            $fontFamilies = array_map(function($font) {
                return "family=" . str_replace(' ', '+', $font) . ":wght@300;400;500;600;700;800;900";
            }, $fontsToLoad);
            
            $fontUrl = "https://fonts.googleapis.com/css2?" . implode('&', $fontFamilies) . "&display=swap";
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
