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
    <title>CardNet.ec | Grabado láser y personalización corporativa en Quito</title>
    <meta name="description" content="Grabado láser, identificación y productos personalizados para empresas, instituciones y eventos. Termos, agendas, placas, kits corporativos y más en Quito.">
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

    <!-- SEO: Marcado Estructurado JSON-LD -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "CardNet.ec",
      "image": "https://cardnet.ec/images/logo.png",
      "telephone": "+593900000000",
      "email": "info@cardnet.ec",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Av. Amazonas",
        "addressLocality": "Quito",
        "addressRegion": "Pichincha",
        "addressCountry": "EC"
      },
      "url": "https://cardnet.ec",
      "priceRange": "$$"
    }
    </script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "¿Puedo enviar mi logo por WhatsApp?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Sí. Puedes enviar tu logo, una referencia o una idea. Revisamos el archivo y te orientamos antes de preparar la propuesta."
          }
        },
        {
          "@type": "Question",
          "name": "¿Me muestran cómo quedará antes de producir?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Sí. Antes de grabar o personalizar, se puede preparar una vista previa para revisar ubicación, tamaño y proporción."
          }
        },
        {
          "@type": "Question",
          "name": "¿Qué formato debe tener mi logo?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Lo ideal es enviar el logo en buena calidad. Si tienes archivo vectorial, mucho mejor. También se puede revisar una imagen para confirmar si sirve."
          }
        },
        {
          "@type": "Question",
          "name": "¿Qué materiales se pueden grabar?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Se puede trabajar sobre materiales como acero inoxidable, madera, acrílico, cuero PU y otros soportes compatibles."
          }
        },
        {
          "@type": "Question",
          "name": "¿Puedo cotizar productos que no están en el catálogo?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Sí. El catálogo es una referencia. También puedes enviar el producto o idea que necesitas y se revisa la posibilidad de personalización."
          }
        },
        {
          "@type": "Question",
          "name": "¿El grabado se borra?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "El grabado láser marca directamente el material. Por eso es más resistente que tintas superficiales o adhesivos."
          }
        },
        {
          "@type": "Question",
          "name": "¿Trabajan para empresas y eventos?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Sí. La página debe comunicar que se pueden preparar productos para empresas, instituciones, equipos de trabajo y eventos."
          }
        }
      ]
    }
    </script>
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
                    <a href="index.php#antes-despues" class="nav-link">Trabajos</a>
                    <a href="index.php#laser" class="nav-link">Grabado láser</a>
                    <a href="productos.php" class="nav-link">Productos</a>
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
        <a href="index.php" class="mobile-link active">Inicio</a>
        <a href="index.php#antes-despues" class="mobile-link">Trabajos</a>
        <a href="index.php#laser" class="mobile-link">Grabado láser</a>
        <a href="productos.php" class="mobile-link">Productos</a>
        <a href="empresas.php" class="mobile-link">Kits empresariales</a>
        <a href="index.php#proceso" class="mobile-link">Cómo pedir</a>
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
                            <?php foreach ($slides as $idx => $slide): ?>
                                <div class="carousel-slide">
                                    <div class="carousel-slide-content-wrap">
                                        <?php if ($idx === 0): ?>
                                            <h1 class="carousel-slide-title" style="margin-bottom: 0.75rem; font-family: var(--font-heading); font-size: clamp(1.4rem, 2.5vw, 2.1rem);"><?php echo htmlspecialchars($slide['title']); ?></h1>
                                        <?php else: ?>
                                            <h2 class="carousel-slide-title" style="margin-bottom: 0.75rem; font-family: var(--font-heading);"><?php echo htmlspecialchars($slide['title']); ?></h2>
                                        <?php endif; ?>
                                        <?php if ($slide['subtitle']): ?>
                                            <p class="carousel-slide-subtitle"><?php echo htmlspecialchars($slide['subtitle']); ?></p>
                                        <?php endif; ?>
                                        <div class="hero-actions" style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 1.5rem;">
                                            <?php if ($slide['cta_text']): ?>
                                                <a href="<?php echo htmlspecialchars($slide['cta_url'] ?: '#'); ?>" class="btn btn-primary"><?php echo htmlspecialchars($slide['cta_text']); ?></a>
                                            <?php endif; ?>
                                            <a href="https://wa.me/593900000000" class="btn btn-secondary" target="_blank" rel="noopener noreferrer">Cotizar por WhatsApp</a>
                                        </div>
                                    </div>
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
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Slide de Resguardo si la base está vacía -->
                            <div class="carousel-slide">
                                <div class="carousel-slide-content-wrap">
                                    <span class="section-subtitle" style="margin-bottom: 0.75rem; border-color: var(--primary); color: var(--primary);">Taller de Grabado en Quito</span>
                                    <h1 class="carousel-slide-title" style="margin-bottom: 0.75rem; font-family: var(--font-heading);">Grabado láser y personalización corporativa en Quito</h1>
                                    <p class="carousel-slide-subtitle">Personalizamos termos, agendas, placas, llaveros, cajas, kits empresariales y productos de identificación con acabados limpios, duraderos y profesionales.</p>
                                    <div class="hero-actions" style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 1.5rem;">
                                        <a href="https://wa.me/593900000000" class="btn btn-primary" target="_blank" rel="noopener noreferrer">Cotizar por WhatsApp</a>
                                        <a href="productos.php" class="btn btn-secondary">Ver productos</a>
                                    </div>
                                </div>
                                <div class="carousel-image-container">
                                    <!-- Placeholder visual premium y elegante -->
                                    <div class="hero-premium-visual" style="width: 100%; height: 100%; background: radial-gradient(circle at 70% 30%, var(--surface-light) 0%, var(--border) 100%); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                                        <div class="laser-beam-animation" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(99, 174, 44, 0.03) 0%, rgba(99, 174, 44, 0) 70%); pointer-events: none;"></div>
                                        <div style="text-align: center; padding: 2rem; z-index: 2;">
                                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="1.2" style="margin: 0 auto 1rem; opacity: 0.85;">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                                <path d="M12 8v8M8 12h8"/>
                                            </svg>
                                            <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 0.5rem;">Showcase de Productos</span>
                                            <p style="font-size: 0.85rem; color: var(--text-muted); max-width: 280px; margin: 0 auto; line-height: 1.4;">Previsualiza tus diseños en nuestro simulador en línea</p>
                                        </div>
                                    </div>
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
                    <a href="producto.php?slug=<?php echo urlencode($product['slug']); ?>" class="product-card" style="text-decoration: none; color: inherit; display: flex; flex-direction: column;">
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
                            <h3 class="product-card-title" style="margin-bottom: 0.25rem; font-family: var(--font-heading);"><?php echo htmlspecialchars($product['name']); ?></h3>
                            
                            <!-- Badges de especificaciones técnicas premium -->
                            <div class="product-specs-badges" style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 0.85rem; margin-top: 0.25rem;">
                                <?php if (stripos($product['category'], 'tarjeta') !== false || stripos($product['name'], 'tarjeta') !== false): ?>
                                    <span style="font-size: 0.65rem; background: rgba(0,0,0,0.03); color: var(--text-muted); padding: 3px 8px; border-radius: 20px; font-weight: 500; border: 1px solid rgba(0,0,0,0.03); letter-spacing: 0.02em;">Mate & Grabado</span>
                                    <span style="font-size: 0.65rem; background: rgba(99, 174, 44, 0.08); color: var(--primary-hover); padding: 3px 8px; border-radius: 20px; font-weight: 600; border: 1px solid rgba(99, 174, 44, 0.1); letter-spacing: 0.02em;">Metal 316L</span>
                                <?php elseif (stripos($product['category'], 'termo') !== false || stripos($product['name'], 'termo') !== false): ?>
                                    <span style="font-size: 0.65rem; background: rgba(0,0,0,0.03); color: var(--text-muted); padding: 3px 8px; border-radius: 20px; font-weight: 500; border: 1px solid rgba(0,0,0,0.03); letter-spacing: 0.02em;">Doble Pared</span>
                                    <span style="font-size: 0.65rem; background: rgba(99, 174, 44, 0.08); color: var(--primary-hover); padding: 3px 8px; border-radius: 20px; font-weight: 600; border: 1px solid rgba(99, 174, 44, 0.1); letter-spacing: 0.02em;">Láser CO2</span>
                                <?php else: ?>
                                    <span style="font-size: 0.65rem; background: rgba(0,0,0,0.03); color: var(--text-muted); padding: 3px 8px; border-radius: 20px; font-weight: 500; border: 1px solid rgba(0,0,0,0.03); letter-spacing: 0.02em;">Edición B2B</span>
                                    <span style="font-size: 0.65rem; background: rgba(99, 174, 44, 0.08); color: var(--primary-hover); padding: 3px 8px; border-radius: 20px; font-weight: 600; border: 1px solid rgba(99, 174, 44, 0.1); letter-spacing: 0.02em;">Precisión Alta</span>
                                <?php endif; ?>
                            </div>

                            <p class="product-card-desc"><?php echo htmlspecialchars($product['description_short']); ?></p>
                            <span class="btn btn-secondary" style="margin-top: auto; padding: 0.5rem 1rem; font-size: 0.8rem; text-align: center; display: block; width: 100%;">
                                Ver producto y simular
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Trabajos que podemos personalizar -->
        <?php if (!empty($beforeAfters)): ?>
            <section id="antes-despues" class="section-padding container reveal-on-scroll">
                <div class="section-header center">
                    <span class="section-subtitle">Capacidad de Producción</span>
                    <h2>Trabajos que podemos personalizar</h2>
                    <p>Visualiza el contraste entre el soporte base y el resultado final grabado con tu logotipo.</p>
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
                <!-- Por qué empresas nos eligen -->
        <section class="section-padding section-bg-light reveal-on-scroll">
            <div class="container">
                <div class="section-header center">
                    <span class="section-subtitle">Garantía de Calidad</span>
                    <h2>Por qué empresas nos eligen</h2>
                    <p>Un servicio pensado en la tranquilidad de directores de compras, marcas e instituciones.</p>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-top: 2.5rem;">
                    <div style="background: var(--light); padding: 2rem; border-radius: 8px; border: 1px solid var(--border);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="margin-bottom: 1rem;">
                            <path d="M2 12h20M12 2v20"/>
                        </svg>
                        <h4 style="margin-bottom: 0.5rem; font-family: var(--font-heading); font-size: 1.1rem;">Vista previa antes de producir</h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Antes de grabar revisamos contigo la ubicación del logo, el tamaño y el acabado para evitar resultados improvisados.</p>
                    </div>
                    
                    <div style="background: var(--light); padding: 2rem; border-radius: 8px; border: 1px solid var(--border);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="margin-bottom: 1rem;">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <h4 style="margin-bottom: 0.5rem; font-family: var(--font-heading); font-size: 1.1rem;">Grabado limpio y permanente</h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">El marcado láser desgasta o altera térmicamente el soporte base. No se despega ni se borra como las tintas o adhesivos convencionales.</p>
                    </div>
                    
                    <div style="background: var(--light); padding: 2rem; border-radius: 8px; border: 1px solid var(--border);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" style="margin-bottom: 1rem;">
                            <rect x="1" y="3" width="15" height="13" rx="2" ry="2"/>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                            <circle cx="5.5" cy="18.5" r="2.5"/>
                            <circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>
                        <h4 style="margin-bottom: 0.5rem; font-family: var(--font-heading); font-size: 1.1rem;">Envíos y kits corporativos</h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Atendemos requerimientos de empaques personalizados y coordinamos despachos seguros en Quito y envíos a todo el Ecuador.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Proceso de Taller -->
        <section id="proceso" class="section-padding container reveal-on-scroll">
            <div class="section-header center">
                <span class="section-subtitle">Paso a Paso</span>
                <h2>Cómo hacemos tu pedido</h2>
                <p>Un flujo de pedido simple, transparente y de trato directo.</p>
            </div>
            
            <div class="process-grid">
                <div class="process-step">
                    <div class="process-number" style="color: var(--primary); font-weight: 700; opacity: 0.95;">01</div>
                    <h4>Nos envías tu logo o referencia</h4>
                    <p>Revisamos tu archivo, idea o ejemplo y te orientamos sobre el producto más adecuado.</p>
                </div>
                <div class="process-step">
                    <div class="process-number" style="color: var(--primary); font-weight: 700; opacity: 0.95;">02</div>
                    <h4>Elegimos producto y acabado</h4>
                    <p>Te ayudamos a seleccionar material, tamaño, color y tipo de personalización.</p>
                </div>
                <div class="process-step">
                    <div class="process-number" style="color: var(--primary); font-weight: 700; opacity: 0.95;">03</div>
                    <h4>Preparamos una vista previa</h4>
                    <p>Antes de producir, revisamos contigo la ubicación del logo, proporciones y detalles.</p>
                </div>
                <div class="process-step">
                    <div class="process-number" style="color: var(--primary); font-weight: 700; opacity: 0.95;">04</div>
                    <h4>Grabamos y coordinamos la entrega</h4>
                    <p>Personalizamos tus piezas y coordinamos retiro en Quito o envío a otras ciudades.</p>
                </div>
            </div>
        </section>

        <!-- Preguntas Frecuentes -->
        <section id="preguntas-frecuentes" class="section-padding section-bg-light reveal-on-scroll">
            <div class="container" style="max-width: 800px;">
                <div class="section-header center">
                    <span class="section-subtitle">Dudas Comunes</span>
                    <h2>Preguntas frecuentes</h2>
                    <p>Todo lo que necesitas saber sobre el proceso de diseño, personalización y entrega en Quito.</p>
                </div>
                
                <div class="faq-accordion" style="margin-top: 2rem; display: flex; flex-direction: column; gap: 10px;">
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span>¿Puedo enviar mi logo por WhatsApp?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0;">Sí. Puedes enviar tu logo, una referencia o una idea. Revisamos el archivo y te orientamos antes de preparar la propuesta.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span>¿Me muestran cómo quedará antes de producir?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0;">Sí. Antes de grabar o personalizar, se puede preparar una vista previa para revisar ubicación, tamaño y proporción.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span>¿Qué formato debe tener mi logo?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0;">Lo ideal es enviar el logo en buena calidad. Si tienes archivo vectorial, mucho mejor. También se puede revisar una imagen para confirmar si sirve.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span>¿Qué materiales se pueden grabar?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0;">Se puede trabajar sobre materiales como acero inoxidable, madera, acrílico, cuero PU y otros soportes compatibles.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span>¿Puedo cotizar productos que no están en el catálogo?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0;">Sí. El catálogo es una referencia. También puedes enviar el producto o idea que necesitas y se revisa la posibilidad de personalización.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span>¿El grabado se borra?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0;">El grabado láser marca directamente el material. Por eso es más resistente que tintas superficiales o adhesivos.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span>¿Trabajan para empresas y eventos?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0;">Sí, realizamos trabajos al por mayor para marcas corporativas, instituciones académicas, organizaciones y eventos en general.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Garantía Final / Llamado a la Acción -->
        <section class="section-padding container reveal-on-scroll" style="text-align: center; max-width: 800px; margin-top: 1rem; margin-bottom: 2rem;">
            <span class="section-subtitle" style="color: var(--primary); border-color: var(--primary);">Contacto Directo</span>
            <h2 style="margin-bottom: 1.25rem; font-family: var(--font-heading);">¿Ya tienes una idea para personalizar?</h2>
            <p style="margin-bottom: 2rem; font-size: 1rem; color: var(--text-muted); line-height: 1.6;">Envíanos el producto que necesitas, la cantidad aproximada y tu logo. Te ayudamos a elegir el material, el acabado y la mejor forma de presentarlo.</p>
            <div class="hero-actions" style="justify-content: center; display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="https://wa.me/593900000000?text=Hola,%20tengo%20una%20idea%20para%20personalizar%20un%20producto..." class="btn btn-primary" target="_blank" rel="noopener noreferrer">Enviar solicitud por WhatsApp</a>
                <a href="productos.php" class="btn btn-secondary">Ver productos para cotizar</a>
            </div>
        </section>

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
