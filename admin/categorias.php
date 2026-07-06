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

// Procesar Formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $order_val = (int)$_POST['order_val'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }

    if ($id > 0) {
        // Edición
        try {
            $stmt = $pdo->prepare("UPDATE categorias SET name = ?, slug = ?, order_val = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $order_val, $is_active, $id]);
            $message = 'Categoría actualizada correctamente.';
        } catch (PDOException $e) {
            $error = 'Error al actualizar: ' . $e->getMessage();
        }
    } else {
        // Creación
        try {
            $stmt = $pdo->prepare("INSERT INTO categorias (name, slug, order_val, is_active) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $order_val, $is_active]);
            $message = 'Categoría creada correctamente.';
        } catch (PDOException $e) {
            $error = 'Error al crear: ' . $e->getMessage();
        }
    }
}

// Cargar categorías
$categorias = $pdo->query("SELECT * FROM categorias ORDER BY order_val ASC")->fetchAll();

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
            <a href="categorias.php" class="nav-admin-link active">Categorías</a>
            <a href="productos.php" class="nav-admin-link">Productos</a>
            <a href="carrusel.php" class="nav-admin-link">Carrusel Hero</a>
            <a href="antes-despues.php" class="nav-admin-link">Antes y Después</a>
            <a href="configuracion.php" class="nav-admin-link">Configuración</a>
            <a href="logout.php" class="nav-admin-link" style="margin-top: 2rem; color: #FCA5A5;">Cerrar Sesión</a>
        </nav>
    </div>

    <div class="main-content">
        <h1 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 2rem;">Gestión de Categorías</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h2 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 1.25rem;">
                <?php echo $edit_cat ? 'Editar Categoría' : 'Añadir Nueva Categoría'; ?>
            </h2>

            <form method="POST" action="categorias.php">
                <?php if ($edit_cat): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_cat['id']; ?>">
                <?php endif; ?>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="name">Nombre de la Categoría</label>
                        <input class="form-input" type="text" name="name" id="name" required value="<?php echo $edit_cat ? htmlspecialchars($edit_cat['name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="slug">Slug (URL amigable)</label>
                        <input class="form-input" type="text" name="slug" id="slug" value="<?php echo $edit_cat ? htmlspecialchars($edit_cat['slug']) : ''; ?>">
                    </div>
                </div>

                <div class="grid-2" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="order_val">Orden de Aparición</label>
                        <input class="form-input" type="number" name="order_val" id="order_val" value="<?php echo $edit_cat ? (int)$edit_cat['order_val'] : '0'; ?>">
                    </div>
                    <div class="form-group" style="display: flex; align-items: flex-end; padding-bottom: 10px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer;">
                            <input type="checkbox" name="is_active" <?php echo (!$edit_cat || $edit_cat['is_active']) ? 'checked' : ''; ?>> Categoría Activa
                        </label>
                    </div>
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 10px;">
                    <button class="btn btn-primary" type="submit">Guardar Categoría</button>
                    <?php if ($edit_cat): ?>
                        <a href="categorias.php" class="btn btn-secondary">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <h2 style="font-family: var(--font-heading); margin-bottom: 1.25rem; font-size: 1.4rem;">Categorías Registradas</h2>
        <table>
            <thead>
                <tr>
                    <th>Orden</th>
                    <th>Nombre</th>
                    <th>Slug</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $cat): ?>
                    <tr>
                        <td><?php echo (int)$cat['order_val']; ?></td>
                        <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                        <td><code><?php echo htmlspecialchars($cat['slug']); ?></code></td>
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
