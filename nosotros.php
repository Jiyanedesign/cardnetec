<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trayectoria y Valores | CardNet.ec - Marcaje Técnico</title>
    <meta name="description" content="Conoce nuestro taller de personalización de artículos corporativos y grabado láser en Quito, Ecuador.">
    <link rel="canonical" href="https://cardnet.ec/nosotros.php">
    
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://cardnet.ec/nosotros.php">
    <meta property="og:title" content="Trayectoria del Taller de Grabado | CardNet.ec">
    <meta property="og:description" content="Marcas conscientes eligen nuestra precisión. Conoce nuestro equipo, tecnología y valores.">
    <meta property="og:image" content="https://cardnet.ec/images/og-image.jpg">

    <!-- CSS Modulares con Cache Busting -->
    <link rel="stylesheet" href="css/base.css?v=2.0">
    <link rel="stylesheet" href="css/layout.css?v=2.0">
    <link rel="stylesheet" href="css/components.css?v=2.0">
    <link rel="stylesheet" href="css/pages.css?v=2.0">
    <link rel="stylesheet" href="css/animations.css?v=1.1.2">

    <!-- Optimización Google Fonts Preconnect -->

    <!-- Google Fonts: Marcellus (Títulos Elegantes) & Work Sans (Textos Limpios) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Barra de Anuncios Superior -->
    <div class="top-announcement-bar">
        Taller de personalización en Quito | Envíos corporativos asegurados a todo el Ecuador
    </div>

    <!-- Cabecera de Página Multicapa -->
    <header class="main-header">
        <div class="container">
            <div class="header-middle">
                <!-- Logotipo Real en Imagen (logo.png) -->
                <a href="index.php" class="logo" aria-label="CardNet.ec Inicio">
                    <img src="images/logo.png?v=2.0" alt="CardNet.ec Logo" class="logo-img">
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
                            <p>+593 00 000 0000</p>
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
                    <a href="cotizacion.php" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">SOLICITAR COTIZACIÓN</a>
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
            <h1 class="page-header-title">Trayectoria y Procesos de Taller</h1>
            <p class="page-header-description">Desde nuestro taller en Quito, diseñamos y realizamos acabados permanentes y precisos sobre superficies seleccionadas.</p>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main>
        
        <section class="section-padding container reveal-on-scroll">
            <div class="split-feature">
                <div class="split-content">
                    <span class="section-subtitle">Taller en Quito</span>
                    <h2>Dedicación técnica en cada acabado</h2>
                    <p>En CardNet.ec no trabajamos con procesos masivos sin supervisión. Entendemos la composición y textura de cada material: acero, madera, cuero y vidrio.</p>
                    <p>Contamos con equipos de grabado láser de fibra y CO2 de última generación, operados por diseñadores que adaptan tu logotipo a las proporciones reales del soporte. Nos comprometemos a que el acabado final represente la calidad de tu marca.</p>
                </div>
                <div class="split-visual">
                    <div class="image-placeholder theme-gray">
                        <div class="image-placeholder-inner">
                            <svg class="image-placeholder-icon" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
                            </svg>
                            <span class="image-placeholder-text">Calibración de haz de luz en taller</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Pie de Página -->
    <footer class="main-footer">
        <div class="container footer-top section-padding" style="padding-top: 3rem; padding-bottom: 3rem;">
            <div class="footer-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 40px;">
                <div class="footer-brand-column">
                    <a href="index.php" class="logo footer-logo" aria-label="CardNet.ec Inicio">
                        <img src="images/logo.png?v=2.0" alt="CardNet.ec Logo" class="logo-img">
                    </a>
                    <p class="footer-description" style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-top: 1rem;">Grabado láser, carnets y productos personalizados para empresas, instituciones y eventos.</p>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading" style="font-size: 0.9rem; font-family: var(--font-heading); margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dark);">Productos</h3>
                    <nav class="footer-links" aria-label="Enlaces de productos" style="display: flex; flex-direction: column; gap: 8px; font-size: 0.85rem;">
                        <a href="productos.php" class="footer-link">Termos</a>
                        <a href="productos.php" class="footer-link">Agendas</a>
                        <a href="empresas.php" class="footer-link">Kits</a>
                        <a href="productos.php" class="footer-link">Placas</a>
                        <a href="productos.php" class="footer-link">Carnets</a>
                    </nav>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading" style="font-size: 0.9rem; font-family: var(--font-heading); margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dark);">Contacto</h3>
                    <div class="footer-contact-info" style="display: flex; flex-direction: column; gap: 10px; font-size: 0.85rem; color: var(--text-muted);">
                        <a href="https://wa.me/593000000000" class="footer-link" target="_blank" rel="noopener noreferrer">WhatsApp: +593 00 000 0000</a>
                        <a href="mailto:correo@cardnet.ec" class="footer-link">Correo: correo@cardnet.ec</a>
                        <span class="footer-link" style="color: var(--text-muted); cursor: default;">Ubicación: Ecuador</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom" style="border-top: 1px solid var(--border); padding-top: 1.5rem; padding-bottom: 1.5rem;">
            <div class="container footer-bottom-flex" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <p style="font-size: 0.8rem; color: var(--text-muted);">&copy; 2026 CardNet.ec — Detalles personalizados para marcas que cuidan su presentación.</p>
                <div class="footer-bottom-links" style="display: flex; gap: 15px; font-size: 0.8rem;">
                    <a href="faq.php" class="footer-link">Preguntas Frecuentes</a>
                    <a href="contacto.php" class="footer-link">Soporte</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Botón de WhatsApp Flotante Redondeado -->
    <a href="https://wa.me/593900000000" class="whatsapp-float" target="_blank" rel="noopener noreferrer">
        <svg class="whatsapp-icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
            <path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 0 0 1.333 4.982L2 22l5.233-1.371a9.994 9.994 0 0 0 4.779 1.22h.005c5.505 0 9.99-4.478 9.99-9.985A9.988 9.988 0 0 0 12.012 2zm4.7 13.916c-.223.633-1.29 1.205-1.782 1.282-.477.075-.947.168-3.067-.665-2.707-1.06-4.442-3.817-4.577-3.996-.134-.178-1.096-1.455-1.096-2.781 0-1.325.692-1.973.938-2.228.246-.255.535-.319.714-.319.18 0 .358.001.514.009.16.008.375-.062.586.448.223.54.76 1.851.827 1.984.067.134.112.29.022.468-.09.18-.134.29-.268.447-.134.156-.282.35-.403.47-.134.134-.273.28-.117.548.156.268.693 1.139 1.492 1.85 1.026.914 1.89 1.196 2.158 1.33.268.134.424.112.58-.067.157-.18.67-.781.848-1.049.178-.268.358-.223.58-.134.224.089 1.42.67 1.666.792.246.123.411.18.47.282.06.101.06.586-.163 1.218z"/>
        </svg>
    </a>

    <!-- Scripts Modulares -->
    <script src="js/main.js"></script>
    <script src="js/animations.js"></script>
</body>
</html>
