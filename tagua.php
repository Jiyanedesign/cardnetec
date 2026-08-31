<?php
session_start();
require_once 'db.php';

// Cargar configuraciones del sitio
$site_settings = getSiteSettings($pdo);
$page_title = 'Productos de Tagua Personalizados | Marfil Vegetal Ecuatoriano con Grabado Láser | CardNet.ec';
$page_description = 'Descubre llaveros, botones, medallas, placas y regalos corporativos en Tagua (marfil vegetal ecuatoriano 100% ecológico). Personalización de alta precisión con grabado láser.';
$tagua_wa_clean = !empty($site_settings['whatsapp']) ? preg_replace('/[^0-9]/', '', $site_settings['whatsapp']) : '593000000000';
$tagua_wa_display = !empty($site_settings['whatsapp']) ? $site_settings['whatsapp'] : '+593 00 000 0000';

// Obtener productos de Tagua desde la base de datos
try {
    $stmtTagua = $pdo->query("SELECT p.*, c.name as category_name, c.slug as cat_slug 
                              FROM productos p 
                              LEFT JOIN categorias c ON p.category_id = c.id 
                              WHERE p.is_active = 1 AND (c.slug = 'tagua' OR p.name LIKE '%tagua%' OR p.slug LIKE '%tagua%') 
                              ORDER BY CASE WHEN p.order_val IS NULL OR p.order_val = 0 THEN 999999 ELSE p.order_val END ASC, p.id ASC");
    $tagua_products = $stmtTagua->fetchAll();
} catch (PDOException $e) {
    $tagua_products = [];
}

// Fallback de catálogo si no hubiera productos cargados aún
if (empty($tagua_products)) {
    $tagua_products = [
        [
            'name' => 'Llaveros de Tagua Grabados a Láser',
            'slug' => 'llaveros-tagua-laser',
            'description_short' => 'Llavero de marfil vegetal ecuatoriano pulido y personalizado con grabado láser permanente de tu logo o marca.',
            'image_main' => 'tagua_llavero.jpg',
            'price' => 2.50,
            'cta_text' => 'Personalizar Llaveros'
        ],
        [
            'name' => 'Botones de Tagua Personalizados',
            'slug' => 'botones-tagua-personalizados',
            'description_short' => 'Botones ecológicos de marfil vegetal para uniformes corporativos, camisas y prendas de alta gama con grabado de logotipo.',
            'image_main' => 'tagua_botones.jpg',
            'price' => 0.90,
            'cta_text' => 'Cotizar Botones'
        ],
        [
            'name' => 'Dijes y Medallas de Tagua con Logo',
            'slug' => 'dijes-medallas-tagua',
            'description_short' => 'Medallas y dijes conmemorativos para eventos sostenibles, congresos, reconocimientos y recuerdos turísticos.',
            'image_main' => 'tagua_dije.jpg',
            'price' => 1.80,
            'cta_text' => 'Solicitar Muestra'
        ],
        [
            'name' => 'Porta Credenciales Ecológico con Tagua',
            'slug' => 'porta-credenciales-tagua',
            'description_short' => 'Accesorio de identificación corporativa que combina cordón textil y un elegante broche distintivo de tagua grabado.',
            'image_main' => 'tagua_portacarnet.jpg',
            'price' => 3.20,
            'cta_text' => 'Cotizar para Eventos'
        ],
        [
            'name' => 'Placa de Reconocimiento en Tagua y Madera',
            'slug' => 'placa-reconocimiento-tagua-madera',
            'description_short' => 'Placa conmemorativa premium en madera noble con apliques centrales de tagua tallada y grabada al láser.',
            'image_main' => 'tagua_placa.jpg',
            'price' => 18.00,
            'cta_text' => 'Cotizar Placa'
        ],
        [
            'name' => 'Kit Ejecutivo Ecológico con Tagua',
            'slug' => 'kit-ejecutivo-tagua',
            'description_short' => 'Caja de madera con libreta reciclada, bolígrafo de bambú y llavero de tagua grabado con el logo de tu empresa.',
            'image_main' => 'tagua_kit.jpg',
            'price' => 15.50,
            'cta_text' => 'Cotizar Kit'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <link rel="canonical" href="https://cardnet.ec/tagua.php">
    <link rel="icon" type="image/png" href="favicon.png?v=2.0">
    <link rel="apple-touch-icon" href="favicon.png?v=2.0">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://cardnet.ec/tagua.php">
    <meta property="og:image" content="https://cardnet.ec/uploads/carnet_mockup.jpg">

    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=6.1">
    <link rel="stylesheet" href="css/layout.css?v=6.1">
    <link rel="stylesheet" href="css/components.css?v=6.1">
    <link rel="stylesheet" href="css/pages.css?v=6.1">
    <link rel="stylesheet" href="css/animations.css?v=1.1.2">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* HERO TAGUA */
        .tagua-hero {
            background: linear-gradient(135deg, #091209 0%, #122114 60%, #1a2e1d 100%);
            color: white;
            padding: 5.5rem 0 4.5rem 0;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid var(--border);
        }
        .tagua-hero::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(140, 255, 50, 0.09) 0%, transparent 70%);
            pointer-events: none;
        }
        .tagua-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(140, 255, 50, 0.12);
            border: 1px solid rgba(140, 255, 50, 0.35);
            color: #8CFF32;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }
        .tagua-hero-title {
            font-family: var(--font-heading);
            font-size: clamp(2.2rem, 4.5vw, 3.4rem);
            line-height: 1.15;
            color: white;
            margin-bottom: 1.25rem;
            font-weight: 400;
        }
        .tagua-hero-subtitle {
            font-size: clamp(1rem, 2vw, 1.18rem);
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.65;
            max-width: 720px;
            margin: 0 auto 2rem auto;
        }
        .tagua-stats-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 3.5rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }
        .tagua-stat-item {
            text-align: center;
        }
        .tagua-stat-icon {
            font-size: 1.5rem;
            margin-bottom: 6px;
            display: block;
        }
        .tagua-stat-title {
            font-weight: 600;
            font-size: 0.95rem;
            color: white;
            margin-bottom: 3px;
        }
        .tagua-stat-desc {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.65);
        }

        /* BENEFICIOS CARD */
        .benefit-box {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 2rem;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
        }
        .benefit-box:hover {
            transform: translateY(-6px);
            border-color: var(--primary);
            box-shadow: 0 16px 32px rgba(99, 174, 44, 0.08);
        }
        .benefit-icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--surface-light);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            margin-bottom: 1.25rem;
        }

        /* CATÁLOGO PRODUCTOS TAGUA */
        .tagua-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }
        .tagua-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .tagua-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary);
            box-shadow: 0 16px 36px rgba(99, 174, 44, 0.1);
        }
        .tagua-card-img-wrap {
            width: 100%;
            aspect-ratio: 1.2;
            background: var(--surface-light);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-bottom: 1px solid var(--border);
            position: relative;
        }
        .tagua-card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .tagua-card:hover .tagua-card-img-wrap img {
            transform: scale(1.06);
        }
        .tagua-card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        /* PROCESO STEPS */
        .process-step-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 2rem;
            position: relative;
            transition: all 0.3s ease;
        }
        .process-step-card:hover {
            border-color: var(--primary);
            box-shadow: 0 10px 25px rgba(0,0,0,0.04);
        }
        .step-number-badge {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        /* FAQ ACCORDION */
        .faq-item {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            margin-bottom: 12px;
            overflow: hidden;
        }
        .faq-question {
            width: 100%;
            padding: 1.25rem 1.5rem;
            text-align: left;
            background: none;
            border: none;
            font-family: 'Work Sans', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .faq-question:hover {
            color: var(--primary);
        }
        .faq-answer {
            padding: 0 1.5rem 1.25rem 1.5rem;
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        @media (max-width: 992px) {
            .tagua-stats-strip {
                grid-template-columns: repeat(2, 1fr);
                gap: 24px;
            }
        }
        @media (max-width: 576px) {
            .tagua-stats-strip {
                grid-template-columns: 1fr;
            }
            .tagua-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <main>
        <!-- 1. HERO SECTION -->
        <section class="tagua-hero">
            <div class="container center" style="max-width: 900px; position: relative; z-index: 5;">
                <div class="tagua-badge">
                    <span>🌱</span> Marfil Vegetal Ecuatoriano · 100% Ecológico & Sostenible
                </div>
                <h1 class="tagua-hero-title">Productos y regalos corporativos en Tagua personalizada</h1>
                <p class="tagua-hero-subtitle">
                    Transformamos la semilla de marfil vegetal ecuatoriano en elegantes llaveros, botones, dijes, accesorios de identificación y reconocimientos únicos con corte y grabado láser de alta fidelidad.
                </p>
                
                <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; margin-top: 1rem;">
                    <a href="#catalogo-tagua" class="btn btn-primary" style="padding: 12px 28px; font-size: 0.9rem; text-transform: none; font-weight: 600;">
                        Explorar Catálogo de Tagua
                    </a>
                    <a href="https://wa.me/<?php echo $tagua_wa_clean; ?>?text=Hola%20CardNet,%20deseo%20cotizar%20productos%20en%20Tagua%20personalizada" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="padding: 12px 24px; font-size: 0.9rem; text-transform: none; background: rgba(255,255,255,0.1); color: white; border-color: rgba(255,255,255,0.25);">
                        Consultar por WhatsApp
                    </a>
                </div>

                <!-- Barra de Estadísticas y Sellos de Valor -->
                <div class="tagua-stats-strip">
                    <div class="tagua-stat-item">
                        <span class="tagua-stat-icon">🍃</span>
                        <div class="tagua-stat-title">100% Biodegradable</div>
                        <div class="tagua-stat-desc">Alternativa natural al plástico</div>
                    </div>
                    <div class="tagua-stat-item">
                        <span class="tagua-stat-icon">⚡</span>
                        <div class="tagua-stat-title">Grabado Láser HD</div>
                        <div class="tagua-stat-desc">Marcado térmico indeleble</div>
                    </div>
                    <div class="tagua-stat-item">
                        <span class="tagua-stat-icon">🇪🇨</span>
                        <div class="tagua-stat-title">Origen Ecuatoriano</div>
                        <div class="tagua-stat-desc">Semilla silvestre seleccionada</div>
                    </div>
                    <div class="tagua-stat-item">
                        <span class="tagua-stat-icon">📦</span>
                        <div class="tagua-stat-title">Al Por Mayor</div>
                        <div class="tagua-stat-desc">Empresas, eventos y exportación</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. HISTORIA Y VALOR ECOLÓGICO DE LA TAGUA -->
        <section class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle">Identidad Natural & Sostenibilidad</span>
                <h2>¿Por qué elegir Tagua para tu marca o evento?</h2>
                <p style="max-width: 750px; margin: 0 auto;">
                    La Tagua (*Phytelephas aequatorialis*) es conocida mundialmente como el <strong>Marfil Vegetal</strong> por su extrema dureza, color marfil aperlado y textura suave. Al secarse, adquiere una densidad idéntica al marfil animal sin dañar a ninguna especie ni talar árboles.
                </p>
            </div>

            <div class="grid-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
                <!-- Card 1 -->
                <div class="benefit-box">
                    <div class="benefit-icon-wrap">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: var(--dark); margin-bottom: 0.6rem;">Cero Plástico & ESG</h3>
                    <p style="font-size: 0.86rem; color: var(--text-muted); line-height: 1.55; margin: 0;">
                        Cumple con los estándares ambientales de tu empresa sustituyendo el acrílico o el plástico por material 100% orgánico y compostable.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="benefit-box">
                    <div class="benefit-icon-wrap">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    </div>
                    <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: var(--dark); margin-bottom: 0.6rem;">Grabado Láser Inalterable</h3>
                    <p style="font-size: 0.86rem; color: var(--text-muted); line-height: 1.55; margin: 0;">
                        El haz de láser genera un contraste tostado natural de alta legibilidad sobre la semilla que nunca se despinta ni se desprende con el uso.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="benefit-box">
                    <div class="benefit-icon-wrap">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/></svg>
                    </div>
                    <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: var(--dark); margin-bottom: 0.6rem;">Piezas Exclusivas</h3>
                    <p style="font-size: 0.86rem; color: var(--text-muted); line-height: 1.55; margin: 0;">
                        Cada semilla presenta vetas, tonos y anillos botánicos únicos. Ningún regalo de tagua es igual a otro, otorgando exclusividad artesanal.
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="benefit-box">
                    <div class="benefit-icon-wrap">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <h3 style="font-family: var(--font-heading); font-size: 1.2rem; color: var(--dark); margin-bottom: 0.6rem;">Orgullo Ecuatoriano</h3>
                    <p style="font-size: 0.86rem; color: var(--text-muted); line-height: 1.55; margin: 0;">
                        Cosecha silvestre en los bosques húmedos del Ecuador que promueve la economía local y la conservación de la biodiversidad.
                    </p>
                </div>
            </div>
        </section>

        <!-- 3. CATÁLOGO DE PRODUCTOS DE TAGUA -->
        <section id="catalogo-tagua" class="section-padding" style="background: var(--surface-light); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
            <div class="container reveal-on-scroll">
                <div class="section-header center" style="margin-bottom: 3.5rem;">
                    <span class="section-subtitle">Catálogo de Taller</span>
                    <h2>Nuestra línea de productos en Tagua</h2>
                    <p>Modelos listos para personalizar con tu logotipo corporativo, nombres o diseños vectoriales.</p>
                </div>

                <div class="tagua-grid">
                    <?php foreach ($tagua_products as $tp): ?>
                        <?php
                        $p_img = !empty($tp['image_main']) ? $tp['image_main'] : '';
                        if (!empty($p_img)) {
                            if (strpos($p_img, 'http') === 0 || strpos($p_img, 'uploads/') === 0 || strpos($p_img, 'images/') === 0) {
                                $img_src = $p_img;
                            } else {
                                $img_src = 'uploads/' . $p_img;
                            }
                        } else {
                            $img_src = 'uploads/carnet_mockup.jpg';
                        }
                        ?>
                        <div class="tagua-card">
                            <a href="producto.php?slug=<?php echo htmlspecialchars($tp['slug']); ?>" class="tagua-card-img-wrap" style="text-decoration: none;">
                                <img src="<?php echo htmlspecialchars($img_src); ?>" alt="<?php echo htmlspecialchars($tp['name']); ?>">
                                <span style="position: absolute; top: 12px; left: 12px; background: rgba(16, 20, 15, 0.85); color: #8CFF32; font-size: 0.72rem; font-weight: 600; padding: 4px 8px; border-radius: 4px; letter-spacing: 0.05em; text-transform: uppercase;">
                                    🌱 Tagua 100% Natural
                                </span>
                            </a>
                            <div class="tagua-card-body">
                                <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--dark); margin-bottom: 0.5rem; font-weight: 500;">
                                    <a href="producto.php?slug=<?php echo htmlspecialchars($tp['slug']); ?>" style="color: inherit; text-decoration: none;">
                                        <?php echo htmlspecialchars($tp['name']); ?>
                                    </a>
                                </h3>
                                <p style="font-size: 0.84rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex-grow: 1;">
                                    <?php echo htmlspecialchars($tp['description_short']); ?>
                                </p>

                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; padding-top: 0.75rem; border-top: 1px solid var(--border);">
                                    <span style="font-size: 0.78rem; color: var(--text-muted);">
                                        Técnica: <strong style="color: var(--dark);">Láser HD</strong>
                                    </span>
                                    <?php if (!empty($tp['price']) && $tp['price'] > 0): ?>
                                        <span style="font-size: 0.95rem; font-weight: 700; color: var(--primary);">
                                            Desde $<?php echo number_format($tp['price'], 2); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <a href="cotizacion.php?producto=<?php echo htmlspecialchars($tp['slug']); ?>" class="btn btn-primary" style="width: 100%; text-align: center; font-size: 0.82rem; padding: 9px 0; text-transform: none;">
                                    <?php echo htmlspecialchars($tp['cta_text'] ?: 'Cotizar este producto'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="text-align: center; margin-top: 3rem;">
                    <p style="font-size: 0.92rem; color: var(--text-muted); margin-bottom: 1rem;">
                        ¿Requieres un corte especial, medidas personalizadas o botones con monograma para tu marca?
                    </p>
                    <a href="https://wa.me/<?php echo $tagua_wa_clean; ?>?text=Hola%20CardNet,%20deseo%20un%20diseno%20personalizado%20en%20Tagua" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="text-transform: none; font-size: 0.85rem; padding: 10px 24px;">
                        Solicitar diseño o medida especial
                    </a>
                </div>
            </div>
        </section>

        <!-- 4. NUESTRO PROCESO DE TALLER -->
        <section class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle">Precisión y Artesanía</span>
                <h2>Cómo trabajamos cada pieza de Tagua</h2>
                <p>Unimos la riqueza de la artesanía ecuatoriana con maquinaria láser de control numérico computarizado.</p>
            </div>

            <div class="grid-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
                <div class="process-step-card">
                    <div class="step-number-badge">1</div>
                    <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--dark); margin-bottom: 0.5rem;">Selección y Curado</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                        Escogemos semillas maduras de tagua con el secado óptimo para evitar micro-fisuras y garantizar dureza máxima.
                    </p>
                </div>

                <div class="process-step-card">
                    <div class="step-number-badge">2</div>
                    <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--dark); margin-bottom: 0.5rem;">Torneado y Calibrado</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                        Cortamos y pulimos la semilla hasta lograr discos, dijes o placas perfectamente niveladas con tacto sedoso.
                    </p>
                </div>

                <div class="process-step-card">
                    <div class="step-number-badge">3</div>
                    <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--dark); margin-bottom: 0.5rem;">Grabado Láser de Precisión</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                        Grabamos tu logotipo con focalización milimétrica para obtener líneas nítidas, tipografías legibles y contraste natural.
                    </p>
                </div>

                <div class="process-step-card">
                    <div class="step-number-badge">4</div>
                    <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--dark); margin-bottom: 0.5rem;">Control y Entrega</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                        Inspección visual una por una, ensamblado de herrajes metálicos y empaque listo para distribución corporativa.
                    </p>
                </div>
            </div>
        </section>

        <!-- 5. SECTORES Y CASOS DE USO -->
        <section class="section-padding" style="background: var(--dark); color: white; border-top: 1px solid var(--border);">
            <div class="container reveal-on-scroll">
                <div class="section-header center" style="margin-bottom: 3.5rem;">
                    <span class="section-subtitle" style="color: #8CFF32; border-color: #8CFF32;">Aplicaciones Comerciales</span>
                    <h2 style="color: white;">Soluciones en Tagua para tu Empresa o Evento</h2>
                    <p style="color: rgba(255,255,255,0.75);">Opciones diseñadas para organizaciones que buscan destacar con valor ecológico real.</p>
                </div>

                <div class="grid-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
                    <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.12); padding: 2rem; border-radius: var(--radius-md);">
                        <h4 style="font-family: var(--font-heading); font-size: 1.2rem; color: #8CFF32; margin-bottom: 0.75rem;">Congresos & Eventos ESG</h4>
                        <p style="font-size: 0.85rem; color: rgba(255,255,255,0.8); line-height: 1.6; margin: 0;">
                            Identificación de ponentes, dijes conmemorativos y souvenirs para cumbres sostenibles libres de plástico.
                        </p>
                    </div>

                    <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.12); padding: 2rem; border-radius: var(--radius-md);">
                        <h4 style="font-family: var(--font-heading); font-size: 1.2rem; color: #8CFF32; margin-bottom: 0.75rem;">Hoteles & Souvenirs VIP</h4>
                        <p style="font-size: 0.85rem; color: rgba(255,255,255,0.8); line-height: 1.6; margin: 0;">
                            Llaveros de habitación con numeración láser, placas decorativas y recuerdos auténticos de procedencia ecuatoriana.
                        </p>
                    </div>

                    <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.12); padding: 2rem; border-radius: var(--radius-md);">
                        <h4 style="font-family: var(--font-heading); font-size: 1.2rem; color: #8CFF32; margin-bottom: 0.75rem;">Uniformes & Confección</h4>
                        <p style="font-size: 0.85rem; color: rgba(255,255,255,0.8); line-height: 1.6; margin: 0;">
                            Botones grabados para camisas ejecutivas, chaquetas de chef y prendas institucionales de alta costura.
                        </p>
                    </div>

                    <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.12); padding: 2rem; border-radius: var(--radius-md);">
                        <h4 style="font-family: var(--font-heading); font-size: 1.2rem; color: #8CFF32; margin-bottom: 0.75rem;">Kits & Regalos de Fin de Año</h4>
                        <p style="font-size: 0.85rem; color: rgba(255,255,255,0.8); line-height: 1.6; margin: 0;">
                            Detalles ejecutivos combinados en cajas de madera y libretas recicladas con el sello distintivo de tu marca.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. PREGUNTAS FRECUENTES (FAQ TAGUA) -->
        <section class="section-padding container reveal-on-scroll">
            <div class="section-header center" style="margin-bottom: 3rem;">
                <span class="section-subtitle">Respuestas Rápidas</span>
                <h2>Preguntas Frecuentes sobre la Tagua</h2>
                <p>Todo lo que necesitas saber antes de encargar tu lote personalizado.</p>
            </div>

            <div style="max-width: 800px; margin: 0 auto;">
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        ¿Qué es la tagua y por qué se llama marfil vegetal?
                        <span style="font-size: 1.2rem; transition: transform 0.3s;">+</span>
                    </button>
                    <div class="faq-answer">
                        La tagua es la semilla madura de la palma *Phytelephas aequatorialis*, nativa de los bosques tropicales de Ecuador. Cuando se deshidrata, su interior se vuelve duro, blanco y pulido con una composición y densidad idéntica al marfil animal, siendo 100% ecológica, renovable y legal.
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        ¿Se puede grabar el logo exacto de mi empresa con tipografías y detalles finos?
                        <span style="font-size: 1.2rem; transition: transform 0.3s;">+</span>
                    </button>
                    <div class="faq-answer">
                        Sí. Con nuestras máquinas de corte y grabado láser computarizado podemos grabar vectores, isotipos y tipografías con absoluta precisión milimétrica. Antes de iniciar la producción en masa, te enviamos un render previo digital para tu aprobación.
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        ¿El grabado en tagua se borra con el agua o con el uso diario?
                        <span style="font-size: 1.2rem; transition: transform 0.3s;">+</span>
                    </button>
                    <div class="faq-answer">
                        No. El grabado láser no utiliza tintas que puedan correrse; se trata de una micro-cauterización térmica profunda en la propia fibra natural de la semilla, haciéndolo totalmente permanente e inalterable en el tiempo.
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        ¿Realizan envíos a todo el Ecuador y cuál es el tiempo de entrega?
                        <span style="font-size: 1.2rem; transition: transform 0.3s;">+</span>
                    </button>
                    <div class="faq-answer">
                        Sí, despachamos pedidos a Quito, Guayaquil, Cuenca y todas las ciudades del país mediante Servientrega o transporte seguro. Los tiempos de producción habituales van de 24 a 72 horas según el volumen solicitado.
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. CTA FINAL -->
        <section class="section-padding" style="background: linear-gradient(135deg, #10140f 0%, #172418 100%); color: white; text-align: center; border-top: 1px solid var(--border);">
            <div class="container reveal-on-scroll" style="max-width: 750px;">
                <span class="tagua-badge">Empieza tu proyecto</span>
                <h2 style="font-family: var(--font-heading); font-size: 2.4rem; color: white; margin-bottom: 1rem; font-weight: 400;">
                    Lleva tu marca al siguiente nivel con marfil vegetal sostenible
                </h2>
                <p style="color: rgba(255,255,255,0.85); font-size: 1.05rem; line-height: 1.6; margin-bottom: 2rem;">
                    Escríbenos con la cantidad de piezas y tu logotipo. Te enviaremos una muestra digital y cotización inmediata para tu empresa.
                </p>
                <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                    <a href="cotizacion.php?cat=tagua" class="btn btn-primary" style="padding: 14px 32px; font-size: 0.95rem; text-transform: none; font-weight: 600;">
                        Iniciar Cotización de Tagua
                    </a>
                    <a href="https://wa.me/<?php echo $tagua_wa_clean; ?>?text=Hola%20CardNet,%20deseo%20asesoria%20para%20un%20pedido%20de%20productos%20en%20Tagua" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="padding: 14px 28px; font-size: 0.95rem; text-transform: none; background: rgba(255,255,255,0.1); color: white; border-color: rgba(255,255,255,0.3);">
                        Hablar con un Asesor
                    </a>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        function toggleFaq(btn) {
            const answer = btn.nextElementSibling;
            const icon = btn.querySelector('span');
            if (answer.style.display === 'block') {
                answer.style.display = 'none';
                icon.textContent = '+';
            } else {
                answer.style.display = 'block';
                icon.textContent = '−';
            }
        }
        // Cerrar respuestas por defecto excepto la primera
        document.querySelectorAll('.faq-answer').forEach((el, idx) => {
            if (idx > 0) el.style.display = 'none';
            else el.style.display = 'block';
        });
    </script>
</body>
</html>
