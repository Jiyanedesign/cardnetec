<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CardNet.ec | Grabado Láser y Personalización Corporativa')</title>
    <meta name="description" content="@yield('meta_description', 'Taller de personalización de artículos corporativos y grabado láser en Quito, Ecuador.')">
    
    <!-- CSS Modulares -->
    <link rel="stylesheet" href="{{ asset('css/base.css?v=2.0') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css?v=2.0') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css?v=2.0') }}">
    <link rel="stylesheet" href="{{ asset('css/pages.css?v=2.0') }}">
    <link rel="stylesheet" href="{{ asset('css/animations.css?v=1.1.2') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @yield('head')
</head>
<body>

    <div class="top-announcement-bar">
        Taller de personalización en Quito | Envíos corporativos asegurados a todo el Ecuador
    </div>

    <header class="main-header">
        <div class="container">
            <div class="header-middle">
                <a href="{{ route('home') }}" class="logo" aria-label="CardNet.ec Inicio">
                    <img src="{{ asset('images/logo.png?v=2.0') }}" alt="CardNet.ec Logo" class="logo-img">
                </a>
                
                <div class="header-search">
                    <svg class="search-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input class="search-input" type="text" placeholder="Buscar grabados en metal, corcho, madera...">
                </div>

                <div class="header-contact-status">
                    <div class="contact-status-item">
                        <span class="status-icon-wrap">
                            <svg style="width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2.5;" viewBox="0 0 24 24">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </span>
                        <div class="status-text">
                            <h4>Asesoría personalizada</h4>
                            <p>+593 90 000 0000</p>
                        </div>
                    </div>
                </div>

                <button class="burger-menu" aria-label="Abrir menú de navegación" aria-expanded="false" aria-controls="mobile-nav">
                    <span class="burger-line"></span>
                    <span class="burger-line"></span>
                    <span class="burger-line"></span>
                </button>
            </div>
        </div>

        <div class="header-bottom">
            <div class="container nav-container">
                <nav class="nav-menu" aria-label="Navegación principal">
                    <a href="{{ route('home') }}" class="nav-link {{ Route::is('home') ? 'active' : '' }}">Inicio</a>
                    <a href="{{ route('home') }}#destacados" class="nav-link">Destacados</a>
                    <a href="{{ route('home') }}#laser" class="nav-link">Grabado láser</a>
                    <a href="{{ route('productos') }}" class="nav-link {{ Route::is('productos') ? 'active' : '' }}">Productos</a>
                    <a href="{{ route('empresas') }}" class="nav-link {{ Route::is('empresas') ? 'active' : '' }}">Kits corporativos</a>
                    <a href="{{ route('cotizacion') }}" class="nav-link {{ Route::is('cotizacion') ? 'active' : '' }}">Cotizar</a>
                </nav>
                <div class="header-bottom-actions">
                    <a href="{{ route('cotizacion') }}" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">Cotizar</a>
                </div>
            </div>
        </div>
    </header>

    <div class="mobile-nav-overlay"></div>
    <nav id="mobile-nav" class="mobile-nav" aria-label="Navegación móvil">
        <a href="{{ route('home') }}" class="mobile-link {{ Route::is('home') ? 'active' : '' }}">Inicio</a>
        <a href="{{ route('home') }}#destacados" class="mobile-link">Destacados</a>
        <a href="{{ route('home') }}#laser" class="mobile-link">Grabado láser</a>
        <a href="{{ route('productos') }}" class="mobile-link {{ Route::is('productos') ? 'active' : '' }}">Productos</a>
        <a href="{{ route('empresas') }}" class="mobile-link {{ Route::is('empresas') ? 'active' : '' }}">Kits corporativos</a>
        <a href="{{ route('cotizacion') }}" class="btn btn-primary" style="margin-top: 1rem; width: 100%;">Cotizar</a>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="main-footer">
        <div class="container footer-top section-padding">
            <div class="footer-grid">
                <div class="footer-brand-column">
                    <a href="{{ route('home') }}" class="logo footer-logo" aria-label="CardNet.ec Inicio">
                        <img src="{{ asset('images/logo.png?v=2.0') }}" alt="CardNet.ec Logo" class="logo-img">
                    </a>
                    <p class="footer-description">Grabado láser y personalización corporativa en Ecuador.</p>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading">Productos</h3>
                    <nav class="footer-links" aria-label="Enlaces de productos">
                        <a href="{{ route('productos') }}#termos" class="footer-link">Termos</a>
                        <a href="{{ route('productos') }}#oficina" class="footer-link">Agendas</a>
                        <a href="{{ route('productos') }}#kits" class="footer-link">Kits</a>
                        <a href="{{ route('productos') }}#placas" class="footer-link">Placas</a>
                        <a href="{{ route('productos') }}#llaveros" class="footer-link">Llaveros</a>
                    </nav>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading">Contacto</h3>
                    <div class="footer-contact-info">
                        <div class="footer-contact-item">
                            <svg class="footer-contact-icon" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72(12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span>WhatsApp: +593 90 000 0000</span>
                        </div>
                        <div class="footer-contact-item">
                            <svg class="footer-contact-icon" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="m22 6-10 7L2 6"/></svg>
                            <span>Correo: info@cardnet.ec</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container footer-bottom-flex">
                <p>&copy; 2026 CardNet.ec — Detalles personalizados para marcas que cuidan su presentación.</p>
                <div class="footer-bottom-links">
                    <a href="{{ route('faq') }}" class="footer-bottom-link">Preguntas Frecuentes</a>
                    <a href="{{ route('contacto') }}" class="footer-bottom-link">Soporte de Taller</a>
                </div>
            </div>
        </div>
    </footer>

    <a href="https://wa.me/593900000000" class="whatsapp-float" target="_blank" rel="noopener noreferrer">
        <svg class="whatsapp-icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
            <path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 0 0 1.333 4.982L2 22l5.233-1.371a9.994 9.994 0 0 0 4.779 1.22h.005c5.505 0 9.99-4.478 9.99-9.985A9.988 9.988 0 0 0 12.012 2zm4.7 13.916c-.223.633-1.29 1.205-1.782 1.282-.477.075-.947.168-3.067-.665-2.707-1.06-4.442-3.817-4.577-3.996-.134-.178-1.096-1.455-1.096-2.781 0-1.325.692-1.973.938-2.228.246-.255.535-.319.714-.319.18 0 .358.001.514.009.16.008.375-.062.586.448.223.54.76 1.851.827 1.984.067.134.112.29.022.468-.09.18-.134.29-.268.447-.134.156-.282.35-.403.47-.134.134-.273.28-.117.548.156.268.693 1.139 1.492 1.85 1.026.914 1.89 1.196 2.158 1.33.268.134.424.112.58-.067.157-.18.67-.781.848-1.049.178-.268.358-.223.58-.134.224.089 1.42.67 1.666.792.246.123.411.18.47.282.06.101.06.586-.163 1.218z"/>
        </svg>
    </a>

    <!-- Scripts -->
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/animations.js') }}"></script>
    @yield('scripts')
</body>
</html>
