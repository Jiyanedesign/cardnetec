<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Persistencia y respaldo por Cookie para servidores con sesiones estrictas
if (!isset($_SESSION['admin_logged']) && isset($_COOKIE['cardnet_admin_logged']) && $_COOKIE['cardnet_admin_logged'] === 'cardnet_auth_2026_ok') {
    $_SESSION['admin_logged'] = true;
    $_SESSION['admin_name'] = 'CardNet Admin';
}
if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit;
}
require_once '../db.php';

$message = '';
$error = '';

// Procesar formulario de actualización de contenidos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_tagua_content'])) {
    try {
        $upload_dir = '../uploads/';
        $hero_image_val = $_POST['existing_hero_image'] ?? 'tagua_hero_bg.jpg';

        if (isset($_FILES['hero_image_file']) && $_FILES['hero_image_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['hero_image_file']['tmp_name'];
            $file_name = $_FILES['hero_image_file']['name'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $new_filename = 'tagua_hero_' . time() . '.' . $ext;
                if (move_uploaded_file($file_tmp, $upload_dir . $new_filename)) {
                    $hero_image_val = $new_filename;
                }
            }
        }

        $stmtUpsert = $pdo->prepare("INSERT INTO tagua_content (content_key, content_value) 
                                     VALUES (?, ?) 
                                     ON DUPLICATE KEY UPDATE content_value = VALUES(content_value)");

        // Guardar cada campo enviado
        foreach ($_POST as $key => $val) {
            if ($key === 'save_tagua_content' || $key === 'existing_hero_image') continue;
            $stmtUpsert->execute([$key, trim($val)]);
        }

        // Guardar imagen del Hero
        $stmtUpsert->execute(['hero_image', $hero_image_val]);

        $message = 'Información de la página de Tagua guardada y actualizada correctamente.';
    } catch (PDOException $e) {
        $error = 'Error al guardar la información: ' . $e->getMessage();
    }
}

// Cargar contenidos actuales
$tagua_c = getTaguaContent($pdo);

// Obtener categoría Tagua
$catTagua = $pdo->query("SELECT * FROM categorias WHERE slug = 'tagua'")->fetch();
$tagua_cat_id = $catTagua ? $catTagua['id'] : 0;

// Obtener productos de Tagua
$tagua_prods = [];
if ($tagua_cat_id) {
    $stmtProds = $pdo->prepare("SELECT * FROM productos WHERE category_id = ? OR slug LIKE '%tagua%' ORDER BY CASE WHEN order_val IS NULL OR order_val = 0 THEN 999999 ELSE order_val END ASC, id ASC");
    $stmtProds->execute([$tagua_cat_id]);
    $tagua_prods = $stmtProds->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Página de Tagua | CardNet Admin</title>
    <link rel="stylesheet" href="../css/base.css?v=6.3">
    <link rel="stylesheet" href="../css/layout.css?v=6.3">
    <link rel="stylesheet" href="../css/components.css?v=6.3">
    <link rel="stylesheet" href="../css/admin.css?v=6.3">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: var(--surface-light);
        }
        .sidebar {
            width: 250px;
            background-color: var(--dark);
            color: white;
            padding: 2rem 1.5rem;
            flex-shrink: 0;
        }
        .sidebar-logo {
            max-width: 140px;
            margin-bottom: 2rem;
            filter: brightness(0) invert(1);
        }
        .nav-admin {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .nav-admin-link {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            padding: 10px 15px;
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition-fast);
        }
        .nav-admin-link:hover, .nav-admin-link.active {
            color: white;
            background-color: var(--primary);
        }
        .main-content {
            flex-grow: 1;
            padding: 3rem;
            box-sizing: border-box;
            max-width: calc(100vw - 250px);
        }
        .admin-section-box {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 2rem;
            margin-bottom: 2.25rem;
        }
        .admin-section-title {
            font-family: var(--font-heading);
            font-size: 1.25rem;
            color: var(--dark);
            border-bottom: 1px solid var(--border);
            padding-bottom: 0.75rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-grid-4 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
            color: var(--text-dark);
        }
        .form-control {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-size: 0.88rem;
            background: white;
            box-sizing: border-box;
        }
        textarea.form-control {
            min-height: 80px;
            resize: vertical;
        }
        .alert {
            padding: 12px 18px;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .alert-success { background-color: #DEF7EC; color: #03543F; }
        .alert-danger { background-color: #FDE8E8; color: #9B1C1C; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            margin-top: 1rem;
        }
        th, td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            font-size: 0.88rem;
        }
        th { background: var(--surface-light); }
        .tab-btn {
            background: none;
            border: 1px solid var(--border);
            padding: 10px 18px;
            border-radius: 20px;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .tab-btn.active, .tab-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <img src="../images/logo.png?v=2.0" alt="CardNet Logo" class="sidebar-logo">
        <nav class="nav-admin">
            <a href="index.php" class="nav-admin-link">Dashboard</a>
            <a href="categorias.php" class="nav-admin-link">Categorías</a>
            <a href="secciones.php" class="nav-admin-link">Secciones Home</a>
            <a href="tagua.php" class="nav-admin-link active">Tagua</a>
            <a href="etiquetas.php" class="nav-admin-link">Etiquetas</a>
            <a href="productos.php" class="nav-admin-link">Productos</a>
            <a href="materiales.php" class="nav-admin-link">Materiales</a>
            <a href="carrusel.php" class="nav-admin-link">Carrusel Hero</a>
            <a href="antes-despues.php" class="nav-admin-link">Antes y Después</a>
            <a href="clientes.php" class="nav-admin-link">Logos Clientes</a>
            <a href="credenciales.php" class="nav-admin-link">Credenciales QR</a>
            <a href="configuracion.php" class="nav-admin-link">Configuración</a>
            <a href="logout.php" class="nav-admin-link" style="margin-top: 2rem; color: #FCA5A5;">Cerrar Sesión</a>
        </nav>
    </div>

    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1 style="font-family: var(--font-heading); font-size: 2rem; margin-bottom: 0.4rem;">Gestión de Página de Tagua</h1>
                <p style="color: var(--text-muted); font-size: 0.92rem; margin: 0;">Personaliza todos los textos, imágenes, beneficios, pasos de taller, preguntas frecuentes y productos de la página de Tagua.</p>
            </div>
            <div>
                <a href="../tagua.php" target="_blank" class="btn btn-secondary" style="font-size: 0.85rem; padding: 10px 20px; text-transform: none;">
                    Ver Página en Vivo ↗
                </a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="save_tagua_content" value="1">
            <input type="hidden" name="existing_hero_image" value="<?php echo htmlspecialchars($tagua_c['hero_image']); ?>">

            <!-- 1. HERO PRINCIPAL & ESTADÍSTICAS -->
            <div class="admin-section-box">
                <div class="admin-section-title">
                    <span>1. Encabezado Hero y Métricas Técnicas</span>
                </div>
                
                <div class="form-group">
                    <label>Badge Superior del Hero:</label>
                    <input type="text" name="hero_badge" class="form-control" value="<?php echo htmlspecialchars($tagua_c['hero_badge']); ?>">
                </div>

                <div class="form-group">
                    <label>Título Principal (H1):</label>
                    <input type="text" name="hero_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['hero_title']); ?>">
                </div>

                <div class="form-group">
                    <label>Subtítulo Explicativo:</label>
                    <textarea name="hero_subtitle" class="form-control"><?php echo htmlspecialchars($tagua_c['hero_subtitle']); ?></textarea>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Imagen de Fondo del Hero:</label>
                        <input type="file" name="hero_image_file" class="form-control" accept="image/*">
                        <?php if (!empty($tagua_c['hero_image'])): ?>
                            <div style="margin-top: 6px; display: flex; align-items: center; gap: 8px;">
                                <img src="<?php echo htmlspecialchars(getUploadedImgUrl($tagua_c['hero_image'])); ?>" style="width: 70px; height: 44px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border);" alt="Hero actual">
                                <small style="color: var(--text-muted);">Actual: <strong><?php echo htmlspecialchars($tagua_c['hero_image']); ?></strong></small>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Texto Botón Principal:</label>
                        <input type="text" name="hero_btn_text" class="form-control" value="<?php echo htmlspecialchars($tagua_c['hero_btn_text']); ?>">
                    </div>
                </div>

                <h4 style="margin: 1.5rem 0 1rem 0; font-size: 0.95rem; color: var(--primary);">4 Métricas de Garantía (Barra de Estadísticas):</h4>
                <div class="form-grid-4">
                    <div style="background: var(--surface-light); padding: 12px; border-radius: 6px;">
                        <label style="font-size: 0.8rem;">Métrica 1 Título:</label>
                        <input type="text" name="stat_1_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['stat_1_title']); ?>" style="margin-bottom: 6px;">
                        <label style="font-size: 0.8rem;">Métrica 1 Detalle:</label>
                        <input type="text" name="stat_1_desc" class="form-control" value="<?php echo htmlspecialchars($tagua_c['stat_1_desc']); ?>">
                    </div>

                    <div style="background: var(--surface-light); padding: 12px; border-radius: 6px;">
                        <label style="font-size: 0.8rem;">Métrica 2 Título:</label>
                        <input type="text" name="stat_2_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['stat_2_title']); ?>" style="margin-bottom: 6px;">
                        <label style="font-size: 0.8rem;">Métrica 2 Detalle:</label>
                        <input type="text" name="stat_2_desc" class="form-control" value="<?php echo htmlspecialchars($tagua_c['stat_2_desc']); ?>">
                    </div>

                    <div style="background: var(--surface-light); padding: 12px; border-radius: 6px;">
                        <label style="font-size: 0.8rem;">Métrica 3 Título:</label>
                        <input type="text" name="stat_3_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['stat_3_title']); ?>" style="margin-bottom: 6px;">
                        <label style="font-size: 0.8rem;">Métrica 3 Detalle:</label>
                        <input type="text" name="stat_3_desc" class="form-control" value="<?php echo htmlspecialchars($tagua_c['stat_3_desc']); ?>">
                    </div>

                    <div style="background: var(--surface-light); padding: 12px; border-radius: 6px;">
                        <label style="font-size: 0.8rem;">Métrica 4 Título:</label>
                        <input type="text" name="stat_4_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['stat_4_title']); ?>" style="margin-bottom: 6px;">
                        <label style="font-size: 0.8rem;">Métrica 4 Detalle:</label>
                        <input type="text" name="stat_4_desc" class="form-control" value="<?php echo htmlspecialchars($tagua_c['stat_4_desc']); ?>">
                    </div>
                </div>
            </div>

            <!-- 2. ORIGEN Y SUSTENTABILIDAD (ESG) -->
            <div class="admin-section-box">
                <div class="admin-section-title">
                    <span>2. Origen, Sostenibilidad y 4 Beneficios ESG</span>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Badge de Sección:</label>
                        <input type="text" name="esg_badge" class="form-control" value="<?php echo htmlspecialchars($tagua_c['esg_badge']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Título de Sección:</label>
                        <input type="text" name="esg_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['esg_title']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Párrafo Explicativo del Marfil Vegetal:</label>
                    <textarea name="esg_description" class="form-control"><?php echo htmlspecialchars($tagua_c['esg_description']); ?></textarea>
                </div>

                <h4 style="margin: 1.5rem 0 1rem 0; font-size: 0.95rem; color: var(--primary);">4 Tarjetas de Beneficios Técnicos:</h4>
                <div class="form-grid-2">
                    <div style="background: var(--surface-light); padding: 14px; border-radius: 6px; margin-bottom: 10px;">
                        <label>Beneficio 1 Título:</label>
                        <input type="text" name="benefit_1_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['benefit_1_title']); ?>" style="margin-bottom: 8px;">
                        <label>Beneficio 1 Descripción:</label>
                        <textarea name="benefit_1_desc" class="form-control"><?php echo htmlspecialchars($tagua_c['benefit_1_desc']); ?></textarea>
                    </div>

                    <div style="background: var(--surface-light); padding: 14px; border-radius: 6px; margin-bottom: 10px;">
                        <label>Beneficio 2 Título:</label>
                        <input type="text" name="benefit_2_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['benefit_2_title']); ?>" style="margin-bottom: 8px;">
                        <label>Beneficio 2 Descripción:</label>
                        <textarea name="benefit_2_desc" class="form-control"><?php echo htmlspecialchars($tagua_c['benefit_2_desc']); ?></textarea>
                    </div>

                    <div style="background: var(--surface-light); padding: 14px; border-radius: 6px;">
                        <label>Beneficio 3 Título:</label>
                        <input type="text" name="benefit_3_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['benefit_3_title']); ?>" style="margin-bottom: 8px;">
                        <label>Beneficio 3 Descripción:</label>
                        <textarea name="benefit_3_desc" class="form-control"><?php echo htmlspecialchars($tagua_c['benefit_3_desc']); ?></textarea>
                    </div>

                    <div style="background: var(--surface-light); padding: 14px; border-radius: 6px;">
                        <label>Beneficio 4 Título:</label>
                        <input type="text" name="benefit_4_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['benefit_4_title']); ?>" style="margin-bottom: 8px;">
                        <label>Beneficio 4 Descripción:</label>
                        <textarea name="benefit_4_desc" class="form-control"><?php echo htmlspecialchars($tagua_c['benefit_4_desc']); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- 3. CATÁLOGO INTRO & CAJA A MEDIDA -->
            <div class="admin-section-box">
                <div class="admin-section-title">
                    <span>3. Textos del Catálogo y Caja de Fabricación Especial</span>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Título del Catálogo:</label>
                        <input type="text" name="catalog_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['catalog_title']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Subtítulo del Catálogo:</label>
                        <input type="text" name="catalog_description" class="form-control" value="<?php echo htmlspecialchars($tagua_c['catalog_description']); ?>">
                    </div>
                </div>

                <div class="form-grid-2" style="margin-top: 10px;">
                    <div class="form-group">
                        <label>Caja Especial - Título:</label>
                        <input type="text" name="custom_box_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['custom_box_title']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Caja Especial - Texto Botón:</label>
                        <input type="text" name="custom_box_btn_text" class="form-control" value="<?php echo htmlspecialchars($tagua_c['custom_box_btn_text']); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Caja Especial - Descripción:</label>
                    <textarea name="custom_box_desc" class="form-control"><?php echo htmlspecialchars($tagua_c['custom_box_desc']); ?></textarea>
                </div>
            </div>

            <!-- 4. PROTOCOLO DE TALLER (4 PASOS) -->
            <div class="admin-section-box">
                <div class="admin-section-title">
                    <span>4. Proceso y Protocolo de Taller (4 Pasos)</span>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Título de la Sección:</label>
                        <input type="text" name="process_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['process_title']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Descripción de la Sección:</label>
                        <input type="text" name="process_description" class="form-control" value="<?php echo htmlspecialchars($tagua_c['process_description']); ?>">
                    </div>
                </div>

                <div class="form-grid-2" style="margin-top: 10px;">
                    <div style="background: var(--surface-light); padding: 12px; border-radius: 6px; margin-bottom: 10px;">
                        <label>Paso 01 - Título:</label>
                        <input type="text" name="step_1_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['step_1_title']); ?>" style="margin-bottom: 6px;">
                        <label>Paso 01 - Descripción:</label>
                        <textarea name="step_1_desc" class="form-control"><?php echo htmlspecialchars($tagua_c['step_1_desc']); ?></textarea>
                    </div>

                    <div style="background: var(--surface-light); padding: 12px; border-radius: 6px; margin-bottom: 10px;">
                        <label>Paso 02 - Título:</label>
                        <input type="text" name="step_2_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['step_2_title']); ?>" style="margin-bottom: 6px;">
                        <label>Paso 02 - Descripción:</label>
                        <textarea name="step_2_desc" class="form-control"><?php echo htmlspecialchars($tagua_c['step_2_desc']); ?></textarea>
                    </div>

                    <div style="background: var(--surface-light); padding: 12px; border-radius: 6px;">
                        <label>Paso 03 - Título:</label>
                        <input type="text" name="step_3_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['step_3_title']); ?>" style="margin-bottom: 6px;">
                        <label>Paso 03 - Descripción:</label>
                        <textarea name="step_3_desc" class="form-control"><?php echo htmlspecialchars($tagua_c['step_3_desc']); ?></textarea>
                    </div>

                    <div style="background: var(--surface-light); padding: 12px; border-radius: 6px;">
                        <label>Paso 04 - Título:</label>
                        <input type="text" name="step_4_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['step_4_title']); ?>" style="margin-bottom: 6px;">
                        <label>Paso 04 - Descripción:</label>
                        <textarea name="step_4_desc" class="form-control"><?php echo htmlspecialchars($tagua_c['step_4_desc']); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- 5. PREGUNTAS FRECUENTES (FAQ) -->
            <div class="admin-section-box">
                <div class="admin-section-title">
                    <span>5. Preguntas Frecuentes (FAQ)</span>
                </div>

                <div class="form-group" style="background: var(--surface-light); padding: 14px; border-radius: 6px; margin-bottom: 12px;">
                    <label>Pregunta 1:</label>
                    <input type="text" name="faq_1_q" class="form-control" value="<?php echo htmlspecialchars($tagua_c['faq_1_q']); ?>" style="margin-bottom: 8px;">
                    <label>Respuesta 1:</label>
                    <textarea name="faq_1_a" class="form-control"><?php echo htmlspecialchars($tagua_c['faq_1_a']); ?></textarea>
                </div>

                <div class="form-group" style="background: var(--surface-light); padding: 14px; border-radius: 6px; margin-bottom: 12px;">
                    <label>Pregunta 2:</label>
                    <input type="text" name="faq_2_q" class="form-control" value="<?php echo htmlspecialchars($tagua_c['faq_2_q']); ?>" style="margin-bottom: 8px;">
                    <label>Respuesta 2:</label>
                    <textarea name="faq_2_a" class="form-control"><?php echo htmlspecialchars($tagua_c['faq_2_a']); ?></textarea>
                </div>

                <div class="form-group" style="background: var(--surface-light); padding: 14px; border-radius: 6px; margin-bottom: 12px;">
                    <label>Pregunta 3:</label>
                    <input type="text" name="faq_3_q" class="form-control" value="<?php echo htmlspecialchars($tagua_c['faq_3_q']); ?>" style="margin-bottom: 8px;">
                    <label>Respuesta 3:</label>
                    <textarea name="faq_3_a" class="form-control"><?php echo htmlspecialchars($tagua_c['faq_3_a']); ?></textarea>
                </div>

                <div class="form-group" style="background: var(--surface-light); padding: 14px; border-radius: 6px;">
                    <label>Pregunta 4:</label>
                    <input type="text" name="faq_4_q" class="form-control" value="<?php echo htmlspecialchars($tagua_c['faq_4_q']); ?>" style="margin-bottom: 8px;">
                    <label>Respuesta 4:</label>
                    <textarea name="faq_4_a" class="form-control"><?php echo htmlspecialchars($tagua_c['faq_4_a']); ?></textarea>
                </div>
            </div>

            <!-- 6. BANNER FINAL CTA -->
            <div class="admin-section-box">
                <div class="admin-section-title">
                    <span>6. Banner de Cierre y Llamado a la Acción (CTA)</span>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Badge CTA:</label>
                        <input type="text" name="cta_badge" class="form-control" value="<?php echo htmlspecialchars($tagua_c['cta_badge']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Título CTA:</label>
                        <input type="text" name="cta_title" class="form-control" value="<?php echo htmlspecialchars($tagua_c['cta_title']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Subtítulo / Mensaje CTA:</label>
                    <textarea name="cta_subtitle" class="form-control"><?php echo htmlspecialchars($tagua_c['cta_subtitle']); ?></textarea>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Texto Botón Cotizar:</label>
                        <input type="text" name="cta_btn_text" class="form-control" value="<?php echo htmlspecialchars($tagua_c['cta_btn_text']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Texto Botón Asesor WhatsApp:</label>
                        <input type="text" name="cta_btn_sec_text" class="form-control" value="<?php echo htmlspecialchars($tagua_c['cta_btn_sec_text']); ?>">
                    </div>
                </div>
            </div>

            <div style="position: sticky; bottom: 20px; background: white; padding: 1.25rem 2rem; border-radius: var(--radius-md); box-shadow: 0 -4px 20px rgba(0,0,0,0.1); border: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; z-index: 100;">
                <span style="font-size: 0.9rem; color: var(--text-muted);">Guarda los cambios realizados en cualquier sección.</span>
                <button type="submit" class="btn btn-primary" style="padding: 12px 32px; font-weight: 600; text-transform: none;">Guardar Todos los Cambios</button>
            </div>
        </form>

        <!-- 7. GESTIÓN DE PRODUCTOS DE TAGUA -->
        <div class="admin-section-box" style="margin-top: 3rem;">
            <div class="admin-section-title">
                <span>Productos asignados a la Categoría Tagua (<?php echo count($tagua_prods); ?>)</span>
                <a href="productos.php?cat_id=<?php echo $tagua_cat_id; ?>" class="btn btn-primary" style="font-size: 0.8rem; padding: 8px 16px; text-transform: none;">
                    + Gestionar / Añadir Productos
                </a>
            </div>

            <?php if (empty($tagua_prods)): ?>
                <p style="color: var(--text-muted); font-size: 0.9rem;">No hay productos asignados a la categoría Tagua todavía.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 70px;">Foto</th>
                            <th>Nombre</th>
                            <th>SKU</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Orden</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tagua_prods as $tp): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars(getUploadedImgUrl($tp['image_main'] ?? '', 'uploads/tagua_llavero.jpg')); ?>" style="width: 50px; height: 50px; object-fit: contain; border-radius: 4px; border: 1px solid var(--border); background: white; padding: 2px;" alt="">
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($tp['name']); ?></strong><br>
                                    <small style="color: var(--text-muted);"><?php echo htmlspecialchars(mb_substr($tp['description_short'] ?? '', 0, 60)) . '...'; ?></small>
                                </td>
                                <td><code><?php echo htmlspecialchars($tp['sku'] ?? 'N/A'); ?></code></td>
                                <td><strong>$<?php echo number_format($tp['price'], 2); ?></strong></td>
                                <td><?php echo (int)($tp['stock'] ?? 0); ?> u.</td>
                                <td><?php echo (int)$tp['order_val']; ?></td>
                                <td style="text-align: right;">
                                    <a href="productos.php?edit=<?php echo $tp['id']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.75rem; text-transform: none;">Editar Producto</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
