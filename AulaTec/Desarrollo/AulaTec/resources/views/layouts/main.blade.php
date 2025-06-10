<!DOCTYPE html>
{{-- Configurar idioma dinámico y altura completa --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- RESET CSS PERSONALIZADO PARA NORMALIZAR NAVEGADORES --}}
    <style>
        /* Reset básico: eliminar márgenes y paddings por defecto */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        /* Configuración base del HTML */
        html {
            line-height: 1.15;                    /* Altura de línea estándar */
            -webkit-text-size-adjust: 100%;       /* Prevenir zoom automático en iOS */
            -webkit-font-smoothing: antialiased;  /* Suavizado de fuentes en WebKit */
            -moz-osx-font-smoothing: grayscale;   /* Suavizado de fuentes en Firefox */
        }
        
        /* Configuración base del BODY */
        body {
            margin: 0;
            /* Stack de fuentes del sistema para mejor rendimiento */
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Open Sans', 'Helvetica Neue', sans-serif;
            line-height: 1.6;                     /* Altura de línea legible */
        }
        
        /* Normalizar elementos multimedia */
        img, picture, video, canvas, svg {
            display: block;                        /* Eliminar espacio debajo de imágenes */
            max-width: 100%;                       /* Responsive por defecto */
        }
        
        /* Heredar fuente en elementos de formulario */
        input, button, textarea, select {
            font: inherit;                         /* Usar la misma fuente que el padre */
        }
        
        /* Prevenir overflow en contenedores raíz de React/Next.js */
        #root, #__next {
            isolation: isolate;
        }
        
        /* Optimización específica para Chrome */
        @media screen and (-webkit-min-device-pixel-ratio:0) {
            body {
                -webkit-font-smoothing: antialiased;
            }
        }
    </style>
    
    {{-- Favicon del sitio --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    {{-- Título dinámico con fallback --}}
    <title>@yield('title', 'AulaTec') - Sistema de Reservas</title>
    
    {{-- TAILWIND CSS CON CONFIGURACIÓN PERSONALIZADA --}}
    {{-- CDN con plugins necesarios incluidos --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <script>
        {{-- Configuración personalizada de Tailwind --}}
        tailwind.config = {
            theme: {
                extend: {
                    {{-- Stack de fuentes personalizado --}}
                    fontFamily: {
                        sans: ['-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Open Sans', 'Helvetica Neue', 'sans-serif'],
                    },
                    {{-- Configuración del contenedor responsivo --}}
                    container: {
                        center: true,                    /* Centrar automáticamente */
                        {{-- Padding responsivo según breakpoint --}}
                        padding: {
                            DEFAULT: '1rem',            /* Móvil: 16px */
                            sm: '2rem',                  /* Small: 32px */
                            lg: '4rem',                  /* Large: 64px */
                            xl: '5rem',                  /* Extra Large: 80px */
                            '2xl': '6rem',              /* 2X Large: 96px */
                        },
                        {{-- Breakpoints personalizados --}}
                        screens: {
                            sm: '640px',                 /* Tablets pequeñas */
                            md: '768px',                 /* Tablets */
                            lg: '1024px',                /* Laptops */
                            xl: '1280px',                /* Monitores */
                            '2xl': '1400px',            /* Monitores grandes */
                        }
                    }
                }
            },
            corePlugins: {
                preflight: true,                     /* Activar el reset CSS de Tailwind */
            }
        }
    </script>
    
    {{-- Bootstrap CSS comentado para evitar conflictos con Tailwind --}}
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
</head>

{{-- BODY con layout flexbox para footer pegajoso --}}
<body class="flex flex-col min-h-screen">

    {{-- ======= HEADER ======= --}}
    {{-- Incluir el header compartido --}}
    @include('shared.header')
    {{-- End Header --}}

    {{-- CONTENIDO PRINCIPAL --}}
    {{-- flex-grow hace que ocupe todo el espacio disponible --}}
    <main class="flex-grow">
        {{-- Aquí se inyecta el contenido específico de cada página --}}
        @yield('contenido')
    </main>

    {{-- ======= FOOTER ======= --}}
    {{-- Incluir el footer compartido --}}
    @include('shared.footer')
    {{-- End Footer --}}

    {{-- Bootstrap JS comentado para evitar conflictos --}}
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>