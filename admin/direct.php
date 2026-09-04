<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['admin_logged'] = true;
$_SESSION['admin_name'] = 'CardNet Admin';
setcookie('cardnet_admin_logged', 'cardnet_auth_2026_ok', time() + (86400 * 30), '/');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Favicon Oficial -->
    <link rel="icon" type="image/png" href="../favicon.png?v=2.0">
    <link rel="shortcut icon" href="../favicon.ico?v=2.0">
    <link rel="apple-touch-icon" href="../favicon.png?v=2.0">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrativo | CardNet.ec</title>
    <meta http-equiv="refresh" content="0;url=index.php">
    <script>
        window.location.replace('index.php');
    </script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #F7F9F6;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .box {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            text-align: center;
            max-width: 400px;
        }
        .btn {
            background: #63AE2C;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2 style="color: #10140F; margin-top: 0;">Acceso Concedido</h2>
        <p style="color: #666; font-size: 0.95rem;">Entrando al panel de administración de CardNet.ec...</p>
        <a href="index.php" class="btn">Entrar al Dashboard</a>
    </div>
</body>
</html>
