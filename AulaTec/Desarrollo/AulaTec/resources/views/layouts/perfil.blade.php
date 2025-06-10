<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <title>@yield('title', 'Mi Perfil') - Sistema de Reservas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        border: "hsl(214.3 31.8% 91.4%)",
                        input: "hsl(214.3 31.8% 91.4%)",
                        ring: "hsl(262.1 83.3% 57.8%)",
                        background: "hsl(0 0% 100%)",
                        foreground: "hsl(222.2 84% 4.9%)",
                        primary: {
                            DEFAULT: "hsl(262.1 83.3% 57.8%)",
                            foreground: "hsl(210 40% 98%)",
                        },
                        secondary: {
                            DEFAULT: "hsl(210 40% 96.1%)",
                            foreground: "hsl(222.2 47.4% 11.2%)",
                        },
                        destructive: {
                            DEFAULT: "hsl(0 84.2% 60.2%)",
                            foreground: "hsl(210 40% 98%)",
                        },
                        muted: {
                            DEFAULT: "hsl(210 40% 96.1%)",
                            foreground: "hsl(215.4 16.3% 46.9%)",
                        },
                        accent: {
                            DEFAULT: "hsl(210 40% 96.1%)",
                            foreground: "hsl(222.2 47.4% 11.2%)",
                        },
                        purple: {
                            50: "#f5f3ff",
                            100: "#ede9fe",
                            200: "#ddd6fe",
                            300: "#c4b5fd",
                            400: "#a78bfa",
                            500: "#8b5cf6",
                            600: "#7c3aed",
                            700: "#6d28d9",
                            800: "#5b21b6",
                            900: "#4c1d95",
                            950: "#2e1065",
                        },
                        cyan: {
                            50: "#ecfeff",
                            100: "#cffafe",
                            200: "#a5f3fc",
                            300: "#67e8f9",
                            400: "#22d3ee",
                            500: "#06b6d4",
                            600: "#0891b2",
                            700: "#0e7490",
                            800: "#155e75",
                            900: "#164e63",
                            950: "#083344",
                        },
                    },
                    borderRadius: {
                        lg: "0.5rem",
                        md: "calc(0.5rem - 2px)",
                        sm: "calc(0.5rem - 4px)",
                    },
                },
            },
        }
    </script>
    <style>
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        
        /* Asegurar box-sizing consistente */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        
        /* Normalizar márgenes y padding */
        body, h1, h2, h3, h4, h5, h6, p, ul, ol, li, figure, figcaption, blockquote, dl, dd {
            margin: 0;
            padding: 0;
        }
        
        /* Asegurar altura mínima consistente */
        html, body {
            height: 100%;
        }
        
        /* Prevenir overflow horizontal */
        html {
            overflow-x: hidden;
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="flex flex-col min-h-screen">

    <!-- Header -->
    @include('shared.header')

    <!-- Notification Component -->
    @include('components.notification')

    <!-- Main Content -->
    <main class="flex-1">
        @yield('contenido')
    </main>

    <!-- Footer -->
    @include('shared.footer')

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Toastify JS para notificaciones -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
</body>
</html>