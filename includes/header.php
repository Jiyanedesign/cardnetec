<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$c_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Barra de Anuncios Superior -->
<div class="top-announcement-bar">
    Taller de personalización en Ecuador · Grabado láser, carnets y productos corporativos
</div>

<!-- Cabecera de Página -->
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
                <input class="search-input" type="text" placeholder="Buscar termos, agendas, placas...">
            </div>

            <div class="header-contact-status">
                <a href="https://wa.me/593000000000" target="_blank" rel="noopener noreferrer" style="text-decoration: none; color: inherit;">
                    <div class="contact-status-item">
                        <span class="status-icon-wrap">
                            <svg style="width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2.5;" viewBox="0 0 24 24">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72"/>
                            </svg>
                        </span>
                        <div class="status-text">
                            <h4>Asesoría personalizada</h4>
                            <p style="font-size: 0.8rem; font-weight: 500; color: var(--primary);">+593 00 000 0000</p>
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
                <a href="index.php#laser" class="nav-link">Grabado láser</a>
                <a href="empresas.php" class="nav-link <?php echo ($current_page == 'empresas.php') ? 'active' : ''; ?>">Kits empresariales</a>
                <a href="index.php#antes-despues" class="nav-link">Personalización</a>
                <a href="index.php#proceso" class="nav-link">Cómo pedir</a>
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
    <a href="index.php" class="mobile-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Inicio</a>
    <a href="productos.php" class="mobile-link <?php echo ($current_page == 'productos.php' || $current_page == 'producto.php') ? 'active' : ''; ?>">Productos</a>
    <a href="index.php#laser" class="mobile-link">Grabado láser</a>
    <a href="empresas.php" class="mobile-link <?php echo ($current_page == 'empresas.php') ? 'active' : ''; ?>">Kits empresariales</a>
    <a href="index.php#antes-despues" class="mobile-link">Personalización</a>
    <a href="index.php#proceso" class="mobile-link">Cómo pedir</a>
    <a href="cotizacion.php" class="mobile-link <?php echo ($current_page == 'cotizacion.php') ? 'active' : ''; ?>">Cotizar</a>
    <a href="cotizacion.php" class="btn btn-primary" style="margin-top: 1rem; width: 100%; text-transform: none;">Cotizar</a>
</nav>
