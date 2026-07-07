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
        $stmt = $pdo->prepare("DELETE FROM antes_despues WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Comparación eliminada correctamente.';
    } catch (PDOException $e) {
        $error = 'Error al eliminar: ' . $e->getMessage();
    }
}

// Procesar Formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = trim($_POST['title']);
    $technique = trim($_POST['technique']);
    $material = trim($_POST['material']);
    $order_val = (int)$_POST['order_val'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($id > 0) {
        // Edición
        try {
            $stmt = $pdo->prepare("UPDATE antes_despues SET title = ?, technique = ?, material = ?, order_val = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$title, $technique, $material, $order_val, $is_active, $id]);
            $message = 'Comparación actualizada correctamente.';
        } catch (PDOException $e) {
            $error = 'Error al actualizar base de datos: ' . $e->getMessage();
        }
    } else {
        // Creación
        try {
            $stmt = $pdo->prepare("INSERT INTO antes_despues (title, technique, material, order_val, is_active) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $technique, $material, $order_val, $is_active]);
            $message = 'Comparación creada correctamente.';
        } catch (PDOException $e) {
            $error = 'Error al crear comparación: ' . $e->getMessage();
        }
    }
}

// Cargar antes y después
$items = $pdo->query("SELECT * FROM antes_despues ORDER BY order_val ASC")->fetchAll();

// Cargar elemento a editar si aplica
$edit_item = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM antes_despues WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_item = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Antes y Después | CardNet.ec</title>
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
</head>
<body>

    <div class="sidebar">
        <img src="../images/logo.png?v=2.0" alt="CardNet Logo" class="sidebar-logo">
        <nav class="nav-admin">
            <a href="index.php" class="nav-admin-link">Dashboard</a>
            <a href="categorias.php" class="nav-admin-link">Categorías</a>
            <a href="productos.php" class="nav-admin-link">Productos</a>
            <a href="carrusel.php" class="nav-admin-link">Carrusel Hero</a>
            <a href="antes-despues.php" class="nav-admin-link active">Antes y Después</a>
            <a href="credenciales.php" class="nav-admin-link">Credenciales QR</a>
            <a href="configuracion.php" class="nav-admin-link">Configuración</a>
            <a href="logout.php" class="nav-admin-link" style="margin-top: 2rem; color: #FCA5A5;">Cerrar Sesión</a>
        </nav>
    </div>

    <div class="main-content">
        <h1 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 2rem;">Gestión de Antes y Después</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h2 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 1.25rem;">
                <?php echo $edit_item ? 'Editar Comparación' : 'Añadir Nueva Comparación'; ?>
            </h2>

            <form method="POST" action="antes-despues.php">
                <?php if ($edit_item): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label" for="title">Título (Ej: Termos de acero inoxidable)</label>
                    <input class="form-input" type="text" name="title" id="title" required value="<?php echo $edit_item ? htmlspecialchars($edit_item['title']) : ''; ?>">
                </div>

                <div class="grid-3" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="technique">Técnica (Ej: Grabado láser de fibra)</label>
                        <input class="form-input" type="text" name="technique" id="technique" required value="<?php echo $edit_item ? htmlspecialchars($edit_item['technique']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="material">Material (Ej: Acero inoxidable)</label>
                        <input class="form-input" type="text" name="material" id="material" required value="<?php echo $edit_item ? htmlspecialchars($edit_item['material']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="order_val">Orden</label>
                        <input class="form-input" type="number" name="order_val" id="order_val" value="<?php echo $edit_item ? (int)$edit_item['order_val'] : '0'; ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer;">
                        <input type="checkbox" name="is_active" <?php echo (!$edit_item || $edit_item['is_active']) ? 'checked' : ''; ?>> Comparación Activa
                    </label>
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 10px;">
                    <button class="btn btn-primary" type="submit">Guardar Comparación</button>
                    <?php if ($edit_item): ?>
                        <a href="antes-despues.php" class="btn btn-secondary">Cancelar Edición</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <h2 style="font-family: var(--font-heading); margin-bottom: 1.25rem; font-size: 1.4rem;">Comparaciones Registradas</h2>
        <table>
            <thead>
                <tr>
                    <th>Orden</th>
                    <th>Título</th>
                    <th>Técnica</th>
                    <th>Material</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $it): ?>
                    <tr>
                        <td><?php echo (int)$it['order_val']; ?></td>
                        <td><strong><?php echo htmlspecialchars($it['title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($it['technique']); ?></td>
                        <td><?php echo htmlspecialchars($it['material']); ?></td>
                        <td>
                            <span class="badge <?php echo $it['is_active'] ? 'badge-success' : 'badge-danger'; ?>" style="font-size: 0.7rem; border-radius: 4px; padding: 2px 6px;">
                                <?php echo $it['is_active'] ? 'Activo' : 'Inactivo'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="antes-despues.php?edit=<?php echo $it['id']; ?>" style="color: var(--primary); text-decoration: none; font-weight: bold; margin-right: 10px;">Editar</a>
                            <a href="antes-despues.php?delete=<?php echo $it['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar esta comparación?')" style="color: #EF4444; text-decoration: none; font-weight: bold;">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
