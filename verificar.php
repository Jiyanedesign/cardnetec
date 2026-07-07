<?php
require_once 'db.php';

$cedula = isset($_GET['id']) ? trim($_GET['id']) : '';
$employee = null;
$error = '';

if (!empty($cedula)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM credenciales WHERE cedula = ?");
        $stmt->execute([$cedula]);
        $employee = $stmt->fetch();
    } catch (PDOException $e) {
        $error = 'Error de conexión con el sistema de seguridad.';
    }
} else {
    $error = 'No se proporcionó un ID de credencial válido.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Credencial | CardNet.ec</title>
    <link rel="stylesheet" href="css/base.css?v=1.1.2">
    <link rel="stylesheet" href="css/layout.css?v=1.1.2">
    <link rel="stylesheet" href="css/components.css?v=1.1.2">
    <style>
        body {
            font-family: 'Work Sans', sans-serif;
            background-color: var(--surface-light);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1.5rem;
            box-sizing: border-box;
        }
        .verification-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
            box-shadow: var(--shadow-lg);
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 2rem;
        }
        .status-verified {
            background-color: #DEF7EC;
            color: #03543F;
            border: 1px solid #BCF0DA;
        }
        .status-unverified {
            background-color: #FDE8E8;
            color: #9B1C1C;
            border: 1px solid #F8B4B4;
        }
        .employee-photo {
            width: 110px;
            height: 130px;
            object-fit: cover;
            border-radius: var(--radius-sm);
            border: 2px solid var(--border);
            margin: 0 auto 1.5rem auto;
            background-color: var(--surface-light);
            display: block;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            text-align: left;
        }
        .info-table td {
            padding: 10px 0;
            border-bottom: 1px solid var(--surface-light);
            font-size: 0.9rem;
        }
        .info-label {
            color: var(--text-muted);
            font-weight: 600;
            width: 35%;
        }
    </style>
</head>
<body>

    <div class="verification-card">
        
        <!-- Logotipo CardNet -->
        <img src="images/logo.png" alt="CardNet.ec Logo" style="max-width: 130px; margin-bottom: 2rem; display: block; margin-left: auto; margin-right: auto;">

        <?php if ($employee): ?>
            
            <?php if ($employee['estado'] === 'Activo'): ?>
                <div class="status-badge status-verified">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Credencial Activa
                </div>
            <?php else: ?>
                <div class="status-badge status-unverified">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Credencial Inactiva / Suspendida
                </div>
            <?php endif; ?>

            <!-- Foto -->
            <?php 
            $photo_src = 'images/avatar_default.png'; // Fallback
            if (!empty($employee['foto_path'])) {
                if ($employee['foto_path'] === 'default_avatar.png') {
                    // Podemos usar una imagen por defecto
                    $photo_src = 'images/avatar_default.png';
                } else {
                    $photo_src = 'uploads/' . htmlspecialchars($employee['foto_path']);
                }
            }
            ?>
            
            <!-- Si no existe la foto default, mostramos un marcador de posición de silueta -->
            <div style="position: relative;">
                <img src="<?php echo $photo_src; ?>" class="employee-photo" onerror="this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png';">
            </div>

            <h2 style="font-family: var(--font-heading); margin: 0 0 6px 0; font-size: 1.5rem; color: var(--text-main);"><?php echo htmlspecialchars($employee['nombre']); ?></h2>
            <p style="font-size: 0.88rem; color: var(--primary); font-weight: 600; margin: 0; text-transform: uppercase;"><?php echo htmlspecialchars($employee['puesto']); ?></p>

            <table class="info-table">
                <tr>
                    <td class="info-label">Cédula / ID</td>
                    <td><?php echo htmlspecialchars($employee['cedula']); ?></td>
                </tr>
                <tr>
                    <td class="info-label">Empresa</td>
                    <td><strong><?php echo htmlspecialchars($employee['empresa']); ?></strong></td>
                </tr>
                <tr>
                    <td class="info-label">Emisión</td>
                    <td><?php echo date('d/m/Y', strtotime($employee['created_at'])); ?></td>
                </tr>
            </table>

        <?php else: ?>
            
            <div class="status-badge status-unverified">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                Código no registrado
            </div>

            <h3 style="font-family: var(--font-heading); font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--text-main);">Error de Autenticidad</h3>
            <p style="color: var(--text-muted); font-size: 0.88rem; line-height: 1.5;">Esta credencial no coincide con los registros validados en nuestra base de datos de CardNet.ec.</p>
            
            <?php if (!empty($error)): ?>
                <p style="font-size:0.8rem; color:#EF4444; margin-top:1rem; font-style:italic;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

        <?php endif; ?>

        <div style="margin-top: 2.5rem; border-top: 1px solid var(--border); padding-top: 1rem;">
            <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0;">Sistema de Validación en Línea de CardNet.ec</p>
        </div>

    </div>

</body>
</html>
