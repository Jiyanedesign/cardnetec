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

// Procesar Eliminación de Producto de Tagua
if (isset($_GET['delete_product'])) {
    $del_id = (int)$_GET['delete_product'];
    try {
        $pdo->prepare("DELETE FROM productos WHERE id = ?")->execute([$del_id]);
        $message = 'Producto de Tagua eliminado correctamente.';
        if (isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'message' => $message]);
            exit;
        }
    } catch (PDOException $e) {
        $error = 'Error al eliminar producto: ' . $e->getMessage();
        if (isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        }
    }
}

// Procesar Orden Rápido de Producto de Tagua
if (isset($_POST['quick_order_id'])) {
    $q_id = (int)$_POST['quick_order_id'];
    $q_val = (int)$_POST['quick_order_val'];
    try {
        $pdo->prepare("UPDATE productos SET order_val = ? WHERE id = ?")->execute([$q_val, $q_id]);
        if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'message' => 'Orden actualizado']);
            exit;
        }
    } catch (PDOException $e) {
        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
}

// Obtener categoría Tagua
$catTagua = $pdo->query("SELECT * FROM categorias WHERE slug = 'tagua'")->fetch();
$tagua_cat_id = $catTagua ? (int)$catTagua['id'] : 0;

// Procesar Guardado/Edición de Producto de Tagua desde la Pestaña de Tagua
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_tagua_product'])) {
    $tp_id = isset($_POST['tagua_prod_id']) ? (int)$_POST['tagua_prod_id'] : 0;
    $tp_name = trim($_POST['tp_name'] ?? '');
    $tp_slug = trim($_POST['tp_slug'] ?? '');
    if (empty($tp_slug)) {
        $tp_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $tp_name)));
    }
    $tp_sku = trim($_POST['tp_sku'] ?? '');
    $tp_price = (float)($_POST['tp_price'] ?? 0);
    $tp_stock = (int)($_POST['tp_stock'] ?? 0);
    $tp_order_val = (int)($_POST['tp_order_val'] ?? 0);
    $tp_desc_short = trim($_POST['tp_description_short'] ?? '');
    $tp_desc_long = trim($_POST['tp_description_long'] ?? '');
    $tp_cta = trim($_POST['tp_cta_text'] ?? 'Cotizar');
    $tp_is_active = isset($_POST['tp_is_active']) ? 1 : 0;

    $upload_dir = '../uploads/products/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $tp_image = $_POST['existing_tp_image'] ?? '';
    if (isset($_FILES['tp_image']) && $_FILES['tp_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['tp_image']['tmp_name'];
        $file_name = $_FILES['tp_image']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $new_fn = 'tagua_p_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($file_tmp, $upload_dir . $new_fn)) {
                $webp_fn = convertToWebP($upload_dir . $new_fn);
                $tp_image = 'products/' . basename($webp_fn);
            }
        }
    }

    // Procesar fotos de galería (existentes y nuevas)
    $gallery_paths = [];
    if (!empty($_POST['existing_tp_gallery'])) {
        $gallery_paths = json_decode($_POST['existing_tp_gallery'], true) ?: [];
    }

    if (isset($_FILES['tp_gallery']) && !empty($_FILES['tp_gallery']['name'])) {
        $files = $_FILES['tp_gallery'];
        $fcount = is_array($files['name']) ? count($files['name']) : 0;
        for ($i = 0; $i < $fcount; $i++) {
            if (isset($files['error'][$i]) && $files['error'][$i] === UPLOAD_ERR_OK) {
                $file_tmp = $files['tmp_name'][$i];
                $file_name = $files['name'][$i];
                $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $new_fn = 'tagua_gal_' . time() . '_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($file_tmp, $upload_dir . $new_fn)) {
                        $webp_fn = convertToWebP($upload_dir . $new_fn);
                        $gallery_paths[] = 'products/' . basename($webp_fn);
                    }
                }
            }
        }
    }

    // Si no se asignó foto principal pero sí hay fotos en galería, promover la primera foto a principal
    if (empty($tp_image) && !empty($gallery_paths)) {
        $tp_image = $gallery_paths[0];
    }
    $gallery_json = json_encode(array_values(array_unique($gallery_paths)));

    if (empty($tp_name)) {
        $error = 'El nombre del producto de Tagua es obligatorio.';
    } else {
        if ($tp_id > 0) {
            try {
                $stmtUp = $pdo->prepare("UPDATE productos SET name = ?, slug = ?, sku = ?, price = ?, stock = ?, order_val = ?, description_short = ?, description_long = ?, cta_text = ?, is_active = ?, image_main = ?, gallery_images = ?, category = 'Tagua', category_id = ? WHERE id = ?");
                $stmtUp->execute([$tp_name, $tp_slug, $tp_sku, $tp_price, $tp_stock, $tp_order_val, $tp_desc_short, $tp_desc_long, $tp_cta, $tp_is_active, $tp_image, $gallery_json, $tagua_cat_id, $tp_id]);
                $message = 'Producto de Tagua actualizado correctamente.';
            } catch (PDOException $e) {
                $error = 'Error al actualizar producto: ' . $e->getMessage();
            }
        } else {
            try {
                $stmtIn = $pdo->prepare("INSERT INTO productos (category_id, category, name, slug, sku, price, stock, order_val, description_short, description_long, cta_text, is_active, is_featured, image_main, gallery_images) VALUES (?, 'Tagua', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)");
                $stmtIn->execute([$tagua_cat_id, $tp_name, $tp_slug, $tp_sku, $tp_price, $tp_stock, $tp_order_val, $tp_desc_short, $tp_desc_long, $tp_cta, $tp_is_active, $tp_image, $gallery_json]);
                $message = 'Producto de Tagua creado correctamente.';
            } catch (PDOException $e) {
                $error = 'Error al crear producto: ' . $e->getMessage();
            }
        }
        if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => empty($error), 'message' => $message ?: $error]);
            exit;
        }
    }
}

// Procesar formulario de actualización de contenidos de la página de Tagua
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
                    $webp_hero = convertToWebP($upload_dir . $new_filename);
                    $hero_image_val = basename($webp_hero);
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

// Cargar producto de Tagua en edición (si se pasó ?edit_prod=ID)
$edit_tagua_prod = null;
if (isset($_GET['edit_prod'])) {
    $ep_id = (int)$_GET['edit_prod'];
    $stmtEP = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmtEP->execute([$ep_id]);
    $edit_tagua_prod = $stmtEP->fetch();
}

// Obtener lista actualizada de productos de Tagua
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
    <!-- Favicon Oficial -->
    <link rel="icon" type="image/png" href="../favicon.png?v=2.0">
    <link rel="shortcut icon" href="../favicon.ico?v=2.0">
    <link rel="apple-touch-icon" href="../favicon.png?v=2.0">

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
        /* Galería de Fotos de Tagua */
        .gallery-preview {
            display: flex;
            gap: 12px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .gallery-img-wrap {
            position: relative;
            width: 86px;
            height: 86px;
            border: 2px solid var(--border);
            border-radius: 6px;
            overflow: hidden;
            cursor: grab;
            user-select: none;
            transition: transform 0.2s ease, border-color 0.2s ease, opacity 0.2s ease, box-shadow 0.2s ease;
            background: white;
        }
        .gallery-img-wrap:active {
            cursor: grabbing;
        }
        .gallery-img-wrap.dragging {
            opacity: 0.35;
            transform: scale(0.92);
            border-style: dashed;
            border-color: var(--primary);
        }
        .gallery-img-wrap.drag-over {
            transform: scale(1.08);
            border-color: var(--primary);
            box-shadow: 0 4px 14px rgba(99, 174, 44, 0.4);
        }
        .gallery-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            pointer-events: none;
        }
        .gallery-img-wrap .delete-gallery-img {
            position: absolute;
            top: 3px;
            right: 3px;
            background: rgba(239, 68, 68, 0.9);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s;
            border: none;
            line-height: 1;
            padding: 0;
            z-index: 10;
        }
        .gallery-img-wrap .delete-gallery-img:hover {
            background: rgba(220, 38, 38, 1);
        }
        .gallery-drag-badge {
            position: absolute;
            bottom: 3px;
            left: 3px;
            background: rgba(0, 0, 0, 0.65);
            color: #fff;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 10px;
            pointer-events: none;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <img src="../images/logo.png?v=2.0" alt="CardNet Logo" class="sidebar-logo">
        <nav class="nav-admin">
            <a href="index.php" class="nav-admin-link">Dashboard</a>
            <a href="carnets-empresas.php" class="nav-admin-link">Carnets y Empresas</a>
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
                <span style="font-size: 0.9rem; color: var(--text-muted);">Guarda los cambios realizados en cualquier sección de la página.</span>
                <button type="submit" class="btn btn-primary" style="padding: 12px 32px; font-weight: 600; text-transform: none;">Guardar Contenidos de Tagua</button>
            </div>
        </form>

        <!-- 7. GESTIÓN DE PRODUCTOS DE TAGUA (EDICIÓN Y ELIMINACIÓN DIRECTA) -->
        <div id="seccion-productos-tagua" class="admin-section-box" style="margin-top: 3rem; border-left: 4px solid var(--primary);">
            <div class="admin-section-title" style="display: flex; justify-content: space-between; align-items: center;">
                <span>Catálogo de Productos de Tagua (<?php echo count($tagua_prods); ?>)</span>
                <?php if ($edit_tagua_prod): ?>
                    <a href="tagua.php#form-tagua-prod" class="btn btn-secondary" style="font-size: 0.8rem; padding: 6px 14px; text-transform: none;">+ Añadir Nuevo Producto de Tagua</a>
                <?php endif; ?>
            </div>

            <!-- Formulario de Añadir / Editar Producto de Tagua -->
            <div id="form-tagua-prod" style="background: var(--surface-light); border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem;">
                <h3 style="font-family: var(--font-heading); font-size: 1.15rem; margin-bottom: 1rem; color: var(--dark); display: flex; align-items: center; gap: 8px;">
                    <span><?php echo $edit_tagua_prod ? '✏️ Editar Producto de Tagua' : '➕ Añadir Nuevo Producto de Tagua'; ?></span>
                    <?php if ($edit_tagua_prod): ?>
                        <small style="font-size: 0.8rem; color: var(--primary);">(ID #<?php echo $edit_tagua_prod['id']; ?>: <?php echo htmlspecialchars($edit_tagua_prod['name']); ?>)</small>
                    <?php endif; ?>
                </h3>

                <form method="POST" action="tagua.php#form-tagua-prod" enctype="multipart/form-data" id="tagua-prod-form">
                    <input type="hidden" name="save_tagua_product" value="1">
                    <input type="hidden" name="tagua_prod_id" value="<?php echo $edit_tagua_prod ? $edit_tagua_prod['id'] : '0'; ?>">
                    <input type="hidden" name="existing_tp_image" value="<?php echo htmlspecialchars($edit_tagua_prod['image_main'] ?? ''); ?>">

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Nombre del Producto *:</label>
                            <input type="text" name="tp_name" class="form-control" required placeholder="Ej: Llaveros de Tagua Grabados a Láser" value="<?php echo htmlspecialchars($edit_tagua_prod['name'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Slug / URL limpia (Opcional):</label>
                            <input type="text" name="tp_slug" class="form-control" placeholder="ej: llaveros-tagua-laser" value="<?php echo htmlspecialchars($edit_tagua_prod['slug'] ?? ''); ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px; margin-top: 1rem;">
                        <div class="form-group">
                            <label>SKU / Código:</label>
                            <input type="text" name="tp_sku" class="form-control" placeholder="TAG-001" value="<?php echo htmlspecialchars($edit_tagua_prod['sku'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Precio Unitario ($) *:</label>
                            <input type="number" step="0.01" name="tp_price" class="form-control" required placeholder="2.50" value="<?php echo $edit_tagua_prod ? $edit_tagua_prod['price'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Stock (uds):</label>
                            <input type="number" name="tp_stock" class="form-control" placeholder="100" value="<?php echo $edit_tagua_prod ? (int)$edit_tagua_prod['stock'] : '100'; ?>">
                        </div>
                        <div class="form-group">
                            <label>Orden (1, 2, 3...):</label>
                            <input type="number" name="tp_order_val" class="form-control" placeholder="1" value="<?php echo $edit_tagua_prod ? (int)$edit_tagua_prod['order_val'] : '1'; ?>">
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label>Foto Principal / Portada del Producto de Tagua:</label>
                        <input type="file" name="tp_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                        <?php if ($edit_tagua_prod && !empty($edit_tagua_prod['image_main'])): ?>
                            <div style="margin-top: 6px; display: flex; align-items: center; gap: 8px;">
                                <img src="<?php echo htmlspecialchars(getUploadedImgUrl($edit_tagua_prod['image_main'])); ?>" style="width: 44px; height: 44px; object-fit: contain; border-radius: 4px; border: 1px solid var(--border); background: white; padding: 2px;">
                                <small style="color: var(--text-muted);">Foto de portada actual asignada</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Galería de Fotos Adicionales de Tagua -->
                    <div class="form-group" style="margin-top: 1.25rem;">
                        <label style="display: flex; justify-content: space-between; align-items: center;">
                            <span>Fotos de la Galería (Puedes seleccionar varias a la vez):</span>
                            <small style="color: var(--text-muted); font-weight: normal;">Formatos: WEBP, PNG, JPG</small>
                        </label>
                        <input type="file" name="tp_gallery[]" class="form-control" multiple accept="image/jpeg,image/png,image/webp">
                        <input type="hidden" name="existing_tp_gallery" id="existing_tp_gallery" value="<?php echo htmlspecialchars($edit_tagua_prod['gallery_images'] ?? '[]'); ?>">

                        <?php 
                        $cur_tagua_gallery = [];
                        if ($edit_tagua_prod && !empty($edit_tagua_prod['gallery_images'])) {
                            $cur_tagua_gallery = json_decode($edit_tagua_prod['gallery_images'], true) ?: [];
                        }
                        if (!empty($cur_tagua_gallery)): 
                        ?>
                            <small style="color: var(--text-muted); display: block; margin-top: 8px; font-size: 0.85rem;">
                                🖐️ <strong>Arrastra y suelta</strong> las imágenes para definir el orden en que se mostrarán en la ficha de Tagua. Haz clic en la <strong>×</strong> para eliminar una foto.
                            </small>
                            <div class="gallery-preview" id="tagua-gallery-preview">
                                <?php foreach ($cur_tagua_gallery as $g_img): ?>
                                    <div class="gallery-img-wrap" draggable="true" data-img-path="<?php echo htmlspecialchars($g_img); ?>" title="Arrastra para reordenar">
                                        <img src="<?php echo htmlspecialchars(getUploadedImgUrl($g_img)); ?>" alt="Foto Tagua">
                                        <span class="gallery-drag-badge">⠿ Mover</span>
                                        <button type="button" class="delete-gallery-img" onclick="removeTaguaGalleryImage(this, '<?php echo htmlspecialchars($g_img, ENT_QUOTES, 'UTF-8'); ?>')" title="Eliminar foto">&times;</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label>Descripción Corta (Aparece en tarjetas y listados):</label>
                        <textarea name="tp_description_short" class="form-control" rows="2" placeholder="Resumen conciso de la pieza de tagua..."><?php echo htmlspecialchars($edit_tagua_prod['description_short'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label>Descripción Completa (Aparece en la ficha del producto):</label>
                        <textarea name="tp_description_long" class="form-control" rows="3" placeholder="Detalles de acabado, grabado láser, dimensiones..."><?php echo htmlspecialchars($edit_tagua_prod['description_long'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-grid-2" style="margin-top: 1rem;">
                        <div class="form-group">
                            <label>Texto del Botón:</label>
                            <input type="text" name="tp_cta_text" class="form-control" value="<?php echo htmlspecialchars($edit_tagua_prod['cta_text'] ?? 'Cotizar'); ?>">
                        </div>
                        <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 1.8rem;">
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 500;">
                                <input type="checkbox" name="tp_is_active" <?php echo (!$edit_tagua_prod || $edit_tagua_prod['is_active']) ? 'checked' : ''; ?>>
                                Producto de Tagua Activo (Visible)
                            </label>
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                        <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                            <?php echo $edit_tagua_prod ? '✓ Guardar Cambios del Producto' : '+ Crear Producto de Tagua'; ?>
                        </button>
                        <?php if ($edit_tagua_prod): ?>
                            <button type="button" class="btn-delete-tagua-form" data-id="<?php echo $edit_tagua_prod['id']; ?>" style="background-color: #DC2626; color: white; border: none; padding: 10px 18px; border-radius: 4px; font-weight: 500; cursor: pointer;">
                                🗑️ Eliminar este Producto
                            </button>
                            <a href="tagua.php#seccion-productos-tagua" class="btn btn-secondary" style="padding: 10px 18px; text-decoration: none;">Cancelar Edición</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Tabla de Productos de Tagua -->
            <h4 style="font-family: var(--font-heading); font-size: 1rem; margin-bottom: 1rem; color: var(--dark);">Lista de Productos en Tagua</h4>
            <?php if (empty($tagua_prods)): ?>
                <p style="color: var(--text-muted); font-size: 0.9rem;">No hay productos registrados en la categoría Tagua. Utiliza el formulario superior para añadir uno.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">Orden</th>
                            <th style="width: 60px;">Foto</th>
                            <th>Nombre</th>
                            <th>SKU</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tagua_prods as $tp): ?>
                            <tr id="tagua-prod-row-<?php echo $tp['id']; ?>">
                                <td style="width: 90px;">
                                    <form method="POST" action="tagua.php" class="quick-order-tagua-form" style="display: flex; align-items: center; gap: 4px; margin: 0;">
                                        <input type="hidden" name="quick_order_id" value="<?php echo $tp['id']; ?>">
                                        <input type="number" name="quick_order_val" value="<?php echo (int)$tp['order_val']; ?>" style="width: 44px; padding: 4px 6px; font-size: 0.85rem; border: 1px solid var(--border); border-radius: 4px; text-align: center;">
                                        <button type="submit" class="btn btn-secondary" style="padding: 3px 6px; font-size: 0.75rem; line-height: 1;" title="Guardar orden">✓</button>
                                    </form>
                                </td>
                                <td>
                                    <?php 
                                    $gal_count = 0;
                                    if (!empty($tp['gallery_images'])) {
                                        $g_dec = json_decode($tp['gallery_images'], true);
                                        $gal_count = is_array($g_dec) ? count($g_dec) : 0;
                                    }
                                    ?>
                                    <div style="position: relative; display: inline-block;">
                                        <img src="<?php echo htmlspecialchars(getUploadedImgUrl($tp['image_main'] ?? '', 'uploads/tagua_llavero.webp')); ?>" style="width: 46px; height: 46px; object-fit: contain; border-radius: 4px; border: 1px solid var(--border); background: white; padding: 2px;" alt="">
                                        <?php if ($gal_count > 0): ?>
                                            <span style="position: absolute; bottom: -4px; right: -4px; background: var(--primary); color: white; border-radius: 10px; font-size: 0.65rem; padding: 1px 5px; font-weight: 700; box-shadow: 0 1px 3px rgba(0,0,0,0.25);" title="<?php echo $gal_count; ?> fotos adicionales en galería">
                                                +<?php echo $gal_count; ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($tp['name']); ?></strong><br>
                                    <small style="color: var(--text-muted);"><?php echo htmlspecialchars(mb_substr($tp['description_short'] ?? '', 0, 50)) . '...'; ?></small>
                                </td>
                                <td><code><?php echo htmlspecialchars($tp['sku'] ?? 'N/A'); ?></code></td>
                                <td><strong>$<?php echo number_format($tp['price'], 2); ?></strong></td>
                                <td><?php echo (int)($tp['stock'] ?? 0); ?> u.</td>
                                <td>
                                    <span class="badge <?php echo $tp['is_active'] ? 'badge-success' : 'badge-danger'; ?>" style="font-size: 0.7rem; border-radius: 4px; padding: 2px 6px;">
                                        <?php echo $tp['is_active'] ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="tagua.php?edit_prod=<?php echo $tp['id']; ?>#form-tagua-prod" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.75rem; text-transform: none; margin-right: 6px;">Editar</a>
                                    <a href="tagua.php?delete_product=<?php echo $tp['id']; ?>" class="btn-delete-tagua-prod" data-id="<?php echo $tp['id']; ?>" style="color: #EF4444; font-weight: bold; font-size: 0.85rem; text-decoration: none;">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Toast Notifications Container -->
    <div id="toast-container" style="position: fixed; bottom: 25px; right: 25px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;"></div>

    <script>
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.style.pointerEvents = 'auto';
        toast.style.padding = '12px 20px';
        toast.style.borderRadius = '8px';
        toast.style.color = '#fff';
        toast.style.fontSize = '0.9rem';
        toast.style.fontWeight = '500';
        toast.style.boxShadow = '0 10px 25px rgba(0,0,0,0.15)';
        toast.style.display = 'flex';
        toast.style.alignItems = 'center';
        toast.style.gap = '10px';
        toast.style.transition = 'all 0.3s cubic-bezier(0.16, 1, 0.3, 1)';
        toast.style.transform = 'translateY(20px)';
        toast.style.opacity = '0';
        toast.style.maxWidth = '380px';

        if (type === 'success') {
            toast.style.backgroundColor = '#059669';
            toast.innerHTML = '<span style="font-size: 1.1rem;">✓</span> <span>' + message + '</span>';
        } else {
            toast.style.backgroundColor = '#DC2626';
            toast.innerHTML = '<span style="font-size: 1.1rem;">⚠</span> <span>' + message + '</span>';
        }

        container.appendChild(toast);
        requestAnimationFrame(() => {
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
        });

        setTimeout(() => {
            toast.style.transform = 'translateY(15px)';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3200);
    }

    // Gestión de Galería de Tagua (Reordenar y Eliminar)
    function updateTaguaGalleryInputOrder() {
        const preview = document.getElementById('tagua-gallery-preview');
        const input = document.getElementById('existing_tp_gallery');
        if (!preview || !input) return;

        const updatedPaths = [];
        preview.querySelectorAll('.gallery-img-wrap').forEach(function(wrap) {
            const path = wrap.getAttribute('data-img-path');
            if (path) {
                updatedPaths.push(path);
            }
        });
        input.value = JSON.stringify(updatedPaths);
    }

    function removeTaguaGalleryImage(btn, imgPath) {
        if (confirm('¿Eliminar esta imagen de la galería de Tagua?')) {
            const wrap = btn.closest('.gallery-img-wrap');
            if (wrap) {
                wrap.remove();
            }
            updateTaguaGalleryInputOrder();
            showToast('Foto eliminada de la galería. Guarda los cambios para confirmar.', 'success');
        }
    }

    // Preservar y restaurar la posición exacta de scroll
    window.addEventListener('beforeunload', function() {
        sessionStorage.setItem('admin_tagua_scroll', window.scrollY);
    });

    document.addEventListener('DOMContentLoaded', function() {
        const savedY = sessionStorage.getItem('admin_tagua_scroll');
        if (savedY !== null) {
            window.scrollTo(0, parseInt(savedY, 10));
        }

        // Inicializar Drag & Drop en la Galería de Tagua
        const taguaPreview = document.getElementById('tagua-gallery-preview');
        if (taguaPreview) {
            let draggedItem = null;

            function addTaguaDragEvents(wrap) {
                wrap.addEventListener('dragstart', function(e) {
                    draggedItem = wrap;
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', wrap.getAttribute('data-img-path') || '');
                    setTimeout(() => wrap.classList.add('dragging'), 0);
                });

                wrap.addEventListener('dragend', function() {
                    wrap.classList.remove('dragging');
                    taguaPreview.querySelectorAll('.gallery-img-wrap').forEach(w => w.classList.remove('drag-over'));
                    draggedItem = null;
                    updateTaguaGalleryInputOrder();
                });

                wrap.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    if (draggedItem && wrap !== draggedItem) {
                        wrap.classList.add('drag-over');
                    }
                });

                wrap.addEventListener('dragleave', function() {
                    wrap.classList.remove('drag-over');
                });

                wrap.addEventListener('drop', function(e) {
                    e.preventDefault();
                    wrap.classList.remove('drag-over');
                    if (draggedItem && wrap !== draggedItem) {
                        const allItems = Array.from(taguaPreview.querySelectorAll('.gallery-img-wrap'));
                        const draggedIdx = allItems.indexOf(draggedItem);
                        const targetIdx = allItems.indexOf(wrap);

                        if (draggedIdx < targetIdx) {
                            wrap.parentNode.insertBefore(draggedItem, wrap.nextSibling);
                        } else {
                            wrap.parentNode.insertBefore(draggedItem, wrap);
                        }
                        updateTaguaGalleryInputOrder();
                    }
                });
            }

            taguaPreview.querySelectorAll('.gallery-img-wrap').forEach(addTaguaDragEvents);
        }

        // Eliminación AJAX de producto desde la tabla
        document.querySelectorAll('.btn-delete-tagua-prod').forEach(function(delBtn) {
            delBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (!confirm('¿Seguro que deseas eliminar este producto de Tagua?')) return;

                const row = delBtn.closest('tr');
                const origText = delBtn.innerHTML;
                delBtn.innerHTML = '...';
                delBtn.style.pointerEvents = 'none';

                fetch(delBtn.getAttribute('href') + '&ajax=1', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        if (row) {
                            row.style.transition = 'all 0.35s ease';
                            row.style.backgroundColor = '#FEE2E2';
                            row.style.opacity = '0';
                            row.style.transform = 'scale(0.97)';
                            setTimeout(() => row.remove(), 350);
                        }
                    } else {
                        showToast(data.error || 'Error al eliminar', 'danger');
                        delBtn.innerHTML = origText;
                        delBtn.style.pointerEvents = 'auto';
                    }
                })
                .catch(err => {
                    sessionStorage.setItem('admin_tagua_scroll', window.scrollY);
                    window.location.href = delBtn.getAttribute('href');
                });
            });
        });

        // Eliminación directa desde el botón del formulario de edición
        const formDelBtn = document.querySelector('.btn-delete-tagua-form');
        if (formDelBtn) {
            formDelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (!confirm('¿Seguro que deseas eliminar definitivamente este producto de Tagua?')) return;

                const prodId = formDelBtn.getAttribute('data-id');
                formDelBtn.disabled = true;
                formDelBtn.innerHTML = 'Eliminando...';

                fetch('tagua.php?delete_product=' + prodId + '&ajax=1', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        const row = document.getElementById('tagua-prod-row-' + prodId);
                        if (row) row.remove();
                        setTimeout(() => {
                            window.location.href = 'tagua.php#seccion-productos-tagua';
                        }, 600);
                    } else {
                        showToast(data.error || 'Error al eliminar', 'danger');
                        formDelBtn.disabled = false;
                        formDelBtn.innerHTML = '🗑️ Eliminar este Producto';
                    }
                })
                .catch(err => {
                    window.location.href = 'tagua.php?delete_product=' + prodId;
                });
            });
        }

        // Orden Rápido AJAX
        document.querySelectorAll('.quick-order-tagua-form').forEach(function(qForm) {
            qForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = qForm.querySelector('button[type="submit"]');
                const origText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '...';

                const formData = new FormData(qForm);
                formData.append('ajax', '1');

                fetch('tagua.php', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    if (data.success) {
                        btn.innerHTML = '✓';
                        btn.style.backgroundColor = '#059669';
                        btn.style.color = '#fff';
                        showToast(data.message, 'success');
                        setTimeout(() => {
                            btn.innerHTML = origText;
                            btn.style.backgroundColor = '';
                            btn.style.color = '';
                        }, 1500);
                    } else {
                        btn.innerHTML = origText;
                        showToast(data.error || 'Error al actualizar orden', 'danger');
                    }
                })
                .catch(err => {
                    qForm.submit();
                });
            });
        });
    });
    </script>
</body>
</html>
