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
        // Obtener ruta del logo antes de borrar de la base de datos
        $stmt = $pdo->prepare("SELECT logo_path FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $file_path = '../' . $row['logo_path'];
            if (file_exists($file_path) && strpos($row['logo_path'], 'default') === false) {
                @unlink($file_path);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Cliente eliminado correctamente.';
    } catch (PDOException $e) {
        $error = 'Error al eliminar: ' . $e->getMessage();
    }
}

// Procesar Formulario (Creación o Edición)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name']);
    $order_val = (int)$_POST['order_val'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $logo_path = '';

    // Manejar subida de archivo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['logo']['tmp_name'];
        $fileName = $_FILES['logo']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $newFileName = 'logo_' . time() . '_' . rand(1000, 9999) . '.' . $fileExtension;
        $dest_path = '../uploads/' . $newFileName;
        
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $logo_path = 'uploads/' . $newFileName;
        } else {
            $error = 'Error al mover el archivo subido.';
        }
    }

    if (empty($error)) {
        if ($id > 0) {
            // Edición
            try {
                if ($logo_path !== '') {
                    // Borrar el archivo anterior
                    $stmt = $pdo->prepare("SELECT logo_path FROM clientes WHERE id = ?");
                    $stmt->execute([$id]);
                    $old_row = $stmt->fetch();
                    if ($old_row) {
                        $old_file = '../' . $old_row['logo_path'];
                        if (file_exists($old_file) && strpos($old_row['logo_path'], 'default') === false) {
                            @unlink($old_file);
                        }
                    }

                    $stmt = $pdo->prepare("UPDATE clientes SET name = ?, logo_path = ?, order_val = ?, is_active = ? WHERE id = ?");
                    $stmt->execute([$name, $logo_path, $order_val, $is_active, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE clientes SET name = ?, order_val = ?, is_active = ? WHERE id = ?");
                    $stmt->execute([$name, $order_val, $is_active, $id]);
                }
                $message = 'Cliente actualizado correctamente.';
            } catch (PDOException $e) {
                $error = 'Error al actualizar: ' . $e->getMessage();
            }
        } else {
            // Creación
            if ($logo_path === '') {
                $error = 'El archivo de logo es obligatorio para nuevos clientes.';
            } else {
                try {
                    $stmt = $pdo->prepare("INSERT INTO clientes (name, logo_path, order_val, is_active) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$name, $logo_path, $order_val, $is_active]);
                    $message = 'Cliente creado correctamente.';
                } catch (PDOException $e) {
                    $error = 'Error al crear: ' . $e->getMessage();
                }
            }
        }
    }
}

// Cargar clientes
$clientes = $pdo->query("SELECT * FROM clientes ORDER BY order_val ASC")->fetchAll();

// Cargar cliente a editar
$edit_client = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_client = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Logos de Clientes | CardNet.ec</title>
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
            <a href="carrusel.php" class="nav-admin-link">Carrusel Hero</a>
            <a href="antes-despues.php" class="nav-admin-link">Antes y Después</a>
            <a href="clientes.php" class="nav-admin-link active">Logos Clientes</a>
            <a href="credenciales.php" class="nav-admin-link">Credenciales QR</a>
            <a href="configuracion.php" class="nav-admin-link">Configuración</a>
            <a href="logout.php" class="nav-admin-link" style="margin-top: 2rem; color: #FCA5A5;">Cerrar Sesión</a>
        </nav>
    </div>

    <div class="main-content">
        <h1 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 2rem;">Gestión de Logos de Clientes</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h2 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 1.25rem;">
                <?php echo $edit_client ? 'Editar Cliente' : 'Añadir Nuevo Cliente'; ?>
            </h2>

            <form method="POST" action="clientes.php" enctype="multipart/form-data">
                <?php if ($edit_client): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_client['id']; ?>">
                <?php endif; ?>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="name">Nombre del Cliente</label>
                        <input class="form-input" type="text" name="name" id="name" required value="<?php echo $edit_client ? htmlspecialchars($edit_client['name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="order_val">Orden de visualización</label>
                        <input class="form-input" type="number" name="order_val" id="order_val" required value="<?php echo $edit_client ? htmlspecialchars($edit_client['order_val']) : '0'; ?>">
                    </div>
                </div>

                <div class="grid-2" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="logo">Archivo de Logo (.png transparente, idealmente monocromo/gris)</label>
                        <input class="form-input" type="file" name="logo" id="logo" <?php echo $edit_client ? '' : 'required'; ?>>
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 2rem;">
                        <input type="checkbox" name="is_active" id="is_active" value="1" <?php echo (!$edit_client || $edit_client['is_active']) ? 'checked' : ''; ?>>
                        <label for="is_active">Activo (Se muestra en la web)</label>
                    </div>
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">Guardar Cliente</button>
                    <?php if ($edit_client): ?>
                        <a href="clientes.php" class="btn btn-secondary">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <h2 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 1.5rem;">Clientes actuales</h2>
        <table>
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Nombre</th>
                    <th>Orden</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $c): ?>
                    <tr>
                        <td>
                            <img src="../<?php echo htmlspecialchars($c['logo_path']); ?>" alt="Logo" style="max-height: 40px; background-color: #f4f4f4; padding: 5px; border-radius: 4px;">
                        </td>
                        <td><?php echo htmlspecialchars($c['name']); ?></td>
                        <td><?php echo htmlspecialchars($c['order_val']); ?></td>
                        <td>
                            <span style="background-color: <?php echo $c['is_active'] ? '#DEF7EC; color: #03543F' : '#FDE8E8; color: #9B1C1C'; ?>; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem; font-weight: bold;">
                                <?php echo $c['is_active'] ? 'Activo' : 'Inactivo'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="clientes.php?edit=<?php echo $c['id']; ?>" class="btn btn-secondary" style="font-size: 0.75rem; padding: 4px 8px; text-decoration: none;">Editar</a>
                            <a href="clientes.php?delete=<?php echo $c['id']; ?>" class="btn btn-secondary" style="font-size: 0.75rem; padding: 4px 8px; text-decoration: none; color: #EF4444;" onclick="return confirm('¿Está seguro de eliminar este cliente?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($clientes)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted);">No hay clientes registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
