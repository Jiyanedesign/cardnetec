<?php
session_start();
require_once '../db.php';

// Verificar autenticación
if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit;
}

// 1. Obtener estadísticas
try {
    $total_products = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
    $total_slides = $pdo->query("SELECT COUNT(*) FROM carrusel")->fetchColumn();
    $total_quotes = $pdo->query("SELECT COUNT(*) FROM solicitudes")->fetchColumn();
    
    // Solicitudes recientes (últimas 10)
    $stmt = $pdo->query("SELECT * FROM solicitudes ORDER BY id DESC LIMIT 10");
    $recent_quotes = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error al consultar datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | CardNet.ec</title>
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
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            border-bottom: 1px solid var(--border);
            padding-bottom: 1rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .stat-card {
            background-color: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .stat-value {
            font-size: 2.2rem;
            font-family: var(--font-heading);
            color: var(--primary);
            font-weight: bold;
        }
        .table-container {
            background-color: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        th, td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.88rem;
        }
        th {
            background-color: var(--surface-light);
            font-weight: 600;
            color: var(--text-muted);
        }
        .badge {
            padding: 4px 8px;
            font-size: 0.72rem;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-success { background-color: #DEF7EC; color: #03543F; }
    </style>
</head>
<body>

    <!-- Sidebar de Navegación -->
    <div class="sidebar">
        <img src="../images/logo.png?v=2.0" alt="CardNet Logo" class="sidebar-logo">
        <nav class="nav-admin">
            <a href="index.php" class="nav-admin-link active">Dashboard</a>
            <a href="categorias.php" class="nav-admin-link">Categorías</a>
            <a href="productos.php" class="nav-admin-link">Productos</a>
            <a href="carrusel.php" class="nav-admin-link">Carrusel Hero</a>
            <a href="antes-despues.php" class="nav-admin-link">Antes y Después</a>
            <a href="credenciales.php" class="nav-admin-link">Credenciales QR</a>
            <a href="configuracion.php" class="nav-admin-link">Configuración</a>
            <a href="logout.php" class="nav-admin-link" style="margin-top: 2rem; color: #FCA5A5;">Cerrar Sesión</a>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">
        <div class="dashboard-header">
            <div>
                <h1 style="font-family: var(--font-heading); margin: 0; font-size: 2rem;">Panel de Control</h1>
                <p style="color: var(--text-muted); margin: 5px 0 0 0;">Bienvenido, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></p>
            </div>
            <a href="../index.php" target="_blank" class="btn btn-secondary">Ver Sitio Público</a>
        </div>

        <!-- Métrica General -->
        <div class="stats-grid">
            <div class="stat-card">
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Productos en Showroom</span>
                <span class="stat-value"><?php echo $total_products; ?></span>
            </div>
            <div class="stat-card">
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Slides Activos</span>
                <span class="stat-value"><?php echo $total_slides; ?></span>
            </div>
            <div class="stat-card">
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Solicitudes de Cotización</span>
                <span class="stat-value"><?php echo $total_quotes; ?></span>
            </div>
        </div>

        <!-- Listado de Solicitudes Recientes -->
        <h2 style="font-family: var(--font-heading); margin-bottom: 1.25rem; font-size: 1.4rem;">Solicitudes de Cotización Recientes</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>WhatsApp</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Archivos</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_quotes)): ?>
                        <?php foreach ($recent_quotes as $quote): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($quote['created_at'])); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($quote['name']); ?></strong>
                                    <?php if ($quote['company']): ?>
                                        <br><span style="font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars($quote['company']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $quote['whatsapp']); ?>" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: 600;">
                                        <?php echo htmlspecialchars($quote['whatsapp']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($quote['product_name'] ?: 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($quote['qty'] ?: 'N/A'); ?></td>
                                <td>
                                    <?php if ($quote['logo_path']): ?>
                                        <a href="../uploads/<?php echo htmlspecialchars($quote['logo_path']); ?>" target="_blank" style="font-size: 0.8rem; color: var(--primary); display: block;">Logo original</a>
                                    <?php endif; ?>
                                    <?php if ($quote['simulation_path']): ?>
                                        <a href="../uploads/<?php echo htmlspecialchars($quote['simulation_path']); ?>" target="_blank" style="font-size: 0.8rem; color: var(--primary); display: block;">Captura Canvas</a>
                                    <?php endif; ?>
                                    <?php if (!$quote['logo_path'] && !$quote['simulation_path']): ?>
                                        <span style="color: var(--text-muted); font-size: 0.8rem;">Ninguno</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-size: 0.75rem; font-weight: bold; border-radius: 4px; padding: 2px 6px; background-color: #E0E7FF; color: #3730A3;">
                                        <?php echo htmlspecialchars($quote['status'] ?: 'Nuevo'); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="cotizacion-detalle.php?id=<?php echo $quote['id']; ?>" style="color: var(--primary); text-decoration: none; font-weight: bold;">Ver Ficha</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted);">No se han recibido solicitudes todavía.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
