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
    <link rel="icon" type="image/png" href="favicon.png?v=2.0">
    <link rel="apple-touch-icon" href="favicon.png?v=2.0">
    
    <!-- Open Graph -->
    <meta property="og:title" content="CardNet.ec | Personalización corporativa y grabado láser">
    <meta property="og:description" content="Piezas personalizadas para empresas, instituciones y eventos con acabado profesional.">
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

    <!-- SEO: Marcado Estructurado JSON-LD -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "CardNet.ec",
      "image": "https://cardnet.ec/images/logo.png?v=2.0",
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
                    <img src="images/logo.png?v=2.0" alt="CardNet.ec Logo" class="logo-img">
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
        
        <!-- 1. Hero Principal - Carrusel Automático Showroom -->
        <section class="hero-block reveal-on-scroll" id="inicio" style="padding-top: 1rem; padding-bottom: 2rem;">
            <div class="container" style="position: relative;">
                <div class="hero-right-carousel" style="width: 100%; min-height: 520px; height: 70vh; position: relative; border-radius: var(--radius-md); overflow: hidden; background: #E2E5DF; border: 1px solid rgba(0,0,0,0.03); display: flex; flex-direction: column;">
                    <div class="hero-slider-track" style="width: 100%; height: 100%; position: relative; flex-grow: 1;">
                        <?php if (!empty($slides)): ?>
                            <?php foreach ($slides as $idx => $slide): ?>
                                <div class="hero-slide-item <?php echo $idx === 0 ? 'active' : ''; ?>" data-slide-index="<?php echo $idx; ?>" 
                                     style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: <?php echo $idx === 0 ? '1' : '0'; ?>; visibility: <?php echo $idx === 0 ? 'visible' : 'hidden'; ?>; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: <?php echo $idx === 0 ? '5' : '1'; ?>; padding: 4rem;">
                                    
                                    <?php if ($slide['image']): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($slide['image']); ?>" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1;" alt="<?php echo htmlspecialchars($slide['title']); ?>">
                                    <?php else: ?>
                                        <div style="position: absolute; inset: 0; background: radial-gradient(circle at 70% 30%, #F5F6F3 0%, #D4D8D1 100%); z-index: 1;"></div>
                                    <?php endif; ?>

                                    <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(16, 20, 15, 0.75) 0%, rgba(16, 20, 15, 0.6) 40%, rgba(16, 20, 15, 0.1) 100%); z-index: 2;"></div>

                                    <div style="position: relative; z-index: 3; max-width: 580px; color: white;">
                                        <span class="section-subtitle" style="margin-bottom: 1rem; border-color: var(--primary); color: #8CFF32; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em; display: inline-block; border-left: 3px solid var(--primary); padding-left: 10px; border-radius: 0;">
                                            Destacado CardNet
                                        </span>
                                        <h2 style="font-family: var(--font-heading); font-size: clamp(2rem, 4vw, 3rem); color: white; font-weight: 500; margin-bottom: 1rem; line-height: 1.2; text-shadow: 0 2px 4px rgba(0,0,0,0.15);">
                                            <?php echo htmlspecialchars($slide['title']); ?>
                                        </h2>
                                        <p style="font-size: clamp(0.95rem, 1.5vw, 1.1rem); color: rgba(255,255,255,0.9); line-height: 1.6; margin-bottom: 2rem;">
                                            <?php echo htmlspecialchars($slide['subtitle'] ?: 'Personalización premium con marcado láser de alta definición.'); ?>
                                        </p>
                                        
                                        <a href="<?php echo htmlspecialchars($slide['cta_url'] ?: 'productos.php'); ?>" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; background-color: var(--primary); border: none; display: inline-block;">
                                            <?php echo htmlspecialchars($slide['cta_text'] ?: 'Ver Colección'); ?> →
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Slide 1 -->
                            <div class="hero-slide-item active" data-slide-index="0" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: 1; visibility: visible; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: 5; padding: 4rem;">
                                <div style="position: absolute; inset: 0; background: linear-gradient(135deg, #1E231C 0%, #2A3027 100%); z-index: 1;"></div>
                                <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(16, 20, 15, 0.75) 0%, rgba(16, 20, 15, 0.4) 100%); z-index: 2;"></div>
                                <div style="position: relative; z-index: 3; max-width: 580px; color: white;">
                                    <span class="section-subtitle" style="margin-bottom: 1rem; border-color: var(--primary); color: #8CFF32; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em; display: inline-block; border-left: 3px solid var(--primary); padding-left: 10px; border-radius: 0;">Colección Ejecutiva</span>
                                    <h2 style="font-family: var(--font-heading); font-size: clamp(2rem, 4vw, 3rem); color: white; font-weight: 500; margin-bottom: 1rem; line-height: 1.2;">Productos que hacen visible tu marca</h2>
                                    <p style="font-size: 1.05rem; color: rgba(255,255,255,0.9); line-height: 1.6; margin-bottom: 2.2rem;">Grabado láser y personalización sobre piezas que sí generan impacto.</p>
                                    <a href="#productos" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600;">Ver productos destacados</a>
                                </div>
                            </div>

                            <!-- Slide 2 -->
                            <div class="hero-slide-item" data-slide-index="1" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: 1; padding: 4rem;">
                                <div style="position: absolute; inset: 0; background: linear-gradient(135deg, #112818 0%, #1F3F2A 100%); z-index: 1;"></div>
                                <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(16, 20, 15, 0.75) 0%, rgba(16, 20, 15, 0.4) 100%); z-index: 2;"></div>
                                <div style="position: relative; z-index: 3; max-width: 580px; color: white;">
                                    <span class="section-subtitle" style="margin-bottom: 1rem; border-color: var(--primary); color: #8CFF32; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em; display: inline-block; border-left: 3px solid var(--primary); padding-left: 10px; border-radius: 0;">Soportes Hidratación</span>
                                    <h2 style="font-family: var(--font-heading); font-size: clamp(2rem, 4vw, 3rem); color: white; font-weight: 500; margin-bottom: 1rem; line-height: 1.2;">Termos grabados con acabado premium</h2>
                                    <p style="font-size: 1.05rem; color: rgba(255,255,255,0.9); line-height: 1.6; margin-bottom: 2.2rem;">Personalización limpia, elegante y resistente al uso diario.</p>
                                    <a href="productos.php" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600;">Quiero algo similar</a>
                                </div>
                            </div>

                            <!-- Slide 3 -->
                            <div class="hero-slide-item" data-slide-index="2" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: 1; padding: 4rem;">
                                <div style="position: absolute; inset: 0; background: linear-gradient(135deg, #252835 0%, #151821 100%); z-index: 1;"></div>
                                <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(16, 20, 15, 0.75) 0%, rgba(16, 20, 15, 0.4) 100%); z-index: 2;"></div>
                                <div style="position: relative; z-index: 3; max-width: 580px; color: white;">
                                    <span class="section-subtitle" style="margin-bottom: 1rem; border-color: var(--primary); color: #8CFF32; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em; display: inline-block; border-left: 3px solid var(--primary); padding-left: 10px; border-radius: 0;">Soluciones Corporativas</span>
                                    <h2 style="font-family: var(--font-heading); font-size: clamp(2rem, 4vw, 3rem); color: white; font-weight: 500; margin-bottom: 1rem; line-height: 1.2;">Kits corporativos con mejor presentación</h2>
                                    <p style="font-size: 1.05rem; color: rgba(255,255,255,0.9); line-height: 1.6; margin-bottom: 2.2rem;">Piezas personalizadas para eventos, equipos y clientes.</p>
                                    <a href="cotizacion.php" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600;">Cotizar una idea</a>
                                </div>
                            </div>

                            <!-- Slide 4 -->
                            <div class="hero-slide-item" data-slide-index="3" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: 1; padding: 4rem;">
                                <div style="position: absolute; inset: 0; background: linear-gradient(135deg, #1C2224 0%, #30373A 100%); z-index: 1;"></div>
                                <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(16, 20, 15, 0.75) 0%, rgba(16, 20, 15, 0.4) 100%); z-index: 2;"></div>
                                <div style="position: relative; z-index: 3; max-width: 580px; color: white;">
                                    <span class="section-subtitle" style="margin-bottom: 1rem; border-color: var(--primary); color: #8CFF32; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em; display: inline-block; border-left: 3px solid var(--primary); padding-left: 10px; border-radius: 0;">Identificación Segura</span>
                                    <h2 style="font-family: var(--font-heading); font-size: clamp(2rem, 4vw, 3rem); color: white; font-weight: 500; margin-bottom: 1rem; line-height: 1.2;">Carnets y credenciales con imagen profesional</h2>
                                    <p style="font-size: 1.05rem; color: rgba(255,255,255,0.9); line-height: 1.6; margin-bottom: 2.2rem;">Diseños corporativos listos para representar tu empresa.</p>
                                    <a href="productos.php" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600;">Explorar opciones</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Indicadores estilo barra fina de progreso en la parte inferior derecha -->
                    <div style="position: absolute; bottom: 2rem; right: 3rem; z-index: 10; display: flex; gap: 8px;">
                        <?php 
                        $total_slides = !empty($slides) ? count($slides) : 4;
                        for ($idx = 0; $idx < $total_slides; $idx++): 
                        ?>
                            <button class="hero-dot <?php echo $idx === 0 ? 'active' : ''; ?>" data-slide-to="<?php echo $idx; ?>" aria-label="Slide <?php echo $idx+1; ?>" style="width: 32px; height: 3px; border-radius: 2px; border: none; background: <?php echo $idx === 0 ? 'var(--primary)' : 'rgba(255,255,255,0.3)'; ?>; cursor: pointer; transition: background 0.3s ease, width 0.3s ease; padding:0;"></button>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Barra de Garantías -->
        <section class="satisfaction-bar" style="border-bottom: 1px solid var(--border);">
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
                    <span>Soportes seleccionados B2B</span>
                </div>
            </div>
        </section>

        <!-- 2. Sección: Productos Destacados (Showroom Curado) -->
        <section id="productos" class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle">Showroom Corporativo</span>
                <h2>Productos destacados</h2>
                <p>Nuestra selección de piezas premium para representación de marca y personalización de alta precisión.</p>
            </div>
            
            <div class="grid-3">
                <?php if (!empty($featuredProducts)): ?>
                    <?php foreach ($featuredProducts as $product): 
                        $enriched = enrichProduct($product);
                    ?>
                        <div class="product-card catalog-product-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease, border-color 0.25s ease;">
                            <div class="product-card-image-wrap" style="position: relative; overflow: hidden; aspect-ratio: 1.15; background: var(--surface-light); border-bottom: 1px solid var(--border);">
                                <?php if ($product['image_main']): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($product['image_main']); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" loading="lazy" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php else: ?>
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--text-muted);">
                                        <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.2">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                                <span style="font-size: 0.7rem; text-transform: uppercase; color: var(--primary); font-weight: 600; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">
                                    <?php echo htmlspecialchars($enriched['material']); ?> · <?php echo htmlspecialchars($enriched['technique']); ?>
                                </span>
                                <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 0.75rem;">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </h3>
                                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">
                                    <?php echo htmlspecialchars($product['description_short']); ?>
                                </p>
                                <div style="display: flex; gap: 8px; margin-top: auto;">
                                    <button class="btn btn-primary btn-add-to-quote" 
                                            data-slug="<?php echo htmlspecialchars($product['slug']); ?>" 
                                            data-name="<?php echo htmlspecialchars($product['name']); ?>" 
                                            data-price="<?php echo (float)$product['price']; ?>"
                                            style="flex-grow: 1; padding: 10px 14px; font-size: 0.8rem; font-weight: 600; border: none; cursor: pointer;">
                                        Cotizar este producto
                                    </button>
                                    <button class="btn btn-secondary btn-view-details" 
                                            data-slug="<?php echo htmlspecialchars($product['slug']); ?>"
                                            data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                            data-category="<?php echo htmlspecialchars($enriched['category']); ?>"
                                            data-material="<?php echo htmlspecialchars($enriched['material']); ?>"
                                            data-technique="<?php echo htmlspecialchars($enriched['technique']); ?>"
                                            data-use="<?php echo htmlspecialchars($enriched['use']); ?>"
                                            data-desc="<?php echo htmlspecialchars($enriched['details']); ?>"
                                            style="padding: 10px 14px; font-size: 0.8rem; font-weight: 500; border: 1px solid var(--border); background: white; cursor: pointer;">
                                        Quiero este estilo
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 3rem 0;">
                        No hay productos destacados activos en este momento.
                    </div>
                <?php endif; ?>
            </div>
            
            <div style="text-align: center; margin-top: 3.5rem;">
                <a href="productos.php" class="btn btn-secondary" style="padding: 12px 28px; border: 1px solid var(--border); background: white;">Ver catálogo completo</a>
            </div>
        </section>

        <!-- 3. Sección: Categorías Visuales -->
        <section id="categorias-visuales" class="section-padding section-bg-light reveal-on-scroll">
            <div class="container">
                <div class="section-header center" style="margin-bottom: 3.5rem;">
                    <span class="section-subtitle">Showroom Categorías</span>
                    <h2>Explorar por línea de producto</h2>
                    <p>Colecciones diseñadas para facilitar la búsqueda visual de tu soporte de marca.</p>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
                    <?php
                    $visual_cats = [
                        ['name' => 'Termos', 'slug' => 'termos', 'desc' => 'Termos de acero inoxidable de doble pared.', 'img' => 'uploads/termo.png'],
                        ['name' => 'Agendas', 'slug' => 'agendas', 'desc' => 'Agendas ejecutivas y libretas premium.', 'img' => 'uploads/agenda.png'],
                        ['name' => 'Llaveros', 'slug' => 'llaveros', 'desc' => 'Llaveros corporativos de metal o madera.', 'img' => 'uploads/llavero.png'],
                        ['name' => 'Placas y Reconocimientos', 'slug' => 'placas', 'desc' => 'Placas conmemorativas y reconocimientos.', 'img' => 'uploads/placa.png'],
                        ['name' => 'Kits Corporativos', 'slug' => 'kits', 'desc' => 'Kits combinados de alta presentación.', 'img' => 'uploads/kit.png'],
                        ['name' => 'Carnets y Credenciales', 'slug' => 'carnets', 'desc' => 'Identificación corporativa en PVC.', 'img' => 'uploads/carnets.png']
                    ];
                    foreach ($visual_cats as $cat):
                    ?>
                        <a href="productos.php?cat=<?php echo $cat['slug']; ?>" class="category-visual-card" style="position: relative; display: block; border-radius: var(--radius-md); overflow: hidden; aspect-ratio: 1; background: #2A3027; border: 1px solid var(--border); text-decoration: none;">
                            <div style="position: absolute; inset: 0; background: radial-gradient(circle at 50% 50%, rgba(99, 174, 44, 0.08) 0%, rgba(16, 20, 15, 0.95) 100%); z-index: 1;"></div>
                            
                            <div style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: flex-end; padding: 1.5rem; z-index: 2;">
                                <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: white; margin-bottom: 4px; text-shadow: 0 1px 2px rgba(0,0,0,0.3);"><?php echo $cat['name']; ?></h3>
                                <p style="font-size: 0.75rem; color: rgba(255,255,255,0.7); line-height: 1.3; margin: 0; max-width: 180px;"><?php echo $cat['desc']; ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- 4. Sección: Especialidad Grabado Láser (Sintética) -->
        <section id="laser" class="section-padding container reveal-on-scroll">
            <div class="laser-section" style="padding: 4rem; background: #1A1F18; border-radius: var(--radius-lg); overflow: hidden; position: relative;">
                <div style="max-width: 600px; position: relative; z-index: 5;">
                    <span class="section-subtitle" style="color: #8CFF32; border-color: #8CFF32;">Precisión de Marcado</span>
                    <h2 style="color: white; font-family: var(--font-heading); font-size: 2.2rem; font-weight: 500; margin-bottom: 1.25rem;">Nuestra especialidad: grabado láser</h2>
                    <p style="color: rgba(255,255,255,0.85); font-size: 1.05rem; line-height: 1.6; margin-bottom: 2rem;">
                        Marcamos sobre metal, madera, cuero, acrílico y otros materiales para lograr piezas limpias, elegantes y duraderas. Sin tintas ni adhesivos superficiales, garantizando un acabado indeleble que resiste el uso diario.
                    </p>
                    <a href="productos.php" class="btn btn-primary" style="background-color: var(--primary); border: none; padding: 12px 28px; font-weight: 600;">Explorar grabado láser</a>
                </div>
                
                <div style="position: absolute; right: 10%; top: 0; bottom: 0; width: 1px; background: linear-gradient(to bottom, transparent, #8CFF32 50%, transparent); opacity: 0.6; z-index: 1;"></div>
            </div>
        </section>

        <!-- 5. Sección: Antes y Después del Grabado (Visual) -->
        <section id="antes-despues" class="section-padding container reveal-on-scroll" style="border-top: 1px solid var(--border);">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle">Garantía de Acabado</span>
                <h2>Antes y después del grabado</h2>
                <p>Compara el soporte base original con el resultado final personalizado en nuestro taller.</p>
            </div>
            
            <div class="comparison-grid">
                <?php if (!empty($beforeAfters)): ?>
                    <?php foreach ($beforeAfters as $item): ?>
                        <div class="comparison-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; background: white; transition: transform 0.25s ease;">
                            <div class="comparison-views" style="display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid var(--border);">
                                <div class="comparison-view" style="padding: 2.5rem 1rem; text-align: center; background: var(--surface-light);">
                                    <div class="comparison-label" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 4px;">Soporte Base</div>
                                    <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Liso</span>
                                </div>
                                <div class="comparison-view after" style="padding: 2.5rem 1rem; text-align: center; background: white; border-left: 1px solid var(--border);">
                                    <div class="comparison-label after" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 4px;">Con Marcado</div>
                                    <span style="font-size: 0.85rem; font-weight: 600; color: var(--primary);"><?php echo htmlspecialchars($item['technique']); ?></span>
                                </div>
                            </div>
                            <div class="comparison-desc" style="padding: 1.25rem; text-align: center; font-family: var(--font-heading); font-size: 0.95rem; font-weight: 500; color: var(--dark);">
                                <?php echo htmlspecialchars($item['title']); ?> · <?php echo htmlspecialchars($item['material']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback 3 cards de prueba si la BD no tiene registros -->
                    
                    <!-- Card 1 -->
                    <div class="comparison-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; background: white;">
                        <div class="comparison-views" style="display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid var(--border);">
                            <div class="comparison-view" style="padding: 2.5rem 1rem; text-align: center; background: var(--surface-light);">
                                <div class="comparison-label" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 4px;">Termo de Acero</div>
                                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Metal Liso</span>
                            </div>
                            <div class="comparison-view after" style="padding: 2.5rem 1rem; text-align: center; background: white; border-left: 1px solid var(--border);">
                                <div class="comparison-label after" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 4px;">Grabado Láser</div>
                                <span style="font-size: 0.85rem; font-weight: 600; color: var(--primary);">Bajo Relieve</span>
                            </div>
                        </div>
                        <div class="comparison-desc" style="padding: 1.25rem; text-align: center; font-family: var(--font-heading); font-size: 0.95rem; font-weight: 500; color: var(--dark);">
                            Termo de Acero Inoxidable
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="comparison-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; background: white;">
                        <div class="comparison-views" style="display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid var(--border);">
                            <div class="comparison-view" style="padding: 2.5rem 1rem; text-align: center; background: var(--surface-light);">
                                <div class="comparison-label" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 4px;">Libreta de Cuero</div>
                                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Cuero Liso</span>
                            </div>
                            <div class="comparison-view after" style="padding: 2.5rem 1rem; text-align: center; background: white; border-left: 1px solid var(--border);">
                                <div class="comparison-label after" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 4px;">Bajo Relieve</div>
                                <span style="font-size: 0.85rem; font-weight: 600; color: var(--primary);">Termograbado</span>
                            </div>
                        </div>
                        <div class="comparison-desc" style="padding: 1.25rem; text-align: center; font-family: var(--font-heading); font-size: 0.95rem; font-weight: 500; color: var(--dark);">
                            Agenda Ejecutiva de Cuero PU
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="comparison-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; background: white;">
                        <div class="comparison-views" style="display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid var(--border);">
                            <div class="comparison-view" style="padding: 2.5rem 1rem; text-align: center; background: var(--surface-light);">
                                <div class="comparison-label" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 4px;">Placa Base</div>
                                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Acrílico Liso</span>
                            </div>
                            <div class="comparison-view after" style="padding: 2.5rem 1rem; text-align: center; background: white; border-left: 1px solid var(--border);">
                                <div class="comparison-label after" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 4px;">Grabado Blanco</div>
                                <span style="font-size: 0.85rem; font-weight: 600; color: var(--primary);">Láser de CO2</span>
                            </div>
                        </div>
                        <div class="comparison-desc" style="padding: 1.25rem; text-align: center; font-family: var(--font-heading); font-size: 0.95rem; font-weight: 500; color: var(--dark);">
                            Placa Conmemorativa de Acrílico
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- 6. Sección: Materiales que Trabajamos (Visual) -->
        <section id="materiales" class="section-padding section-bg-light reveal-on-scroll">
            <div class="container">
                <div class="section-header center" style="margin-bottom: 3.5rem;">
                    <span class="section-subtitle">Soportes Reales</span>
                    <h2>Materiales que trabajamos</h2>
                    <p>Seleccionamos soportes nobles y de alta compatibilidad con el grabado láser para un resultado óptimo.</p>
                </div>
                
                <div class="materials-grid">
                    <div class="material-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; background: white; text-align: center; transition: border-color 0.25s ease;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 0.5rem;">Acero Inoxidable</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Perfecto para termos y botellas de alta durabilidad.</p>
                    </div>
                    <div class="material-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; background: white; text-align: center; transition: border-color 0.25s ease;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 0.5rem;">Madera</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Textura orgánica e ideal para placas y empaques rústicos.</p>
                    </div>
                    <div class="material-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; background: white; text-align: center; transition: border-color 0.25s ease;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 0.5rem;">Acrílico</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Terminaciones limpias y cristalinas para reconocimientos.</p>
                    </div>
                    <div class="material-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; background: white; text-align: center; transition: border-color 0.25s ease;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 0.5rem;">Cuero / PU</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Elegancia clásica para agendas, libretas y estuches.</p>
                    </div>
                    <div class="material-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; background: white; text-align: center; transition: border-color 0.25s ease;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 0.5rem;">Vidrio</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Grabado translúcido y elegante para copas y botellas.</p>
                    </div>
                    <div class="material-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; background: white; text-align: center; transition: border-color 0.25s ease;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 0.5rem;">Metal</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Terminación brillante u opaca para esferos y placas técnicas.</p>
                    </div>
                    <div class="material-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; background: white; text-align: center; transition: border-color 0.25s ease;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 0.5rem;">PVC</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Soporte plástico resistente para identificación corporativa.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. Sección: Proceso Corto (Cómo trabajamos) -->
        <section id="proceso" class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle">Flujo de Taller</span>
                <h2>Cómo trabajamos</h2>
                <p>Nuestra metodología simplificada para garantizar que cada pedido cumpla tus expectativas.</p>
            </div>
            
            <div class="process-grid">
                <div class="process-step" style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-md);">
                    <div class="process-number" style="color: var(--primary); font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">01</div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">Eliges el producto</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Exploras y seleccionas los artículos promocionales de nuestro catálogo.</p>
                </div>
                <div class="process-step" style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-md);">
                    <div class="process-number" style="color: var(--primary); font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">02</div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">Nos envías tu logo</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Nos compartes tu diseño o marca y revisamos su viabilidad técnica.</p>
                </div>
                <div class="process-step" style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-md);">
                    <div class="process-number" style="color: var(--primary); font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">03</div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">Personalizamos</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Grabamos en el taller cuidando cada detalle con calibración fina de láser.</p>
                </div>
                <div class="process-step" style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-md);">
                    <div class="process-number" style="color: var(--primary); font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">04</div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">Te entregamos</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Recibes una pieza impecable, lista para representar con orgullo tu marca.</p>
                </div>
            </div>
        </section>

        <!-- 8. Sección FAQ Premium -->
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
                </div>
            </div>
        </section>

        <!-- 9. CTA Final Premium -->
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
                        <img src="images/logo.png?v=2.0" alt="CardNet.ec Logo" class="logo-img">
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
