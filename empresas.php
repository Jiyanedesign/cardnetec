<?php
session_start();
require_once 'db.php';
$c_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$site_settings = getSiteSettings($pdo);
$emp_c = getEmpresasContent($pdo);
$emp_wa_clean = !empty($site_settings['whatsapp']) ? preg_replace('/[^0-9]/', '', $site_settings['whatsapp']) : '593000000000';
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
    <title>Carnets Corporativos y Soluciones para Empresas | CardNet.ec</title>
    <meta name="description" content="Soluciones integrales de identificación para empresas y equipos. Carnets PVC de alta fidelidad, cintas sublimadas, combos promocionales y regalos corporativos en Ecuador.">
    <link rel="canonical" href="https://cardnetec.com.ec/empresas.php">
    <link rel="stylesheet" href="css/base.css?v=6.3">
    <link rel="stylesheet" href="css/layout.css?v=6.3">
    <link rel="stylesheet" href="css/components.css?v=6.3">
    <style>
        .empresas-hero {
            background: linear-gradient(135deg, #0d110b 0%, #151a12 100%);
            color: white;
            padding: 6.5rem 0;
            text-align: center;
            border-bottom: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }
        .empresas-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 50%, rgba(99, 174, 44, 0.16) 0%, transparent 70%);
            pointer-events: none;
        }
        .stat-box {
            background: #161b12;
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            color: white;
            transition: all 0.3s ease;
        }
        .stat-box:hover {
            border-color: var(--primary) !important;
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(99, 174, 44, 0.12);
        }
        .importance-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 2.25rem;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .importance-card:hover {
            transform: translateY(-6px);
            border-color: var(--primary) !important;
            box-shadow: 0 15px 30px rgba(99, 174, 44, 0.08);
        }
        .badge-type-card {
            background: #1c1b1b;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            color: white;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .badge-type-card:hover {
            transform: translateY(-6px);
            border-color: var(--primary) !important;
            box-shadow: 0 15px 30px rgba(99, 174, 44, 0.15);
        }
        .badge-type-card h3 {
            color: #ffffff !important;
        }
        .badge-type-card p {
            color: rgba(255, 255, 255, 0.85) !important;
        }
        .custom-feature-item {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }
        .custom-feature-item:hover {
            border-color: var(--primary) !important;
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(99, 174, 44, 0.06);
        }
        .corp-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .corp-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary) !important;
            box-shadow: 0 20px 40px rgba(99, 174, 44, 0.08);
        }
        .combo-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-sm);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .combo-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary) !important;
            box-shadow: 0 20px 40px rgba(99, 174, 44, 0.08);
        }
        .combo-tag {
            background: rgba(99, 174, 44, 0.1);
            color: var(--primary-hover);
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-block;
            margin-bottom: 12px;
        }
        .combo-item-link {
            color: var(--dark);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        .combo-item-link:hover {
            color: var(--primary);
            text-decoration: underline;
        }
        @media (max-width: 767px) {
            .grid-2 {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <main>
        <!-- 1. Hero Section -->
        <section class="empresas-hero">
            <div class="container" style="max-width: 920px; position: relative; z-index: 5;">
                <span style="color: #9eff42; font-weight: 700; font-size: 0.85rem; letter-spacing: 0.12em; text-transform: uppercase;">
                    <?php echo htmlspecialchars($emp_c['hero_badge']); ?>
                </span>
                <h1 style="font-family: var(--font-heading); font-size: 3.2rem; font-weight: 400; margin-top: 15px; margin-bottom: 20px; color: white; line-height: 1.15;">
                    <?php echo htmlspecialchars($emp_c['hero_title']); ?>
                </h1>
                <p style="font-size: 1.15rem; color: rgba(255, 255, 255, 0.88); line-height: 1.6; margin: 0 auto 2.5rem auto;">
                    <?php echo nl2br(htmlspecialchars($emp_c['hero_subtitle'])); ?>
                </p>
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <a href="<?php echo htmlspecialchars($emp_c['hero_btn1_url']); ?>" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600; text-transform: none;">
                        <?php echo htmlspecialchars($emp_c['hero_btn1_text']); ?>
                    </a>
                    <a href="https://wa.me/<?php echo $emp_wa_clean; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="background: rgba(255,255,255,0.08); color: white; border: 1px solid rgba(255,255,255,0.2); padding: 14px 30px; font-weight: 600; text-transform: none;">
                        <?php echo htmlspecialchars($emp_c['hero_btn2_text']); ?>
                    </a>
                </div>
            </div>
        </section>

        <!-- 2. Barra de Métricas y Estadísticas -->
        <section class="section-padding container" style="padding-top: 3.5rem; padding-bottom: 3.5rem;">
            <div class="grid-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px;">
                <div class="stat-box">
                    <div style="font-size: 2.3rem; font-weight: 700; color: #9eff42; margin-bottom: 5px;"><?php echo htmlspecialchars($emp_c['stat_val_1']); ?></div>
                    <div style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; color: rgba(255,255,255,0.7); letter-spacing: 0.05em;"><?php echo htmlspecialchars($emp_c['stat_lbl_1']); ?></div>
                </div>
                <div class="stat-box">
                    <div style="font-size: 2.3rem; font-weight: 700; color: #9eff42; margin-bottom: 5px;"><?php echo htmlspecialchars($emp_c['stat_val_2']); ?></div>
                    <div style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; color: rgba(255,255,255,0.7); letter-spacing: 0.05em;"><?php echo htmlspecialchars($emp_c['stat_lbl_2']); ?></div>
                </div>
                <div class="stat-box">
                    <div style="font-size: 2.3rem; font-weight: 700; color: #9eff42; margin-bottom: 5px;"><?php echo htmlspecialchars($emp_c['stat_val_3']); ?></div>
                    <div style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; color: rgba(255,255,255,0.7); letter-spacing: 0.05em;"><?php echo htmlspecialchars($emp_c['stat_lbl_3']); ?></div>
                </div>
                <div class="stat-box">
                    <div style="font-size: 2.3rem; font-weight: 700; color: #9eff42; margin-bottom: 5px;"><?php echo htmlspecialchars($emp_c['stat_val_4']); ?></div>
                    <div style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; color: rgba(255,255,255,0.7); letter-spacing: 0.05em;"><?php echo htmlspecialchars($emp_c['stat_lbl_4']); ?></div>
                </div>
            </div>
        </section>

        <!-- 3. Sección: Importancia del Carnet (Valor Institucional) -->
        <section class="section-padding container" style="border-top: 1px solid var(--border);">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle"><?php echo htmlspecialchars($emp_c['why_badge']); ?></span>
                <h2><?php echo htmlspecialchars($emp_c['why_title']); ?></h2>
                <p><?php echo htmlspecialchars($emp_c['why_subtitle']); ?></p>
            </div>

            <div class="grid-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px;">
                <!-- Tarjeta 1: Seguridad -->
                <div class="importance-card">
                    <div style="color: var(--primary); margin-bottom: 1.25rem;">
                        <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.2rem; font-family: var(--font-heading); margin-bottom: 0.75rem; color: var(--dark); font-weight: 500;">
                        <?php echo htmlspecialchars($emp_c['why_card1_title']); ?>
                    </h3>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                        <?php echo htmlspecialchars($emp_c['why_card1_desc']); ?>
                    </p>
                </div>

                <!-- Tarjeta 2: Imagen de Marca -->
                <div class="importance-card">
                    <div style="color: var(--primary); margin-bottom: 1.25rem;">
                        <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.2rem; font-family: var(--font-heading); margin-bottom: 0.75rem; color: var(--dark); font-weight: 500;">
                        <?php echo htmlspecialchars($emp_c['why_card2_title']); ?>
                    </h3>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                        <?php echo htmlspecialchars($emp_c['why_card2_desc']); ?>
                    </p>
                </div>

                <!-- Tarjeta 3: Pertenencia y Orgullo -->
                <div class="importance-card">
                    <div style="color: var(--primary); margin-bottom: 1.25rem;">
                        <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.2rem; font-family: var(--font-heading); margin-bottom: 0.75rem; color: var(--dark); font-weight: 500;">
                        <?php echo htmlspecialchars($emp_c['why_card3_title']); ?>
                    </h3>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                        <?php echo htmlspecialchars($emp_c['why_card3_desc']); ?>
                    </p>
                </div>

                <!-- Tarjeta 4: Organización Interna -->
                <div class="importance-card">
                    <div style="color: var(--primary); margin-bottom: 1.25rem;">
                        <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.2rem; font-family: var(--font-heading); margin-bottom: 0.75rem; color: var(--dark); font-weight: 500;">
                        <?php echo htmlspecialchars($emp_c['why_card4_title']); ?>
                    </h3>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                        <?php echo htmlspecialchars($emp_c['why_card4_desc']); ?>
                    </p>
                </div>
            </div>
        </section>

        <!-- 4. Sección: Tipos de Carnets y Formatos (Dark Mode Contrast) -->
        <section id="tipos-carnets" style="background: #121212; color: white; padding: 5.5rem 0;">
            <div class="container">
                <div class="section-header" style="margin-bottom: 3.5rem; max-width: 700px;">
                    <span class="section-subtitle" style="color: var(--primary); border-color: var(--primary);"><?php echo htmlspecialchars($emp_c['types_badge']); ?></span>
                    <h2 style="color: white;"><?php echo htmlspecialchars($emp_c['types_title']); ?></h2>
                    <p style="color: rgba(255,255,255,0.7);"><?php echo htmlspecialchars($emp_c['types_subtitle']); ?></p>
                </div>

                <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                    <!-- Formato 1: PVC Estándar -->
                    <div class="badge-type-card">
                        <div style="width: 100%; aspect-ratio: 1.6; overflow: hidden; background: #1c1b1b;">
                            <img src="uploads/<?php echo htmlspecialchars($emp_c['type_card1_img']); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($emp_c['type_card1_title']); ?>" loading="lazy" decoding="async">
                        </div>
                        <div style="padding: 2rem;">
                            <h3 style="color: #ffffff !important; font-size: 1.35rem; font-family: var(--font-heading); margin-bottom: 0.75rem; font-weight: 500;">
                                <?php echo htmlspecialchars($emp_c['type_card1_title']); ?>
                            </h3>
                            <p style="font-size: 0.88rem; color: rgba(255,255,255,0.85); line-height: 1.6; margin: 0;">
                                <?php echo htmlspecialchars($emp_c['type_card1_desc']); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Formato 2: Gran Formato -->
                    <div class="badge-type-card">
                        <div style="width: 100%; aspect-ratio: 1.6; overflow: hidden; background: #1c1b1b;">
                            <img src="uploads/<?php echo htmlspecialchars($emp_c['type_card2_img']); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($emp_c['type_card2_title']); ?>" loading="lazy" decoding="async">
                        </div>
                        <div style="padding: 2rem;">
                            <h3 style="color: #ffffff !important; font-size: 1.35rem; font-family: var(--font-heading); margin-bottom: 0.75rem; font-weight: 500;">
                                <?php echo htmlspecialchars($emp_c['type_card2_title']); ?>
                            </h3>
                            <p style="font-size: 0.88rem; color: rgba(255,255,255,0.85); line-height: 1.6; margin: 0;">
                                <?php echo htmlspecialchars($emp_c['type_card2_desc']); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Formato 3: RFID Proximidad -->
                    <div class="badge-type-card">
                        <div style="width: 100%; aspect-ratio: 1.6; overflow: hidden; background: #1c1b1b;">
                            <img src="uploads/<?php echo htmlspecialchars($emp_c['type_card3_img']); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($emp_c['type_card3_title']); ?>" loading="lazy" decoding="async">
                        </div>
                        <div style="padding: 2rem;">
                            <h3 style="color: #ffffff !important; font-size: 1.35rem; font-family: var(--font-heading); margin-bottom: 0.75rem; font-weight: 500;">
                                <?php echo htmlspecialchars($emp_c['type_card3_title']); ?>
                            </h3>
                            <p style="font-size: 0.88rem; color: rgba(255,255,255,0.85); line-height: 1.6; margin: 0;">
                                <?php echo htmlspecialchars($emp_c['type_card3_desc']); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. Sección: Acabados y Personalización Técnica -->
        <section class="section-padding container">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle"><?php echo htmlspecialchars($emp_c['finishes_badge']); ?></span>
                <h2><?php echo htmlspecialchars($emp_c['finishes_title']); ?></h2>
                <p><?php echo htmlspecialchars($emp_c['finishes_subtitle']); ?></p>
            </div>

            <div class="grid-2" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px;">
                <!-- Dúplex -->
                <div class="custom-feature-item">
                    <div style="color: var(--primary); font-size: 1.4rem; font-weight: bold; flex-shrink: 0; padding-top: 2px;">✓</div>
                    <div>
                        <h4 style="font-size: 1.1rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;"><?php echo htmlspecialchars($emp_c['finish_1_title']); ?></h4>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;"><?php echo htmlspecialchars($emp_c['finish_1_desc']); ?></p>
                    </div>
                </div>

                <!-- Acabado brillo/mate -->
                <div class="custom-feature-item">
                    <div style="color: var(--primary); font-size: 1.4rem; font-weight: bold; flex-shrink: 0; padding-top: 2px;">✓</div>
                    <div>
                        <h4 style="font-size: 1.1rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;"><?php echo htmlspecialchars($emp_c['finish_2_title']); ?></h4>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;"><?php echo htmlspecialchars($emp_c['finish_2_desc']); ?></p>
                    </div>
                </div>

                <!-- Perforación slot punch -->
                <div class="custom-feature-item">
                    <div style="color: var(--primary); font-size: 1.4rem; font-weight: bold; flex-shrink: 0; padding-top: 2px;">✓</div>
                    <div>
                        <h4 style="font-size: 1.1rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;"><?php echo htmlspecialchars($emp_c['finish_3_title']); ?></h4>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;"><?php echo htmlspecialchars($emp_c['finish_3_desc']); ?></p>
                    </div>
                </div>

                <!-- Códigos QR -->
                <div class="custom-feature-item">
                    <div style="color: var(--primary); font-size: 1.4rem; font-weight: bold; flex-shrink: 0; padding-top: 2px;">✓</div>
                    <div>
                        <h4 style="font-size: 1.1rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;"><?php echo htmlspecialchars($emp_c['finish_4_title']); ?></h4>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;"><?php echo htmlspecialchars($emp_c['finish_4_desc']); ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. Soluciones Corporativas a Medida -->
        <section class="section-padding" style="background: var(--surface-light); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
            <div class="container">
                <div class="section-header center" style="margin-bottom: 3.5rem;">
                    <span class="section-subtitle"><?php echo htmlspecialchars($emp_c['solutions_badge']); ?></span>
                    <h2><?php echo htmlspecialchars($emp_c['solutions_title']); ?></h2>
                    <p><?php echo htmlspecialchars($emp_c['solutions_subtitle']); ?></p>
                </div>

                <div class="grid-2" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px;">
                    <!-- Tarjeta 1: Carnets PVC -->
                    <div class="corp-card">
                        <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: white; border-bottom: 1px solid var(--border);">
                            <img src="uploads/<?php echo htmlspecialchars($emp_c['sol_1_img']); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($emp_c['sol_1_title']); ?>" loading="lazy" decoding="async">
                        </div>
                        <div style="padding: 2.25rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--dark); margin-bottom: 12px; font-weight: 500;">
                                <?php echo htmlspecialchars($emp_c['sol_1_title']); ?>
                            </h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 2rem;">
                                <?php echo htmlspecialchars($emp_c['sol_1_desc']); ?>
                            </p>
                            <div style="margin-top: auto; display: flex; gap: 12px;">
                                <a href="<?php echo htmlspecialchars($emp_c['sol_1_btn1_url']); ?>" class="btn btn-secondary" style="flex: 1; text-align: center; text-transform: none; font-size: 0.8rem; padding: 12px 0;">
                                    <?php echo htmlspecialchars($emp_c['sol_1_btn1_text']); ?>
                                </a>
                                <a href="<?php echo htmlspecialchars($emp_c['sol_1_btn2_url']); ?>" class="btn btn-primary" style="flex: 1; text-align: center; text-transform: none; font-size: 0.8rem; padding: 12px 0;">
                                    <?php echo htmlspecialchars($emp_c['sol_1_btn2_text']); ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 2: Cintas y Lanyards -->
                    <div class="corp-card">
                        <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: white; border-bottom: 1px solid var(--border);">
                            <img src="uploads/<?php echo htmlspecialchars($emp_c['sol_2_img']); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($emp_c['sol_2_title']); ?>" loading="lazy" decoding="async">
                        </div>
                        <div style="padding: 2.25rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--dark); margin-bottom: 12px; font-weight: 500;">
                                <?php echo htmlspecialchars($emp_c['sol_2_title']); ?>
                            </h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 2rem;">
                                <?php echo htmlspecialchars($emp_c['sol_2_desc']); ?>
                            </p>
                            <div style="margin-top: auto; display: flex; gap: 12px;">
                                <a href="<?php echo htmlspecialchars($emp_c['sol_2_btn1_url']); ?>" class="btn btn-secondary" style="flex: 1; text-align: center; text-transform: none; font-size: 0.8rem; padding: 12px 0;">
                                    <?php echo htmlspecialchars($emp_c['sol_2_btn1_text']); ?>
                                </a>
                                <a href="<?php echo htmlspecialchars($emp_c['sol_2_btn2_url']); ?>" class="btn btn-primary" style="flex: 1; text-align: center; text-transform: none; font-size: 0.8rem; padding: 12px 0;">
                                    <?php echo htmlspecialchars($emp_c['sol_2_btn2_text']); ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 3: Accesorios -->
                    <div class="corp-card">
                        <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: white; border-bottom: 1px solid var(--border);">
                            <img src="uploads/<?php echo htmlspecialchars($emp_c['sol_3_img']); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($emp_c['sol_3_title']); ?>" loading="lazy" decoding="async">
                        </div>
                        <div style="padding: 2.25rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--dark); margin-bottom: 12px; font-weight: 500;">
                                <?php echo htmlspecialchars($emp_c['sol_3_title']); ?>
                            </h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 2rem;">
                                <?php echo htmlspecialchars($emp_c['sol_3_desc']); ?>
                            </p>
                            <div style="margin-top: auto; display: flex; gap: 12px;">
                                <a href="<?php echo htmlspecialchars($emp_c['sol_3_btn1_url']); ?>" class="btn btn-secondary" style="flex: 1; text-align: center; text-transform: none; font-size: 0.8rem; padding: 12px 0;">
                                    <?php echo htmlspecialchars($emp_c['sol_3_btn1_text']); ?>
                                </a>
                                <a href="<?php echo htmlspecialchars($emp_c['sol_3_btn2_url']); ?>" class="btn btn-primary" style="flex: 1; text-align: center; text-transform: none; font-size: 0.8rem; padding: 12px 0;">
                                    <?php echo htmlspecialchars($emp_c['sol_3_btn2_text']); ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 4: Productos Corporativos Premium -->
                    <div class="corp-card">
                        <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: white; border-bottom: 1px solid var(--border);">
                            <img src="uploads/<?php echo htmlspecialchars($emp_c['sol_4_img']); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($emp_c['sol_4_title']); ?>" loading="lazy" decoding="async">
                        </div>
                        <div style="padding: 2.25rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--dark); margin-bottom: 12px; font-weight: 500;">
                                <?php echo htmlspecialchars($emp_c['sol_4_title']); ?>
                            </h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 2rem;">
                                <?php echo htmlspecialchars($emp_c['sol_4_desc']); ?>
                            </p>
                            <div style="margin-top: auto; display: flex; gap: 12px;">
                                <a href="<?php echo htmlspecialchars($emp_c['sol_4_btn1_url']); ?>" class="btn btn-secondary" style="flex: 1; text-align: center; text-transform: none; font-size: 0.8rem; padding: 12px 0;">
                                    <?php echo htmlspecialchars($emp_c['sol_4_btn1_text']); ?>
                                </a>
                                <a href="<?php echo htmlspecialchars($emp_c['sol_4_btn2_url']); ?>" class="btn btn-primary" style="flex: 1; text-align: center; text-transform: none; font-size: 0.8rem; padding: 12px 0;">
                                    <?php echo htmlspecialchars($emp_c['sol_4_btn2_text']); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. Sección: Combos y Combinaciones Inteligentes -->
        <section id="combos" class="section-padding container">
            <div class="section-header center" style="margin-bottom: 3.5rem;">
                <span class="section-subtitle"><?php echo htmlspecialchars($emp_c['combos_badge']); ?></span>
                <h2><?php echo htmlspecialchars($emp_c['combos_title']); ?></h2>
                <p><?php echo htmlspecialchars($emp_c['combos_subtitle']); ?></p>
            </div>

            <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <!-- COMBO 1 -->
                <div class="combo-card">
                    <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: #fff; border-bottom: 1px solid var(--border);">
                        <img src="uploads/<?php echo htmlspecialchars($emp_c['combo_1_img']); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($emp_c['combo_1_title']); ?>" loading="lazy" decoding="async">
                    </div>
                    <div style="padding: 2rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <span class="combo-tag"><?php echo htmlspecialchars($emp_c['combo_1_tag']); ?></span>
                        <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--dark); margin-bottom: 10px; font-weight: 500;">
                            <?php echo htmlspecialchars($emp_c['combo_1_title']); ?>
                        </h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem;">
                            <?php echo htmlspecialchars($emp_c['combo_1_desc']); ?>
                        </p>
                        
                        <h4 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 10px; font-weight: 700;">Productos incluidos:</h4>
                        <ul style="padding-left: 18px; margin: 0 0 2rem 0; font-size: 0.85rem; line-height: 1.8; color: var(--dark);">
                            <?php 
                            $items1 = explode("
", trim($emp_c['combo_1_items']));
                            foreach ($items1 as $item_text):
                                if (!empty(trim($item_text))):
                            ?>
                                <li><?php echo htmlspecialchars(trim($item_text)); ?></li>
                            <?php endif; endforeach; ?>
                        </ul>
                        
                        <button class="btn btn-primary btn-add-combo" data-combo="basico" style="width: 100%; text-align: center; text-transform: none; font-weight: 600; padding: 12px 0; margin-top: auto;">
                            Agregar Combo a Cotización
                        </button>
                    </div>
                </div>

                <!-- COMBO 2 -->
                <div class="combo-card">
                    <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: #fff; border-bottom: 1px solid var(--border);">
                        <img src="uploads/<?php echo htmlspecialchars($emp_c['combo_2_img']); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($emp_c['combo_2_title']); ?>" loading="lazy" decoding="async">
                    </div>
                    <div style="padding: 2rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <span class="combo-tag" style="background: rgba(99, 174, 44, 0.15);"><?php echo htmlspecialchars($emp_c['combo_2_tag']); ?></span>
                        <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--dark); margin-bottom: 10px; font-weight: 500;">
                            <?php echo htmlspecialchars($emp_c['combo_2_title']); ?>
                        </h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem;">
                            <?php echo htmlspecialchars($emp_c['combo_2_desc']); ?>
                        </p>
                        
                        <h4 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 10px; font-weight: 700;">Productos incluidos:</h4>
                        <ul style="padding-left: 18px; margin: 0 0 2rem 0; font-size: 0.85rem; line-height: 1.8; color: var(--dark);">
                            <?php 
                            $items2 = explode("
", trim($emp_c['combo_2_items']));
                            foreach ($items2 as $item_text):
                                if (!empty(trim($item_text))):
                            ?>
                                <li><?php echo htmlspecialchars(trim($item_text)); ?></li>
                            <?php endif; endforeach; ?>
                        </ul>
                        
                        <button class="btn btn-primary btn-add-combo" data-combo="ejecutivo" style="width: 100%; text-align: center; text-transform: none; font-weight: 600; padding: 12px 0; margin-top: auto;">
                            Agregar Combo a Cotización
                        </button>
                    </div>
                </div>

                <!-- COMBO 3 -->
                <div class="combo-card">
                    <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: #fff; border-bottom: 1px solid var(--border);">
                        <img src="uploads/<?php echo htmlspecialchars($emp_c['combo_3_img']); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($emp_c['combo_3_title']); ?>" loading="lazy" decoding="async">
                    </div>
                    <div style="padding: 2rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <span class="combo-tag" style="background: rgba(99,174,44,0.2); color: #3b6d13;"><?php echo htmlspecialchars($emp_c['combo_3_tag']); ?></span>
                        <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--dark); margin-bottom: 10px; font-weight: 500;">
                            <?php echo htmlspecialchars($emp_c['combo_3_title']); ?>
                        </h3>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem;">
                            <?php echo htmlspecialchars($emp_c['combo_3_desc']); ?>
                        </p>
                        
                        <h4 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 10px; font-weight: 700;">Productos incluidos:</h4>
                        <ul style="padding-left: 18px; margin: 0 0 2rem 0; font-size: 0.85rem; line-height: 1.8; color: var(--dark);">
                            <?php 
                            $items3 = explode("
", trim($emp_c['combo_3_items']));
                            foreach ($items3 as $item_text):
                                if (!empty(trim($item_text))):
                            ?>
                                <li><?php echo htmlspecialchars(trim($item_text)); ?></li>
                            <?php endif; endforeach; ?>
                        </ul>
                        
                        <button class="btn btn-primary btn-add-combo" data-combo="premium" style="width: 100%; text-align: center; text-transform: none; font-weight: 600; padding: 12px 0; margin-top: auto;">
                            Agregar Combo a Cotización
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. Sección: Ventajas Corporativas -->
        <section class="section-padding" style="background: var(--surface-light); border-top: 1px solid var(--border);">
            <div class="container">
                <div class="section-header center" style="margin-bottom: 3.5rem;">
                    <span class="section-subtitle"><?php echo htmlspecialchars($emp_c['adv_badge']); ?></span>
                    <h2><?php echo htmlspecialchars($emp_c['adv_title']); ?></h2>
                    <p><?php echo htmlspecialchars($emp_c['adv_subtitle']); ?></p>
                </div>

                <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                    <div style="background: white; border: 1px solid var(--border); padding: 2.25rem; border-radius: 8px;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 10px;">
                            <?php echo htmlspecialchars($emp_c['adv_1_title']); ?>
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin: 0;">
                            <?php echo htmlspecialchars($emp_c['adv_1_desc']); ?>
                        </p>
                    </div>
                    <div style="background: white; border: 1px solid var(--border); padding: 2.25rem; border-radius: 8px;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 10px;">
                            <?php echo htmlspecialchars($emp_c['adv_2_title']); ?>
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin: 0;">
                            <?php echo htmlspecialchars($emp_c['adv_2_desc']); ?>
                        </p>
                    </div>
                    <div style="background: white; border: 1px solid var(--border); padding: 2.25rem; border-radius: 8px;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 10px;">
                            <?php echo htmlspecialchars($emp_c['adv_3_title']); ?>
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin: 0;">
                            <?php echo htmlspecialchars($emp_c['adv_3_desc']); ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 9. CTA Corporativo Final -->
        <section class="section-padding" style="background: linear-gradient(135deg, #10140f 0%, #1c221a 100%); color: white; text-align: center; border-top: 1px solid var(--border);">
            <div class="container" style="max-width: 780px;">
                <span style="color: var(--primary); font-weight: 700; font-size: 0.75rem; letter-spacing: 0.1em; text-transform: uppercase;">
                    <?php echo htmlspecialchars($emp_c['cta_badge']); ?>
                </span>
                <h2 style="font-family: var(--font-heading); font-size: 2.4rem; color: white; margin-top: 15px; margin-bottom: 15px; font-weight: 400;">
                    <?php echo htmlspecialchars($emp_c['cta_title']); ?>
                </h2>
                <p style="color: rgba(255,255,255,0.8); font-size: 1rem; line-height: 1.6; margin-bottom: 2rem;">
                    <?php echo htmlspecialchars($emp_c['cta_desc']); ?>
                </p>
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <a href="cotizacion.php" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600; text-transform: none;">
                        <?php echo htmlspecialchars($emp_c['cta_btn1_text']); ?>
                    </a>
                    <a href="https://wa.me/<?php echo $emp_wa_clean; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="background: rgba(255,255,255,0.08); color: white; border: 1px solid rgba(255,255,255,0.2); padding: 14px 30px; font-weight: 600; text-transform: none;">
                        <?php echo htmlspecialchars($emp_c['cta_btn2_text']); ?>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js?v=6.3" defer></script>
    <script src="js/animations.js" defer></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Manejador interactivo para agregar combos al carrito (Add to Quote list)
            const comboData = {
                basico: [
                    { name: "Carnet PVC Estándar (Combo)", slug: "credenciales-pvc", qty: 100, price: 1.20, snapshot: "uploads/carnet_mockup.webp" },
                    { name: "Funda de PVC flexible (Combo)", slug: "porta-credenciales", qty: 100, price: 0.40, snapshot: "uploads/fundas.webp" },
                    { name: "Cinta lisa sin impresión (Combo)", slug: "cintas-sin-impresion", qty: 100, price: 0.80, snapshot: "uploads/cintas_mockup.webp" }
                ],
                ejecutivo: [
                    { name: "Carnet PVC Estándar (Combo)", slug: "credenciales-pvc", qty: 100, price: 1.20, snapshot: "uploads/carnet_mockup.webp" },
                    { name: "Porta Carnet Rígido (Combo)", slug: "porta-carnets", qty: 100, price: 0.50, snapshot: "uploads/llavero.webp" },
                    { name: "Yoyo retráctil corporativo (Combo)", slug: "accesorios-identificacion", qty: 100, price: 0.60, snapshot: "uploads/yoyos.webp" }
                ],
                premium: [
                    { name: "Carnet PVC Estándar (Combo)", slug: "credenciales-pvc", qty: 100, price: 1.20, snapshot: "uploads/carnet_mockup.webp" },
                    { name: "Porta Carnet Rígido Premium (Combo)", slug: "porta-carnets", qty: 100, price: 0.50, snapshot: "uploads/llavero.webp" },
                    { name: "Cinta personalizada full color (Combo)", slug: "cintas-full-color", qty: 100, price: 1.80, snapshot: "uploads/cintas_full_color.webp" }
                ]
            };

            const addComboBtns = document.querySelectorAll(".btn-add-combo");
            addComboBtns.forEach(btn => {
                btn.addEventListener("click", function() {
                    const comboKey = this.getAttribute("data-combo");
                    const items = comboData[comboKey];
                    
                    if (!items) return;

                    const oldText = this.textContent;
                    this.textContent = "Agregando...";
                    this.disabled = true;

                    const formData = new FormData();
                    formData.append("items", JSON.stringify(items));

                    fetch("cart-action.php?action=add_multiple", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.textContent = oldText;
                        this.disabled = false;

                        if (data.success) {
                            const badges = document.querySelectorAll(".cart-count");
                            badges.forEach(b => {
                                b.textContent = data.cart_count;
                                b.style.display = "flex";
                            });

                            if (window.showNotification) {
                                window.showNotification("Combo agregado a tu lista de cotización");
                            }

                            const cartIcon = document.querySelector(".cart-icon-btn");
                            if (cartIcon) {
                                cartIcon.click();
                            }
                        }
                    })
                    .catch(err => {
                        console.error("Error al agregar combo:", err);
                        this.textContent = oldText;
                        this.disabled = false;
                    });
                });
            });
        });
    </script>
</body>
</html>
