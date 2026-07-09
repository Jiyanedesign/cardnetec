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
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : '';

$order_clause = "ORDER BY p.order_val ASC";
if ($sort === 'price_asc') {
    $order_clause = "ORDER BY p.price ASC";
}

try {
    if ($category_filter) {
        $stmtProds = $pdo->prepare("SELECT p.*, c.slug as cat_slug FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE p.is_active = 1 AND p.name NOT LIKE '%test%' AND p.name NOT LIKE '%Taza%' AND p.name NOT LIKE '%demo%' AND c.slug = ? $order_clause");
        $stmtProds->execute([$category_filter]);
    } else {
        $stmtProds = $pdo->query("SELECT p.*, c.slug as cat_slug FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE p.is_active = 1 AND p.name NOT LIKE '%test%' AND p.name NOT LIKE '%Taza%' AND p.name NOT LIKE '%demo%' $order_clause");
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
    <link rel="stylesheet" href="css/base.css?v=3.8">
    <link rel="stylesheet" href="css/layout.css?v=3.8">
    <link rel="stylesheet" href="css/components.css?v=3.8">
    <link rel="stylesheet" href="css/pages.css?v=3.8">
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
        
        /* Grilla responsiva de 2 por fila en móvil */
        @media (max-width: 767px) {
            .grid-3 {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 12px !important;
            }
            .product-card-body {
                padding: 0.85rem !important;
            }
            .product-card-title {
                font-size: 0.95rem !important;
                margin-bottom: 0.25rem !important;
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                min-height: 2.2em;
            }
            .product-card-price {
                font-size: 0.65rem !important;
            }
            .product-specs-badges {
                display: none !important;
            }
            .product-card-desc {
                display: none !important;
            }
            /* Acomodar botones de acción verticalmente para pantallas angostas */
            .product-card div[style*="display: flex; gap: 8px;"] {
                flex-direction: column !important;
                gap: 6px !important;
                padding: 0 0.85rem 0.85rem 0.85rem !important;
            }
            .product-card .btn {
                padding: 8px 4px !important;
                font-size: 0.72rem !important;
                width: 100% !important;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <!-- Encabezado de Página Interna -->
    <div class="page-header-block">
        <div class="container">
            <h1 class="page-header-title">Productos de identificación y personalización</h1>
            <p class="page-header-description">Explora carnets, credenciales, cintas, porta credenciales y productos personalizados para empresas, instituciones y eventos.</p>
        </div>
    </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="section-padding container">

        <!-- Selector de Ordenamiento Premium -->
        <div style="display: flex; justify-content: center; margin-bottom: 1.5rem; align-items: center; gap: 8px; flex-wrap: wrap;">
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Ordenar por:</span>
            <select id="sort-selector" style="padding: 6px 14px; border-radius: 20px; border: 1px solid var(--border); background: white; font-family: var(--font-body); font-size: 0.82rem; color: var(--text-dark); cursor: pointer; outline: none; transition: border-color 0.2s; font-weight: 500;" onchange="location.href = this.value;">
                <option value="productos.php?cat=<?php echo urlencode($category_filter); ?>" <?php echo ($sort != 'price_asc') ? 'selected' : ''; ?>>Relevancia (Destacados)</option>
                <option value="productos.php?cat=<?php echo urlencode($category_filter); ?>&sort=price_asc" <?php echo ($sort == 'price_asc') ? 'selected' : ''; ?>>Precio: Menor a Mayor</option>
            </select>
        </div>

        <!-- Selector de Ordenamiento -->
        <div style="display: flex; justify-content: flex-end; margin-bottom: 1rem; align-items: center; gap: 8px;">
            <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Ordenar:</span>
            <select id="sort-selector" onchange="location.href=this.value;" style="padding: 5px 12px; border-radius: 20px; border: 1px solid var(--border); background: white; font-family: var(--font-body); font-size: 0.78rem; color: var(--text-dark); cursor: pointer; outline: none; font-weight: 500; -webkit-appearance: none; appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2216%22%20height%3D%2216%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23999%22%20stroke-width%3D%222%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 8px center; padding-right: 28px;">
                <option value="productos.php<?php echo $category_filter ? '?cat='.urlencode($category_filter) : ''; ?>" <?php echo ($sort != 'price_asc') ? 'selected' : ''; ?>>Destacados</option>
                <option value="productos.php?<?php echo $category_filter ? 'cat='.urlencode($category_filter).'&' : ''; ?>sort=price_asc" <?php echo ($sort == 'price_asc') ? 'selected' : ''; ?>>Precio: Menor a Mayor</option>
            </select>
        </div>

        <!-- Barra de Filtros por Categoría (Chips con Scroll Horizontal en móvil) -->
        <div class="filter-bar" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 2.5rem; justify-content: center; overflow-x: auto; padding-bottom: 5px;">
            <button class="filter-btn active" data-filter="all" style="border:none; cursor:pointer; white-space:nowrap;">Todos</button>
            <button class="filter-btn" data-filter="Carnet" style="border:none; cursor:pointer; white-space:nowrap;">Carnets</button>
            <button class="filter-btn" data-filter="Credencial" style="border:none; cursor:pointer; white-space:nowrap;">Credenciales</button>
            <button class="filter-btn" data-filter="Cinta" style="border:none; cursor:pointer; white-space:nowrap;">Cintas</button>
            <button class="filter-btn" data-filter="Porta" style="border:none; cursor:pointer; white-space:nowrap;">Porta credenciales</button>
            <button class="filter-btn" data-filter="Accesorios" style="border:none; cursor:pointer; white-space:nowrap;">Accesorios</button>
            <button class="filter-btn" data-filter="PVC" style="border:none; cursor:pointer; white-space:nowrap;">Tarjetas PVC</button>
            <button class="filter-btn" data-filter="Personalización" style="border:none; cursor:pointer; white-space:nowrap;">Personalización</button>
            <button class="filter-btn" data-filter="Agenda" style="border:none; cursor:pointer; white-space:nowrap;">Agendas</button>
            <button class="filter-btn" data-filter="Llavero" style="border:none; cursor:pointer; white-space:nowrap;">Llaveros</button>
            <button class="filter-btn" data-filter="Termo" style="border:none; cursor:pointer; white-space:nowrap;">Termos</button>
            <button class="filter-btn" data-filter="Kit" style="border:none; cursor:pointer; white-space:nowrap;">Kits</button>
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
    <script src="js/main.js?v=3.8"></script>
    <script src="js/animations.js"></script>
</body>
</html>
