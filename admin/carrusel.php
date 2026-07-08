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
        $stmt = $pdo->prepare("DELETE FROM carrusel WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Slide eliminado correctamente.';
    } catch (PDOException $e) {
        $error = 'Error al eliminar: ' . $e->getMessage();
    }
}

// Procesar Formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = trim($_POST['title']);
    $subtitle = trim($_POST['subtitle']);
    $cta_text = trim($_POST['cta_text']);
    $cta_url = trim($_POST['cta_url']);
    $order_val = (int)$_POST['order_val'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Subida de imagen
    $image_filename = isset($_POST['existing_image']) ? $_POST['existing_image'] : '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $new_filename = 'slide_' . time() . '_' . uniqid() . '.' . $ext;
            $dest_path = '../uploads/carousel/' . $new_filename;
            
            if (!is_dir('../uploads/carousel')) {
                mkdir('../uploads/carousel', 0755, true);
            }
            
            if (move_uploaded_file($file_tmp, $dest_path)) {
                $image_filename = 'carousel/' . $new_filename;
            } else {
                $error = 'Error al mover el archivo de cabecera.';
            }
        } else {
            $error = 'Extensión de archivo no permitida (solo JPG, PNG, WEBP).';
        }
    }

    if (empty($error)) {
        if ($id > 0) {
            // Edición
            try {
                $stmt = $pdo->prepare("UPDATE carrusel SET title = ?, subtitle = ?, image = ?, cta_text = ?, cta_url = ?, order_val = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$title, $subtitle, $image_filename, $cta_text, $cta_url, $order_val, $is_active, $id]);
                $message = 'Slide actualizado correctamente.';
            } catch (PDOException $e) {
                $error = 'Error al actualizar base de datos: ' . $e->getMessage();
            }
        } else {
            // Creación
            try {
                $stmt = $pdo->prepare("INSERT INTO carrusel (title, subtitle, image, cta_text, cta_url, order_val, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $subtitle, $image_filename, $cta_text, $cta_url, $order_val, $is_active]);
                $message = 'Slide creado correctamente.';
            } catch (PDOException $e) {
                $error = 'Error al crear slide: ' . $e->getMessage();
            }
        }
    }
}

// Cargar slides
$slides = $pdo->query("SELECT * FROM carrusel ORDER BY order_val ASC")->fetchAll();

// Cargar slide a editar
$edit_slide = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM carrusel WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_slide = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión del Carrusel | CardNet.ec</title>
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
    </style>
    <link rel="stylesheet" href="../css/admin.css?v=2.0">
</head>
<body>

    <div class="sidebar">
        <img src="../images/logo.png?v=2.0" alt="CardNet Logo" class="sidebar-logo">
        <nav class="nav-admin">
            <a href="index.php" class="nav-admin-link">Dashboard</a>
            <a href="categorias.php" class="nav-admin-link">Categorías</a>
            <a href="productos.php" class="nav-admin-link">Productos</a>
            <a href="materiales.php" class="nav-admin-link">Materiales</a>
            <a href="carrusel.php" class="nav-admin-link active">Carrusel Hero</a>
            <a href="antes-despues.php" class="nav-admin-link">Antes y Después</a>
            <a href="clientes.php" class="nav-admin-link">Logos Clientes</a>
            <a href="logout.php" class="nav-admin-link" style="margin-top: 2rem; color: #FCA5A5;">Cerrar Sesión</a>
        </nav>
    </div>

    <div class="main-content">
        <h1 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 2rem;">Gestión del Carrusel</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h2 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 1.25rem;">
                <?php echo $edit_slide ? 'Editar Slide' : 'Añadir Nuevo Slide'; ?>
            </h2>

            <form method="POST" action="carrusel.php" enctype="multipart/form-data">
                <?php if ($edit_slide): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_slide['id']; ?>">
                    <input type="hidden" name="existing_image" value="<?php echo $edit_slide['image']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label" for="title">Título del Slide</label>
                    <input class="form-input" type="text" name="title" id="title" required value="<?php echo $edit_slide ? htmlspecialchars($edit_slide['title']) : ''; ?>">
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="subtitle">Subtítulo / Texto Corto</label>
                    <textarea class="form-input" name="subtitle" id="subtitle" rows="2" required><?php echo $edit_slide ? htmlspecialchars($edit_slide['subtitle']) : ''; ?></textarea>
                </div>

                <div class="grid-3" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="cta_text">Texto del Botón</label>
                        <input class="form-input" type="text" name="cta_text" id="cta_text" value="<?php echo $edit_slide ? htmlspecialchars($edit_slide['cta_text']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="cta_url">Enlace del Botón</label>
                        <input class="form-input" type="text" name="cta_url" id="cta_url" placeholder="Ej: cotizacion.php" value="<?php echo $edit_slide ? htmlspecialchars($edit_slide['cta_url']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="image">Imagen de Fondo (Slide)</label>
                        <input class="form-input" type="file" name="image" id="image">
                    </div>
                </div>

                <div class="grid-2" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="order_val">Orden de Aparición</label>
                        <input class="form-input" type="number" name="order_val" id="order_val" value="<?php echo $edit_slide ? (int)$edit_slide['order_val'] : '0'; ?>">
                    </div>
                    <div class="form-group" style="display: flex; align-items: flex-end; padding-bottom: 10px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer;">
                            <input type="checkbox" name="is_active" <?php echo (!$edit_slide || $edit_slide['is_active']) ? 'checked' : ''; ?>> Slide Activo
                        </label>
                    </div>
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 10px;">
                    <button class="btn btn-primary" type="submit">Guardar Slide</button>
                    <?php if ($edit_slide): ?>
                        <a href="carrusel.php" class="btn btn-secondary">Cancelar Edición</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <h2 style="font-family: var(--font-heading); margin-bottom: 1.25rem; font-size: 1.4rem;">Slides Registrados</h2>
        <table>
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Orden</th>
                    <th>Título</th>
                    <th>Subtítulo</th>
                    <th>Texto Botón</th>
                    <th>Destino</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($slides as $sl): ?>
                    <tr>
                        <td>
                            <?php if ($sl['image']): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($sl['image']); ?>" style="width: 80px; height: 44px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                                <div style="width: 80px; height: 44px; background-color: var(--border); border-radius: 4px;"></div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo (int)$sl['order_val']; ?></td>
                        <td><strong><?php echo htmlspecialchars($sl['title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($sl['subtitle']); ?></td>
                        <td><?php echo htmlspecialchars($sl['cta_text']); ?></td>
                        <td><code><?php echo htmlspecialchars($sl['cta_url']); ?></code></td>
                        <td>
                            <span class="badge <?php echo $sl['is_active'] ? 'badge-success' : 'badge-danger'; ?>" style="font-size: 0.7rem; border-radius: 4px; padding: 2px 6px;">
                                <?php echo $sl['is_active'] ? 'Activo' : 'Inactivo'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="carrusel.php?edit=<?php echo $sl['id']; ?>" style="color: var(--primary); text-decoration: none; font-weight: bold; margin-right: 10px;">Editar</a>
                            <a href="carrusel.php?delete=<?php echo $sl['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este slide?')" style="color: #EF4444; text-decoration: none; font-weight: bold;">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
