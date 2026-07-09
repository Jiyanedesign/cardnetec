<?php
session_start();
require_once 'db.php';

// 1. Obtener slides del carrusel activos
try {
    $stmt = $pdo->query("SELECT * FROM carrusel WHERE is_active = 1 ORDER BY order_val ASC");
    $slides = $stmt->fetchAll();
} catch (PDOException $e) {
    $slides = [];
}

// 2. Obtener productos destacados de identificación y luego personalización
try {
    $stmtFeatured = $pdo->query("SELECT p.*, c.slug as cat_slug FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE p.is_active = 1 AND p.is_featured = 1 ORDER BY p.order_val ASC LIMIT 12");
    $featured_products = $stmtFeatured->fetchAll();
} catch (PDOException $e) {
    $featured_products = [];
}

// Separar productos para mostrarlos ordenados: Identificación primero, Personalización después
$identification_prods = [];
$custom_prods = [];
foreach ($featured_products as $p) {
    if (in_array($p['cat_slug'], ['carnets', 'credenciales', 'cintas', 'porta-credenciales', 'tarjetas-pvc', 'accesorios'])) {
        $identification_prods[] = $p;
    } else {
        $custom_prods[] = $p;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CardNet.ec | Identificación y accesorios para personal en Ecuador</title>
    <meta name="description" content="Especialistas en carnets PVC, credenciales, cintas porta credenciales impresas y accesorios para identificar a tu equipo de forma profesional.">
    <link rel="canonical" href="https://cardnet.ec/index.php">
    <link rel="icon" type="image/png" href="favicon.png?v=2.0">
    <link rel="apple-touch-icon" href="favicon.png?v=2.0">
    
    <!-- Open Graph -->
    <meta property="og:title" content="CardNet.ec | Identificación y accesorios para personal">
    <meta property="og:description" content="Carnets, credenciales y cintas para empresas, instituciones y eventos en Ecuador.">
    <meta property="og:url" content="https://cardnet.ec">
    <meta property="og:type" content="website">

    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=2.0">
    <link rel="stylesheet" href="css/layout.css?v=2.0">
    <link rel="stylesheet" href="css/components.css?v=2.0">
    <link rel="stylesheet" href="css/pages.css?v=2.0">
    <link rel="stylesheet" href="css/animations.css?v=1.1.3">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <!-- MAIN CONTENT -->
    <main>
        
        <!-- 1. Hero Principal - Carrusel Automático Showroom de Identificación -->
        <section class="hero-block reveal-on-scroll" id="inicio" style="padding-top: 1rem; padding-bottom: 2rem;">
            <div class="container" style="position: relative;">
                <div class="hero-right-carousel" style="width: 100%; min-height: 520px; height: 70vh; position: relative; border-radius: var(--radius-md); overflow: hidden; background: #0f110e; border: 1px solid rgba(0,0,0,0.03); display: flex; flex-direction: column;">
                    <div class="hero-slider-track" style="width: 100%; height: 100%; position: relative; flex-grow: 1;">
                        
                        <?php if (!empty($slides)): ?>
                            <?php foreach ($slides as $idx => $slide): ?>
                                <div class="hero-slide-item <?php echo ($idx === 0) ? 'active' : ''; ?>" data-slide-index="<?php echo $idx; ?>" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: <?php echo ($idx === 0) ? '1' : '0'; ?>; visibility: <?php echo ($idx === 0) ? 'visible' : 'hidden'; ?>; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: <?php echo ($idx === 0) ? '5' : '1'; ?>; padding: 4rem; background-color: #0f110e;">
                                    <div style="position: absolute; right: 0; top: 0; bottom: 0; width: 55%; z-index: 1; -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,1) 40%, rgba(0,0,0,0) 100%); mask-image: linear-gradient(to left, rgba(0,0,0,1) 40%, rgba(0,0,0,0) 100%); pointer-events: none;">
                                        <?php
                                        $img_path = 'uploads/carnets.png';
                                        if (stripos($slide['title'], 'cintas') !== false) {
                                            $img_path = 'uploads/llavero.png';
                                        } elseif (stripos($slide['title'], 'porta') !== false || stripos($slide['title'], 'accesorios') !== false) {
                                            $img_path = 'uploads/caja.png';
                                        }
                                        ?>
                                        <img src="<?php echo $img_path; ?>" alt="<?php echo htmlspecialchars($slide['title']); ?>" style="width: 100%; height: 100%; object-fit: contain; object-position: right center; filter: drop-shadow(-10px 10px 25px rgba(0,0,0,0.2));">
                                    </div>
                                    <div style="position: absolute; inset: 0; background: linear-gradient(to right, #0f110e 0%, rgba(15, 17, 14, 0.8) 40%, transparent 100%); z-index: 2; pointer-events: none;"></div>
                                    
                                    <div style="position: relative; z-index: 3; max-width: 500px; color: white;">
                                        <h2 style="font-family: var(--font-heading); font-size: clamp(1.8rem, 3.5vw, 2.6rem); color: white; font-weight: 500; margin-bottom: 1rem; line-height: 1.2;"><?php echo htmlspecialchars($slide['title']); ?></h2>
                                        <p style="font-size: 1.05rem; color: rgba(255,255,255,0.85); line-height: 1.6; margin-bottom: 2.2rem;"><?php echo htmlspecialchars($slide['subtitle']); ?></p>
                                        <a href="<?php echo htmlspecialchars($slide['cta_url']); ?>" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600; text-transform: none;"><?php echo htmlspecialchars($slide['cta_text']); ?></a>
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
                <div class="satisfaction-item" style="display: flex; align-items: center; gap: 8px;">
                    <svg class="satisfaction-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span style="font-size: 0.9rem; font-weight: 500; color: var(--dark);">Identificación profesional certificada</span>
                </div>
                <div class="satisfaction-item" style="display: flex; align-items: center; gap: 8px;">
                    <svg class="satisfaction-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span style="font-size: 0.9rem; font-weight: 500; color: var(--dark);">Materiales duraderos y de alta resistencia</span>
                </div>
                <div class="satisfaction-item" style="display: flex; align-items: center; gap: 8px;">
                    <svg class="satisfaction-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span style="font-size: 0.9rem; font-weight: 500; color: var(--dark);">Vista previa antes de personalizar</span>
                </div>
            </div>
        </section>
        <!-- 3. Sección: Categorías Visuales (Masonry Grid de Identificación) -->
        <section id="categorias-visuales" class="section-padding" style="background: #121212; color: white; padding-top: 5rem; padding-bottom: 5rem;">
            <div class="container">
                <div class="section-header" style="margin-bottom: 3.5rem; text-align: left; max-width: 700px;">
                    <span class="section-subtitle" style="color: var(--primary); border-color: var(--primary);">Líneas del Taller</span>
                    <h2 style="font-family: var(--font-heading); font-size: 3rem; color: white; font-weight: 400; margin-bottom: 1rem;">Categorías destacadas</h2>
                    <p style="color: rgba(255,255,255,0.75); font-size: 1rem; line-height: 1.6; margin: 0;">Explora las principales líneas de identificación y personalización corporativa.</p>
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
                        align-items: start;
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

                <div class="premium-masonry-grid">
                    <!-- Columna Izquierda -->
                    <div class="premium-left-col">
                        <!-- Cintas y Lanyards (Antes Merchandising General, ahora enfocado a lo que quiere el cliente con la foto real) -->
                        <a href="productos.php?cat=cintas" class="premium-cat-card" style="aspect-ratio: 595/302;">
                            <img src="uploads/cintas_mockup.jpg" alt="Cintas y Lanyards">
                            <div class="premium-cat-overlay">
                                <h3 class="premium-cat-title">Cintas y lanyards</h3>
                                <p class="premium-cat-subtitle">Cintas impresas full color y accesorios de sujeción.</p>
                            </div>
                        </a>
                        
                        <div class="premium-bottom-row">
                            <!-- Cajas y Empaques -->
                            <a href="productos.php?cat=personalizacion" class="premium-cat-card" style="aspect-ratio: 288/337;">
                                <img src="uploads/caja.png" alt="Cajas y Empaques">
                                <div class="premium-cat-overlay">
                                    <h3 class="premium-cat-title">Cajas y Empaques</h3>
                                    <p class="premium-cat-subtitle">Packaging corporativo a medida.</p>
                                </div>
                            </a>
                            <!-- Especialidad Láser (Texto limpio sin duplicaciones) -->
                            <a href="#laser" class="premium-cat-card" style="aspect-ratio: 288/257;">
                                <img src="images/cat_laser.png" alt="Especialidad Láser">
                                <div class="premium-cat-overlay">
                                    <h3 class="premium-cat-title">Especialidad Láser</h3>
                                    <p class="premium-cat-subtitle">Grabado resistente al uso diario.</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Columna Derecha -->
                    <div class="premium-right-col" style="display: flex; flex-direction: column;">
                        <!-- Carnetización (Imagen real generada) -->
                        <a href="productos.php?cat=carnets" class="premium-cat-card" style="aspect-ratio: 288/460; height: 100%;">
                            <img src="uploads/carnet_mockup.jpg" alt="Carnetización" style="height: 100%; object-fit: cover;">
                            <div class="premium-cat-overlay" style="height: 100%;">
                                <h3 class="premium-cat-title">Carnetización</h3>
                                <p class="premium-cat-subtitle">Identificación profesional para empresas e instituciones.</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>


        <!-- 2. Productos principales de identificación -->
        <section id="productos" class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle">Identificación Corporativa</span>
                <h2>Productos de identificación</h2>
                <p>Carnets, credenciales, cintas y accesorios para empresas, instituciones, eventos y equipos.</p>
            </div>
            
            <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <?php if (!empty($identification_prods)): ?>
                    <?php foreach ($identification_prods as $prod): ?>
                        <div class="product-card catalog-product-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease, border-color 0.25s ease;">
                            <a href="producto.php?slug=<?php echo htmlspecialchars($prod['slug']); ?>" style="text-decoration: none; color: inherit; display: block; flex-grow: 1;">
                                <div class="product-card-image-wrap" style="position: relative; overflow: hidden; aspect-ratio: 1.2; background: var(--surface-light); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: center; padding: 1.5rem;">
                                    <?php
                                    $img_src = 'uploads/carnets.png';
                                    if (stripos($prod['slug'], 'cinta') !== false) {
                                        $img_src = 'uploads/llavero.png';
                                    } elseif (stripos($prod['slug'], 'porta') !== false || stripos($prod['slug'], 'accesorio') !== false) {
                                        $img_src = 'uploads/caja.png';
                                    }
                                    ?>
                                    <img src="<?php echo $img_src; ?>" style="max-height: 100%; max-width: 100%; object-fit: contain; transition: transform 0.4s ease;" alt="<?php echo htmlspecialchars($prod['name']); ?>">
                                </div>
                                <div style="padding: 1.5rem; display: flex; flex-direction: column;">
                                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($prod['name']); ?></h3>
                                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1rem;"><?php echo htmlspecialchars($prod['description_short']); ?></p>
                                </div>
                            </a>
                            <div style="padding: 0 1.5rem 1.5rem 1.5rem; display: flex; gap: 8px; margin-top: auto;">
                                <button class="btn btn-primary btn-add-to-quote" 
                                        data-slug="<?php echo htmlspecialchars($prod['slug']); ?>" 
                                        data-name="<?php echo htmlspecialchars($prod['name']); ?>" 
                                        data-price="<?php echo (float)$prod['price']; ?>" 
                                        style="flex-grow: 1; padding: 10px 14px; font-size: 0.8rem; font-weight: 600; border: none; cursor: pointer; text-transform: none;">
                                    <?php echo htmlspecialchars($prod['cta_text']); ?>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- 3. Sección Opciones de cintas y credenciales -->
        <section id="cintas-credenciales" class="section-padding" style="background: var(--surface-light); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
            <div class="container">
                <div class="section-header center" style="margin-bottom: 4rem;">
                    <span class="section-subtitle">Catálogo Detallado</span>
                    <h2>Opciones de cintas y credenciales</h2>
                    <p>Elige el tipo de identificación que mejor se adapta a tu empresa, evento o institución.</p>
                </div>

                <div class="grid-2" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px;">
                    <!-- BLOQUE 1: Cintas -->
                    <div style="background: white; padding: 2.5rem; border-radius: var(--radius-md); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                        <h3 style="font-family: var(--font-heading); font-size: 1.6rem; color: var(--dark); margin-bottom: 1.5rem; border-bottom: 2px solid var(--primary); padding-bottom: 8px;">Cintas porta credenciales</h3>
                        
                        <div style="display: flex; flex-direction: column; gap: 20px;">
                            <div>
                                <h4 style="font-size: 1.05rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">Cintas full color</h4>
                                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 10px;">Ideales para diseños con logotipo, colores corporativos y mayor presencia visual.</p>
                                <a href="cotizacion.php?producto=cintas-full-color" class="btn btn-secondary" style="font-size: 0.75rem; padding: 6px 12px; text-transform: none;">Cotizar cinta full color</a>
                            </div>
                            <hr style="border: 0; border-top: 1px solid var(--border);">
                            <div>
                                <h4 style="font-size: 1.05rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">Cintas a un color</h4>
                                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 10px;">Una opción sobria y funcional para identificar equipos o eventos.</p>
                                <a href="cotizacion.php?producto=cintas-un-color" class="btn btn-secondary" style="font-size: 0.75rem; padding: 6px 12px; text-transform: none;">Cotizar cinta a un color</a>
                            </div>
                            <hr style="border: 0; border-top: 1px solid var(--border);">
                            <div>
                                <h4 style="font-size: 1.05rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">Cintas sin impresión</h4>
                                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 10px;">Prácticas para identificaciones simples o uso interno.</p>
                                <a href="productos.php?cat=cintas" class="btn btn-secondary" style="font-size: 0.75rem; padding: 6px 12px; text-transform: none;">Ver opciones</a>
                            </div>
                        </div>
                    </div>

                    <!-- BLOQUE 2: Credenciales -->
                    <div style="background: white; padding: 2.5rem; border-radius: var(--radius-md); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                        <h3 style="font-family: var(--font-heading); font-size: 1.6rem; color: var(--dark); margin-bottom: 1.5rem; border-bottom: 2px solid var(--primary); padding-bottom: 8px;">Credenciales y porta credenciales</h3>
                        
                        <div style="display: flex; flex-direction: column; gap: 20px;">
                            <div>
                                <h4 style="font-size: 1.05rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">Credenciales PVC</h4>
                                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 10px;">Identificaciones duraderas y personalizadas para personal o instituciones.</p>
                                <a href="cotizacion.php?producto=credenciales-pvc" class="btn btn-secondary" style="font-size: 0.75rem; padding: 6px 12px; text-transform: none;">Cotizar credenciales</a>
                            </div>
                            <hr style="border: 0; border-top: 1px solid var(--border);">
                            <div>
                                <h4 style="font-size: 1.05rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">Credenciales para eventos</h4>
                                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 10px;">Opciones claras y funcionales para asistentes, staff, visitantes o equipos.</p>
                                <a href="cotizacion.php?producto=credenciales-eventos" class="btn btn-secondary" style="font-size: 0.75rem; padding: 6px 12px; text-transform: none;">Cotizar para evento</a>
                            </div>
                            <hr style="border: 0; border-top: 1px solid var(--border);">
                            <div>
                                <h4 style="font-size: 1.05rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">Porta credenciales</h4>
                                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 10px;">Diseños prácticos para proteger y usar las identificaciones todos los días.</p>
                                <a href="productos.php?cat=porta-credenciales" class="btn btn-secondary" style="font-size: 0.75rem; padding: 6px 12px; text-transform: none;">Ver porta credenciales</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Identificación para empresas, instituciones y eventos -->
        <section class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle">Soluciones de Taller</span>
                <h2>Identificación para empresas, instituciones y eventos</h2>
                <p>Preparamos soluciones de identificación para equipos que necesitan orden, presentación y claridad.</p>
            </div>

            <div class="grid-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
                <div style="background: white; border: 1px solid var(--border); padding: 1.5rem; border-radius: var(--radius-sm); display: flex; flex-direction: column;">
                    <h3 style="font-size: 1.15rem; font-family: var(--font-heading); margin-bottom: 0.5rem; font-weight: 500;">Empresas</h3>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex-grow: 1;">Carnets, cintas y accesorios para colaboradores, áreas internas y visitantes.</p>
                    <a href="cotizacion.php" class="btn btn-secondary" style="font-size: 0.75rem; padding: 8px 12px; text-align: center; text-transform: none;">Cotizar para mi empresa</a>
                </div>
                <div style="background: white; border: 1px solid var(--border); padding: 1.5rem; border-radius: var(--radius-sm); display: flex; flex-direction: column;">
                    <h3 style="font-size: 1.15rem; font-family: var(--font-heading); margin-bottom: 0.5rem; font-weight: 500;">Instituciones</h3>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex-grow: 1;">Identificación para personal administrativo, equipos de apoyo, estudiantes o miembros.</p>
                    <a href="cotizacion.php" class="btn btn-secondary" style="font-size: 0.75rem; padding: 8px 12px; text-align: center; text-transform: none;">Solicitar opciones</a>
                </div>
                <div style="background: white; border: 1px solid var(--border); padding: 1.5rem; border-radius: var(--radius-sm); display: flex; flex-direction: column;">
                    <h3 style="font-size: 1.15rem; font-family: var(--font-heading); margin-bottom: 0.5rem; font-weight: 500;">Eventos</h3>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex-grow: 1;">Credenciales, cintas y porta credenciales para staff, invitados y asistentes.</p>
                    <a href="cotizacion.php" class="btn btn-secondary" style="font-size: 0.75rem; padding: 8px 12px; text-align: center; text-transform: none;">Cotizar para evento</a>
                </div>
                <div style="background: white; border: 1px solid var(--border); padding: 1.5rem; border-radius: var(--radius-sm); display: flex; flex-direction: column;">
                    <h3 style="font-size: 1.15rem; font-family: var(--font-heading); margin-bottom: 0.5rem; font-weight: 500;">Equipos de trabajo</h3>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex-grow: 1;">Soluciones prácticas para identificar cargos, áreas y personal operativo.</p>
                    <a href="productos.php" class="btn btn-secondary" style="font-size: 0.75rem; padding: 8px 12px; text-align: center; text-transform: none;">Ver productos</a>
                </div>
            </div>
        </section>

        <!-- 5. Accesorios para el uso diario -->
        <section class="section-padding" style="background: var(--surface-light); border-top: 1px solid var(--border);">
            <div class="container reveal-on-scroll">
                <div class="section-header center" style="margin-bottom: 3.5rem;">
                    <span class="section-subtitle">Accesorios Diarios</span>
                    <h2>Accesorios para el uso diario</h2>
                    <p>Complementos prácticos para proteger, portar y presentar mejor cada credencial.</p>
                </div>

                <div class="grid-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 1.25rem; text-align: center; display: flex; flex-direction: column;">
                        <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 5px;">Porta carnets</h4>
                        <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 1rem; flex-grow: 1;">Protección práctica para carnets de uso diario.</p>
                        <a href="productos.php?cat=porta-credenciales" style="font-size: 0.75rem; color: var(--primary); font-weight: 600; text-decoration: none; text-transform: none;">Ver opciones</a>
                    </div>
                    <div style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 1.25rem; text-align: center; display: flex; flex-direction: column;">
                        <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 5px;">Yoyos retráctiles</h4>
                        <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 1rem; flex-grow: 1;">Accesorio cómodo para personal que usa identificación constantemente.</p>
                        <a href="cotizacion.php?producto=accesorios-identificacion" style="font-size: 0.75rem; color: var(--primary); font-weight: 600; text-decoration: none; text-transform: none;">Cotizar accesorios</a>
                    </div>
                    <div style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 1.25rem; text-align: center; display: flex; flex-direction: column;">
                        <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 5px;">Fundas transparentes</h4>
                        <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 1rem; flex-grow: 1;">Ideales para acreditaciones en eventos y credenciales rápidas.</p>
                        <a href="productos.php?cat=porta-credenciales" style="font-size: 0.75rem; color: var(--primary); font-weight: 600; text-decoration: none; text-transform: none;">Ver opciones</a>
                    </div>
                    <div style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 1.25rem; text-align: center; display: flex; flex-direction: column;">
                        <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 5px;">Clips y sujetadores</h4>
                        <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 1rem; flex-grow: 1;">Sujeción metálica o plástica segura para prender a la ropa.</p>
                        <a href="cotizacion.php?producto=accesorios-identificacion" style="font-size: 0.75rem; color: var(--primary); font-weight: 600; text-decoration: none; text-transform: none;">Cotizar accesorios</a>
                    </div>
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
                            <img src="images/mat_acero.png" alt="Acero" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.05em;">Acero</span>
                    </div>
                    <!-- Madera -->
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 12px; width: 140px;">
                        <div style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border); background: white;">
                            <img src="images/mat_madera.png" alt="Madera" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.05em;">Madera</span>
                    </div>
                    <!-- Acrílico -->
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 12px; width: 140px;">
                        <div style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border); background: white;">
                            <img src="images/mat_acrilico.png" alt="Acrílico" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.05em;">Acrílico</span>
                    </div>
                    <!-- Cuero/PU -->
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 12px; width: 140px;">
                        <div style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border); background: white;">
                            <img src="images/mat_cuero.png" alt="Cuero/PU" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.05em;">Cuero/PU</span>
                    </div>
                    <!-- Vidrio -->
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 12px; width: 140px;">
                        <div style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border); background: white;">
                            <img src="images/mat_vidrio.png" alt="Vidrio" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.05em;">Vidrio</span>
                    </div>
                    <!-- PVC -->
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 12px; width: 140px;">
                        <div style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border); background: white;">
                            <img src="images/mat_pvc.png" alt="PVC" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: 0.05em;">PVC</span>
                    </div>
                </div>
            </div>
        </section>


        <!-- 6. Sección secundaria: ahora también personalizamos productos corporativos -->
        <section id="personalizacion-adicional" class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle">Servicio Adicional</span>
                <h2>Ahora también personalizamos productos corporativos</h2>
                <p>Además de identificación, también preparamos detalles personalizados para empresas, clientes y eventos.</p>
            </div>
            
            <p style="font-size: 0.92rem; color: var(--text-muted); text-align: center; max-width: 750px; margin: 0 auto 2.5rem auto; line-height: 1.6;">
                También contamos con una línea de personalización corporativa para empresas que desean preparar detalles, kits o productos con su marca con grabado duradero y presentación cuidada.
            </p>

            <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                <?php if (!empty($custom_prods)): ?>
                    <?php foreach ($custom_prods as $prod): ?>
                        <div style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease;">
                            <div style="aspect-ratio: 1.25; background: var(--surface-light); display: flex; align-items: center; justify-content: center; padding: 1rem; border-bottom: 1px solid var(--border);">
                                <?php
                                $img_src = 'uploads/termo.png';
                                if (stripos($prod['slug'], 'agenda') !== false) {
                                    $img_src = 'uploads/agenda.png';
                                } elseif (stripos($prod['slug'], 'llavero') !== false) {
                                    $img_src = 'uploads/llavero.png';
                                } elseif (stripos($prod['slug'], 'caja') !== false) {
                                    $img_src = 'uploads/caja.png';
                                } elseif (stripos($prod['slug'], 'kit') !== false) {
                                    $img_src = 'uploads/kit.png';
                                } elseif (stripos($prod['slug'], 'placa') !== false) {
                                    $img_src = 'uploads/placa.png';
                                }
                                ?>
                                <img src="<?php echo $img_src; ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;" alt="<?php echo htmlspecialchars($prod['name']); ?>">
                            </div>
                            <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                                <h4 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--dark); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($prod['name']); ?></h4>
                                <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex-grow: 1;"><?php echo htmlspecialchars($prod['description_short']); ?></p>
                                <a href="cotizacion.php?producto=<?php echo htmlspecialchars($prod['slug']); ?>" class="btn btn-secondary" style="width: 100%; text-align: center; font-size: 0.8rem; padding: 10px 0; text-transform: none;">
                                    <?php echo htmlspecialchars($prod['cta_text']); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- 7. Grabado láser y personalización (Servicio técnico e ilustrativo) -->
        <section id="laser" class="section-padding container reveal-on-scroll" style="border-top: 1px solid var(--border);">
            <div class="laser-section" style="padding: 4rem; background-image: url('images/laser_action.jpg'); background-size: cover; background-position: center; border-radius: var(--radius-lg); overflow: hidden; position: relative;">
                <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(16, 20, 15, 0.98) 0%, rgba(16, 20, 15, 0.85) 45%, rgba(16, 20, 15, 0.2) 100%); z-index: 1;"></div>
                <div style="max-width: 600px; position: relative; z-index: 5;">
                    <span class="section-subtitle" style="color: #8CFF32; border-color: #8CFF32;">Servicio de Taller</span>
                    <h2 style="color: white; font-family: var(--font-heading); font-size: 2.2rem; font-weight: 500; margin-bottom: 1.25rem;">Grabado láser y personalización</h2>
                    <p style="color: rgba(255,255,255,0.85); font-size: 1.05rem; line-height: 1.6; margin-bottom: 2rem;">
                        Personalizamos productos seleccionados con acabados limpios y duraderos, ideales para detalles corporativos y piezas de presentación.
                    </p>
                    <ul style="color: rgba(255,255,255,0.9); margin-bottom: 2rem; padding-left: 20px; line-height: 1.8; font-size: 0.95rem;">
                        <li>✓ Acabado limpio y permanente sin tintas</li>
                        <li>✓ Grabado resistente al uso diario</li>
                        <li>✓ Excelente definición de trazos finos en metales</li>
                        <li>✓ Personalización de marcas en agendas y cajas</li>
                    </ul>
                    <a href="cotizacion.php" class="btn btn-primary" style="background-color: var(--primary); border: none; padding: 12px 28px; font-weight: 600; text-transform: none;">Cotizar personalización</a>
                </div>
            </div>
        </section>

        <!-- 8. Sección: Antes y Después del Grabado -->
        <section id="antes-despues" class="section-padding container reveal-on-scroll" style="border-top: 1px solid var(--border); padding-top: 5rem; padding-bottom: 5rem;">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle">Garantía de Acabado</span>
                <h2>Antes y después del grabado</h2>
                <p>Mira cómo un producto liso se convierte en una pieza personalizada para representar a tu marca.</p>
            </div>
            
            <div class="interactive-slider-wrapper" style="display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 40px; align-items: center; max-width: 1000px; margin: 0 auto;">
                
                <!-- Comparador Deslizable -->
                <div class="before-after-slider-container" style="position: relative; width: 100%; aspect-ratio: 1; border-radius: var(--radius-md); overflow: hidden; user-select: none; border: 1px solid var(--border); box-shadow: var(--shadow-lg); cursor: ew-resize;">
                    <img id="slider-img-after" src="images/termo_after.png" alt="Termo Grabado" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none;">
                    <div id="slider-before-wrap" style="position: absolute; inset: 0; width: 100%; height: 100%; overflow: hidden; pointer-events: none; clip-path: inset(0 50% 0 0); border-right: 2px solid white;">
                        <img id="slider-img-before" src="images/termo_before.png" alt="Termo Liso" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none;">
                    </div>
                    <div id="slider-handle" style="position: absolute; top: 0; bottom: 0; left: 50%; width: 4px; background: white; z-index: 10; margin-left: -2px; pointer-events: none;">
                        <div style="position: absolute; top: 50%; left: 50%; width: 40px; height: 40px; border-radius: 50%; background: white; margin-top: -20px; margin-left: -20px; box-shadow: 0 4px 10px rgba(0,0,0,0.15); display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2D3748" stroke-width="2.5"><polyline points="8 17 12 21 16 17"/><polyline points="8 7 12 3 16 7"/></svg>
                        </div>
                    </div>
                </div>
                
                <!-- Info y Selector de Productos -->
                <div class="before-after-selector-wrap">
                    <h3 id="slider-title" style="font-family: var(--font-heading); font-size: 1.85rem; font-weight: 500; margin-bottom: 0.75rem; color: var(--dark);">Termo grabado</h3>
                    <p id="slider-text" style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.6; margin-bottom: 2rem;">De producto simple a detalle corporativo listo para entregar.</p>
                    
                    <div style="display: flex; flex-direction: column; gap: 10px; background: var(--surface-light); padding: 1.5rem; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 5px;">Elige el producto a visualizar:</span>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <button class="slider-tab-btn active" data-prod="termo" style="border: 1px solid var(--border); padding: 8px 16px; border-radius: 20px; font-weight: 500; font-size: 0.85rem; cursor: pointer; transition: all 0.2s ease; background: white;">Termo</button>
                            <button class="slider-tab-btn" data-prod="agenda" style="border: 1px solid var(--border); padding: 8px 16px; border-radius: 20px; font-weight: 500; font-size: 0.85rem; cursor: pointer; transition: all 0.2s ease; background: #fbfbfb;">Agenda</button>
                            <button class="slider-tab-btn" data-prod="caja" style="border: 1px solid var(--border); padding: 8px 16px; border-radius: 20px; font-weight: 500; font-size: 0.85rem; cursor: pointer; transition: all 0.2s ease; background: #fbfbfb;">Caja</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 9. Cómo hacemos tu pedido -->
        <section id="proceso" class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <h2>Cómo hacemos tu pedido</h2>
                <p>Nuestra metodología simplificada para garantizar que cada pedido cumpla tus expectativas.</p>
            </div>
            
            <div class="process-grid">
                <div class="process-step" style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-md);">
                    <div class="process-number" style="color: var(--primary); font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">01</div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">Eliges el producto</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Seleccionas carnets, credenciales, cintas, accesorios o productos personalizados.</p>
                </div>
                <div class="process-step" style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-md);">
                    <div class="process-number" style="color: var(--primary); font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">02</div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">Nos envías tu logo o idea</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Revisamos tu diseño, colores y el tipo de producto que necesitas.</p>
                </div>
                <div class="process-step" style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-md);">
                    <div class="process-number" style="color: var(--primary); font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">03</div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">Preparamos una vista previa</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Te mostramos una referencia visual de cómo quedará antes de personalizar.</p>
                </div>
                <div class="process-step" style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-md);">
                    <div class="process-number" style="color: var(--primary); font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">04</div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">Personalizamos y entregamos</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Preparamos tus piezas para que estén listas para representar a tu equipo o empresa.</p>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 3.5rem;">
                <a href="cotizacion.php" class="btn btn-primary" style="padding: 12px 28px; text-transform: none;">Iniciar cotización</a>
            </div>
        </section>

        <!-- 10. Preguntas frecuentes (FAQ prioritario de identificación) -->
        <section id="preguntas-frecuentes" class="section-padding section-bg-light reveal-on-scroll">
            <div class="container" style="max-width: 800px;">
                <div class="section-header center">
                    <span class="section-subtitle">Dudas Comunes</span>
                    <h2>Preguntas frecuentes</h2>
                    <p>Lo básico que necesitas saber antes de cotizar tus identificaciones o productos personalizados.</p>
                </div>
                
                <div class="faq-accordion" style="margin-top: 2rem; display: flex; flex-direction: column; gap: 10px;">
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.95rem;">¿Hacen carnets personalizados?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Sí. Preparamos carnets PVC y credenciales para empresas, instituciones, eventos y equipos de trabajo en diferentes espesores y acabados.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.95rem;">¿También hacen cintas porta credenciales?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Sí. Fabricamos y suministramos cintas (lanyards) con impresión sublimada full color, estampación a un color o cintas lisas sin impresión en una gran variedad de anchos y ganchos.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.95rem;">¿Tienen porta credenciales y accesorios?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Sí. Contamos con porta carnets rígidos, fundas de PVC transparentes flexibles, yoyos retráctiles corporativos, clips cocodrilo y adaptadores de sujeción.</p>
                        </div>
                    </div>

                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.95rem;">¿Puedo enviar mi logo o diseño listo?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Sí. Puedes adjuntar tu logotipo en formato vectorial (.AI, .SVG, .PDF) o en imagen de alta resolución para que preparemos la adaptación.</p>
                        </div>
                    </div>

                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.95rem;">¿Puedo ver una vista previa antes de producir?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Totalmente. Preparamos una muestra digital (vista previa) para tu aprobación formal antes de mandar a imprimir o personalizar en el taller.</p>
                        </div>
                    </div>

                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.95rem;">¿También personalizan agendas, llaveros o termos?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Sí. Ofrecemos como servicio adicional una línea curada de productos personalizados de presentación (termos, agendas, llaveros, placas y kits) con acabados limpios.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 11. CTA Final -->
        <section class="section-padding container reveal-on-scroll" style="text-align: center; max-width: 800px; margin-top: 1rem; margin-bottom: 2rem;">
            <span class="section-subtitle" style="color: var(--primary); border-color: var(--primary);">Contacto Corporativo</span>
            <h2 style="margin-bottom: 1.25rem; font-family: var(--font-heading);">¿Necesitas identificar a tu equipo o preparar productos para tu empresa?</h2>
            <p style="margin-bottom: 2rem; font-size: 1rem; color: var(--text-muted); line-height: 1.6;">Cuéntanos qué necesitas y te ayudamos a elegir una solución de identificación y marcaje de marca adecuada.</p>
            <div class="hero-actions" style="justify-content: center; display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="cotizacion.php" class="btn btn-primary" style="text-transform: none;">Iniciar cotización</a>
                <a href="productos.php" class="btn btn-secondary" style="background: white; text-transform: none;">Ver productos</a>
            </div>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts Modulares -->
    <script src="js/main.js?v=3.5"></script>
    <script src="js/slider.js"></script>
    <script src="js/animations.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Slider de Antes/Después
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
                
                container.addEventListener("mousedown", () => active = true);
                window.addEventListener("mouseup", () => active = false);
                window.addEventListener("mousemove", (e) => { if (active) slide(e.clientX); });
                container.addEventListener("touchstart", () => active = true);
                window.addEventListener("touchend", () => active = false);
                window.addEventListener("touchmove", (e) => { if (active) slide(e.touches[0].clientX); });
            }
            
            // Selector de productos del comparador
            const tabs = document.querySelectorAll(".slider-tab-btn");
            const sliderImgAfter = document.getElementById("slider-img-after");
            const sliderImgBefore = document.getElementById("slider-img-before");
            const sliderTitle = document.getElementById("slider-title");
            const sliderText = document.getElementById("slider-text");
            
            const prodData = {
                termo: {
                    title: "Termo grabado",
                    text: "De producto simple a detalle corporativo listo para entregar.",
                    before: "images/termo_before.png",
                    after: "images/termo_after.png"
                },
                agenda: {
                    title: "Agenda personalizada",
                    text: "De agenda lisa a pieza ejecutiva con identidad de marca.",
                    before: "images/agenda_before.png",
                    after: "images/agenda_after.png"
                },
                caja: {
                    title: "Caja corporativa",
                    text: "De empaque básico a presentación personalizada.",
                    before: "images/caja_before.png",
                    after: "images/caja_after.png"
                }
            };
            
            tabs.forEach(btn => {
                btn.addEventListener("click", function() {
                    tabs.forEach(t => {
                        t.classList.remove("active");
                        t.style.background = "#fbfbfb";
                    });
                    this.classList.add("active");
                    this.style.background = "white";
                    
                    const prod = this.getAttribute("data-prod");
                    const data = prodData[prod];
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
                });
            });
        });
    </script>
</body>
</html>
