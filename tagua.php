<?php
session_start();
require_once 'db.php';

// Cargar configuraciones del sitio y contenido dinámico de Tagua
$site_settings = getSiteSettings($pdo);
$tagua_c = getTaguaContent($pdo);

$page_title = 'Productos de Tagua Personalizados | Marfil Vegetal Ecuatoriano con Grabado Láser | CardNet.ec';
$page_description = 'Llaveros, botones, medallas, placas y regalos corporativos en Tagua (marfil vegetal ecuatoriano 100% ecológico). Personalización de alta precisión con grabado láser indeleble.';
$tagua_wa_clean = cleanWhatsAppNumber($site_settings['whatsapp'] ?? '');
$tagua_wa_display = !empty($site_settings['whatsapp']) ? $site_settings['whatsapp'] : '+593 99 978 180';

// Obtener productos de Tagua desde la base de datos (SOLO los productos de la categoría tagua)
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

// Helper para resolver imagen del Hero
$hero_bg_url = getUploadedImgUrl(!empty($tagua_c['hero_image']) ? $tagua_c['hero_image'] : 'tagua_hero_bg.webp', 'images/tagua_hero_bg.webp');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Favicon Oficial -->
    <link rel="icon" type="image/png" href="favicon.png?v=2.0">
    <link rel="shortcut icon" href="favicon.ico?v=2.0">
    <link rel="apple-touch-icon" href="favicon.png?v=2.0">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <link rel="canonical" href="https://cardnet.ec/tagua.php">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://cardnet.ec/tagua.php">
    <meta property="og:image" content="https://cardnet.ec/<?php echo htmlspecialchars($hero_bg_url); ?>">

    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=6.3">
    <link rel="stylesheet" href="css/layout.css?v=6.3">
    <link rel="stylesheet" href="css/components.css?v=6.3">
    <link rel="stylesheet" href="css/pages.css?v=6.3">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Asegurar visibilidad inmediata de todos los bloques */
        section {
            opacity: 1 !important;
            transform: none !important;
            visibility: visible !important;
        }

        /* HERO TAGUA */
        .tagua-hero-container {
            background-color: #0b140d;
            background-image: linear-gradient(135deg, rgba(11, 20, 13, 0.94) 0%, rgba(18, 33, 20, 0.88) 100%), url('<?php echo htmlspecialchars($hero_bg_url); ?>');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 6rem 0 5rem 0;
            position: relative;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .tagua-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(99, 174, 44, 0.15);
            border: 1px solid rgba(140, 255, 50, 0.35);
            color: #9eff42;
            padding: 6px 16px;
            border-radius: 4px;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }
        .tagua-hero-h1 {
            font-family: var(--font-heading);
            font-size: clamp(2.2rem, 4.5vw, 3.4rem);
            line-height: 1.15;
            color: white;
            margin-bottom: 1.25rem;
            font-weight: 400;
            letter-spacing: -0.01em;
        }
        .tagua-hero-p {
            font-size: clamp(1rem, 1.8vw, 1.15rem);
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.65;
            max-width: 760px;
            margin: 0 auto 2.25rem auto;
        }
        .tagua-stats-bar {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 3.5rem;
            padding-top: 2.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }
        .tagua-stat-cell {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .tagua-stat-icon-svg {
            width: 28px;
            height: 28px;
            stroke: #8CFF32;
            fill: none;
            stroke-width: 2;
            margin-bottom: 8px;
        }
        .tagua-stat-head {
            font-weight: 600;
            font-size: 0.95rem;
            color: white;
            margin-bottom: 3px;
        }
        .tagua-stat-sub {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.65);
        }

        /* BENEFICIOS CARD */
        .benefit-card-tech {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 2.25rem;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .benefit-card-tech:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.05);
        }
        .benefit-svg-box {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            background: var(--surface-light);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            margin-bottom: 1.25rem;
        }

        /* CATÁLOGO PRODUCTOS TAGUA */
        .tagua-catalog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 28px;
        }
        .tagua-prod-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.35s ease;
        }
        .tagua-prod-card:hover {
            transform: translateY(-6px);
            border-color: var(--primary);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.08);
        }
        .tagua-prod-media {
            width: 100%;
            aspect-ratio: 1.2;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-bottom: 1px solid var(--border);
            position: relative;
        }
        .tagua-prod-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .tagua-prod-card:hover .tagua-prod-media img {
            transform: scale(1.05);
        }
        .tagua-prod-info {
            padding: 1.75rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        /* PROCESO STEPS */
        .process-box {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 2rem;
            position: relative;
            transition: all 0.3s ease;
        }
        .process-box:hover {
            border-color: var(--primary);
            box-shadow: 0 10px 25px rgba(0,0,0,0.04);
        }
        .process-step-num {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--primary);
            background: rgba(99, 174, 44, 0.1);
            padding: 4px 10px;
            border-radius: 4px;
            text-transform: uppercase;
            margin-bottom: 1rem;
            letter-spacing: 0.05em;
        }

        /* TABLA COMPARATIVA */
        .tagua-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            overflow: hidden;
            font-size: 0.88rem;
        }
        .tagua-table th {
            background: #101511;
            color: white;
            padding: 1.25rem 1rem;
            font-weight: 600;
            text-align: left;
            border-bottom: 2px solid var(--primary);
        }
        .tagua-table td {
            padding: 1.15rem 1rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            color: var(--text-dark);
        }
        .tagua-table tr:last-child td {
            border-bottom: none;
        }
        .tagua-table tr:nth-child(even) {
            background: #fbfcfb;
        }

        /* FAQ ACCORDION */
        .faq-item-serious {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            margin-bottom: 12px;
            overflow: hidden;
        }
        .faq-trigger {
            width: 100%;
            padding: 1.25rem 1.5rem;
            text-align: left;
            background: none;
            border: none;
            font-family: 'Work Sans', sans-serif;
            font-size: 0.98rem;
            font-weight: 600;
            color: var(--dark);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .faq-trigger:hover {
            color: var(--primary);
        }
        .faq-body {
            padding: 0 1.5rem 1.25rem 1.5rem;
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        @media (max-width: 992px) {
            .tagua-stats-bar {
                grid-template-columns: repeat(2, 1fr);
                gap: 24px;
            }
        }
        @media (max-width: 576px) {
            .tagua-stats-bar {
                grid-template-columns: 1fr;
            }
            .tagua-catalog-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <main>
        <!-- 1. HERO SECTION -->
        <section class="tagua-hero-container">
            <div class="container center" style="max-width: 920px; position: relative; z-index: 5;">
                <div class="tagua-badge-pill">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <?php echo htmlspecialchars($tagua_c['hero_badge']); ?>
                </div>
                <h1 class="tagua-hero-h1"><?php echo htmlspecialchars($tagua_c['hero_title']); ?></h1>
                <p class="tagua-hero-p">
                    <?php echo htmlspecialchars($tagua_c['hero_subtitle']); ?>
                </p>
                
                <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; margin-top: 1rem;">
                    <a href="<?php echo htmlspecialchars($tagua_c['hero_btn_url']); ?>" class="btn btn-primary" style="padding: 12px 28px; font-size: 0.88rem; text-transform: none; font-weight: 600;">
                        <?php echo htmlspecialchars($tagua_c['hero_btn_text']); ?>
                    </a>
                    <a href="https://wa.me/<?php echo $tagua_wa_clean; ?>?text=Hola%20CardNet,%20deseo%20cotizar%20articulos%20en%20Tagua%20personalizada" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="padding: 12px 24px; font-size: 0.88rem; text-transform: none; background: rgba(255,255,255,0.1); color: white; border-color: rgba(255,255,255,0.25);">
                        <?php echo htmlspecialchars($tagua_c['hero_btn_sec_text']); ?>
                    </a>
                </div>

                <!-- Tira de Garantías con Iconos SVG -->
                <div class="tagua-stats-bar">
                    <div class="tagua-stat-cell">
                        <svg class="tagua-stat-icon-svg" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                        <div class="tagua-stat-head"><?php echo htmlspecialchars($tagua_c['stat_1_title']); ?></div>
                        <div class="tagua-stat-sub"><?php echo htmlspecialchars($tagua_c['stat_1_desc']); ?></div>
                    </div>
                    <div class="tagua-stat-cell">
                        <svg class="tagua-stat-icon-svg" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        <div class="tagua-stat-head"><?php echo htmlspecialchars($tagua_c['stat_2_title']); ?></div>
                        <div class="tagua-stat-sub"><?php echo htmlspecialchars($tagua_c['stat_2_desc']); ?></div>
                    </div>
                    <div class="tagua-stat-cell">
                        <svg class="tagua-stat-icon-svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                        <div class="tagua-stat-head"><?php echo htmlspecialchars($tagua_c['stat_3_title']); ?></div>
                        <div class="tagua-stat-sub"><?php echo htmlspecialchars($tagua_c['stat_3_desc']); ?></div>
                    </div>
                    <div class="tagua-stat-cell">
                        <svg class="tagua-stat-icon-svg" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        <div class="tagua-stat-head"><?php echo htmlspecialchars($tagua_c['stat_4_title']); ?></div>
                        <div class="tagua-stat-sub"><?php echo htmlspecialchars($tagua_c['stat_4_desc']); ?></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. ORIGEN Y SUSTENTABILIDAD -->
        <section class="section-padding container">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle"><?php echo htmlspecialchars($tagua_c['esg_badge']); ?></span>
                <h2><?php echo htmlspecialchars($tagua_c['esg_title']); ?></h2>
                <p style="max-width: 780px; margin: 0 auto;">
                    <?php echo htmlspecialchars($tagua_c['esg_description']); ?>
                </p>
            </div>

            <div class="grid-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
                <div class="benefit-card-tech">
                    <div class="benefit-svg-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h3 style="font-family: var(--font-heading); font-size: 1.18rem; color: var(--dark); margin-bottom: 0.6rem;"><?php echo htmlspecialchars($tagua_c['benefit_1_title']); ?></h3>
                    <p style="font-size: 0.86rem; color: var(--text-muted); line-height: 1.55; margin: 0;">
                        <?php echo htmlspecialchars($tagua_c['benefit_1_desc']); ?>
                    </p>
                </div>

                <div class="benefit-card-tech">
                    <div class="benefit-svg-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <h3 style="font-family: var(--font-heading); font-size: 1.18rem; color: var(--dark); margin-bottom: 0.6rem;"><?php echo htmlspecialchars($tagua_c['benefit_2_title']); ?></h3>
                    <p style="font-size: 0.86rem; color: var(--text-muted); line-height: 1.55; margin: 0;">
                        <?php echo htmlspecialchars($tagua_c['benefit_2_desc']); ?>
                    </p>
                </div>

                <div class="benefit-card-tech">
                    <div class="benefit-svg-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                    <h3 style="font-family: var(--font-heading); font-size: 1.18rem; color: var(--dark); margin-bottom: 0.6rem;"><?php echo htmlspecialchars($tagua_c['benefit_3_title']); ?></h3>
                    <p style="font-size: 0.86rem; color: var(--text-muted); line-height: 1.55; margin: 0;">
                        <?php echo htmlspecialchars($tagua_c['benefit_3_desc']); ?>
                    </p>
                </div>

                <div class="benefit-card-tech">
                    <div class="benefit-svg-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <h3 style="font-family: var(--font-heading); font-size: 1.18rem; color: var(--dark); margin-bottom: 0.6rem;"><?php echo htmlspecialchars($tagua_c['benefit_4_title']); ?></h3>
                    <p style="font-size: 0.86rem; color: var(--text-muted); line-height: 1.55; margin: 0;">
                        <?php echo htmlspecialchars($tagua_c['benefit_4_desc']); ?>
                    </p>
                </div>
            </div>
        </section>

        <!-- 3. CATÁLOGO DE PRODUCTOS DE TAGUA -->
        <section id="catalogo-tagua" class="section-padding" style="background: var(--surface-light); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
            <div class="container">
                <div class="section-header center" style="margin-bottom: 3.5rem;">
                    <span class="section-subtitle"><?php echo htmlspecialchars($tagua_c['catalog_badge']); ?></span>
                    <h2><?php echo htmlspecialchars($tagua_c['catalog_title']); ?></h2>
                    <p><?php echo htmlspecialchars($tagua_c['catalog_description']); ?></p>
                </div>

                <div class="tagua-catalog-grid">
                    <?php foreach ($tagua_products as $tp): ?>
                        <?php
                        $img_src = getUploadedImgUrl($tp['image_main'] ?? '', 'uploads/tagua_llavero.webp');
                        ?>
                        <div class="tagua-prod-card">
                            <a href="producto.php?slug=<?php echo htmlspecialchars($tp['slug']); ?>" class="tagua-prod-media" style="text-decoration: none;">
                                <img src="<?php echo htmlspecialchars($img_src); ?>" alt="<?php echo htmlspecialchars($tp['name']); ?>" loading="lazy" decoding="async">
                                <span style="position: absolute; top: 12px; left: 12px; background: rgba(16, 20, 15, 0.88); color: #9eff42; font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 3px; letter-spacing: 0.05em; text-transform: uppercase;">
                                    Marfil Vegetal
                                </span>
                            </a>
                            <div class="tagua-prod-info">
                                <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--dark); margin-bottom: 0.5rem; font-weight: 500;">
                                    <a href="producto.php?slug=<?php echo htmlspecialchars($tp['slug']); ?>" style="color: inherit; text-decoration: none;">
                                        <?php echo htmlspecialchars($tp['name']); ?>
                                    </a>
                                </h3>
                                <p style="font-size: 0.84rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex-grow: 1;">
                                    <?php echo htmlspecialchars($tp['description_short']); ?>
                                </p>

                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-top: 0.85rem; border-top: 1px solid var(--border);">
                                    <span style="font-size: 0.78rem; color: var(--text-muted);">
                                        Grabado: <strong style="color: var(--dark);">Láser HD</strong>
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

                <div style="text-align: center; margin-top: 3.5rem; background: white; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 2rem;">
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($tagua_c['custom_box_title']); ?></h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted); max-width: 600px; margin: 0 auto 1.25rem auto;">
                        <?php echo htmlspecialchars($tagua_c['custom_box_desc']); ?>
                    </p>
                    <a href="https://wa.me/<?php echo $tagua_wa_clean; ?>?text=Hola%20CardNet,%20deseo%20solicitar%20piezas%20de%20Tagua%20con%20medidas%20especiales" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="text-transform: none; font-size: 0.85rem; padding: 10px 24px;">
                        <?php echo htmlspecialchars($tagua_c['custom_box_btn_text']); ?>
                    </a>
                </div>
            </div>
        </section>

        <!-- 4. TABLA COMPARATIVA TÉCNICA -->
        <section class="section-padding container">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle">Especificaciones Técnicas</span>
                <h2>Comparativa de Materiales para Identificación</h2>
                <p>Análisis técnico entre Tagua y materiales convencionales de marcaje.</p>
            </div>

            <div style="overflow-x: auto;">
                <table class="tagua-table">
                    <thead>
                        <tr>
                            <th>Característica</th>
                            <th style="color: #8CFF32;">Tagua (Marfil Vegetal)</th>
                            <th>Acrílico Comercial</th>
                            <th>Plástico Inyectado / PVC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Origen del Material</strong></td>
                            <td>Semilla 100% natural y renovable</td>
                            <td>Polímero sintético derivado de petróleo</td>
                            <td>Plástico derivado de hidrocarburos</td>
                        </tr>
                        <tr>
                            <td><strong>Impacto Ambiental</strong></td>
                            <td>Biodegradable y compostable</td>
                            <td>No biodegradable (cientos de años)</td>
                            <td>Alta huella de carbono</td>
                        </tr>
                        <tr>
                            <td><strong>Técnica de Marcado</strong></td>
                            <td>Grabado térmico láser indeleble</td>
                            <td>Corte / grabado láser superficial</td>
                            <td>Impresión por tinta o serigrafía</td>
                        </tr>
                        <tr>
                            <td><strong>Durabilidad del Grabado</strong></td>
                            <td>Permanente (nunca se borra)</td>
                            <td>Media (susceptible a rayones)</td>
                            <td>Baja (se desgasta con fricción)</td>
                        </tr>
                        <tr>
                            <td><strong>Percepción de Marca</strong></td>
                            <td>Exclusiva, artesanal y responsable</td>
                            <td>Estándar industrial</td>
                            <td>Promocional básica</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 5. PROCESO DE TALLER -->
        <section class="section-padding" style="background: var(--surface-light); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
            <div class="container">
                <div class="section-header center" style="margin-bottom: 3.5rem;">
                    <span class="section-subtitle"><?php echo htmlspecialchars($tagua_c['process_badge']); ?></span>
                    <h2><?php echo htmlspecialchars($tagua_c['process_title']); ?></h2>
                    <p><?php echo htmlspecialchars($tagua_c['process_description']); ?></p>
                </div>

                <div class="grid-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
                    <div class="process-box">
                        <span class="process-step-num">Paso 01</span>
                        <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--dark); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($tagua_c['step_1_title']); ?></h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                            <?php echo htmlspecialchars($tagua_c['step_1_desc']); ?>
                        </p>
                    </div>

                    <div class="process-box">
                        <span class="process-step-num">Paso 02</span>
                        <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--dark); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($tagua_c['step_2_title']); ?></h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                            <?php echo htmlspecialchars($tagua_c['step_2_desc']); ?>
                        </p>
                    </div>

                    <div class="process-box">
                        <span class="process-step-num">Paso 03</span>
                        <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--dark); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($tagua_c['step_3_title']); ?></h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                            <?php echo htmlspecialchars($tagua_c['step_3_desc']); ?>
                        </p>
                    </div>

                    <div class="process-box">
                        <span class="process-step-num">Paso 04</span>
                        <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--dark); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($tagua_c['step_4_title']); ?></h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                            <?php echo htmlspecialchars($tagua_c['step_4_desc']); ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. SECTORES Y APLICACIONES -->
        <section class="section-padding container">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle"><?php echo htmlspecialchars($tagua_c['sectors_badge']); ?></span>
                <h2><?php echo htmlspecialchars($tagua_c['sectors_title']); ?></h2>
                <p><?php echo htmlspecialchars($tagua_c['sectors_description']); ?></p>
            </div>

            <div class="grid-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
                <div style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-sm);">
                    <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--dark); margin-bottom: 0.6rem;"><?php echo htmlspecialchars($tagua_c['sector_1_title']); ?></h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.55; margin: 0;">
                        <?php echo htmlspecialchars($tagua_c['sector_1_desc']); ?>
                    </p>
                </div>

                <div style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-sm);">
                    <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--dark); margin-bottom: 0.6rem;"><?php echo htmlspecialchars($tagua_c['sector_2_title']); ?></h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.55; margin: 0;">
                        <?php echo htmlspecialchars($tagua_c['sector_2_desc']); ?>
                    </p>
                </div>

                <div style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-sm);">
                    <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--dark); margin-bottom: 0.6rem;"><?php echo htmlspecialchars($tagua_c['sector_3_title']); ?></h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.55; margin: 0;">
                        <?php echo htmlspecialchars($tagua_c['sector_3_desc']); ?>
                    </p>
                </div>

                <div style="background: white; border: 1px solid var(--border); padding: 2rem; border-radius: var(--radius-sm);">
                    <h3 style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--dark); margin-bottom: 0.6rem;"><?php echo htmlspecialchars($tagua_c['sector_4_title']); ?></h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.55; margin: 0;">
                        <?php echo htmlspecialchars($tagua_c['sector_4_desc']); ?>
                    </p>
                </div>
            </div>
        </section>

        <!-- 7. PREGUNTAS FRECUENTES (FAQ) -->
        <section class="section-padding" style="background: var(--surface-light); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
            <div class="container">
                <div class="section-header center" style="margin-bottom: 3rem;">
                    <span class="section-subtitle">Preguntas Frecuentes</span>
                    <h2>Consultas Habituales sobre Productos de Tagua</h2>
                    <p>Información clave para planificar pedidos corporativos.</p>
                </div>

                <div style="max-width: 800px; margin: 0 auto;">
                    <div class="faq-item-serious">
                        <button class="faq-trigger" onclick="toggleFaqSerious(this)">
                            <?php echo htmlspecialchars($tagua_c['faq_1_q']); ?>
                            <span style="font-size: 1.1rem; transition: transform 0.3s;">+</span>
                        </button>
                        <div class="faq-body">
                            <?php echo htmlspecialchars($tagua_c['faq_1_a']); ?>
                        </div>
                    </div>

                    <div class="faq-item-serious">
                        <button class="faq-trigger" onclick="toggleFaqSerious(this)">
                            <?php echo htmlspecialchars($tagua_c['faq_2_q']); ?>
                            <span style="font-size: 1.1rem; transition: transform 0.3s;">+</span>
                        </button>
                        <div class="faq-body">
                            <?php echo htmlspecialchars($tagua_c['faq_2_a']); ?>
                        </div>
                    </div>

                    <div class="faq-item-serious">
                        <button class="faq-trigger" onclick="toggleFaqSerious(this)">
                            <?php echo htmlspecialchars($tagua_c['faq_3_q']); ?>
                            <span style="font-size: 1.1rem; transition: transform 0.3s;">+</span>
                        </button>
                        <div class="faq-body">
                            <?php echo htmlspecialchars($tagua_c['faq_3_a']); ?>
                        </div>
                    </div>

                    <div class="faq-item-serious">
                        <button class="faq-trigger" onclick="toggleFaqSerious(this)">
                            <?php echo htmlspecialchars($tagua_c['faq_4_q']); ?>
                            <span style="font-size: 1.1rem; transition: transform 0.3s;">+</span>
                        </button>
                        <div class="faq-body">
                            <?php echo htmlspecialchars($tagua_c['faq_4_a']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. BANNER CTA FINAL -->
        <section class="section-padding" style="background: #101511; color: white; text-align: center;">
            <div class="container" style="max-width: 760px;">
                <div class="tagua-badge-pill" style="margin-bottom: 1.25rem;">
                    <?php echo htmlspecialchars($tagua_c['cta_badge']); ?>
                </div>
                <h2 style="font-family: var(--font-heading); font-size: 2.3rem; color: white; margin-bottom: 1rem; font-weight: 400;">
                    <?php echo htmlspecialchars($tagua_c['cta_title']); ?>
                </h2>
                <p style="color: rgba(255,255,255,0.8); font-size: 1rem; line-height: 1.6; margin-bottom: 2.25rem;">
                    <?php echo htmlspecialchars($tagua_c['cta_subtitle']); ?>
                </p>
                <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                    <a href="cotizacion.php?cat=tagua" class="btn btn-primary" style="padding: 13px 30px; font-size: 0.92rem; text-transform: none; font-weight: 600;">
                        <?php echo htmlspecialchars($tagua_c['cta_btn_text']); ?>
                    </a>
                    <a href="https://wa.me/<?php echo $tagua_wa_clean; ?>?text=Hola%20CardNet,%20deseo%20asesoria%20tecnica%20para%20un%20pedido%20en%20Tagua" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="padding: 13px 26px; font-size: 0.92rem; text-transform: none; background: rgba(255,255,255,0.1); color: white; border-color: rgba(255,255,255,0.25);">
                        <?php echo htmlspecialchars($tagua_c['cta_btn_sec_text']); ?>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        function toggleFaqSerious(btn) {
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
        // Inicializar FAQ: primera abierta, resto cerradas
        document.querySelectorAll('.faq-body').forEach((el, idx) => {
            if (idx > 0) el.style.display = 'none';
            else el.style.display = 'block';
        });
    </script>
</body>
</html>
