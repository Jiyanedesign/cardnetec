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

// 4. Obtener materiales activos (máximo 5)
try {
    $stmt = $pdo->query("SELECT * FROM materiales WHERE is_active = 1 LIMIT 5");
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
    <meta name="description" content="Identificación, grabado láser y productos personalizados para empresas, instituciones y eventos. Termos, agendas, placas, credenciales, kits corporativos y más.">
    <link rel="canonical" href="https://cardnet.ec/index.php">
    
    <!-- Open Graph -->
    <meta property="og:title" content="CardNet.ec | Personalización corporativa y grabado láser">
    <meta property="og:description" content="Piezas personalizadas para empresas, instituciones y eventos con acabado profesional.">
    <meta property="og:url" content="https://cardnet.ec">
    <meta property="og:type" content="website">

    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=1.1.3">
    <link rel="stylesheet" href="css/layout.css?v=1.1.3">
    <link rel="stylesheet" href="css/components.css?v=1.1.3">
    <link rel="stylesheet" href="css/pages.css?v=1.1.3">
    <link rel="stylesheet" href="css/animations.css?v=1.1.3">

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
          "name": "¿Puedo pedir una vista previa antes de producir?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Sí. La vista previa permite revisar ubicación, tamaño y proporción del logo antes de personalizar."
          }
        },
        {
          "@type": "Question",
          "name": "¿Qué pasa si mi logo no está en buena calidad?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Podemos revisarlo y decirte si sirve para grabado o si necesita preparación antes de producir."
          }
        },
        {
          "@type": "Question",
          "name": "¿Puedo cotizar varios productos al mismo tiempo?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Sí. Puedes agregar diferentes productos a tu solicitud y enviar todo en un solo mensaje."
          }
        },
        {
          "@type": "Question",
          "name": "¿El grabado se despega o se borra?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "No funciona como un adhesivo. El láser marca directamente el material compatible."
          }
        },
        {
          "@type": "Question",
          "name": "¿Pueden preparar productos para eventos?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Sí. Se pueden organizar opciones para asistentes, invitados, equipos o reconocimientos."
          }
        },
        {
          "@type": "Question",
          "name": "¿Puedo consultar un producto que no aparece en el catálogo?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Sí. El catálogo funciona como referencia. Puedes enviar tu idea y se revisa si es viable personalizarla."
          }
        },
        {
          "@type": "Question",
          "name": "¿Qué información debo enviar para cotizar?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Producto, cantidad aproximada, logo o referencia, ciudad de entrega y cualquier observación importante."
          }
        }
      ]
    }
    </script>
</head>
<body>

    <!-- Barra de Anuncios Superior -->
    <div class="top-announcement-bar">
        Taller de personalización en Quito | Envíos corporativos a todo el Ecuador
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
                    <input class="search-input" type="text" placeholder="Buscar termos, agendas, placas...">
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
                    <a href="index.php" class="nav-link active">Inicio</a>
                    <a href="#soluciones" class="nav-link">Soluciones</a>
                    <a href="#colecciones" class="nav-link">Colecciones</a>
                    <a href="#laser" class="nav-link">Grabado láser</a>
                    <a href="productos.php" class="nav-link">Productos</a>
                    <a href="#proceso" class="nav-link">Cómo pedir</a>
                    <a href="#preguntas-frecuentes" class="nav-link">FAQ</a>
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
                                            <div class="minicart-item-img" style="background:#f4f4f4; display:flex; align-items:center; justify-content:center; font-size:0.6rem; color:#888; width: 50px; height: 50px; flex-shrink: 0; border-radius: 4px;">Liso</div>
                                            <div class="minicart-item-info">
                                                <span class="minicart-item-name"><?php echo htmlspecialchars($m_item['name']); ?></span>
                                                <span class="minicart-item-meta"><?php echo $m_item['qty']; ?> uds</span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <a href="cotizacion.php" class="btn btn-primary" style="width:100%; text-align:center; padding:10px 0; font-size:0.8rem; text-decoration:none;">Ver mi cotización</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Botón Cotizar Principal -->
                    <a href="cotizacion.php" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">Solicitar cotización</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Menú Móvil -->
    <div class="mobile-nav-overlay"></div>
    <nav id="mobile-nav" class="mobile-nav" aria-label="Navegación móvil">
        <a href="index.php" class="mobile-link active">Inicio</a>
        <a href="#soluciones" class="mobile-link">Soluciones</a>
        <a href="#colecciones" class="mobile-link">Colecciones</a>
        <a href="#laser" class="mobile-link">Grabado láser</a>
        <a href="productos.php" class="mobile-link">Productos</a>
        <a href="#proceso" class="mobile-link">Cómo pedir</a>
        <a href="#preguntas-frecuentes" class="mobile-link">FAQ</a>
        <a href="cotizacion.php" class="btn btn-primary" style="margin-top: 1rem; width: 100%;">Solicitar cotización</a>
    </nav>

    <!-- MAIN CONTENT -->
    <main>
        
        <!-- 1. Hero Principal Premium -->
        <section class="hero-block reveal-on-scroll" id="inicio">
            <div class="container hero-carousel-wrapper">
                <div class="hero-carousel">
                    <div class="carousel-track">
                        <div class="carousel-slide">
                            <div class="carousel-slide-content-wrap">
                                <span class="section-subtitle" style="margin-bottom: 0.75rem; border-color: var(--primary); color: var(--primary); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.08em;">Identificación · Grabado láser · Personalización corporativa</span>
                                <h1 class="carousel-slide-title" style="margin-bottom: 0.75rem; font-family: var(--font-heading); font-size: clamp(1.6rem, 3.5vw, 2.5rem); line-height: 1.2;">Piezas corporativas personalizadas con acabado profesional</h1>
                                <p class="carousel-slide-subtitle" style="font-size: 1rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.5rem;">Diseñamos y personalizamos productos para empresas, instituciones y eventos que necesitan proyectar orden, presencia y calidad desde el primer detalle.</p>
                                <div class="hero-actions" style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 1.5rem;">
                                    <a href="productos.php" class="btn btn-primary">Explorar productos</a>
                                    <a href="cotizacion.php" class="btn btn-secondary">Solicitar cotización</a>
                                </div>
                                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 1rem; font-weight: 500;">✓ Vista previa antes de producir · ✓ Grabado permanente · ✓ Atención para pedidos corporativos</p>
                            </div>
                            <div class="carousel-image-container">
                                <!-- Showcase de composición visual premium temporal -->
                                <div class="hero-premium-visual" style="width: 100%; height: 100%; background: radial-gradient(circle at 70% 30%, var(--surface-light) 0%, var(--border) 100%); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; border-radius: 6px;">
                                    <div class="laser-beam-animation" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(99, 174, 44, 0.04) 0%, rgba(99, 174, 44, 0) 70%); pointer-events: none;"></div>
                                    <div style="padding: 2.5rem; text-align: left; z-index: 2; width: 100%;">
                                        <div style="display: flex; gap: 10px; margin-bottom: 1.5rem; flex-wrap: wrap;">
                                            <span style="font-size: 0.65rem; background: var(--dark); color: white; padding: 4px 10px; border-radius: 4px; font-weight: 600;">ACERO</span>
                                            <span style="font-size: 0.65rem; background: white; color: var(--dark); border: 1px solid var(--border); padding: 4px 10px; border-radius: 4px; font-weight: 600;">MADERA</span>
                                            <span style="font-size: 0.65rem; background: white; color: var(--dark); border: 1px solid var(--border); padding: 4px 10px; border-radius: 4px; font-weight: 600;">ACRÍLICO</span>
                                            <span style="font-size: 0.65rem; background: white; color: var(--dark); border: 1px solid var(--border); padding: 4px 10px; border-radius: 4px; font-weight: 600;">PVC</span>
                                            <span style="font-size: 0.65rem; background: white; color: var(--dark); border: 1px solid var(--border); padding: 4px 10px; border-radius: 4px; font-weight: 600;">CUERO PU</span>
                                        </div>
                                        <h4 style="font-family: var(--font-heading); font-size: 1.15rem; margin-bottom: 0.5rem; color: var(--dark);">Soportes Técnicos Premium</h4>
                                        <p style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.4; max-width: 320px;">Estudios de calibración láser y contraste sobre materiales nobles para asegurar legibilidad institucional.</p>
                                        <!-- Reemplazar por render o foto real de productos corporativos grabados en alta definición -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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

        <!-- 2. Sección: Soluciones por Tipo de Cliente -->
        <section id="soluciones" class="section-padding container reveal-on-scroll">
            <div class="section-header center">
                <span class="section-subtitle">Líneas de Servicio</span>
                <h2>Soluciones para empresas, instituciones y eventos</h2>
                <p>Presentaciones adaptadas al contexto y al nivel de formalidad de tu marca.</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-top: 2.5rem;">
                <div style="background: white; border: 1px solid var(--border); border-radius: 6px; padding: 2rem; display: flex; flex-direction: column; transition: transform 0.25s ease;">
                    <h3 style="font-family: var(--font-heading); font-size: 1.2rem; margin-bottom: 0.75rem;">1. Empresas</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">Productos personalizados para equipos comerciales, clientes, colaboradores y regalos corporativos.</p>
                    <a href="productos.php" class="btn btn-secondary" style="font-size: 0.75rem; padding: 6px 12px; margin-top: auto; align-self: flex-start;">Ver opciones</a>
                </div>
                <div style="background: white; border: 1px solid var(--border); border-radius: 6px; padding: 2rem; display: flex; flex-direction: column; transition: transform 0.25s ease;">
                    <h3 style="font-family: var(--font-heading); font-size: 1.2rem; margin-bottom: 0.75rem;">2. Instituciones</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">Credenciales, carnets, placas y elementos de identificación con presentación profesional.</p>
                    <a href="productos.php" class="btn btn-secondary" style="font-size: 0.75rem; padding: 6px 12px; margin-top: auto; align-self: flex-start;">Ver opciones</a>
                </div>
                <div style="background: white; border: 1px solid var(--border); border-radius: 6px; padding: 2rem; display: flex; flex-direction: column; transition: transform 0.25s ease;">
                    <h3 style="font-family: var(--font-heading); font-size: 1.2rem; margin-bottom: 0.75rem;">3. Eventos</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">Kits, acreditaciones, detalles personalizados y piezas conmemorativas para asistentes o invitados.</p>
                    <a href="productos.php" class="btn btn-secondary" style="font-size: 0.75rem; padding: 6px 12px; margin-top: auto; align-self: flex-start;">Ver opciones</a>
                </div>
                <div style="background: white; border: 1px solid var(--border); border-radius: 6px; padding: 2rem; display: flex; flex-direction: column; transition: transform 0.25s ease;">
                    <h3 style="font-family: var(--font-heading); font-size: 1.2rem; margin-bottom: 0.75rem;">4. Marcas</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">Productos pensados para aplicar tu identidad visual con un acabado limpio, sobrio y coherente.</p>
                    <a href="productos.php" class="btn btn-secondary" style="font-size: 0.75rem; padding: 6px 12px; margin-top: auto; align-self: flex-start;">Ver opciones</a>
                </div>
            </div>
        </section>

        <!-- 3. Sección: Colecciones Corporativas -->
        <section id="colecciones" class="section-padding section-bg-light reveal-on-scroll">
            <div class="container">
                <div class="section-header center">
                    <span class="section-subtitle">Nuestras Líneas</span>
                    <h2>Colecciones corporativas</h2>
                    <p>Organizamos los productos por uso, material y tipo de presentación para facilitar tu proceso de cotización.</p>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-top: 2.5rem;">
                    <div style="background: white; border: 1px solid var(--border); border-radius: 6px; overflow: hidden; display: flex; flex-direction: column;">
                        <div style="padding: 1.5rem 1.5rem 0 1.5rem; background: var(--surface-light); height: 120px; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600; color: var(--text-muted);">[ Vista de Termos & Agendas ]</span>
                            <!-- Reemplazar por foto real de linea ejecutiva -->
                        </div>
                        <div style="padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.15rem; margin-bottom: 0.5rem;">Línea ejecutiva</h3>
                            <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1rem;">Agendas, termos, cajas, libretas y accesorios para regalos empresariales.</p>
                            <p style="font-size: 0.72rem; color: var(--primary); font-weight: 600; margin-bottom: 1.5rem; margin-top: auto;">Materiales frecuentes: Acero, Cuero PU, Madera</p>
                            <a href="productos.php" class="btn btn-secondary" style="font-size: 0.78rem; text-align: center; width: 100%;">Ver productos</a>
                        </div>
                    </div>
                    
                    <div style="background: white; border: 1px solid var(--border); border-radius: 6px; overflow: hidden; display: flex; flex-direction: column;">
                        <div style="padding: 1.5rem 1.5rem 0 1.5rem; background: var(--surface-light); height: 120px; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600; color: var(--text-muted);">[ Vista de Credenciales & Placas ]</span>
                            <!-- Reemplazar por foto real de linea institucional -->
                        </div>
                        <div style="padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.15rem; margin-bottom: 0.5rem;">Línea institucional</h3>
                            <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1rem;">Carnets, credenciales, placas, identificadores y señalética personalizada.</p>
                            <p style="font-size: 0.72rem; color: var(--primary); font-weight: 600; margin-bottom: 1.5rem; margin-top: auto;">Materiales frecuentes: PVC, Acrílico, Aluminio</p>
                            <a href="productos.php" class="btn btn-secondary" style="font-size: 0.78rem; text-align: center; width: 100%;">Ver productos</a>
                        </div>
                    </div>
                    
                    <div style="background: white; border: 1px solid var(--border); border-radius: 6px; overflow: hidden; display: flex; flex-direction: column;">
                        <div style="padding: 1.5rem 1.5rem 0 1.5rem; background: var(--surface-light); height: 120px; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600; color: var(--text-muted);">[ Vista de Kits & Acreditaciones ]</span>
                            <!-- Reemplazar por foto real de linea eventos -->
                        </div>
                        <div style="padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.15rem; margin-bottom: 0.5rem;">Línea eventos</h3>
                            <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1rem;">Kits, acreditaciones, llaveros, detalles para asistentes y recuerdos corporativos.</p>
                            <p style="font-size: 0.72rem; color: var(--primary); font-weight: 600; margin-bottom: 1.5rem; margin-top: auto;">Materiales frecuentes: Cartón estructurado, Madera, Metal</p>
                            <a href="productos.php" class="btn btn-secondary" style="font-size: 0.78rem; text-align: center; width: 100%;">Ver productos</a>
                        </div>
                    </div>
                    
                    <div style="background: white; border: 1px solid var(--border); border-radius: 6px; overflow: hidden; display: flex; flex-direction: column;">
                        <div style="padding: 1.5rem 1.5rem 0 1.5rem; background: var(--surface-light); height: 120px; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600; color: var(--text-muted);">[ Vista de Placas Conmemorativas ]</span>
                            <!-- Reemplazar por foto real de linea reconocimientos -->
                        </div>
                        <div style="padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.15rem; margin-bottom: 0.5rem;">Línea reconocimientos</h3>
                            <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1rem;">Placas, piezas acrílicas, madera grabada y detalles conmemorativos.</p>
                            <p style="font-size: 0.72rem; color: var(--primary); font-weight: 600; margin-bottom: 1.5rem; margin-top: auto;">Materiales frecuentes: Madera Noble, Acrílico Cristal</p>
                            <a href="productos.php" class="btn btn-secondary" style="font-size: 0.78rem; text-align: center; width: 100%;">Ver productos</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Sección Clave: Tu Marca Aplicada con Criterio -->
        <section id="criterio" class="section-padding container reveal-on-scroll" style="border-bottom: 1px solid var(--border);">
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <span class="section-subtitle">Control de Calidad</span>
                <h2 style="font-family: var(--font-heading); font-size: clamp(1.4rem, 2.5vw, 2.1rem); margin-bottom: 1rem;">Tu marca aplicada con criterio</h2>
                <p style="font-size: 1rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 3rem;">No se trata solo de poner un logo sobre un producto. Revisamos el material, el tamaño, la ubicación y el acabado para que cada pieza se vea profesional desde el primer uso.</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 30px;">
                <div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.05rem; margin-bottom: 0.5rem;">Material correcto</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Elegimos productos compatibles con el tipo de personalización y el uso que tendrá cada pieza.</p>
                </div>
                <div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.05rem; margin-bottom: 0.5rem;">Logo bien ubicado</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Cuidamos proporción, alineación y legibilidad para que la marca no se vea improvisada.</p>
                </div>
                <div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.05rem; margin-bottom: 0.5rem;">Acabado coherente</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Buscamos que cada producto se sienta parte de la identidad visual de la empresa.</p>
                </div>
                <div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.05rem; margin-bottom: 0.5rem;">Entrega organizada</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Preparamos solicitudes para empresas, instituciones y eventos con una estructura clara desde el inicio.</p>
                </div>
            </div>
        </section>

        <!-- 5. Sección: Materiales que Trabajamos -->
        <?php if (!empty($materials)): ?>
            <section id="materiales" class="section-padding section-bg-light reveal-on-scroll">
                <div class="container">
                    <div class="section-header center">
                        <span class="section-subtitle">Soportes Reales</span>
                        <h2>Materiales que elevan la presentación de tu marca</h2>
                        <p>Cada material comunica algo distinto. Por eso revisamos el producto, el uso y el tipo de personalización antes de producir.</p>
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

        <!-- 6. Sección: Grabado Láser Especialidad -->
        <section id="laser" class="section-padding container reveal-on-scroll">
            <div class="laser-section">
                <div class="laser-layout">
                    <div class="laser-content">
                        <span class="section-subtitle" style="color: var(--primary); border-color: var(--primary);">Técnica de Marcado</span>
                        <h2 style="color: var(--light); margin-bottom: 1.25rem;">Nuestra especialidad: grabado láser</h2>
                        <p style="margin-bottom: 2rem; font-size: 0.98rem; color: rgba(252,253,251,0.85);">El láser marca directamente la superficie del material para lograr un acabado limpio, preciso y permanente. Es ideal para empresas que buscan piezas elegantes, resistentes y sin adhesivos superficiales.</p>
                        
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
                        <div class="image-placeholder theme-gray" style="aspect-ratio: 1.15; border-radius: 6px;">
                            <div class="image-placeholder-inner" style="background-color: var(--dark-alt); border-color: rgba(255,255,255,0.08);">
                                <svg class="image-placeholder-icon" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#63AE2C" stroke-width="1.5">
                                    <line x1="5" y1="12" x2="19" y2="12"/><line x1="12" y1="5" x2="12" y2="19"/>
                                </svg>
                                <span class="image-placeholder-text" style="color: var(--light); font-size: 0.75rem;">Control técnico del proceso de grabado</span>
                                <!-- Reemplazar por video real del proceso de grabado láser en el taller -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. Sección: Productos Destacados -->
        <section id="productos" class="section-padding container reveal-on-scroll">
            <div class="section-header center">
                <span class="section-subtitle">Showroom Corporativo</span>
                <h2>Productos para cotizar</h2>
                <p>Selecciona una o varias opciones y prepara una solicitud clara. Te orientamos con material, acabado y ubicación del logo.</p>
            </div>
            
            <div class="grid-3" style="margin-top: 2rem;">
                <?php foreach ($featuredProducts as $product): ?>
                    <?php 
                    $enriched = enrichProduct($product);
                    ?>
                    <div class="product-card catalog-product-item" 
                         data-name="<?php echo htmlspecialchars($enriched['name']); ?>" 
                         data-category="<?php echo htmlspecialchars($enriched['category']); ?>" 
                         data-material="<?php echo htmlspecialchars($enriched['material']); ?>" 
                         data-technique="<?php echo htmlspecialchars($enriched['technique']); ?>" 
                         data-use="<?php echo htmlspecialchars($enriched['use']); ?>"
                         style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; padding: 0;">
                        <div class="product-card-image-wrap">
                            <div class="image-placeholder theme-gray" style="border-radius: 0; aspect-ratio: 1.15;">
                                <?php if ($product['image_main']): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($product['image_main']); ?>" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php else: ?>
                                    <div class="image-placeholder-inner">
                                        <svg class="image-placeholder-icon" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.2">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        </svg>
                                        <span class="image-placeholder-text" style="font-size: 0.75rem;"><?php echo htmlspecialchars($product['name']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="product-card-body" style="padding: 1.25rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <span class="product-card-price" style="font-size: 0.72rem; text-transform: uppercase; color: var(--primary); font-weight: 600; display: block; margin-bottom: 4px;"><?php echo htmlspecialchars($enriched['category']); ?></span>
                            <h3 class="product-card-title" style="margin-bottom: 0.5rem; font-size: 1.15rem; font-family: var(--font-heading); color: var(--dark); font-weight: 500;"><?php echo htmlspecialchars($enriched['name']); ?></h3>
                            
                            <!-- Badges de especificaciones técnicas premium -->
                            <div class="product-specs-badges" style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 0.85rem; margin-top: 0.25rem;">
                                <span style="font-size: 0.65rem; background: rgba(0,0,0,0.03); color: var(--text-muted); padding: 3px 8px; border-radius: 20px; font-weight: 500; border: 1px solid rgba(0,0,0,0.02);"><?php echo htmlspecialchars($enriched['material']); ?></span>
                                <span style="font-size: 0.65rem; background: rgba(99, 174, 44, 0.08); color: var(--primary-hover); padding: 3px 8px; border-radius: 20px; font-weight: 600; border: 1px solid rgba(99, 174, 44, 0.1);"><?php echo htmlspecialchars($enriched['technique']); ?></span>
                            </div>

                            <p class="product-card-desc" style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex-grow: 1;"><?php echo htmlspecialchars($product['description_short']); ?></p>
                            
                            <div style="display: flex; gap: 8px; margin-top: auto;">
                                <button class="btn btn-primary btn-add-to-quote" 
                                        data-slug="<?php echo htmlspecialchars($product['slug']); ?>" 
                                        data-name="<?php echo htmlspecialchars($product['name']); ?>" 
                                        data-price="<?php echo (float)$product['price']; ?>"
                                        style="flex-grow: 1; padding: 8px 12px; font-size: 0.78rem; font-weight: 600; white-space: nowrap; border: none; cursor: pointer;">
                                    Agregar a cotización
                                </button>
                                <button class="btn btn-secondary btn-view-details" 
                                        data-slug="<?php echo htmlspecialchars($product['slug']); ?>"
                                        data-name="<?php echo htmlspecialchars($product['name']); ?>"
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
            </div>
            <div style="text-align: center; margin-top: 3rem;">
                <a href="productos.php" class="btn btn-secondary">Ver catálogo completo de productos</a>
            </div>
        </section>

        <!-- 8. Sección: Pedidos Corporativos sin Complicaciones -->
        <section id="pedidos-corporativos" class="section-padding section-bg-light reveal-on-scroll">
            <div class="container">
                <div class="section-header center">
                    <span class="section-subtitle">Organización & Capacidad</span>
                    <h2>Pedidos corporativos sin complicaciones</h2>
                    <p>Para empresas, instituciones o eventos, podemos ayudarte a organizar productos, cantidades, personalización y entrega de forma clara.</p>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-top: 2.5rem;">
                    <div style="background: white; padding: 2rem; border-radius: 6px; border: 1px solid var(--border);">
                        <h4 style="font-family: var(--font-heading); font-size: 1.05rem; margin-bottom: 0.5rem; font-weight: 600;">Cotización por cantidad</h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Puedes seleccionar varios productos y preparar una solicitud completa para tu área de compras.</p>
                    </div>
                    <div style="background: white; padding: 2rem; border-radius: 6px; border: 1px solid var(--border);">
                        <h4 style="font-family: var(--font-heading); font-size: 1.05rem; margin-bottom: 0.5rem; font-weight: 600;">Vista previa antes de producir</h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Revisamos ubicación, proporción y acabado sobre plantillas digitales antes de avanzar con el marcaje.</p>
                    </div>
                    <div style="background: white; padding: 2rem; border-radius: 6px; border: 1px solid var(--border);">
                        <h4 style="font-family: var(--font-heading); font-size: 1.05rem; margin-bottom: 0.5rem; font-weight: 600;">Organización por categorías</h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Se pueden ordenar productos específicos para equipos de ventas, eventos, visitas o áreas internas.</p>
                    </div>
                    <div style="background: white; padding: 2rem; border-radius: 6px; border: 1px solid var(--border);">
                        <h4 style="font-family: var(--font-heading); font-size: 1.05rem; margin-bottom: 0.5rem; font-weight: 600;">Coordinación de entrega</h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Deja indicada la ciudad o referencia de entrega para orientar los tiempos y costos de logística.</p>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 2.5rem;">
                    <button class="btn btn-primary toggle-quote-drawer-btn">Preparar pedido corporativo</button>
                </div>
            </div>
        </section>

        <!-- 9. Sección: Cómo hacemos tu pedido -->
        <section id="proceso" class="section-padding container reveal-on-scroll">
            <div class="section-header center">
                <span class="section-subtitle">Método de Trabajo</span>
                <h2>Un proceso pensado para evitar resultados improvisados</h2>
                <p>Controlamos cada fase técnica para que la presentación de tu marca sea coherente y limpia.</p>
            </div>
            
            <div class="process-grid">
                <div class="process-step">
                    <div class="process-number" style="color: var(--primary); font-weight: 700; opacity: 0.95;">01</div>
                    <h4>Revisamos tu idea</h4>
                    <p>Nos envías tu logo, referencia o necesidad. Revisamos si el archivo sirve para grabado o personalización.</p>
                </div>
                <div class="process-step">
                    <div class="process-number" style="color: var(--primary); font-weight: 700; opacity: 0.95;">02</div>
                    <h4>Definimos el producto correcto</h4>
                    <p>Te orientamos según material, cantidad, uso, presupuesto y tipo de evento o empresa.</p>
                </div>
                <div class="process-step">
                    <div class="process-number" style="color: var(--primary); font-weight: 700; opacity: 0.95;">03</div>
                    <h4>Preparamos una vista previa</h4>
                    <p>Validamos ubicación, tamaño y proporción antes de iniciar la producción.</p>
                </div>
                <div class="process-step">
                    <div class="process-number" style="color: var(--primary); font-weight: 700; opacity: 0.95;">04</div>
                    <h4>Producimos con cuidado</h4>
                    <p>Personalizamos las piezas y coordinamos entrega o retiro según los plazos establecidos.</p>
                </div>
            </div>
        </section>

        <!-- 10. Sección: Antes de producir, revisamos cómo se verá -->
        <section id="antes-despues" class="section-padding container reveal-on-scroll" style="border-top: 1px solid var(--border);">
            <div class="section-header center">
                <span class="section-subtitle">Capacidad de Taller</span>
                <h2>Antes de producir, revisamos cómo se verá</h2>
                <p>La personalización no debería hacerse al azar. Cuidamos proporciones y ubicación sobre el soporte final.</p>
            </div>
            
            <div class="comparison-grid" style="margin-top: 2rem;">
                <?php if (!empty($beforeAfters)): ?>
                    <?php foreach ($beforeAfters as $item): ?>
                        <div class="comparison-card">
                            <div class="comparison-views">
                                <div class="comparison-view">
                                    <div class="comparison-label">Soporte Base</div>
                                    <span style="font-size: 0.8rem; color: var(--text-muted);">Liso sin marcar</span>
                                </div>
                                <div class="comparison-view after">
                                    <div class="comparison-label after">Con Grabado</div>
                                    <span style="font-weight: 600; color: var(--primary);"><?php echo htmlspecialchars($item['technique']); ?></span>
                                </div>
                            </div>
                            <div class="comparison-desc" style="font-weight: 500; font-family: var(--font-heading);"><?php echo htmlspecialchars($item['title']); ?> (<?php echo htmlspecialchars($item['material']); ?>)</div>
                            <!-- Reemplazar por fotos reales de trabajos ejecutados en taller -->
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 3rem 0;">
                        Trabajos de grabado en madera, acero y acrílico. Visualización de acabados disponible próximamente.
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- 11. Sección FAQ Premium -->
        <section id="preguntas-frecuentes" class="section-padding section-bg-light reveal-on-scroll">
            <div class="container" style="max-width: 800px;">
                <div class="section-header center">
                    <span class="section-subtitle">Dudas Comunes</span>
                    <h2>Antes de cotizar, esto te puede ayudar</h2>
                    <p>Respuestas técnicas sobre diseño, personalización B2B y logística en Quito.</p>
                </div>
                
                <div class="faq-accordion" style="margin-top: 2rem; display: flex; flex-direction: column; gap: 10px;">
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.95rem;">¿Puedo pedir una vista previa antes de producir?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Sí. La vista previa permite revisar ubicación, tamaño y proporción del logo antes de personalizar.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.95rem;">¿Qué pasa si mi logo no está en buena calidad?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Podemos revisarlo y decirte si sirve para grabado o si necesita preparación antes de producir.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.95rem;">¿Puedo cotizar varios productos al mismo tiempo?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Sí. Puedes agregar diferentes productos a tu solicitud y enviar todo en un solo mensaje.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.95rem;">¿El grabado se despega o se borra?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">No funciona como un adhesivo. El láser marca directamente el material compatible, logrando un relieve de alta resistencia.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.95rem;">¿Pueden preparar productos para eventos?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Sí. Se pueden organizar opciones para asistentes, invitados, equipos corporativos o reconocimientos.</p>
                        </div>
                    </div>

                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.95rem;">¿Puedo consultar un producto que no aparece en el catálogo?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Sí. El catálogo funciona como referencia. Puedes enviar tu idea y se revisa si es viable personalizarla en el taller.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 12. CTA Final Premium -->
        <section class="section-padding container reveal-on-scroll" style="text-align: center; max-width: 800px; margin-top: 1rem; margin-bottom: 2rem;">
            <span class="section-subtitle" style="color: var(--primary); border-color: var(--primary);">Contacto Directo</span>
            <h2 style="margin-bottom: 1.25rem; font-family: var(--font-heading);">¿Ya tienes una idea para personalizar?</h2>
            <p style="margin-bottom: 2rem; font-size: 1rem; color: var(--text-muted); line-height: 1.6;">Envíanos el producto que necesitas, la cantidad aproximada y tu logo. Te ayudamos a elegir el material, el acabado y la mejor forma de presentarlo.</p>
            <div class="hero-actions" style="justify-content: center; display: flex; gap: 12px; flex-wrap: wrap;">
                <button class="btn btn-primary toggle-quote-drawer-btn">Preparar cotización</button>
                <a href="productos.php" class="btn btn-secondary">Ver productos</a>
            </div>
            <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 1.25rem;">No necesitas tener todo definido. Puedes enviarnos una idea general y la revisamos contigo.</p>
        </section>

    </main>

    <!-- 13. Footer Corporativo -->
    <footer class="main-footer">
        <div class="container footer-top section-padding" style="padding-top: 3rem; padding-bottom: 3rem;">
            <div class="footer-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 40px;">
                <div class="footer-brand-column">
                    <a href="index.php" class="logo footer-logo" aria-label="CardNet.ec Inicio">
                        <img src="images/logo.png" alt="CardNet.ec Logo" class="logo-img">
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
                        <a href="#preguntas-frecuentes" class="footer-link">Preguntas frecuentes</a>
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
    <script src="js/slider.js"></script>
    <script src="js/animations.js"></script>
</body>
</html>
