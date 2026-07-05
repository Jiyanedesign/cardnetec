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
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Producto eliminado correctamente.';
    } catch (PDOException $e) {
        $error = 'Error al eliminar: ' . $e->getMessage();
    }
}

// Procesar Formulario (Creación o Edición)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $description_short = trim($_POST['description_short']);
    $category_id = (int)$_POST['category_id'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $allows_simulation = isset($_POST['allows_simulation']) ? 1 : 0;
    $cta_text = trim($_POST['cta_text']);
    
    // Obtener nombre de la categoría para guardar en texto para compatibilidad
    $stmtCat = $pdo->prepare("SELECT name FROM categorias WHERE id = ?");
    $stmtCat->execute([$category_id]);
    $category_name = $stmtCat->fetchColumn() ?: 'Artículos personalizados';

    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }

    // Subida de imagen
    $image_filename = isset($_POST['existing_image']) ? $_POST['existing_image'] : '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $new_filename = 'prod_' . time() . '_' . uniqid() . '.' . $ext;
            $dest_path = '../uploads/products/' . $new_filename;
            
            if (!is_dir('../uploads/products')) {
                mkdir('../uploads/products', 0755, true);
            }
            
            if (move_uploaded_file($file_tmp, $dest_path)) {
                $image_filename = 'products/' . $new_filename;
            } else {
                $error = 'Error al mover el archivo subido.';
            }
        } else {
            $error = 'Extensión de archivo no permitida.';
        }
    }

    if (empty($error)) {
        if ($id > 0) {
            // Edición
            try {
                $stmt = $pdo->prepare("UPDATE productos SET name = ?, slug = ?, description_short = ?, category = ?, category_id = ?, image_main = ?, is_active = ?, is_featured = ?, allows_simulation = ?, cta_text = ? WHERE id = ?");
                $stmt->execute([$name, $slug, $description_short, $category_name, $category_id, $image_filename, $is_active, $is_featured, $allows_simulation, $cta_text, $id]);
                $message = 'Producto actualizado correctamente.';
            } catch (PDOException $e) {
                $error = 'Error al actualizar base de datos: ' . $e->getMessage();
            }
        } else {
            // Creación
            try {
                $stmt = $pdo->prepare("INSERT INTO productos (name, slug, description_short, category, category_id, image_main, is_active, is_featured, allows_simulation, cta_text) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $slug, $description_short, $category_name, $category_id, $image_filename, $is_active, $is_featured, $allows_simulation, $cta_text]);
                $message = 'Producto creado correctamente.';
            } catch (PDOException $e) {
                $error = 'Error al crear producto: ' . $e->getMessage();
            }
        }
    }
}

// Cargar todas las categorías activas para el select
$categorias = $pdo->query("SELECT * FROM categorias WHERE is_active = 1 ORDER BY order_val ASC")->fetchAll();

// Cargar todos los productos con el nombre de su categoría
$products = $pdo->query("SELECT p.*, c.name as category_name FROM productos p LEFT JOIN categorias c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();

// Cargar producto a editar
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_product = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos | CardNet.ec</title>
    <link rel="stylesheet" href="../css/base.css?v=1.1.2">
    <link rel="stylesheet" href="../css/layout.css?v=1.1.2">
    <link rel="stylesheet" href="../css/components.css?v=1.1.2">
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
</head>
<body>

    <div class="sidebar">
        <img src="../images/logo.png" alt="CardNet Logo" class="sidebar-logo">
        <nav class="nav-admin">
            <a href="index.php" class="nav-admin-link">Dashboard</a>
            <a href="categorias.php" class="nav-admin-link">Categorías</a>
            <a href="productos.php" class="nav-admin-link active">Productos</a>
            <a href="carrusel.php" class="nav-admin-link">Carrusel Hero</a>
            <a href="antes-despues.php" class="nav-admin-link">Antes y Después</a>
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
                    <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
                    <input type="hidden" name="existing_image" value="<?php echo $edit_product['image_main']; ?>">
                <?php endif; ?>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="name">Nombre del Producto</label>
                        <input class="form-input" type="text" name="name" id="name" required value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="slug">Slug (URL amigable)</label>
                        <input class="form-input" type="text" name="slug" id="slug" placeholder="Ej: termos-grabados" value="<?php echo $edit_product ? htmlspecialchars($edit_product['slug']) : ''; ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="description_short">Descripción Corta</label>
                    <textarea class="form-input" name="description_short" id="description_short" rows="2" required><?php echo $edit_product ? htmlspecialchars($edit_product['description_short']) : ''; ?></textarea>
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
                        <label class="form-label" for="image">Imagen Principal (1:1)</label>
                        <input class="form-input" type="file" name="image" id="image">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="cta_text">Texto del Botón CTA</label>
                        <input class="form-input" type="text" name="cta_text" id="cta_text" value="<?php echo $edit_product ? htmlspecialchars($edit_product['cta_text']) : 'Quiero este acabado'; ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1.5rem; display: flex; gap: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer;">
                        <input type="checkbox" name="is_active" <?php echo (!$edit_product || $edit_product['is_active']) ? 'checked' : ''; ?>> Producto Activo
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer;">
                        <input type="checkbox" name="is_featured" <?php echo ($edit_product && $edit_product['is_featured']) ? 'checked' : ''; ?>> Producto Destacado
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer;">
                        <input type="checkbox" name="allows_simulation" <?php echo ($edit_product && $edit_product['allows_simulation']) ? 'checked' : ''; ?>> Permitir Simulador Canvas
                    </label>
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 10px;">
                    <button class="btn btn-primary" type="submit">Guardar Producto</button>
                    <?php if ($edit_product): ?>
                        <a href="productos.php" class="btn btn-secondary">Cancelar Edición</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <h2 style="font-family: var(--font-heading); margin-bottom: 1.25rem; font-size: 1.4rem;">Productos Registrados</h2>
        <table>
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Destacado</th>
                    <th>Simulable</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $prod): ?>
                    <tr>
                        <td>
                            <?php if ($prod['image_main']): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($prod['image_main']); ?>" style="width: 44px; height: 44px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                                <div style="width: 44px; height: 44px; background-color: var(--border); border-radius: 4px;"></div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($prod['name']); ?></strong></td>
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
                            <a href="productos.php?delete=<?php echo $prod['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este producto?')" style="color: #EF4444; text-decoration: none; font-weight: bold;">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
