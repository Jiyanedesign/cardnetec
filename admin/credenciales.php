<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit;
}

$message = '';
$error = '';
$upload_dir = '../uploads/';

// Crear carpeta si no existe
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// 1. Procesar POST: AGREGAR / EDITAR CREDENCIAL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_credential'])) {
    $cedula = trim($_POST['cedula']);
    $nombre = trim($_POST['nombre']);
    $puesto = trim($_POST['puesto']);
    $empresa = trim($_POST['empresa']);
    $estado = trim($_POST['estado']);
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    $foto_filename = isset($_POST['old_foto']) ? $_POST['old_foto'] : 'default_avatar.png';

    // Subida de Foto del empleado
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $new_filename = 'emp_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($file_tmp, $upload_dir . $new_filename)) {
                $foto_filename = $new_filename;
            }
        } else {
            $error = 'Formato de imagen de foto no soportado.';
        }
    }

    if (empty($error)) {
        if ($id > 0) {
            // EDITAR
            try {
                $stmt = $pdo->prepare("UPDATE credenciales SET cedula = ?, nombre = ?, puesto = ?, empresa = ?, estado = ?, foto_path = ? WHERE id = ?");
                $stmt->execute([$cedula, $nombre, $puesto, $empresa, $estado, $foto_filename, $id]);
                $message = 'Credencial actualizada con éxito.';
            } catch (PDOException $e) {
                $error = 'Error al actualizar credencial (posible cédula duplicada).';
            }
        } else {
            // INSERTAR
            try {
                $stmt = $pdo->prepare("INSERT INTO credenciales (cedula, nombre, puesto, empresa, estado, foto_path) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$cedula, $nombre, $puesto, $empresa, $estado, $foto_filename]);
                $message = 'Credencial registrada con éxito.';
            } catch (PDOException $e) {
                $error = 'Error al registrar credencial (posible cédula duplicada).';
            }
        }
    }
}

// 2. Procesar GET: ELIMINAR CREDENCIAL
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM credenciales WHERE id = ?");
        $stmt->execute([$del_id]);
        $message = 'Credencial eliminada correctamente.';
    } catch (PDOException $e) {
        $error = 'Error al eliminar la credencial.';
    }
}

// 3. Procesar GET: CARGAR PARA EDITAR
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM credenciales WHERE id = ?");
        $stmt->execute([$edit_id]);
        $edit_data = $stmt->fetch();
    } catch (PDOException $e) {}
}

// 4. Obtener listado de credenciales registradas
try {
    $stmt = $pdo->query("SELECT * FROM credenciales ORDER BY created_at DESC");
    $credentials = $stmt->fetchAll();
} catch (PDOException $e) {
    $credentials = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Credenciales QR | CardNet.ec</title>
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
        .grid-split {
            display: grid;
            grid-template-columns: 0.8fr 1.2fr;
            gap: 2rem;
            align-items: start;
        }
        @media (max-width: 1024px) {
            .grid-split {
                grid-template-columns: 1fr;
            }
        }
        .card {
            background-color: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 2rem;
            box-shadow: var(--shadow-sm);
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
            <a href="antes-despues.php" class="nav-admin-link">Antes y Después</a>
            <a href="credenciales.php" class="nav-admin-link active">Credenciales QR</a>
            <a href="configuracion.php" class="nav-admin-link">Configuración</a>
            <a href="logout.php" class="nav-admin-link" style="margin-top: 2rem; color: #FCA5A5;">Cerrar Sesión</a>
        </nav>
    </div>

    <div class="main-content">
        <h1 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 2rem;">Gestión de Credenciales Validadas (QR)</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="grid-split">
            
            <!-- Columna Izquierda: Formulario -->
            <div class="card">
                <h2 style="font-family: var(--font-heading); font-size: 1.25rem; margin-bottom: 1.5rem;">
                    <?php echo $edit_data ? 'Editar Registro' : 'Registrar Credencial de Seguridad'; ?>
                </h2>

                <form method="POST" action="credenciales.php" enctype="multipart/form-data">
                    <input type="hidden" name="save_credential" value="1">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                        <input type="hidden" name="old_foto" value="<?php echo $edit_data['foto_path']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label" for="cedula">Cédula de Identidad / RUC *</label>
                        <input class="form-input" type="text" name="cedula" id="cedula" required placeholder="Ej. 1725489630" value="<?php echo $edit_data ? htmlspecialchars($edit_data['cedula']) : ''; ?>">
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label class="form-label" for="nombre">Nombre Completo del Personal *</label>
                        <input class="form-input" type="text" name="nombre" id="nombre" required placeholder="Ej. Alejandro Silva" value="<?php echo $edit_data ? htmlspecialchars($edit_data['nombre']) : ''; ?>">
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label class="form-label" for="puesto">Cargo / Puesto *</label>
                        <input class="form-input" type="text" name="puesto" id="puesto" required placeholder="Ej. Director de Operaciones" value="<?php echo $edit_data ? htmlspecialchars($edit_data['puesto']) : ''; ?>">
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label class="form-label" for="empresa">Empresa Cliente *</label>
                        <input class="form-input" type="text" name="empresa" id="empresa" required placeholder="Ej. CardNet Corporativo" value="<?php echo $edit_data ? htmlspecialchars($edit_data['empresa']) : ''; ?>">
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label class="form-label" for="estado">Estado de la Credencial</label>
                        <select class="sim-select" name="estado" id="estado" style="width:100%; border: 1px solid var(--border); padding:10px; border-radius:4px; background:white;">
                            <option value="Activo" <?php echo ($edit_data && $edit_data['estado'] === 'Activo') ? 'selected' : ''; ?>>✓ Activo (Habilitado)</option>
                            <option value="Inactivo" <?php echo ($edit_data && $edit_data['estado'] === 'Inactivo') ? 'selected' : ''; ?>>✗ Inactivo (Acceso Denegado)</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label class="form-label" for="foto">Foto de Perfil (Proporción 3x4 recomendada)</label>
                        <input class="form-input" type="file" name="foto" id="foto" accept="image/*" style="border: 1px solid var(--border); padding: 8px; width: 100%; box-sizing: border-box; background: white;">
                        <?php if ($edit_data && $edit_data['foto_path']): ?>
                            <span style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-top: 6px;">Foto actual: <?php echo htmlspecialchars($edit_data['foto_path']); ?></span>
                        <?php endif; ?>
                    </div>

                    <div style="margin-top: 1.5rem; display: flex; gap: 10px;">
                        <button class="btn btn-primary" type="submit" style="flex-grow: 1;">Guardar Registro</button>
                        <?php if ($edit_data): ?>
                            <a href="credenciales.php" class="btn btn-secondary" style="padding: 12px; text-decoration: none;">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Columna Derecha: Listado -->
            <div class="card">
                <h2 style="font-family: var(--font-heading); font-size: 1.25rem; margin-bottom: 1.5rem;">Registros de Seguridad Activos</h2>

                <div class="table-container">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--border); text-align: left;">
                                <th style="padding: 10px;">Foto</th>
                                <th style="padding: 10px;">Cédula</th>
                                <th style="padding: 10px;">Nombre</th>
                                <th style="padding: 10px;">Empresa</th>
                                <th style="padding: 10px;">Estado</th>
                                <th style="padding: 10px; text-align: right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($credentials)): ?>
                                <?php foreach ($credentials as $cred): ?>
                                    <tr style="border-bottom: 1px solid var(--surface-light);">
                                        <td style="padding: 10px;">
                                            <?php 
                                            $thumb = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
                                            if (!empty($cred['foto_path']) && $cred['foto_path'] !== 'default_avatar.png') {
                                                $thumb = '../uploads/' . $cred['foto_path'];
                                            }
                                            ?>
                                            <img src="<?php echo $thumb; ?>" style="width: 36px; height: 42px; object-fit: cover; border-radius: 2px; border: 1px solid var(--border);" onerror="this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png';">
                                        </td>
                                        <td style="padding: 10px; font-weight: 600;"><?php echo htmlspecialchars($cred['cedula']); ?></td>
                                        <td style="padding: 10px;">
                                            <strong><?php echo htmlspecialchars($cred['nombre']); ?></strong>
                                            <br><span style="font-size:0.75rem; color:var(--text-muted);"><?php echo htmlspecialchars($cred['puesto']); ?></span>
                                        </td>
                                        <td style="padding: 10px; font-size: 0.85rem;"><?php echo htmlspecialchars($cred['empresa']); ?></td>
                                        <td style="padding: 10px;">
                                            <span style="font-size: 0.75rem; font-weight: bold; border-radius: 4px; padding: 2px 6px; <?php echo ($cred['estado'] === 'Activo') ? 'background-color: #D1FAE5; color: #065F46;' : 'background-color: #FDE8E8; color: #9B1C1C;'; ?>">
                                                <?php echo htmlspecialchars($cred['estado']); ?>
                                            </span>
                                        </td>
                                        <td style="padding: 10px; text-align: right; font-size: 0.9rem;">
                                            <!-- Enlace directo para abrir la verificación QR en vivo -->
                                            <a href="../verificar.php?id=<?php echo htmlspecialchars($cred['cedula']); ?>" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: bold; margin-right: 12px;" title="Verificar URL del código QR escaneado">Escanear</a>
                                            <a href="credenciales.php?edit=<?php echo $cred['id']; ?>" style="color: #3B82F6; text-decoration: none; font-weight: bold; margin-right: 12px;">Editar</a>
                                            <a href="credenciales.php?delete=<?php echo $cred['id']; ?>" onclick="return confirm('¿Seguro de eliminar esta credencial del registro de seguridad?');" style="color: #EF4444; text-decoration: none; font-weight: bold;">Borrar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="padding: 2rem; text-align: center; color: var(--text-muted);">No hay credenciales registradas aún.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
