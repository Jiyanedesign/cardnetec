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

// Procesar Eliminación
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    try {
        $stmtDel = $pdo->prepare("DELETE FROM solicitudes WHERE id = ?");
        $stmtDel->execute([$del_id]);
        $message = "La cotización #$del_id fue eliminada correctamente.";
    } catch (PDOException $e) {
        $error = "Error al eliminar la cotización: " . $e->getMessage();
    }
}

// Procesar Cambio Rápido de Estado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $quote_id = (int)$_POST['quote_id'];
    $new_status = trim($_POST['status']);
    try {
        $stmtUp = $pdo->prepare("UPDATE solicitudes SET status = ? WHERE id = ?");
        $stmtUp->execute([$new_status, $quote_id]);
        $message = "Estado de la cotización #$quote_id actualizado a '$new_status'.";
    } catch (PDOException $e) {
        $error = "Error al actualizar estado: " . $e->getMessage();
    }
}

// Filtros y Búsqueda
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

// Construir consulta dinámica
$query = "SELECT * FROM solicitudes WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR company LIKE ? OR email LIKE ? OR whatsapp LIKE ? OR product_name LIKE ? OR message LIKE ?)";
    $term = "%$search%";
    $params = array_merge($params, [$term, $term, $term, $term, $term, $term]);
}

if (!empty($status_filter)) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
}

$query .= " ORDER BY id DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $quotes = $stmt->fetchAll();

    // Métricas para tarjetas
    $total_count = $pdo->query("SELECT COUNT(*) FROM solicitudes")->fetchColumn();
    $new_count = $pdo->query("SELECT COUNT(*) FROM solicitudes WHERE status = 'Nuevo' OR status IS NULL OR status = ''")->fetchColumn();
    $process_count = $pdo->query("SELECT COUNT(*) FROM solicitudes WHERE status = 'En Proceso'")->fetchColumn();
    $completed_count = $pdo->query("SELECT COUNT(*) FROM solicitudes WHERE status IN ('Respondido', 'Aprobado', 'Finalizado')")->fetchColumn();
} catch (PDOException $e) {
    die("Error al consultar cotizaciones: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Favicon Oficial -->
    <link rel="icon" type="image/png" href="../favicon.png?v=4.0">
    <link rel="shortcut icon" href="../favicon.ico?v=4.0">
    <link rel="apple-touch-icon" href="../favicon.png?v=4.0">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cotizaciones | CardNet.ec</title>
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
            flex-shrink: 0;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-admin-link:hover, .nav-admin-link.active {
            color: white;
            background-color: var(--primary);
        }
        .main-content {
            flex-grow: 1;
            padding: 3rem;
            box-sizing: border-box;
            overflow-x: auto;
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border);
            padding-bottom: 1rem;
            flex-wrap: wrap;
            gap: 15px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2.5rem;
        }
        .stat-card {
            background-color: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1.25rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 6px;
            box-shadow: var(--shadow-sm);
        }
        .stat-value {
            font-size: 2rem;
            font-family: var(--font-heading);
            color: var(--primary);
            font-weight: bold;
        }
        .filter-bar {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-sm);
        }
        .table-container {
            background-color: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            overflow-x: auto;
            box-shadow: var(--shadow-sm);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.88rem;
        }
        th, td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }
        th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #475569;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        tr:hover td {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 0.72rem;
            font-weight: 700;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-new { background-color: #DBEAFE; color: #1E40AF; }
        .badge-process { background-color: #FEF3C7; color: #92400E; }
        .badge-completed { background-color: #D1FAE5; color: #065F46; }
        .badge-archived { background-color: #F1F5F9; color: #475569; }
        .badge-counter {
            background: rgba(255,255,255,0.25);
            padding: 2px 7px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: bold;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
        }
    </style>
    <link rel="stylesheet" href="../css/admin.css?v=2.0">
</head>
<body>

    <!-- Sidebar de Navegación Oficial -->
    <div class="sidebar">
        <img src="../images/logo.png?v=4.0" alt="CardNet Logo" class="sidebar-logo">
        <nav class="nav-admin">
            <a href="index.php" class="nav-admin-link">Dashboard</a>
            <a href="cotizaciones.php" class="nav-admin-link active">
                <span>📋 Cotizaciones</span>
                <?php if ($new_count > 0): ?>
                    <span class="badge-counter"><?php echo $new_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="carnets-empresas.php" class="nav-admin-link">Credenciales y Empresas</a>
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
            <a href="configuracion.php" class="nav-admin-link">Configuración</a>
            <a href="logout.php" class="nav-admin-link" style="margin-top: 2rem; color: #FCA5A5;">Cerrar Sesión</a>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">
        <div class="dashboard-header">
            <div>
                <h1 style="font-family: var(--font-heading); margin: 0; font-size: 2rem;">Solicitudes de Cotización</h1>
                <p style="color: var(--text-muted); margin: 5px 0 0 0;">Gestión y seguimiento de presupuestos recibidos a través del sitio web.</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="../cotizacion.php" target="_blank" class="btn btn-secondary" style="font-size: 0.85rem; padding: 10px 16px;">
                    Abrir Formulario de Cotización
                </a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Tarjetas de Métricas -->
        <div class="stats-grid">
            <div class="stat-card">
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Total Recibidas</span>
                <span class="stat-value"><?php echo $total_count; ?></span>
            </div>
            <div class="stat-card">
                <span style="font-size: 0.85rem; color: #1E40AF; font-weight: 600;">Nuevas / Pendientes</span>
                <span class="stat-value" style="color: #2563EB;"><?php echo $new_count; ?></span>
            </div>
            <div class="stat-card">
                <span style="font-size: 0.85rem; color: #92400E; font-weight: 600;">En Proceso</span>
                <span class="stat-value" style="color: #D97706;"><?php echo $process_count; ?></span>
            </div>
            <div class="stat-card">
                <span style="font-size: 0.85rem; color: #065F46; font-weight: 600;">Respondidas / Listas</span>
                <span class="stat-value" style="color: #059669;"><?php echo $completed_count; ?></span>
            </div>
        </div>

        <!-- Barra de Filtros y Búsqueda -->
        <form method="GET" action="cotizaciones.php" class="filter-bar">
            <div style="display: flex; gap: 12px; flex-wrap: wrap; flex-grow: 1; align-items: center;">
                <input type="text" name="q" placeholder="Buscar por cliente, empresa, teléfono o producto..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 10px 14px; border: 1px solid var(--border); border-radius: 6px; font-size: 0.88rem; width: 340px; max-width: 100%;">
                
                <select name="status" style="padding: 10px 14px; border: 1px solid var(--border); border-radius: 6px; font-size: 0.88rem; background: white;">
                    <option value="">Todos los Estados</option>
                    <option value="Nuevo" <?php echo ($status_filter === 'Nuevo') ? 'selected' : ''; ?>>Nuevo</option>
                    <option value="En Proceso" <?php echo ($status_filter === 'En Proceso') ? 'selected' : ''; ?>>En Proceso</option>
                    <option value="Respondido" <?php echo ($status_filter === 'Respondido') ? 'selected' : ''; ?>>Respondido</option>
                    <option value="Archivado" <?php echo ($status_filter === 'Archivado') ? 'selected' : ''; ?>>Archivado</option>
                </select>

                <button type="submit" class="btn btn-primary" style="padding: 10px 18px; font-size: 0.85rem;">Filtrar</button>

                <?php if (!empty($search) || !empty($status_filter)): ?>
                    <a href="cotizaciones.php" style="color: #EF4444; font-size: 0.85rem; text-decoration: none; font-weight: 600;">Limpiar Filtros</a>
                <?php endif; ?>
            </div>

            <div style="color: var(--text-muted); font-size: 0.85rem;">
                Mostrando <strong><?php echo count($quotes); ?></strong> solicitud(es)
            </div>
        </form>

        <!-- Tabla Completa de Cotizaciones -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th style="width: 130px;">Fecha</th>
                        <th>Cliente / Empresa</th>
                        <th>Contacto</th>
                        <th>Detalle de Productos</th>
                        <th style="text-align: center;">Cantidad</th>
                        <th>Archivos</th>
                        <th>Estado</th>
                        <th style="text-align: right; width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($quotes)): ?>
                        <?php foreach ($quotes as $q): ?>
                            <?php
                            $status_badge = 'badge-new';
                            if ($q['status'] === 'En Proceso') $status_badge = 'badge-process';
                            if ($q['status'] === 'Respondido') $status_badge = 'badge-completed';
                            if ($q['status'] === 'Archivado') $status_badge = 'badge-archived';
                            ?>
                            <tr>
                                <td><strong>#<?php echo $q['id']; ?></strong></td>
                                <td style="color: var(--text-muted); font-size: 0.82rem;">
                                    <?php echo date('d/m/Y', strtotime($q['created_at'])); ?><br>
                                    <span style="font-size: 0.75rem;"><?php echo date('h:i A', strtotime($q['created_at'])); ?></span>
                                </td>
                                <td>
                                    <strong style="color: var(--dark); font-size: 0.95rem;"><?php echo htmlspecialchars($q['name']); ?></strong>
                                    <?php if (!empty($q['company'])): ?>
                                        <div style="font-size: 0.8rem; color: #475569; font-weight: 500;">🏢 <?php echo htmlspecialchars($q['company']); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($q['email'])): ?>
                                        <div style="font-size: 0.78rem; margin-top: 2px;">
                                            <a href="mailto:<?php echo htmlspecialchars($q['email']); ?>" style="color: var(--primary); text-decoration: none;">
                                                ✉️ <?php echo htmlspecialchars($q['email']); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($q['whatsapp'])): ?>
                                        <a href="<?php echo formatWhatsAppUrl($q['whatsapp'], 'Hola ' . $q['name'] . ', te contactamos de Cardnetec en relación a tu cotización #' . $q['id']); ?>" target="_blank" style="display: inline-flex; align-items: center; gap: 4px; color: #16A34A; text-decoration: none; font-weight: 700; font-size: 0.88rem;" title="Abrir chat en WhatsApp">
                                            💬 <?php echo htmlspecialchars($q['whatsapp']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-size: 0.8rem;">No registrado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="max-width: 280px; font-size: 0.85rem; line-height: 1.4;">
                                        <strong><?php echo htmlspecialchars($q['product_name'] ?: 'Solicitud general'); ?></strong>
                                        <?php if (!empty($q['message'])): ?>
                                            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 4px; font-style: italic;">
                                                "<?php echo htmlspecialchars(mb_strimwidth($q['message'], 0, 70, '...')); ?>"
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td style="text-align: center; font-weight: 700; color: var(--dark);">
                                    <?php echo htmlspecialchars($q['qty'] ?: '1'); ?> uds
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 4px; font-size: 0.78rem;">
                                        <?php if (!empty($q['logo_path'])): ?>
                                            <a href="../uploads/<?php echo htmlspecialchars($q['logo_path']); ?>" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: 600;">
                                                📥 Logotipo
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($q['simulation_path'])): ?>
                                            <a href="../uploads/<?php echo htmlspecialchars($q['simulation_path']); ?>" target="_blank" style="color: #2563EB; text-decoration: none; font-weight: 600;">
                                                🎨 Render 3D
                                            </a>
                                        <?php endif; ?>
                                        <?php if (empty($q['logo_path']) && empty($q['simulation_path'])): ?>
                                            <span style="color: var(--text-muted);">Sin archivos</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <form method="POST" action="cotizaciones.php" style="display: flex; flex-direction: column; gap: 5px;">
                                        <input type="hidden" name="update_status" value="1">
                                        <input type="hidden" name="quote_id" value="<?php echo $q['id']; ?>">
                                        <span class="badge <?php echo $status_badge; ?>" style="align-self: flex-start;">
                                            <?php echo htmlspecialchars($q['status'] ?: 'Nuevo'); ?>
                                        </span>
                                        <select name="status" onchange="this.form.submit()" style="font-size: 0.75rem; padding: 3px 6px; border: 1px solid var(--border); border-radius: 4px; background: white; cursor: pointer;">
                                            <option value="Nuevo" <?php echo ($q['status'] === 'Nuevo' || empty($q['status'])) ? 'selected' : ''; ?>>Nuevo</option>
                                            <option value="En Proceso" <?php echo ($q['status'] === 'En Proceso') ? 'selected' : ''; ?>>En Proceso</option>
                                            <option value="Respondido" <?php echo ($q['status'] === 'Respondido') ? 'selected' : ''; ?>>Respondido</option>
                                            <option value="Archivado" <?php echo ($q['status'] === 'Archivado') ? 'selected' : ''; ?>>Archivado</option>
                                        </select>
                                    </form>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                        <a href="cotizacion-detalle.php?id=<?php echo $q['id']; ?>" class="btn-sm btn-primary" title="Ver detalles y gestionar notas">
                                            Ver Ficha
                                        </a>
                                        <a href="cotizaciones.php?delete=<?php echo $q['id']; ?>" class="btn-sm" onclick="return confirm('¿Confirmas que deseas eliminar permanentemente la cotización #<?php echo $q['id']; ?> de <?php echo addslashes($q['name']); ?>?')" style="background-color: #FEE2E2; color: #DC2626;" title="Eliminar cotización">
                                            🗑️
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📭</div>
                                <div style="font-size: 1rem; font-weight: 600; color: var(--dark);">No se encontraron solicitudes de cotización</div>
                                <p style="font-size: 0.85rem; margin: 4px 0 0 0;">Las cotizaciones enviadas desde la tienda aparecerán listadas automáticamente aquí.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
