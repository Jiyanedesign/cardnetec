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
        $stmt = $pdo->prepare("DELETE FROM secciones_home WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Tarjeta eliminada correctamente.';
    } catch (PDOException $e) {
        $error = 'Error al eliminar: ' . $e->getMessage();
    }
}

// Procesar Formulario de Textos de Encabezado de Obras del Taller
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['action_header_obras']) || isset($_POST['action_header_texts']))) {
    $obras_subtitle = trim($_POST['obras_subtitle'] ?? '');
    $obras_title = trim($_POST['obras_title'] ?? '');
    $obras_desc = trim($_POST['obras_desc'] ?? '');

    try {
        $stmtH = $pdo->prepare("UPDATE configuraciones SET obras_subtitle = ?, obras_title = ?, obras_desc = ? WHERE id = 1");
        $stmtH->execute([$obras_subtitle, $obras_title, $obras_desc]);
        $message = 'Textos del encabezado de Obras del Taller actualizados correctamente.';
    } catch (PDOException $e) {
        $error = 'Error al actualizar textos de cabecera: ' . $e->getMessage();
    }
}

// Procesar Formulario de Textos de Encabezado de Accesorios Diarios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_header_accesorios'])) {
    $accesorios_subtitle = trim($_POST['accesorios_subtitle'] ?? '');
    $accesorios_title = trim($_POST['accesorios_title'] ?? '');
    $accesorios_desc = trim($_POST['accesorios_desc'] ?? '');

    try {
        $stmtH = $pdo->prepare("UPDATE configuraciones SET accesorios_subtitle = ?, accesorios_title = ?, accesorios_desc = ? WHERE id = 1");
        $stmtH->execute([$accesorios_subtitle, $accesorios_title, $accesorios_desc]);
        $message = 'Textos del encabezado de Accesorios Diarios actualizados correctamente.';
    } catch (PDOException $e) {
        $error = 'Error al actualizar textos de cabecera: ' . $e->getMessage();
    }
}

// Procesar Formulario de Textos de Encabezado de Líneas de Personalización (Bento Grid)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_header_bento'])) {
    $bento_subtitle = trim($_POST['bento_subtitle'] ?? '');
    $bento_title = trim($_POST['bento_title'] ?? '');
    $bento_desc = trim($_POST['bento_desc'] ?? '');

    try {
        $stmtH = $pdo->prepare("UPDATE configuraciones SET bento_subtitle = ?, bento_title = ?, bento_desc = ? WHERE id = 1");
        $stmtH->execute([$bento_subtitle, $bento_title, $bento_desc]);
        $message = 'Textos del encabezado de Líneas de Personalización actualizados correctamente.';
    } catch (PDOException $e) {
        $error = 'Error al actualizar textos de cabecera: ' . $e->getMessage();
    }
}

// Procesar Formulario de Edición de Tarjeta del Bento Grid
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_save_bento_card'])) {
    $bento_id = (int)$_POST['bento_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description'] ?? '');
    $custom_link = trim($_POST['custom_link'] ?? '');
    $order_val = isset($_POST['order_val']) ? (int)$_POST['order_val'] : 1;

    // Carpeta de subidas de categorías
    $upload_dir = '../uploads/categories/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $image_path = isset($_POST['existing_image']) ? $_POST['existing_image'] : '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg'])) {
            $new_filename = 'cat_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($file_tmp, $upload_dir . $new_filename)) {
                if ($ext !== 'svg') {
                    $webp_file = convertToWebP($upload_dir . $new_filename);
                    $image_path = 'categories/' . basename($webp_file);
                } else {
                    $image_path = 'categories/' . $new_filename;
                }
            }
        }
    }

    if (empty($name)) {
        $error = 'El título de la tarjeta es obligatorio.';
    } else {
        try {
            $stmtUB = $pdo->prepare("UPDATE categorias SET name = ?, description = ?, custom_link = ?, order_val = ?, image = ? WHERE id = ?");
            $stmtUB->execute([$name, $description, $custom_link, $order_val, $image_path, $bento_id]);
            $message = 'Tarjeta de la grilla Bento actualizada correctamente.';
        } catch (PDOException $e) {
            $error = 'Error al actualizar tarjeta: ' . $e->getMessage();
        }
    }
}

// Procesar Formulario de Tarjetas (Creación o Edición)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action_header_texts']) && !isset($_POST['action_header_obras']) && !isset($_POST['action_header_accesorios']) && !isset($_POST['action_header_bento']) && !isset($_POST['action_save_bento_card'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $section_key = trim($_POST['section_key']);
    $group_name = trim($_POST['group_name'] ?? '');
    $title = trim($_POST['title']);
    $subtitle = trim($_POST['subtitle'] ?? '');
    $btn_text = trim($_POST['btn_text'] ?? '');
    $btn_link = trim($_POST['btn_link'] ?? '');
    $order_val = isset($_POST['order_val']) ? (int)$_POST['order_val'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Carpeta de subidas
    $upload_dir = '../uploads/sections/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $image_path = isset($_POST['existing_image']) ? $_POST['existing_image'] : '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg'])) {
            $new_filename = 'sec_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($file_tmp, $upload_dir . $new_filename)) {
                if ($ext !== 'svg') {
                    $webp_file = convertToWebP($upload_dir . $new_filename);
                    $image_path = 'sections/' . basename($webp_file);
                } else {
                    $image_path = 'sections/' . $new_filename;
                }
            }
        }
    }

    if (empty($title)) {
        $error = 'El título de la tarjeta es obligatorio.';
    } else {
        if ($id > 0) {
            // Edición
            try {
                $stmt = $pdo->prepare("UPDATE secciones_home SET section_key = ?, group_name = ?, title = ?, subtitle = ?, image = ?, btn_text = ?, btn_link = ?, order_val = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$section_key, $group_name, $title, $subtitle, $image_path, $btn_text, $btn_link, $order_val, $is_active, $id]);
                $message = 'Tarjeta actualizada correctamente.';
            } catch (PDOException $e) {
                $error = 'Error al actualizar tarjeta: ' . $e->getMessage();
            }
        } else {
            // Creación
            try {
                $stmt = $pdo->prepare("INSERT INTO secciones_home (section_key, group_name, title, subtitle, image, btn_text, btn_link, order_val, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$section_key, $group_name, $title, $subtitle, $image_path, $btn_text, $btn_link, $order_val, $is_active]);
                $message = 'Tarjeta creada correctamente.';
            } catch (PDOException $e) {
                $error = 'Error al crear tarjeta: ' . $e->getMessage();
            }
        }
    }
}

// Cargar configuraciones actuales
$settings = getSiteSettings($pdo);

// Cargar tarjetas de la base de datos
$cards_obras = $pdo->query("SELECT * FROM secciones_home WHERE section_key = 'obras_taller' ORDER BY CASE WHEN order_val IS NULL OR order_val = 0 THEN 999999 ELSE order_val END ASC, id ASC")->fetchAll();
$cards_soluciones = $pdo->query("SELECT * FROM secciones_home WHERE section_key = 'soluciones' ORDER BY CASE WHEN order_val IS NULL OR order_val = 0 THEN 999999 ELSE order_val END ASC, id ASC")->fetchAll();
$cards_catalogo = $pdo->query("SELECT * FROM secciones_home WHERE section_key = 'catalogo_opciones' ORDER BY CASE WHEN order_val IS NULL OR order_val = 0 THEN 999999 ELSE order_val END ASC, id ASC")->fetchAll();
$cards_accesorios = $pdo->query("SELECT * FROM secciones_home WHERE section_key = 'accesorios' ORDER BY CASE WHEN order_val IS NULL OR order_val = 0 THEN 999999 ELSE order_val END ASC, id ASC")->fetchAll();

// Cargar las 4 categorías destacadas del Bento Grid
$bento_cards = $pdo->query("SELECT * FROM categorias WHERE is_active = 1 AND is_featured = 1 ORDER BY CASE WHEN order_val IS NULL OR order_val = 0 THEN 999999 ELSE order_val END ASC, id ASC LIMIT 4")->fetchAll();

// Cargar tarjeta Bento a editar si se solicita
$edit_bento = null;
if (isset($_GET['edit_bento'])) {
    $edit_bento_id = (int)$_GET['edit_bento'];
    $stmtEB = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
    $stmtEB->execute([$edit_bento_id]);
    $edit_bento = $stmtEB->fetch();
}

// Cargar tarjeta a editar
$edit_card = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM secciones_home WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_card = $stmt->fetch();
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
    <title>Gestión de Categorías | CardNet.ec</title>
    <link rel="stylesheet" href="../css/base.css?v=5.7">
    <link rel="stylesheet" href="../css/layout.css?v=5.7">
    <link rel="stylesheet" href="../css/components.css?v=5.7">
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
            max-width: calc(100vw - 250px);
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
            vertical-align: middle;
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
        
        .card-thumb-preview {
            width: 70px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid var(--border);
            background: #222;
        }
        .section-badge-obras {
            background: #FEF3C7;
            color: #92400E;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .section-badge-sol {
            background: #E0E7FF;
            color: #3730A3;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .section-badge-cat {
            background: #DCFCE7;
            color: #166534;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .section-badge-acc {
            background: #E0F2FE;
            color: #0369A1;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
    <link rel="stylesheet" href="../css/admin.css?v=6.3">
</head>
<body>

    <div class="sidebar">
        <img src="../images/logo.png?v=2.0" alt="CardNet Logo" class="sidebar-logo">
        <nav class="nav-admin">
            <a href="index.php" class="nav-admin-link">Dashboard</a>
            <a href="carnets-empresas.php" class="nav-admin-link">Credenciales y Empresas</a>
            <a href="categorias.php" class="nav-admin-link">Categorías</a>
            <a href="secciones.php" class="nav-admin-link active">Secciones Home</a>
            <a href="tagua.php" class="nav-admin-link">Tagua</a>
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
        <h1 style="font-family: var(--font-heading); margin-bottom: 0.5rem; font-size: 2rem;">Gestión de Secciones de Portada</h1>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Edita los títulos, fotos, descripciones y tarjetas de las secciones de portada: <em>Líneas de Personalización (Grilla Bento)</em>, <em>Obras del Taller (Carrusel)</em>, <em>Soluciones de Taller</em>, <em>Catálogo de Opciones</em> y <em>Accesorios para el uso diario</em>.</p>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- 0. SECCIÓN DESTACADA: LÍNEAS DE PERSONALIZACIÓN DE AUTOR (BENTO GRID) -->
        <div class="form-container" id="seccion-bento" style="border-left: 4px solid #10b981; margin-bottom: 2.5rem; box-shadow: var(--shadow-sm);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h2 style="font-family: var(--font-heading); font-size: 1.35rem; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <span style="background: #10b981; color: white; padding: 3px 10px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Grilla Principal</span>
                        Líneas de Personalización de Autor (Bento Grid)
                    </h2>
                    <p style="color: var(--text-muted); font-size: 0.88rem; margin: 4px 0 0 0;">
                        Controla los textos del encabezado superior y las 4 tarjetas visuales principales de la portada.
                    </p>
                </div>
                <div style="display: flex; gap: 8px;">
                    <a href="categorias.php" class="btn btn-secondary" style="font-size: 0.8rem; padding: 6px 12px;">Ver Catálogo de Categorías</a>
                    <a href="../index.php#categorias-visuales" target="_blank" class="btn btn-secondary" style="font-size: 0.8rem; padding: 6px 12px; background: #f0fdf4; border-color: #86efac; color: #15803d;">Ver en Portada ↗</a>
                </div>
            </div>

            <!-- Formulario de Textos de Encabezado -->
            <form method="POST" action="secciones.php" style="background: #f8fafc; padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border); margin-bottom: 2rem;">
                <input type="hidden" name="action_header_bento" value="1">
                <h3 style="font-size: 1rem; font-weight: 600; margin-top: 0; margin-bottom: 1rem; color: var(--dark); display: flex; align-items: center; gap: 6px;">
                    <span>📝</span> Textos del Encabezado de la Sección
                </h3>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="bento_subtitle">Subtítulo Superior (Badge verde)</label>
                        <input class="form-input" type="text" name="bento_subtitle" id="bento_subtitle" required value="<?php echo htmlspecialchars($settings['bento_subtitle'] ?: 'Maestría en Materiales'); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="bento_title">Título Principal de la Sección</label>
                        <input class="form-input" type="text" name="bento_title" id="bento_title" required value="<?php echo htmlspecialchars($settings['bento_title'] ?: 'Líneas de personalización de autor'); ?>">
                    </div>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="bento_desc">Descripción / Párrafo Inferior</label>
                    <textarea class="form-input" name="bento_desc" id="bento_desc" rows="2" required><?php echo htmlspecialchars($settings['bento_desc'] ?: 'No producimos volumen genérico descartable. Grabamos y personalizamos piezas nobles con acabado indeleble, textura palpable y control de calidad individual.'); ?></textarea>
                </div>
                <div style="margin-top: 1.25rem;">
                    <button class="btn btn-primary" type="submit" style="background-color: #10b981; border-color: #10b981;">Guardar Encabezado de Líneas de Autor</button>
                </div>
            </form>

            <!-- Formulario de Edición de Tarjeta Bento (si se solicitó editar una) -->
            <?php if ($edit_bento): ?>
            <div style="background: #ecfdf5; border: 2px solid #10b981; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem;" id="form-edit-bento">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="margin: 0; font-size: 1.05rem; color: #065f46; font-weight: 700;">
                        ✏️ Editando Tarjeta Bento: "<?php echo htmlspecialchars($edit_bento['name']); ?>"
                    </h3>
                    <a href="secciones.php" class="btn btn-secondary" style="font-size: 0.75rem; padding: 4px 10px;">✕ Cerrar Edición</a>
                </div>

                <form method="POST" action="secciones.php" enctype="multipart/form-data">
                    <input type="hidden" name="action_save_bento_card" value="1">
                    <input type="hidden" name="bento_id" value="<?php echo (int)$edit_bento['id']; ?>">
                    <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($edit_bento['image'] ?? ''); ?>">

                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label" for="bento_card_name">Título de la Tarjeta *</label>
                            <input class="form-input" type="text" name="name" id="bento_card_name" required value="<?php echo htmlspecialchars($edit_bento['name']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="bento_card_link">Enlace de Destino</label>
                            <input class="form-input" type="text" name="custom_link" id="bento_card_link" placeholder="Ej: productos.php?cat=personalizacion" value="<?php echo htmlspecialchars($edit_bento['custom_link'] ?: ('productos.php?cat=' . $edit_bento['slug'])); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="bento_card_order">Posición en la Grilla (1, 2, 3, 4)</label>
                            <input class="form-input" type="number" name="order_val" id="bento_card_order" required value="<?php echo (int)$edit_bento['order_val']; ?>">
                            <small style="color: var(--text-muted); font-size: 0.72rem; display: block; margin-top: 3px;">1: Sup. Izq. Ancha | 2: Inf. Izq. 1 | 3: Inf. Izq. 2 | 4: Derecha Alta</small>
                        </div>
                    </div>

                    <div class="grid-2" style="margin-top: 1rem;">
                        <div class="form-group">
                            <label class="form-label" for="bento_card_desc">Subtítulo / Bajada de la Tarjeta (Opcional)</label>
                            <textarea class="form-input" name="description" id="bento_card_desc" rows="2" placeholder="Texto descriptivo corto..."><?php echo htmlspecialchars($edit_bento['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="bento_card_image">Cambiar Foto / Imagen de Fondo</label>
                            <input class="form-input" type="file" name="image" id="bento_card_image">
                            <?php if (!empty($edit_bento['image'])): ?>
                                <div style="margin-top: 6px; display: flex; align-items: center; gap: 8px;">
                                    <img src="<?php echo htmlspecialchars(getUploadedImgUrl($edit_bento['image'])); ?>" style="width: 60px; height: 42px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border);" alt="Foto actual">
                                    <small style="color: var(--text-muted);">Foto actual asignada</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="margin-top: 1.25rem; display: flex; gap: 10px;">
                        <button class="btn btn-primary" type="submit" style="background-color: #10b981; border-color: #10b981;">Guardar Cambios de Tarjeta</button>
                        <a href="secciones.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <!-- Visualización de las 4 Tarjetas Actuales del Bento Grid -->
            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--dark); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;">
                <span>🖼️ Las 4 Tarjetas Visuales de la Portada</span>
                <span style="font-size: 0.8rem; font-weight: 400; color: var(--text-muted);">Haz clic en "Editar Tarjeta" en cualquiera para cambiar foto, texto o enlace</span>
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                <?php 
                $pos_labels = [
                    1 => 'Posición #1 (Superior Izq. - Ancha)',
                    2 => 'Posición #2 (Inferior Izq. 1)',
                    3 => 'Posición #3 (Inferior Izq. 2)',
                    4 => 'Posición #4 (Columna Derecha - Alta)'
                ];
                foreach ($bento_cards as $idx => $bCard): 
                    $pos_num = $idx + 1;
                    $pos_label = $pos_labels[$pos_num] ?? ("Posición #" . $pos_num);
                ?>
                    <div style="background: white; border: 1px solid var(--border); border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                        <div style="position: relative; aspect-ratio: 16/10; background: #1c1b1b; overflow: hidden;">
                            <?php if (!empty($bCard['image'])): ?>
                                <img src="<?php echo htmlspecialchars(getUploadedImgUrl($bCard['image'])); ?>" style="width: 100%; height: 100%; object-fit: cover; filter: grayscale(70%);" alt="<?php echo htmlspecialchars($bCard['name']); ?>">
                            <?php else: ?>
                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#888; font-size:0.8rem;">Sin imagen</div>
                            <?php endif; ?>
                            <span style="position: absolute; top: 8px; left: 8px; background: rgba(0,0,0,0.8); color: #9eff42; padding: 2px 8px; border-radius: 4px; font-size: 0.68rem; font-weight: 700;">
                                <?php echo $pos_label; ?>
                            </span>
                            <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 12px; background: linear-gradient(to top, rgba(0,0,0,0.92) 0%, transparent 100%); color: white;">
                                <div style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 500;"><?php echo htmlspecialchars($bCard['name']); ?></div>
                                <?php if (!empty($bCard['description'])): ?>
                                    <div style="font-size: 0.75rem; color: rgba(255,255,255,0.75); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px;"><?php echo htmlspecialchars($bCard['description']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="padding: 12px; display: flex; flex-direction: column; gap: 6px; flex-grow: 1; background: #fafafa;">
                            <div style="font-size: 0.78rem; color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
                                <strong>Enlace:</strong> <code style="font-size: 0.72rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($bCard['custom_link'] ?: ('productos.php?cat=' . $bCard['slug'])); ?></code>
                            </div>
                            <div style="margin-top: auto; padding-top: 8px; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.75rem; color: var(--text-muted);">Orden: <strong>#<?php echo (int)$bCard['order_val']; ?></strong></span>
                                <a href="secciones.php?edit_bento=<?php echo (int)$bCard['id']; ?>#form-edit-bento" class="btn btn-secondary" style="font-size: 0.75rem; padding: 4px 10px; background: white;">
                                    ✏️ Editar Tarjeta
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 1. FORMULARIO DE TEXTOS DE ENCABEZADO: OBRAS DEL TALLER -->
        <div class="form-container" style="border-left: 4px solid var(--primary); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <div>
                    <h2 style="font-family: var(--font-heading); font-size: 1.25rem; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <span class="section-badge-obras">Encabezado</span> Textos de la Sección: "Obras del Taller"
                    </h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin: 4px 0 0 0;">Personaliza el subtítulo, título y descripción que aparecen arriba del carrusel de piezas seleccionadas.</p>
                </div>
            </div>

            <form method="POST" action="secciones.php">
                <input type="hidden" name="action_header_obras" value="1">
                
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="obras_subtitle">Subtítulo de la Sección (Texto pequeño verde)</label>
                        <input class="form-input" type="text" name="obras_subtitle" id="obras_subtitle" required value="<?php echo htmlspecialchars($settings['obras_subtitle'] ?: 'Obras del Taller'); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="obras_title">Título Principal de la Sección</label>
                        <input class="form-input" type="text" name="obras_title" id="obras_title" required value="<?php echo htmlspecialchars($settings['obras_title'] ?: 'Piezas seleccionadas para personalizar'); ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="obras_desc">Descripción / Texto Inferior</label>
                    <textarea class="form-input" name="obras_desc" id="obras_desc" rows="2" required><?php echo htmlspecialchars($settings['obras_desc'] ?: 'Artículos de alta resistencia diseñados para acoger tu marca con grabado láser de máxima definición.'); ?></textarea>
                </div>

                <div style="margin-top: 1rem;">
                    <button class="btn btn-primary" type="submit">Actualizar Encabezado de Obras del Taller</button>
                </div>
            </form>
        </div>

        <!-- 2. FORMULARIO DE TEXTOS DE ENCABEZADO: ACCESORIOS PARA EL USO DIARIO -->
        <div class="form-container" style="border-left: 4px solid #0369a1; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <div>
                    <h2 style="font-family: var(--font-heading); font-size: 1.25rem; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <span class="section-badge-acc">Encabezado</span> Textos de la Sección: "Accesorios para el uso diario"
                    </h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin: 4px 0 0 0;">Personaliza el subtítulo, título y descripción que aparecen arriba de las tarjetas de accesorios (Porta carnets, Yoyos, Fundas...).</p>
                </div>
            </div>

            <form method="POST" action="secciones.php">
                <input type="hidden" name="action_header_accesorios" value="1">
                
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="accesorios_subtitle">Subtítulo de la Sección (Texto pequeño verde)</label>
                        <input class="form-input" type="text" name="accesorios_subtitle" id="accesorios_subtitle" required value="<?php echo htmlspecialchars($settings['accesorios_subtitle'] ?: 'Accesorios Diarios'); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="accesorios_title">Título Principal de la Sección</label>
                        <input class="form-input" type="text" name="accesorios_title" id="accesorios_title" required value="<?php echo htmlspecialchars($settings['accesorios_title'] ?: 'Accesorios para el uso diario'); ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="accesorios_desc">Descripción / Texto Inferior</label>
                    <textarea class="form-input" name="accesorios_desc" id="accesorios_desc" rows="2" required><?php echo htmlspecialchars($settings['accesorios_desc'] ?: 'Complementos prácticos para proteger, portar y presentar mejor cada credencial.'); ?></textarea>
                </div>

                <div style="margin-top: 1rem;">
                    <button class="btn btn-primary" type="submit" style="background-color: #0369a1; border-color: #0369a1;">Actualizar Encabezado de Accesorios</button>
                </div>
            </form>
        </div>

        <!-- 3. FORMULARIO DE TARJETA -->
        <div class="form-container">
            <h2 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 1.25rem;">
                <?php echo $edit_card ? 'Editar Tarjeta' : 'Añadir Nueva Tarjeta a la Portada'; ?>
            </h2>

            <form method="POST" action="secciones.php" enctype="multipart/form-data">
                <?php if ($edit_card): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_card['id']; ?>">
                    <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($edit_card['image'] ?? ''); ?>">
                <?php endif; ?>

                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label" for="section_key">Sección de Portada *</label>
                        <select class="form-select" name="section_key" id="section_key" required>
                            <option value="obras_taller" <?php echo ($edit_card && $edit_card['section_key'] === 'obras_taller') ? 'selected' : ''; ?>>
                                🎨 Obras del Taller (Piezas Seleccionadas / Carrusel)
                            </option>
                            <option value="soluciones" <?php echo ($edit_card && $edit_card['section_key'] === 'soluciones') ? 'selected' : ''; ?>>
                                🏢 Soluciones de Taller (Empresas, Eventos...)
                            </option>
                            <option value="catalogo_opciones" <?php echo ($edit_card && $edit_card['section_key'] === 'catalogo_opciones') ? 'selected' : ''; ?>>
                                🏷️ Catálogo (Cintas y Credenciales)
                            </option>
                            <option value="accesorios" <?php echo ($edit_card && $edit_card['section_key'] === 'accesorios') ? 'selected' : ''; ?>>
                                🪪 Accesorios Diarios (Porta carnets, Yoyos retráctiles, Fundas...)
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="group_name">Grupo / Subcategoría (Opcional)</label>
                        <input class="form-input" type="text" name="group_name" id="group_name" placeholder="Ej: Showcase, Cintas..." value="<?php echo $edit_card ? htmlspecialchars($edit_card['group_name'] ?? '') : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="order_val">Orden (1, 2, 3...)</label>
                        <input class="form-input" type="number" name="order_val" id="order_val" required value="<?php echo $edit_card ? (int)$edit_card['order_val'] : '1'; ?>">
                    </div>
                </div>

                <div class="grid-2" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="title">Título de la Tarjeta *</label>
                        <input class="form-input" type="text" name="title" id="title" required placeholder="Ej: Carnets PVC Corporativos, Termos..." value="<?php echo $edit_card ? htmlspecialchars($edit_card['title']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="image">Imagen / Foto de la Tarjeta</label>
                        <input class="form-input" type="file" name="image" id="image">
                        <?php if ($edit_card && !empty($edit_card['image'])): ?>
                            <div style="margin-top: 6px; display: flex; align-items: center; gap: 8px;">
                                <img src="<?php echo htmlspecialchars(getUploadedImgUrl($edit_card['image'])); ?>" class="card-thumb-preview" alt="Foto actual">
                                <small style="color: var(--text-muted);">Foto asignada</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="subtitle">Descripción / Texto de la Tarjeta</label>
                    <textarea class="form-input" name="subtitle" id="subtitle" rows="2" placeholder="Describe brevemente la pieza o solución..."><?php echo $edit_card ? htmlspecialchars($edit_card['subtitle'] ?? '') : ''; ?></textarea>
                </div>

                <div class="grid-2" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="btn_text">Texto del Botón (Para Soluciones/Catálogo)</label>
                        <input class="form-input" type="text" name="btn_text" id="btn_text" placeholder="Ej: Cotizar, Ver catálogo..." value="<?php echo $edit_card ? htmlspecialchars($edit_card['btn_text'] ?? '') : 'Cotizar'; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="btn_link">Enlace de Destino (Al hacer clic)</label>
                        <input class="form-input" type="text" name="btn_link" id="btn_link" placeholder="Ej: producto.php?slug=credenciales-pvc" value="<?php echo $edit_card ? htmlspecialchars($edit_card['btn_link'] ?? '') : 'cotizacion.php'; ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer;">
                        <input type="checkbox" name="is_active" <?php echo (!$edit_card || $edit_card['is_active']) ? 'checked' : ''; ?>>
                        <strong>Tarjeta Activa (Visible en la Portada)</strong>
                    </label>
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 10px;">
                    <button class="btn btn-primary" type="submit">Guardar Tarjeta</button>
                    <?php if ($edit_card): ?>
                        <a href="secciones.php" class="btn btn-secondary">Cancelar Edición</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- TABLA 1: OBRAS DEL TALLER (CARRUSEL PIEZAS SELECCIONADAS) -->
        <h2 style="font-family: var(--font-heading); margin-bottom: 1.25rem; font-size: 1.35rem; display: flex; align-items: center; gap: 8px;">
            <span class="section-badge-obras">Carrusel</span> Obras del Taller (Tarjetas del Carrusel de Piezas Seleccionadas)
        </h2>
        <table style="margin-bottom: 3rem;">
            <thead>
                <tr>
                    <th style="width: 60px;">Orden</th>
                    <th style="width: 80px;">Foto</th>
                    <th>Título</th>
                    <th>Subtítulo / Descripción</th>
                    <th>Enlace al hacer Clic</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cards_obras)): ?>
                    <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">No hay tarjetas registradas para Obras del Taller.</td></tr>
                <?php else: ?>
                    <?php foreach ($cards_obras as $card): ?>
                        <tr>
                            <td><strong>#<?php echo (int)$card['order_val']; ?></strong></td>
                            <td>
                                <?php if (!empty($card['image'])): ?>
                                    <img src="<?php echo htmlspecialchars(getUploadedImgUrl($card['image'])); ?>" class="card-thumb-preview" alt="Miniatura">
                                <?php else: ?>
                                    <div style="width: 70px; height: 50px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">Sin foto</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($card['title']); ?></strong></td>
                            <td style="max-width: 250px; color: var(--text-muted); font-size: 0.84rem;"><?php echo htmlspecialchars($card['subtitle'] ?? '—'); ?></td>
                            <td>
                                <code style="font-size: 0.8rem; background: #f3f4f6; padding: 2px 6px; border-radius: 4px;"><?php echo htmlspecialchars($card['btn_link'] ?? 'cotizacion.php'); ?></code>
                            </td>
                            <td>
                                <span class="badge <?php echo $card['is_active'] ? 'badge-success' : 'badge-danger'; ?>" style="font-size: 0.7rem; border-radius: 4px; padding: 2px 6px;">
                                    <?php echo $card['is_active'] ? 'Activa' : 'Inactiva'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="secciones.php?edit=<?php echo $card['id']; ?>" style="color: var(--primary); text-decoration: none; font-weight: bold; margin-right: 10px;">Editar</a>
                                <a href="secciones.php?delete=<?php echo $card['id']; ?>" onclick="return confirm('¿Eliminar esta tarjeta?')" style="color: #EF4444; text-decoration: none; font-weight: bold;">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- TABLA 2: SOLUCIONES DE TALLER -->
        <h2 style="font-family: var(--font-heading); margin-bottom: 1.25rem; font-size: 1.35rem; display: flex; align-items: center; gap: 8px;">
            <span class="section-badge-sol">Sección</span> Soluciones de Taller (Empresas, Instituciones, Eventos)
        </h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">Orden</th>
                    <th style="width: 80px;">Foto</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Botón / Enlace</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cards_soluciones as $card): ?>
                    <tr>
                        <td><strong>#<?php echo (int)$card['order_val']; ?></strong></td>
                        <td>
                            <?php if (!empty($card['image'])): ?>
                                <img src="<?php echo htmlspecialchars(getUploadedImgUrl($card['image'])); ?>" class="card-thumb-preview" alt="Miniatura">
                            <?php else: ?>
                                <div style="width: 70px; height: 50px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">Sin foto</div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($card['title']); ?></strong></td>
                        <td style="max-width: 250px; color: var(--text-muted); font-size: 0.84rem;"><?php echo htmlspecialchars($card['subtitle'] ?? '—'); ?></td>
                        <td>
                            <span style="font-weight: 500;"><?php echo htmlspecialchars($card['btn_text'] ?? 'Cotizar'); ?></span><br>
                            <small><code><?php echo htmlspecialchars($card['btn_link'] ?? 'cotizacion.php'); ?></code></small>
                        </td>
                        <td>
                            <span class="badge <?php echo $card['is_active'] ? 'badge-success' : 'badge-danger'; ?>" style="font-size: 0.7rem; border-radius: 4px; padding: 2px 6px;">
                                <?php echo $card['is_active'] ? 'Activa' : 'Inactiva'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="secciones.php?edit=<?php echo $card['id']; ?>" style="color: var(--primary); text-decoration: none; font-weight: bold; margin-right: 10px;">Editar</a>
                            <a href="secciones.php?delete=<?php echo $card['id']; ?>" onclick="return confirm('¿Eliminar esta tarjeta?')" style="color: #EF4444; text-decoration: none; font-weight: bold;">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- TABLA 2: CATÁLOGO DE CINTAS Y CREDENCIALES -->
        <h2 style="font-family: var(--font-heading); margin-bottom: 1.25rem; font-size: 1.35rem; display: flex; align-items: center; gap: 8px; margin-top: 3rem;">
            <span class="section-badge-cat">Sección 2</span> Opciones de Cintas y Credenciales (Catálogo Detallado)
        </h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">Orden</th>
                    <th style="width: 80px;">Foto</th>
                    <th>Grupo</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Botón / Enlace</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cards_catalogo as $card): ?>
                    <tr>
                        <td><strong>#<?php echo (int)$card['order_val']; ?></strong></td>
                        <td>
                            <?php if (!empty($card['image'])): ?>
                                <img src="<?php echo htmlspecialchars(getUploadedImgUrl($card['image'])); ?>" class="card-thumb-preview" alt="Miniatura">
                            <?php else: ?>
                                <div style="width: 70px; height: 50px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">Sin foto</div>
                            <?php endif; ?>
                        </td>
                        <td><span style="font-size: 0.82rem; font-weight: 600; color: var(--primary); background: #f0fdf4; padding: 2px 6px; border-radius: 4px;"><?php echo htmlspecialchars($card['group_name'] ?: 'General'); ?></span></td>
                        <td><strong><?php echo htmlspecialchars($card['title']); ?></strong></td>
                        <td style="max-width: 250px; color: var(--text-muted); font-size: 0.84rem;"><?php echo htmlspecialchars($card['subtitle'] ?? '—'); ?></td>
                        <td>
                            <span style="font-weight: 500;"><?php echo htmlspecialchars($card['btn_text'] ?? 'Cotizar'); ?></span><br>
                            <small><code><?php echo htmlspecialchars($card['btn_link'] ?? 'cotizacion.php'); ?></code></small>
                        </td>
                        <td>
                            <span class="badge <?php echo $card['is_active'] ? 'badge-success' : 'badge-danger'; ?>" style="font-size: 0.7rem; border-radius: 4px; padding: 2px 6px;">
                                <?php echo $card['is_active'] ? 'Activa' : 'Inactiva'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="secciones.php?edit=<?php echo $card['id']; ?>" style="color: var(--primary); text-decoration: none; font-weight: bold; margin-right: 10px;">Editar</a>
                            <a href="secciones.php?delete=<?php echo $card['id']; ?>" onclick="return confirm('¿Eliminar esta tarjeta?')" style="color: #EF4444; text-decoration: none; font-weight: bold;">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- TABLA 4: ACCESORIOS PARA EL USO DIARIO -->
        <h2 style="font-family: var(--font-heading); margin-bottom: 1.25rem; font-size: 1.35rem; display: flex; align-items: center; gap: 8px; margin-top: 3rem;">
            <span class="section-badge-acc">Sección 3</span> Accesorios para el Uso Diario (Porta carnets, Yoyos retráctiles, Fundas...)
        </h2>
        <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: -0.5rem; margin-bottom: 1.25rem;">Tarjetas ilustrativas que se muestran en el bloque de accesorios diarios de la portada.</p>
        <table style="margin-bottom: 3rem;">
            <thead>
                <tr>
                    <th style="width: 60px;">Orden</th>
                    <th style="width: 80px;">Foto</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Botón / Enlace</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cards_accesorios)): ?>
                    <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">No hay tarjetas registradas para Accesorios Diarios.</td></tr>
                <?php else: ?>
                    <?php foreach ($cards_accesorios as $card): ?>
                        <tr>
                            <td><strong>#<?php echo (int)$card['order_val']; ?></strong></td>
                            <td>
                                <?php if (!empty($card['image'])): ?>
                                    <img src="<?php echo htmlspecialchars(getUploadedImgUrl($card['image'])); ?>" class="card-thumb-preview" alt="Miniatura">
                                <?php else: ?>
                                    <div style="width: 70px; height: 50px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">Sin foto</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($card['title']); ?></strong></td>
                            <td style="max-width: 250px; color: var(--text-muted); font-size: 0.84rem;"><?php echo htmlspecialchars($card['subtitle'] ?? '—'); ?></td>
                            <td>
                                <span style="font-weight: 500;"><?php echo htmlspecialchars($card['btn_text'] ?? 'Ver opciones'); ?></span><br>
                                <small><code><?php echo htmlspecialchars($card['btn_link'] ?? 'productos.php'); ?></code></small>
                            </td>
                            <td>
                                <span class="badge <?php echo $card['is_active'] ? 'badge-success' : 'badge-danger'; ?>" style="font-size: 0.7rem; border-radius: 4px; padding: 2px 6px;">
                                    <?php echo $card['is_active'] ? 'Activa' : 'Inactiva'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="secciones.php?edit=<?php echo $card['id']; ?>" style="color: var(--primary); text-decoration: none; font-weight: bold; margin-right: 10px;">Editar</a>
                                <a href="secciones.php?delete=<?php echo $card['id']; ?>" onclick="return confirm('¿Eliminar esta tarjeta?')" style="color: #EF4444; text-decoration: none; font-weight: bold;">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
    // Preservar scroll al interactuar con formularios
    window.addEventListener('beforeunload', function() {
        sessionStorage.setItem('admin_secciones_scroll', window.scrollY);
    });
    document.addEventListener('DOMContentLoaded', function() {
        const savedY = sessionStorage.getItem('admin_secciones_scroll');
        if (savedY !== null) {
            window.scrollTo(0, parseInt(savedY, 10));
        }
    });
    </script>
</body>
</html>
