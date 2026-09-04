<?php
// Version Control V4.0.1
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$c_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$current_page = basename($_SERVER['PHP_SELF']);

// Cargar configuraciones del sitio
if (!isset($site_settings) && isset($pdo)) {
    $site_settings = getSiteSettings($pdo);
}
$header_wa_url = formatWhatsAppUrl($site_settings['whatsapp'] ?? '', 'Hola CardNet, deseo información sobre sus servicios.');
$header_wa_display = !empty($site_settings['whatsapp']) ? $site_settings['whatsapp'] : '+593 99 978 180';
?>
<!-- Barra de Anuncios Superior -->
<div class="top-announcement-bar">
    Taller de personalización de precisión · Grabado láser indeleble y acabados de autor · Envíos a todo el Ecuador
</div>

<!-- Cabecera de Página -->
<header class="main-header">
    <div class="container">
        <div class="header-middle">
            <!-- Logotipo Real en Imagen (logo.webp) -->
            <a href="index.php" class="logo" aria-label="CardNet.ec Inicio">
                <img src="images/logo.webp?v=2.1" alt="CardNet.ec Logo" class="logo-img" width="132" height="48" fetchpriority="high">
            </a>
            
            <form action="productos.php" method="GET" class="header-search" id="global-header-search" role="search" style="position: relative;">
                <div class="search-input-wrapper" style="position: relative; width: 100%; display: flex; align-items: center;">
                    <svg class="search-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#9aa0a6" stroke-width="2" style="position: absolute; left: 16px; pointer-events: none;">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input class="search-input" type="text" name="q" placeholder="Buscar productos, carnets, tagua, placas..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" autocomplete="off" aria-label="Buscar productos">
                    <button type="button" class="search-clear-btn" id="search-clear-btn" style="display: none;" title="Limpiar búsqueda" aria-label="Limpiar búsqueda">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    <button type="submit" class="search-submit-btn" title="Buscar en catálogo" aria-label="Buscar">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
                <div class="search-results-dropdown" id="search-results-dropdown" style="display: none;"></div>
            </form>

            <div class="header-contact-status">
                <a href="<?php echo $header_wa_url; ?>" target="_blank" rel="noopener noreferrer" style="text-decoration: none; color: inherit;" title="Chatear con un Asesor por WhatsApp">
                    <div class="contact-status-item">
                        <span class="status-icon-wrap">
                            <svg style="width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2.5;" viewBox="0 0 24 24">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72"/>
                            </svg>
                        </span>
                        <div class="status-text">
                            <h4>Taller & Personalización</h4>
                            <p style="font-size: 0.8rem; font-weight: 500; color: var(--primary);"><?php echo htmlspecialchars($header_wa_display); ?></p>
                        </div>
                    </div>
                </a>
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
                <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Inicio</a>
                <a href="productos.php" class="nav-link <?php echo ($current_page == 'productos.php' || $current_page == 'producto.php') ? 'active' : ''; ?>">Productos</a>
                <a href="empresas.php" class="nav-link <?php echo ($current_page == 'empresas.php' || $current_page == 'carnets.php') ? 'active' : ''; ?>">Carnets y Empresas</a>
                <a href="personalizacion.php" class="nav-link <?php echo ($current_page == 'personalizacion.php') ? 'active' : ''; ?>">Personalización</a>
                <a href="tagua.php" class="nav-link <?php echo ($current_page == 'tagua.php') ? 'active' : ''; ?>">Tagua</a>
                <a href="cotizacion.php" class="nav-link <?php echo ($current_page == 'cotizacion.php') ? 'active' : ''; ?>">Cotizar<?php
                if ($c_count > 0) {
                    echo '<span style="background: var(--primary); color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.7rem; font-weight: bold; margin-left: 3px;">' . $c_count . '</span>';
                }
                ?></a>
            </nav>
            <div class="header-bottom-actions" style="display: flex; align-items: center; gap: 15px;">
                <div class="header-cart-dropdown-wrapper">
                    <a href="cotizacion.php" class="cart-icon-btn" aria-label="Ver mi lista de cotización">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <path d="M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                        <?php if ($c_count > 0): ?>
                            <span class="cart-count"><?php echo $c_count; ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <a href="cotizacion.php" class="btn btn-primary header-cta" style="padding: 8px 16px; font-size: 0.8rem; text-transform: none;">Cotizar</a>
            </div>
        </div>
    </div>
</header>

<!-- Menú Móvil -->
<div class="mobile-nav-overlay"></div>
<nav id="mobile-nav" class="mobile-nav" aria-label="Navegación móvil">
    <!-- Logotipo al inicio del menú -->
    <div style="text-align: center; margin-bottom: 2.25rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border); width: 100%;">
        <img src="images/logo.webp?v=2.1" alt="CardNet.ec Logo" style="height: 48px; width: auto; display: inline-block;" width="132" height="48" loading="lazy" decoding="async">
    </div>
    
    <!-- Enlaces con margen superior incrementado -->
    <div style="display: flex; flex-direction: column; gap: 1.25rem; width: 100%; padding-top: 0.5rem;">
        <a href="index.php" class="mobile-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Inicio</a>
        <a href="productos.php" class="mobile-link <?php echo ($current_page == 'productos.php' || $current_page == 'producto.php') ? 'active' : ''; ?>">Productos</a>
        <a href="empresas.php" class="mobile-link <?php echo ($current_page == 'empresas.php' || $current_page == 'carnets.php') ? 'active' : ''; ?>">Carnets y Empresas</a>
        <a href="personalizacion.php" class="mobile-link <?php echo ($current_page == 'personalizacion.php') ? 'active' : ''; ?>">Personalización</a>
        <a href="tagua.php" class="mobile-link <?php echo ($current_page == 'tagua.php') ? 'active' : ''; ?>">Tagua</a>
        <a href="cotizacion.php" class="mobile-link <?php echo ($current_page == 'cotizacion.php') ? 'active' : ''; ?>">Cotizar</a>
        <a href="cotizacion.php" class="btn btn-primary" style="margin-top: 1.5rem; width: 100%; text-transform: none; font-weight: 600; padding: 12px 0;">Iniciar Cotización</a>
    </div>
</nav>
