<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit;
}

$message = '';
$error = '';

// Procesar Formulario de Guardado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $whatsapp = trim($_POST['whatsapp']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $instagram = trim($_POST['instagram']);
    $facebook = trim($_POST['facebook']);
    $site_title = trim($_POST['site_title']);
    $site_description = trim($_POST['site_description']);

    try {
        $stmt = $pdo->prepare("UPDATE configuraciones SET whatsapp = ?, email = ?, address = ?, instagram = ?, facebook = ?, site_title = ?, site_description = ? WHERE id = 1");
        $stmt->execute([$whatsapp, $email, $address, $instagram, $facebook, $site_title, $site_description]);
        $message = 'Configuración guardada correctamente.';
    } catch (PDOException $e) {
        $error = 'Error al guardar la configuración: ' . $e->getMessage();
    }
}

// Cargar configuraciones actuales
$settings = getSiteSettings($pdo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración General | CardNet.ec</title>
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
            <a href="clientes.php" class="nav-admin-link">Logos Clientes</a>
            <a href="credenciales.php" class="nav-admin-link">Credenciales QR</a>
            <a href="configuracion.php" class="nav-admin-link active">Configuración</a>
            <a href="logout.php" class="nav-admin-link" style="margin-top: 2rem; color: #FCA5A5;">Cerrar Sesión</a>
        </nav>
    </div>

    <div class="main-content">
        <h1 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 2rem;">Configuración General</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="configuracion.php">
                <h2 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 1.25rem;">Datos de Contacto Comercial</h2>
                
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="whatsapp">WhatsApp Comercial (ej: 593900000000) *</label>
                        <input class="form-input" type="text" name="whatsapp" id="whatsapp" required value="<?php echo htmlspecialchars($settings['whatsapp']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">Correo de Contacto *</label>
                        <input class="form-input" type="email" name="email" id="email" required value="<?php echo htmlspecialchars($settings['email']); ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="address">Dirección Física del Taller *</label>
                    <input class="form-input" type="text" name="address" id="address" required value="<?php echo htmlspecialchars($settings['address']); ?>">
                </div>

                <h2 style="font-family: var(--font-heading); margin-top: 2rem; margin-bottom: 1.5rem; font-size: 1.25rem;">Redes Sociales (Footer)</h2>
                
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="instagram">Instagram Link (URL completa)</label>
                        <input class="form-input" type="text" name="instagram" id="instagram" value="<?php echo htmlspecialchars($settings['instagram']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="facebook">Facebook Link (URL completa)</label>
                        <input class="form-input" type="text" name="facebook" id="facebook" value="<?php echo htmlspecialchars($settings['facebook']); ?>">
                    </div>
                </div>

                <h2 style="font-family: var(--font-heading); margin-top: 2rem; margin-bottom: 1.5rem; font-size: 1.25rem;">SEO y Meta Tags (Google)</h2>
                
                <div class="form-group">
                    <label class="form-label" for="site_title">Título del Sitio Web *</label>
                    <input class="form-input" type="text" name="site_title" id="site_title" required value="<?php echo htmlspecialchars($settings['site_title']); ?>">
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="site_description">Meta Descripción del Sitio Web *</label>
                    <textarea class="form-input" name="site_description" id="site_description" rows="3" required><?php echo htmlspecialchars($settings['site_description']); ?></textarea>
                </div>

                <div style="margin-top: 2rem;">
                    <button class="btn btn-primary" type="submit">Guardar Configuración</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
