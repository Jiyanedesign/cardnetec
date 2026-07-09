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
        $stmtProds = $pdo->prepare("SELECT p.*, c.slug as cat_slug FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE p.is_active = 1 AND p.name NOT LIKE '%test%' AND p.name NOT LIKE '%Taza%' AND p.name NOT LIKE '%demo%' AND c.slug = ? ORDER BY p.order_val ASC");
        $stmtProds->execute([$category_filter]);
    } else {
        $stmtProds = $pdo->query("SELECT p.*, c.slug as cat_slug FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE p.is_active = 1 AND p.name NOT LIKE '%test%' AND p.name NOT LIKE '%Taza%' AND p.name NOT LIKE '%demo%' ORDER BY p.order_val ASC");
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
    <title>Productos personalizados | CardNet.ec</title>
    <meta name="description" content="Explora termos, agendas, carnets, placas, kits y productos listos para personalizar con grabado láser y acabados corporativos.">
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

    <?php include 'includes/header.php'; ?>

    <!-- Encabezado de Página Interna -->
    <div class="page-header-block">
        <div class="container">
            <h1 class="page-header-title">Productos personalizados</h1>
            <p class="page-header-description">Explora productos listos para personalizar con grabado láser, carnets, kits y acabados corporativos.</p>
        </div>
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
            <button class="filter-btn" data-filter="Carnet" style="border:none; cursor:pointer;">Carnets</button>
            <button class="filter-btn" data-filter="Caja" style="border:none; cursor:pointer;">Cajas</button>
            <button class="filter-btn" data-filter="Llavero" style="border:none; cursor:pointer;">Llaveros</button>
            <button class="filter-btn" data-filter="Esfero" style="border:none; cursor:pointer;">Esferos</button>
            <button class="filter-btn" data-filter="Reconocimiento" style="border:none; cursor:pointer;">Reconocimientos</button>
        </div>
        
        <div class="grid-3">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $prod): ?>
                    <?php 
                    $enriched = enrichProduct($prod);
                    ?>
                    <?php 
                    // Inicializar $enriched para evitar errores 500 de variable no definida
                    $enriched = enrichProduct($prod);
                    // Obtener la galería de imágenes del producto
                    $prod_gallery = json_decode($prod['gallery_images'], true) ?: [];
                    if ($prod['image_main']) {
                        array_unshift($prod_gallery, $prod['image_main']);
                    }
                    $prod_gallery = array_unique($prod_gallery);
                    // Convertir a rutas relativas
                    $prod_gallery_paths = array_map(function($img) {
                        return 'uploads/' . $img;
                    }, $prod_gallery);
                    $prod_gallery_json = json_encode(array_values($prod_gallery_paths));
                    ?>
                    <div class="product-card catalog-product-item reveal-on-scroll" 
                         data-name="<?php echo htmlspecialchars($enriched['name']); ?>" 
                         data-category="<?php echo htmlspecialchars($enriched['category']); ?>" 
                         data-material="<?php echo htmlspecialchars($enriched['material']); ?>" 
                         data-technique="<?php echo htmlspecialchars($enriched['technique']); ?>" 
                         data-use="<?php echo htmlspecialchars($enriched['use']); ?>"
                         data-gallery='<?php echo htmlspecialchars($prod_gallery_json, ENT_QUOTES, 'UTF-8'); ?>'
                         style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; padding: 0; transition: transform 0.25s ease, border-color 0.25s ease;">
                        
                        <a href="producto.php?slug=<?php echo htmlspecialchars($prod['slug']); ?>" style="text-decoration: none; color: inherit; display: block; flex-grow: 1;">
                            <div class="product-card-image-wrap" style="position: relative; overflow: hidden; aspect-ratio: 1.15; background: var(--surface-light); border-bottom: 1px solid var(--border);">
                                <?php if ($prod['image_main']): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($prod['image_main']); ?>" style="width:100%; height:100%; object-fit:cover; transition: transform 0.4s ease;" loading="lazy" alt="<?php echo htmlspecialchars($prod['name']); ?>">
                                <?php else: ?>
                                    <div class="image-placeholder-inner" style="background: var(--surface-light); height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                        <svg class="image-placeholder-icon" viewBox="0 0 24 24" width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.2" style="opacity: 0.3;">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        </svg>
                                        <span class="image-placeholder-text" style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; letter-spacing: 0.05em; display: block; margin-top: 5px;"><?php echo htmlspecialchars($prod['name']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="product-card-body" style="padding: 1.25rem; display: flex; flex-direction: column;">
                                <span class="product-card-price" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); font-weight: 600; display: block; margin-bottom: 4px;"><?php echo htmlspecialchars($enriched['category']); ?></span>
                                <h3 class="product-card-title" style="margin-bottom: 0.5rem; font-size: 1.15rem; font-family: var(--font-heading); color: var(--dark); font-weight: 500; line-height: 1.2;"><?php echo htmlspecialchars($enriched['name']); ?></h3>
                                
                                <div class="product-specs-badges" style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 0.85rem; margin-top: 0.25rem;">
                                    <span style="font-size: 0.65rem; background: rgba(0,0,0,0.03); color: var(--text-muted); padding: 3px 8px; border-radius: 20px; font-weight: 500; border: 1px solid rgba(0,0,0,0.02);"><?php echo htmlspecialchars($enriched['material']); ?></span>
                                    <span style="font-size: 0.65rem; background: rgba(99, 174, 44, 0.08); color: var(--primary-hover); padding: 3px 8px; border-radius: 20px; font-weight: 600; border: 1px solid rgba(99, 174, 44, 0.1);"><?php echo htmlspecialchars($enriched['technique']); ?></span>
                                </div>
                                <p class="product-card-desc" style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem;"><?php echo htmlspecialchars($prod['description_short']); ?></p>
                            </div>
                        </a>
                        
                        <div style="display: flex; gap: 8px; margin-top: auto; padding: 0 1.25rem 1.25rem 1.25rem;">
                            <?php
                            $btn_text = 'Cotizar este producto';
                            if (stripos($prod['slug'], 'termo') !== false) {
                                $btn_text = 'Cotizar este producto';
                            } elseif (stripos($prod['slug'], 'agenda') !== false) {
                                $btn_text = 'Quiero algo similar';
                            } elseif (stripos($prod['slug'], 'carnet') !== false || stripos($prod['slug'], 'credencial') !== false) {
                                $btn_text = 'Explorar opciones';
                            } elseif (stripos($prod['slug'], 'kit') !== false) {
                                $btn_text = 'Armar un kit';
                            }
                            ?>
                            <button class="btn btn-primary btn-add-to-quote" 
                                    data-slug="<?php echo htmlspecialchars($prod['slug']); ?>" 
                                    data-name="<?php echo htmlspecialchars($prod['name']); ?>" 
                                    data-price="<?php echo (float)$prod['price']; ?>"
                                    style="flex-grow: 1; padding: 8px 12px; font-size: 0.78rem; font-weight: 600; white-space: nowrap; border: none; cursor: pointer;">
                                <?php echo $btn_text; ?>
                            </button>
                            <a href="producto.php?slug=<?php echo htmlspecialchars($prod['slug']); ?>" class="btn btn-secondary" 
                                    style="padding: 8px 12px; font-size: 0.78rem; font-weight: 500; border: 1px solid var(--border); text-decoration: none; text-align: center; color: var(--dark); cursor: pointer; background: white;">
                                Ver detalles
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 4rem 1rem; background: white; border: 1px solid var(--border); border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
                    <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.2" style="color: var(--text-muted); opacity: 0.5; margin-bottom: 1rem;">
                        <circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; margin-bottom: 0.5rem; font-weight: 500;">No encontramos productos con este filtro</h3>
                    <p style="font-size: 0.88rem; margin-bottom: 1.5rem;">Puedes elegir otra categoría o cotizar una idea desde cero.</p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <a href="productos.php" class="btn btn-secondary" style="font-size: 0.8rem; padding: 8px 16px;">Ver todos</a>
                        <a href="cotizacion.php" class="btn btn-primary" style="font-size: 0.8rem; padding: 8px 16px;">Cotizar una idea</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- Sección CTA Final -->
    <section class="section-padding" style="background-color: var(--surface-light); border-top: 1px solid var(--border);">
        <div class="container" style="text-align: center; max-width: 650px;">
            <h2 style="font-family: var(--font-heading); font-size: 1.85rem; font-weight: 500; color: var(--dark); margin-bottom: 1rem;">¿Tienes un producto en mente?</h2>
            <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.6; margin-bottom: 1.75rem;">Envíanos tu idea, tu logo o una referencia. Te ayudamos a elegir el producto y el acabado adecuado para representar a tu marca.</p>
            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <a href="cotizacion.php" class="btn btn-primary" style="padding: 10px 22px; font-size: 0.85rem;">Iniciar cotización</a>
                <a href="https://wa.me/593000000000" class="btn btn-secondary" target="_blank" rel="noopener noreferrer" style="padding: 10px 22px; font-size: 0.85rem; background: white;">Enviar mi logo</a>
            </div>
        </div>
    </section>

    <!-- Pie de Página -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts Modulares -->
    <script src="js/main.js?v=3.5"></script>
    <script src="js/animations.js"></script>
</body>
</html>
