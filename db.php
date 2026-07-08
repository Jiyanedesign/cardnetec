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
          `email` varchar(100) NOT NULL DEFAULT 'correo@cardnet.ec',
          `address` varchar(255) NOT NULL DEFAULT 'Av. Amazonas, Quito, Ecuador',
          `instagram` varchar(150) DEFAULT NULL,
          `facebook` varchar(150) DEFAULT NULL,
          `site_title` varchar(150) NOT NULL DEFAULT 'CardNet.ec | Personalización Láser',
          `site_description` varchar(255) NOT NULL DEFAULT 'Especialistas en grabado láser y personalización avanzada en Quito.',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $pdo->exec("INSERT INTO `configuraciones` (id, whatsapp, email, address, site_title, site_description) VALUES (1, '593900000000', 'correo@cardnet.ec', 'Av. Amazonas, Quito, Ecuador', 'CardNet.ec | Personalización Láser', 'Especialistas en grabado láser y personalización avanzada en Quito.');");
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

    // 6. AUTO-MIGRACIÓN: Tabla de Clientes
    $clientCheck = $pdo->query("SHOW TABLES LIKE 'clientes'")->fetch();
    if (!$clientCheck) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `clientes` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(150) NOT NULL,
          `logo_path` varchar(255) NOT NULL,
          `order_val` int(11) DEFAULT 0,
          `is_active` tinyint(1) DEFAULT 1,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $pdo->exec("INSERT IGNORE INTO `clientes` (id, name, logo_path, order_val, is_active) VALUES 
            (1, 'APEX', 'uploads/cliente1.png', 1, 1),
            (2, 'LUMINA', 'uploads/cliente2.png', 2, 1),
            (3, 'VORTEX', 'uploads/cliente3.png', 3, 1),
            (4, 'KRONA', 'uploads/cliente4.png', 4, 1),
            (5, 'AERO', 'uploads/cliente5.png', 5, 1);");
    }

    // Asegurar que los nombres de clientes coincidan con los logotipos generados
    $pdo->exec("UPDATE clientes SET name = 'APEX' WHERE id = 1 AND logo_path = 'uploads/cliente1.png';");
    $pdo->exec("UPDATE clientes SET name = 'LUMINA' WHERE id = 2 AND logo_path = 'uploads/cliente2.png';");
    $pdo->exec("UPDATE clientes SET name = 'VORTEX' WHERE id = 3 AND logo_path = 'uploads/cliente3.png';");
    $pdo->exec("UPDATE clientes SET name = 'KRONA' WHERE id = 4 AND logo_path = 'uploads/cliente4.png';");
    $pdo->exec("UPDATE clientes SET name = 'AERO' WHERE id = 5 AND logo_path = 'uploads/cliente5.png';");

    // 7. AUTO-MIGRACIÓN: Seeding de productos oficiales de CardNet
    $stmtCount = $pdo->query("SELECT COUNT(*) FROM productos");
    $count = $stmtCount->fetchColumn();
    if ($count == 0) {
        // Limpiar tablas para evitar duplicados o demos como Taza
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->exec("DELETE FROM productos;");
        $pdo->exec("DELETE FROM categorias;");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // Insertar Categorías oficiales
        $cats = [
            ['Termos', 'termos', 1],
            ['Agendas', 'agendas', 2],
            ['Kits', 'kits', 3],
            ['Placas', 'placas', 4],
            ['Carnets', 'carnets', 5],
            ['Cajas', 'cajas', 6],
            ['Llaveros', 'llaveros', 7],
            ['Esferos', 'esferos', 8],
            ['Reconocimientos', 'reconocimientos', 9]
        ];
        
        $insCat = $pdo->prepare("INSERT INTO categorias (name, slug, order_val, is_active) VALUES (?, ?, ?, 1)");
        foreach ($cats as $c) {
            $insCat->execute($c);
        }

        // Obtener ids de las categorías insertadas
        $catIds = [];
        $stmtAllCats = $pdo->query("SELECT id, slug FROM categorias");
        foreach ($stmtAllCats->fetchAll() as $cRow) {
            $catIds[$cRow['slug']] = $cRow['id'];
        }

        // Insertar los 8 productos oficiales
        $productsToSeed = [
            [
                'Termos grabados', 'termos-grabados', 
                'Termos de acero inoxidable con grabado láser de acabado limpio, sobrio y altamente duradero.', 
                $catIds['termos'], 'termo.png', json_encode(['termo_before.jpg', 'termo_after.jpg']), 'termos-grabados-sku', 1.80, 'Termos'
            ],
            [
                'Agendas personalizadas', 'agendas-personalizadas', 
                'Agendas con cubiertas de tacto cuero (PU termosensible) listas para grabados de gran textura o bajo relieve.', 
                $catIds['agendas'], 'agenda.png', json_encode(['agenda_before.jpg', 'agenda_after.jpg']), 'agendas-sku', 2.20, 'Agendas'
            ],
            [
                'Llaveros corporativos', 'llaveros-corporativos', 
                'Llaveros de metal cepillado y cuero con grabado láser permanente de alta precisión.', 
                $catIds['llaveros'], 'llavero.png', json_encode(['llavero_detail.jpg']), 'llaveros-sku', 0.90, 'Llaveros'
            ],
            [
                'Placas corporativas', 'placas-reconocimientos', 
                'Placas conmemorativas y de reconocimiento en acrílico, metal o madera con cortes y acabados limpios.', 
                $catIds['placas'], 'placa.png', json_encode(['placa_detail.jpg']), 'placas-sku', 15.00, 'Placas'
            ],
            [
                'Cajas personalizadas', 'cajas-personalizadas', 
                'Cajas de madera o cartón estructurado a medida con grabado láser CO2 de alta calidad para presentaciones corporativas.', 
                $catIds['cajas'], 'caja.png', json_encode(['caja_before.jpg', 'caja_after.jpg']), 'cajas-sku', 4.50, 'Cajas'
            ],
            [
                'Kits empresariales', 'kits-corporativos', 
                'Cajas y empaques combinando termos grabados, agendas personalizadas y esferos a juego listos para entregar.', 
                $catIds['kits'], 'kit.png', json_encode(['kit_detail.jpg']), 'kits-sku', 12.50, 'Kits'
            ],
            [
                'Carnets PVC', 'credenciales-pvc', 
                'Identificación profesional e institucional impresa en PVC laminado de alta durabilidad con diseño personalizado.', 
                $catIds['carnets'], 'carnets.png', json_encode(['carnet_detail.jpg']), 'carnets-sku', 1.20, 'Carnets'
            ],
            [
                'Esferos grabados', 'esferos-grabados', 
                'Bolígrafos de metal grabados con láser de fibra con acabados finos, ideales para merchandising o uso corporativo.', 
                $catIds['esferos'], 'esfero.png', json_encode(['esfero_detail.jpg']), 'esferos-sku', 0.45, 'Esferos'
            ]
        ];

        $insProd = $pdo->prepare("INSERT INTO productos (name, slug, description_short, category_id, image_main, gallery_images, sku, price, category, is_featured, allows_simulation, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1, 1)");
        foreach ($productsToSeed as $p) {
            $insProd->execute($p);
        }
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
            'email' => 'correo@cardnet.ec',
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
        $enriched['details'] = 'Termo de doble pared de acero inoxidable que mantiene la temperatura. Ideal para personalización con grabado láser de acabado limpio.';
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
