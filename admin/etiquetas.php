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
        $stmt = $pdo->prepare("DELETE FROM etiquetas WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Etiqueta eliminada correctamente.';
    } catch (PDOException $e) {
        $error = 'Error al eliminar: ' . $e->getMessage();
    }
}

// Procesar Formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name']);
    $color = trim($_POST['color']) ?: 'rgba(0,0,0,0.03)';
    $text_color = trim($_POST['text_color']) ?: 'var(--text-muted)';

    if ($id > 0) {
        // Edición
        try {
            $stmt = $pdo->prepare("UPDATE etiquetas SET name = ?, color = ?, text_color = ? WHERE id = ?");
            $stmt->execute([$name, $color, $text_color, $id]);
            $message = 'Etiqueta actualizada correctamente.';
        } catch (PDOException $e) {
            $error = 'Error al actualizar: ' . $e->getMessage();
        }
    } else {
        // Creación
        try {
            $stmt = $pdo->prepare("INSERT INTO etiquetas (name, color, text_color) VALUES (?, ?, ?)");
            $stmt->execute([$name, $color, $text_color]);
            $message = 'Etiqueta creada correctamente.';
        } catch (PDOException $e) {
            $error = 'Error al crear: ' . $e->getMessage();
        }
    }
}

// Cargar etiquetas
$etiquetas = $pdo->query("SELECT * FROM etiquetas ORDER BY id ASC")->fetchAll();

// Cargar etiqueta a editar
$edit_tag = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM etiquetas WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_tag = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Etiquetas | CardNet.ec</title>
    <link rel="stylesheet" href="../css/base.css?v=4.7">
    <link rel="stylesheet" href="../css/layout.css?v=4.7">
    <link rel="stylesheet" href="../css/components.css?v=4.7">
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
        .tag-badge-preview {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
            border: 1px solid rgba(0,0,0,0.05);
            text-transform: none;
        }
    </style>
    <link rel="stylesheet" href="../css/admin.css?v=4.7">
</head>
<body>

    <div class="sidebar">
        <img src="../images/logo.png?v=2.0" alt="CardNet Logo" class="sidebar-logo">
        <nav class="nav-admin">
            <a href="index.php" class="nav-admin-link">Dashboard</a>
            <a href="categorias.php" class="nav-admin-link">Categorías</a>
            <a href="etiquetas.php" class="nav-admin-link active">Etiquetas</a>
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
        <h1 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 2rem;">Gestión de Etiquetas</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h2 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 1.25rem;">
                <?php echo $edit_tag ? 'Editar Etiqueta' : 'Añadir Nueva Etiqueta'; ?>
            </h2>

            <form method="POST" action="etiquetas.php">
                <?php if ($edit_tag): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_tag['id']; ?>">
                <?php endif; ?>

                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label" for="name">Nombre de la Etiqueta</label>
                        <input class="form-input" type="text" name="name" id="name" required value="<?php echo $edit_tag ? htmlspecialchars($edit_tag['name']) : ''; ?>" placeholder="Ej. Premium">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="color">Color de Fondo (RGBA, Hex o nombre)</label>
                        <input class="form-input" type="text" name="color" id="color" value="<?php echo $edit_tag ? htmlspecialchars($edit_tag['color']) : 'rgba(99, 174, 44, 0.08)'; ?>" placeholder="Ej. rgba(99, 174, 44, 0.08)">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="text_color">Color del Texto (RGBA, Hex o clase)</label>
                        <input class="form-input" type="text" name="text_color" id="text_color" value="<?php echo $edit_tag ? htmlspecialchars($edit_tag['text_color']) : 'var(--primary-hover)'; ?>" placeholder="Ej. var(--primary-hover) o #ffffff">
                    </div>
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 10px;">
                    <button class="btn btn-primary" type="submit">Guardar Etiqueta</button>
                    <?php if ($edit_tag): ?>
                        <a href="etiquetas.php" class="btn btn-secondary">Cancelar Edición</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <h2 style="font-family: var(--font-heading); margin-bottom: 1.25rem; font-size: 1.4rem;">Etiquetas Registradas</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Color de Fondo</th>
                    <th>Color de Texto</th>
                    <th>Vista Previa</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($etiquetas)): ?>
                    <?php foreach ($etiquetas as $tag): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($tag['name']); ?></strong></td>
                            <td><code><?php echo htmlspecialchars($tag['color']); ?></code></td>
                            <td><code><?php echo htmlspecialchars($tag['text_color']); ?></code></td>
                            <td>
                                <span class="tag-badge-preview" style="background-color: <?php echo htmlspecialchars($tag['color']); ?>; color: <?php echo htmlspecialchars($tag['text_color']); ?>;">
                                    <?php echo htmlspecialchars($tag['name']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="etiquetas.php?edit=<?php echo $tag['id']; ?>" style="color: var(--primary); text-decoration: none; font-weight: bold; margin-right: 10px;">Editar</a>
                                <a href="etiquetas.php?delete=<?php echo $tag['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar esta etiqueta?')" style="color: #EF4444; text-decoration: none; font-weight: bold;">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted);">No hay etiquetas creadas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
