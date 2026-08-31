<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit;
}

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

// Procesar Formulario (Creación o Edición)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                $image_path = 'sections/' . $new_filename;
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

// Cargar tarjetas de la base de datos
$cards_soluciones = $pdo->query("SELECT * FROM secciones_home WHERE section_key = 'soluciones' ORDER BY CASE WHEN order_val IS NULL OR order_val = 0 THEN 999999 ELSE order_val END ASC, id ASC")->fetchAll();
$cards_catalogo = $pdo->query("SELECT * FROM secciones_home WHERE section_key = 'catalogo_opciones' ORDER BY CASE WHEN order_val IS NULL OR order_val = 0 THEN 999999 ELSE order_val END ASC, id ASC")->fetchAll();

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
    </style>
    <link rel="stylesheet" href="../css/admin.css?v=6.1">
</head>
<body>

    <div class="sidebar">
        <img src="../images/logo.png?v=2.0" alt="CardNet Logo" class="sidebar-logo">
        <nav class="nav-admin">
            <a href="index.php" class="nav-admin-link">Dashboard</a>
            <a href="categorias.php" class="nav-admin-link">Categorías</a>
            <a href="secciones.php" class="nav-admin-link active">Secciones Home</a>
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
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Edita los títulos, fotos, descripciones y botones de las secciones <em>Soluciones de Taller</em> y <em>Catálogo de Cintas y Credenciales</em>.</p>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h2 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 1.25rem;">
                <?php echo $edit_card ? 'Editar Tarjeta' : 'Añadir Nueva Tarjeta'; ?>
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
                            <option value="soluciones" <?php echo ($edit_card && $edit_card['section_key'] === 'soluciones') ? 'selected' : ''; ?>>
                                🏢 Soluciones de Taller (Empresas, Eventos...)
                            </option>
                            <option value="catalogo_opciones" <?php echo ($edit_card && $edit_card['section_key'] === 'catalogo_opciones') ? 'selected' : ''; ?>>
                                🏷️ Catálogo (Cintas y Credenciales)
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="group_name">Grupo / Columna (Para Catálogo)</label>
                        <input class="form-input" type="text" name="group_name" id="group_name" placeholder="Ej: Cintas porta credenciales" value="<?php echo $edit_card ? htmlspecialchars($edit_card['group_name'] ?? '') : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="order_val">Orden (1, 2, 3...)</label>
                        <input class="form-input" type="number" name="order_val" id="order_val" required value="<?php echo $edit_card ? (int)$edit_card['order_val'] : '1'; ?>">
                    </div>
                </div>

                <div class="grid-2" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="title">Título de la Tarjeta *</label>
                        <input class="form-input" type="text" name="title" id="title" required placeholder="Ej: Empresas, Cintas full color..." value="<?php echo $edit_card ? htmlspecialchars($edit_card['title']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="image">Imagen / Foto de la Tarjeta</label>
                        <input class="form-input" type="file" name="image" id="image">
                        <?php if ($edit_card && !empty($edit_card['image'])): ?>
                            <div style="margin-top: 6px; display: flex; align-items: center; gap: 8px;">
                                <?php 
                                    $img_src = (strpos($edit_card['image'], 'images/') === 0 || strpos($edit_card['image'], 'uploads/') === 0) 
                                        ? '../' . $edit_card['image'] 
                                        : '../uploads/' . $edit_card['image'];
                                ?>
                                <img src="<?php echo htmlspecialchars($img_src); ?>" class="card-thumb-preview" alt="Foto actual">
                                <small style="color: var(--text-muted);">Foto asignada</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="subtitle">Descripción / Texto de la Tarjeta</label>
                    <textarea class="form-input" name="subtitle" id="subtitle" rows="2" placeholder="Describe brevemente la solución..."><?php echo $edit_card ? htmlspecialchars($edit_card['subtitle'] ?? '') : ''; ?></textarea>
                </div>

                <div class="grid-2" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="btn_text">Texto del Botón</label>
                        <input class="form-input" type="text" name="btn_text" id="btn_text" placeholder="Ej: Cotizar, Ver catálogo..." value="<?php echo $edit_card ? htmlspecialchars($edit_card['btn_text'] ?? '') : 'Cotizar'; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="btn_link">Enlace del Botón (URL)</label>
                        <input class="form-input" type="text" name="btn_link" id="btn_link" placeholder="Ej: cotizacion.php o productos.php?cat=cintas" value="<?php echo $edit_card ? htmlspecialchars($edit_card['btn_link'] ?? '') : 'cotizacion.php'; ?>">
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

        <!-- TABLA 1: SOLUCIONES DE TALLER -->
        <h2 style="font-family: var(--font-heading); margin-bottom: 1.25rem; font-size: 1.35rem; display: flex; align-items: center; gap: 8px;">
            <span class="section-badge-sol">Sección 1</span> Soluciones de Taller (Empresas, Instituciones, Eventos)
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
                                <?php 
                                    $img_src = (strpos($card['image'], 'images/') === 0 || strpos($card['image'], 'uploads/') === 0) 
                                        ? '../' . $card['image'] 
                                        : '../uploads/' . $card['image'];
                                ?>
                                <img src="<?php echo htmlspecialchars($img_src); ?>" class="card-thumb-preview" alt="Miniatura">
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
                                <?php 
                                    $img_src = (strpos($card['image'], 'images/') === 0 || strpos($card['image'], 'uploads/') === 0) 
                                        ? '../' . $card['image'] 
                                        : '../uploads/' . $card['image'];
                                ?>
                                <img src="<?php echo htmlspecialchars($img_src); ?>" class="card-thumb-preview" alt="Miniatura">
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
    </div>

</body>
</html>
