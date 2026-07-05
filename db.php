<?php
// Configuración de la Base de Datos para CardNet.ec
// Conexión oficial de producción para Hostinger

$db_host = 'localhost';
$db_name = 'u434851126_cardnetec';
$db_user = 'u434851126_cardnetec_usr';
$db_pass = 'Cardnetec2026!';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // AUTO-MIGRACIÓN: Auto-actualizar base de datos si falta la tabla de categorías
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'categorias'")->fetch();
    if (!$tableCheck) {
        // Crear tabla de categorías
        $pdo->exec("CREATE TABLE IF NOT EXISTS `categorias` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(100) NOT NULL,
          `slug` varchar(100) NOT NULL UNIQUE,
          `order_val` int(11) DEFAULT 0,
          `is_active` tinyint(1) DEFAULT 1,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Insertar categorías iniciales
        $pdo->exec("INSERT INTO `categorias` (`name`, `slug`, `order_val`, `is_active`) VALUES
            ('Artículos personalizados', 'articulos-personalizados', 1, 1),
            ('Identificación corporativa', 'identificacion-corporativa', 2, 1),
            ('Reconocimientos', 'reconocimientos', 3, 1),
            ('Kits corporativos', 'kits-corporativos', 4, 1);");

        // Agregar columna category_id a productos
        $pdo->exec("ALTER TABLE `productos` ADD COLUMN `category_id` int(11) DEFAULT NULL;");

        // Mapear los productos existentes a las nuevas categorías por nombre
        $cats = $pdo->query("SELECT * FROM categorias")->fetchAll();
        foreach ($cats as $cat) {
            $update = $pdo->prepare("UPDATE productos SET category_id = ? WHERE category = ?");
            $update->execute([$cat['id'], $cat['name']]);
        }
    }
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
