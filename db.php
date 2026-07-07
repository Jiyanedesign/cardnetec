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

    // 1. AUTO-MIGRACIÓN: Crear tabla de categorías si falta
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'categorias'")->fetch();
    if (!$tableCheck) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `categorias` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(100) NOT NULL,
          `slug` varchar(100) NOT NULL UNIQUE,
          `order_val` int(11) DEFAULT 0,
          `is_active` tinyint(1) DEFAULT 1,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $pdo->exec("INSERT INTO `categorias` (`name`, `slug`, `order_val`, `is_active`) VALUES
            ('Artículos personalizados', 'articulos-personalizados', 1, 1),
            ('Identificación corporativa', 'identificacion-corporativa', 2, 1),
            ('Reconocimientos', 'reconocimientos', 3, 1),
            ('Kits corporativos', 'kits-corporativos', 4, 1);");

        $pdo->exec("ALTER TABLE `productos` ADD COLUMN `category_id` int(11) DEFAULT NULL;");
    }

    // 2. AUTO-MIGRACIÓN: Agregar campos de Galería de Imágenes, SKU, Stock, Precio y Descripción Larga si faltan
    $columns = $pdo->query("DESCRIBE productos")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('gallery_images', $columns)) {
        $pdo->exec("ALTER TABLE productos ADD COLUMN gallery_images text DEFAULT NULL;");
    }
    if (!in_array('sku', $columns)) {
        $pdo->exec("ALTER TABLE productos ADD COLUMN sku varchar(50) DEFAULT NULL;");
    }
    if (!in_array('stock', $columns)) {
        $pdo->exec("ALTER TABLE productos ADD COLUMN stock int(11) DEFAULT 700;");
    }
    if (!in_array('price', $columns)) {
        $pdo->exec("ALTER TABLE productos ADD COLUMN price decimal(10,2) DEFAULT 2.50;");
    }
    if (!in_array('description_long', $columns)) {
        $pdo->exec("ALTER TABLE productos ADD COLUMN description_long text DEFAULT NULL;");
    }

    // 3. AUTO-MIGRACIÓN: Tabla de Configuración General
    $settingsCheck = $pdo->query("SHOW TABLES LIKE 'configuraciones'")->fetch();
    if (!$settingsCheck) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `configuraciones` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `whatsapp` varchar(20) NOT NULL DEFAULT '593900000000',
          `email` varchar(100) NOT NULL DEFAULT 'info@cardnet.ec',
          `address` varchar(255) NOT NULL DEFAULT 'Av. Amazonas, Quito, Ecuador',
          `instagram` varchar(150) DEFAULT NULL,
          `facebook` varchar(150) DEFAULT NULL,
          `site_title` varchar(150) NOT NULL DEFAULT 'CardNet.ec | Personalización Láser',
          `site_description` varchar(255) NOT NULL DEFAULT 'Especialistas en grabado láser y personalización avanzada en Quito.',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $pdo->exec("INSERT INTO `configuraciones` (id, whatsapp, email, address, site_title, site_description) VALUES (1, '593900000000', 'info@cardnet.ec', 'Av. Amazonas, Quito, Ecuador', 'CardNet.ec | Personalización Láser', 'Especialistas en grabado láser y personalización avanzada en Quito.');");
    }

    // 4. AUTO-MIGRACIÓN: Agregar columna de estado y notas internas a la tabla solicitudes si faltan
    $sol_columns = $pdo->query("DESCRIBE solicitudes")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('status', $sol_columns)) {
        $pdo->exec("ALTER TABLE solicitudes ADD COLUMN status varchar(50) DEFAULT 'Nuevo';");
    }
    if (!in_array('internal_notes', $sol_columns)) {
        $pdo->exec("ALTER TABLE solicitudes ADD COLUMN internal_notes text DEFAULT NULL;");
    }
    if (!in_array('products_json', $sol_columns)) {
        $pdo->exec("ALTER TABLE solicitudes ADD COLUMN products_json text DEFAULT NULL;");
    }

    // 5. AUTO-MIGRACIÓN: Tabla de Credenciales para Verificación QR
    $credCheck = $pdo->query("SHOW TABLES LIKE 'credenciales'")->fetch();
    if (!$credCheck) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `credenciales` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `cedula` varchar(50) NOT NULL UNIQUE,
          `nombre` varchar(150) NOT NULL,
          `puesto` varchar(100) NOT NULL,
          `empresa` varchar(150) NOT NULL,
          `estado` varchar(50) NOT NULL DEFAULT 'Activo',
          `foto_path` varchar(255) DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Insertar un empleado de prueba para demostración inmediata
        $pdo->exec("INSERT IGNORE INTO `credenciales` (cedula, nombre, puesto, empresa, estado, foto_path) VALUES 
            ('1725489630', 'Alejandro Silva', 'Director Operativo', 'CardNet Corporativo', 'Activo', 'default_avatar.png');");
    }

} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Función auxiliar para obtener las configuraciones del sitio
function getSiteSettings($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM configuraciones WHERE id = 1");
        return $stmt->fetch() ?: [
            'whatsapp' => '593900000000',
            'email' => 'info@cardnet.ec',
            'address' => 'Av. Amazonas, Quito, Ecuador',
            'instagram' => '',
            'facebook' => '',
            'site_title' => 'CardNet.ec | Personalización Láser',
            'site_description' => 'Especialistas en grabado láser y personalización avanzada en Quito.'
        ];
    } catch (PDOException $e) {
        return [];
    }
}

// Función auxiliar para enriquecer productos con atributos comerciales para buscador, filtros y modales
function enrichProduct($prod) {
    $slug = $prod['slug'];
    $enriched = [
        'material' => 'Acero',
        'technique' => 'Grabado láser',
        'use' => 'Kits corporativos, eventos',
        'details' => 'Recomendado para regalos ejecutivos y promocionales de alta gama.'
    ];

    if (stripos($slug, 'termo') !== false) {
        $enriched['material'] = 'Acero';
        $enriched['technique'] = 'Grabado láser permanente';
        $enriched['use'] = 'Kits corporativos, eventos, uso diario';
        $enriched['details'] = 'Termo de doble pared de acero inoxidable que mantiene la temperatura. Ideal para personalización con grabado láser de alta definición.';
    } elseif (stripos($slug, 'agenda') !== false || stripos($slug, 'libreta') !== false) {
        $enriched['material'] = 'Cuero / PU';
        $enriched['technique'] = 'Grabado láser térmico / Bajo relieve';
        $enriched['use'] = 'Oficina, congresos, regalos ejecutivos';
        $enriched['details'] = 'Agenda ejecutiva con hojas de papel avena, marcapáginas y cubierta de tacto cuero (cuerina PU termosensible) ideal para grabado láser de bajo relieve.';
    } elseif (stripos($slug, 'placa') !== false) {
        $enriched['material'] = 'Acrílico';
        $enriched['technique'] = 'Corte y grabado láser de alta precisión';
        $enriched['use'] = 'Reconocimientos, premiaciones, señalética';
        $enriched['details'] = 'Placa conmemorativa de acrílico cristalino o madera noble. Acabados pulidos y grabado posterior de gran profundidad.';
    } elseif (stripos($slug, 'kit') !== false) {
        $enriched['material'] = 'Mixto (Acero/Madera/Cuero)';
        $enriched['technique'] = 'Grabado y serigrafía';
        $enriched['use'] = 'Regalos empresariales, bienvenida a colaboradores';
        $enriched['details'] = 'Kit completo premium en caja de madera o cartón rígido personalizado. Incluye termo grabado, agenda y bolígrafo.';
    } elseif (stripos($slug, 'llavero') !== false) {
        $enriched['material'] = 'Acero / Cuero';
        $enriched['technique'] = 'Grabado láser de fibra';
        $enriched['use'] = 'Regalos promocionales, merchandising';
        $enriched['details'] = 'Llavero robusto metálico con inserciones de cuero PU. Marcado indeleble de alta visibilidad para tu marca.';
    } elseif (stripos($slug, 'caja') !== false) {
        $enriched['material'] = 'Madera';
        $enriched['technique'] = 'Grabado láser CO2';
        $enriched['use'] = 'Empaque de regalo, botellas de vino, kits';
        $enriched['details'] = 'Caja de madera de pino o MDF con tapa deslizable. Grabado láser de gran contraste para presentaciones corporativas.';
    } elseif (stripos($slug, 'carnet') !== false || stripos($slug, 'credencial') !== false) {
        $enriched['material'] = 'Plástico PVC';
        $enriched['technique'] = 'Impresión térmica color';
        $enriched['use'] = 'Identificación institucional, seguridad, eventos';
        $enriched['details'] = 'Carnets impresos en PVC laminado de alta duración. Compatible con código de barras, QR y chips de proximidad.';
    }

    return array_merge($prod, $enriched);
}
