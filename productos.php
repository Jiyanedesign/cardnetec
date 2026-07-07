<?php
session_start();
require_once 'db.php';

// Obtener todas las categorías para los filtros
try {
    $stmtCats = $pdo->query("SELECT * FROM categorias WHERE is_active = 1 ORDER BY order_val ASC");
    $categories = $stmtCats->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

// Obtener productos filtrados si se solicita
$category_filter = isset($_GET['cat']) ? trim($_GET['cat']) : '';

try {
    if ($category_filter) {
        $stmtProds = $pdo->prepare("SELECT p.*, c.slug as cat_slug FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE p.is_active = 1 AND c.slug = ? ORDER BY p.order_val ASC");
        $stmtProds->execute([$category_filter]);
    } else {
        $stmtProds = $pdo->query("SELECT p.*, c.slug as cat_slug FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE p.is_active = 1 ORDER BY p.order_val ASC");
    }
    $products = $stmtProds->fetchAll();
} catch (PDOException $e) {
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos | CardNet.ec</title>
    <meta name="description" content="Explora nuestra selección de artículos promocionales listos para personalizar: termos, libretas y kits.">
    <link rel="canonical" href="https://cardnet.ec/productos.php">
    
    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=1.1.2">
    <link rel="stylesheet" href="css/layout.css?v=1.1.2">
    <link rel="stylesheet" href="css/components.css?v=1.1.2">
    <link rel="stylesheet" href="css/pages.css?v=1.1.2">
    <link rel="stylesheet" href="css/animations.css?v=1.1.2">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .filter-bar {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }
        .filter-btn {
            background-color: var(--surface-light);
            border: 1px solid var(--border);
            color: var(--text-dark);
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition-fast);
        }
        .filter-btn:hover, .filter-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
    </style>
</head>
<body>

    <!-- Barra de Anuncios Superior -->
    <div class="top-announcement-bar">
        Taller de personalización en Quito | Envíos corporativos asegurados a todo el Ecuador
    </div>

    <!-- Cabecera de Página -->
    <header class="main-header">
        <div class="container">
            <div class="header-middle">
                <a href="index.php" class="logo" aria-label="CardNet.ec Inicio">
                    <img src="images/logo.png" alt="CardNet.ec Logo" class="logo-img">
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
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72"/>
                            </svg>
                        </span>
                        <div class="status-text">
                            <h4>Asesoría Directa</h4>
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
                    <a href="index.php" class="nav-link">Inicio</a>
                    <a href="index.php#antes-despues" class="nav-link">Trabajos</a>
                    <a href="index.php#laser" class="nav-link">Grabado láser</a>
                    <a href="productos.php" class="nav-link active">Productos</a>
                    <a href="empresas.php" class="nav-link">Kits empresariales</a>
                    <a href="index.php#proceso" class="nav-link">Cómo pedir</a>
                    <a href="cotizacion.php" class="nav-link">Cotizar <?php
                    $c_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                    if ($c_count > 0) {
                        echo '<span style="background: var(--primary); color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.7rem; font-weight: bold; margin-left: 3px;">' . $c_count . '</span>';
                    }
                    ?></a>
                </nav>
                <div class="header-bottom-actions" style="display: flex; align-items: center; gap: 15px;">
                    <!-- Icono de Lista de Cotización Flotante con Dropdown -->
                    <div class="header-cart-dropdown-wrapper">
                        <a href="cotizacion.php" class="cart-icon-btn" aria-label="Ver mi lista de cotización">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                            <?php if ($c_count > 0): ?>
                                <span class="cart-badge-count"><?php echo $c_count; ?></span>
                            <?php endif; ?>
                        </a>
                        
                        <?php if ($c_count > 0): ?>
                            <div class="minicart-dropdown">
                                <div class="minicart-items">
                                    <?php 
                                    $m_total = 0;
                                    foreach ($_SESSION['cart'] as $m_item): 
                                        $m_total += $m_item['subtotal'];
                                    ?>
                                        <div class="minicart-item">
                                            <?php if (!empty($m_item['snapshot'])): ?>
                                                <img src="<?php echo htmlspecialchars($m_item['snapshot']); ?>" width="50" height="50" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0; display: block; flex-shrink: 0;">
                                            <?php else: ?>
                                                <div class="minicart-item-img" style="background:#f4f4f4; display:flex; align-items:center; justify-content:center; font-size:0.6rem; color:#888; width: 50px; height: 50px; flex-shrink: 0; border-radius: 4px;">Liso</div>
                                            <?php endif; ?>
                                            <div class="minicart-item-info">
                                                <span class="minicart-item-name"><?php echo htmlspecialchars($m_item['name']); ?></span>
                                                <span class="minicart-item-meta"><?php echo $m_item['qty']; ?> uds x $<?php echo number_format($m_item['price'], 2); ?></span>
                                            </div>
                                            <div class="minicart-item-subtotal">
                                                $<?php echo number_format($m_item['subtotal'], 0); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="minicart-total">
                                    <span class="minicart-total-label">Total Est.</span>
                                    <span class="minicart-total-value">$<?php echo number_format($m_total, 2); ?></span>
                                </div>
                                <a href="cotizacion.php" class="btn btn-primary" style="width:100%; text-align:center; padding:10px 0; font-size:0.8rem; text-decoration:none;">Finalizar Cotización</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Botón Cotizar Principal -->
                    <a href="cotizacion.php" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">Cotizar</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Menú Móvil -->
    <div class="mobile-nav-overlay"></div>
    <nav id="mobile-nav" class="mobile-nav" aria-label="Navegación móvil">
        <a href="index.php" class="mobile-link">Inicio</a>
        <a href="index.php#antes-despues" class="mobile-link">Trabajos</a>
        <a href="index.php#laser" class="mobile-link">Grabado láser</a>
        <a href="productos.php" class="mobile-link active">Productos</a>
        <a href="empresas.php" class="mobile-link">Kits empresariales</a>
        <a href="index.php#proceso" class="mobile-link">Cómo pedir</a>
        <a href="cotizacion.php" class="btn btn-primary" style="margin-top: 1rem; width: 100%;">Cotizar</a>
    </nav>

    <!-- Encabezado de Página Interna -->
    <div class="page-header-block">
        <div class="container">
            <h1 class="page-header-title">Catálogo de Productos</h1>
            <p class="page-header-description">Seleccionamos soportes de gran tacto y durabilidad listos para recibir tu logotipo corporativo bajo estándares profesionales.</p>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="section-padding container">

        <!-- Barra de Filtros por Categoría -->
        <div class="filter-bar">
            <a href="productos.php" class="filter-btn <?php echo !$category_filter ? 'active' : ''; ?>">Todos los artículos</a>
            <?php foreach ($categories as $cat): ?>
                <a href="productos.php?cat=<?php echo urlencode($cat['slug']); ?>" class="filter-btn <?php echo $category_filter === $cat['slug'] ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div class="grid-3">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $prod): ?>
                    <a href="producto.php?slug=<?php echo urlencode($prod['slug']); ?>" class="product-card reveal-on-scroll" style="text-decoration: none; color: inherit; display: flex; flex-direction: column;">
                        <div class="product-card-image-wrap">
                            <div class="image-placeholder theme-gray">
                                <?php if ($prod['image_main']): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($prod['image_main']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                <?php else: ?>
                                    <div class="image-placeholder-inner">
                                        <svg class="image-placeholder-icon" viewBox="0 0 24 24" width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                        </svg>
                                        <span class="image-placeholder-text"><?php echo htmlspecialchars($prod['name']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="product-card-body">
                            <span class="product-card-price"><?php echo htmlspecialchars($prod['category']); ?></span>
                            <h3 class="product-card-title" style="margin-bottom: 0.25rem; font-family: var(--font-heading);"><?php echo htmlspecialchars($prod['name']); ?></h3>
                            
                            <!-- Badges de especificaciones técnicas premium -->
                            <div class="product-specs-badges" style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 0.85rem; margin-top: 0.25rem;">
                                <?php if (stripos($prod['category'], 'tarjeta') !== false || stripos($prod['name'], 'tarjeta') !== false): ?>
                                    <span style="font-size: 0.65rem; background: rgba(0,0,0,0.03); color: var(--text-muted); padding: 3px 8px; border-radius: 20px; font-weight: 500; border: 1px solid rgba(0,0,0,0.03); letter-spacing: 0.02em;">Mate & Grabado</span>
                                    <span style="font-size: 0.65rem; background: rgba(99, 174, 44, 0.08); color: var(--primary-hover); padding: 3px 8px; border-radius: 20px; font-weight: 600; border: 1px solid rgba(99, 174, 44, 0.1); letter-spacing: 0.02em;">Metal 316L</span>
                                <?php elseif (stripos($prod['category'], 'termo') !== false || stripos($prod['name'], 'termo') !== false): ?>
                                    <span style="font-size: 0.65rem; background: rgba(0,0,0,0.03); color: var(--text-muted); padding: 3px 8px; border-radius: 20px; font-weight: 500; border: 1px solid rgba(0,0,0,0.03); letter-spacing: 0.02em;">Doble Pared</span>
                                    <span style="font-size: 0.65rem; background: rgba(99, 174, 44, 0.08); color: var(--primary-hover); padding: 3px 8px; border-radius: 20px; font-weight: 600; border: 1px solid rgba(99, 174, 44, 0.1); letter-spacing: 0.02em;">Láser CO2</span>
                                <?php else: ?>
                                    <span style="font-size: 0.65rem; background: rgba(0,0,0,0.03); color: var(--text-muted); padding: 3px 8px; border-radius: 20px; font-weight: 500; border: 1px solid rgba(0,0,0,0.03); letter-spacing: 0.02em;">Edición B2B</span>
                                    <span style="font-size: 0.65rem; background: rgba(99, 174, 44, 0.08); color: var(--primary-hover); padding: 3px 8px; border-radius: 20px; font-weight: 600; border: 1px solid rgba(99, 174, 44, 0.1); letter-spacing: 0.02em;">Precisión Alta</span>
                                <?php endif; ?>
                            </div>

                            <p class="product-card-desc"><?php echo htmlspecialchars($prod['description_short']); ?></p>
                            <span class="btn btn-secondary" style="margin-top: auto; padding: 0.5rem 1rem; font-size: 0.8rem; text-align: center; display: block; width: 100%;">
                                Ver producto y simular
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 3rem 0;">
                    No se encontraron productos en esta categoría.
                </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container footer-top section-padding" style="padding-top: 3rem; padding-bottom: 3rem;">
            <div class="footer-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 40px;">
                <div class="footer-brand-column">
                    <a href="index.php" class="logo footer-logo" aria-label="CardNet.ec Inicio">
                        <img src="images/logo.png" alt="CardNet.ec Logo" class="logo-img">
                    </a>
                    <p class="footer-description" style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-top: 1rem;">Especialistas en identificación, grabado láser y personalización corporativa para empresas, instituciones y eventos en Ecuador.</p>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading" style="font-size: 0.9rem; font-family: var(--font-heading); margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dark);">Líneas de Negocio</h3>
                    <nav class="footer-links" aria-label="Enlaces de productos" style="display: flex; flex-direction: column; gap: 8px; font-size: 0.85rem;">
                        <a href="productos.php#termos" class="footer-link">Termos Grabados</a>
                        <a href="productos.php#oficina" class="footer-link">Agendas Ejecutivas</a>
                        <a href="empresas.php" class="footer-link">Kits Corporativos</a>
                        <a href="productos.php#placas" class="footer-link">Placas y Reconocimientos</a>
                        <a href="simulador-carnets.php" class="footer-link">Identificación & Carnets PVC</a>
                    </nav>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading" style="font-size: 0.9rem; font-family: var(--font-heading); margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dark);">Enlaces Útiles</h3>
                    <nav class="footer-links" aria-label="Enlaces de navegación" style="display: flex; flex-direction: column; gap: 8px; font-size: 0.85rem;">
                        <a href="index.php#antes-despues" class="footer-link">Trabajos Realizados</a>
                        <a href="index.php#proceso" class="footer-link">Cómo Pedir</a>
                        <a href="faq.php" class="footer-link">Preguntas Frecuentes</a>
                        <a href="contacto.php" class="footer-link">Soporte de Taller</a>
                    </nav>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading" style="font-size: 0.9rem; font-family: var(--font-heading); margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dark);">Contacto & Horario</h3>
                    <div class="footer-contact-info" style="display: flex; flex-direction: column; gap: 10px; font-size: 0.85rem; color: var(--text-muted);">
                        <div class="footer-contact-item" style="display: flex; align-items: center; gap: 8px;">
                            <svg class="footer-contact-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span>WhatsApp: +593 90 000 0000</span>
                        </div>
                        <div class="footer-contact-item" style="display: flex; align-items: center; gap: 8px;">
                            <svg class="footer-contact-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="m22 6-10 7L2 6"/></svg>
                            <span>info@cardnet.ec</span>
                        </div>
                        <div class="footer-contact-item" style="display: flex; align-items: center; gap: 8px; margin-top: 5px;">
                            <span>⏱️ Lunes a Viernes · 09:00 a 18:00</span>
                        </div>
                        <div class="footer-contact-item" style="display: flex; align-items: center; gap: 8px;">
                            <span>📍 Quito · Envíos a nivel nacional</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom" style="border-top: 1px solid var(--border); padding-top: 1.5rem; padding-bottom: 1.5rem;">
            <div class="container footer-bottom-flex" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <p style="font-size: 0.8rem; color: var(--text-muted);">&copy; 2026 CardNet.ec — Detalles personalizados para marcas que cuidan su presentación.</p>
                <div class="footer-bottom-links" style="display: flex; gap: 15px; font-size: 0.8rem;">
                    <a href="faq.php" class="footer-bottom-link">Preguntas Frecuentes</a>
                    <a href="contacto.php" class="footer-bottom-link">Soporte de Taller</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts Modulares -->
    <script src="js/main.js"></script>
    <script src="js/animations.js"></script>
</body>
</html>
