<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | CardNet.ec - Taller de Marcado Promocional</title>
    <meta name="description" content="Ponte en contacto con CardNet.ec. Agenda una llamada comercial, escríbenos por WhatsApp o visítanos en nuestro taller de Quito, Ecuador.">
    <link rel="canonical" href="https://cardnet.ec/contacto.php">
    
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://cardnet.ec/contacto.php">
    <meta property="og:title" content="Contacto Comercial B2B | CardNet.ec">
    <meta property="og:description" content="Comunícate con nuestros asesores de marcado técnico. Rápida respuesta corporativa.">
    <meta property="og:image" content="https://cardnet.ec/images/og-image.jpg">

    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=2.0">
    <link rel="stylesheet" href="css/layout.css?v=2.0">
    <link rel="stylesheet" href="css/components.css?v=2.0">
    <link rel="stylesheet" href="css/pages.css?v=2.0">
    <link rel="stylesheet" href="css/animations.css?v=1.1.2">

    <!-- Google Fonts: Marcellus (Títulos Elegantes) & Work Sans (Textos Limpios) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Barra de Anuncios Superior -->
    <div class="top-announcement-bar">
        Envío estándar gratis en pedidos corporativos mayores a $150 | Tiempos de entrega rápidos a todo el país
    </div>

    <!-- Cabecera de Página Multicapa -->
    <header class="main-header">
        <div class="container">
            <div class="header-middle">
                <a href="index.php" class="logo" aria-label="CardNet.ec Inicio">
                    <img src="images/logo.png?v=2.0" alt="CardNet.ec Logo" class="logo-img">
                </a>
                
                <div class="header-search">
                    <svg class="search-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input class="search-input" type="text" placeholder="Buscar termos, camisetas, libretas...">
                </div>

                <div class="header-contact-status">
                    <div class="contact-status-item">
                        <span class="status-icon-wrap">
                            <svg style="width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2.5;" viewBox="0 0 24 24">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72(12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </span>
                        <div class="status-text">
                            <h4>Asesoría Directa</h4>
                            <p>+593 90 000 0000</p>
                        </div>
                    </div>
                    <div class="contact-status-item">
                        <span class="status-icon-wrap">
                            <svg style="width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2.5;" viewBox="0 0 24 24">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </span>
                        <div class="status-text">
                            <h4>¿Dudas Comerciales?</h4>
                            <p><a href="#" class="status-link">Chatea ahora</a></p>
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
                    <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
                    <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Inicio</a>
                    <a href="productos.php" class="nav-link <?php echo ($current_page == 'productos.php' || $current_page == 'producto.php') ? 'active' : ''; ?>">Productos</a>
                    <a href="index.php#laser" class="nav-link">Grabado láser</a>
                    <a href="empresas.php" class="nav-link <?php echo ($current_page == 'empresas.php') ? 'active' : ''; ?>">Kits empresariales</a>
                    <a href="index.php#antes-despues" class="nav-link">Personalización</a>
                    <a href="index.php#proceso" class="nav-link">Cómo pedir</a>
                    <a href="cotizacion.php" class="nav-link <?php echo ($current_page == 'cotizacion.php') ? 'active' : ''; ?>">Cotizar<?php
                    $c_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                    if ($c_count > 0) {
                        echo '<span style="background: var(--primary); color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.7rem; font-weight: bold; margin-left: 3px;">' . $c_count . '</span>';
                    }
                    ?></a>
                </nav>
                <div class="header-bottom-actions">
                    <a href="cotizacion.php" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">Cotizar Ahora</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Menú Móvil -->
    <div class="mobile-nav-overlay"></div>
    <nav id="mobile-nav" class="mobile-nav" aria-label="Navegación móvil">
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <a href="index.php" class="mobile-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Inicio</a>
        <a href="productos.php" class="mobile-link <?php echo ($current_page == 'productos.php' || $current_page == 'producto.php') ? 'active' : ''; ?>">Productos</a>
        <a href="index.php#laser" class="mobile-link">Grabado láser</a>
        <a href="empresas.php" class="mobile-link <?php echo ($current_page == 'empresas.php') ? 'active' : ''; ?>">Kits empresariales</a>
        <a href="index.php#antes-despues" class="mobile-link">Personalización</a>
        <a href="index.php#proceso" class="mobile-link">Cómo pedir</a>
        <a href="cotizacion.php" class="mobile-link <?php echo ($current_page == 'cotizacion.php') ? 'active' : ''; ?>">SOLICITAR COTIZACIÓN</a>
        <a href="cotizacion.php" class="btn btn-primary" style="margin-top: 1rem; width: 100%;">SOLICITAR COTIZACIÓN</a>
    </nav>

    <!-- Encabezado de Página Interna -->
    <div class="page-header-block">
        <div class="container">
            <h1 class="page-header-title">Canales de Contacto</h1>
            <p class="page-header-description">¿Tienes preguntas operativas, de entrega o dudas de color? Escríbenos o visítanos directamente en las oficinas de nuestro taller. Respondemos rápido.</p>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="section-padding container">
        
        <div class="split-feature">
            
            <!-- Canales y Horarios -->
            <div class="split-content reveal-on-scroll">
                <span class="section-subtitle">Canales Directos</span>
                <h2>Habla con un Asesor Técnico</h2>
                <p>Nuestra planta y taller se encuentran ubicados estratégicamente en la zona comercial del norte de Quito, con acceso directo para el despacho de carga a terminales y aeropuertos.</p>
                
                <div class="footer-contact-info" style="margin-bottom: 2rem; gap: 1rem;">
                    <div class="footer-contact-item" style="font-size: 1rem;">
                        <svg class="footer-contact-icon" style="width: 20px; height: 20px;" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72(12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span><strong>Teléfono:</strong> +593 90 000 0000</span>
                    </div>
                    <div class="footer-contact-item" style="font-size: 1rem;">
                        <svg class="footer-contact-icon" style="width: 20px; height: 20px;" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="m22 6-10 7L2 6"/></svg>
                        <span><strong>Correo:</strong> info@cardnet.ec</span>
                    </div>
                    <div class="footer-contact-item" style="font-size: 1rem;">
                        <svg class="footer-contact-icon" style="width: 20px; height: 20px;" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span><strong>Taller:</strong> Av. de los Granados, Edificio Corporativo, Quito, Ecuador</span>
                    </div>
                    <div class="footer-contact-item" style="font-size: 1rem;">
                        <svg class="footer-contact-icon" style="width: 20px; height: 20px;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span><strong>Horario:</strong> Lunes a Viernes, de 09:00 a 18:00</span>
                    </div>
                </div>

                <div class="image-placeholder theme-blue">
                    <svg class="image-placeholder-icon" viewBox="0 0 24 24" width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5">
                        <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/></svg>
                    <span class="image-placeholder-text">Av. de los Granados y Av. Eloy Alfaro, Quito</span>
                </div>
            </div>

            <!-- Formulario de Consulta -->
            <div class="split-visual reveal-on-scroll delay-100">
                <div class="solution-card">
                    <h3 style="margin-bottom: 1.25rem; font-size: 1.25rem;">Escribe a nuestro taller</h3>
                    
                    <form id="contact-form" novalidate>
                        <div class="form-group">
                            <label class="form-label" for="name">Tu Nombre Completo *</label>
                            <input class="form-input" type="text" id="name" required placeholder="Ej. Javier Ortiz">
                            <span class="form-error-msg">Este campo es obligatorio.</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Correo Corporativo *</label>
                            <input class="form-input" type="email" id="email" required placeholder="javier@empresa.com">
                            <span class="form-error-msg">Por favor, introduce un correo corporativo válido.</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="message">Consulta o Detalle Técnico *</label>
                            <textarea class="form-textarea" id="message" required placeholder="Escribe aquí tu consulta o los detalles de marcaje que deseas evaluar..."></textarea>
                            <span class="form-error-msg">Este campo es obligatorio.</span>
                        </div>

                        <button class="btn btn-primary" type="submit" style="width: 100%;">
                            Enviar Consulta
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </main>

    <!-- Pie de Página -->
    <footer class="main-footer">
        <div class="container footer-top section-padding">
            <div class="footer-grid">
                <div class="footer-brand-column">
                    <a href="index.php" class="logo footer-logo" aria-label="CardNet.ec Inicio">
                        <img src="images/logo.png?v=2.0" alt="CardNet.ec Logo" class="logo-img">
                    </a>
                    <p class="footer-description">Taller de personalización y marcado de artículos promocionales de alta fidelidad en Ecuador.</p>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading">Nosotros</h3>
                    <nav class="footer-links" aria-label="Enlaces corporativos">
                        <a href="nosotros.php" class="footer-link">Trayectoria</a>
                        <a href="personalizacion.php" class="footer-link">Técnicas</a>
                        <a href="empresas.php" class="footer-link">Servicios B2B</a>
                        <a href="proyectos.php" class="footer-link">Proyectos</a>
                    </nav>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading">Productos</h3>
                    <nav class="footer-links" aria-label="Enlaces de productos">
                        <a href="productos.php" class="footer-link">Todo el Catálogo</a>
                        <a href="productos.php#termos" class="footer-link">Termos y Vasos</a>
                        <a href="productos.php#textil" class="footer-link">Polos y Gorras</a>
                        <a href="productos.php#oficina" class="footer-link">Libretas y Oficina</a>
                    </nav>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading">Contacto</h3>
                    <div class="footer-contact-info">
                        <div class="footer-contact-item">
                            <svg class="footer-contact-icon" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72(12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span>+593 90 000 0000</span>
                        </div>
                        <div class="footer-contact-item">
                            <svg class="footer-contact-icon" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="m22 6-10 7L2 6"/></svg>
                            <span>info@cardnet.ec</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container footer-bottom-flex">
                <p>&copy; 2026 CardNet.ec. Todos los derechos reservados. Diseñado para marcas conscientes.</p>
                <div class="footer-bottom-links">
                    <a href="faq.php" class="footer-bottom-link">Preguntas Frecuentes</a>
                    <a href="contacto.php" class="footer-bottom-link">Soporte</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Botón de WhatsApp Flotante -->
    <a href="#" class="whatsapp-float" target="_blank" rel="noopener noreferrer">
        <svg class="whatsapp-icon" viewBox="0 0 24 24" width="28" height="28" fill="currentColor">
            <path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 0 0 1.333 4.982L2 22l5.233-1.371a9.994 9.994 0 0 0 4.779 1.22h.005c5.505 0 9.99-4.478 9.99-9.985A9.988 9.988 0 0 0 12.012 2zm4.7 13.916c-.223.633-1.29 1.205-1.782 1.282-.477.075-.947.168-3.067-.665-2.707-1.06-4.442-3.817-4.577-3.996-.134-.178-1.096-1.455-1.096-2.781 0-1.325.692-1.973.938-2.228.246-.255.535-.319.714-.319.18 0 .358.001.514.009.16.008.375-.062.586.448.223.54.76 1.851.827 1.984.067.134.112.29.022.468-.09.18-.134.29-.268.447-.134.156-.282.35-.403.47-.134.134-.273.28-.117.548.156.268.693 1.139 1.492 1.85 1.026.914 1.89 1.196 2.158 1.33.268.134.424.112.58-.067.157-.18.67-.781.848-1.049.178-.268.358-.223.58-.134.224.089 1.42.67 1.666.792.246.123.411.18.47.282.06.101.06.586-.163 1.218z"/>
        </svg>
    </a>

    <!-- Scripts Modulares -->
    <script src="js/main.js"></script>
    <script src="js/animations.js"></script>
    <script src="js/forms.js"></script>
</body>
</html>
