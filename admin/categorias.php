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
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Categoría eliminada correctamente.';
    } catch (PDOException $e) {
        $error = 'Error al eliminar: ' . $e->getMessage();
    }
}

// Procesar Formulario (Creación o Edición)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $description = trim($_POST['description'] ?? '');
    $custom_link = trim($_POST['custom_link'] ?? '');
    $order_val = isset($_POST['order_val']) ? (int)$_POST['order_val'] : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }

    // Carpeta de subidas
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
                $image_path = 'categories/' . $new_filename;
            }
        }
    }

    if (empty($name)) {
        $error = 'El nombre de la categoría es obligatorio.';
    } else {
        if ($id > 0) {
            // Edición
            try {
                $stmt = $pdo->prepare("UPDATE categorias SET name = ?, slug = ?, description = ?, custom_link = ?, image = ?, order_val = ?, is_featured = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$name, $slug, $description, $custom_link, $image_path, $order_val, $is_featured, $is_active, $id]);
                $message = 'Categoría actualizada correctamente.';
            } catch (PDOException $e) {
                $error = 'Error al actualizar categoría: ' . $e->getMessage();
            }
        } else {
            // Creación
            try {
                $stmt = $pdo->prepare("INSERT INTO categorias (name, slug, description, custom_link, image, order_val, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $slug, $description, $custom_link, $image_path, $order_val, $is_featured, $is_active]);
                $message = 'Categoría creada correctamente.';
            } catch (PDOException $e) {
                $error = 'Error al crear categoría: ' . $e->getMessage();
            }
        }
    }
}

// Cargar categorías ordenadas
$categorias = $pdo->query("SELECT * FROM categorias ORDER BY CASE WHEN order_val IS NULL OR order_val = 0 THEN 999999 ELSE order_val END ASC, id ASC")->fetchAll();

// Cargar categoría a editar
$edit_cat = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_cat = $stmt->fetch();
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
        
        .cat-thumb-preview {
            width: 70px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid var(--border);
            background: #222;
        }
        .info-card {
            background-color: #EFF6FF;
            border-left: 4px solid #3B82F6;
            padding: 12px 16px;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            font-size: 0.88rem;
            color: #1E40AF;
        }
    </style>
    <link rel="stylesheet" href="../css/admin.css?v=5.7">
</head>
<body>

    <div class="sidebar">
        <img src="../images/logo.png?v=2.0" alt="CardNet Logo" class="sidebar-logo">
        <nav class="nav-admin">
            <a href="index.php" class="nav-admin-link">Dashboard</a>
            <a href="categorias.php" class="nav-admin-link active">Categorías</a>
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
        <h1 style="font-family: var(--font-heading); margin-bottom: 0.5rem; font-size: 2rem;">Gestión de Categorías y Líneas del Taller</h1>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Administra las categorías de productos y las 4 tarjetas destacadas del mosaico (Bento Grid) en la portada.</p>

        <div class="info-card">
            💡 <strong>Posiciones en la Portada (Líneas del Taller):</strong>
            Las 4 categorías con el checkbox <em>"Destacar en Página Principal"</em> y con orden <strong>1, 2, 3 y 4</strong> se mostrarán automáticamente en el mosaico visual del inicio (1: Superior Izquierda Ancha, 2: Inferior Izquierda 1, 3: Inferior Izquierda 2, 4: Derecha Alta).
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h2 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 1.25rem;">
                <?php echo $edit_cat ? 'Editar Categoría / Tarjeta' : 'Añadir Nueva Categoría'; ?>
            </h2>

            <form method="POST" action="categorias.php" enctype="multipart/form-data">
                <?php if ($edit_cat): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_cat['id']; ?>">
                    <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($edit_cat['image'] ?? ''); ?>">
                <?php endif; ?>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="name">Nombre / Título de la Línea *</label>
                        <input class="form-input" type="text" name="name" id="name" required placeholder="Ej: Cintas y lanyards" value="<?php echo $edit_cat ? htmlspecialchars($edit_cat['name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="slug">Slug (URL amigable)</label>
                        <input class="form-input" type="text" name="slug" id="slug" placeholder="Ej: cintas" value="<?php echo $edit_cat ? htmlspecialchars($edit_cat['slug']) : ''; ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="description">Subtítulo / Bajada de la Tarjeta</label>
                    <input class="form-input" type="text" name="description" id="description" placeholder="Ej: Cintas impresas full color y accesorios de sujeción." value="<?php echo $edit_cat ? htmlspecialchars($edit_cat['description'] ?? '') : ''; ?>">
                </div>

                <div class="grid-3" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="custom_link">Enlace de Destino (URL o Ancla)</label>
                        <input class="form-input" type="text" name="custom_link" id="custom_link" placeholder="Ej: productos.php?cat=cintas o #laser" value="<?php echo $edit_cat ? htmlspecialchars($edit_cat['custom_link'] ?? '') : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="order_val">Orden de Posición (1, 2, 3, 4...)</label>
                        <input class="form-input" type="number" name="order_val" id="order_val" required placeholder="1, 2, 3..." value="<?php echo $edit_cat ? (int)$edit_cat['order_val'] : '1'; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="image">Imagen de Fondo / Portada</label>
                        <input class="form-input" type="file" name="image" id="image">
                        <?php if ($edit_cat && !empty($edit_cat['image'])): ?>
                            <div style="margin-top: 6px; display: flex; align-items: center; gap: 8px;">
                                <?php 
                                    $img_src = (strpos($edit_cat['image'], 'images/') === 0 || strpos($edit_cat['image'], 'uploads/') === 0) 
                                        ? '../' . $edit_cat['image'] 
                                        : '../uploads/' . $edit_cat['image'];
                                ?>
                                <img src="<?php echo htmlspecialchars($img_src); ?>" class="cat-thumb-preview" alt="Foto actual">
                                <small style="color: var(--text-muted);">Imagen actual asignada</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1.5rem; display: flex; gap: 24px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer;">
                        <input type="checkbox" name="is_featured" <?php echo (!$edit_cat || !empty($edit_cat['is_featured'])) ? 'checked' : ''; ?>>
                        <strong>Destacar en Página Principal (Bento Grid - Líneas del Taller)</strong>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer;">
                        <input type="checkbox" name="is_active" <?php echo (!$edit_cat || $edit_cat['is_active']) ? 'checked' : ''; ?>>
                        Categoría Activa
                    </label>
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 10px;">
                    <button class="btn btn-primary" type="submit">Guardar Categoría</button>
                    <?php if ($edit_cat): ?>
                        <a href="categorias.php" class="btn btn-secondary">Cancelar Edición</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <h2 style="font-family: var(--font-heading); margin-bottom: 1.25rem; font-size: 1.4rem;">Categorías Registradas</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">Orden</th>
                    <th style="width: 80px;">Fondo</th>
                    <th>Nombre / Título</th>
                    <th>Subtítulo</th>
                    <th>Enlace</th>
                    <th>Destacada</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $cat): ?>
                    <tr>
                        <td><strong>#<?php echo (int)$cat['order_val']; ?></strong></td>
                        <td>
                            <?php if (!empty($cat['image'])): ?>
                                <?php 
                                    $img_src = (strpos($cat['image'], 'images/') === 0 || strpos($cat['image'], 'uploads/') === 0) 
                                        ? '../' . $cat['image'] 
                                        : '../uploads/' . $cat['image'];
                                ?>
                                <img src="<?php echo htmlspecialchars($img_src); ?>" class="cat-thumb-preview" alt="Miniatura">
                            <?php else: ?>
                                <div style="width: 70px; height: 50px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">Sin foto</div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong><br><small style="color: var(--text-muted);">Slug: <code><?php echo htmlspecialchars($cat['slug']); ?></code></small></td>
                        <td style="max-width: 200px; color: var(--text-muted); font-size: 0.85rem;"><?php echo htmlspecialchars($cat['description'] ?? '—'); ?></td>
                        <td><code><?php echo htmlspecialchars($cat['custom_link'] ?: ('productos.php?cat=' . $cat['slug'])); ?></code></td>
                        <td>
                            <?php if (!empty($cat['is_featured'])): ?>
                                <span class="badge badge-success" style="font-size: 0.7rem; padding: 2px 6px;">⭐ Destacada (Pos #<?php echo (int)$cat['order_val']; ?>)</span>
                            <?php else: ?>
                                <span class="badge" style="background: #e5e7eb; color: #6b7280; font-size: 0.7rem; padding: 2px 6px;">Normal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?php echo $cat['is_active'] ? 'badge-success' : 'badge-danger'; ?>" style="font-size: 0.7rem; border-radius: 4px; padding: 2px 6px;">
                                <?php echo $cat['is_active'] ? 'Activa' : 'Inactiva'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="categorias.php?edit=<?php echo $cat['id']; ?>" style="color: var(--primary); text-decoration: none; font-weight: bold; margin-right: 10px;">Editar</a>
                            <a href="categorias.php?delete=<?php echo $cat['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar esta categoría?')" style="color: #EF4444; text-decoration: none; font-weight: bold;">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
