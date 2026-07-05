<?php
require_once 'db.php';

// 1. Obtener slides del carrusel activos
try {
    $stmt = $pdo->query("SELECT * FROM carrusel WHERE is_active = 1 ORDER BY order_val ASC");
    $slides = $stmt->fetchAll();
} catch (PDOException $e) {
    $slides = [];
}

// 2. Obtener productos destacados activos (máximo 6)
try {
    $stmt = $pdo->query("SELECT * FROM productos WHERE is_active = 1 AND is_featured = 1 ORDER BY order_val ASC LIMIT 6");
    $featuredProducts = $stmt->fetchAll();
} catch (PDOException $e) {
    $featuredProducts = [];
}

// 3. Obtener comparaciones antes/después activas (máximo 3)
try {
    $stmt = $pdo->query("SELECT * FROM antes_despues WHERE is_active = 1 ORDER BY order_val ASC LIMIT 3");
    $beforeAfters = $stmt->fetchAll();
} catch (PDOException $e) {
    $beforeAfters = [];
}

// 4. Obtener materiales activos (máximo 4)
try {
    $stmt = $pdo->query("SELECT * FROM materiales WHERE is_active = 1 LIMIT 4");
    $materials = $stmt->fetchAll();
} catch (PDOException $e) {
    $materials = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CardNet.ec | Grabado Láser y Personalización Corporativa Premium</title>
    <meta name="description" content="No hacemos artículos con logos. Creamos piezas personalizadas que hacen que tu marca se recuerde. Especialistas en grabado láser en Quito, Ecuador.">
    <link rel="canonical" href="https://cardnet.ec/index.php">
    
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
                <!-- Logotipo Real en Imagen (logo.png) -->
                <a href="index.php" class="logo" aria-label="CardNet.ec Inicio">
                    <img src="images/logo.png" alt="CardNet.ec Logo" class="logo-img">
                </a>
                
                <div class="header-search">
                    <svg class="search-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input class="search-input" type="text" placeholder="Buscar grabados en metal, corcho, madera...">
                </div>

                <div class="header-contact-status">
                    <div class="contact-status-item">
                        <span class="status-icon-wrap">
                            <svg style="width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2.5;" viewBox="0 0 24 24">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </span>
                        <div class="status-text">
                            <h4>Asesoría personalizada</h4>
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
                    <a href="index.php" class="nav-link active">Inicio</a>
                    <a href="index.php#destacados" class="nav-link">Destacados</a>
                    <a href="index.php#laser" class="nav-link">Grabado láser</a>
                    <a href="productos.php" class="nav-link">Productos</a>
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
        <a href="index.php" class="mobile-link active">Inicio</a>
        <a href="index.php#destacados" class="mobile-link">Destacados</a>
        <a href="index.php#laser" class="mobile-link">Grabado láser</a>
        <a href="productos.php" class="mobile-link">Productos</a>
        <a href="empresas.php" class="mobile-link">Kits corporativos</a>
        <a href="cotizacion.php" class="btn btn-primary" style="margin-top: 1rem; width: 100%;">Cotizar</a>
    </nav>

    <!-- MAIN CONTENT -->
    <main>
        
        <!-- Hero con Carrusel de Banners Hero Dinámico -->
        <section class="hero-block reveal-on-scroll">
            <div class="container hero-carousel-wrapper">
                <div class="hero-carousel">
                    <div class="carousel-track">
                        
                        <?php if (!empty($slides)): ?>
                            <?php foreach ($slides as $slide): ?>
                                <div class="carousel-slide">
                                    <div class="carousel-image-container">
                                        <?php if ($slide['image']): ?>
                                            <img src="uploads/<?php echo htmlspecialchars($slide['image']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="image-placeholder theme-gray" style="height: 100%; border-radius: 0;">
                                                <div class="image-placeholder-inner" style="background-color: var(--surface-light);">
                                                    <svg class="image-placeholder-icon" viewBox="0 0 24 24" width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.2">
                                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                                    </svg>
                                                    <span class="image-placeholder-text"><?php echo htmlspecialchars($slide['title']); ?></span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="carousel-slide-content">
                                        <h2 class="carousel-slide-title"><?php echo htmlspecialchars($slide['title']); ?></h2>
                                        <?php if ($slide['subtitle']): ?>
                                            <p class="carousel-slide-subtitle"><?php echo htmlspecialchars($slide['subtitle']); ?></p>
                                        <?php endif; ?>
                                        <?php if ($slide['cta_text']): ?>
                                            <a href="<?php echo htmlspecialchars($slide['cta_url'] ?: '#'); ?>" class="btn btn-primary"><?php echo htmlspecialchars($slide['cta_text']); ?></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Slide de Resguardo si la base está vacía -->
                            <div class="carousel-slide">
                                <div class="carousel-image-container">
                                    <div class="image-placeholder theme-gray" style="height: 100%; border-radius: 0;">
                                        <div class="image-placeholder-inner" style="background-color: var(--surface-light);">
                                            <span class="image-placeholder-text">Personalización en Quito</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="carousel-slide-content">
                                    <h2 class="carousel-slide-title">Productos personalizados que hacen visible el valor de tu marca</h2>
                                    <p class="carousel-slide-subtitle">Grabado láser, termos, agendas, placas y kits corporativos con acabados limpios y duraderos.</p>
                                    <a href="#destacados" class="btn btn-primary">Ver productos destacados</a>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- Controles -->
                    <button class="carousel-control prev" aria-label="Anterior">←</button>
                    <button class="carousel-control next" aria-label="Siguiente">→</button>
                    <div class="carousel-indicators"></div>
                </div>
            </div>
        </section>

        <!-- Barra de Garantías -->
        <section class="satisfaction-bar">
            <div class="container satisfaction-grid">
                <div class="satisfaction-item">
                    <svg class="satisfaction-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>Acabado limpio sin tintas</span>
                </div>
                <div class="satisfaction-item">
                    <svg class="satisfaction-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    <span>Prueba visual previa al marcaje</span>
                </div>
                <div class="satisfaction-item">
                    <svg class="satisfaction-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    <span>Soportes seleccionados</span>
                </div>
            </div>
        </section>

        <!-- Productos Destacados -->
        <section id="destacados" class="section-padding container reveal-on-scroll">
            <div class="section-header center">
                <span class="section-subtitle">Showroom Digital</span>
                <h2>Productos destacados</h2>
                <p>Una selección de artículos adaptados para grabados y marcajes de alta calidad.</p>
            </div>
            
            <div class="grid-3" style="margin-top: 2rem;">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="product-card">
                        <div class="product-card-image-wrap">
                            <div class="image-placeholder theme-gray">
                                <?php if ($product['image_main']): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($product['image_main']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div class="image-placeholder-inner">
                                        <svg class="image-placeholder-icon" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                        </svg>
                                        <span class="image-placeholder-text"><?php echo htmlspecialchars($product['name']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="product-card-body">
                            <span class="product-card-price"><?php echo htmlspecialchars($product['category']); ?></span>
                            <h3 class="product-card-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-card-desc"><?php echo htmlspecialchars($product['description_short']); ?></p>
                            <a href="producto.php?slug=<?php echo urlencode($product['slug']); ?>" class="btn btn-secondary" style="margin-top: auto; padding: 0.5rem 1rem; font-size: 0.8rem; text-align: center;">
                                Ver producto y simular
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Comparación Antes y Después -->
        <?php if (!empty($beforeAfters)): ?>
            <section id="antes-despues" class="section-padding container reveal-on-scroll">
                <div class="section-header center">
                    <span class="section-subtitle">Evidencia de Taller</span>
                    <h2>Antes y después del grabado</h2>
                    <p>Compara el producto base limpio frente al resultado final con identidad de marca grabada.</p>
                </div>
                
                <div class="comparison-grid" style="margin-top: 2rem;">
                    <?php foreach ($beforeAfters as $item): ?>
                        <div class="comparison-card">
                            <div class="comparison-views">
                                <div class="comparison-view">
                                    <div class="comparison-label">Antes</div>
                                    <span style="font-size: 0.8rem; color: var(--text-muted);">Liso sin marcar</span>
                                </div>
                                <div class="comparison-view after">
                                    <div class="comparison-label after">Después</div>
                                    <span style="font-weight: 600; color: var(--primary);"><?php echo htmlspecialchars($item['technique']); ?></span>
                                </div>
                            </div>
                            <div class="comparison-desc"><?php echo htmlspecialchars($item['title']); ?> (<?php echo htmlspecialchars($item['material']); ?>)</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Materiales Reales -->
        <?php if (!empty($materials)): ?>
            <section id="materiales" class="section-padding section-bg-light reveal-on-scroll">
                <div class="container">
                    <div class="section-header center">
                        <span class="section-subtitle">Soportes Reales</span>
                        <h2>Materiales que trabajamos</h2>
                        <p>Seleccionamos materiales óptimos para lograr marcajes permanentes y definidos.</p>
                    </div>
                    
                    <div class="materials-grid" style="margin-top: 2rem;">
                        <?php foreach ($materials as $material): ?>
                            <div class="material-card">
                                <h3 class="material-title"><?php echo htmlspecialchars($material['name']); ?></h3>
                                <p class="material-desc"><?php echo htmlspecialchars($material['description']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Grabado Láser Especialidad -->
        <section id="laser" class="section-padding container reveal-on-scroll">
            <div class="laser-section">
                <div class="laser-layout">
                    <div class="laser-content">
                        <span class="section-subtitle" style="color: var(--primary); border-color: var(--primary);">Técnica Especializada</span>
                        <h2 style="color: var(--light); margin-bottom: 1.25rem;">Nuestra especialidad: grabado láser</h2>
                        <p style="margin-bottom: 2rem; font-size: 0.98rem; color: rgba(252,253,251,0.85);">El láser trabaja directamente sobre la superficie del material para lograr una marca limpia, precisa y duradera. Grabamos sobre metal, madera, cuero, acrílico y otros materiales para lograr piezas limpias, duraderas y elegantes.</p>
                        
                        <div class="laser-capabilities">
                            <div class="laser-cap-item">
                                <span>No se despega</span>
                                <h4>Marcado permanente</h4>
                            </div>
                            <div class="laser-cap-item">
                                <span>Detalles finos</span>
                                <h4>Sin tintas superficiales</h4>
                            </div>
                        </div>
                    </div>

                    <div class="laser-visual">
                        <div class="image-placeholder theme-gray" style="aspect-ratio: 1.15;">
                            <div class="image-placeholder-inner" style="background-color: var(--dark-alt); border-color: rgba(255,255,255,0.08);">
                                <svg class="image-placeholder-icon" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#63AE2C" stroke-width="1.5">
                                    <line x1="5" y1="12" x2="19" y2="12"/><line x1="12" y1="5" x2="12" y2="19"/>
                                </svg>
                                <span class="image-placeholder-text" style="color: var(--light);">Calibración láser en taller</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Proceso de Taller -->
        <section id="proceso" class="section-padding container reveal-on-scroll">
            <div class="section-header center">
                <span class="section-subtitle">Atención en Quito</span>
                <h2>Cómo hacemos tu pedido</h2>
                <p>Un flujo de pedido simple, transparente y de trato directo.</p>
            </div>
            
            <div class="process-grid">
                <div class="process-step">
                    <div class="process-number">01</div>
                    <h4>Nos envías tu logo o idea</h4>
                    <p>Elegimos el producto ideal para tu marca o evento.</p>
                </div>
                <div class="process-step">
                    <div class="process-number">02</div>
                    <h4>Elegimos el producto</h4>
                    <p>Elegimos el producto y acabado ideal para el soporte.</p>
                </div>
                <div class="process-step">
                    <div class="process-number">03</div>
                    <h4>Preparamos una vista previa</h4>
                    <p>Revisamos la colocación y proporciones en pantalla.</p>
                </div>
                <div class="process-step">
                    <div class="process-number">04</div>
                    <h4>Grabamos y entregamos</h4>
                    <p>Personalizamos y despachamos tus piezas terminadas.</p>
                </div>
            </div>
        </section>

        <!-- Garantía Final -->
        <section class="section-padding container reveal-on-scroll" style="text-align: center; max-width: 800px;">
            <span class="section-subtitle">Hecho en Ecuador</span>
            <h2 style="margin-bottom: 1.25rem;">Cuidamos que cada pieza se vea tan bien como la marca que representa.</h2>
            <p style="margin-bottom: 2rem; font-size: 1rem; color: var(--text-muted);">El acabado final también habla de tu empresa. Por eso cuidamos la selección del producto, la ubicación del logo y la presentación de cada pieza.</p>
            <div class="hero-actions" style="justify-content: center;">
                <a href="https://wa.me/593900000000" class="btn btn-primary" target="_blank" rel="noopener noreferrer">Cotizar por WhatsApp</a>
                <a href="cotizacion.php" class="btn btn-secondary">Enviar mi logo</a>
            </div>
        </section>

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
                        <a href="productos.php#termos" class="footer-link">Termos</a>
                        <a href="productos.php#oficina" class="footer-link">Agendas</a>
                        <a href="productos.php#kits" class="footer-link">Kits</a>
                        <a href="productos.php#placas" class="footer-link">Placas</a>
                        <a href="productos.php#llaveros" class="footer-link">Llaveros</a>
                    </nav>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading">Contacto</h3>
                    <div class="footer-contact-info">
                        <div class="footer-contact-item">
                            <svg class="footer-contact-icon" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72(12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span>WhatsApp: +593 90 000 0000</span>
                        </div>
                        <div class="footer-contact-item">
                            <svg class="footer-contact-icon" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="m22 6-10 7L2 6"/></svg>
                            <span>Correo: info@cardnet.ec</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container footer-bottom-flex">
                <p>&copy; 2026 CardNet.ec — Detalles personalizados para marcas que cuidan su presentación.</p>
                <div class="footer-bottom-links">
                    <a href="faq.php" class="footer-bottom-link">Preguntas Frecuentes</a>
                    <a href="contacto.php" class="footer-bottom-link">Soporte de Taller</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Flotante -->
    <a href="https://wa.me/593900000000" class="whatsapp-float" target="_blank" rel="noopener noreferrer">
        <svg class="whatsapp-icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
            <path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 0 0 1.333 4.982L2 22l5.233-1.371a9.994 9.994 0 0 0 4.779 1.22h.005c5.505 0 9.99-4.478 9.99-9.985A9.988 9.988 0 0 0 12.012 2zm4.7 13.916c-.223.633-1.29 1.205-1.782 1.282-.477.075-.947.168-3.067-.665-2.707-1.06-4.442-3.817-4.577-3.996-.134-.178-1.096-1.455-1.096-2.781 0-1.325.692-1.973.938-2.228.246-.255.535-.319.714-.319.18 0 .358.001.514.009.16.008.375-.062.586.448.223.54.76 1.851.827 1.984.067.134.112.29.022.468-.09.18-.134.29-.268.447-.134.156-.282.35-.403.47-.134.134-.273.28-.117.548.156.268.693 1.139 1.492 1.85 1.026.914 1.89 1.196 2.158 1.33.268.134.424.112.58-.067.157-.18.67-.781.848-1.049.178-.268.358-.223.58-.134.224.089 1.42.67 1.666.792.246.123.411.18.47.282.06.101.06.586-.163 1.218z"/>
        </svg>
    </a>

    <!-- Scripts Modulares -->
    <script src="js/main.js"></script>
    <script src="js/slider.js"></script>
    <script src="js/animations.js"></script>
</body>
</html>
