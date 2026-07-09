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
// Obtener productos destacados de la base de datos
try {
    $stmtFeatured = $pdo->query("SELECT p.*, c.slug as cat_slug FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE p.is_active = 1 AND p.is_featured = 1 ORDER BY p.order_val ASC");
    $featured_products = $stmtFeatured->fetchAll();
} catch (PDOException $e) {
    $featured_products = [];
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
      "email": "correo@cardnet.ec",
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

    <?php include 'includes/header.php'; ?>

    <!-- MAIN CONTENT -->
    <main>
        
        <!-- 1. Hero Principal - Carrusel Automático Showroom -->
        <section class="hero-block reveal-on-scroll" id="inicio" style="padding-top: 1rem; padding-bottom: 2rem;">
            <div class="container" style="position: relative;">
                <div class="hero-right-carousel" style="width: 100%; min-height: 520px; height: 70vh; position: relative; border-radius: var(--radius-md); overflow: hidden; background: #0f110e; border: 1px solid rgba(0,0,0,0.03); display: flex; flex-direction: column;">
                    <div class="hero-slider-track" style="width: 100%; height: 100%; position: relative; flex-grow: 1;">
                        
                        <!-- Slide 1: Termos -->
                        <div class="hero-slide-item active" data-slide-index="0" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: 1; visibility: visible; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: 5; padding: 4rem; background-color: #0f110e;">
                            <!-- Imagen de producto con difuminado suave premium (máscara de opacidad) -->
                            <div style="position: absolute; right: 0; top: 0; bottom: 0; width: 55%; z-index: 1; -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,1) 40%, rgba(0,0,0,0) 100%); mask-image: linear-gradient(to left, rgba(0,0,0,1) 40%, rgba(0,0,0,0) 100%); pointer-events: none;">
                                <img src="uploads/termo.png" alt="Termo" style="width: 100%; height: 100%; object-fit: contain; object-position: right center; filter: drop-shadow(-10px 10px 25px rgba(0,0,0,0.2));">
                            </div>
                            <div style="position: absolute; inset: 0; background: linear-gradient(to right, #0f110e 0%, rgba(15, 17, 14, 0.8) 40%, transparent 100%); z-index: 2; pointer-events: none;"></div>
                            
                            <div style="position: relative; z-index: 3; max-width: 480px; color: white;">
                                <h2 style="font-family: var(--font-heading); font-size: clamp(1.8rem, 3.5vw, 2.6rem); color: white; font-weight: 500; margin-bottom: 1rem; line-height: 1.2;">Termos grabados en acero inoxidable</h2>
                                <p style="font-size: 1.05rem; color: rgba(255,255,255,0.85); line-height: 1.6; margin-bottom: 2.2rem;">Acabado láser limpio, sobrio y resistente al uso diario.</p>
                                <a href="#productos" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600;">Cotizar este producto</a>
                            </div>
                        </div>

                        <!-- Slide 2: Agendas -->
                        <div class="hero-slide-item" data-slide-index="1" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: 1; padding: 4rem; background-color: #0f110e;">
                            <!-- Imagen de producto con difuminado suave premium (máscara de opacidad) -->
                            <div style="position: absolute; right: 0; top: 0; bottom: 0; width: 55%; z-index: 1; -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,1) 40%, rgba(0,0,0,0) 100%); mask-image: linear-gradient(to left, rgba(0,0,0,1) 40%, rgba(0,0,0,0) 100%); pointer-events: none;">
                                <img src="uploads/agenda.png" alt="Agenda" style="width: 100%; height: 100%; object-fit: contain; object-position: right center; filter: drop-shadow(-10px 10px 25px rgba(0,0,0,0.2));">
                            </div>
                            <div style="position: absolute; inset: 0; background: linear-gradient(to right, #0f110e 0%, rgba(15, 17, 14, 0.8) 40%, transparent 100%); z-index: 2; pointer-events: none;"></div>
                            
                            <div style="position: relative; z-index: 3; max-width: 480px; color: white;">
                                <h2 style="font-family: var(--font-heading); font-size: clamp(1.8rem, 3.5vw, 2.6rem); color: white; font-weight: 500; margin-bottom: 1rem; line-height: 1.2;">Agendas corporativas personalizadas</h2>
                                <p style="font-size: 1.05rem; color: rgba(255,255,255,0.85); line-height: 1.6; margin-bottom: 2.2rem;">Detalles elegantes para clientes, equipos y eventos.</p>
                                <a href="#productos" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600;">Quiero algo similar</a>
                            </div>
                        </div>

                        <!-- Slide 3: Kits -->
                        <div class="hero-slide-item" data-slide-index="2" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: 1; padding: 4rem; background-color: #0f110e;">
                            <!-- Imagen de producto con difuminado suave premium (máscara de opacidad) -->
                            <div style="position: absolute; right: 0; top: 0; bottom: 0; width: 55%; z-index: 1; -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,1) 40%, rgba(0,0,0,0) 100%); mask-image: linear-gradient(to left, rgba(0,0,0,1) 40%, rgba(0,0,0,0) 100%); pointer-events: none;">
                                <img src="uploads/kit.png" alt="Kit" style="width: 100%; height: 100%; object-fit: contain; object-position: right center; filter: drop-shadow(-10px 10px 25px rgba(0,0,0,0.2));">
                            </div>
                            <div style="position: absolute; inset: 0; background: linear-gradient(to right, #0f110e 0%, rgba(15, 17, 14, 0.8) 40%, transparent 100%); z-index: 2; pointer-events: none;"></div>
                            
                            <div style="position: relative; z-index: 3; max-width: 480px; color: white;">
                                <h2 style="font-family: var(--font-heading); font-size: clamp(1.8rem, 3.5vw, 2.6rem); color: white; font-weight: 500; margin-bottom: 1rem; line-height: 1.2;">Kits corporativos listos para entregar</h2>
                                <p style="font-size: 1.05rem; color: rgba(255,255,255,0.85); line-height: 1.6; margin-bottom: 2.2rem;">Termos, agendas y cajas personalizadas para empresas y eventos.</p>
                                <a href="cotizacion.php" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600;">Cotizar una idea</a>
                            </div>
                        </div>

                        <!-- Slide 4: Carnets -->
                        <div class="hero-slide-item" data-slide-index="3" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: 1; padding: 4rem; background-color: #0f110e;">
                            <!-- Imagen de producto con difuminado suave premium (máscara de opacidad) -->
                            <div style="position: absolute; right: 0; top: 0; bottom: 0; width: 55%; z-index: 1; -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,1) 40%, rgba(0,0,0,0) 100%); mask-image: linear-gradient(to left, rgba(0,0,0,1) 40%, rgba(0,0,0,0) 100%); pointer-events: none;">
                                <img src="uploads/carnets.png" alt="Carnets" style="width: 100%; height: 100%; object-fit: contain; object-position: right center; filter: drop-shadow(-10px 10px 25px rgba(0,0,0,0.2));">
                            </div>
                            <div style="position: absolute; inset: 0; background: linear-gradient(to right, #0f110e 0%, rgba(15, 17, 14, 0.8) 40%, transparent 100%); z-index: 2; pointer-events: none;"></div>
                            
                            <div style="position: relative; z-index: 3; max-width: 480px; color: white;">
                                <h2 style="font-family: var(--font-heading); font-size: clamp(1.8rem, 3.5vw, 2.6rem); color: white; font-weight: 500; margin-bottom: 1rem; line-height: 1.2;">Carnets y credenciales profesionales</h2>
                                <p style="font-size: 1.05rem; color: rgba(255,255,255,0.85); line-height: 1.6; margin-bottom: 2.2rem;">Identificación corporativa con diseño limpio y presentación cuidada.</p>
                                <a href="productos.php" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600;">Explorar opciones</a>
                            </div>
                        </div>

                        <!-- Slide 5: Placas -->
                        <div class="hero-slide-item" data-slide-index="4" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out; z-index: 1; padding: 4rem; background-color: #0f110e;">
                            <!-- Imagen de producto con difuminado suave premium (máscara de opacidad) -->
                            <div style="position: absolute; right: 0; top: 0; bottom: 0; width: 55%; z-index: 1; -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,1) 40%, rgba(0,0,0,0) 100%); mask-image: linear-gradient(to left, rgba(0,0,0,1) 40%, rgba(0,0,0,0) 100%); pointer-events: none;">
                                <img src="uploads/placa.png" alt="Placa" style="width: 100%; height: 100%; object-fit: contain; object-position: right center; filter: drop-shadow(-10px 10px 25px rgba(0,0,0,0.2));">
                            </div>
                            <div style="position: absolute; inset: 0; background: linear-gradient(to right, #0f110e 0%, rgba(15, 17, 14, 0.8) 40%, transparent 100%); z-index: 2; pointer-events: none;"></div>
                            
                            <div style="position: relative; z-index: 3; max-width: 480px; color: white;">
                                <h2 style="font-family: var(--font-heading); font-size: clamp(1.8rem, 3.5vw, 2.6rem); color: white; font-weight: 500; margin-bottom: 1rem; line-height: 1.2;">Placas y reconocimientos personalizados</h2>
                                <p style="font-size: 1.05rem; color: rgba(255,255,255,0.85); line-height: 1.6; margin-bottom: 2.2rem;">Piezas con acabado elegante para eventos, empresas e instituciones.</p>
                                <a href="#productos" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600;">Solicitar este estilo</a>
                            </div>
                        </div>

                    </div>
                    
                    <!-- Indicadores estilo barra fina de progreso en la parte inferior derecha -->
                    <div style="position: absolute; bottom: 2rem; right: 3rem; z-index: 10; display: flex; gap: 8px;">
                        <button class="hero-dot active" data-slide-to="0" aria-label="Slide 1" style="width: 32px; height: 3px; border-radius: 2px; border: none; background: var(--primary); cursor: pointer; transition: background 0.3s ease, width 0.3s ease; padding:0;"></button>
                        <button class="hero-dot" data-slide-to="1" aria-label="Slide 2" style="width: 32px; height: 3px; border-radius: 2px; border: none; background: rgba(255, 255, 255, 0.3); cursor: pointer; transition: background 0.3s ease, width 0.3s ease; padding:0;"></button>
                        <button class="hero-dot" data-slide-to="2" aria-label="Slide 3" style="width: 32px; height: 3px; border-radius: 2px; border: none; background: rgba(255, 255, 255, 0.3); cursor: pointer; transition: background 0.3s ease, width 0.3s ease; padding:0;"></button>
                        <button class="hero-dot" data-slide-to="3" aria-label="Slide 4" style="width: 32px; height: 3px; border-radius: 2px; border: none; background: rgba(255, 255, 255, 0.3); cursor: pointer; transition: background 0.3s ease, width 0.3s ease; padding:0;"></button>
                        <button class="hero-dot" data-slide-to="4" aria-label="Slide 5" style="width: 32px; height: 3px; border-radius: 2px; border: none; background: rgba(255, 255, 255, 0.3); cursor: pointer; transition: background 0.3s ease, width 0.3s ease; padding:0;"></button>
                    </div>
                </div>
            </div>
            
            <style>
                @media (max-width: 768px) {
                    .hero-slide-item {
                        padding: 2.5rem 2rem !important;
                    }
                    .hero-slide-item > div[style*="width: 55%"] {
                        width: 100% !important;
                        height: 50% !important;
                        top: auto !important;
                        bottom: 0 !important;
                        -webkit-mask-image: linear-gradient(to top, rgba(0,0,0,1) 50%, rgba(0,0,0,0) 100%) !important;
                        mask-image: linear-gradient(to top, rgba(0,0,0,1) 50%, rgba(0,0,0,0) 100%) !important;
                    }
                    .hero-slide-item img {
                        object-position: center bottom !important;
                    }
                    .hero-slide-item div[style*="background: linear-gradient"] {
                        background: linear-gradient(to bottom, #0f110e 0%, rgba(15, 17, 14, 0.95) 50%, transparent 100%) !important;
                    }
                    .hero-slide-item div[style*="position: relative"] {
                        max-width: 100% !important;
                        text-align: center !important;
                        margin-bottom: 12rem !important;
                    }
                }
            </style>
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
                <?php if (!empty($featured_products)): ?>
                    <?php foreach ($featured_products as $prod): ?>
                        <?php 
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
                        <div class="product-card catalog-product-item" 
                             data-name="<?php echo htmlspecialchars($enriched['name']); ?>"
                             data-gallery='<?php echo htmlspecialchars($prod_gallery_json, ENT_QUOTES, 'UTF-8'); ?>' 
                             style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease, border-color 0.25s ease;">
                            
                            <a href="producto.php?slug=<?php echo htmlspecialchars($prod['slug']); ?>" style="text-decoration: none; color: inherit; display: block; flex-grow: 1;">
                                <div class="product-card-image-wrap" style="position: relative; overflow: hidden; aspect-ratio: 1.15; background: var(--surface-light); border-bottom: 1px solid var(--border);">
                                    <?php if ($prod['image_main']): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($prod['image_main']); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" alt="<?php echo htmlspecialchars($prod['name']); ?>">
                                    <?php endif; ?>
                                    <div style="display: none; align-items: center; justify-content: center; height: 100%; color: var(--text-muted);">
                                        <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.2">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        </svg>
                                    </div>
                                </div>
                                <div style="padding: 1.5rem; display: flex; flex-direction: column;">
                                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($prod['name']); ?></h3>
                                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1rem;"><?php echo htmlspecialchars($enriched['material'] . ' · ' . $enriched['technique']); ?></p>
                                </div>
                            </a>
                            <div style="padding: 0 1.5rem 1.5rem 1.5rem; display: flex; gap: 8px; margin-top: auto;">
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
                                        style="flex-grow: 1; padding: 10px 14px; font-size: 0.8rem; font-weight: 600; border: none; cursor: pointer;">
                                    <?php echo $btn_text; ?>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 3rem 0;">
                        No hay productos destacados disponibles en este momento.
                    </div>
                <?php endif; ?>
            </div>
            
            <div style="text-align: center; margin-top: 3.5rem;">
                <a href="productos.php" class="btn btn-secondary" style="padding: 12px 28px; border: 1px solid var(--border); background: white;">Ver catálogo completo</a>
            </div>
        </section>
        
        <!-- 3. Sección: Categorías Visuales -->
        <section id="categorias-visuales" class="section-padding" style="background: #121212; color: white; padding-top: 5rem; padding-bottom: 5rem;">
            <div class="container">
                <div class="section-header" style="margin-bottom: 3.5rem; text-align: left; max-width: 700px;">
                    <h2 style="font-family: var(--font-heading); font-size: 3rem; color: white; font-weight: 400; margin-bottom: 1rem; ">Categorías destacadas</h2>
                    <p style="color: rgba(255,255,255,0.75); font-size: 1rem; line-height: 1.6; margin: 0;">Explora las principales líneas de productos para empresas, eventos e instituciones.</p>
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
                        flex-direction: column;
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
                        <!-- Merchandising General -->
                        <a href="productos.php" class="premium-cat-card" style="aspect-ratio: 595/302;">
                            <img src="uploads/kit.png" alt="Merchandising General">
                            <div class="premium-cat-overlay">
                                <h3 class="premium-cat-title">Merchandising General</h3>
                                <p class="premium-cat-subtitle">Llaveros, esferos y más.</p>
                            </div>
                        </a>
                        
                        <div class="premium-bottom-row">
                            <!-- Cajas y Empaques -->
                            <a href="productos.php?cat=cajas" class="premium-cat-card" style="aspect-ratio: 288/337;">
                                <img src="uploads/caja.png" alt="Cajas y Empaques">
                                <div class="premium-cat-overlay">
                                    <h3 class="premium-cat-title">Cajas y Empaques</h3>
                                </div>
                            </a>
                            <!-- Especialidad Láser -->
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
                    <div class="premium-right-col">
                        <!-- Carnetización -->
                        <a href="productos.php?cat=carnets" class="premium-cat-card" style="aspect-ratio: 288/460; height: 100%;">
                            <img src="uploads/carnets.png" alt="Carnetización">
                            <div class="premium-cat-overlay" style="height: 100%;">
                                <h3 class="premium-cat-title">Carnetización</h3>
                                <p class="premium-cat-subtitle">Identificación profesional para empresas e instituciones.</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Sección: Especialidad Grabado Láser (Sintética) -->
        <section id="laser" class="section-padding container reveal-on-scroll">
            <div class="laser-section" style="padding: 4rem; background-image: url('images/laser_action.jpg'); background-size: cover; background-position: center; border-radius: var(--radius-lg); overflow: hidden; position: relative;">
                <!-- Degradado de alta opacidad a la izquierda para visibilidad óptima de los textos -->
                <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(16, 20, 15, 0.98) 0%, rgba(16, 20, 15, 0.85) 45%, rgba(16, 20, 15, 0.2) 100%); z-index: 1;"></div>
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

        <!-- 5. Sección: Antes y Después del Grabado (Visual & Interactivo) -->
        <section id="antes-despues" class="section-padding container reveal-on-scroll" style="border-top: 1px solid var(--border); padding-top: 5rem; padding-bottom: 5rem;">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle">Garantía de Acabado</span>
                <h2>Antes y después del grabado</h2>
                <p>Mira cómo un producto simple se convierte en una pieza personalizada para tu marca.</p>
            </div>
            
            <div class="interactive-slider-wrapper" style="display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 40px; align-items: center; max-width: 1000px; margin: 0 auto;">
                
                <!-- El Comparador Deslizable (Izquierda) -->
                <div class="before-after-slider-container" style="position: relative; width: 100%; aspect-ratio: 1; border-radius: var(--radius-md); overflow: hidden; user-select: none; border: 1px solid var(--border); box-shadow: var(--shadow-lg); cursor: ew-resize;">
                    <!-- Imagen del Después (Fondo) -->
                    <img id="slider-img-after" src="images/termo_after.png" alt="Termo Grabado" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none;">
                    
                    <!-- Imagen del Antes (Capa Superior recortable) -->
                    <div id="slider-before-wrap" style="position: absolute; inset: 0; width: 100%; height: 100%; overflow: hidden; pointer-events: none; clip-path: inset(0 50% 0 0); border-right: 2px solid white;">
                        <img id="slider-img-before" src="images/termo_before.png" alt="Termo Liso" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none;">
                    </div>
                    
                    <!-- Barra de Deslizamiento y Manejador -->
                    <div id="slider-handle" style="position: absolute; top: 0; bottom: 0; left: 50%; width: 40px; margin-left: -20px; display: flex; align-items: center; justify-content: center; z-index: 10; pointer-events: none;">
                        <div style="width: 2px; height: 100%; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.5);"></div>
                        <div style="position: absolute; width: 44px; height: 44px; border-radius: 50%; background: white; border: 2px solid var(--primary); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.25); font-weight: bold; color: var(--primary); font-size: 1rem;">
                            &harr;
                        </div>
                    </div>
                    
                    <!-- Indicadores de estado flotantes -->
                    <div style="position: absolute; top: 1.5rem; left: 1.5rem; background: rgba(0,0,0,0.6); color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.05em; z-index: 5;">ANTES (LISO)</div>
                    <div style="position: absolute; top: 1.5rem; right: 1.5rem; background: var(--primary); color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.05em; z-index: 5;">DESPUÉS (GRABADO)</div>
                </div>

                <!-- Control de Productos y Detalles (Derecha) -->
                <div class="slider-controls" style="display: flex; flex-direction: column; gap: 24px;">
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <h3 id="slider-title" style="font-family: var(--font-heading); font-size: 1.8rem; font-weight: 500; color: var(--dark); margin: 0;">Termo grabado</h3>
                        <p id="slider-text" style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.6; margin: 0;">De producto simple a detalle corporativo listo para entregar.</p>
                    </div>

                    <!-- Selector de pestañas -->
                    <div class="slider-tabs" style="display: flex; flex-direction: column; gap: 10px;">
                        <button class="slider-tab-btn active" data-prod="termo" style="width: 100%; text-align: left; padding: 1rem 1.25rem; background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: all 0.3s ease;">
                            <span style="font-weight: 600; font-size: 0.95rem;">Termo de Acero</span>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">Ver muestra &rarr;</span>
                        </button>
                        <button class="slider-tab-btn" data-prod="agenda" style="width: 100%; text-align: left; padding: 1rem 1.25rem; background: #fbfbfb; border: 1px solid var(--border); border-radius: var(--radius-sm); cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: all 0.3s ease;">
                            <span style="font-weight: 600; font-size: 0.95rem; color: var(--dark);">Agenda de Cuero</span>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">Ver muestra &rarr;</span>
                        </button>
                        <button class="slider-tab-btn" data-prod="caja" style="width: 100%; text-align: left; padding: 1rem 1.25rem; background: #fbfbfb; border: 1px solid var(--border); border-radius: var(--radius-sm); cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: all 0.3s ease;">
                            <span style="font-weight: 600; font-size: 0.95rem; color: var(--dark);">Caja de Madera</span>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">Ver muestra &rarr;</span>
                        </button>
                    </div>

                    <div>
                        <a href="productos.php" class="btn btn-primary" style="width: 100%; text-align: center; display: block; padding: 14px 0;">Quiero algo similar</a>
                    </div>
                </div>
                
            </div>
            
            <style>
                .slider-tab-btn.active {
                    border-color: var(--primary) !important;
                    background: rgba(99, 174, 44, 0.04) !important;
                    box-shadow: 0 4px 12px rgba(99, 174, 44, 0.08);
                }
                .slider-tab-btn.active span {
                    color: var(--primary) !important;
                }
                @media (max-width: 768px) {
                    .interactive-slider-wrapper {
                        grid-template-columns: 1fr;
                        gap: 30px;
                    }
                }
            </style>
        </section>

        <!-- 6. Sección: Materiales que Trabajamos (Visual) -->
        <section id="materiales" class="section-padding section-bg-light reveal-on-scroll" style="padding-top: 5rem; padding-bottom: 5rem; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
            <div class="container">
                <div class="section-header center" style="margin-bottom: 3.5rem;">
                    <h2>Materiales que trabajamos</h2>
                    <p>Cada material requiere un acabado distinto. Elegimos la técnica según el producto y el resultado que buscas.</p>
                </div>
                
                <div class="materials-row-visual" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 30px; justify-content: center; max-width: 1000px; margin: 0 auto; text-align: center;">
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                        <div style="width: 130px; height: 130px; border-radius: 16px; overflow: hidden; border: 1px solid rgba(0,0,0,0.08); box-shadow: 0 4px 10px rgba(0,0,0,0.02); background: white;">
                            <img src="images/mat_acero.png" alt="Acero" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.8rem; font-weight: 700; letter-spacing: 0.1em; color: #3E4A56; text-transform: uppercase;">Acero</span>
                    </div>
                    
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                        <div style="width: 130px; height: 130px; border-radius: 16px; overflow: hidden; border: 1px solid rgba(0,0,0,0.08); box-shadow: 0 4px 10px rgba(0,0,0,0.02); background: white;">
                            <img src="images/mat_madera.png" alt="Madera" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.8rem; font-weight: 700; letter-spacing: 0.1em; color: #3E4A56; text-transform: uppercase;">Madera</span>
                    </div>
                    
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                        <div style="width: 130px; height: 130px; border-radius: 16px; overflow: hidden; border: 1px solid rgba(0,0,0,0.08); box-shadow: 0 4px 10px rgba(0,0,0,0.02); background: white;">
                            <img src="images/mat_acrilico.png" alt="Acrílico" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.8rem; font-weight: 700; letter-spacing: 0.1em; color: #3E4A56; text-transform: uppercase;">Acrílico</span>
                    </div>
                    
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                        <div style="width: 130px; height: 130px; border-radius: 16px; overflow: hidden; border: 1px solid rgba(0,0,0,0.08); box-shadow: 0 4px 10px rgba(0,0,0,0.02); background: white;">
                            <img src="images/mat_cuero.png" alt="Cuero/PU" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.8rem; font-weight: 700; letter-spacing: 0.1em; color: #3E4A56; text-transform: uppercase;">Cuero/PU</span>
                    </div>
                    
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                        <div style="width: 130px; height: 130px; border-radius: 16px; overflow: hidden; border: 1px solid rgba(0,0,0,0.08); box-shadow: 0 4px 10px rgba(0,0,0,0.02); background: white;">
                            <img src="images/mat_vidrio.png" alt="Vidrio" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.8rem; font-weight: 700; letter-spacing: 0.1em; color: #3E4A56; text-transform: uppercase;">Vidrio</span>
                    </div>
                    
                    <div class="material-visual-item" style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                        <div style="width: 130px; height: 130px; border-radius: 16px; overflow: hidden; border: 1px solid rgba(0,0,0,0.08); box-shadow: 0 4px 10px rgba(0,0,0,0.02); background: white;">
                            <img src="images/mat_pvc.png" alt="PVC" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-size: 0.8rem; font-weight: 700; letter-spacing: 0.1em; color: #3E4A56; text-transform: uppercase;">PVC</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección de Logos de Clientes (Smooth Ticker) -->
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
                    <p>Lo básico que necesitas saber antes de personalizar tus productos.</p>
                </div>
                
                <div class="faq-accordion" style="margin-top: 2rem; display: flex; flex-direction: column; gap: 10px;">
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.95rem;">¿Puedo enviar mi logo?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Sí. Puedes enviarlo en buena calidad y revisamos cómo aplicarlo sobre el producto.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.95rem;">¿Puedo ver una vista previa antes de producir?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Sí. Preparamos una referencia visual antes de personalizar.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.95rem;">¿Qué productos se pueden grabar?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Trabajamos con termos, agendas, placas, llaveros, cajas, carnets y otros productos corporativos.</p>
                        </div>
                    </div>

                    <div class="faq-item" style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: white;">
                        <button class="faq-trigger" style="background: none; border: none; outline: none; width: 100%; text-align: left; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span style="font-weight: 600; font-size: 0.95rem;">¿Atienden pedidos para empresas?</span>
                            <span class="faq-icon"></span>
                        </button>
                        <div class="faq-content">
                            <p style="padding: 0 1.5rem 1.25rem 1.5rem; margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">Sí. Preparamos productos para empresas, instituciones, eventos y equipos.</p>
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
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts Modulares -->
    <script src="js/main.js?v=3.5"></script>
    <script src="js/slider.js"></script>
    <script src="js/animations.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Manejo del Slider de Antes/Después
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
            
            // Eventos del Mouse
            container.addEventListener("mousedown", function(e) {
                active = true;
                slide(e.clientX);
            });
            window.addEventListener("mouseup", function() {
                active = false;
            });
            window.addEventListener("mousemove", function(e) {
                if (!active) return;
                slide(e.clientX);
            });
            
            // Eventos Táctiles
            container.addEventListener("touchstart", function(e) {
                active = true;
                slide(e.touches[0].clientX);
            });
            window.addEventListener("touchend", function() {
                active = false;
            });
            window.addEventListener("touchmove", function(e) {
                if (!active) return;
                slide(e.touches[0].clientX);
            });
        }
        
        // 2. Manejo de Pestañas del Selector
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
                    
                    // Reiniciar slider a la mitad (50%)
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
