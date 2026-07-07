<?php
session_start();
require_once '../db.php';

$error = '';

if (isset($_SESSION['admin_logged'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios_admin WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_logged'] = true;
                $_SESSION['admin_name'] = $user['name'];
                header("Location: index.php");
                exit;
            } else {
                $error = 'Credenciales incorrectas.';
            }
        } catch (PDOException $e) {
            $error = 'Error de conexión: ' . $e->getMessage();
        }
    } else {
        $error = 'Por favor, rellene todos los campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | CardNet.ec Admin</title>
    <link rel="stylesheet" href="../css/base.css?v=1.1.2">
    <link rel="stylesheet" href="../css/components.css?v=1.1.2">
    <style>
        body {
            background-color: var(--surface-light);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Work Sans', sans-serif;
        }
        .login-card {
            background-color: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: var(--shadow-md);
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-logo {
            max-width: 140px;
            margin-bottom: 1rem;
        }
        .error-banner {
            background-color: #FEE2E2;
            border: 1px solid #FCA5A5;
            color: #991B1B;
            padding: 0.75rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <img src="../images/logo.png?v=2.0" alt="CardNet Logo" class="login-logo">
            <h2 style="font-family: var(--font-heading); font-size: 1.5rem; color: var(--dark);">Acceso de Administrador</h2>
        </div>

        <?php if ($error): ?>
            <div class="error-banner"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label" for="email">Correo Electrónico</label>
                <input class="form-input" type="email" name="email" id="email" required placeholder="ejemplo@cardnet.ec">
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label" for="password">Contraseña</label>
                <input class="form-input" type="password" name="password" id="password" required placeholder="••••••••">
            </div>

            <button class="btn btn-primary" type="submit" style="width: 100%; padding: 0.75rem;">Iniciar Sesión</button>
        </form>
    </div>

</body>
</html>
