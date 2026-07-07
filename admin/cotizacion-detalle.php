<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';

// Procesar Actualización de Estado / Notas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = trim($_POST['status']);
    $internal_notes = trim($_POST['internal_notes']);

    try {
        $stmt = $pdo->prepare("UPDATE solicitudes SET status = ?, internal_notes = ? WHERE id = ?");
        $stmt->execute([$status, $internal_notes, $id]);
        $message = 'Ficha de cotización actualizada correctamente.';
    } catch (PDOException $e) {
        $error = 'Error al actualizar: ' . $e->getMessage();
    }
}

// Cargar Ficha de Cotización
$quote = null;
if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM solicitudes WHERE id = ?");
        $stmt->execute([$id]);
        $quote = $stmt->fetch();
    } catch (PDOException $e) {
        $quote = null;
    }
}

if (!$quote) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Cotización #<?php echo $quote['id']; ?> | CardNet.ec</title>
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
        .flex-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        @media (max-width: 900px) {
            .flex-container {
                grid-template-columns: 1fr;
            }
        }
        .card {
            background-color: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
        }
        .quote-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 10px 0;
            border-bottom: 1px solid var(--surface-light);
            font-size: 0.9rem;
        }
        .meta-label {
            font-weight: 600;
            color: var(--text-muted);
            width: 35%;
        }
        .image-preview-box {
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 10px;
            text-align: center;
            background-color: var(--surface-light);
            margin-top: 1rem;
        }
        .image-preview-box img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 4px;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-new { background-color: #DBEAFE; color: #1E40AF; }
        .badge-process { background-color: #FEF3C7; color: #92400E; }
        .badge-completed { background-color: #D1FAE5; color: #065F46; }
        .badge-archived { background-color: #F3F4F6; color: #374151; }
    </style>
</head>
<body>

    <div class="sidebar">
        <img src="../images/logo.png?v=2.0" alt="CardNet Logo" class="sidebar-logo">
        <nav class="nav-admin">
            <a href="index.php" class="nav-admin-link active">Dashboard</a>
            <a href="categorias.php" class="nav-admin-link">Categorías</a>
            <a href="productos.php" class="nav-admin-link">Productos</a>
            <a href="carrusel.php" class="nav-admin-link">Carrusel Hero</a>
            <a href="antes-despues.php" class="nav-admin-link">Antes y Después</a>
            <a href="configuracion.php" class="nav-admin-link">Configuración</a>
            <a href="logout.php" class="nav-admin-link" style="margin-top: 2rem; color: #FCA5A5;">Cerrar Sesión</a>
        </nav>
    </div>

    <div class="main-content">
        
        <div class="quote-header">
            <div>
                <a href="index.php" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.9rem;">← Volver al Dashboard</a>
                <h1 style="font-family: var(--font-heading); margin: 10px 0 0 0; font-size: 2rem;">Ficha de Cotización #<?php echo $quote['id']; ?></h1>
            </div>
            <div>
                <?php
                $status_class = 'badge-new';
                if ($quote['status'] === 'En Proceso') $status_class = 'badge-process';
                if ($quote['status'] === 'Respondido') $status_class = 'badge-completed';
                if ($quote['status'] === 'Archivado') $status_class = 'badge-archived';
                ?>
                <span class="badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($quote['status'] ?: 'Nuevo'); ?></span>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="flex-container">
            
            <!-- Columna Izquierda: Información del Cliente -->
            <div class="card">
                <h2 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 1.3rem;">Información de la Solicitud</h2>
                
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Cliente</td>
                        <td><strong><?php echo htmlspecialchars($quote['name']); ?></strong></td>
                    </tr>
                    <tr>
                        <td class="meta-label">Empresa</td>
                        <td><?php echo htmlspecialchars($quote['company'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="meta-label">WhatsApp</td>
                        <td>
                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $quote['whatsapp']); ?>" target="_blank" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                                <?php echo htmlspecialchars($quote['whatsapp']); ?> ➔ Chatear
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label">Correo</td>
                        <td><?php echo htmlspecialchars($quote['email'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="meta-label">Producto</td>
                        <td><strong><?php echo htmlspecialchars($quote['product_name'] ?: 'N/A'); ?></strong></td>
                    </tr>
                    <tr>
                        <td class="meta-label">Cantidad</td>
                        <td><?php echo htmlspecialchars($quote['qty']); ?> unidades</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Fecha de Envío</td>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($quote['created_at'])); ?></td>
                    </tr>
                </table>

                <h3 style="margin-top: 2rem; font-size: 1rem; color: var(--text-muted); margin-bottom: 0.5rem;">Mensaje / Instrucciones:</h3>
                <p style="background-color: var(--surface-light); padding: 15px; border-radius: 4px; font-size: 0.9rem; line-height: 1.5; white-space: pre-line;">
                    <?php echo htmlspecialchars($quote['message'] ?: 'Sin mensaje adicional.'); ?>
                </p>
            </div>

            <!-- Columna Derecha: Control de Gestión y Archivos -->
            <div class="card">
                <h2 style="font-family: var(--font-heading); margin-bottom: 1.5rem; font-size: 1.3rem;">Gestión Comercial</h2>

                <form method="POST" action="cotizacion-detalle.php?id=<?php echo $quote['id']; ?>">
                    <div class="form-group">
                        <label class="form-label" for="status">Estado del Pedido</label>
                        <select class="sim-select" name="status" id="status" style="width: 100%; border: 1px solid var(--border); padding: 10px; border-radius: 4px;">
                            <option value="Nuevo" <?php echo ($quote['status'] === 'Nuevo') ? 'selected' : ''; ?>>Nuevo</option>
                            <option value="En Proceso" <?php echo ($quote['status'] === 'En Proceso') ? 'selected' : ''; ?>>En Proceso</option>
                            <option value="Respondido" <?php echo ($quote['status'] === 'Respondido') ? 'selected' : ''; ?>>Respondido / Cotizado</option>
                            <option value="Archivado" <?php echo ($quote['status'] === 'Archivado') ? 'selected' : ''; ?>>Archivado</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label class="form-label" for="internal_notes">Notas Internas (Bitácora de seguimiento)</label>
                        <textarea class="form-input" name="internal_notes" id="internal_notes" rows="4" placeholder="Escribe notas del seguimiento aquí (descuentos, estado de mockups, llamadas...)" style="width: 100%; border: 1px solid var(--border); border-radius: 4px; padding: 10px; box-sizing: border-box;"><?php echo htmlspecialchars($quote['internal_notes']); ?></textarea>
                    </div>

                    <button class="btn btn-primary" type="submit" style="margin-top: 1.5rem; width: 100%;">Guardar Cambios de Gestión</button>
                </form>

                <h3 style="margin-top: 2.5rem; font-size: 1.1rem; font-family: var(--font-heading); margin-bottom: 1rem;">Archivos Adjuntos</h3>
                
                <?php if ($quote['logo_path']): ?>
                    <div class="image-preview-box">
                        <span style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 10px;">Logotipo Original Subido</span>
                        <a href="../uploads/<?php echo htmlspecialchars($quote['logo_path']); ?>" target="_blank">
                            <img src="../uploads/<?php echo htmlspecialchars($quote['logo_path']); ?>">
                        </a>
                        <br>
                        <a href="../uploads/<?php echo htmlspecialchars($quote['logo_path']); ?>" download class="btn btn-secondary" style="margin-top: 10px; display: inline-block; padding: 5px 15px; font-size: 0.8rem;">Descargar Logo original</a>
                    </div>
                <?php endif; ?>

                <?php if ($quote['simulation_path']): ?>
                    <div class="image-preview-box" style="margin-top: 2rem;">
                        <span style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 10px;">Maqueta Digital de Canvas</span>
                        <a href="../uploads/<?php echo htmlspecialchars($quote['simulation_path']); ?>" target="_blank">
                            <img src="../uploads/<?php echo htmlspecialchars($quote['simulation_path']); ?>">
                        </a>
                        <br>
                        <a href="../uploads/<?php echo htmlspecialchars($quote['simulation_path']); ?>" download class="btn btn-secondary" style="margin-top: 10px; display: inline-block; padding: 5px 15px; font-size: 0.8rem;">Descargar Maqueta Canvas</a>
                    </div>
                <?php endif; ?>

                <?php if (!$quote['logo_path'] && !$quote['simulation_path']): ?>
                    <p style="color: var(--text-muted); font-size: 0.9rem; text-align: center; padding: 2rem 0;">El cliente no adjuntó ningún archivo gráfico.</p>
                <?php endif; ?>
            </div>

        </div>

    </div>

</body>
</html>
