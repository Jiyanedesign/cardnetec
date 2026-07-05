<?php
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
                    <a href="index.php#destacados" class="nav-link">Destacados</a>
                    <a href="index.php#laser" class="nav-link">Grabado láser</a>
                    <a href="productos.php" class="nav-link active">Productos</a>
                    <a href="empresas.php" class="nav-link">Kits corporativos</a>
                    <a href="cotizacion.php" class="nav-link">Cotizar</a>
                </nav>
                <div class="header-bottom-actions">
                    <a href="cotizacion.php" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">Cotizar</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Menú Móvil -->
    <div class="mobile-nav-overlay"></div>
    <nav id="mobile-nav" class="mobile-nav" aria-label="Navegación móvil">
        <a href="index.php" class="mobile-link">Inicio</a>
        <a href="index.php#destacados" class="mobile-link">Destacados</a>
        <a href="index.php#laser" class="mobile-link">Grabado láser</a>
        <a href="productos.php" class="mobile-link active">Productos</a>
        <a href="empresas.php" class="mobile-link">Kits corporativos</a>
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
                    <div class="product-card reveal-on-scroll">
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
                            <h3 class="product-card-title"><?php echo htmlspecialchars($prod['name']); ?></h3>
                            <p class="product-card-desc"><?php echo htmlspecialchars($prod['description_short']); ?></p>
                            <a href="producto.php?slug=<?php echo urlencode($prod['slug']); ?>" class="btn btn-secondary" style="margin-top: auto; padding: 0.5rem 1rem; font-size: 0.8rem; text-align: center;">
                                Ver producto y simular
                            </a>
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

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container footer-top section-padding">
            <div class="footer-grid">
                <div class="footer-brand-column">
                    <a href="index.php" class="logo footer-logo" aria-label="CardNet.ec Inicio">
                        <img src="images/logo.png" alt="CardNet.ec Logo" class="logo-img">
                    </a>
                    <p class="footer-description">Grabado láser y personalización corporativa en Ecuador.</p>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading">Productos</h3>
                    <nav class="footer-links" aria-label="Enlaces de productos">
                        <a href="productos.php" class="footer-link">Todo el Catálogo</a>
                    </nav>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading">Contacto</h3>
                    <div class="footer-contact-info">
                        <div class="footer-contact-item">
                            <span>WhatsApp: +593 90 000 0000</span>
                        </div>
                        <div class="footer-contact-item">
                            <span>Correo: info@cardnet.ec</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts Modulares -->
    <script src="js/main.js"></script>
    <script src="js/animations.js"></script>
</body>
</html>
