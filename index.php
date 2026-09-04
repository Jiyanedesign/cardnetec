<?php
// Version Control V4.0.1
session_start();
require_once 'db.php';

// 1. Obtener slides del carrusel activos
try {
    $stmt = $pdo->query("SELECT * FROM carrusel WHERE is_active = 1 ORDER BY order_val ASC");
    $slides = $stmt->fetchAll();
} catch (PDOException $e) {
    $slides = [];
}

// 2. Obtener tarjetas de Obras del Taller desde secciones_home o portfolio de productos
try {
    $stmtObras = $pdo->query("SELECT * FROM secciones_home WHERE section_key = 'obras_taller' AND is_active = 1 ORDER BY CASE WHEN order_val IS NULL OR order_val = 0 THEN 999999 ELSE order_val END ASC, id ASC");
    $obras_items = $stmtObras->fetchAll();
} catch (PDOException $e) {
    $obras_items = [];
}

// 2. Obtener trabajos realizados (portfolio) desde la administración
try {
    $stmtShowcase = $pdo->query("SELECT p.*, c.slug as cat_slug FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE p.is_active = 1 AND p.is_featured = 1 AND (c.slug != 'tagua' OR c.slug IS NULL) ORDER BY CASE WHEN p.order_val IS NULL OR p.order_val = 0 THEN 999999 ELSE p.order_val END ASC, p.id DESC");
    $showcase_items = $stmtShowcase->fetchAll();
} catch (PDOException $e) {
    $showcase_items = [];
}

// Obtener también productos de personalización para la sección secundaria
try {
    $stmtCustom = $pdo->query("SELECT p.*, c.slug as cat_slug FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE p.is_active = 1 AND c.slug = 'personalizacion' ORDER BY CASE WHEN p.order_val IS NULL OR p.order_val = 0 THEN 999999 ELSE p.order_val END ASC, p.id DESC");
    $custom_prods = $stmtCustom->fetchAll();
} catch (PDOException $e) {
    $custom_prods = [];
}

// Obtener logos de clientes para prueba social
try {
    $stmtClients = $pdo->query("SELECT * FROM clientes WHERE is_active = 1 ORDER BY order_val ASC");
    $clients = $stmtClients->fetchAll();
} catch (PDOException $e) {
    $clients = [];
}

// Obtener las 4 categorías destacadas para el Bento Grid (Líneas del Taller)
try {
    $stmtFeaturedCats = $pdo->query("SELECT * FROM categorias WHERE is_active = 1 AND is_featured = 1 ORDER BY CASE WHEN order_val IS NULL OR order_val = 0 THEN 999999 ELSE order_val END ASC, id ASC LIMIT 4");
    $featured_categories = $stmtFeaturedCats->fetchAll();
} catch (PDOException $e) {
    $featured_categories = [];
}

// Obtener tarjetas de Soluciones de Taller (Empresas, Instituciones, Eventos)
try {
    $stmtSecSol = $pdo->query("SELECT * FROM secciones_home WHERE section_key = 'soluciones' AND is_active = 1 ORDER BY CASE WHEN order_val IS NULL OR order_val = 0 THEN 999999 ELSE order_val END ASC, id ASC");
    $home_soluciones = $stmtSecSol->fetchAll();
} catch (PDOException $e) {
    $home_soluciones = [];
}

// Obtener opciones de catálogo (Cintas y Credenciales)
try {
    $stmtSecCat = $pdo->query("SELECT * FROM secciones_home WHERE section_key = 'catalogo_opciones' AND is_active = 1 ORDER BY CASE WHEN order_val IS NULL OR order_val = 0 THEN 999999 ELSE order_val END ASC, id ASC");
    $home_catalogo_opciones = $stmtSecCat->fetchAll();
} catch (PDOException $e) {
    $home_catalogo_opciones = [];
}

// Obtener tarjetas de Accesorios Diarios
try {
    $stmtSecAcc = $pdo->query("SELECT * FROM secciones_home WHERE section_key = 'accesorios' AND is_active = 1 ORDER BY CASE WHEN order_val IS NULL OR order_val = 0 THEN 999999 ELSE order_val END ASC, id ASC");
    $home_accesorios = $stmtSecAcc->fetchAll();
} catch (PDOException $e) {
    $home_accesorios = [];
}

// Helper para resolver rutas de imágenes
if (!function_exists('resolveHomeImg')) {
    function resolveHomeImg($img, $default = 'uploads/carnet_mockup.webp') {
        return getUploadedImgUrl($img, $default);
    }
}

// 4. Cargar configuraciones del sitio
$site_settings = getSiteSettings($pdo);
$page_title = !empty($site_settings['site_title']) ? $site_settings['site_title'] : 'CardNet.ec | Identificación y accesorios para personal en Ecuador';
$page_description = !empty($site_settings['site_description']) ? $site_settings['site_description'] : 'Especialistas en carnets PVC, credenciales, cintas porta credenciales impresas y accesorios para identificar a tu equipo de forma profesional.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <link rel="canonical" href="https://cardnet.ec/index.php">
    <link rel="icon" type="image/png" href="favicon.png?v=2.0">
    <link rel="apple-touch-icon" href="favicon.png?v=2.0">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:url" content="https://cardnet.ec">
    <meta property="og:type" content="website">

    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=6.3">
    <link rel="stylesheet" href="css/layout.css?v=6.3">
    <link rel="stylesheet" href="css/components.css?v=6.3">
    <link rel="stylesheet" href="css/pages.css?v=6.3">
    <link rel="stylesheet" href="css/animations.css?v=1.1.3">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Efecto de elevación, glow y transiciones premium en tarjetas */
        .showcase-card, .custom-prod-card, #cintas-credenciales div, #cintas-credenciales .grid-2 > div, 
        #personalizacion-adicional .custom-prod-card, .faq-item, .process-step {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        
        /* Efecto hover general premium */
        .showcase-card:hover, .custom-prod-card:hover, .process-step:hover {
            transform: translateY(-6px) !important;
            box-shadow: 0 20px 40px rgba(99, 174, 44, 0.06), 0 2px 10px rgba(0,0,0,0.02) !important;
            border-color: rgba(99, 174, 44, 0.3) !important;
        }
        
        /* Transiciones premium en los botones de cotizar del comparador */
        .before-after-cta-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 174, 44, 0.3);
            background-color: #559c25 !important;
        }
        
        /* Animaciones para pestañas block del comparador */
        .slider-tab-btn:hover {
            border-color: var(--primary) !important;
            color: var(--primary) !important;
            transform: translateX(4px);
        }
    </style>

    <style>
        /* Borde verde y glow en hover para todas las cajas indicadas */
        .company-card-item, .accessory-card-item, .custom-prod-card, .process-step {
            transition: border-color 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1), transform 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        
        .company-card-item:hover, 
        .accessory-card-item:hover, 
        .custom-prod-card:hover, 
        .process-step:hover {
            border-color: var(--primary) !important;
            transform: translateY(-6px) !important;
            box-shadow: 0 15px 30px rgba(99, 174, 44, 0.1) !important;
        }
        
        /* Borde verde en hover para los materiales */
        .material-visual-item {
            cursor: pointer;
        }
        .material-visual-item div {
            transition: border-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease !important;
        }
        .material-visual-item:hover div {
            border-color: var(--primary) !important;
            transform: scale(1.05) !important;
            box-shadow: 0 8px 20px rgba(99, 174, 44, 0.12) !important;
        }
    </style>

</head>
<body>

    <?php include 'includes/header.php'; ?>

    <!-- MAIN CONTENT -->
    <main>
        
        <!-- 1. Hero Principal - Carrusel Automático Showroom de Identificación -->
        <section class="hero-block reveal-on-scroll" id="inicio" style="padding-top: 1rem; padding-bottom: 2rem;">
            <div class="container" style="position: relative;">
                <div class="hero-right-carousel" style="width: 100%; min-height: 520px; height: 70vh; position: relative; border-radius: var(--radius-md); overflow: hidden; background: #eef2eb; border: 1px solid var(--border); display: flex; flex-direction: column;">
                    <div class="hero-slider-track" style="width: 100%; height: 100%; position: relative; flex-grow: 1;">
                        
                        <?php if (!empty($slides)): ?>
                            <?php foreach ($slides as $idx => $slide): ?>
                                <div class="hero-slide-item <?php echo ($idx === 0) ? 'active' : ''; ?>" data-slide-index="<?php echo $idx; ?>" style="position: absolute; inset: 0; display: flex; align-items: center; opacity: <?php echo ($idx === 0) ? '1' : '0'; ?>; visibility: <?php echo ($idx === 0) ? 'visible' : 'hidden'; ?>; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: <?php echo ($idx === 0) ? '5' : '1'; ?>; padding: 3rem;">
                                    
                                    <!-- Imagen al 100% del fondo -->
                                    <div style="position: absolute; inset: 0; z-index: 1;">
                                        <?php
                                        $img_path = !empty($slide['image']) ? getUploadedImgUrl($slide['image'], 'uploads/carnet_mockup.webp') : 'uploads/carnet_mockup.webp';
                                        ?>
                                        <img src="<?php echo $img_path; ?>?v=2.2" alt="<?php echo htmlspecialchars($slide['title']); ?>" style="width: 100%; height: 100%; object-fit: cover; object-position: center;" class="hero-slide-img">
                                    </div>
                                    
                                    <!-- Suave capa protectora sobre la imagen completa para integrar contrastes -->
                                    <div style="position: absolute; inset: 0; background: rgba(255, 255, 255, 0.15); z-index: 2; pointer-events: none;"></div>
                                    
                                    <!-- Tarjeta Flotante con Glassmorphism para el Texto -->
                                    <div class="hero-text-card" style="position: relative; z-index: 3; max-width: 460px; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); padding: 2.75rem; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.25); box-shadow: 0 30px 60px rgba(0,0,0,0.06), 0 10px 20px rgba(0,0,0,0.02); margin-left: 2rem;">
                                        <h2 style="font-family: var(--font-heading); font-size: clamp(1.6rem, 3vw, 2.2rem); color: var(--dark); font-weight: 600; margin-bottom: 0.75rem; line-height: 1.25;"><?php echo htmlspecialchars($slide['title']); ?></h2>
                                        <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.75rem;"><?php echo htmlspecialchars($slide['subtitle']); ?></p>
                                        <a href="<?php echo htmlspecialchars($slide['cta_url']); ?>" class="btn btn-primary" style="padding: 12px 28px; font-weight: 600; text-transform: none; display: inline-block; text-align: center; border-radius: 6px; text-decoration: none; font-size: 0.85rem;"><?php echo htmlspecialchars($slide['cta_text']); ?></a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </div>
                    
                    <!-- Indicadores -->
                    <div style="position: absolute; bottom: 2rem; right: 3rem; z-index: 10; display: flex; gap: 8px;">
                        <?php foreach ($slides as $idx => $slide): ?>
                            <button class="hero-dot <?php echo ($idx === 0) ? 'active' : ''; ?>" data-slide-to="<?php echo $idx; ?>" aria-label="Slide <?php echo $idx + 1; ?>" style="width: 32px; height: 3px; border-radius: 2px; border: none; background: <?php echo ($idx === 0) ? 'var(--primary)' : 'rgba(255, 255, 255, 0.3)'; ?>; cursor: pointer; transition: background 0.3s ease, width 0.3s ease; padding:0;"></button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Barra de Garantías / Satisfacción -->
        <section class="satisfaction-bar" style="border-bottom: 1px solid var(--border); background: var(--surface-light); padding: 1.5rem 0;">
            <div class="container satisfaction-grid" style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px;">
                <div class="satisfaction-item" style="display: flex; align-items: center; gap: 10px;">
                    <svg class="satisfaction-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/></svg>
                    <span style="font-size: 0.88rem; font-weight: 600; color: var(--dark);">Grabado Láser de Precisión Milimétrica</span>
                </div>
                <div class="satisfaction-item" style="display: flex; align-items: center; gap: 10px;">
                    <svg class="satisfaction-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                    <span style="font-size: 0.88rem; font-weight: 600; color: var(--dark);">Personalización de Taller (Sin Mínimos Masivos)</span>
                </div>
                <div class="satisfaction-item" style="display: flex; align-items: center; gap: 10px;">
                    <svg class="satisfaction-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);"><path d="M2 12h20"/><path d="M20 12v8H4v-8"/><path d="m4 4 16 0"/><circle cx="12" cy="12" r="3"/></svg>
                    <span style="font-size: 0.88rem; font-weight: 600; color: var(--dark);">Calibración Individual Pieza por Pieza</span>
                </div>
            </div>
        </section>
        <!-- Sección: Prueba de confianza - Logos de Marcas -->
        <section id="marcas-confianza" style="background: white; border-bottom: 1px solid var(--border); overflow: hidden; padding-top: 3.5rem; padding-bottom: 3.5rem;">
            <div class="container">
                <p style="text-align: center; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); font-weight: 600; margin-bottom: 1.75rem;">Marcas y empresas que confían en nosotros</p>
                
                <style>
                    .logos-ticker-container {
                        width: 100%;
                        overflow: hidden;
                        position: relative;
                        display: flex;
                        align-items: center;
                    }
                    .logos-ticker-track {
                        display: flex;
                        gap: 50px;
                        width: max-content;
                        animation: scrollTicker 25s linear infinite;
                    }
                    .logos-ticker-item {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        height: 45px;
                        flex-shrink: 0;
                    }
                    .logos-ticker-item img {
                        height: 100%;
                        width: auto;
                        object-fit: contain;
                        opacity: 0.6;
                        filter: grayscale(100%);
                        transition: opacity 0.3s ease, filter 0.3s ease;
                    }
                    .logos-ticker-item img:hover {
                        opacity: 1;
                        filter: grayscale(0%);
                    }
                    @keyframes scrollTicker {
                        0% { transform: translateX(0); }
                        100% { transform: translateX(-50%); }
                    }
                </style>
                
                <div class="logos-ticker-container">
                    <div class="logos-ticker-track">
                        <?php if (!empty($clients)): ?>
                            <?php 
                            // Duplicar los clientes para hacer scroll infinito fluido
                            $double_clients = array_merge($clients, $clients);
                            ?>
                            <?php foreach ($double_clients as $client): ?>
                                <?php
                                $c_logo = trim($client['logo_path']);
                                if (!empty($c_logo)) {
                                    if (strpos($c_logo, 'uploads/') !== 0 && strpos($c_logo, 'images/') !== 0 && strpos($c_logo, 'http') !== 0) {
                                        $c_logo = 'uploads/' . $c_logo;
                                    }
                                } else {
                                    $c_logo = 'uploads/cliente1.png';
                                }
                                ?>
                                <div class="logos-ticker-item">
                                    <img src="<?php echo htmlspecialchars($c_logo); ?>" alt="<?php echo htmlspecialchars($client['name']); ?>" loading="lazy" onerror="this.style.opacity='0.4';">
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallbacks estáticos premium si no hay datos cargados -->
                            <div class="logos-ticker-item"><img src="images/empresa1.svg" alt="Empresa 1"></div>
                            <div class="logos-ticker-item"><img src="images/empresa2.svg" alt="Empresa 2"></div>
                            <div class="logos-ticker-item"><img src="images/empresa3.svg" alt="Empresa 3"></div>
                            <div class="logos-ticker-item"><img src="images/empresa4.svg" alt="Empresa 4"></div>
                            <div class="logos-ticker-item"><img src="images/empresa1.svg" alt="Empresa 1"></div>
                            <div class="logos-ticker-item"><img src="images/empresa2.svg" alt="Empresa 2"></div>
                            <div class="logos-ticker-item"><img src="images/empresa3.svg" alt="Empresa 3"></div>
                            <div class="logos-ticker-item"><img src="images/empresa4.svg" alt="Empresa 4"></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Sección: Categorías Visuales (Masonry Grid de Identificación) -->
        <section id="categorias-visuales" class="section-padding" style="background: #121212; color: white; padding-top: 5rem; padding-bottom: 5rem;">
            <div class="container">
                <div class="section-header" style="margin-bottom: 3.5rem; text-align: left; max-width: 720px;">
                    <span class="section-subtitle" style="color: var(--primary); border-color: var(--primary);">Maestría en Materiales</span>
                    <h2 style="font-family: var(--font-heading); font-size: 3rem; color: white; font-weight: 400; margin-bottom: 1rem;">Líneas de personalización de autor</h2>
                    <p style="color: rgba(255,255,255,0.75); font-size: 1rem; line-height: 1.6; margin: 0;">No producimos volumen genérico descartable. Grabamos y personalizamos piezas nobles con acabado indeleble, textura palpable y control de calidad individual.</p>
                </div>
                
                <style>
                    .premium-masonry-grid {
                        display: grid;
                        grid-template-columns: 2fr 1fr;
                        gap: 20px;
                    }
                    .premium-left-col {
                        display: flex;
                        flex-direction: column;
                        gap: 20px;
                    }
                    .premium-bottom-row {
                        display: grid;
                        grid-template-columns: 1.03fr 0.97fr;
                        gap: 20px;
                        align-items: stretch;
                        flex: 1;
                    }
                    .premium-right-col {
                        display: flex;
                        flex-column: column;
                        gap: 20px;
                    }
                    .premium-cat-card {
                        position: relative;
                        border-radius: 8px;
                        overflow: hidden;
                        background: #1c1b1b;
                        border: 1px solid rgba(255,255,255,0.03);
                        text-decoration: none;
                        display: block;
                        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
                    }
                    .premium-cat-card img {
                        width: 100%;
                        height: 100%;
                        display: block;
                        object-fit: cover;
                        filter: grayscale(100%);
                        transition: filter 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
                    }
                    .premium-cat-card:hover img {
                        filter: grayscale(0%);
                        transform: scale(1.025);
                    }
                    .premium-cat-overlay {
                        position: absolute;
                        inset: 0;
                        background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.2) 65%, transparent 100%);
                        z-index: 2;
                        display: flex;
                        flex-direction: column;
                        justify-content: flex-end;
                        padding: 2rem;
                        box-sizing: border-box;
                    }
                    .premium-cat-title {
                        font-family: var(--font-heading);
                        font-size: 1.65rem;
                        font-weight: 400;
                        color: #ffffff;
                        margin-bottom: 4px;
                    }
                    .premium-cat-subtitle {
                        font-size: 0.88rem;
                        color: rgba(255,255,255,0.65);
                        margin: 0;
                    }
                    @media (max-width: 768px) {
                        .premium-masonry-grid {
                            grid-template-columns: 1fr;
                            gap: 15px;
                        }
                        .premium-bottom-row {
                            grid-template-columns: 1fr;
                            gap: 15px;
                        }
                        .premium-cat-overlay {
                            padding: 1.5rem;
                        }
                        .premium-cat-title {
                            font-size: 1.4rem;
                        }
                    }
                </style>

                <?php
                // Mapear las 4 posiciones del Bento Grid
                $bento_1 = $featured_categories[0] ?? [
                    'name' => 'Grabado Láser de Precisión',
                    'description' => 'Fidelidad milimétrica en acero inoxidable, aluminio y metales.',
                    'image' => 'images/cat_laser.png',
                    'custom_link' => 'personalizacion.php'
                ];
                $bento_2 = $featured_categories[1] ?? [
                    'name' => 'Packaging y Cajas de Autor',
                    'description' => 'Presentación premium y cajas grabadas a medida.',
                    'image' => 'caja.png',
                    'custom_link' => 'productos.php?cat=personalizacion'
                ];
                $bento_3 = $featured_categories[2] ?? [
                    'name' => 'Cuero y Madera Grabada',
                    'description' => 'Termograbado en bajo relieve y corte artesanal.',
                    'image' => 'images/mat_cuero.webp',
                    'custom_link' => 'productos.php?cat=personalizacion'
                ];
                $bento_4 = $featured_categories[3] ?? [
                    'name' => 'Identificación de Autor',
                    'description' => 'Credenciales y cintas con estándares de alta presentación institucional.',
                    'image' => 'carnet_mockup.webp',
                    'custom_link' => 'carnets.php'
                ];

                function getBentoImgUrl($img) {
                    return getUploadedImgUrl($img, 'uploads/cintas_mockup.webp');
                }

                function getBentoLinkUrl($cat) {
                    if (!empty($cat['custom_link'])) return $cat['custom_link'];
                    if (!empty($cat['slug'])) return 'productos.php?cat=' . $cat['slug'];
                    return 'productos.php';
                }
                ?>

                <div class="premium-masonry-grid">
                    <!-- Columna Izquierda -->
                    <div class="premium-left-col">
                        <!-- Tarjeta 1: Superior Izquierda Ancha -->
                        <a href="<?php echo htmlspecialchars(getBentoLinkUrl($bento_1)); ?>" class="premium-cat-card" style="aspect-ratio: 595/302;">
                            <img src="<?php echo htmlspecialchars(getBentoImgUrl($bento_1['image'])); ?>" alt="<?php echo htmlspecialchars($bento_1['name']); ?>">
                            <div class="premium-cat-overlay">
                                <h3 class="premium-cat-title"><?php echo htmlspecialchars($bento_1['name']); ?></h3>
                                <?php if (!empty($bento_1['description'])): ?>
                                    <p class="premium-cat-subtitle"><?php echo htmlspecialchars($bento_1['description']); ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                        
                        <div class="premium-bottom-row">
                            <!-- Tarjeta 2: Inferior Izquierda 1 -->
                            <a href="<?php echo htmlspecialchars(getBentoLinkUrl($bento_2)); ?>" class="premium-cat-card" style="height: 100%; min-height: 260px;">
                                <img src="<?php echo htmlspecialchars(getBentoImgUrl($bento_2['image'])); ?>" alt="<?php echo htmlspecialchars($bento_2['name']); ?>">
                                <div class="premium-cat-overlay">
                                    <h3 class="premium-cat-title"><?php echo htmlspecialchars($bento_2['name']); ?></h3>
                                    <?php if (!empty($bento_2['description'])): ?>
                                        <p class="premium-cat-subtitle"><?php echo htmlspecialchars($bento_2['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <!-- Tarjeta 3: Inferior Izquierda 2 -->
                            <a href="<?php echo htmlspecialchars(getBentoLinkUrl($bento_3)); ?>" class="premium-cat-card" style="height: 100%; min-height: 260px;">
                                <img src="<?php echo htmlspecialchars(getBentoImgUrl($bento_3['image'])); ?>" alt="<?php echo htmlspecialchars($bento_3['name']); ?>">
                                <div class="premium-cat-overlay">
                                    <h3 class="premium-cat-title"><?php echo htmlspecialchars($bento_3['name']); ?></h3>
                                    <?php if (!empty($bento_3['description'])): ?>
                                        <p class="premium-cat-subtitle"><?php echo htmlspecialchars($bento_3['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Columna Derecha -->
                    <div class="premium-right-col" style="display: flex; flex-direction: column;">
                        <!-- Tarjeta 4: Derecha Alta Vertical -->
                        <a href="<?php echo htmlspecialchars(getBentoLinkUrl($bento_4)); ?>" class="premium-cat-card" style="aspect-ratio: 288/460; height: 100%;">
                            <img src="<?php echo htmlspecialchars(getBentoImgUrl($bento_4['image'])); ?>" alt="<?php echo htmlspecialchars($bento_4['name']); ?>" style="height: 100%; object-fit: cover;">
                            <div class="premium-cat-overlay" style="height: 100%;">
                                <h3 class="premium-cat-title"><?php echo htmlspecialchars($bento_4['name']); ?></h3>
                                <?php if (!empty($bento_4['description'])): ?>
                                    <p class="premium-cat-subtitle"><?php echo htmlspecialchars($bento_4['description']); ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>



        <!-- 2. Productos principales de identificación (Showcase Carrusel Moderno) -->
        <section id="productos" class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3rem;">
                <span class="section-subtitle"><?php echo htmlspecialchars($site_settings['obras_subtitle'] ?: 'Obras del Taller'); ?></span>
                <h2><?php echo htmlspecialchars($site_settings['obras_title'] ?: 'Piezas seleccionadas para personalizar'); ?></h2>
                <p><?php echo htmlspecialchars($site_settings['obras_desc'] ?: 'Artículos de alta resistencia diseñados para acoger tu marca con grabado láser de máxima definición.'); ?></p>
            </div>
            
            <style>
                .showcase-carousel-wrapper {
                    position: relative;
                    width: 100%;
                    overflow: hidden;
                    padding: 10px 0;
                }
                .showcase-carousel-track {
                    display: flex;
                    gap: 24px;
                    transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1);
                }
                .showcase-card {
                    flex: 0 0 calc(33.333% - 16px);
                    background: white;
                    border: none;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 4px 16px rgba(0,0,0,0.04);
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                    display: flex;
                    flex-direction: column;
                }
                .showcase-card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 12px 28px rgba(0,0,0,0.08);
                }
                .showcase-image-wrap {
                    width: 100%;
                    aspect-ratio: 1.35;
                    background: #FFFFFF;
                    border-bottom: none;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    overflow: hidden;
                    padding: 0;
                    box-sizing: border-box;
                }
                .showcase-image-wrap img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.5s ease;
                }
                .showcase-card:hover .showcase-image-wrap img {
                    transform: scale(1.04);
                }
                .showcase-info {
                    padding: 1.5rem;
                    text-align: center;
                }
                .showcase-title {
                    font-family: var(--font-heading);
                    font-size: 1.25rem;
                    font-weight: 500;
                    color: var(--dark);
                    margin: 0;
                }
                
                /* Controles del Carrusel */
                .showcase-control {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    width: 44px;
                    height: 44px;
                    border-radius: 50%;
                    background: white;
                    border: none;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    box-shadow: 0 4px 14px rgba(0,0,0,0.1);
                    z-index: 10;
                    transition: all 0.2s ease;
                }
                .showcase-control:hover {
                    background: var(--primary);
                    color: white;
                    box-shadow: 0 6px 18px rgba(99,174,44,0.3);
                }
                .showcase-control.prev { left: 10px; }
                .showcase-control.next { right: 10px; }
                
                .showcase-dots {
                    display: flex;
                    justify-content: center;
                    gap: 8px;
                    margin-top: 2rem;
                }
                .showcase-dot {
                    width: 8px;
                    height: 8px;
                    border-radius: 50%;
                    background: var(--border);
                    border: none;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    padding: 0;
                }
                .showcase-dot.active {
                    background: var(--primary);
                    transform: scale(1.2);
                }
                
                @media (max-width: 992px) {
                    .showcase-card {
                        flex: 0 0 calc(50% - 12px);
                    }
                }
                @media (max-width: 576px) {
                    .showcase-card {
                        flex: 0 0 100%;
                    }
                    .showcase-control { display: none; }
                }
            </style>

            <div class="showcase-carousel-wrapper">
                <button class="showcase-control prev" aria-label="Anterior">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                
                <div class="showcase-carousel-track">
                    <?php if (!empty($obras_items)): ?>
                        <?php foreach ($obras_items as $item): ?>
                            <?php
                            $item_src = resolveHomeImg($item['image'], 'uploads/carnet_mockup.jpg');
                            $item_link = !empty($item['btn_link']) ? $item['btn_link'] : 'cotizacion.php';
                            ?>
                            <a href="<?php echo htmlspecialchars($item_link); ?>" class="showcase-card" style="text-decoration: none; color: inherit;">
                                <div class="showcase-image-wrap">
                                    <img src="<?php echo htmlspecialchars($item_src); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                </div>
                                <div class="showcase-info">
                                    <h3 class="showcase-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                                    <?php if (!empty($item['subtitle'])): ?>
                                        <p style="font-size: 0.8rem; color: var(--text-muted); margin: 6px 0 0 0; line-height: 1.3;"><?php echo htmlspecialchars($item['subtitle']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php elseif (!empty($showcase_items)): ?>
                        <?php foreach ($showcase_items as $item): ?>
                            <?php
                            $i_img = !empty($item['image_main']) ? $item['image_main'] : '';
                            $item_src = resolveHomeImg($i_img, 'uploads/carnet_mockup.jpg');
                            ?>
                            <a href="producto.php?slug=<?php echo htmlspecialchars($item['slug']); ?>" class="showcase-card" style="text-decoration: none; color: inherit;">
                                <div class="showcase-image-wrap">
                                    <img src="<?php echo htmlspecialchars($item_src); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>
                                <div class="showcase-info">
                                    <h3 class="showcase-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Carnets PVC -->
                        <div class="showcase-card">
                            <div class="showcase-image-wrap">
                                <img src="uploads/carnet_mockup.webp" alt="Carnets PVC">
                            </div>
                            <div class="showcase-info">
                                <h3 class="showcase-title">Carnets PVC Corporativos</h3>
                            </div>
                        </div>
                        <!-- Cintas y lanyards -->
                        <div class="showcase-card">
                            <div class="showcase-image-wrap">
                                <img src="uploads/cintas_mockup.webp" alt="Cintas y lanyards">
                            </div>
                            <div class="showcase-info">
                                <h3 class="showcase-title">Cintas Porta Credenciales</h3>
                            </div>
                        </div>
                        <!-- Porta credenciales -->
                        <div class="showcase-card">
                            <div class="showcase-image-wrap">
                                <img src="uploads/llavero.webp" alt="Porta credenciales y accesorios">
                            </div>
                            <div class="showcase-info">
                                <h3 class="showcase-title">Porta Carnets y Accesorios</h3>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <button class="showcase-control next" aria-label="Siguiente">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
            
            <div class="showcase-dots">
                <button class="showcase-dot active" data-index="0" aria-label="Grupo 1"></button>
                <button class="showcase-dot" data-index="1" aria-label="Grupo 2"></button>
            </div>
        </section>


        <!-- 3. Sección Opciones de cintas y credenciales con Fotos Ilustrativas -->
        <section id="cintas-credenciales" class="section-padding" style="background: var(--surface-light); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
            <div class="container">
                <div class="section-header center" style="margin-bottom: 4rem;">
                    <span class="section-subtitle">Identidad Corporativa</span>
                    <h2>Credenciales y lanyards de alta fidelidad</h2>
                    <p>Materiales de resistencia superior, nitidez de impresión y personalización pensada para representar con orgullo a tu organización.</p>
                </div>

                <?php
                // Separar opciones por grupo
                $group_cintas = [];
                $group_credenciales = [];
                foreach ($home_catalogo_opciones as $op) {
                    if (stripos($op['group_name'] ?? '', 'cintas') !== false || stripos($op['title'], 'cinta') !== false) {
                        $group_cintas[] = $op;
                    } else {
                        $group_credenciales[] = $op;
                    }
                }
                ?>

                <div class="grid-2" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px;">
                    <!-- BLOQUE 1: Cintas -->
                    <div style="background: white; padding: 2.5rem; border-radius: var(--radius-md); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                        <h3 style="font-family: var(--font-heading); font-size: 1.6rem; color: var(--dark); margin-bottom: 2rem; border-bottom: 2px solid var(--primary); padding-bottom: 8px;">Cintas porta credenciales</h3>
                        
                        <div style="display: flex; flex-direction: column; gap: 24px;">
                            <?php foreach ($group_cintas as $idx => $card): ?>
                                <?php if ($idx > 0): ?>
                                    <hr style="border: 0; border-top: 1px solid var(--border); margin: 0;">
                                <?php endif; ?>
                                <div style="display: flex; gap: 20px; align-items: flex-start;">
                                    <div style="width: 90px; height: 90px; border-radius: 8px; overflow: hidden; flex-shrink: 0; border: 1px solid var(--border); background: var(--surface-light);">
                                        <img src="<?php echo htmlspecialchars(resolveHomeImg($card['image'], 'uploads/cintas_full_color.jpg')); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($card['title']); ?>">
                                    </div>
                                    <div style="flex-grow: 1;">
                                        <h4 style="font-size: 1.05rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;"><?php echo htmlspecialchars($card['title']); ?></h4>
                                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 10px;"><?php echo htmlspecialchars($card['subtitle']); ?></p>
                                        <a href="<?php echo htmlspecialchars($card['btn_link'] ?: 'cotizacion.php'); ?>" class="btn btn-secondary" style="font-size: 0.72rem; padding: 4px 10px; text-transform: none; background: white; display: inline-block;">
                                            <?php echo htmlspecialchars($card['btn_text'] ?: 'Cotizar'); ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- BLOQUE 2: Credenciales -->
                    <div style="background: white; padding: 2.5rem; border-radius: var(--radius-md); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                        <h3 style="font-family: var(--font-heading); font-size: 1.6rem; color: var(--dark); margin-bottom: 2rem; border-bottom: 2px solid var(--primary); padding-bottom: 8px;">Credenciales y porta credenciales</h3>
                        
                        <div style="display: flex; flex-direction: column; gap: 24px;">
                            <?php foreach ($group_credenciales as $idx => $card): ?>
                                <?php if ($idx > 0): ?>
                                    <hr style="border: 0; border-top: 1px solid var(--border); margin: 0;">
                                <?php endif; ?>
                                <div style="display: flex; gap: 20px; align-items: flex-start;">
                                    <div style="width: 90px; height: 90px; border-radius: 8px; overflow: hidden; flex-shrink: 0; border: 1px solid var(--border); background: var(--surface-light);">
                                        <img src="<?php echo htmlspecialchars(resolveHomeImg($card['image'], 'uploads/carnet_mockup.jpg')); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($card['title']); ?>">
                                    </div>
                                    <div style="flex-grow: 1;">
                                        <h4 style="font-size: 1.05rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;"><?php echo htmlspecialchars($card['title']); ?></h4>
                                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 10px;"><?php echo htmlspecialchars($card['subtitle']); ?></p>
                                        <a href="<?php echo htmlspecialchars($card['btn_link'] ?: 'cotizacion.php'); ?>" class="btn btn-secondary" style="font-size: 0.72rem; padding: 4px 10px; text-transform: none; background: white; display: inline-block;">
                                            <?php echo htmlspecialchars($card['btn_text'] ?: 'Cotizar'); ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Identificación para empresas, instituciones y eventos con Fotos -->
        <section class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle">Proyectos a Medida</span>
                <h2>Personalización para marcas, instituciones y eventos selectos</h2>
                <p>Soluciones a escala humana: cada proyecto recibe piezas con atención al detalle, nombres personalizados y presentación impecable.</p>
            </div>

            <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                <?php if (!empty($home_soluciones)): ?>
                    <?php foreach ($home_soluciones as $sol): ?>
                        <div class="company-card-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; display: flex; flex-direction: column;">
                            <div style="width: 100%; aspect-ratio: 1.6; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                                <img src="<?php echo htmlspecialchars(resolveHomeImg($sol['image'], 'uploads/carnet_mockup.jpg')); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($sol['title']); ?>">
                            </div>
                            <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                                <h3 style="font-size: 1.15rem; font-family: var(--font-heading); margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);"><?php echo htmlspecialchars($sol['title']); ?></h3>
                                <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex-grow: 1;"><?php echo htmlspecialchars($sol['subtitle']); ?></p>
                                <a href="<?php echo htmlspecialchars($sol['btn_link'] ?: 'cotizacion.php'); ?>" class="btn btn-secondary" style="font-size: 0.75rem; padding: 8px 12px; text-align: center; text-transform: none; margin-top: auto;">
                                    <?php echo htmlspecialchars($sol['btn_text'] ?: 'Cotizar'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- 5. Accesorios para el uso diario con Fotos Ilustrativas (100% Dinámico desde el Dashboard) -->
        <section class="section-padding" style="background: var(--surface-light); border-top: 1px solid var(--border);">
            <div class="container reveal-on-scroll">
                <div class="section-header center" style="margin-bottom: 3.5rem;">
                    <span class="section-subtitle"><?php echo htmlspecialchars($site_settings['accesorios_subtitle'] ?? 'Accesorios Diarios'); ?></span>
                    <h2><?php echo htmlspecialchars($site_settings['accesorios_title'] ?? 'Accesorios para el uso diario'); ?></h2>
                    <p><?php echo htmlspecialchars($site_settings['accesorios_desc'] ?? 'Complementos prácticos para proteger, portar y presentar mejor cada credencial.'); ?></p>
                </div>

                <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px;">
                    <?php if (!empty($home_accesorios)): ?>
                        <?php foreach ($home_accesorios as $acc): ?>
                            <div class="accessory-card-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; display: flex; flex-direction: column;">
                                <div style="width: 100%; aspect-ratio: 1.4; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                                    <img src="<?php echo htmlspecialchars(resolveHomeImg($acc['image'], 'uploads/llavero.png')); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($acc['title']); ?>">
                                </div>
                                <div style="padding: 1.25rem; text-align: center; display: flex; flex-direction: column; flex-grow: 1;">
                                    <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 5px; color: var(--dark);"><?php echo htmlspecialchars($acc['title']); ?></h4>
                                    <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 1rem; flex-grow: 1;"><?php echo htmlspecialchars($acc['subtitle'] ?? ''); ?></p>
                                    <a href="<?php echo htmlspecialchars($acc['btn_link'] ?: 'productos.php?cat=porta-credenciales'); ?>" style="font-size: 0.75rem; color: var(--primary); font-weight: 600; text-decoration: none; text-transform: none; margin-top: auto;">
                                        <?php echo htmlspecialchars($acc['btn_text'] ?: 'Ver opciones'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback si la tabla está vacía -->
                        <div class="accessory-card-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; display: flex; flex-direction: column;">
                            <div style="width: 100%; aspect-ratio: 1.4; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                                <img src="uploads/llavero.webp" style="width: 100%; height: 100%; object-fit: cover;" alt="Porta carnets">
                            </div>
                            <div style="padding: 1.25rem; text-align: center; display: flex; flex-direction: column; flex-grow: 1;">
                                <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 5px; color: var(--dark);">Porta carnets</h4>
                                <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 1rem; flex-grow: 1;">Protección práctica para carnets y tarjetas rígidas.</p>
                                <a href="productos.php?cat=porta-credenciales" style="font-size: 0.75rem; color: var(--primary); font-weight: 600; text-decoration: none; text-transform: none; margin-top: auto;">Ver opciones</a>
                            </div>
                        </div>
                        <div class="accessory-card-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; display: flex; flex-direction: column;">
                            <div style="width: 100%; aspect-ratio: 1.4; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                                <img src="uploads/yoyos.webp" style="width: 100%; height: 100%; object-fit: cover;" alt="Yoyos retráctiles">
                            </div>
                            <div style="padding: 1.25rem; text-align: center; display: flex; flex-direction: column; flex-grow: 1;">
                                <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 5px; color: var(--dark);">Yoyos retráctiles</h4>
                                <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 1rem; flex-grow: 1;">Accesorio cómodo con cordón extensible para accesos rápidos.</p>
                                <a href="cotizacion.php?producto=accesorios-identificacion" style="font-size: 0.75rem; color: var(--primary); font-weight: 600; text-decoration: none; text-transform: none; margin-top: auto;">Cotizar yoyos</a>
                            </div>
                        </div>
                        <div class="accessory-card-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; display: flex; flex-direction: column;">
                            <div style="width: 100%; aspect-ratio: 1.4; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                                <img src="uploads/fundas.webp" style="width: 100%; height: 100%; object-fit: cover;" alt="Fundas transparentes">
                            </div>
                            <div style="padding: 1.25rem; text-align: center; display: flex; flex-direction: column; flex-grow: 1;">
                                <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 5px; color: var(--dark);">Fundas transparentes</h4>
                                <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 1rem; flex-grow: 1;">Fundas de PVC blando para acreditaciones de eventos.</p>
                                <a href="productos.php?cat=porta-credenciales" style="font-size: 0.75rem; color: var(--primary); font-weight: 600; text-decoration: none; text-transform: none; margin-top: auto;">Ver opciones</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        
        <!-- Sección: Materiales que trabajamos -->
        <section id="materiales-trabajamos" class="section-padding" style="background: var(--surface-light); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding-top: 4rem; padding-bottom: 4rem;">
            <div class="container">
                <div class="section-header center" style="margin-bottom: 3rem;">
                    <h2 style="font-family: var(--font-heading); font-size: 2.2rem; font-weight: 500; color: var(--dark);">Materiales que trabajamos</h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.5;">Cada material requiere un acabado distinto. Elegimos la técnica según el producto y el resultado que buscas.</p>
                </div>

                <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center; margin-top: 2rem;">
                    <!-- Acero -->
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 12px; width: 140px;">
                        <div style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border); background: white;">
                            <img src="images/mat_acero.webp" alt="Acero" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.05em;">Acero</span>
                    </div>
                    <!-- Madera -->
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 12px; width: 140px;">
                        <div style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border); background: white;">
                            <img src="images/mat_madera.webp" alt="Madera" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.05em;">Madera</span>
                    </div>
                    <!-- Acrílico -->
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 12px; width: 140px;">
                        <div style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border); background: white;">
                            <img src="images/mat_acrilico.webp" alt="Acrílico" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.05em;">Acrílico</span>
                    </div>
                    <!-- Cuero/PU -->
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 12px; width: 140px;">
                        <div style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border); background: white;">
                            <img src="images/mat_cuero.webp" alt="Cuero/PU" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.05em;">Cuero/PU</span>
                    </div>
                    <!-- Vidrio -->
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 12px; width: 140px;">
                        <div style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border); background: white;">
                            <img src="images/mat_vidrio.webp" alt="Vidrio" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.05em;">Vidrio</span>
                    </div>
                    <!-- PVC -->
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 12px; width: 140px;">
                        <div style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border); background: white;">
                            <img src="images/mat_pvc.webp" alt="PVC" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.05em;">PVC</span>
                    </div>
                </div>
            </div>
        </section>


        <!-- 6. Sección: Maestría en Personalización & Piezas de Autor -->
        <section id="personalizacion-adicional" class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle">Maestría en Grabado</span>
                <h2>Piezas selectas con grabado láser de alta fidelidad</h2>
                <p>Rechazamos la mediocridad de la maquila genérica. Grabamos piezas individuales y proyectos corporativos con calibración óptica milimétrica.</p>
            </div>
            
            <p style="font-size: 0.96rem; color: var(--text-muted); text-align: center; max-width: 780px; margin: 0 auto 2.5rem auto; line-height: 1.6;">
                A diferencia de las fábricas masivas que exigen miles de unidades descartables, en nuestro taller personalizamos artículos nobles: acero inoxidable térmico, libretas en cuero PU de alto contraste, madera tratada y marfil vegetal de tagua. Cada pieza recibe control de calidad individual.
            </p>

            <style>
                .custom-prods-grid {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    gap: 20px;
                }
                .custom-prod-card {
                    background: white;
                    border: 1px solid var(--border);
                    border-radius: var(--radius-md);
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
                    position: relative;
                }
                .custom-prod-card:hover {
                    transform: translateY(-8px);
                    box-shadow: 0 15px 35px rgba(99, 174, 44, 0.08);
                    border-color: var(--primary);
                }
                @media (max-width: 992px) {
                    .custom-prods-grid {
                        grid-template-columns: repeat(2, 1fr);
                    }
                }
                @media (max-width: 576px) {
                    .custom-prods-grid {
                        grid-template-columns: 1fr;
                    }
                }
            </style>
            
            <div class="custom-prods-grid">
                <?php if (!empty($custom_prods)): ?>
                    <?php foreach ($custom_prods as $prod): ?>
                        <div class="custom-prod-card">
                            <a href="producto.php?slug=<?php echo htmlspecialchars($prod['slug']); ?>" style="aspect-ratio: 1.25; background: var(--surface-light); display: flex; align-items: center; justify-content: center; overflow: hidden; border-bottom: 1px solid var(--border); text-decoration: none;">
                                <?php
                                $prod_img = !empty($prod['image_main']) ? $prod['image_main'] : '';
                                $img_src = getUploadedImgUrl($prod_img, 'uploads/cintas_mockup.jpg');
                                ?>
                                <img src="<?php echo htmlspecialchars($img_src); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" alt="<?php echo htmlspecialchars($prod['name']); ?>" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            </a>
                            <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                                <h4 style="font-family: var(--font-heading); font-size: 1.1rem; color: var(--dark); margin-bottom: 0.5rem;">
                                    <a href="producto.php?slug=<?php echo htmlspecialchars($prod['slug']); ?>" style="color: inherit; text-decoration: none;">
                                        <?php echo htmlspecialchars($prod['name']); ?>
                                    </a>
                                </h4>
                                <p style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex-grow: 1;"><?php echo htmlspecialchars($prod['description_short']); ?></p>
                                <a href="cotizacion.php?producto=<?php echo htmlspecialchars($prod['slug']); ?>" class="btn btn-secondary" style="width: 100%; text-align: center; font-size: 0.78rem; padding: 8px 0; text-transform: none;">
                                    <?php echo htmlspecialchars($prod['cta_text'] ?: 'Personalizar pieza'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Bloque Comparativo: Personalización de Autor vs Producción Masiva Genérica -->
        <section class="section-padding" style="background: #11140e; color: white; border-top: 1px solid rgba(255,255,255,0.06); border-bottom: 1px solid rgba(255,255,255,0.06);">
            <div class="container">
                <div class="section-header center" style="margin-bottom: 3.5rem;">
                    <span class="section-subtitle" style="color: #9eff42; border-color: #9eff42;">El Estándar CardNet</span>
                    <h2 style="color: white; font-family: var(--font-heading); font-size: 2.3rem;">Personalización de taller vs. producción masiva</h2>
                    <p style="color: rgba(255,255,255,0.7); max-width: 680px; margin: 0 auto;">Por qué las marcas y profesionales que cuidan su reputación eligen la precisión de nuestro taller en lugar del volumen genérico sin control.</p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; max-width: 1000px; margin: 0 auto;">
                    <!-- Caja 1: Producción Masiva Típica (Lo que evitamos) -->
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 2.25rem;">
                        <span style="display: inline-block; background: rgba(239, 68, 68, 0.15); color: #f87171; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 4px 10px; border-radius: 4px; margin-bottom: 1rem;">La típica maquila masiva</span>
                        <h3 style="font-family: var(--font-heading); font-size: 1.35rem; color: #e5e7eb; margin-bottom: 1.25rem;">Impresión en serie descartable</h3>
                        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 14px; font-size: 0.88rem; color: rgba(255,255,255,0.65);">
                            <li style="display: flex; gap: 10px; align-items: flex-start;">
                                <span style="color: #f87171; font-weight: bold;">✕</span>
                                <span><strong>Mínimos forzados:</strong> te obligan a comprar cientos o miles de piezas que muchas veces no necesitas.</span>
                            </li>
                            <li style="display: flex; gap: 10px; align-items: flex-start;">
                                <span style="color: #f87171; font-weight: bold;">✕</span>
                                <span><strong>Tintas superficiales:</strong> estampados que se desgastan, se rayan o se borran en pocas semanas de uso.</span>
                            </li>
                            <li style="display: flex; gap: 10px; align-items: flex-start;">
                                <span style="color: #f87171; font-weight: bold;">✕</span>
                                <span><strong>Cero revisión humana:</strong> tu logo pasa directo a la máquina sin calibrar proporciones ni sangrados.</span>
                            </li>
                            <li style="display: flex; gap: 10px; align-items: flex-start;">
                                <span style="color: #f87171; font-weight: bold;">✕</span>
                                <span><strong>Sensación de baratija:</strong> artículos genéricos que los clientes perciben de poco valor.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Caja 2: Nuestro Taller (Lo que nos diferencia) -->
                    <div style="background: rgba(158, 255, 66, 0.05); border: 2px solid rgba(158, 255, 66, 0.35); border-radius: 12px; padding: 2.25rem; position: relative;">
                        <div style="position: absolute; top: -12px; right: 20px; background: #9eff42; color: #0d110b; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; padding: 3px 10px; border-radius: 20px;">Nuestra Promesa</div>
                        <span style="display: inline-block; background: rgba(158, 255, 66, 0.15); color: #9eff42; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 4px 10px; border-radius: 4px; margin-bottom: 1rem;">Personalización de Autor CardNet</span>
                        <h3 style="font-family: var(--font-heading); font-size: 1.35rem; color: white; margin-bottom: 1.25rem;">Grabado láser de alta fidelidad</h3>
                        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 14px; font-size: 0.88rem; color: rgba(255,255,255,0.9);">
                            <li style="display: flex; gap: 10px; align-items: flex-start;">
                                <span style="color: #9eff42; font-weight: bold;">✓</span>
                                <span><strong>Sin mínimos absurdos:</strong> atendemos desde piezas únicas, regalos directivos exclusivos y lotes selectos.</span>
                            </li>
                            <li style="display: flex; gap: 10px; align-items: flex-start;">
                                <span style="color: #9eff42; font-weight: bold;">✓</span>
                                <span><strong>100% Indeleble:</strong> el láser de fibra y CO2 graba la materia prima; jamás se desprende ni se decolora.</span>
                            </li>
                            <li style="display: flex; gap: 10px; align-items: flex-start;">
                                <span style="color: #9eff42; font-weight: bold;">✓</span>
                                <span><strong>Calibración individual:</strong> adaptamos vectores, profundidad y potencia óptica para que cada trazo sea perfecto.</span>
                            </li>
                            <li style="display: flex; gap: 10px; align-items: flex-start;">
                                <span style="color: #9eff42; font-weight: bold;">✓</span>
                                <span><strong>Prestigio tangible:</strong> piezas que generan orgullo y que las personas conservan por años.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. Grabado láser y personalización (Servicio técnico e ilustrativo) -->
        <section id="laser" class="section-padding container reveal-on-scroll" style="border-top: 1px solid var(--border);">
            <div class="laser-section" style="padding: 4rem; background-image: url('images/laser_action.jpg'); background-size: cover; background-position: center; border-radius: var(--radius-lg); overflow: hidden; position: relative;">
                <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(16, 20, 15, 0.98) 0%, rgba(16, 20, 15, 0.88) 50%, rgba(16, 20, 15, 0.3) 100%); z-index: 1;"></div>
                <div style="max-width: 620px; position: relative; z-index: 5;">
                    <span class="section-subtitle" style="color: #8CFF32; border-color: #8CFF32;">Tecnología de Taller</span>
                    <h2 style="color: white; font-family: var(--font-heading); font-size: 2.3rem; font-weight: 500; margin-bottom: 1.25rem;">La mejor tecnología de personalización en Ecuador</h2>
                    <p style="color: rgba(255,255,255,0.85); font-size: 1.05rem; line-height: 1.6; margin-bottom: 2rem;">
                        El grabado láser no es una impresión superficial: es una transformación óptica que funde tu identidad en la materia prima. Cero tintas que se borran, cero etiquetas pegadas.
                    </p>
                    <ul style="color: rgba(255,255,255,0.9); margin-bottom: 2rem; padding-left: 0; list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 0.95rem;">
                        <li><span style="color: #8CFF32; font-weight: bold; margin-right: 8px;">✓</span> <strong>Sin barreras de volumen:</strong> personaliza desde 1 pieza exclusiva hasta lotes corporativos selectos.</li>
                        <li><span style="color: #8CFF32; font-weight: bold; margin-right: 8px;">✓</span> <strong>Grabado 100% indeleble:</strong> resistente al uso diario continuo, lavados y roces.</li>
                        <li><span style="color: #8CFF32; font-weight: bold; margin-right: 8px;">✓</span> <strong>Resolución micrométrica:</strong> definición nítida en letras pequeñas, isotipos y detalles finos.</li>
                        <li><span style="color: #8CFF32; font-weight: bold; margin-right: 8px;">✓</span> <strong>Inspección visual unitaria:</strong> cada pieza es revisada y pulida a mano antes del empaque.</li>
                    </ul>
                    <a href="cotizacion.php" class="btn btn-primary" style="background-color: var(--primary); border: none; padding: 14px 32px; font-weight: 600; text-transform: none; font-size: 0.9rem;">Cotizar proyecto de personalización</a>
                </div>
            </div>
        </section>

        <!-- 8. Sección: Antes y Después del Grabado (Rediseño Estilo Premium) -->
        <section id="antes-despues" class="section-padding container reveal-on-scroll" style="border-top: 1px solid var(--border); padding-top: 5rem; padding-bottom: 5rem;">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle" style="color: var(--primary); border-color: var(--primary); font-weight: 600; padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">GARANTÍA DE ACABADO</span>
                <h2 style="font-family: var(--font-heading); font-size: 2.2rem; font-weight: 500; margin-top: 10px;">Antes y después del grabado</h2>
                <p style="color: var(--text-muted); font-size: 0.95rem;">Mira cómo un producto simple se convierte en una pieza personalizada para tu marca.</p>
            </div>
            
            <div class="interactive-slider-wrapper" style="display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 50px; align-items: center; max-width: 1000px; margin: 0 auto;">
                
                <!-- Comparador Deslizable -->
                <div class="before-after-slider-container" style="position: relative; width: 100%; aspect-ratio: 1.2; border-radius: 8px; overflow: hidden; user-select: none; border: 1px solid var(--border); box-shadow: var(--shadow-lg); cursor: ew-resize;">
                    <!-- Etiquetas en la esquina -->
                    <div style="position: absolute; top: 15px; left: 15px; background: rgba(0,0,0,0.8); color: white; padding: 6px 12px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; border-radius: 4px; z-index: 8; letter-spacing: 0.05em;">Antes (Liso)</div>
                    <div style="position: absolute; top: 15px; right: 15px; background: var(--primary); color: white; padding: 6px 12px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; border-radius: 4px; z-index: 8; letter-spacing: 0.05em;">Después (Grabado)</div>
                    
                    <img id="slider-img-after" src="images/termo_after.webp" alt="Termo Grabado" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none;">
                    <div id="slider-before-wrap" style="position: absolute; inset: 0; width: 100%; height: 100%; overflow: hidden; pointer-events: none; clip-path: inset(0 50% 0 0); border-right: 2px solid white;">
                        <img id="slider-img-before" src="images/termo_before.webp" alt="Termo Liso" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none;">
                    </div>
                    <div id="slider-handle" style="position: absolute; top: 0; bottom: 0; left: 50%; width: 4px; background: white; z-index: 10; margin-left: -2px; pointer-events: none;">
                        <div style="position: absolute; top: 50%; left: 50%; width: 40px; height: 40px; border-radius: 50%; background: white; margin-top: -20px; margin-left: -20px; box-shadow: 0 4px 10px rgba(0,0,0,0.15); display: flex; align-items: center; justify-content: center; border: 1px solid var(--border);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2D3748" stroke-width="2.5"><polyline points="8 17 12 21 16 17"/><polyline points="8 7 12 3 16 7"/></svg>
                        </div>
                    </div>
                </div>
                
                <!-- Info y Selector de Productos (Estilo Tabs Block) -->
                <div class="before-after-selector-wrap">
                    <h3 id="slider-title" style="font-family: var(--font-heading); font-size: 2.2rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--dark);">Termo grabado</h3>
                    <p id="slider-text" style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem;">De producto simple a detalle corporativo listo para entregar.</p>
                    
                    <div class="before-after-tabs" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 2rem;">
                        <div class="slider-tab-btn active" data-prod="termo" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border: 2px solid var(--primary); border-radius: 6px; background: white; cursor: pointer; color: var(--primary); font-weight: 600; transition: all 0.2s ease;">
                            <span>Termo de Acero</span>
                            <span style="font-size: 0.85rem; font-weight: 600;">Ver muestra →</span>
                        </div>
                        <div class="slider-tab-btn" data-prod="agenda" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border: 1px solid var(--border); border-radius: 6px; background: white; cursor: pointer; color: var(--dark); font-weight: 500; transition: all 0.2s ease;">
                            <span>Agenda de Cuero</span>
                            <span style="font-size: 0.85rem; font-weight: 500;">Ver muestra →</span>
                        </div>
                        <div class="slider-tab-btn" data-prod="caja" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border: 1px solid var(--border); border-radius: 6px; background: white; cursor: pointer; color: var(--dark); font-weight: 500; transition: all 0.2s ease;">
                            <span>Caja de Madera</span>
                            <span style="font-size: 0.85rem; font-weight: 500;">Ver muestra →</span>
                        </div>
                    </div>
                    
                    <a href="cotizacion.php" class="btn btn-primary before-after-cta-btn" style="width: 100%; text-align: center; background-color: var(--primary); border: none; padding: 15px 0; font-weight: 700; text-transform: uppercase; border-radius: 4px; display: block; color: white; letter-spacing: 0.05em; transition: all 0.3s ease;">QUIERO ALGO SIMILAR</a>
                </div>
            </div>
        </section>

        <!-- 9. Cómo hacemos tu pedido -->
        <section id="proceso" class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle">Metodología de Taller</span>
                <h2>Cómo personalizamos cada pieza</h2>
                <p>Cuidamos cada fase con precisión óptica y rigor artesanal, garantizando acabados de máxima definición.</p>
            </div>
            
            <div class="process-grid">
                <div class="process-step" style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-md);">
                    <div class="process-number" style="color: var(--primary); font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">01</div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">Eliges la pieza base</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Seleccionas termos térmicos, libretas en cuero PU, madera, tagua ecológica o credenciales.</p>
                </div>
                <div class="process-step" style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-md);">
                    <div class="process-number" style="color: var(--primary); font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">02</div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">Envías tu marca o logotipo</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Analizamos vectores, curvas y contrastes para garantizar un grabado con nitidez microscópica.</p>
                </div>
                <div class="process-step" style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-md);">
                    <div class="process-number" style="color: var(--primary); font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">03</div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">Calibración y render técnico</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Te enviamos una maqueta digital con escala y ubicación exacta antes de pasar a máquina.</p>
                </div>
                <div class="process-step" style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-md);">
                    <div class="process-number" style="color: var(--primary); font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">04</div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">Grabado láser y control unitario</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Grabamos cada pieza en taller y realizamos una inspección visual manual antes del despacho.</p>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 3.5rem;">
                <a href="cotizacion.php" class="btn btn-primary" style="padding: 12px 28px; text-transform: none;">Iniciar cotización sin mínimos forzados</a>
            </div>
        </section>

        <!-- 10. Preguntas frecuentes (Refinadas y Persuasivas) -->
        <section id="preguntas-frecuentes" class="section-padding section-bg-light reveal-on-scroll">
            <div class="container" style="max-width: 800px;">
                <div class="section-header center">
                    <span class="section-subtitle" style="color: var(--primary); border-color: var(--primary); font-weight: 600; padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">DUDAS RESUELTAS</span>
                    <h2 style="font-family: var(--font-heading); font-size: 2.2rem; font-weight: 500; margin-top: 10px;">Preguntas sobre personalización</h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem;">Transparencia total sobre nuestros acabados, cantidades y tiempos de entrega.</p>
                </div>
                
                <div class="faq-accordion" style="margin-top: 2.5rem; display: flex; flex-direction: column; gap: 14px;">
                    <!-- Pregunta 1: Desarmar la idea de pedido masivo -->
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white; transition: all 0.3s ease;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.98rem; color: var(--dark);">¿Tengo que hacer un pedido masivo o puedo personalizar cantidades pequeñas o piezas exclusivas?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;"><strong>No exigimos pedidos masivos.</strong> En CardNet nos especializamos en la personalización de alta calidad y precisión de taller, no en la maquila genérica en serie. Puedes personalizar desde piezas individuales y regalos directivos exclusivos hasta lotes corporativos selectos. Cada unidad recibe exactamente la misma calibración óptica, inspección minuciosa y acabado perfecto.</p>
                        </div>
                    </div>
                    
                    <!-- Pregunta 2: Superioridad del grabado láser vs tintas -->
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white; transition: all 0.3s ease;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.98rem; color: var(--dark);">¿Por qué su grabado láser es superior a la impresión promocional genérica?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">Porque no utilizamos tintas superficiales que se decoloran, pelan o rayan en semanas. Trabajamos con tecnología láser de fibra óptica y CO2 que graba directamente sobre la estructura del material (acero inoxidable, cuero, madera, tagua, acrílico). El resultado es un relieve indeleble, elegante y con un tacto prémium que nunca se borra.</p>
                        </div>
                    </div>
                    
                    <!-- Pregunta 3 -->
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white; transition: all 0.3s ease;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.98rem; color: var(--dark);">¿Puedo enviar mi logo o diseño listo?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">Sí. Puedes hacernos llegar tu logotipo en formatos vectoriales (PDF, AI, SVG) o en imágenes de alta resolución. Nuestro equipo técnico optimiza los nodos y ajusta la escala para que el haz láser logre la máxima definición posible en el producto elegido.</p>
                        </div>
                    </div>

                    <!-- Pregunta 4 -->
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white; transition: all 0.3s ease;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.98rem; color: var(--dark);">¿Puedo ver una vista previa o muestra antes de grabar?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">Totalmente. Antes de iniciar cualquier fase de grabado o personalización en el taller, te compartiremos una muestra digital o maqueta (vista previa) para tu revisión y aprobación formal.</p>
                        </div>
                    </div>

                    <!-- Pregunta 5 -->
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white; transition: all 0.3s ease;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.98rem; color: var(--dark);">¿Hacen envíos a todo el Ecuador?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">Sí. Despachamos pedidos embalados cuidadosamente con protección de taller a todas las provincias y cantones del Ecuador (Quito, Guayaquil, Cuenca, Manta, Loja, etc.) a través de Servientrega o cooperativas de transporte seguras.</p>
                        </div>
                    </div>

                    <!-- Pregunta 6 -->
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white; transition: all 0.3s ease;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.98rem; color: var(--dark);">¿Cuál es el tiempo de personalización y entrega?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">Al contar con taller propio de tecnología láser, nuestros tiempos son ágiles: típicamente entre 2 a 4 días laborables a partir de la confirmación del pago y la aprobación del render final.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 11. CTA Final -->
        <section class="section-padding container reveal-on-scroll" style="text-align: center; max-width: 820px; margin-top: 1rem; margin-bottom: 2rem;">
            <span class="section-subtitle" style="color: var(--primary); border-color: var(--primary);">Taller de Autor</span>
            <h2 style="margin-bottom: 1.25rem; font-family: var(--font-heading); font-size: 2.3rem;">Tu marca merece la mejor personalización, no un grabado genérico</h2>
            <p style="margin-bottom: 2rem; font-size: 1.05rem; color: var(--text-muted); line-height: 1.6;">Trabajamos contigo desde piezas exclusivas y proyectos especiales hasta lotes corporativos de alto impacto. Sin barreras de volumen masivo y con la máxima precisión láser del país.</p>
            <div class="hero-actions" style="justify-content: center; display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="cotizacion.php" class="btn btn-primary" style="text-transform: none; padding: 14px 32px; font-size: 0.95rem;">Iniciar cotización de proyecto</a>
                <a href="productos.php" class="btn btn-secondary" style="background: white; text-transform: none; padding: 14px 32px; font-size: 0.95rem;">Explorar catálogo</a>
            </div>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts Modulares -->
    <script src="js/main.js?v=6.3"></script>
    <script src="js/slider.js?v=2.1"></script>
    <script src="js/animations.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Slider de Antes/Después con soporte táctil mejorado (evita que se mueva la pantalla al arrastrar)
            const container = document.querySelector(".before-after-slider-container");
            if (container) {
                const beforeWrap = document.getElementById("slider-before-wrap");
                const handle = document.getElementById("slider-handle");
                let active = false;
                
                function slide(x) {
                    const rect = container.getBoundingClientRect();
                    let position = ((x - rect.left) / rect.width) * 100;
                    if (position < 0) position = 0;
                    if (position > 100) position = 100;
                    beforeWrap.style.clipPath = `inset(0 ${100 - position}% 0 0)`;
                    handle.style.left = position + "%";
                }
                
                // Mousedown / Mousemove / Mouseup
                container.addEventListener("mousedown", (e) => {
                    active = true;
                    slide(e.clientX);
                });
                window.addEventListener("mouseup", () => active = false);
                window.addEventListener("mousemove", (e) => {
                    if (active) {
                        e.preventDefault();
                        slide(e.clientX);
                    }
                });
                
                // Touchstart / Touchmove / Touchend
                container.addEventListener("touchstart", (e) => {
                    active = true;
                    slide(e.touches[0].clientX);
                }, { passive: true });
                window.addEventListener("touchend", () => active = false);
                window.addEventListener("touchmove", (e) => {
                    if (active) {
                        // Importante: preventDefault evita el scroll de la página mientras se arrastra el comparador
                        if (e.cancelable) e.preventDefault();
                        slide(e.touches[0].clientX);
                    }
                }, { passive: false });
            }
            
            // Selector de productos del comparador (Clase de pestañas de bloque)
            const tabs = document.querySelectorAll(".slider-tab-btn");
            const sliderImgAfter = document.getElementById("slider-img-after");
            const sliderImgBefore = document.getElementById("slider-img-before");
            const sliderTitle = document.getElementById("slider-title");
            const sliderText = document.getElementById("slider-text");
            
            const prodData = {
                termo: {
                    title: "Termo de Acero",
                    text: "De producto simple a detalle corporativo listo para entregar.",
                    before: "images/termo_before.webp",
                    after: "images/termo_after.webp"
                },
                agenda: {
                    title: "Agenda de Cuero",
                    text: "De agenda lisa a pieza ejecutiva con identidad de marca.",
                    before: "images/agenda_before.webp",
                    after: "images/agenda_after.webp"
                },
                caja: {
                    title: "Caja de Madera",
                    text: "De empaque básico a presentación personalizada.",
                    before: "images/caja_before.webp",
                    after: "images/caja_after.webp"
                }
            };
            
            let currentProdIndex = 0;
            const prodsKeys = Object.keys(prodData);
            let beforeAfterInterval;

            function switchProduct(prodKey) {
                tabs.forEach(t => {
                    t.classList.remove("active");
                    t.style.borderColor = "var(--border)";
                    t.style.color = "var(--dark)";
                    t.style.fontWeight = "500";
                    t.querySelector("span:last-child").style.fontWeight = "500";
                });
                const activeTab = document.querySelector(`.slider-tab-btn[data-prod="${prodKey}"]`);
                if (activeTab) {
                    activeTab.classList.add("active");
                    activeTab.style.borderColor = "var(--primary)";
                    activeTab.style.color = "var(--primary)";
                    activeTab.style.fontWeight = "600";
                    activeTab.querySelector("span:last-child").style.fontWeight = "600";
                }
                
                const data = prodData[prodKey];
                if (data) {
                    sliderImgBefore.src = data.before;
                    sliderImgAfter.src = data.after;
                    sliderTitle.textContent = data.title;
                    sliderText.textContent = data.text;
                    
                    const beforeWrap = document.getElementById("slider-before-wrap");
                    const handle = document.getElementById("slider-handle");
                    if (beforeWrap && handle) {
                        beforeWrap.style.clipPath = `inset(0 50% 0 0)`;
                        handle.style.left = "50%";
                    }
                }
            }

            function startBeforeAfterAutoplay() {
                clearInterval(beforeAfterInterval);
                beforeAfterInterval = setInterval(() => {
                    currentProdIndex = (currentProdIndex + 1) % prodsKeys.length;
                    switchProduct(prodsKeys[currentProdIndex]);
                }, 5000); // 5 segundos
            }

            tabs.forEach((btn, index) => {
                btn.addEventListener("click", function() {
                    currentProdIndex = index;
                    const prod = this.getAttribute("data-prod");
                    switchProduct(prod);
                    startBeforeAfterAutoplay(); // Resetear temporizador al interactuar
                });
            });

            // Iniciar rotación automática al cargar
            startBeforeAfterAutoplay();

            // Fail-safe FAQ Accordion toggle inline
            document.querySelectorAll('.faq-trigger').forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    const item = this.closest('.faq-item');
                    const content = item.querySelector('.faq-content');
                    const isActive = item.classList.contains('active');
                    
                    // Cerrar todos los demás
                    document.querySelectorAll('.faq-item').forEach(i => {
                        i.classList.remove('active');
                        const c = i.querySelector('.faq-content');
                        if (c) {
                            c.style.maxHeight = '0px';
                        }
                    });
                    
                    if (!isActive) {
                        item.classList.add('active');
                        content.style.maxHeight = content.scrollHeight + 'px';
                    } else {
                        item.classList.remove('active');
                        content.style.maxHeight = '0px';
                    }
                });
            });

            // 12. Carrusel Showcase de Productos Destacados
            const track = document.querySelector(".showcase-carousel-track");
            const cards = document.querySelectorAll(".showcase-card");
            const prevBtn = document.querySelector(".showcase-control.prev");
            const nextBtn = document.querySelector(".showcase-control.next");
            const dots = document.querySelectorAll(".showcase-dot");
            
            if (track && cards.length > 0) {
                let index = 0;
                
                function getItemsPerPage() {
                    if (window.innerWidth <= 576) return 1;
                    if (window.innerWidth <= 992) return 2;
                    return 3;
                }
                
                function updateShowcase() {
                    const itemsPerPage = getItemsPerPage();
                    const maxIndex = Math.ceil(cards.length / itemsPerPage) - 1;
                    if (index > maxIndex) index = maxIndex;
                    if (index < 0) index = 0;
                    
                    const cardWidth = cards[0].getBoundingClientRect().width;
                    const gap = 24; // Gap de CSS
                    const amountToMove = index * (cardWidth * itemsPerPage + gap * itemsPerPage);
                    
                    track.style.transform = `translateX(-${amountToMove}px)`;
                    
                    // Actualizar dots
                    dots.forEach((dot, idx) => {
                        if (idx === index) {
                            dot.classList.add("active");
                        } else {
                            dot.classList.remove("active");
                        }
                    });
                }
                
                if (nextBtn) {
                    nextBtn.addEventListener("click", () => {
                        const itemsPerPage = getItemsPerPage();
                        const maxIndex = Math.ceil(cards.length / itemsPerPage) - 1;
                        if (index < maxIndex) {
                            index++;
                        } else {
                            index = 0; // Cíclico
                        }
                        updateShowcase();
                    });
                }
                
                if (prevBtn) {
                    prevBtn.addEventListener("click", () => {
                        const itemsPerPage = getItemsPerPage();
                        const maxIndex = Math.ceil(cards.length / itemsPerPage) - 1;
                        if (index > 0) {
                            index--;
                        } else {
                            index = maxIndex; // Cíclico
                        }
                        updateShowcase();
                    });
                }
                
                dots.forEach(dot => {
                    dot.addEventListener("click", (e) => {
                        index = parseInt(e.target.getAttribute("data-index"));
                        updateShowcase();
                    });
                });
                
                window.addEventListener("resize", updateShowcase);
                
                // Autoplay cada 5 segundos
                let showcaseInterval = setInterval(() => {
                    if (nextBtn) nextBtn.click();
                }, 5000);
                
                track.addEventListener("mouseenter", () => clearInterval(showcaseInterval));
                track.addEventListener("mouseleave", () => {
                    clearInterval(showcaseInterval);
                    showcaseInterval = setInterval(() => {
                        if (nextBtn) nextBtn.click();
                    }, 5000);
                });
            }
        });
    </script>
</body>
</html>
 