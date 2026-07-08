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
    <link rel="icon" type="image/png" href="favicon.png?v=2.0">
    <link rel="apple-touch-icon" href="favicon.png?v=2.0">
    
    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=2.0">
    <link rel="stylesheet" href="css/layout.css?v=2.0">
    <link rel="stylesheet" href="css/components.css?v=2.0">
    <link rel="stylesheet" href="css/pages.css?v=2.0">
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
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72"/>
                            </svg>
                        </span>
                        <div class="status-text">
                            <h4>Asesoría Directa</h4>
                            <p style="font-size: 0.8rem; font-weight: 500; color: var(--primary);">Contacto por WhatsApp</p>
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
            <h1 class="page-header-title">Catálogo de Productos</h1>
            <p class="page-header-description">Seleccionamos soportes de gran tacto y durabilidad listos para recibir tu logotipo corporativo bajo estándares profesionales.</p>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="section-padding container">

        <!-- Barra de Filtros por Categoría y Materiales -->
        <div class="filter-bar" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 2.5rem; justify-content: center;">
            <button class="filter-btn active" data-filter="all" style="border:none; cursor:pointer;">Todos</button>
            <button class="filter-btn" data-filter="Termo" style="border:none; cursor:pointer;">Termos</button>
            <button class="filter-btn" data-filter="Agenda" style="border:none; cursor:pointer;">Agendas</button>
            <button class="filter-btn" data-filter="Kit" style="border:none; cursor:pointer;">Kits</button>
            <button class="filter-btn" data-filter="Placa" style="border:none; cursor:pointer;">Placas</button>
            <button class="filter-btn" data-filter="Llavero" style="border:none; cursor:pointer;">Llaveros</button>
            <button class="filter-btn" data-filter="Identificación" style="border:none; cursor:pointer;">Identificación</button>
            <button class="filter-btn" data-filter="Acero" style="border:none; cursor:pointer;">Acero</button>
            <button class="filter-btn" data-filter="Madera" style="border:none; cursor:pointer;">Madera</button>
            <button class="filter-btn" data-filter="Acrílico" style="border:none; cursor:pointer;">Acrílico</button>
            <button class="filter-btn" data-filter="Cuero" style="border:none; cursor:pointer;">Cuero / PU</button>
        </div>
        
        <div class="grid-3">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $prod): ?>
                    <?php 
                    $enriched = enrichProduct($prod);
                    ?>
                    <div class="product-card catalog-product-item reveal-on-scroll" 
                         data-name="<?php echo htmlspecialchars($enriched['name']); ?>" 
                         data-category="<?php echo htmlspecialchars($enriched['category']); ?>" 
                         data-material="<?php echo htmlspecialchars($enriched['material']); ?>" 
                         data-technique="<?php echo htmlspecialchars($enriched['technique']); ?>" 
                         data-use="<?php echo htmlspecialchars($enriched['use']); ?>"
                         style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; padding: 0;">
                        <div class="product-card-image-wrap" style="position: relative;">
                            <div class="image-placeholder theme-gray" style="border-radius: 0; aspect-ratio: 1.15;">
                                <?php if ($prod['image_main']): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($prod['image_main']); ?>" style="width:100%; height:100%; object-fit:cover;" loading="lazy" alt="<?php echo htmlspecialchars($prod['name']); ?>">
                                <?php else: ?>
                                    <div class="image-placeholder-inner" style="background: var(--surface-light);">
                                        <svg class="image-placeholder-icon" viewBox="0 0 24 24" width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.2" style="opacity: 0.3;">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        </svg>
                                        <span class="image-placeholder-text" style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; letter-spacing: 0.05em; display: block; margin-top: 5px;"><?php echo htmlspecialchars($prod['name']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="product-card-body" style="padding: 1.25rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <span class="product-card-price" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); font-weight: 600; display: block; margin-bottom: 4px;"><?php echo htmlspecialchars($enriched['category']); ?></span>
                            <h3 class="product-card-title" style="margin-bottom: 0.5rem; font-size: 1.15rem; font-family: var(--font-heading); color: var(--dark); font-weight: 500; line-height: 1.2;"><?php echo htmlspecialchars($enriched['name']); ?></h3>
                            
                            <!-- Badges de especificaciones técnicas premium -->
                            <div class="product-specs-badges" style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 0.85rem; margin-top: 0.25rem;">
                                <span style="font-size: 0.65rem; background: rgba(0,0,0,0.03); color: var(--text-muted); padding: 3px 8px; border-radius: 20px; font-weight: 500; border: 1px solid rgba(0,0,0,0.02);"><?php echo htmlspecialchars($enriched['material']); ?></span>
                                <span style="font-size: 0.65rem; background: rgba(99, 174, 44, 0.08); color: var(--primary-hover); padding: 3px 8px; border-radius: 20px; font-weight: 600; border: 1px solid rgba(99, 174, 44, 0.1);"><?php echo htmlspecialchars($enriched['technique']); ?></span>
                            </div>

                            <p class="product-card-desc" style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex-grow: 1;"><?php echo htmlspecialchars($prod['description_short']); ?></p>
                            
                            <div style="display: flex; gap: 8px; margin-top: auto;">
                                <button class="btn btn-primary btn-add-to-quote" 
                                        data-slug="<?php echo htmlspecialchars($prod['slug']); ?>" 
                                        data-name="<?php echo htmlspecialchars($prod['name']); ?>" 
                                        data-price="<?php echo (float)$prod['price']; ?>"
                                        style="flex-grow: 1; padding: 8px 12px; font-size: 0.78rem; font-weight: 600; white-space: nowrap; border: none; cursor: pointer;">
                                    Agregar a cotización
                                </button>
                                <button class="btn btn-secondary btn-view-details" 
                                        data-slug="<?php echo htmlspecialchars($prod['slug']); ?>"
                                        data-name="<?php echo htmlspecialchars($prod['name']); ?>"
                                        data-category="<?php echo htmlspecialchars($enriched['category']); ?>"
                                        data-material="<?php echo htmlspecialchars($enriched['material']); ?>"
                                        data-technique="<?php echo htmlspecialchars($enriched['technique']); ?>"
                                        data-use="<?php echo htmlspecialchars($enriched['use']); ?>"
                                        data-desc="<?php echo htmlspecialchars($enriched['details']); ?>"
                                        style="padding: 8px 12px; font-size: 0.78rem; font-weight: 500; border: 1px solid var(--border); cursor: pointer; background: white;">
                                    Ver detalles
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 3rem 0;">
                    No se encontraron productos en esta categoría.
                </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- Footer Corporativo -->
    <footer class="main-footer">
        <div class="container footer-top section-padding" style="padding-top: 3rem; padding-bottom: 3rem;">
            <div class="footer-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 40px;">
                <div class="footer-brand-column">
                    <a href="index.php" class="logo footer-logo" aria-label="CardNet.ec Inicio">
                        <img src="images/logo.png?v=2.0" alt="CardNet.ec Logo" class="logo-img">
                    </a>
                    <p class="footer-description" style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-top: 1rem;">Identificación, grabado láser y personalización corporativa para empresas, instituciones y eventos.</p>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading" style="font-size: 0.9rem; font-family: var(--font-heading); margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dark);">Líneas de Trabajo</h3>
                    <nav class="footer-links" aria-label="Enlaces de productos" style="display: flex; flex-direction: column; gap: 8px; font-size: 0.85rem;">
                        <a href="productos.php" class="footer-link">Grabado láser</a>
                        <a href="productos.php" class="footer-link">Identificación corporativa</a>
                        <a href="productos.php" class="footer-link">Kits empresariales</a>
                        <a href="productos.php" class="footer-link">Placas y reconocimientos</a>
                        <a href="productos.php" class="footer-link">Productos personalizados</a>
                    </nav>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading" style="font-size: 0.9rem; font-family: var(--font-heading); margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dark);">Para Quién Trabajamos</h3>
                    <nav class="footer-links" aria-label="Enlaces de navegación" style="display: flex; flex-direction: column; gap: 8px; font-size: 0.85rem;">
                        <span class="footer-link" style="color: var(--text-muted); cursor: default;">Empresas</span>
                        <span class="footer-link" style="color: var(--text-muted); cursor: default;">Instituciones</span>
                        <span class="footer-link" style="color: var(--text-muted); cursor: default;">Eventos</span>
                        <span class="footer-link" style="color: var(--text-muted); cursor: default;">Marcas</span>
                        <span class="footer-link" style="color: var(--text-muted); cursor: default;">Equipos comerciales</span>
                    </nav>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading" style="font-size: 0.9rem; font-family: var(--font-heading); margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dark);">Solicitudes</h3>
                    <div class="footer-contact-info" style="display: flex; flex-direction: column; gap: 10px; font-size: 0.85rem; color: var(--text-muted);">
                        <a href="cotizacion.php" class="footer-link">Cotizar productos</a>
                        <a href="cotizacion.php" class="footer-link">Enviar logo</a>
                        <button class="footer-link toggle-quote-drawer-btn" style="background: none; border: none; text-align: left; padding: 0; font-family: inherit; font-size: inherit; color: inherit; cursor: pointer;">Preparar pedido corporativo</button>
                        <a href="index.php#preguntas-frecuentes" class="footer-link">Preguntas frecuentes</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom" style="border-top: 1px solid var(--border); padding-top: 1.5rem; padding-bottom: 1.5rem;">
            <div class="container footer-bottom-flex" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <p style="font-size: 0.8rem; color: var(--text-muted);">&copy; CardNet.ec. Todos los derechos reservados. Sitio desarrollado para presentación corporativa y solicitudes de cotización.</p>
                <div class="footer-bottom-links" style="display: flex; gap: 15px; font-size: 0.8rem;">
                    <span style="color: var(--text-muted);">Contacto disponible próximamente</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts Modulares -->
    <script src="js/main.js"></script>
    <script src="js/animations.js"></script>
</body>
</html>
