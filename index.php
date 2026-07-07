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

// 5. Obtener logos de clientes activos
try {
    $stmt = $pdo->query("SELECT * FROM clientes WHERE is_active = 1 ORDER BY order_val ASC");
    $clients = $stmt->fetchAll();
} catch (PDOException $e) {
    $clients = [];
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
<style>
    @keyframes scrollTicker {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    .ticker-track {
        display: flex;
        gap: 4rem;
        width: max-content;
        animation: scrollTicker 30s linear infinite;
    }
    .ticker-track:hover {
        animation-play-state: paused;
    }
    .ticker-item img {
        filter: grayscale(100%);
        opacity: 0.55;
        transition: opacity 0.3s ease, filter 0.3s ease;
    }
    .ticker-item img:hover {
        filter: grayscale(0%);
        opacity: 1;
    }
    </style>
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
                    <a href="#productos" class="nav-link">Destacados</a>
                    <a href="#laser" class="nav-link">Grabado láser</a>
                    <a href="productos.php" class="nav-link">Productos</a>
                    <a href="#antes-despues" class="nav-link">Personalización</a>
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
        <a href="#productos" class="mobile-link">Destacados</a>
        <a href="#laser" class="mobile-link">Grabado láser</a>
        <a href="productos.php" class="mobile-link">Productos</a>
        <a href="#antes-despues" class="mobile-link">Personalización</a>
        <a href="cotizacion.php" class="btn btn-primary" style="margin-top: 1rem; width: 100%;">Solicitar cotización</a>
    </nav>

    <!-- MAIN CONTENT -->
    <main>
        
        <!-- 1. Hero Principal - Carrusel Automático Showroom -->
        <section class="hero-block reveal-on-scroll" id="inicio" style="padding-top: 1rem; padding-bottom: 2rem;">
            <div class="container" style="position: relative;">
                <div class="hero-right-carousel" style="width: 100%; min-height: 520px; height: 70vh; position: relative; border-radius: var(--radius-md); overflow: hidden; background: #E2E5DF; border: 1px solid rgba(0,0,0,0.03); display: flex; flex-direction: column;">
                    <div class="hero-slider-track" style="width: 100%; height: 100%; position: relative; flex-grow: 1;">
                        
                        <!-- Slide 1: Termos -->
                        <div class="hero-slide-item active" data-slide-index="0" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: 1; visibility: visible; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: 5; padding: 4rem;">
                            <div style="position: absolute; inset: 0; background: linear-gradient(135deg, #1E231C 0%, #2A3027 100%); z-index: 1;"></div>
                            <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(16, 20, 15, 0.7) 0%, rgba(16, 20, 15, 0.3) 100%); z-index: 2;"></div>
                            <div style="position: relative; z-index: 3; max-width: 580px; color: white;">
                                <h2 style="font-family: var(--font-heading); font-size: clamp(2rem, 4vw, 3rem); color: white; font-weight: 500; margin-bottom: 1rem; line-height: 1.2;">Termos grabados en acero inoxidable</h2>
                                <p style="font-size: 1.05rem; color: rgba(255,255,255,0.9); line-height: 1.6; margin-bottom: 2.2rem;">Acabado láser limpio, sobrio y resistente al uso diario.</p>
                                <a href="#productos" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600;">Cotizar este producto</a>
                            </div>
                        </div>

                        <!-- Slide 2: Agendas -->
                        <div class="hero-slide-item" data-slide-index="1" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: 1; padding: 4rem;">
                            <div style="position: absolute; inset: 0; background: linear-gradient(135deg, #112818 0%, #1F3F2A 100%); z-index: 1;"></div>
                            <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(16, 20, 15, 0.7) 0%, rgba(16, 20, 15, 0.3) 100%); z-index: 2;"></div>
                            <div style="position: relative; z-index: 3; max-width: 580px; color: white;">
                                <h2 style="font-family: var(--font-heading); font-size: clamp(2rem, 4vw, 3rem); color: white; font-weight: 500; margin-bottom: 1rem; line-height: 1.2;">Agendas corporativas personalizadas</h2>
                                <p style="font-size: 1.05rem; color: rgba(255,255,255,0.9); line-height: 1.6; margin-bottom: 2.2rem;">Detalles elegantes para clientes, equipos y eventos.</p>
                                <a href="#productos" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600;">Quiero algo similar</a>
                            </div>
                        </div>

                        <!-- Slide 3: Kits -->
                        <div class="hero-slide-item" data-slide-index="2" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: 1; padding: 4rem;">
                            <div style="position: absolute; inset: 0; background: linear-gradient(135deg, #252835 0%, #151821 100%); z-index: 1;"></div>
                            <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(16, 20, 15, 0.7) 0%, rgba(16, 20, 15, 0.3) 100%); z-index: 2;"></div>
                            <div style="position: relative; z-index: 3; max-width: 580px; color: white;">
                                <h2 style="font-family: var(--font-heading); font-size: clamp(2rem, 4vw, 3rem); color: white; font-weight: 500; margin-bottom: 1rem; line-height: 1.2;">Kits corporativos listos para entregar</h2>
                                <p style="font-size: 1.05rem; color: rgba(255,255,255,0.9); line-height: 1.6; margin-bottom: 2.2rem;">Termos, agendas y cajas personalizadas para empresas y eventos.</p>
                                <a href="cotizacion.php" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600;">Cotizar una idea</a>
                            </div>
                        </div>

                        <!-- Slide 4: Carnets -->
                        <div class="hero-slide-item" data-slide-index="3" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: 1; padding: 4rem;">
                            <div style="position: absolute; inset: 0; background: linear-gradient(135deg, #1C2224 0%, #30373A 100%); z-index: 1;"></div>
                            <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(16, 20, 15, 0.7) 0%, rgba(16, 20, 15, 0.3) 100%); z-index: 2;"></div>
                            <div style="position: relative; z-index: 3; max-width: 580px; color: white;">
                                <h2 style="font-family: var(--font-heading); font-size: clamp(2rem, 4vw, 3rem); color: white; font-weight: 500; margin-bottom: 1rem; line-height: 1.2;">Carnets y credenciales profesionales</h2>
                                <p style="font-size: 1.05rem; color: rgba(255,255,255,0.9); line-height: 1.6; margin-bottom: 2.2rem;">Identificación corporativa con diseño limpio y presentación cuidada.</p>
                                <a href="productos.php" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600;">Explorar opciones</a>
                            </div>
                        </div>

                        <!-- Slide 5: Placas -->
                        <div class="hero-slide-item" data-slide-index="4" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: 1; padding: 4rem;">
                            <div style="position: absolute; inset: 0; background: linear-gradient(135deg, #2D231E 0%, #3E322B 100%); z-index: 1;"></div>
                            <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(16, 20, 15, 0.7) 0%, rgba(16, 20, 15, 0.3) 100%); z-index: 2;"></div>
                            <div style="position: relative; z-index: 3; max-width: 580px; color: white;">
                                <h2 style="font-family: var(--font-heading); font-size: clamp(2rem, 4vw, 3rem); color: white; font-weight: 500; margin-bottom: 1rem; line-height: 1.2;">Placas y reconocimientos personalizados</h2>
                                <p style="font-size: 1.05rem; color: rgba(255,255,255,0.9); line-height: 1.6; margin-bottom: 2.2rem;">Piezas con acabado elegante para eventos, empresas e instituciones.</p>
                                <a href="#productos" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600;">Solicitar este estilo</a>
                            </div>
                        </div>

                    </div>
                    
                    <!-- Indicadores estilo barra fina de progreso en la parte inferior derecha -->
                    <div style="position: absolute; bottom: 2rem; right: 3rem; z-index: 10; display: flex; gap: 8px;">
                        <button class="hero-dot active" data-slide-to="0" aria-label="Slide 1" style="width: 32px; height: 3px; border-radius: 2px; border: none; background: var(--primary); cursor: pointer; transition: background 0.3s ease, width 0.3s ease; padding:0;"></button>
                        <button class="hero-dot" data-slide-to="1" aria-label="Slide 2" style="width: 32px; height: 3px; border-radius: 2px; border: none; background: rgba(255,255,255,0.3); cursor: pointer; transition: background 0.3s ease, width 0.3s ease; padding:0;"></button>
                        <button class="hero-dot" data-slide-to="2" aria-label="Slide 3" style="width: 32px; height: 3px; border-radius: 2px; border: none; background: rgba(255,255,255,0.3); cursor: pointer; transition: background 0.3s ease, width 0.3s ease; padding:0;"></button>
                        <button class="hero-dot" data-slide-to="3" aria-label="Slide 4" style="width: 32px; height: 3px; border-radius: 2px; border: none; background: rgba(255,255,255,0.3); cursor: pointer; transition: background 0.3s ease, width 0.3s ease; padding:0;"></button>
                        <button class="hero-dot" data-slide-to="4" aria-label="Slide 5" style="width: 32px; height: 3px; border-radius: 2px; border: none; background: rgba(255,255,255,0.3); cursor: pointer; transition: background 0.3s ease, width 0.3s ease; padding:0;"></button>
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
                    <span>Vista previa antes de personalizar</span>
                </div>
                <div class="satisfaction-item">
                    <svg class="satisfaction-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    <span>Materiales seleccionados</span>
                </div>
            </div>
        </section>

        <!-- 2. Sección: Productos Destacados (Showroom Curado) -->
        <section id="productos" class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle">Showroom Corporativo</span>
                <h2>Productos destacados</h2>
                <p>Una selección visual de productos listos para personalizar con tu marca.</p>
            </div>
            
            <div class="grid-3">
                
                <!-- Producto 1: Termos grabados -->
                <div class="product-card catalog-product-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease, border-color 0.25s ease;">
                    <div class="product-card-image-wrap" style="position: relative; overflow: hidden; aspect-ratio: 1.15; background: var(--surface-light); border-bottom: 1px solid var(--border);">
                        <img src="uploads/termo.png" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" alt="Termos grabados">
                        <div style="display: none; align-items: center; justify-content: center; height: 100%; color: var(--text-muted);">
                            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            </svg>
                        </div>
                    </div>
                    <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 0.5rem;">Termos grabados</h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">Acero inoxidable · Grabado láser</p>
                        <div style="display: flex; gap: 8px; margin-top: auto;">
                            <button class="btn btn-primary btn-add-to-quote" data-slug="termos-grabados" data-name="Termos grabados" data-price="2.50" style="flex-grow: 1; padding: 10px 14px; font-size: 0.8rem; font-weight: 600; border: none; cursor: pointer;">
                                Cotizar este producto
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Producto 2: Agendas personalizadas -->
                <div class="product-card catalog-product-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease, border-color 0.25s ease;">
                    <div class="product-card-image-wrap" style="position: relative; overflow: hidden; aspect-ratio: 1.15; background: var(--surface-light); border-bottom: 1px solid var(--border);">
                        <img src="uploads/agenda.png" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" alt="Agendas personalizadas">
                        <div style="display: none; align-items: center; justify-content: center; height: 100%; color: var(--text-muted);">
                            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            </svg>
                        </div>
                    </div>
                    <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 0.5rem;">Agendas personalizadas</h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">Cuero/PU · Grabado o bajo relieve</p>
                        <div style="display: flex; gap: 8px; margin-top: auto;">
                            <button class="btn btn-primary btn-add-to-quote" data-slug="agendas-personalizadas" data-name="Agendas personalizadas" data-price="2.50" style="flex-grow: 1; padding: 10px 14px; font-size: 0.8rem; font-weight: 600; border: none; cursor: pointer;">
                                Quiero algo similar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Producto 3: Llaveros corporativos -->
                <div class="product-card catalog-product-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease, border-color 0.25s ease;">
                    <div class="product-card-image-wrap" style="position: relative; overflow: hidden; aspect-ratio: 1.15; background: var(--surface-light); border-bottom: 1px solid var(--border);">
                        <img src="uploads/llavero.png" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" alt="Llaveros corporativos">
                        <div style="display: none; align-items: center; justify-content: center; height: 100%; color: var(--text-muted);">
                            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            </svg>
                        </div>
                    </div>
                    <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 0.5rem;">Llaveros corporativos</h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">Metal o acrílico · Marcaje personalizado</p>
                        <div style="display: flex; gap: 8px; margin-top: auto;">
                            <button class="btn btn-primary btn-add-to-quote" data-slug="llaveros-corporativos" data-name="Llaveros corporativos" data-price="2.50" style="flex-grow: 1; padding: 10px 14px; font-size: 0.8rem; font-weight: 600; border: none; cursor: pointer;">
                                Solicitar este estilo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Producto 4: Placas corporativas -->
                <div class="product-card catalog-product-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease, border-color 0.25s ease;">
                    <div class="product-card-image-wrap" style="position: relative; overflow: hidden; aspect-ratio: 1.15; background: var(--surface-light); border-bottom: 1px solid var(--border);">
                        <img src="uploads/placa.png" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" alt="Placas corporativas">
                        <div style="display: none; align-items: center; justify-content: center; height: 100%; color: var(--text-muted);">
                            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            </svg>
                        </div>
                    </div>
                    <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 0.5rem;">Placas corporativas</h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">Acrílico, metal o madera · Personalización corporativa</p>
                        <div style="display: flex; gap: 8px; margin-top: auto;">
                            <button class="btn btn-primary btn-add-to-quote" data-slug="placas-corporativas" data-name="Placas corporativas" data-price="2.50" style="flex-grow: 1; padding: 10px 14px; font-size: 0.8rem; font-weight: 600; border: none; cursor: pointer;">
                                Cotizar una idea
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Producto 5: Cajas personalizadas -->
                <div class="product-card catalog-product-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease, border-color 0.25s ease;">
                    <div class="product-card-image-wrap" style="position: relative; overflow: hidden; aspect-ratio: 1.15; background: var(--surface-light); border-bottom: 1px solid var(--border);">
                        <img src="uploads/caja.png" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" alt="Cajas personalizadas">
                        <div style="display: none; align-items: center; justify-content: center; height: 100%; color: var(--text-muted);">
                            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            </svg>
                        </div>
                    </div>
                    <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 0.5rem;">Cajas personalizadas</h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">Packaging corporativo · Acabado de marca</p>
                        <div style="display: flex; gap: 8px; margin-top: auto;">
                            <button class="btn btn-primary btn-add-to-quote" data-slug="cajas-personalizadas" data-name="Cajas personalizadas" data-price="2.50" style="flex-grow: 1; padding: 10px 14px; font-size: 0.8rem; font-weight: 600; border: none; cursor: pointer;">
                                Ver opciones
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Producto 6: Kits empresariales -->
                <div class="product-card catalog-product-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease, border-color 0.25s ease;">
                    <div class="product-card-image-wrap" style="position: relative; overflow: hidden; aspect-ratio: 1.15; background: var(--surface-light); border-bottom: 1px solid var(--border);">
                        <img src="uploads/kit.png" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" alt="Kits empresariales">
                        <div style="display: none; align-items: center; justify-content: center; height: 100%; color: var(--text-muted);">
                            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            </svg>
                        </div>
                    </div>
                    <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 0.5rem;">Kits empresariales</h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">Piezas personalizadas para clientes, equipos y eventos</p>
                        <div style="display: flex; gap: 8px; margin-top: auto;">
                            <button class="btn btn-primary btn-add-to-quote" data-slug="kits-empresariales" data-name="Kits empresariales" data-price="2.50" style="flex-grow: 1; padding: 10px 14px; font-size: 0.8rem; font-weight: 600; border: none; cursor: pointer;">
                                Armar un kit
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Producto 7: Carnets PVC -->
                <div class="product-card catalog-product-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease, border-color 0.25s ease;">
                    <div class="product-card-image-wrap" style="position: relative; overflow: hidden; aspect-ratio: 1.15; background: var(--surface-light); border-bottom: 1px solid var(--border);">
                        <img src="uploads/carnets.png" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" alt="Carnets PVC">
                        <div style="display: none; align-items: center; justify-content: center; height: 100%; color: var(--text-muted);">
                            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            </svg>
                        </div>
                    </div>
                    <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 0.5rem;">Carnets PVC</h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">Identificación corporativa · Diseño personalizado</p>
                        <div style="display: flex; gap: 8px; margin-top: auto;">
                            <button class="btn btn-primary btn-add-to-quote" data-slug="carnets-pvc" data-name="Carnets PVC" data-price="2.50" style="flex-grow: 1; padding: 10px 14px; font-size: 0.8rem; font-weight: 600; border: none; cursor: pointer;">
                                Explorar opciones
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Producto 8: Esferos grabados -->
                <div class="product-card catalog-product-item" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease, border-color 0.25s ease;">
                    <div class="product-card-image-wrap" style="position: relative; overflow: hidden; aspect-ratio: 1.15; background: var(--surface-light); border-bottom: 1px solid var(--border);">
                        <img src="uploads/esfero.png" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" alt="Esferos grabados">
                        <div style="display: none; align-items: center; justify-content: center; height: 100%; color: var(--text-muted);">
                            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            </svg>
                        </div>
                    </div>
                    <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 0.5rem;">Esferos grabados</h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">Marcaje láser · Detalle corporativo</p>
                        <div style="display: flex; gap: 8px; margin-top: auto;">
                            <button class="btn btn-primary btn-add-to-quote" data-slug="esferos-grabados" data-name="Esferos grabados" data-price="2.50" style="flex-grow: 1; padding: 10px 14px; font-size: 0.8rem; font-weight: 600; border: none; cursor: pointer;">
                                Cotizar este producto
                            </button>
                        </div>
                    </div>
                </div>

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
                    <span class="section-subtitle" style="color: #8CFF32; border-color: #8CFF32;">Marcaje preciso</span>
                    <h2 style="color: white; font-family: var(--font-heading); font-size: 2.2rem; font-weight: 500; margin-bottom: 1.25rem;">Nuestra especialidad: grabado láser</h2>
                    <p style="color: rgba(255,255,255,0.85); font-size: 1.05rem; line-height: 1.6; margin-bottom: 2rem;">
                        Grabamos sobre metal, madera, acrílico, cuero y otros materiales para lograr piezas limpias, elegantes y resistentes al uso diario.
                    </p>
                    <ul style="color: rgba(255,255,255,0.9); margin-bottom: 2rem; padding-left: 20px; line-height: 1.8; font-size: 0.95rem;">
                        <li>✓ Acabado limpio</li>
                        <li>✓ Marcaje duradero</li>
                        <li>✓ Ideal para detalles finos</li>
                        <li>✓ Excelente para regalos corporativos</li>
                        <li>✓ Funciona en distintos materiales</li>
                    </ul>
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
                <p>Mira cómo un producto simple se convierte en una pieza personalizada para tu marca.</p>
            </div>
            
            <div class="comparison-grid">
                
                <!-- Comparación 1: Termo -->
                <div class="comparison-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; background: white; transition: transform 0.25s ease;">
                    <div class="comparison-views" style="display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid var(--border);">
                        <div class="comparison-view" style="padding: 2.5rem 1rem; text-align: center; background: var(--surface-light);">
                            <div class="comparison-label" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 4px;">Termo Liso</div>
                            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Sin marca</span>
                        </div>
                        <div class="comparison-view after" style="padding: 2.5rem 1rem; text-align: center; background: white; border-left: 1px solid var(--border);">
                            <div class="comparison-label after" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 4px;">Con Marcado</div>
                            <span style="font-size: 0.85rem; font-weight: 600; color: var(--primary);">Grabado láser</span>
                        </div>
                    </div>
                    <div class="comparison-desc" style="padding: 1.25rem; text-align: center; font-family: var(--font-heading); font-size: 0.95rem; font-weight: 500; color: var(--dark);">
                        Termo grabado
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; font-family: var(--font-body);">De producto simple a detalle corporativo listo para entregar.</p>
                        <a href="productos.php" class="btn btn-secondary" style="margin-top: 10px; font-size: 0.75rem; padding: 6px 12px; display: inline-block;">Quiero algo similar</a>
                    </div>
                </div>

                <!-- Comparación 2: Agenda -->
                <div class="comparison-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; background: white; transition: transform 0.25s ease;">
                    <div class="comparison-views" style="display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid var(--border);">
                        <div class="comparison-view" style="padding: 2.5rem 1rem; text-align: center; background: var(--surface-light);">
                            <div class="comparison-label" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 4px;">Agenda Lisa</div>
                            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Sin marca</span>
                        </div>
                        <div class="comparison-view after" style="padding: 2.5rem 1rem; text-align: center; background: white; border-left: 1px solid var(--border);">
                            <div class="comparison-label after" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 4px;">Con Marcado</div>
                            <span style="font-size: 0.85rem; font-weight: 600; color: var(--primary);">Bajo relieve</span>
                        </div>
                    </div>
                    <div class="comparison-desc" style="padding: 1.25rem; text-align: center; font-family: var(--font-heading); font-size: 0.95rem; font-weight: 500; color: var(--dark);">
                        Agenda personalizada
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; font-family: var(--font-body);">De producto simple a detalle corporativo listo para entregar.</p>
                        <a href="productos.php" class="btn btn-secondary" style="margin-top: 10px; font-size: 0.75rem; padding: 6px 12px; display: inline-block;">Quiero algo similar</a>
                    </div>
                </div>

                <!-- Comparación 3: Caja -->
                <div class="comparison-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; background: white; transition: transform 0.25s ease;">
                    <div class="comparison-views" style="display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid var(--border);">
                        <div class="comparison-view" style="padding: 2.5rem 1rem; text-align: center; background: var(--surface-light);">
                            <div class="comparison-label" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 4px;">Caja Simple</div>
                            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Sin marca</span>
                        </div>
                        <div class="comparison-view after" style="padding: 2.5rem 1rem; text-align: center; background: white; border-left: 1px solid var(--border);">
                            <div class="comparison-label after" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 4px;">Con Marcado</div>
                            <span style="font-size: 0.85rem; font-weight: 600; color: var(--primary);">Grabado láser</span>
                        </div>
                    </div>
                    <div class="comparison-desc" style="padding: 1.25rem; text-align: center; font-family: var(--font-heading); font-size: 0.95rem; font-weight: 500; color: var(--dark);">
                        Caja corporativa
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; font-family: var(--font-body);">De producto simple a detalle corporativo listo para entregar.</p>
                        <a href="productos.php" class="btn btn-secondary" style="margin-top: 10px; font-size: 0.75rem; padding: 6px 12px; display: inline-block;">Quiero algo similar</a>
                    </div>
                </div>

            </div>
        </section>

        <!-- 6. Sección: Materiales que Trabajamos (Visual) -->
        <section id="materiales" class="section-padding section-bg-light reveal-on-scroll">
            <div class="container">
                <div class="section-header center" style="margin-bottom: 3.5rem;">
                    <h2>Materiales que trabajamos</h2>
                    <p>Cada material requiere un acabado distinto. Elegimos la técnica según el producto y el resultado que buscas.</p>
                </div>
                
                <div class="materials-grid">
                    <div class="material-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; background: white; text-align: center; transition: border-color 0.25s ease;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 0.5rem;">Acero Inoxidable</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Ideal para termos, botellas y piezas de uso diario.</p>
                    </div>
                    <div class="material-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; background: white; text-align: center; transition: border-color 0.25s ease;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 0.5rem;">Madera</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Perfecta para cajas, reconocimientos y detalles corporativos.</p>
                    </div>
                    <div class="material-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; background: white; text-align: center; transition: border-color 0.25s ease;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 0.5rem;">Acrílico</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Limpio y moderno para placas, señalética y reconocimientos.</p>
                    </div>
                    <div class="material-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; background: white; text-align: center; transition: border-color 0.25s ease;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 0.5rem;">Cuero / PU</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Ideal para agendas, libretas y detalles ejecutivos.</p>
                    </div>
                    <div class="material-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; background: white; text-align: center; transition: border-color 0.25s ease;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 0.5rem;">Vidrio</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Grabado translúcido y elegante para copas y botellas.</p>
                    </div>
                    <div class="material-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; background: white; text-align: center; transition: border-color 0.25s ease;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 0.5rem;">Metal</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Excelente para esferos, llaveros y placas técnicas.</p>
                    </div>
                    <div class="material-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; background: white; text-align: center; transition: border-color 0.25s ease;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 0.5rem;">PVC</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Soporte plástico resistente para identificación corporativa.</p>
                    </div>
                    <div class="material-card" style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; background: white; text-align: center; transition: border-color 0.25s ease;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 0.5rem;">Cartón rígido / packaging</h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Soluciones estructuradas para empaques y kits premium.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección de Logos de Clientes (Smooth Ticker) -->
        <section class="clients-ticker-section" style="padding: 4rem 0; background: var(--surface-light); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); overflow: hidden;">
            <div class="container" style="text-align: center; margin-bottom: 2rem;">
                <span class="section-subtitle" style="font-size: 0.72rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; letter-spacing: 0.08em; display: inline-block; border-left: 3px solid var(--primary); padding-left: 10px;">Marcas que confían en nosotros</span>
            </div>
            <div class="ticker-wrapper" style="display: flex; overflow: hidden; position: relative; width: 100%;">
                <div class="ticker-track">
                    <?php 
                    $tickerItems = array_merge($clients, $clients); // duplicar para scroll infinito
                    if (empty($tickerItems)) {
                        for ($i = 1; $i <= 10; $i++) {
                            echo '<div style="font-family: var(--font-heading); font-size: 1.2rem; font-weight: 600; color: rgba(30, 34, 28, 0.35); letter-spacing: 0.08em; display: flex; align-items: center; min-width: 160px; justify-content: center; height: 60px;">MARCA ' . (($i - 1) % 5 + 1) . '</div>';
                        }
                    } else {
                        foreach ($tickerItems as $c):
                        ?>
                            <div class="ticker-item" style="display: flex; align-items: center; justify-content: center; height: 60px; min-width: 160px;">
                                <img src="<?php echo htmlspecialchars($c['logo_path']); ?>" alt="<?php echo htmlspecialchars($c['name']); ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            </div>
                        <?php 
                        endforeach; 
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- 7. Sección: Proceso Corto (Cómo hacemos tu pedido) -->
        <section id="proceso" class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <h2>Cómo hacemos tu pedido</h2>
                <p>Nuestra metodología simplificada para garantizar que cada pedido cumpla tus expectativas.</p>
            </div>
            
            <div class="process-grid">
                <div class="process-step" style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-md);">
                    <div class="process-number" style="color: var(--primary); font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">01</div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">Eliges el producto</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Seleccionas el artículo o nos cuentas qué necesitas.</p>
                </div>
                <div class="process-step" style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-md);">
                    <div class="process-number" style="color: var(--primary); font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">02</div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">Nos envías tu logo o idea</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Revisamos el diseño y el tipo de acabado ideal.</p>
                </div>
                <div class="process-step" style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-md);">
                    <div class="process-number" style="color: var(--primary); font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">03</div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">Preparamos una vista previa</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Te mostramos cómo quedaría antes de personalizar.</p>
                </div>
                <div class="process-step" style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-md);">
                    <div class="process-number" style="color: var(--primary); font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">04</div>
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 500; margin-bottom: 0.5rem;">Personalizamos y entregamos</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Grabamos, preparamos y entregamos tus piezas.</p>
                </div>
            </div>
        </section>

        <!-- 8. Sección FAQ Premium -->
        <section id="preguntas-frecuentes" class="section-padding section-bg-light reveal-on-scroll">
            <div class="container" style="max-width: 800px;">
                <div class="section-header center">
                    <span class="section-subtitle">Dudas Comunes</span>
                    <h2>Antes de cotizar, esto te puede ayudar</h2>
                    <p>Respuestas técnicas sobre diseño, personalización B2B y logística.</p>
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
                    <p class="footer-description" style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-top: 1rem;">
                        Identificación, grabado láser y personalización corporativa para empresas, instituciones y eventos.
                    </p>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading" style="font-size: 0.9rem; font-family: var(--font-heading); margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dark);">Productos</h3>
                    <nav class="footer-links" aria-label="Enlaces de productos" style="display: flex; flex-direction: column; gap: 8px; font-size: 0.85rem;">
                        <a href="productos.php" class="footer-link">Grabado láser</a>
                        <a href="productos.php" class="footer-link">Identificación corporativa</a>
                        <a href="productos.php" class="footer-link">Kits empresariales</a>
                        <a href="productos.php" class="footer-link">Placas y reconocimientos</a>
                        <a href="productos.php" class="footer-link">Productos personalizados</a>
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
                <div class="footer-links-column">
                    <h3 class="footer-heading" style="font-size: 0.9rem; font-family: var(--font-heading); margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dark);">Ubicación</h3>
                    <div style="margin-bottom: 1rem;">
                        <iframe src="https://www.google.com/maps?ll=-0.165355,-78.483023&z=15&t=m&hl=es&gl=EC&mapclient=embed&cid=13164539704964091228&output=embed" width="100%" height="150" style="border:0; border-radius: 6px;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                    <p style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                        WhatsApp: +593 00 000 0000<br>
                        Correo: correo@cardnet.ec<br>
                        Ubicación: Ecuador
                    </p>
                </div>
            </div>
        </div>
        <div class="footer-bottom" style="border-top: 1px solid var(--border); padding-top: 1.5rem; padding-bottom: 1.5rem;">
            <div class="container footer-bottom-flex" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <p style="font-size: 0.8rem; color: var(--text-muted);">&copy; 2026 CardNet.ec — Detalles personalizados para marcas que cuidan su presentación.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts Modulares -->
    <script src="js/main.js"></script>
    <script src="js/slider.js"></script>
    <script src="js/animations.js"></script>
</body>
</html>
