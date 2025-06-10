<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>@yield('title', 'Sistema de Reservas - Universidad')</title>
    
    <!-- Tailwind configuration -->
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

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
    
<body class="flex flex-col min-h-screen">
    <!-- ======= Header ======= -->
    @include('shared.header')
    <!-- End Header -->

    <main class="flex-grow">
        @yield('contenido')
    </main>

    <!-- ======= Footer ======= -->
    @include('shared.footer')
    <!-- End Footer -->

    @yield('scripts')
</body>
</html>