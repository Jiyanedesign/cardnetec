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

// Procesar Eliminación
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Producto eliminado correctamente.';
        if (isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'message' => $message]);
            exit;
        }
    } catch (PDOException $e) {
        $error = 'Error al eliminar: ' . $e->getMessage();
        if (isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        }
    }
}

// Procesar actualización rápida de orden
if (isset($_POST['quick_order_id'])) {
    $q_id = (int)$_POST['quick_order_id'];
    $q_val = (int)$_POST['quick_order_val'];
    try {
        $pdo->prepare("UPDATE productos SET order_val = ? WHERE id = ?")->execute([$q_val, $q_id]);
        $message = 'Orden actualizado correctamente.';
        if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'message' => $message]);
            exit;
        }
    } catch (PDOException $e) {
        $error = 'Error al actualizar orden: ' . $e->getMessage();
        if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        }
    }
}

// Procesar Formulario (Creación o Edición)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['quick_order_id'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $description_short = trim($_POST['description_short']);
    $description_long = trim($_POST['description_long']);
    $category_id = (int)$_POST['category_id'];
    $sku = trim($_POST['sku']);
    $stock = (int)$_POST['stock'];
    $price = (float)$_POST['price'];
    $order_val = isset($_POST['order_val']) ? (int)$_POST['order_val'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $allows_simulation = isset($_POST['allows_simulation']) ? 1 : 0;
    $cta_text = trim($_POST['cta_text']);
    
    // Procesar Precios por Volumen
    $v_qty = isset($_POST['v_qty']) ? $_POST['v_qty'] : [];
    $v_price = isset($_POST['v_price']) ? $_POST['v_price'] : [];
    $v_arr = [];
    for ($i = 0; $i < count($v_qty); $i++) {
        $q = (int)$v_qty[$i];
        $p = (float)$v_price[$i];
        if ($q > 0 && $p > 0) {
            $v_arr[] = ['qty' => $q, 'price' => $p];
        }
    }
    usort($v_arr, function($a, $b) { return $a['qty'] - $b['qty']; });
    $volume_prices = json_encode($v_arr);

    // Procesar Materiales
    $materials = isset($_POST['materials']) ? $_POST['materials'] : [];
    $materials_json = json_encode($materials);

    // Procesar Etiquetas
    $tags = isset($_POST['tags']) ? $_POST['tags'] : [];
    $tags_json = json_encode($tags);
    
    // Obtener nombre de la categoría para compatibilidad
    $stmtCat = $pdo->prepare("SELECT name FROM categorias WHERE id = ?");
    $stmtCat->execute([$category_id]);
    $category_name = $stmtCat->fetchColumn() ?: 'Artículos personalizados';

    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }

    // Carpeta de subidas
    $upload_dir = '../uploads/products/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // 1. Subida de imagen principal
    $image_filename = isset($_POST['existing_image']) ? $_POST['existing_image'] : '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $new_filename = 'prod_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($file_tmp, $upload_dir . $new_filename)) {
                $webp_file = convertToWebP($upload_dir . $new_filename);
                $image_filename = 'products/' . basename($webp_file);
            }
        }
    }

    // 2. Subida de imágenes de la galería
    $gallery_paths = [];
    if (isset($_POST['existing_gallery'])) {
        $gallery_paths = json_decode($_POST['existing_gallery'], true) ?: [];
    }

    // Procesar subida de hasta 5 imágenes nuevas para la galería
    if (isset($_FILES['gallery'])) {
        $files = $_FILES['gallery'];
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $file_tmp = $files['tmp_name'][$i];
                $file_name = $files['name'][$i];
                $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $new_filename = 'gal_' . time() . '_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($file_tmp, $upload_dir . $new_filename)) {
                        $webp_file = convertToWebP($upload_dir . $new_filename);
                        $gallery_paths[] = 'products/' . basename($webp_file);
                    }
                }
            }
        }
    }

    $gallery_json = json_encode($gallery_paths);

    if (empty($error)) {
        if ($id > 0) {
            // Edición
            try {
                $stmt = $pdo->prepare("UPDATE productos SET name = ?, slug = ?, description_short = ?, description_long = ?, category = ?, category_id = ?, image_main = ?, gallery_images = ?, sku = ?, stock = ?, price = ?, is_active = ?, is_featured = ?, allows_simulation = ?, cta_text = ?, volume_prices = ?, materials_json = ?, tags_json = ?, order_val = ? WHERE id = ?");
                $stmt->execute([$name, $slug, $description_short, $description_long, $category_name, $category_id, $image_filename, $gallery_json, $sku, $stock, $price, $is_active, $is_featured, $allows_simulation, $cta_text, $volume_prices, $materials_json, $tags_json, $order_val, $id]);
                $message = 'Producto actualizado correctamente.';
                if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => true,
                        'message' => $message,
                        'id' => $id,
                        'action' => 'update',
                        'image_main' => $image_filename ? getUploadedImgUrl($image_filename) : null
                    ]);
                    exit;
                }
            } catch (PDOException $e) {
                $error = 'Error al actualizar base de datos: ' . $e->getMessage();
                if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['success' => false, 'error' => $error]);
                    exit;
                }
            }
        } else {
            // Creación
            try {
                $stmt = $pdo->prepare("INSERT INTO productos (name, slug, description_short, description_long, category, category_id, image_main, gallery_images, sku, stock, price, is_active, is_featured, allows_simulation, cta_text, volume_prices, materials_json, tags_json, order_val) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $slug, $description_short, $description_long, $category_name, $category_id, $image_filename, $gallery_json, $sku, $stock, $price, $is_active, $is_featured, $allows_simulation, $cta_text, $volume_prices, $materials_json, $tags_json, $order_val]);
                $new_id = $pdo->lastInsertId();
                $message = 'Producto creado correctamente.';
                if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => true,
                        'message' => $message,
                        'id' => $new_id,
                        'action' => 'create'
                    ]);
                    exit;
                }
            } catch (PDOException $e) {
                $error = 'Error al crear producto: ' . $e->getMessage();
                if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['success' => false, 'error' => $error]);
                    exit;
                }
            }
        }
    } else {
        if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        }
    }
}

// Cargar todas las categorías activas
$categorias = $pdo->query("SELECT * FROM categorias WHERE is_active = 1 ORDER BY order_val ASC")->fetchAll();
$materiales_list = $pdo->query("SELECT * FROM materiales WHERE is_active = 1 ORDER BY id ASC")->fetchAll();
$etiquetas_list = $pdo->query("SELECT * FROM etiquetas ORDER BY id ASC")->fetchAll();

// Filtro de categoría en el listado (Por defecto 'general' excluye Tagua)
$cat_filter = isset($_GET['cat']) ? trim($_GET['cat']) : 'general';

if ($cat_filter === 'tagua') {
    // Solo productos de la categoría Tagua
    $stmtProds = $pdo->query("SELECT p.*, c.name as category_name FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE c.slug = 'tagua' OR p.category = 'Tagua' OR p.slug LIKE '%tagua%' ORDER BY CASE WHEN p.order_val IS NULL OR p.order_val = 0 THEN 999999 ELSE p.order_val END ASC, p.id DESC");
    $products = $stmtProds->fetchAll();
} elseif ($cat_filter === 'todos') {
    // Todos los productos del catálogo completo
    $stmtProds = $pdo->query("SELECT p.*, c.name as category_name FROM productos p LEFT JOIN categorias c ON p.category_id = c.id ORDER BY CASE WHEN p.order_val IS NULL OR p.order_val = 0 THEN 999999 ELSE p.order_val END ASC, p.id DESC");
    $products = $stmtProds->fetchAll();
} elseif ($cat_filter !== '' && $cat_filter !== 'general') {
    // Filtrar por categoría seleccionada
    $stmtProds = $pdo->prepare("SELECT p.*, c.name as category_name FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE (c.slug = ? OR c.id = ?) ORDER BY CASE WHEN p.order_val IS NULL OR p.order_val = 0 THEN 999999 ELSE p.order_val END ASC, p.id DESC");
    $stmtProds->execute([$cat_filter, (int)$cat_filter]);
    $products = $stmtProds->fetchAll();
} else {
    // 'general' (por defecto): Solo productos generales, excluyendo Tagua
    $stmtProds = $pdo->query("SELECT p.*, c.name as category_name FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE (c.slug != 'tagua' OR c.slug IS NULL) AND (p.category != 'Tagua' OR p.category IS NULL) AND (p.slug NOT LIKE '%tagua%') ORDER BY CASE WHEN p.order_val IS NULL OR p.order_val = 0 THEN 999999 ELSE p.order_val END ASC, p.id DESC");
    $products = $stmtProds->fetchAll();
}

// Cargar producto a editar (o mantener cargado el producto que se acaba de actualizar)
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_product = $stmt->fetch();
} elseif (isset($_POST['id']) && (int)$_POST['id'] > 0 && empty($error)) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([(int)$_POST['id']]);
    $edit_product = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos | CardNet.ec</title>
    <link rel="stylesheet" href="../css/base.css?v=2.0">
    <link rel="stylesheet" href="../css/layout.css?v=2.0">
    <link rel="stylesheet" href="../css/components.css?v=2.0">
    <style>
        body {
            font-family: 'Work Sans', sans-serif;
            background-color: var(--surface-light);
            margin: 0;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: var(--dark-alt);
            color: white;
            min-height: 100vh;
            padding: 2rem 1.5rem;
            box-sizing: border-box;
        }
        .sidebar-logo {
            max-width: 140px;
            margin-bottom: 2rem;
            filter: brightness(0) invert(1);
        }
        .nav-admin {
            display: flex;
            flex-direction: column;
            gap: 10px;
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
        }
        .form-container {
            background-color: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            background-color: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
        }
        th, td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.88rem;
        }
        th {
            background-color: var(--surface-light);
        }
        .alert {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .alert-success { background-color: #DEF7EC; color: #03543F; }
        .alert-danger { background-color: #FDE8E8; color: #9B1C1C; }
        .gallery-preview {
            display: flex;
            gap: 12px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .gallery-img-wrap {
            position: relative;
            width: 90px;
            height: 90px;
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
            top: 4px;
            right: 4px;
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
            font-size: 9px;
            font-weight: 500;
            pointer-events: none;
            letter-spacing: 0.05em;
        }
    </style>
    <link rel="stylesheet" href="../css/admin.css?v=2.0">
</head>
<body>

    <div class="sidebar">
        <img src="../images/logo.png?v=2.0" alt="CardNet Logo" class="sidebar-logo">
        <nav class="nav-admin">
            <a href="index.php" class="nav-admin-link">Dashboard</a>
            <a href="categorias.php" class="nav-admin-link">Categorías</a>
            <a href="secciones.php" class="nav-admin-link">Secciones Home</a>
            <a href="tagua.php" class="nav-admin-link">Tagua</a>
            <a href="etiquetas.php" class="nav-admin-link">Etiquetas</a>
            <a href="productos.php" class="nav-admin-link active">Productos</a>
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
        <h1 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 2rem;">Gestión de Productos</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h2 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 1.25rem;">
                <?php echo $edit_product ? 'Editar Producto' : 'Añadir Nuevo Producto'; ?>
            </h2>

            <form method="POST" action="productos.php" enctype="multipart/form-data">
                <?php if ($edit_product): ?>
                    <input type="hidden" name="volume_prices_existing" value="<?php echo htmlspecialchars($edit_product['volume_prices'] ?: '[]'); ?>">
                    <input type="hidden" name="materials_json_existing" value="<?php echo htmlspecialchars($edit_product['materials_json'] ?: '[]'); ?>">
                    <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
                    <input type="hidden" name="existing_image" value="<?php echo $edit_product['image_main']; ?>">
                    <input type="hidden" name="existing_gallery" value="<?php echo htmlspecialchars($edit_product['gallery_images'] ?: '[]'); ?>">
                <?php endif; ?>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="name">Nombre del Producto *</label>
                        <input class="form-input" type="text" name="name" id="name" required value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="slug">Slug (URL amigable)</label>
                        <input class="form-input" type="text" name="slug" id="slug" placeholder="Ej: termos-grabados" value="<?php echo $edit_product ? htmlspecialchars($edit_product['slug']) : ''; ?>">
                    </div>
                </div>

                <div class="grid-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-top: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="sku">SKU *</label>
                        <input class="form-input" type="text" name="sku" id="sku" required placeholder="Ej: 44434" value="<?php echo $edit_product ? htmlspecialchars($edit_product['sku']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="stock">Stock Disponible *</label>
                        <input class="form-input" type="number" name="stock" id="stock" required placeholder="Ej: 700" value="<?php echo $edit_product ? (int)$edit_product['stock'] : '700'; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="price">Precio Unitario ($) *</label>
                        <input class="form-input" type="number" step="0.01" name="price" id="price" required placeholder="Ej: 2.50" value="<?php echo $edit_product ? number_format($edit_product['price'], 2) : '2.50'; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="order_val">Orden en Catálogo</label>
                        <input class="form-input" type="number" name="order_val" id="order_val" required placeholder="1, 2, 3..." value="<?php echo $edit_product ? (int)$edit_product['order_val'] : '0'; ?>" title="Menor número aparece primero">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="description_short">Descripción Corta (Showroom/Cards)</label>
                    <textarea class="form-input" name="description_short" id="description_short" rows="2" required><?php echo $edit_product ? htmlspecialchars($edit_product['description_short']) : ''; ?></textarea>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="description_long">Descripción Detallada (Acerca del Producto)</label>
                    <textarea class="form-input" name="description_long" id="description_long" rows="4" placeholder="Especificaciones técnicas, materiales, peso, área de grabado..."><?php echo $edit_product ? htmlspecialchars($edit_product['description_long']) : ''; ?></textarea>
                </div>

                <div class="grid-3" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="category_id">Categoría</label>
                        <select class="form-select" name="category_id" id="category_id" required>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($edit_product && (int)$edit_product['category_id'] === (int)$cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="image">Imagen de Portada Principal</label>
                        <input class="form-input" type="file" name="image" id="image">
                        <?php if ($edit_product && !empty($edit_product['image_main'])): ?>
                            <div style="margin-top: 8px; display: flex; align-items: center; gap: 10px;">
                                <img src="<?php echo htmlspecialchars(getUploadedImgUrl($edit_product['image_main'])); ?>" style="width: 50px; height: 50px; object-fit: contain; border: 1px solid var(--border); border-radius: 4px; background: white; padding: 2px;">
                                <small style="color: var(--text-muted); font-size: 0.8rem;">Imagen actual asignada</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="cta_text">Texto del Botón</label>
                        <input class="form-input" type="text" name="cta_text" id="cta_text" value="<?php echo $edit_product ? htmlspecialchars($edit_product['cta_text']) : 'Quiero este acabado'; ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label class="form-label" for="gallery">Añadir Fotos a la Galería (Selecciona múltiples archivos)</label>
                    <input class="form-input" type="file" name="gallery[]" id="gallery" multiple>
                    
                    <?php if ($edit_product && !empty($edit_product['gallery_images'])): ?>
                        <small style="color: var(--text-muted); display: block; margin-top: 8px; font-size: 0.85rem;">
                            🖐️ <strong>Arrastra y suelta</strong> las imágenes para definir el orden en que se mostrarán en la galería del producto.
                        </small>
                        <div class="gallery-preview" id="gallery-sortable-preview">
                            <?php 
                             $gallery = json_decode($edit_product['gallery_images'], true) ?: [];
                             foreach ($gallery as $g_img): 
                             ?>
                                 <div class="gallery-img-wrap" draggable="true" data-img-path="<?php echo htmlspecialchars($g_img); ?>" title="Arrastra para reordenar">
                                     <img src="<?php echo htmlspecialchars(getUploadedImgUrl($g_img)); ?>" alt="Foto Galería">
                                     <span class="gallery-drag-badge">⠿ Mover</span>
                                     <button type="button" class="delete-gallery-img" onclick="removeGalleryImage(this, '<?php echo htmlspecialchars($g_img, ENT_QUOTES, 'UTF-8'); ?>')" title="Eliminar imagen">&times;</button>
                                 </div>
                             <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Precios por volumen -->
                <div class="form-group" style="margin-top: 1.5rem; border: 1px solid var(--border); padding: 1.5rem; border-radius: var(--radius-md);">
                    <h3 style="font-family: var(--font-heading); font-size: 1rem; margin-bottom: 1rem;">Precios por Volumen (Descuentos por Cantidad)</h3>
                    <div id="volume-pricing-container">
                        <?php 
                        $vp = [];
                        if ($edit_product && !empty($edit_product['volume_prices'])) {
                            $vp = json_decode($edit_product['volume_prices'], true) ?: [];
                        }
                        for ($i = 0; $i < 3; $i++): 
                            $q_val = isset($vp[$i]['qty']) ? $vp[$i]['qty'] : '';
                            $p_val = isset($vp[$i]['price']) ? $vp[$i]['price'] : '';
                        ?>
                            <div style="display: flex; gap: 15px; margin-bottom: 10px; align-items: center;">
                                <span>Rango <?php echo $i+1; ?>:</span>
                                <input class="form-input" style="max-width: 120px;" type="number" name="v_qty[]" placeholder="Desde cant." value="<?php echo $q_val; ?>">
                                <span>unidades -> Precio unitario: $</span>
                                <input class="form-input" style="max-width: 120px;" type="number" step="0.01" name="v_price[]" placeholder="Precio ($)" value="<?php echo $p_val; ?>">
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Selector de Materiales -->
                <div class="form-group" style="margin-top: 1.5rem; border: 1px solid var(--border); padding: 1.5rem; border-radius: var(--radius-md);">
                    <h3 style="font-family: var(--font-heading); font-size: 1rem; margin-bottom: 1rem;">Materiales y Acabados disponibles</h3>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <?php 
                        $selected_mats = [];
                        if ($edit_product && !empty($edit_product['materials_json'])) {
                            $selected_mats = json_decode($edit_product['materials_json'], true) ?: [];
                        }
                        foreach ($materiales_list as $mat): 
                        ?>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer;">
                                <input type="checkbox" name="materials[]" value="<?php echo $mat['id']; ?>" <?php echo in_array($mat['id'], $selected_mats) ? 'checked' : ''; ?>>
                                <?php echo htmlspecialchars($mat['name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Selector de Etiquetas -->
                <div class="form-group" style="margin-top: 1.5rem; border: 1px solid var(--border); padding: 1.5rem; border-radius: var(--radius-md);">
                    <h3 style="font-family: var(--font-heading); font-size: 1rem; margin-bottom: 1rem;">Etiquetas del Producto (Se muestran en el catálogo y detalle)</h3>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <?php 
                        $selected_tags = [];
                        if ($edit_product && !empty($edit_product['tags_json'])) {
                            $selected_tags = json_decode($edit_product['tags_json'], true) ?: [];
                        }
                        foreach ($etiquetas_list as $tag): 
                        ?>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer;">
                                <input type="checkbox" name="tags[]" value="<?php echo $tag['id']; ?>" <?php echo in_array($tag['id'], $selected_tags) ? 'checked' : ''; ?>>
                                <span style="display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 0.72rem; font-weight: 600; background-color: <?php echo htmlspecialchars($tag['color']); ?>; color: <?php echo htmlspecialchars($tag['text_color']); ?>;">
                                    <?php echo htmlspecialchars($tag['name']); ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1.5rem; display: flex; gap: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer;">
                        <input type="checkbox" name="is_active" <?php echo (!$edit_product || $edit_product['is_active']) ? 'checked' : ''; ?>> Producto Activo
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer;">
                        <input type="checkbox" name="is_featured" <?php echo ($edit_product && $edit_product['is_featured']) ? 'checked' : ''; ?>> Mostrar en Trabajos Realizados (Carrusel Home)
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer;">
                        <input type="checkbox" name="allows_simulation" <?php echo ($edit_product && $edit_product['allows_simulation']) ? 'checked' : ''; ?>> Permitir Simulador Canvas
                    </label>
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <button class="btn btn-primary" type="submit">Guardar Producto</button>
                    <?php if ($edit_product): ?>
                        <input type="hidden" name="volume_prices_existing" value="<?php echo htmlspecialchars($edit_product['volume_prices'] ?: '[]'); ?>">
                        <input type="hidden" name="materials_json_existing" value="<?php echo htmlspecialchars($edit_product['materials_json'] ?: '[]'); ?>">
                        <button type="button" class="btn-delete-prod-form" data-id="<?php echo $edit_product['id']; ?>" style="background-color: #DC2626; color: white; border: none; padding: 10px 18px; border-radius: 4px; font-weight: 500; cursor: pointer;">
                            🗑️ Eliminar este Producto
                        </button>
                        <a href="productos.php" class="btn btn-secondary">Cancelar Edición</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 15px;">
            <div>
                <h2 style="font-family: var(--font-heading); font-size: 1.4rem; margin: 0;">Productos Registrados (<?php echo count($products); ?>)</h2>
                <small style="color: var(--text-muted);">
                    <?php if ($cat_filter === 'tagua'): ?>
                        Mostrando únicamente productos asignados a la categoría <strong>Tagua</strong>.
                    <?php elseif ($cat_filter === 'todos'): ?>
                        Mostrando todo el catálogo completo del sistema.
                    <?php else: ?>
                        Mostrando catálogo general de identificación corporativa (excluye Tagua).
                    <?php endif; ?>
                </small>
            </div>

            <!-- Filtros por Categoría para aislar Tagua -->
            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-right: 4px;">Categoría:</span>
                <a href="productos.php?cat=general" class="btn <?php echo ($cat_filter === 'general' || empty($cat_filter)) ? 'btn-primary' : 'btn-secondary'; ?>" style="padding: 5px 12px; font-size: 0.78rem; text-transform: none; text-decoration: none;">
                    General (Sin Tagua)
                </a>
                <a href="productos.php?cat=tagua" class="btn <?php echo ($cat_filter === 'tagua') ? 'btn-primary' : 'btn-secondary'; ?>" style="padding: 5px 12px; font-size: 0.78rem; text-transform: none; text-decoration: none; <?php echo ($cat_filter === 'tagua') ? 'background-color: #059669; border-color: #059669;' : ''; ?>">
                    🌱 Tagua
                </a>
                <a href="productos.php?cat=todos" class="btn <?php echo ($cat_filter === 'todos') ? 'btn-primary' : 'btn-secondary'; ?>" style="padding: 5px 12px; font-size: 0.78rem; text-transform: none; text-decoration: none;">
                    Ver Todo
                </a>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Orden</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>SKU</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Categoría</th>
                    <th>Trabajos Realizados</th>
                    <th>Simulable</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $prod): ?>
                    <tr id="prod-row-<?php echo $prod['id']; ?>">
                        <td style="width: 100px;">
                            <form method="POST" action="productos.php" class="quick-order-form" style="display: flex; align-items: center; gap: 4px; margin: 0;">
                                <input type="hidden" name="quick_order_id" value="<?php echo $prod['id']; ?>">
                                <input type="number" name="quick_order_val" value="<?php echo (int)$prod['order_val']; ?>" style="width: 50px; padding: 4px 6px; font-size: 0.85rem; border: 1px solid var(--border); border-radius: 4px; text-align: center;">
                                <button type="submit" class="btn btn-secondary" style="padding: 3px 6px; font-size: 0.75rem; line-height: 1;" title="Guardar orden">✓</button>
                            </form>
                        </td>
                        <td>
                            <?php if ($prod['image_main']): ?>
                                <img src="<?php echo htmlspecialchars(getUploadedImgUrl($prod['image_main'])); ?>" style="width: 44px; height: 44px; object-fit: contain; border-radius: 4px; background: white; border: 1px solid var(--border); padding: 2px;">
                            <?php else: ?>
                                <div style="width: 44px; height: 44px; background-color: var(--border); border-radius: 4px;"></div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($prod['name']); ?></strong></td>
                        <td><code><?php echo htmlspecialchars($prod['sku']); ?></code></td>
                        <td>$<?php echo number_format($prod['price'], 2); ?></td>
                        <td><?php echo (int)$prod['stock']; ?> uds</td>
                        <td><?php echo htmlspecialchars($prod['category_name'] ?: $prod['category']); ?></td>
                        <td><?php echo $prod['is_featured'] ? 'Sí' : 'No'; ?></td>
                        <td><?php echo $prod['allows_simulation'] ? 'Sí' : 'No'; ?></td>
                        <td>
                            <span class="badge <?php echo $prod['is_active'] ? 'badge-success' : 'badge-danger'; ?>" style="font-size: 0.7rem; border-radius: 4px; padding: 2px 6px;">
                                <?php echo $prod['is_active'] ? 'Activo' : 'Inactivo'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="productos.php?edit=<?php echo $prod['id']; ?>" style="color: var(--primary); text-decoration: none; font-weight: bold; margin-right: 10px;">Editar</a>
                            <a href="productos.php?delete=<?php echo $prod['id']; ?>" class="btn-delete-prod" data-id="<?php echo $prod['id']; ?>" style="color: #EF4444; text-decoration: none; font-weight: bold;">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Toast Notifications Container -->
    <div id="toast-container" style="position: fixed; bottom: 25px; right: 25px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;"></div>

    <script>
    // Sistema de Notificaciones Toast flotantes
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

    // Preservar y restaurar la posición exacta de scroll
    window.addEventListener('beforeunload', function() {
        sessionStorage.setItem('admin_prod_scroll', window.scrollY);
    });

    document.addEventListener('DOMContentLoaded', function() {
        const savedY = sessionStorage.getItem('admin_prod_scroll');
        if (savedY !== null) {
            window.scrollTo(0, parseInt(savedY, 10));
        }
    });

    function updateGalleryInputOrder() {
        const preview = document.getElementById('gallery-sortable-preview');
        const input = document.querySelector('input[name="existing_gallery"]');
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

    function removeGalleryImage(btn, imgPath) {
        if (confirm('¿Eliminar esta imagen de la galería?')) {
            const wrap = btn.closest('.gallery-img-wrap');
            if (wrap) {
                wrap.remove();
            }
            updateGalleryInputOrder();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // 1. Galería Drag & Drop
        const preview = document.getElementById('gallery-sortable-preview');
        if (preview) {
            let draggedItem = null;

            function addDragEvents(wrap) {
                wrap.addEventListener('dragstart', function(e) {
                    draggedItem = wrap;
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', wrap.getAttribute('data-img-path') || '');
                    setTimeout(function() {
                        wrap.classList.add('dragging');
                    }, 0);
                });

                wrap.addEventListener('dragend', function() {
                    wrap.classList.remove('dragging');
                    preview.querySelectorAll('.gallery-img-wrap').forEach(function(w) {
                        w.classList.remove('drag-over');
                    });
                    draggedItem = null;
                    updateGalleryInputOrder();
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
                        const allItems = Array.from(preview.querySelectorAll('.gallery-img-wrap'));
                        const draggedIndex = allItems.indexOf(draggedItem);
                        const targetIndex = allItems.indexOf(wrap);

                        if (draggedIndex < targetIndex) {
                            wrap.after(draggedItem);
                        } else {
                            wrap.before(draggedItem);
                        }
                        updateGalleryInputOrder();
                    }
                });
            }

            preview.querySelectorAll('.gallery-img-wrap').forEach(addDragEvents);
        }

        // 2. Guardado de Formulario Principal vía AJAX (In-Place, sin saltar ni recargar)
        const mainForm = document.querySelector('.form-container form');
        if (mainForm) {
            mainForm.addEventListener('submit', function(e) {
                const submitBtn = mainForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                const idInput = mainForm.querySelector('input[name="id"]');
                const isEdit = idInput && parseInt(idInput.value, 10) > 0;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '⏳ Guardando...';

                const formData = new FormData(mainForm);
                formData.append('ajax', '1');

                fetch('productos.php', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    submitBtn.disabled = false;
                    if (data.success) {
                        submitBtn.innerHTML = '✓ Guardado';
                        submitBtn.style.backgroundColor = '#059669';
                        showToast(data.message, 'success');

                        // Si hubo nueva imagen de portada, actualizar vista previa
                        if (data.image_main) {
                            const imgContainer = mainForm.querySelector('.form-group img');
                            if (imgContainer) {
                                imgContainer.src = data.image_main;
                            }
                            const fileImg = mainForm.querySelector('input[name="image"]');
                            if (fileImg) fileImg.value = '';
                        }

                        // Limpiar input de galería de archivos nuevos
                        const galleryInput = mainForm.querySelector('input[name="gallery[]"]');
                        if (galleryInput) galleryInput.value = '';

                        // Si fue una creación, redirigir a edición de ese nuevo producto manteniendo scroll
                        if (data.action === 'create' && data.id) {
                            sessionStorage.setItem('admin_prod_scroll', window.scrollY);
                            setTimeout(() => {
                                window.location.href = 'productos.php?edit=' + data.id;
                            }, 600);
                        } else {
                            setTimeout(() => {
                                submitBtn.innerHTML = originalText;
                                submitBtn.style.backgroundColor = '';
                            }, 1800);
                        }
                    } else {
                        submitBtn.innerHTML = originalText;
                        submitBtn.style.backgroundColor = '';
                        showToast(data.error || 'Ocurrió un error al guardar', 'danger');
                    }
                })
                .catch(err => {
                    // Fallback a envío tradicional
                    sessionStorage.setItem('admin_prod_scroll', window.scrollY);
                    mainForm.submit();
                });

                e.preventDefault();
            });
        }

        // 3. Eliminación vía AJAX (In-Place con animación y sin salto de scroll)
        document.querySelectorAll('.btn-delete-prod').forEach(function(delBtn) {
            delBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (!confirm('¿Seguro que deseas eliminar este producto?')) return;

                const row = delBtn.closest('tr');
                const origText = delBtn.innerHTML;
                delBtn.innerHTML = '...';
                delBtn.style.pointerEvents = 'none';

                const url = delBtn.getAttribute('href') + '&ajax=1';

                fetch(url, {
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
                        showToast(data.error || 'Error al eliminar producto', 'danger');
                        delBtn.innerHTML = origText;
                        delBtn.style.pointerEvents = 'auto';
                    }
                })
                .catch(err => {
                    sessionStorage.setItem('admin_prod_scroll', window.scrollY);
                    window.location.href = delBtn.getAttribute('href');
                });
            });
        });

        // Eliminación directa desde el formulario de edición de productos
        const formDelBtn = document.querySelector('.btn-delete-prod-form');
        if (formDelBtn) {
            formDelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (!confirm('¿Seguro que deseas eliminar definitivamente este producto?')) return;

                const prodId = formDelBtn.getAttribute('data-id');
                formDelBtn.disabled = true;
                formDelBtn.innerHTML = 'Eliminando...';

                fetch('productos.php?delete=' + prodId + '&ajax=1', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        const row = document.getElementById('prod-row-' + prodId);
                        if (row) row.remove();
                        setTimeout(() => {
                            window.location.href = 'productos.php';
                        }, 500);
                    } else {
                        showToast(data.error || 'Error al eliminar producto', 'danger');
                        formDelBtn.disabled = false;
                        formDelBtn.innerHTML = '🗑️ Eliminar este Producto';
                    }
                })
                .catch(err => {
                    window.location.href = 'productos.php?delete=' + prodId;
                });
            });
        }

        // 4. Orden Rápido vía AJAX (In-Place sin recarga ni salto)
        document.querySelectorAll('.quick-order-form').forEach(function(qForm) {
            qForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = qForm.querySelector('button[type="submit"]');
                const origText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '...';

                const formData = new FormData(qForm);
                formData.append('ajax', '1');

                fetch('productos.php', {
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
                    sessionStorage.setItem('admin_prod_scroll', window.scrollY);
                    qForm.submit();
                });
            });
        });
    });
    </script>
</body>
</html>
