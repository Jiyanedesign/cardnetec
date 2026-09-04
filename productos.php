<?php
session_start();
require_once 'db.php';

// Obtener todas las categorías para los filtros (Tagua siempre al final)
try {
    $stmtCats = $pdo->query("SELECT * FROM categorias WHERE is_active = 1 ORDER BY CASE WHEN slug = 'tagua' THEN 999999 WHEN order_val IS NULL OR order_val = 0 THEN 999998 ELSE order_val END ASC, id ASC");
    $categories = $stmtCats->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

// Obtener todas las etiquetas para los badges
try {
    $stmtTags = $pdo->query("SELECT * FROM etiquetas");
    $all_tags = [];
    foreach ($stmtTags->fetchAll() as $t) {
        $all_tags[$t['id']] = $t;
    }
} catch (PDOException $e) {
    $all_tags = [];
}

// Cargar configuraciones del sitio
$site_settings = getSiteSettings($pdo);
$prod_wa_clean = cleanWhatsAppNumber($site_settings['whatsapp'] ?? '');

// Obtener productos filtrados o buscados si se solicita
$category_filter = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : '';
$search_query = isset($_GET['q']) ? trim($_GET['q']) : (isset($_GET['search']) ? trim($_GET['search']) : '');

$order_clause = "ORDER BY CASE WHEN p.order_val IS NULL OR p.order_val = 0 THEN 999999 ELSE p.order_val END ASC, p.id DESC";
if ($sort === 'price_asc') {
    $order_clause = "ORDER BY p.price ASC";
}

try {
    if ($search_query !== '') {
        $q_lower = mb_strtolower($search_query);
        $terms = [$search_query];
        
        $synonyms_map = [
            'carnet' => ['credencial', 'portacredencial', 'pvc'],
            'carnets' => ['credencial', 'portacredencial', 'pvc'],
            'gafete' => ['credencial', 'portacredencial'],
            'gafetes' => ['credencial', 'portacredencial'],
            'fotocheck' => ['credencial', 'portacredencial'],
            'lanyard' => ['cinta', 'cordon'],
            'lanyards' => ['cinta', 'cordon'],
            'colgante' => ['cinta', 'cordon'],
            'tagua' => ['aretes', 'collar'],
            'placas' => ['placa'],
            'boligrafo' => ['esfero'],
            'lapicero' => ['esfero'],
            'llaveros' => ['llavero'],
            'cajas' => ['caja']
        ];

        foreach ($synonyms_map as $trigger => $syns) {
            if (str_contains($q_lower, $trigger)) {
                foreach ($syns as $syn) {
                    if (!in_array($syn, $terms)) {
                        $terms[] = $syn;
                    }
                }
            }
        }

        $whereParts = [];
        $params = [];
        foreach ($terms as $t) {
            $st = '%' . $t . '%';
            $whereParts[] = "(p.name LIKE ? OR p.description_short LIKE ? OR p.description_long LIKE ? OR p.sku LIKE ? OR p.category LIKE ? OR c.name LIKE ? OR p.slug LIKE ?)";
            for ($i = 0; $i < 7; $i++) {
                $params[] = $st;
            }
        }
        $whereSql = implode(' OR ', $whereParts);

        $stmtProds = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as cat_slug 
                                    FROM productos p 
                                    LEFT JOIN categorias c ON p.category_id = c.id 
                                    WHERE p.is_active = 1 
                                      AND ($whereSql) 
                                    $order_clause");
        $stmtProds->execute($params);
        $products = $stmtProds->fetchAll();
    } elseif ($category_filter === 'tagua') {
        $stmtProds = $pdo->query("SELECT p.*, c.name as category_name, c.slug as cat_slug FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE p.is_active = 1 AND (c.slug = 'tagua' OR p.category = 'Tagua' OR p.slug LIKE '%tagua%') $order_clause");
        $products = $stmtProds->fetchAll();
    } elseif ($category_filter) {
        $stmtProds = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as cat_slug FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE p.is_active = 1 AND c.slug = ? $order_clause");
        $stmtProds->execute([$category_filter]);
        $products = $stmtProds->fetchAll();
    } else {
        // En la vista general (Todos), los productos activos se listan ordenados excluyendo Tagua
        $stmtProds = $pdo->query("SELECT p.*, c.name as category_name, c.slug as cat_slug FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE p.is_active = 1 AND (c.slug != 'tagua' OR c.slug IS NULL) AND (p.category != 'Tagua' OR p.category IS NULL) AND (p.slug NOT LIKE '%tagua%') $order_clause");
        $products = $stmtProds->fetchAll();
    }
} catch (PDOException $e) {
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Favicon Oficial -->
    <link rel="icon" type="image/png" href="favicon.png?v=2.0">
    <link rel="shortcut icon" href="favicon.ico?v=2.0">
    <link rel="apple-touch-icon" href="favicon.png?v=2.0">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Personalización & Grabado Láser | CardNet.ec</title>
    <meta name="description" content="Catálogo de artículos selectos para personalizar con grabado láser de alta fidelidad en Ecuador. Sin mínimos masivos: cotiza las unidades exactas que necesitas.">
    <link rel="canonical" href="https://cardnet.ec/productos.php">
    
    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=6.3">
    <link rel="stylesheet" href="css/layout.css?v=6.3">
    <link rel="stylesheet" href="css/components.css?v=6.3">
    <link rel="stylesheet" href="css/pages.css?v=6.3">
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
            }
            .product-card-desc {
                display: none !important;
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
            <h1 class="page-header-title">Catálogo de Artículos para Personalizar</h1>
            <p class="page-header-description">Piezas nobles grabadas con tecnología láser de ultra-alta definición. No exigimos producción masiva forzada: personalizamos desde piezas exclusivas hasta lotes corporativos selectos.</p>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="section-padding container">

        <!-- Banner Persuasivo de Taller: Sin Mínimos Masivos -->
        <div style="background: linear-gradient(135deg, #11140e 0%, #1a2215 100%); color: white; padding: 1.25rem 1.75rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid rgba(158, 255, 66, 0.2); display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 42px; height: 42px; border-radius: 50%; background: rgba(158, 255, 66, 0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #9eff42;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                </div>
                <div>
                    <h3 style="font-size: 0.98rem; font-weight: 600; color: white; margin: 0 0 2px 0;">Personalización de taller sin mínimos masivos</h3>
                    <p style="font-size: 0.82rem; color: rgba(255,255,255,0.7); margin: 0;">Cada artículo incluye calibración óptica de logotipo y grabado láser indeleble que jamás se borra con el uso.</p>
                </div>
            </div>
            <a href="cotizacion.php" class="btn btn-primary" style="padding: 9px 20px; font-size: 0.82rem; text-transform: none; white-space: nowrap;">Cotizar mi proyecto</a>
        </div>

        <!-- Barra de Búsqueda + Ordenamiento -->
        <form action="productos.php" method="GET" style="display: flex; gap: 10px; margin-bottom: 1.25rem; align-items: center; flex-wrap: wrap;">
            <?php if (!empty($category_filter)): ?>
                <input type="hidden" name="cat" value="<?php echo htmlspecialchars($category_filter); ?>">
            <?php endif; ?>
            <!-- Buscador Dinámico -->
            <div style="flex: 1; min-width: 200px; position: relative;">
                <svg style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; stroke: var(--text-muted); fill: none; stroke-width: 2; pointer-events: none;" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" id="product-search" name="q" placeholder="Buscar por nombre, material o técnica..." autocomplete="off" value="<?php echo htmlspecialchars($search_query); ?>"
                    style="width: 100%; padding: 9px 12px 9px 36px; border: 1px solid var(--border); border-radius: 8px; font-family: var(--font-body); font-size: 0.85rem; color: var(--text-dark); background: white; outline: none; transition: border-color 0.2s;">
            </div>
            <!-- Ordenamiento -->
            <select id="sort-selector" name="sort" onchange="this.form.submit();"
                style="padding: 9px 28px 9px 12px; border-radius: 8px; border: 1px solid var(--border); background: white; font-family: var(--font-body); font-size: 0.82rem; color: var(--text-dark); cursor: pointer; outline: none; font-weight: 500; -webkit-appearance: none; appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2216%22%20height%3D%2216%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23999%22%20stroke-width%3D%222%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 8px center; white-space: nowrap;">
                <option value="" <?php echo ($sort != 'price_asc') ? 'selected' : ''; ?>>Destacados</option>
                <option value="price_asc" <?php echo ($sort == 'price_asc') ? 'selected' : ''; ?>>Menor precio</option>
            </select>
        </form>

        <?php if (!empty($search_query)): ?>
            <div style="background: #f4f7f2; border: 1px solid rgba(99, 174, 44, 0.35); border-radius: 8px; padding: 12px 18px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <span style="font-size: 0.9rem; color: var(--text-dark);">
                    🔍 Mostrando resultados para: <strong style="color: var(--dark);">"<?php echo htmlspecialchars($search_query); ?>"</strong> (<?php echo count($products); ?> <?php echo count($products) === 1 ? 'producto encontrado' : 'productos encontrados'; ?>)
                </span>
                <a href="productos.php<?php echo $category_filter ? '?cat='.urlencode($category_filter) : ''; ?>" style="font-size: 0.82rem; color: var(--primary); font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 4px;">
                    ✕ Limpiar búsqueda
                </a>
            </div>
        <?php endif; ?>

        <!-- Contador de resultados de búsqueda en tiempo real -->
        <div id="search-results-count" style="display: none; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem; font-weight: 500;"></div>

        <!-- Barra de Filtros por Categoría (Chips con Scroll Horizontal en móvil) -->
        <div class="filter-bar" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 2.5rem; justify-content: center; overflow-x: auto; padding-bottom: 5px;">
            <button class="filter-btn <?php echo empty($category_filter) ? 'active' : ''; ?>" data-filter="all" style="border:none; cursor:pointer; white-space:nowrap;">Todos</button>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <button class="filter-btn <?php echo ($category_filter === $cat['slug']) ? 'active' : ''; ?>" 
                            data-filter="<?php echo htmlspecialchars($cat['name']); ?>" 
                            style="border:none; cursor:pointer; white-space:nowrap;">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
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
                         data-name="<?php echo htmlspecialchars($prod['name']); ?>" 
                         data-category="<?php echo htmlspecialchars(!empty($prod['category_name']) ? $prod['category_name'] : $prod['category']); ?>" 
                         data-material="<?php echo htmlspecialchars($enriched['material']); ?>" 
                         data-technique="<?php echo htmlspecialchars($enriched['technique']); ?>" 
                         data-use="<?php echo htmlspecialchars($enriched['use']); ?>"
                         data-gallery='<?php echo htmlspecialchars($prod_gallery_json, ENT_QUOTES, 'UTF-8'); ?>'
                         style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; padding: 0; transition: transform 0.25s ease, border-color 0.25s ease;">
                        
                        <a href="producto.php?slug=<?php echo htmlspecialchars($prod['slug']); ?>" style="text-decoration: none; color: inherit; display: block; flex-grow: 1;">
                            <div class="product-card-image-wrap" style="position: relative; overflow: hidden; aspect-ratio: 1.15; background: #ffffff; padding: 10px; border-bottom: 1px solid var(--border);">
                                <?php if ($prod['image_main']): ?>
                                    <img src="<?php echo htmlspecialchars(getUploadedImgUrl($prod['image_main'])); ?>" style="width:100%; height:100%; object-fit:contain; mix-blend-mode: multiply; transition: transform 0.4s ease;" loading="lazy" alt="<?php echo htmlspecialchars($prod['name']); ?>">
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
                                <span class="product-card-price" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); font-weight: 600; display: block; margin-bottom: 4px;"><?php echo htmlspecialchars(!empty($prod['category_name']) ? $prod['category_name'] : $prod['category']); ?></span>
                                <h3 class="product-card-title" style="margin-bottom: 0.5rem; font-size: 1.15rem; font-family: var(--font-heading); color: var(--dark); font-weight: 500; line-height: 1.2;"><?php echo htmlspecialchars($prod['name']); ?></h3>
                                
                                <div class="product-specs-badges" style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 0.85rem; margin-top: 0.25rem;">
                                    <?php 
                                    $prod_tags = json_decode($prod['tags_json'] ?? '[]', true) ?: [];
                                    if (!empty($prod_tags)): 
                                        foreach ($prod_tags as $t_id):
                                            if (isset($all_tags[$t_id])):
                                                $tag = $all_tags[$t_id];
                                    ?>
                                                <span style="font-size: 0.65rem; background: <?php echo htmlspecialchars($tag['color']); ?>; color: <?php echo htmlspecialchars($tag['text_color']); ?>; padding: 3px 8px; border-radius: 20px; font-weight: 600; border: 1px solid rgba(0,0,0,0.02);"><?php echo htmlspecialchars($tag['name']); ?></span>
                                    <?php 
                                            endif;
                                        endforeach;
                                    else: 
                                    ?>
                                        <span style="font-size: 0.65rem; background: rgba(0,0,0,0.03); color: var(--text-muted); padding: 3px 8px; border-radius: 20px; font-weight: 500; border: 1px solid rgba(0,0,0,0.02);"><?php echo htmlspecialchars($enriched['material']); ?></span>
                                        <span style="font-size: 0.65rem; background: rgba(99, 174, 44, 0.08); color: var(--primary-hover); padding: 3px 8px; border-radius: 20px; font-weight: 600; border: 1px solid rgba(99, 174, 44, 0.1);"><?php echo htmlspecialchars($enriched['technique']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="product-card-desc" style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem;"><?php echo htmlspecialchars($prod['description_short']); ?></p>
                            </div>
                        </a>
                        
                        <div style="display: flex; gap: 8px; margin-top: auto; padding: 0 1.25rem 1.25rem 1.25rem;">
                            <button class="btn btn-primary btn-add-to-quote" 
                                    data-slug="<?php echo htmlspecialchars($prod['slug']); ?>" 
                                    data-name="<?php echo htmlspecialchars($prod['name']); ?>" 
                                    data-price="<?php echo (float)$prod['price']; ?>"
                                    style="flex-grow: 1; padding: 8px 12px; font-size: 0.78rem; font-weight: 600; border: none; cursor: pointer; text-align: center;">
                                Cotizar
                            </button>
                            <a href="producto.php?slug=<?php echo htmlspecialchars($prod['slug']); ?>" class="btn btn-secondary" 
                                    style="padding: 8px 12px; font-size: 0.78rem; font-weight: 500; border: 1px solid var(--border); text-decoration: none; text-align: center; color: var(--dark); cursor: pointer; background: white;">
                                Ver más
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 4rem 1rem; background: white; border: 1px solid var(--border); border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
                    <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.2" style="color: var(--text-muted); opacity: 0.5; margin-bottom: 1rem;">
                        <circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);">
                        <?php echo !empty($search_query) ? 'No encontramos productos para "' . htmlspecialchars($search_query) . '"' : 'No encontramos productos con este filtro'; ?>
                    </h3>
                    <p style="font-size: 0.88rem; margin-bottom: 1.5rem; max-width: 500px; margin-left: auto; margin-right: auto;">
                        <?php echo !empty($search_query) ? 'Intenta con un término más general (como carnet, cinta, llavero, placa, tagua) o consúltanos directamente por WhatsApp.' : 'Puedes elegir otra categoría o cotizar una idea desde cero.'; ?>
                    </p>
                    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                        <a href="productos.php" class="btn btn-secondary" style="font-size: 0.85rem; padding: 10px 20px;">Ver todo el catálogo</a>
                        <a href="https://wa.me/<?php echo $prod_wa_clean; ?>?text=<?php echo urlencode('Hola CardNet, busco un producto que no encontré en el catálogo: ' . $search_query); ?>" class="btn btn-primary" target="_blank" rel="noopener noreferrer" style="font-size: 0.85rem; padding: 10px 20px;">Consultar por WhatsApp</a>
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
                <a href="https://wa.me/<?php echo $prod_wa_clean; ?>" class="btn btn-secondary" target="_blank" rel="noopener noreferrer" style="padding: 10px 22px; font-size: 0.85rem; background: white;">Enviar mi logo</a>
            </div>
        </div>
    </section>

    <!-- Pie de Página -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts Modulares -->
    <script src="js/main.js?v=6.4" defer></script>
    <script src="js/animations.js" defer></script>
</body>
</html>
 