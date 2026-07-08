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
        $stmt = $pdo->prepare("DELETE FROM materiales WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Material/Acabado eliminado correctamente.';
    } catch (PDOException $e) {
        $error = 'Error al eliminar: ' . $e->getMessage();
    }
}

// Procesar Formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($id > 0) {
        // Edición
        try {
            $stmt = $pdo->prepare("UPDATE materiales SET name = ?, description = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$name, $description, $is_active, $id]);
            $message = 'Material/Acabado actualizado correctamente.';
        } catch (PDOException $e) {
            $error = 'Error al actualizar: ' . $e->getMessage();
        }
    } else {
        // Creación
        try {
            $stmt = $pdo->prepare("INSERT INTO materiales (name, description, is_active) VALUES (?, ?, ?)");
            $stmt->execute([$name, $description, $is_active]);
            $message = 'Material/Acabado creado correctamente.';
        } catch (PDOException $e) {
            $error = 'Error al crear: ' . $e->getMessage();
        }
    }
}

// Cargar todos los materiales
$materiales = $pdo->query("SELECT * FROM materiales ORDER BY id DESC")->fetchAll();

// Cargar material a editar
$edit_material = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM materiales WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_material = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Materiales | CardNet.ec</title>
    <link rel="stylesheet" href="../css/base.css?v=3.5">
    <link rel="stylesheet" href="../css/layout.css?v=3.5">
    <link rel="stylesheet" href="../css/components.css?v=3.5">
    <style>
        body {
            font-family: 'Work Sans', sans-serif;
            background-color: var(--surface-light);
            margin: 0;
            display: flex;
        }
        .sidebar {
            width: 260px;
            background-color: #121212;
            color: white;
            min-height: 100vh;
            padding: 2rem 1.5rem;
            box-sizing: border-box;
            position: fixed;
            left: 0;
            top: 0;
        }
        .sidebar-logo {
            width: 140px;
            margin-bottom: 2.5rem;
        }
        .nav-admin {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .nav-admin-link {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        .nav-admin-link:hover, .nav-admin-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        .main-content {
            margin-left: 260px;
            flex-grow: 1;
            padding: 3rem;
            box-sizing: border-box;
            min-height: 100vh;
        }
        .form-container {
            background-color: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--dark);
        }
        .form-input {
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s ease;
        }
        .form-input:focus {
            border-color: var(--primary);
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover);
        }
        .btn-secondary {
            background-color: #E5E7EB;
            color: #374151;
        }
        .btn-secondary:hover {
            background-color: #D1D5DB;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
        <img src="../images/logo.png?v=3.5" alt="CardNet Logo" class="sidebar-logo">
        <nav class="nav-admin">
            <a href="index.php" class="nav-admin-link">Dashboard</a>
            <a href="categorias.php" class="nav-admin-link">Categorías</a>
            <a href="productos.php" class="nav-admin-link">Productos</a>
            <a href="materiales.php" class="nav-admin-link active">Materiales</a>
            <a href="carrusel.php" class="nav-admin-link">Carrusel Hero</a>
            <a href="antes-despues.php" class="nav-admin-link">Antes y Después</a>
            <a href="clientes.php" class="nav-admin-link">Logos Clientes</a>
            <a href="credenciales.php" class="nav-admin-link">Credenciales QR</a>
            <a href="configuracion.php" class="nav-admin-link">Configuración</a>
            <a href="logout.php" class="nav-admin-link" style="margin-top: 2rem; color: #FCA5A5;">Cerrar Sesión</a>
        </nav>
    </div>

    <div class="main-content">
        <h1 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 2rem;">Gestión de Materiales y Acabados</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h2 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 1.25rem;">
                <?php echo $edit_material ? 'Editar Material / Acabado' : 'Añadir Nuevo Material / Acabado'; ?>
            </h2>

            <form method="POST" action="materiales.php">
                <?php if ($edit_material): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_material['id']; ?>">
                <?php endif; ?>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="name">Nombre del Material/Acabado *</label>
                        <input class="form-input" type="text" name="name" id="name" required placeholder="Ej: Acero inoxidable satinado" value="<?php echo $edit_material ? htmlspecialchars($edit_material['name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="description">Descripción Corta</label>
                        <input class="form-input" type="text" name="description" id="description" placeholder="Ej: Grabado láser limpio de color blanco mate" value="<?php echo $edit_material ? htmlspecialchars($edit_material['description']) : ''; ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem; display: flex; flex-direction: row; align-items: center; gap: 8px;">
                    <input type="checkbox" name="is_active" id="is_active" <?php echo (!$edit_material || $edit_material['is_active']) ? 'checked' : ''; ?>>
                    <label style="font-size: 0.88rem; cursor: pointer;" for="is_active">Material Activo / Disponible</label>
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 10px;">
                    <button class="btn btn-primary" type="submit">
                        <?php echo $edit_material ? 'Actualizar Material' : 'Añadir Material'; ?>
                    </button>
                    <?php if ($edit_material): ?>
                        <a href="materiales.php" class="btn btn-secondary">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div style="background-color: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden;">
            <table>
                <thead>
                    <tr>
                        <th style="text-align: left;">ID</th>
                        <th style="text-align: left;">Nombre</th>
                        <th style="text-align: left;">Descripción</th>
                        <th style="text-align: left;">Estado</th>
                        <th style="text-align: center; width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($materiales)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted);">No hay materiales registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($materiales as $mat): ?>
                            <tr>
                                <td><?php echo $mat['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($mat['name']); ?></strong></td>
                                <td style="color: var(--text-muted);"><?php echo htmlspecialchars($mat['description']); ?></td>
                                <td>
                                    <?php if ($mat['is_active']): ?>
                                        <span style="color: #03543F; background-color: #DEF7EC; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem; font-weight: 600;">Disponible</span>
                                    <?php else: ?>
                                        <span style="color: #9B1C1C; background-color: #FDE8E8; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem; font-weight: 600;">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <a href="materiales.php?edit=<?php echo $mat['id']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.75rem; font-weight: 600;">Editar</a>
                                    <a href="materiales.php?delete=<?php echo $mat['id']; ?>" class="btn btn-secondary" onclick="return confirm('¿Estás seguro de eliminar este material?');" style="padding: 5px 10px; font-size: 0.75rem; font-weight: 600; color: #EF4444;">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
