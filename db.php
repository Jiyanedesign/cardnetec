<?php
// Configuración de la Base de Datos para CardNet.ec
// Edita estos valores con los provistos por Hostinger en tu panel

$db_host = 'localhost';
$db_name = 'cardnet_db';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // Si falla la conexión, mostrar un error amigable o registrar logs
    // Para desarrollo local se puede ver el error, en producción se oculta
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
