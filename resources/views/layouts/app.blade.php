<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 1. TÍTULO DINÁMICO: Si la vista hija tiene título, lo usa. Si no, usa el por defecto --}}
    <title>@yield('title', config('app.name', 'Gobernación del Putumayo'))</title>

    {{-- 2. META DESCRIPCIÓN PARA GOOGLE: Crucial para el texto gris en los resultados --}}
    <meta name="description" content="@yield('meta_description', 'Portal oficial de la Gobernación del Putumayo. Trámites, servicios, noticias y gestión transparente para los ciudadanos.')">

    {{-- 3. OPEN GRAPH (Para que se vea bien al compartir en WhatsApp/Facebook) --}}
    <meta property="og:title" content="@yield('title', 'Gobernación del Putumayo')" />
    <meta property="og:description" content="@yield('meta_description', 'Portal oficial de la Gobernación del Putumayo.')" />
    <meta property="og:image" content="{{ asset('/img/escudo-share.jpg') }}" /> {{-- Asegúrate de tener una imagen genérica aquí --}}
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('/icon/putumayo.ico') }}" type="image/x-icon">

    {{-- Estilos Globales --}}
    <link rel="stylesheet" href="{{ asset('/css/global.css') }}">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Work+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chartist/dist/chartist.min.css">
    
    <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @livewireStyles

    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/api.js') }}" defer></script>
</head>

<body class="{{ request()->is('dashboard*') ? 'dashboard-layout' : 'public-layout' }}" 
      x-data="{ loading: true }"
      x-init="window.addEventListener('load', () => loading = false)">
    
    <x-spinner/>
    <x-accesibility-bar />
    <x-jet-banner />

    @if (!request()->is('dashboard*'))
        <x-navbar />
    @endif

    <div id="container">
        @if (Auth::check() && request()->is('dashboard*'))
            @livewire('sidebar-menu')
        @endif

        <div id="main-content" x-show="!loading" x-cloak>
            
            @if (isset($header))
                <header class="bg-white shadow">
                    <div>{{ $header }}</div>
                </header>
            @endif

            <main class="container-centered">
                {{-- Aquí se inyecta el contenido de tus vistas --}}
                @yield('content')
            </main>
        </div>
    </div>

    @if (!request()->is('dashboard*'))
        <x-footer />
    @endif

    @once
        @include('components.ui.confirm')
        @include('components.ui.toast')
    @endonce
    
    @stack('modals')

    <script>
        window.getCsrf = function () {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                || document.querySelector('input[name="_token"]')?.value;
        };

        function toggleSidebar() {
            let sidebar = document.getElementById("sidebar");
            let mainContent = document.getElementById("main-content");
            let body = document.body;

            sidebar.classList.toggle("collapsed");
            body.classList.toggle("sidebar-collapsed");

            if (sidebar.classList.contains("collapsed")) {
                mainContent.style.width = "calc(100% - 80px)";
            } else {
                mainContent.style.width = "calc(100% - 260px)";
            }
        }
    </script>

    @livewireScripts
    @stack('scripts')
    @stack('styles')
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartist/dist/chartist.min.js"></script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "GovernmentOrganization",
      "name": "Gobernación del Putumayo",
      "alternateName": "Gobernación Putumayo",
      "url": "{{ url('/') }}",
      "logo": "{{ asset('/icon/putumayo.ico') }}", 
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Calle 8 # 7 - 40 Barrio Centro",
        "addressLocality": "Mocoa",
        "addressRegion": "Putumayo",
        "postalCode": "860001",
        "addressCountry": "CO"
      },
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+57-8-4206600",
        "contactType": "customer service",
        "areaServed": "Putumayo",
        "availableLanguage": "Spanish"
      }
    }
    </script>
</body>
</html>