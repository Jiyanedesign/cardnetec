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

// 2. Obtener trabajos realizados (portfolio) desde la administración
try {
    $stmtShowcase = $pdo->query("SELECT p.*, c.slug as cat_slug FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE p.is_active = 1 AND p.is_featured = 1 ORDER BY p.order_val ASC");
    $showcase_items = $stmtShowcase->fetchAll();
} catch (PDOException $e) {
    $showcase_items = [];
}

// Obtener también productos de personalización para la sección secundaria
try {
    $stmtCustom = $pdo->query("SELECT p.*, c.slug as cat_slug FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE p.is_active = 1 AND c.slug = 'personalizacion' ORDER BY p.order_val ASC");
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
    <link rel="stylesheet" href="css/base.css?v=3.6">
    <link rel="stylesheet" href="css/layout.css?v=3.6">
    <link rel="stylesheet" href="css/components.css?v=3.6">
    <link rel="stylesheet" href="css/pages.css?v=3.6">
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
                <div class="hero-right-carousel" style="width: 100%; min-height: 520px; height: 70vh; position: relative; border-radius: var(--radius-md); overflow: hidden; background: #0f110e; border: 1px solid rgba(0,0,0,0.03); display: flex; flex-direction: column;">
                    <div class="hero-slider-track" style="width: 100%; height: 100%; position: relative; flex-grow: 1;">
                        
                        <?php if (!empty($slides)): ?>
                            <?php foreach ($slides as $idx => $slide): ?>
                                <div class="hero-slide-item <?php echo ($idx === 0) ? 'active' : ''; ?>" data-slide-index="<?php echo $idx; ?>" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: <?php echo ($idx === 0) ? '1' : '0'; ?>; visibility: <?php echo ($idx === 0) ? 'visible' : 'hidden'; ?>; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: <?php echo ($idx === 0) ? '5' : '1'; ?>; padding: 4rem; background-color: #0f110e;">
                                    <div style="position: absolute; right: 0; top: 0; bottom: 0; width: 55%; z-index: 1; -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,1) 40%, rgba(0,0,0,0) 100%); mask-image: linear-gradient(to left, rgba(0,0,0,1) 40%, rgba(0,0,0,0) 100%); pointer-events: none;">
                                        <?php
                                        $img_path = !empty($slide['image']) ? 'uploads/' . $slide['image'] : 'uploads/carnet_mockup.jpg';
                                        ?>
                                        <img src="<?php echo $img_path; ?>?v=2.2" alt="<?php echo htmlspecialchars($slide['title']); ?>" style="width: 100%; height: 100%; object-fit: contain; object-position: right center; filter: drop-shadow(-10px 10px 25px rgba(0,0,0,0.2));">
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
                                <div class="logos-ticker-item">
                                    <img src="uploads/<?php echo htmlspecialchars($client['logo_path']); ?>" alt="<?php echo htmlspecialchars($client['name']); ?>">
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
                            <a href="productos.php?cat=personalizacion" class="premium-cat-card" style="height: 100%; min-height: 260px;">
                                <img src="uploads/caja.png" alt="Cajas y Empaques">
                                <div class="premium-cat-overlay">
                                    <h3 class="premium-cat-title">Cajas y Empaques</h3>
                                    <p class="premium-cat-subtitle">Packaging corporativo a medida.</p>
                                </div>
                            </a>
                            <!-- Especialidad Láser (Texto limpio sin duplicaciones) -->
                            <a href="#laser" class="premium-cat-card" style="height: 100%; min-height: 260px;">
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



        <!-- 2. Productos principales de identificación (Showcase Carrusel Moderno) -->
        <section id="productos" class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3rem;">
                <span class="section-subtitle">Galería de Soluciones</span>
                <h2>Nuestros productos destacados</h2>
                <p>Una muestra visual de las soluciones de identificación y personalización de nuestro taller.</p>
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
                    border: 1px solid var(--border);
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.02);
                    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
                    display: flex;
                    flex-direction: column;
                }
                .showcase-card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
                    border-color: var(--primary);
                }
                .showcase-image-wrap {
                    width: 100%;
                    aspect-ratio: 1.25;
                    background: var(--surface-light);
                    border-bottom: 1px solid var(--border);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    overflow: hidden;
                    padding: 1.5rem;
                    box-sizing: border-box;
                }
                .showcase-image-wrap img {
                    max-height: 100%;
                    max-width: 100%;
                    object-fit: contain;
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
                    border: 1px solid var(--border);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
                    z-index: 10;
                    transition: all 0.2s ease;
                }
                .showcase-control:hover {
                    background: var(--primary);
                    color: white;
                    border-color: var(--primary);
                    box-shadow: 0 6px 15px rgba(99,174,44,0.2);
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
                    <!-- Carnets PVC -->
                    <div class="showcase-card">
                        <div class="showcase-image-wrap">
                            <img src="uploads/carnet_mockup.jpg?v=2.2" alt="Carnets PVC">
                        </div>
                        <div class="showcase-info">
                            <h3 class="showcase-title">Carnets PVC Corporativos</h3>
                        </div>
                    </div>
                    
                    <!-- Cintas y lanyards -->
                    <div class="showcase-card">
                        <div class="showcase-image-wrap">
                            <img src="uploads/cintas_mockup.jpg?v=2.2" alt="Cintas y lanyards">
                        </div>
                        <div class="showcase-info">
                            <h3 class="showcase-title">Cintas Porta Credenciales</h3>
                        </div>
                    </div>
                    
                    <!-- Porta credenciales -->
                    <div class="showcase-card">
                        <div class="showcase-image-wrap">
                            <img src="uploads/llavero.png" alt="Porta credenciales y accesorios">
                        </div>
                        <div class="showcase-info">
                            <h3 class="showcase-title">Porta Carnets y Accesorios</h3>
                        </div>
                    </div>
                    
                    <!-- Agendas -->
                    <div class="showcase-card">
                        <div class="showcase-image-wrap">
                            <img src="uploads/agenda.png" alt="Agendas grabadas">
                        </div>
                        <div class="showcase-info">
                            <h3 class="showcase-title">Agendas Personalizadas</h3>
                        </div>
                    </div>
                    
                    <!-- Termos -->
                    <div class="showcase-card">
                        <div class="showcase-image-wrap">
                            <img src="uploads/termo.png" alt="Termos con láser">
                        </div>
                        <div class="showcase-info">
                            <h3 class="showcase-title">Termos Grabados Láser</h3>
                        </div>
                    </div>
                    
                    <!-- Cajas -->
                    <div class="showcase-card">
                        <div class="showcase-image-wrap">
                            <img src="uploads/caja.png" alt="Cajas de presentación">
                        </div>
                        <div class="showcase-info">
                            <h3 class="showcase-title">Cajas y Empaques de Madera</h3>
                        </div>
                    </div>
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
                    <span class="section-subtitle">Catálogo Detallado</span>
                    <h2>Opciones de cintas y credenciales</h2>
                    <p>Elige el tipo de identificación que mejor se adapta a tu empresa, evento o institución.</p>
                </div>

                <div class="grid-2" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px;">
                    <!-- BLOQUE 1: Cintas -->
                    <div style="background: white; padding: 2.5rem; border-radius: var(--radius-md); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                        <h3 style="font-family: var(--font-heading); font-size: 1.6rem; color: var(--dark); margin-bottom: 2rem; border-bottom: 2px solid var(--primary); padding-bottom: 8px;">Cintas porta credenciales</h3>
                        
                        <div style="display: flex; flex-direction: column; gap: 24px;">
                            <!-- Cintas full color -->
                            <div style="display: flex; gap: 20px; align-items: flex-start;">
                                <div style="width: 90px; height: 90px; border-radius: 8px; overflow: hidden; flex-shrink: 0; border: 1px solid var(--border); background: var(--surface-light);">
                                    <img src="uploads/cintas_full_color.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Cintas full color">
                                </div>
                                <div style="flex-grow: 1;">
                                    <h4 style="font-size: 1.05rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">Cintas full color</h4>
                                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 10px;">Sublimación en poliéster suave de alta resolución.</p>
                                    <a href="cotizacion.php?producto=cintas-full-color" class="btn btn-secondary" style="font-size: 0.72rem; padding: 4px 10px; text-transform: none; background: white; display: inline-block;">Cotizar</a>
                                </div>
                            </div>
                            
                            <hr style="border: 0; border-top: 1px solid var(--border); margin: 0;">
                            
                            <!-- Cintas a un color -->
                            <div style="display: flex; gap: 20px; align-items: flex-start;">
                                <div style="width: 90px; height: 90px; border-radius: 8px; overflow: hidden; flex-shrink: 0; border: 1px solid var(--border); background: var(--surface-light);">
                                    <img src="uploads/cintas_mockup.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Cintas a un color">
                                </div>
                                <div style="flex-grow: 1;">
                                    <h4 style="font-size: 1.05rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">Cintas a un color</h4>
                                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 10px;">Serigrafía de alta adherencia para logotipos sólidos y sobrios.</p>
                                    <a href="cotizacion.php?producto=cintas-un-color" class="btn btn-secondary" style="font-size: 0.72rem; padding: 4px 10px; text-transform: none; background: white; display: inline-block;">Cotizar</a>
                                </div>
                            </div>
                            
                            <hr style="border: 0; border-top: 1px solid var(--border); margin: 0;">
                            
                            <!-- Cintas sin impresión -->
                            <div style="display: flex; gap: 20px; align-items: flex-start;">
                                <div style="width: 90px; height: 90px; border-radius: 8px; overflow: hidden; flex-shrink: 0; border: 1px solid var(--border); background: var(--surface-light);">
                                    <img src="uploads/cintas_sin_impresion.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Cintas sin impresión">
                                </div>
                                <div style="flex-grow: 1;">
                                    <h4 style="font-size: 1.05rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">Cintas sin impresión</h4>
                                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 10px;">Lanyards de tela de alta resistencia en colores corporativos básicos.</p>
                                    <a href="productos.php?cat=cintas" class="btn btn-secondary" style="font-size: 0.72rem; padding: 4px 10px; text-transform: none; background: white; display: inline-block;">Ver catálogo</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BLOQUE 2: Credenciales -->
                    <div style="background: white; padding: 2.5rem; border-radius: var(--radius-md); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                        <h3 style="font-family: var(--font-heading); font-size: 1.6rem; color: var(--dark); margin-bottom: 2rem; border-bottom: 2px solid var(--primary); padding-bottom: 8px;">Credenciales y porta credenciales</h3>
                        
                        <div style="display: flex; flex-direction: column; gap: 24px;">
                            <!-- Credenciales PVC -->
                            <div style="display: flex; gap: 20px; align-items: flex-start;">
                                <div style="width: 90px; height: 90px; border-radius: 8px; overflow: hidden; flex-shrink: 0; border: 1px solid var(--border); background: var(--surface-light);">
                                    <img src="uploads/carnet_mockup.jpg?v=2.2" style="width: 100%; height: 100%; object-fit: cover;" alt="Credenciales PVC">
                                </div>
                                <div style="flex-grow: 1;">
                                    <h4 style="font-size: 1.05rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">Credenciales PVC</h4>
                                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 10px;">Laminado de alta durabilidad impreso a doble cara para colaboradores.</p>
                                    <a href="cotizacion.php?producto=credenciales-pvc" class="btn btn-secondary" style="font-size: 0.72rem; padding: 4px 10px; text-transform: none; background: white; display: inline-block;">Cotizar</a>
                                </div>
                            </div>
                            
                            <hr style="border: 0; border-top: 1px solid var(--border); margin: 0;">
                            
                            <!-- Credenciales para eventos -->
                            <div style="display: flex; gap: 20px; align-items: flex-start;">
                                <div style="width: 90px; height: 90px; border-radius: 8px; overflow: hidden; flex-shrink: 0; border: 1px solid var(--border); background: var(--surface-light);">
                                    <img src="uploads/carousel_2.jpg?v=2.2" style="width: 100%; height: 100%; object-fit: cover;" alt="Credenciales para eventos">
                                </div>
                                <div style="flex-grow: 1;">
                                    <h4 style="font-size: 1.05rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">Credenciales para eventos</h4>
                                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 10px;">Tarjetas de gran formato para staff, prensa y asistentes.</p>
                                    <a href="cotizacion.php?producto=credenciales-eventos" class="btn btn-secondary" style="font-size: 0.72rem; padding: 4px 10px; text-transform: none; background: white; display: inline-block;">Cotizar</a>
                                </div>
                            </div>
                            
                            <hr style="border: 0; border-top: 1px solid var(--border); margin: 0;">
                            
                            <!-- Porta credenciales -->
                            <div style="display: flex; gap: 20px; align-items: flex-start;">
                                <div style="width: 90px; height: 90px; border-radius: 8px; overflow: hidden; flex-shrink: 0; border: 1px solid var(--border); background: var(--surface-light);">
                                    <img src="uploads/fundas.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Porta credenciales">
                                </div>
                                <div style="flex-grow: 1;">
                                    <h4 style="font-size: 1.05rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">Porta credenciales</h4>
                                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 10px;">Soportes rígidos o fundas de PVC flexible para proteger identificaciones.</p>
                                    <a href="productos.php?cat=porta-credenciales" class="btn btn-secondary" style="font-size: 0.72rem; padding: 4px 10px; text-transform: none; background: white; display: inline-block;">Ver catálogo</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Identificación para empresas, instituciones y eventos con Fotos -->
        <section class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle">Soluciones de Taller</span>
                <h2>Identificación para empresas, instituciones y eventos</h2>
                <p>Preparamos soluciones de identificación para equipos que necesitan orden, presentación y claridad.</p>
            </div>

            <div class="grid-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
                <!-- Empresas -->
                <div class="company-card-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; display: flex; flex-direction: column;">
                    <div style="width: 100%; aspect-ratio: 1.6; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                        <img src="uploads/carnet_mockup.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Empresas">
                    </div>
                    <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-size: 1.15rem; font-family: var(--font-heading); margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);">Empresas</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex-grow: 1;">Carnets, cintas y accesorios para colaboradores, áreas internas y visitantes.</p>
                        <a href="cotizacion.php" class="btn btn-secondary" style="font-size: 0.75rem; padding: 8px 12px; text-align: center; text-transform: none; margin-top: auto;">Cotizar para mi empresa</a>
                    </div>
                </div>
                
                <!-- Instituciones -->
                <div class="company-card-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; display: flex; flex-direction: column;">
                    <div style="width: 100%; aspect-ratio: 1.6; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                        <img src="uploads/carousel_5.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Instituciones">
                    </div>
                    <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-size: 1.15rem; font-family: var(--font-heading); margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);">Instituciones</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex-grow: 1;">Identificación para personal administrativo, equipos de apoyo, estudiantes o miembros.</p>
                        <a href="cotizacion.php" class="btn btn-secondary" style="font-size: 0.75rem; padding: 8px 12px; text-align: center; text-transform: none; margin-top: auto;">Solicitar opciones</a>
                    </div>
                </div>
                
                <!-- Eventos -->
                <div class="company-card-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; display: flex; flex-direction: column;">
                    <div style="width: 100%; aspect-ratio: 1.6; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                        <img src="uploads/carousel_2.jpg?v=2.2" style="width: 100%; height: 100%; object-fit: cover;" alt="Eventos">
                    </div>
                    <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-size: 1.15rem; font-family: var(--font-heading); margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);">Eventos</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex-grow: 1;">Credenciales, cintas y porta credenciales para staff, invitados y asistentes.</p>
                        <a href="cotizacion.php" class="btn btn-secondary" style="font-size: 0.75rem; padding: 8px 12px; text-align: center; text-transform: none; margin-top: auto;">Cotizar para evento</a>
                    </div>
                </div>
                
                <!-- Equipos de trabajo -->
                <div class="company-card-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; display: flex; flex-direction: column;">
                    <div style="width: 100%; aspect-ratio: 1.6; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                        <img src="uploads/cintas_mockup.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Equipos de trabajo">
                    </div>
                    <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-size: 1.15rem; font-family: var(--font-heading); margin-bottom: 0.5rem; font-weight: 500; color: var(--dark);">Equipos de trabajo</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex-grow: 1;">Soluciones prácticas para identificar cargos, áreas y personal operativo.</p>
                        <a href="productos.php" class="btn btn-secondary" style="font-size: 0.75rem; padding: 8px 12px; text-align: center; text-transform: none; margin-top: auto;">Ver productos</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. Accesorios para el uso diario con Fotos Ilustrativas -->
        <section class="section-padding" style="background: var(--surface-light); border-top: 1px solid var(--border);">
            <div class="container reveal-on-scroll">
                <div class="section-header center" style="margin-bottom: 3.5rem;">
                    <span class="section-subtitle">Accesorios Diarios</span>
                    <h2>Accesorios para el uso diario</h2>
                    <p>Complementos prácticos para proteger, portar y presentar mejor cada credencial.</p>
                </div>

                <div class="grid-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <!-- Porta carnets -->
                    <div class="accessory-card-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; display: flex; flex-direction: column;">
                        <div style="width: 100%; aspect-ratio: 1.4; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                            <img src="uploads/llavero.png" style="width: 100%; height: 100%; object-fit: cover;" alt="Porta carnets">
                        </div>
                        <div style="padding: 1.25rem; text-align: center; display: flex; flex-direction: column; flex-grow: 1;">
                            <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 5px; color: var(--dark);">Porta carnets</h4>
                            <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 1rem; flex-grow: 1;">Protección práctica para carnets y tarjetas rígidas.</p>
                            <a href="productos.php?cat=porta-credenciales" style="font-size: 0.75rem; color: var(--primary); font-weight: 600; text-decoration: none; text-transform: none; margin-top: auto;">Ver opciones</a>
                        </div>
                    </div>
                    
                    <!-- Yoyos retráctiles -->
                    <div class="accessory-card-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; display: flex; flex-direction: column;">
                        <div style="width: 100%; aspect-ratio: 1.4; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                            <img src="uploads/yoyos.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Yoyos retráctiles">
                        </div>
                        <div style="padding: 1.25rem; text-align: center; display: flex; flex-direction: column; flex-grow: 1;">
                            <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 5px; color: var(--dark);">Yoyos retráctiles</h4>
                            <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 1rem; flex-grow: 1;">Accesorio cómodo con cordón extensible para accesos rápidos.</p>
                            <a href="cotizacion.php?producto=accesorios-identificacion" style="font-size: 0.75rem; color: var(--primary); font-weight: 600; text-decoration: none; text-transform: none; margin-top: auto;">Cotizar yoyos</a>
                        </div>
                    </div>
                    
                    <!-- Fundas transparentes -->
                    <div class="accessory-card-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; display: flex; flex-direction: column;">
                        <div style="width: 100%; aspect-ratio: 1.4; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                            <img src="uploads/fundas.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Fundas transparentes">
                        </div>
                        <div style="padding: 1.25rem; text-align: center; display: flex; flex-direction: column; flex-grow: 1;">
                            <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 5px; color: var(--dark);">Fundas transparentes</h4>
                            <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 1rem; flex-grow: 1;">Fundas de PVC blando para acreditaciones de eventos.</p>
                            <a href="productos.php?cat=porta-credenciales" style="font-size: 0.75rem; color: var(--primary); font-weight: 600; text-decoration: none; text-transform: none; margin-top: auto;">Ver opciones</a>
                        </div>
                    </div>
                    
                    <!-- Clips y sujetadores -->
                    <div class="accessory-card-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; display: flex; flex-direction: column;">
                        <div style="width: 100%; aspect-ratio: 1.4; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                            <img src="uploads/llavero.png" style="width: 100%; height: 100%; object-fit: cover;" alt="Clips y sujetadores">
                        </div>
                        <div style="padding: 1.25rem; text-align: center; display: flex; flex-direction: column; flex-grow: 1;">
                            <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 5px; color: var(--dark);">Clips y sujetadores</h4>
                            <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 1rem; flex-grow: 1;">Sujeción metálica o plástica segura para fijar a la prenda.</p>
                            <a href="cotizacion.php?producto=accesorios-identificacion" style="font-size: 0.75rem; color: var(--primary); font-weight: 600; text-decoration: none; text-transform: none; margin-top: auto;">Cotizar clips</a>
                        </div>
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
                                <h4 style="font-family: var(--font-heading); font-size: 1.1rem; color: var(--dark); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($prod['name']); ?></h4>
                                <p style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex-grow: 1;"><?php echo htmlspecialchars($prod['description_short']); ?></p>
                                <a href="cotizacion.php?producto=<?php echo htmlspecialchars($prod['slug']); ?>" class="btn btn-secondary" style="width: 100%; text-align: center; font-size: 0.78rem; padding: 8px 0; text-transform: none;">
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
                    
                    <img id="slider-img-after" src="images/termo_after.png" alt="Termo Grabado" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none;">
                    <div id="slider-before-wrap" style="position: absolute; inset: 0; width: 100%; height: 100%; overflow: hidden; pointer-events: none; clip-path: inset(0 50% 0 0); border-right: 2px solid white;">
                        <img id="slider-img-before" src="images/termo_before.png" alt="Termo Liso" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none;">
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

        <!-- 10. Preguntas frecuentes (Refinadas y Corregidas) -->
        <section id="preguntas-frecuentes" class="section-padding section-bg-light reveal-on-scroll">
            <div class="container" style="max-width: 800px;">
                <div class="section-header center">
                    <span class="section-subtitle" style="color: var(--primary); border-color: var(--primary); font-weight: 600; padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">DUDAS COMUNES</span>
                    <h2 style="font-family: var(--font-heading); font-size: 2.2rem; font-weight: 500; margin-top: 10px;">Preguntas frecuentes</h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem;">Lo básico que necesitas saber antes de cotizar tus identificaciones corporativas.</p>
                </div>
                
                <div class="faq-accordion" style="margin-top: 2.5rem; display: flex; flex-direction: column; gap: 14px;">
                    <!-- Pregunta 1 -->
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white; transition: all 0.3s ease;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.98rem; color: var(--dark);">¿Hacen carnets personalizados?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">Sí. Diseñamos e imprimimos carnets en PVC laminado de alta resistencia con acabados profesionales. Son ideales para empresas, instituciones educativas, eventos y control de accesos.</p>
                        </div>
                    </div>
                    
                    <!-- Pregunta 2 -->
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white; transition: all 0.3s ease;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.98rem; color: var(--dark);">¿También hacen cintas porta credenciales?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">Sí. Producimos cintas (lanyards) personalizadas full color mediante sublimación de alta definición, estampados a un color o cintas lisas en colores corporativos básicos con mosquetón metálico de alta calidad.</p>
                        </div>
                    </div>
                    
                    <!-- Pregunta 3 -->
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white; transition: all 0.3s ease;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.98rem; color: var(--dark);">¿Puedo enviar mi logo o diseño listo?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">Sí. Puedes hacernos llegar tu logotipo en formatos vectoriales como PDF, AI o SVG, o en imágenes de alta resolución. Nuestro equipo se encargará de realizar el montaje y la adaptación para que quede perfecto.</p>
                        </div>
                    </div>

                    <!-- Pregunta 4 -->
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white; transition: all 0.3s ease;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.98rem; color: var(--dark);">¿Puedo ver una vista previa antes de producir?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">Totalmente. Antes de iniciar cualquier fase de producción en el taller, te compartiremos una muestra digital o maqueta (vista previa) para tu revisión y aprobación formal.</p>
                        </div>
                    </div>

                    <!-- Pregunta 5 -->
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white; transition: all 0.3s ease;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.98rem; color: var(--dark);">¿Hacen envíos a todo el Ecuador?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">Sí. Despachamos pedidos a todas las provincias y ciudades del Ecuador (Guayaquil, Cuenca, Manta, Loja, etc.) a través de Servientrega o cooperativas de transporte interprovincial de tu preferencia.</p>
                        </div>
                    </div>

                    <!-- Pregunta 6 -->
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white; transition: all 0.3s ease;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.98rem; color: var(--dark);">¿Cuál es el tiempo promedio de entrega?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">El tiempo estándar estimado es de 3 a 5 días laborables, contados a partir de la confirmación del pago y la aprobación del diseño final de las muestras.</p>
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
                    before: "images/termo_before.png",
                    after: "images/termo_after.png"
                },
                agenda: {
                    title: "Agenda de Cuero",
                    text: "De agenda lisa a pieza ejecutiva con identidad de marca.",
                    before: "images/agenda_before.png",
                    after: "images/agenda_after.png"
                },
                caja: {
                    title: "Caja de Madera",
                    text: "De empaque básico a presentación personalizada.",
                    before: "images/caja_before.png",
                    after: "images/caja_after.png"
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
