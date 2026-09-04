<?php
// Redirección permanente 301 a la página unificada Carnets y Empresas
$query = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("HTTP/1.1 301 Moved Permanently");
header("Location: empresas.php" . $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Favicon Oficial -->
    <link rel="icon" type="image/png" href="favicon.png?v=2.0">
    <link rel="shortcut icon" href="favicon.ico?v=2.0">
    <link rel="apple-touch-icon" href="favicon.png?v=2.0">

    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="0;url=empresas.php<?php echo htmlspecialchars($query); ?>">
    <title>Redirigiendo a Carnets y Empresas | CardNet.ec</title>
</head>
<body>
    <p>Redirigiendo a <a href="empresas.php<?php echo htmlspecialchars($query); ?>">Carnets y Empresas</a>...</p>
</body>
</html>
<?php
exit;
