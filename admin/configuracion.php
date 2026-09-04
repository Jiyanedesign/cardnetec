<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Persistencia y respaldo por Cookie para servidores con sesiones estrictas
if (!isset($_SESSION['admin_logged']) && isset($_COOKIE['cardnet_admin_logged']) && $_COOKIE['cardnet_admin_logged'] === 'cardnet_auth_2026_ok') {
    $_SESSION['admin_logged'] = true;
    $_SESSION['admin_name'] = 'CardNet Admin';
}
if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit;
}
require_once '../db.php';

$message = '';
$error = '';

// Procesar Formulario de Guardado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $whatsapp = trim($_POST['whatsapp']);
    $phone_2 = trim($_POST['phone_2'] ?? '');
    $phone_3 = trim($_POST['phone_3'] ?? '');
    $email = trim($_POST['email']);
    $email_2 = trim($_POST['email_2'] ?? '');
    $address = trim($_POST['address']);
    $instagram = trim($_POST['instagram']);
    $facebook = trim($_POST['facebook']);
    $site_title = trim($_POST['site_title']);
    $site_description = trim($_POST['site_description']);
    $min_order = (int)$_POST['min_order'];
    $obras_subtitle = trim($_POST['obras_subtitle'] ?? 'Obras del Taller');
    $obras_title = trim($_POST['obras_title'] ?? 'Piezas seleccionadas para personalizar');
    $obras_desc = trim($_POST['obras_desc'] ?? 'Artículos de alta resistencia diseñados para acoger tu marca con grabado láser de máxima definición.');
    $accesorios_subtitle = trim($_POST['accesorios_subtitle'] ?? 'Accesorios Diarios');
    $accesorios_title = trim($_POST['accesorios_title'] ?? 'Accesorios para el uso diario');
    $accesorios_desc = trim($_POST['accesorios_desc'] ?? 'Complementos prácticos para proteger, portar y presentar mejor cada credencial.');

    try {
        $count = $pdo->query("SELECT COUNT(*) FROM configuraciones WHERE id = 1")->fetchColumn();
        if ($count > 0) {
            $stmt = $pdo->prepare("UPDATE configuraciones SET whatsapp = ?, phone_2 = ?, phone_3 = ?, email = ?, email_2 = ?, address = ?, instagram = ?, facebook = ?, site_title = ?, site_description = ?, min_order = ?, obras_subtitle = ?, obras_title = ?, obras_desc = ?, accesorios_subtitle = ?, accesorios_title = ?, accesorios_desc = ? WHERE id = 1");
            $stmt->execute([$whatsapp, $phone_2, $phone_3, $email, $email_2, $address, $instagram, $facebook, $site_title, $site_description, $min_order, $obras_subtitle, $obras_title, $obras_desc, $accesorios_subtitle, $accesorios_title, $accesorios_desc]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO configuraciones (id, whatsapp, phone_2, phone_3, email, email_2, address, instagram, facebook, site_title, site_description, min_order, obras_subtitle, obras_title, obras_desc, accesorios_subtitle, accesorios_title, accesorios_desc) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$whatsapp, $phone_2, $phone_3, $email, $email_2, $address, $instagram, $facebook, $site_title, $site_description, $min_order, $obras_subtitle, $obras_title, $obras_desc, $accesorios_subtitle, $accesorios_title, $accesorios_desc]);
        }
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
    <link rel="stylesheet" href="../css/admin.css?v=2.0">
</head>
<body>

    <div class="sidebar">
        <img src="../images/logo.png?v=2.0" alt="CardNet Logo" class="sidebar-logo">
        <nav class="nav-admin">
            <a href="index.php" class="nav-admin-link">Dashboard</a>
            <a href="carnets-empresas.php" class="nav-admin-link">Carnets y Empresas</a>
            <a href="categorias.php" class="nav-admin-link">Categorías</a>
            <a href="secciones.php" class="nav-admin-link">Secciones Home</a>
            <a href="tagua.php" class="nav-admin-link">Tagua</a>
            <a href="etiquetas.php" class="nav-admin-link">Etiquetas</a>
            <a href="productos.php" class="nav-admin-link">Productos</a>
            <a href="materiales.php" class="nav-admin-link">Materiales</a>
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
                <h2 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 1.25rem;">Teléfonos de Contacto (3 Líneas)</h2>
                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label" for="whatsapp">Teléfono 1 / WhatsApp Principal *</label>
                        <input class="form-input" type="text" name="whatsapp" id="whatsapp" required placeholder="Ej: 593900000000 o 0991234567" value="<?php echo htmlspecialchars($settings['whatsapp'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone_2">Teléfono 2 (Secundario / Fijo)</label>
                        <input class="form-input" type="text" name="phone_2" id="phone_2" placeholder="Ej: (02) 234-5678" value="<?php echo htmlspecialchars($settings['phone_2'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone_3">Teléfono 3 (Adicional / Celular)</label>
                        <input class="form-input" type="text" name="phone_3" id="phone_3" placeholder="Ej: 0987654321" value="<?php echo htmlspecialchars($settings['phone_3'] ?? ''); ?>">
                    </div>
                </div>

                <h2 style="font-family: var(--font-heading); margin-top: 2rem; margin-bottom: 1.5rem; font-size: 1.25rem;">Correos Electrónicos (2 Correos)</h2>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="email">Correo Principal (Ventas / Cotizaciones) *</label>
                        <input class="form-input" type="email" name="email" id="email" required placeholder="ventas@cardnet.ec" value="<?php echo htmlspecialchars($settings['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email_2">Correo Secundario (Información / Soporte)</label>
                        <input class="form-input" type="email" name="email_2" id="email_2" placeholder="info@cardnet.ec" value="<?php echo htmlspecialchars($settings['email_2'] ?? ''); ?>">
                    </div>
                </div>

                <h2 style="font-family: var(--font-heading); margin-top: 2rem; margin-bottom: 1.5rem; font-size: 1.25rem;">Ubicación y Pedido Mínimo</h2>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="min_order">Pedido Mínimo Global (0 = Sin mínimo)</label>
                        <input class="form-input" type="number" name="min_order" id="min_order" min="0" value="<?php echo htmlspecialchars($settings['min_order'] ?? '1'); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="address">Dirección Física del Taller *</label>
                        <input class="form-input" type="text" name="address" id="address" required value="<?php echo htmlspecialchars($settings['address'] ?? ''); ?>">
                    </div>
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

                <h2 style="font-family: var(--font-heading); margin-top: 2rem; margin-bottom: 1.5rem; font-size: 1.25rem;">Portada: Textos de Obras del Taller</h2>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="obras_subtitle">Subtítulo de Sección (Badge Verde)</label>
                        <input class="form-input" type="text" name="obras_subtitle" id="obras_subtitle" value="<?php echo htmlspecialchars($settings['obras_subtitle'] ?? 'Obras del Taller'); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="obras_title">Título Principal</label>
                        <input class="form-input" type="text" name="obras_title" id="obras_title" value="<?php echo htmlspecialchars($settings['obras_title'] ?? 'Piezas seleccionadas para personalizar'); ?>">
                    </div>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="obras_desc">Descripción Inferior</label>
                    <textarea class="form-input" name="obras_desc" id="obras_desc" rows="2"><?php echo htmlspecialchars($settings['obras_desc'] ?? 'Artículos de alta resistencia diseñados para acoger tu marca con grabado láser de máxima definición.'); ?></textarea>
                </div>

                <h2 style="font-family: var(--font-heading); margin-top: 2rem; margin-bottom: 1.5rem; font-size: 1.25rem;">Portada: Textos de Accesorios Diarios</h2>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="accesorios_subtitle">Subtítulo de Sección (Badge Verde)</label>
                        <input class="form-input" type="text" name="accesorios_subtitle" id="accesorios_subtitle" value="<?php echo htmlspecialchars($settings['accesorios_subtitle'] ?? 'Accesorios Diarios'); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="accesorios_title">Título Principal</label>
                        <input class="form-input" type="text" name="accesorios_title" id="accesorios_title" value="<?php echo htmlspecialchars($settings['accesorios_title'] ?? 'Accesorios para el uso diario'); ?>">
                    </div>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label" for="accesorios_desc">Descripción Inferior</label>
                    <textarea class="form-input" name="accesorios_desc" id="accesorios_desc" rows="2"><?php echo htmlspecialchars($settings['accesorios_desc'] ?? 'Complementos prácticos para proteger, portar y presentar mejor cada credencial.'); ?></textarea>
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
