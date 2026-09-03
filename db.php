<?php
// Configuración Oficial de Base de Datos para CardNet.ec (cPanel)

$db_host = 'localhost';
$db_name = 'cardnet_db';     
$db_user = 'cardnet_user'; 
$db_pass = 'CardNet2026!#$';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // 0. AUTO-MIGRACIÓN: Crear tabla de administradores y usuario por defecto si falta
    $tableAdminCheck = $pdo->query("SHOW TABLES LIKE 'usuarios_admin'")->fetch();
    if (!$tableAdminCheck) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `usuarios_admin` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(100) NOT NULL,
          `email` varchar(100) NOT NULL UNIQUE,
          `password` varchar(255) NOT NULL,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
    
    // Asegurar usuario admin@cardnet.ec con contraseña admin123
    $adminExist = $pdo->query("SELECT id FROM usuarios_admin WHERE email = 'admin@cardnet.ec'")->fetchColumn();
    if (!$adminExist) {
        $adminPassHash = password_hash('admin123', PASSWORD_BCRYPT);
        $stmtInsAdmin = $pdo->prepare("INSERT INTO usuarios_admin (name, email, password) VALUES (?, ?, ?)");
        $stmtInsAdmin->execute(['CardNet Admin', 'admin@cardnet.ec', $adminPassHash]);
    }

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

    // 1.5. AUTO-MIGRACIÓN: Columnas en tabla categorias para el Bento Grid / Categorías destacadas
    $cat_cols = $pdo->query("DESCRIBE categorias")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('description', $cat_cols)) {
        $pdo->exec("ALTER TABLE categorias ADD COLUMN description varchar(255) DEFAULT NULL;");
    }
    if (!in_array('image', $cat_cols)) {
        $pdo->exec("ALTER TABLE categorias ADD COLUMN image varchar(255) DEFAULT NULL;");
    }
    if (!in_array('custom_link', $cat_cols)) {
        $pdo->exec("ALTER TABLE categorias ADD COLUMN custom_link varchar(255) DEFAULT NULL;");
    }
    if (!in_array('is_featured', $cat_cols)) {
        $pdo->exec("ALTER TABLE categorias ADD COLUMN is_featured tinyint(1) DEFAULT 1;");
    }

    // Inicializar categorías destacadas únicamente si no existe ninguna categoría registrada
    $catCount = $pdo->query("SELECT COUNT(*) FROM categorias")->fetchColumn();
    if ($catCount == 0) {
        $defaultFeatured = [
            [
                'name' => 'Cintas y lanyards',
                'slug' => 'cintas',
                'description' => 'Cintas impresas full color y accesorios de sujeción.',
                'image' => 'cintas_mockup.jpg',
                'custom_link' => 'productos.php?cat=cintas',
                'order_val' => 1
            ],
            [
                'name' => 'Cajas y Empaques',
                'slug' => 'cajas-y-empaques',
                'description' => 'Packaging corporativo a medida.',
                'image' => 'caja.png',
                'custom_link' => 'productos.php?cat=personalizacion',
                'order_val' => 2
            ],
            [
                'name' => 'Especialidad Láser',
                'slug' => 'especialidad-laser',
                'description' => 'Grabado resistente al uso diario.',
                'image' => 'images/cat_laser.png',
                'custom_link' => '#laser',
                'order_val' => 3
            ],
            [
                'name' => 'Carnetización',
                'slug' => 'carnets',
                'description' => 'Identificación profesional para empresas e instituciones.',
                'image' => 'carnet_mockup.jpg',
                'custom_link' => 'productos.php?cat=carnets',
                'order_val' => 4
            ]
        ];

        foreach ($defaultFeatured as $df) {
            $exist = $pdo->prepare("SELECT id FROM categorias WHERE slug = ? OR name = ?");
            $exist->execute([$df['slug'], $df['name']]);
            $existing_id = $exist->fetchColumn();

            if (!$existing_id) {
                $ins = $pdo->prepare("INSERT INTO categorias (name, slug, description, image, custom_link, order_val, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?, 1, 1)");
                $ins->execute([$df['name'], $df['slug'], $df['description'], $df['image'], $df['custom_link'], $df['order_val']]);
            }
        }
    }

    // 1.8. AUTO-MIGRACIÓN: Tabla secciones_home para Soluciones de Taller y Opciones de Catálogo
    $tableSecciones = $pdo->query("SHOW TABLES LIKE 'secciones_home'")->fetch();
    if (!$tableSecciones) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `secciones_home` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `section_key` varchar(50) NOT NULL,
          `group_name` varchar(100) DEFAULT NULL,
          `title` varchar(150) NOT NULL,
          `subtitle` text DEFAULT NULL,
          `image` varchar(255) DEFAULT NULL,
          `btn_text` varchar(80) DEFAULT NULL,
          `btn_link` varchar(255) DEFAULT NULL,
          `order_val` int(11) DEFAULT 0,
          `is_active` tinyint(1) DEFAULT 1,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $defaultSecciones = [
            // Soluciones de Taller (3 cards)
            ['soluciones', NULL, 'Empresas', 'Carnets, cintas y accesorios para colaboradores, áreas internas y visitantes.', 'carnet_mockup.jpg', 'Cotizar para mi empresa', 'cotizacion.php', 1],
            ['soluciones', NULL, 'Instituciones', 'Identificación para personal administrativo, equipos de apoyo, estudiantes o miembros.', 'carousel_5.jpg', 'Solicitar opciones', 'cotizacion.php', 2],
            ['soluciones', NULL, 'Eventos', 'Credenciales, cintas y porta credenciales para staff, invitados y asistentes.', 'carousel_2.jpg', 'Cotizar para evento', 'cotizacion.php', 3],

            // Catálogo Detallado (6 cards)
            ['catalogo_opciones', 'Cintas porta credenciales', 'Cintas full color', 'Sublimación en poliéster suave de alta resolución.', 'cintas_full_color.jpg', 'Cotizar', 'cotizacion.php?producto=cintas-full-color', 1],
            ['catalogo_opciones', 'Cintas porta credenciales', 'Cintas a un color', 'Serigrafía de alta adherencia para logotipos sólidos y sobrios.', 'cintas_mockup.jpg', 'Cotizar', 'cotizacion.php?producto=cintas-un-color', 2],
            ['catalogo_opciones', 'Cintas porta credenciales', 'Cintas sin impresión', 'Lanyards de tela de alta resistencia en colores corporativos básicos.', 'cintas_sin_impresion.jpg', 'Ver catálogo', 'productos.php?cat=cintas', 3],
            ['catalogo_opciones', 'Credenciales y porta credenciales', 'Credenciales PVC', 'Laminado de alta durabilidad impreso a doble cara para colaboradores.', 'carnet_mockup.jpg', 'Cotizar', 'cotizacion.php?producto=credenciales-pvc', 4],
            ['catalogo_opciones', 'Credenciales y porta credenciales', 'Credenciales para eventos', 'Tarjetas de gran formato para staff, prensa y asistentes.', 'carousel_2.jpg', 'Cotizar', 'cotizacion.php?producto=credenciales-eventos', 5],
            ['catalogo_opciones', 'Credenciales y porta credenciales', 'Porta credenciales', 'Soportes rígidos o fundas de PVC flexible para proteger identificaciones.', 'fundas.jpg', 'Ver catálogo', 'productos.php?cat=porta-credenciales', 6]
        ];

        $stmtInsSec = $pdo->prepare("INSERT INTO secciones_home (section_key, group_name, title, subtitle, image, btn_text, btn_link, order_val, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
        foreach ($defaultSecciones as $sec) {
            $stmtInsSec->execute($sec);
        }
    }

    // 1.85. AUTO-MIGRACIÓN: Tarjetas iniciales para Accesorios Diarios (solo si está vacía)
    try {
        $acc_count = $pdo->query("SELECT COUNT(*) FROM secciones_home WHERE section_key = 'accesorios'")->fetchColumn();
        if ($acc_count == 0) {
            $defaultAcc = [
                ['accesorios', NULL, 'Porta carnets', 'Protección práctica para carnets y tarjetas rígidas.', 'llavero.png', 'Ver opciones', 'productos.php?cat=porta-credenciales', 1],
                ['accesorios', NULL, 'Yoyos retráctiles', 'Accesorio cómodo con cordón extensible para accesos rápidos.', 'yoyos.jpg', 'Cotizar yoyos', 'cotizacion.php?producto=accesorios-identificacion', 2],
                ['accesorios', NULL, 'Fundas transparentes', 'Fundas de PVC blando para acreditaciones de eventos.', 'fundas.jpg', 'Ver opciones', 'productos.php?cat=porta-credenciales', 3]
            ];
            $stmtInsAcc = $pdo->prepare("INSERT INTO secciones_home (section_key, group_name, title, subtitle, image, btn_text, btn_link, order_val, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
            foreach ($defaultAcc as $item) {
                $stmtInsAcc->execute($item);
            }
        }
    } catch (PDOException $e) {}

    // 1.9. AUTO-MIGRACIÓN: Categoría Tagua (crear solo si no existe)
    try {
        $existTagua = $pdo->query("SELECT id FROM categorias WHERE slug = 'tagua'")->fetchColumn();
        if (!$existTagua) {
            $stmtInsTagua = $pdo->prepare("INSERT INTO categorias (name, slug, description, image, custom_link, order_val, is_featured, is_active) VALUES (?, ?, ?, ?, ?, 99, 0, 1)");
            $stmtInsTagua->execute([
                'Tagua',
                'tagua',
                'Productos y regalos corporativos en marfil vegetal ecuatoriano con grabado y corte láser.',
                'tagua_mockup.jpg',
                'tagua.php'
            ]);
            $existTagua = $pdo->lastInsertId();
        }

        // Si la categoría Tagua no tiene productos registrados, creamos el catálogo inicial
        if ($existTagua) {
            $taguaCount = $pdo->query("SELECT COUNT(*) FROM productos WHERE category_id = " . (int)$existTagua)->fetchColumn();
            if ($taguaCount == 0) {
                $taguaDefaultProds = [
                    [
                        'Llaveros de Tagua Grabados a Láser',
                        'llaveros-tagua-laser',
                        'Llavero de marfil vegetal ecuatoriano pulido y personalizado con grabado láser permanente de tu logo o marca.',
                        'Semilla de tagua seleccionada y pulida a mano, herraje metálico de alta resistencia y grabado láser indeleble.',
                        'tagua_llavero.jpg',
                        '["tagua_llavero.jpg", "tagua_mockup.jpg"]',
                        'TAG-001',
                        2.50,
                        500,
                        'Personalizar Llaveros',
                        1
                    ],
                    [
                        'Botones de Tagua Personalizados',
                        'botones-tagua-personalizados',
                        'Botones ecológicos de marfil vegetal para uniformes corporativos, camisas y prendas de alta gama con grabado de logotipo.',
                        'Fabricados a partir de rodajas de tagua natural, acabado mate o brillante con 2 o 4 orificios y grabado perimetral.',
                        'tagua_botones.jpg',
                        '["tagua_botones.jpg", "tagua_mockup.jpg"]',
                        'TAG-002',
                        0.90,
                        1200,
                        'Cotizar Botones',
                        2
                    ],
                    [
                        'Dijes y Medallas de Tagua con Logo',
                        'dijes-medallas-tagua',
                        'Medallas y dijes conmemorativos para eventos sostenibles, congresos, reconocimientos y recuerdos turísticos.',
                        'Corte de silueta y grabado en relieve sobre lámina de tagua natural. Incluye perforación o cordón.',
                        'tagua_dije.jpg',
                        '["tagua_dije.jpg", "tagua_mockup.jpg"]',
                        'TAG-003',
                        1.80,
                        400,
                        'Solicitar Muestra',
                        3
                    ],
                    [
                        'Porta Credenciales Ecológico con Tagua',
                        'porta-credenciales-tagua',
                        'Accesorio de identificación corporativa que combina cordón textil y un elegante broche distintivo de tagua grabado.',
                        'La alternativa perfecta para empresas con políticas ESG y compromiso ecológico en sus eventos e identificación.',
                        'tagua_portacarnet.jpg',
                        '["tagua_portacarnet.jpg", "tagua_mockup.jpg"]',
                        'TAG-004',
                        3.20,
                        350,
                        'Cotizar para Eventos',
                        4
                    ],
                    [
                        'Placa de Reconocimiento en Tagua y Madera',
                        'placa-reconocimiento-tagua-madera',
                        'Placa conmemorativa premium en madera noble con apliques centrales de tagua tallada y grabada al láser.',
                        'Ideal para reconocimientos corporativos, premios ecológicos y eventos institucionales de prestigio.',
                        'tagua_placa.jpg',
                        '["tagua_placa.jpg", "tagua_mockup.jpg"]',
                        'TAG-005',
                        18.00,
                        100,
                        'Cotizar Placa',
                        5
                    ],
                    [
                        'Kit Ejecutivo Ecológico con Tagua',
                        'kit-ejecutivo-tagua',
                        'Caja de madera con libreta reciclada, bolígrafo de bambú y llavero de tagua grabado con el logo de tu empresa.',
                        'Presentación lista para regalos de fin de año, bienvenida a nuevos colaboradores y regalos VIP para clientes.',
                        'tagua_kit.jpg',
                        '["tagua_kit.jpg", "tagua_mockup.jpg"]',
                        'TAG-006',
                        15.50,
                        150,
                        'Cotizar Kit',
                        6
                    ]
                ];

                $insTaguaProd = $pdo->prepare("INSERT INTO productos (category_id, name, slug, description_short, description_long, image_main, gallery_images, sku, price, stock, cta_text, order_val, is_active, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0)");
                foreach ($taguaDefaultProds as $tp) {
                    $insTaguaProd->execute([
                        $existTagua,
                        $tp[0], $tp[1], $tp[2], $tp[3], $tp[4], $tp[5], $tp[6], $tp[7], $tp[8], $tp[9], $tp[10]
                    ]);
                }
            }
        }
    } catch (PDOException $e) {
        // Ignorar si ya existe
    }

    // 1.10. AUTO-MIGRACIÓN: Tabla tagua_content para personalización dinámica de la página Tagua desde Dashboard
    $tableTaguaContent = $pdo->query("SHOW TABLES LIKE 'tagua_content'")->fetch();
    if (!$tableTaguaContent) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `tagua_content` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `content_key` varchar(100) NOT NULL UNIQUE,
          `content_value` longtext DEFAULT NULL,
          `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
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
    if (!in_array('model_3d', $columns)) {
        $pdo->exec("ALTER TABLE productos ADD COLUMN model_3d VARCHAR(255) DEFAULT NULL;");
    }
    if (!in_array('tags_json', $columns)) {
        $pdo->exec("ALTER TABLE productos ADD COLUMN tags_json text DEFAULT NULL;");
    }
    if (!in_array('order_val', $columns)) {
        $pdo->exec("ALTER TABLE productos ADD COLUMN order_val int(11) DEFAULT 0;");
    }

    // AUTO-MIGRACIÓN: Tabla de Etiquetas
    $tagsTableCheck = $pdo->query("SHOW TABLES LIKE 'etiquetas'")->fetch();
    if (!$tagsTableCheck) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `etiquetas` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(100) NOT NULL,
          `color` varchar(50) DEFAULT 'rgba(0,0,0,0.03)',
          `text_color` varchar(50) DEFAULT 'var(--text-muted)',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $pdo->exec("INSERT INTO `etiquetas` (`name`, `color`, `text_color`) VALUES
            ('Acero', 'rgba(0,0,0,0.03)', 'var(--text-muted)'),
            ('Grabado láser', 'rgba(99, 174, 44, 0.08)', 'var(--primary-hover)'),
            ('PVC', 'rgba(0,0,0,0.03)', 'var(--text-muted)'),
            ('Sublimación HD', 'rgba(99, 174, 44, 0.08)', 'var(--primary-hover)');");
    }

    // 3. AUTO-MIGRACIÓN: Tabla de Configuración General
    $settingsCheck = $pdo->query("SHOW TABLES LIKE 'configuraciones'")->fetch();
    if (!$settingsCheck) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `configuraciones` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `whatsapp` varchar(20) NOT NULL DEFAULT '593000000000',
          `email` varchar(100) NOT NULL DEFAULT 'correo@cardnet.ec',
          `address` varchar(255) NOT NULL DEFAULT 'Av. Amazonas, Quito, Ecuador',
          `instagram` varchar(150) DEFAULT NULL,
          `facebook` varchar(150) DEFAULT NULL,
          `site_title` varchar(150) NOT NULL DEFAULT 'CardNet.ec | Personalización Láser',
          `site_description` varchar(255) NOT NULL DEFAULT 'Especialistas en grabado láser y personalización avanzada en Quito.',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $pdo->exec("INSERT INTO `configuraciones` (id, whatsapp, email, address, site_title, site_description) VALUES (1, '593000000000', 'correo@cardnet.ec', 'Av. Amazonas, Quito, Ecuador', 'CardNet.ec | Personalización Láser', 'Especialistas en grabado láser y personalización avanzada en Quito.');");
    } else {
        // Asegurar que exista la fila id = 1
        $configRowCount = $pdo->query("SELECT COUNT(*) FROM configuraciones WHERE id = 1")->fetchColumn();
        if ($configRowCount == 0) {
            $pdo->exec("INSERT INTO `configuraciones` (id, whatsapp, email, address, site_title, site_description) VALUES (1, '593000000000', 'correo@cardnet.ec', 'Av. Amazonas, Quito, Ecuador', 'CardNet.ec | Identificación y accesorios para personal', 'Especialistas en carnets PVC, credenciales, cintas porta credenciales impresas y accesorios.');");
        }
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

    // Asegurar que la tabla clientes tenga la columna logo_path
    try {
        $cliCols = $pdo->query("DESCRIBE clientes")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('logo_url', $cliCols) && !in_array('logo_path', $cliCols)) {
            $pdo->exec("ALTER TABLE clientes CHANGE COLUMN logo_url logo_path varchar(255) NOT NULL;");
        }
        if (!in_array('logo_path', $cliCols) && !in_array('logo_url', $cliCols)) {
            $pdo->exec("ALTER TABLE clientes ADD COLUMN logo_path varchar(255) NOT NULL;");
        }

        // Asegurar que los nombres de clientes coincidan con los logotipos generados
        $pdo->exec("UPDATE clientes SET name = 'APEX' WHERE id = 1 AND (logo_path = 'uploads/cliente1.png' OR logo_path = 'cliente1.png');");
        $pdo->exec("UPDATE clientes SET name = 'LUMINA' WHERE id = 2 AND (logo_path = 'uploads/cliente2.png' OR logo_path = 'cliente2.png');");
        $pdo->exec("UPDATE clientes SET name = 'VORTEX' WHERE id = 3 AND (logo_path = 'uploads/cliente3.png' OR logo_path = 'cliente3.png');");
        $pdo->exec("UPDATE clientes SET name = 'KRONA' WHERE id = 4 AND (logo_path = 'uploads/cliente4.png' OR logo_path = 'cliente4.png');");
        $pdo->exec("UPDATE clientes SET name = 'AERO' WHERE id = 5 AND (logo_path = 'uploads/cliente5.png' OR logo_path = 'cliente5.png');");
    } catch (PDOException $e) {
        // Ignorar si ya está sincronizado
    }

    // 6.5. AUTO-MIGRACIÓN: Campos de Pedido Mínimo, Teléfonos y Correos Adicionales
    $config_columns = $pdo->query("DESCRIBE configuraciones")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('min_order', $config_columns)) {
        $pdo->exec("ALTER TABLE configuraciones ADD COLUMN min_order int(11) DEFAULT 1;");
    }
    if (!in_array('phone_2', $config_columns)) {
        $pdo->exec("ALTER TABLE configuraciones ADD COLUMN phone_2 varchar(50) DEFAULT NULL;");
    }
    if (!in_array('phone_3', $config_columns)) {
        $pdo->exec("ALTER TABLE configuraciones ADD COLUMN phone_3 varchar(50) DEFAULT NULL;");
    }
    if (!in_array('email_2', $config_columns)) {
        $pdo->exec("ALTER TABLE configuraciones ADD COLUMN email_2 varchar(100) DEFAULT NULL;");
    }
    if (!in_array('obras_subtitle', $config_columns)) {
        $pdo->exec("ALTER TABLE configuraciones ADD COLUMN obras_subtitle varchar(255) DEFAULT 'Obras del Taller';");
    }
    if (!in_array('obras_title', $config_columns)) {
        $pdo->exec("ALTER TABLE configuraciones ADD COLUMN obras_title varchar(255) DEFAULT 'Piezas seleccionadas para personalizar';");
    }
    if (!in_array('obras_desc', $config_columns)) {
        $pdo->exec("ALTER TABLE configuraciones ADD COLUMN obras_desc text DEFAULT NULL;");
    }
    if (!in_array('accesorios_subtitle', $config_columns)) {
        $pdo->exec("ALTER TABLE configuraciones ADD COLUMN accesorios_subtitle varchar(255) DEFAULT 'Accesorios Diarios';");
    }
    if (!in_array('accesorios_title', $config_columns)) {
        $pdo->exec("ALTER TABLE configuraciones ADD COLUMN accesorios_title varchar(255) DEFAULT 'Accesorios para el uso diario';");
    }
    if (!in_array('accesorios_desc', $config_columns)) {
        $pdo->exec("ALTER TABLE configuraciones ADD COLUMN accesorios_desc text DEFAULT NULL;");
    }


    // AUTO-MIGRACIÓN: Columna de imagen en carrusel y seeding inicial solo si la tabla está vacía
    $carrusel_columns = $pdo->query("DESCRIBE carrusel")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('image', $carrusel_columns)) {
        $pdo->exec("ALTER TABLE carrusel ADD COLUMN image varchar(255) DEFAULT NULL;");
    }

    // Seeding inicial de carrusel ÚNICAMENTE si está 100% vacío (nunca borra ni sobreescribe)
    $carrusel_count = $pdo->query("SELECT COUNT(*) FROM carrusel")->fetchColumn();
    if ($carrusel_count == 0) {
        $pdo->exec("INSERT INTO `carrusel` (`title`, `subtitle`, `image`, `cta_text`, `cta_url`, `order_val`, `is_active`) VALUES
            ('Carnets PVC personalizados', 'Identificación profesional para empresas, instituciones, eventos y equipos.', 'carousel_1.jpg', 'Cotizar carnets', 'cotizacion.php', 1, 1),
            ('Credenciales para eventos y personal', 'Credenciales claras, funcionales y listas para identificar a tu equipo.', 'carousel_2.jpg', 'Ver credenciales', 'productos.php', 2, 1),
            ('Cintas porta credenciales', 'Cintas impresas full color, a un color o sin impresión para diferentes necesidades.', 'carousel_3.jpg', 'Ver opciones de cintas', 'productos.php', 3, 1),
            ('Porta credenciales y accesorios', 'Complementos prácticos para proteger y presentar mejor cada identificación.', 'carousel_4.jpg', 'Explorar accesorios', 'productos.php', 4, 1),
            ('Identificación para empresas e instituciones', 'Soluciones para equipos que necesitan verse organizados y profesionales.', 'carousel_5.jpg', 'Cotizar para mi empresa', 'cotizacion.php', 5, 1);");
    }

    $prod_columns_migration = $pdo->query("DESCRIBE productos")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('volume_prices', $prod_columns_migration)) {
        $pdo->exec("ALTER TABLE productos ADD COLUMN volume_prices text DEFAULT NULL;");
    }
    if (!in_array('materials_json', $prod_columns_migration)) {
        $pdo->exec("ALTER TABLE productos ADD COLUMN materials_json text DEFAULT NULL;");
    }

    // Tabla de materiales por si se borra
    $pdo->exec("CREATE TABLE IF NOT EXISTS `materiales` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL,
      `description` varchar(255) DEFAULT NULL,
      `is_active` tinyint(1) DEFAULT 1,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $mat_count = $pdo->query("SELECT COUNT(*) FROM materiales")->fetchColumn();
    if ($mat_count == 0) {
        $pdo->exec("INSERT INTO `materiales` (`name`, `description`) VALUES
            ('Acero inoxidable', 'Ideal para termos, botellas y piezas metálicas de uso diario.'),
            ('Madera noble', 'Acabado cálido para reconocimientos, cajas y regalos corporativos.'),
            ('Acrílico premium', 'Limpio, moderno y versátil para placas, señalética y detalles.'),
            ('Cuero / PU termosensible', 'Ideal para grabados en agendas, libretas y carpetas ejecutivas.');");
    }

    
    // AUTO-MIGRACIÓN: Seeding de la tabla clientes si está vacía
    $client_count = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
    if ($client_count == 0) {
        $pdo->exec("INSERT INTO `clientes` (`name`, `logo_path`, `order_val`, `is_active`) VALUES
            ('Empresa de Logística', 'logo1.jpg', 1, 1),
            ('Banco del Austro', 'logo2.jpg', 2, 1),
            ('Industrias Médicas', 'logo3.jpg', 3, 1),
            ('Consultora Andina', 'logo4.jpg', 4, 1);");
    }

    // 7. AUTO-MIGRACIÓN: Seeding de productos oficiales ÚNICAMENTE si la tabla está 100% vacía (0 productos)
    $stmtCount = $pdo->query("SELECT COUNT(*) FROM productos");
    $count = $stmtCount->fetchColumn();

    if ($count == 0) {
        // Solo insertar categorías si no hay ninguna
        $catCheckCount = $pdo->query("SELECT COUNT(*) FROM categorias")->fetchColumn();
        if ($catCheckCount == 0) {
            $cats = [
                ['Carnets', 'carnets', 1],
                ['Credenciales', 'credenciales', 2],
                ['Cintas', 'cintas', 3],
                ['Porta credenciales', 'porta-credenciales', 4],
                ['Accesorios', 'accesorios', 5],
                ['Tarjetas PVC', 'tarjetas-pvc', 6],
                ['Personalización', 'personalizacion', 7],
                ['Kits', 'kits', 8],
                ['Placas', 'placas', 9]
            ];
            $insCat = $pdo->prepare("INSERT INTO categorias (name, slug, order_val, is_active) VALUES (?, ?, ?, 1)");
            foreach ($cats as $c) {
                $insCat->execute($c);
            }
        }

        // Obtener ids de las categorías insertadas
        $catIds = [];
        $stmtAllCats = $pdo->query("SELECT id, slug FROM categorias");
        foreach ($stmtAllCats->fetchAll() as $cRow) {
            $catIds[$cRow['slug']] = $cRow['id'];
        }

        // Insertar los productos oficiales con imágenes reales de alta definición
                $productsToSeed = [
            [
                'Carnets PVC', 'credenciales-pvc', 
                'Identificación profesional e institucional impresa en PVC laminado de alta durabilidad con diseño personalizado.', 
                $catIds['carnets'], 'carnet_mockup.jpg', json_encode(['carnet_mockup.jpg']), 'carnets-sku', 1.20, 'Carnets'
            ],
            [
                'Credenciales corporativas', 'credenciales-corporativas', 
                'Credenciales de identificación personalizadas con acabado sobrio para colaboradores de empresas e instituciones.', 
                $catIds['credenciales'], 'carnet_mockup.jpg', json_encode([]), 'cred-corp-sku', 1.50, 'Credenciales'
            ],
            [
                'Credenciales para eventos', 'credenciales-eventos', 
                'Credenciales claras y funcionales para staff, invitados, asistentes y control de acceso en ferias o congresos.', 
                $catIds['credenciales'], 'carousel_2.jpg', json_encode([]), 'cred-event-sku', 1.00, 'Credenciales'
            ],
            [
                'Cintas porta credenciales full color', 'cintas-full-color', 
                'Cintas porta credenciales personalizadas con sublimación full color para mayor presencia de marca.', 
                $catIds['cintas'], 'cintas_mockup.jpg', json_encode([]), 'cintas-fc-sku', 1.80, 'Cintas'
            ],
            [
                'Cintas a un color', 'cintas-un-color', 
                'Cintas porta credenciales de poliéster estampadas a un color con acabado limpio y sobrio.', 
                $catIds['cintas'], 'cintas_mockup.jpg', json_encode([]), 'cintas-uc-sku', 1.20, 'Cintas'
            ],
            [
                'Cintas sin impresión', 'cintas-sin-impresion', 
                'Cintas porta credenciales lisas en colores institucionales básicos para uso diario o eventos.', 
                $catIds['cintas'], 'cintas_mockup.jpg', json_encode([]), 'cintas-si-sku', 0.80, 'Cintas'
            ],
            [
                'Porta carnets', 'porta-carnets', 
                'Accesorios rígidos o flexibles transparentes para proteger y portar carnets de forma práctica.', 
                $catIds['porta-credenciales'], 'llavero.png', json_encode([]), 'porta-carnets-sku', 0.50, 'Porta credenciales'
            ],
            [
                'Porta credenciales', 'porta-credenciales', 
                'Fundas o estuches transparentes ideales para credenciales de eventos y acreditaciones de personal.', 
                $catIds['porta-credenciales'], 'fundas.jpg', json_encode([]), 'porta-cred-sku', 0.40, 'Porta credenciales'
            ],
            [
                'Tarjetas PVC', 'tarjetas-pvc', 
                'Tarjetas plásticas personalizadas para control de accesos, membresías, fidelización de clientes o identificación.', 
                $catIds['tarjetas-pvc'], 'carnet_mockup.jpg', json_encode([]), 'tarjetas-pvc-sku', 1.10, 'Tarjetas PVC'
            ],
            [
                'Accesorios para identificación', 'accesorios-identificacion', 
                'Complementos de identificación diaria como yoyos retráctiles con resorte metálico, clips y lanyards.', 
                $catIds['accesorios'], 'yoyos.jpg', json_encode([]), 'acc-id-sku', 0.60, 'Accesorios'
            ],
            [
                'Agendas personalizadas', 'agendas-personalizadas', 
                'Agendas con cubiertas de tacto cuero (PU termosensible) listas para grabados de gran textura o bajo relieve.', 
                $catIds['personalizacion'], 'agenda.png', json_encode(['agenda_before.jpg', 'agenda_after.jpg']), 'agendas-sku', 2.20, 'Personalización'
            ],
            [
                'Llaveros corporativos', 'llaveros-corporativos', 
                'Llaveros de metal cepillado y cuero con grabado láser permanente de alta precisión.', 
                $catIds['personalizacion'], 'llavero.png', json_encode(['llavero_detail.jpg']), 'llaveros-sku', 0.90, 'Personalización'
            ],
            [
                'Termos grabados', 'termos-grabados', 
                'Termos de acero inoxidable con grabado láser de acabado limpio, sobrio y altamente duradero.', 
                $catIds['personalizacion'], 'termo.png', json_encode(['termo_before.jpg', 'termo_after.jpg']), 'termos-grabados-sku', 1.80, 'Personalización'
            ],
            [
                'Cajas personalizadas', 'cajas-personalizadas', 
                'Cajas de madera o cartón estructurado a medida con grabado láser CO2 de alta calidad para presentaciones corporativas.', 
                $catIds['personalizacion'], 'caja.png', json_encode(['caja_before.jpg', 'caja_after.jpg']), 'cajas-sku', 4.50, 'Personalización'
            ],
            [
                'Kits empresariales', 'kits-corporativos', 
                'Cajas y empaques combinando termos grabados, agendas personalizadas y esferos a juego listos para entregar.', 
                $catIds['kits'], 'kit.png', json_encode(['kit_detail.jpg']), 'kits-sku', 12.50, 'Kits'
            ],
            [
                'Placas corporativas', 'placas-reconocimientos', 
                'Placas conmemorativas y de reconocimiento en acrílico, metal o madera con cortes y acabados limpios.', 
                $catIds['placas'], 'placa.png', json_encode(['placa_detail.jpg']), 'placas-sku', 15.00, 'Placas'
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
            'whatsapp' => '593000000000',
            'phone_2' => '',
            'phone_3' => '',
            'email' => 'correo@cardnet.ec',
            'email_2' => '',
            'address' => 'Av. Amazonas, Quito, Ecuador',
            'instagram' => '',
            'facebook' => '',
            'site_title' => 'CardNet.ec | Identificación y accesorios para personal en Ecuador',
            'site_description' => 'Especialistas en carnets PVC, credenciales, cintas porta credenciales impresas y accesorios.',
            'obras_subtitle' => 'Obras del Taller',
            'obras_title' => 'Piezas seleccionadas para personalizar',
            'obras_desc' => 'Artículos de alta resistencia diseñados para acoger tu marca con grabado láser de máxima definición.',
            'accesorios_subtitle' => 'Accesorios Diarios',
            'accesorios_title' => 'Accesorios para el uso diario',
            'accesorios_desc' => 'Complementos prácticos para proteger, portar y presentar mejor cada credencial.'
        ];
    } catch (PDOException $e) {
        return [];
    }
}

// Función auxiliar para resolver rutas de imágenes omni-compatible
function getUploadedImgUrl($path, $default = 'uploads/carnet_mockup.jpg', $isAdmin = null) {
    if ($isAdmin === null) {
        $script_name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $isAdmin = (strpos($script_name, '/admin/') !== false);
    }
    $prefix = $isAdmin ? '../' : '';

    if (empty($path)) {
        return $prefix . ltrim($default, './');
    }
    $path = trim($path);
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
    
    // Normalizar eliminando prefijos relativos o uploads/ duplicados
    $clean = preg_replace('#^(\.\./)*(uploads/)?#', '', $path);
    
    if (strpos($clean, 'images/') === 0) {
        return $prefix . $clean;
    }
    if (strpos($clean, 'categories/') === 0 || strpos($clean, 'products/') === 0 || strpos($clean, 'carousel/') === 0 || strpos($clean, 'sections/') === 0) {
        return $prefix . 'uploads/' . $clean;
    }
    if (strpos($clean, 'cat_') === 0) {
        return $prefix . 'uploads/categories/' . $clean;
    }
    if (strpos($clean, 'prod_') === 0 || strpos($clean, 'gal_') === 0) {
        return $prefix . 'uploads/products/' . $clean;
    }
    if (strpos($clean, 'slide_') === 0) {
        return $prefix . 'uploads/carousel/' . $clean;
    }
    if (strpos($clean, 'sec_') === 0) {
        return $prefix . 'uploads/sections/' . $clean;
    }
    return $prefix . 'uploads/' . $clean;
}

// Función auxiliar para enriquecer productos con atributos comerciales para buscador, filtros y modales
function enrichProduct($prod) {
    if (empty($prod)) return [];
    $slug = $prod['slug'] ?? '';
    
    // Atributos de apoyo para filtros y buscador si no están definidos
    $enriched = [
        'material' => 'Acero / Metal',
        'technique' => 'Grabado láser',
        'use' => 'Kits corporativos, eventos, uso institucional',
        'details' => $prod['description_short'] ?? 'Artículo corporativo listo para personalización.'
    ];

    if (stripos($slug, 'termo') !== false) {
        $enriched['material'] = 'Acero Inoxidable';
        $enriched['technique'] = 'Grabado láser permanente';
        $enriched['use'] = 'Kits corporativos, eventos, uso diario';
    } elseif (stripos($slug, 'agenda') !== false || stripos($slug, 'libreta') !== false) {
        $enriched['material'] = 'Cuero / Cuerina PU';
        $enriched['technique'] = 'Grabado láser térmico / Bajo relieve';
        $enriched['use'] = 'Oficina, congresos, regalos ejecutivos';
    } elseif (stripos($slug, 'placa') !== false) {
        $enriched['material'] = 'Acrílico Cristalino / Metal';
        $enriched['technique'] = 'Corte y grabado láser de alta precisión';
        $enriched['use'] = 'Reconocimientos, premiaciones, señalética';
    } elseif (stripos($slug, 'kit') !== false) {
        $enriched['material'] = 'Mixto (Acero / Madera / Cuero)';
        $enriched['technique'] = 'Grabado y serigrafía';
        $enriched['use'] = 'Regalos empresariales, bienvenida a colaboradores';
    } elseif (stripos($slug, 'llavero') !== false) {
        $enriched['material'] = 'Acero / Cuero';
        $enriched['technique'] = 'Grabado láser de fibra';
        $enriched['use'] = 'Regalos promocionales, merchandising';
    } elseif (stripos($slug, 'caja') !== false) {
        $enriched['material'] = 'Madera / MDF';
        $enriched['technique'] = 'Grabado láser CO2';
        $enriched['use'] = 'Empaque de regalo, botellas de vino, kits';
    } elseif (stripos($slug, 'carnet') !== false || stripos($slug, 'credencial') !== false) {
        $enriched['material'] = 'Plástico PVC';
        $enriched['technique'] = 'Impresión térmica color';
        $enriched['use'] = 'Identificación institucional, seguridad, eventos';
    } elseif (stripos($slug, 'tagua') !== false || stripos($slug, 'boton') !== false || stripos($slug, 'dije') !== false) {
        $enriched['material'] = 'Semilla de Tagua (Marfil Vegetal Ecuatoriano)';
        $enriched['technique'] = 'Corte y Grabado Láser';
        $enriched['use'] = 'Regalos corporativos sostenibles, souvenirs, moda';
    }

    // Los datos reales de la BD siempre prevalecen sobre los valores por defecto
    return array_merge($enriched, $prod);
}

// Valores por defecto para la página de Tagua (editables desde admin/tagua.php)
$defaultTaguaContent = [
    // Hero
    'hero_badge' => 'Marfil Vegetal Ecuatoriano · Línea Ecológica Corporativa',
    'hero_title' => 'Artesanía y Precisión en Tagua Personalizada',
    'hero_subtitle' => 'Transformamos la semilla de marfil vegetal ecuatoriano en artículos de identificación, botones, accesorios y regalos corporativos mediante grabado y corte láser de alta definición.',
    'hero_image' => 'tagua_hero_bg.jpg',
    'hero_btn_text' => 'Explorar Catálogo de Tagua',
    'hero_btn_url' => '#catalogo-tagua',
    'hero_btn_sec_text' => 'Consultar con un Asesor',

    // Stats Bar
    'stat_1_title' => '100% Biodegradable',
    'stat_1_desc' => 'Sustituto natural al plástico',
    'stat_2_title' => 'Grabado Láser HD',
    'stat_2_desc' => 'Marcado térmico inalterable',
    'stat_3_title' => 'Origen Ecuatoriano',
    'stat_3_desc' => 'Cosecha silvestre seleccionada',
    'stat_4_title' => 'Pedidos por Mayor',
    'stat_4_desc' => 'Empresas, eventos y marcas',

    // Origen & ESG
    'esg_badge' => 'Sostenibilidad & Cumplimiento ESG',
    'esg_title' => 'Propiedades y Ventajas del Marfil Vegetal',
    'esg_description' => 'La Tagua (Phytelephas aequatorialis) es una semilla originaria de los bosques húmedos del Ecuador. Al madurar y secarse, alcanza una dureza y tonalidad aperlada equivalente al marfil animal, permitiendo obtener piezas orgánicas de alta elegancia sin impacto ecológico negativo.',
    
    // Beneficios 1-4
    'benefit_1_title' => 'Cero Huella Plástica',
    'benefit_1_desc' => 'Ideal para organizaciones con directrices ambientales y programas de sostenibilidad que requieren merchandising 100% orgánico.',
    'benefit_2_title' => 'Grabado Inalterable',
    'benefit_2_desc' => 'La tecnología láser cauteriza la fibra interna de la semilla generando un contraste nítido que resiste fricción, humedad y paso del tiempo.',
    'benefit_3_title' => 'Piezas Exclusivas',
    'benefit_3_desc' => 'Cada botón, llavero o distintivo conserva las vetas y tonalidades botánicas de la semilla, haciendo que cada producto sea irrepetible.',
    'benefit_4_title' => 'Producción Nacional',
    'benefit_4_desc' => 'Elaborado con materia prima recolectada éticamente en Ecuador, apoyando el trabajo artesanal y la preservación de los bosques.',

    // Catálogo Intro & Custom Box
    'catalog_badge' => 'Catálogo Corporativo',
    'catalog_title' => 'Línea de Productos en Tagua',
    'catalog_description' => 'Modelos listos para personalizar con su logotipo institucional, numeración o nombres.',
    'custom_box_title' => '¿Requiere medidas, siluetas o cortes especiales?',
    'custom_box_desc' => 'Fabricamos piezas bajo plano vectorial con diámetros, grosores y perforaciones a la medida de su proyecto.',
    'custom_box_btn_text' => 'Solicitar Fabricación a Medida',

    // Proceso de Taller
    'process_badge' => 'Control y Precisión',
    'process_title' => 'Flujo de Producción y Grabado en Taller',
    'process_description' => 'Protocolo riguroso desde la selección de la semilla hasta la entrega corporativa.',
    'step_1_title' => 'Selección y Curado',
    'step_1_desc' => 'Clasificación de semillas maduras con porcentaje de humedad controlado para evitar deformaciones mecánicas.',
    'step_2_title' => 'Torneado y Calibración',
    'step_2_desc' => 'Desbaste y pulido a espejo para generar una superficie plana milimétrica de alta adherencia óptica.',
    'step_3_title' => 'Marcado Láser CNC',
    'step_3_desc' => 'Aplicación del diseño vectorial mediante haz concentrado de alta resolución para líneas y textos finos.',
    'step_4_title' => 'Inspección y Despacho',
    'step_4_desc' => 'Control de calidad unidad por unidad, ensamblado de herrajes y empaque protegido para envío nacional.',

    // Sectores
    'sectors_badge' => 'Aplicaciones Comerciales',
    'sectors_title' => 'Soluciones para Empresas e Instituciones',
    'sectors_description' => 'Sectores que integran productos de Tagua en sus operaciones y eventos.',
    'sector_1_title' => 'Cumbres y Congresos ESG',
    'sector_1_desc' => 'Credenciales, medallas conmemorativas y souvenirs para eventos corporativos con compromiso ecológico.',
    'sector_2_title' => 'Hotelería y Turismo VIP',
    'sector_2_desc' => 'Llaveros de habitación con numeración permanente y detalles exclusivos para huéspedes internacionales.',
    'sector_3_title' => 'Confección y Uniformes',
    'sector_3_desc' => 'Botones personalizados con monograma grabado para camisas ejecutivas y prendas corporativas de alta gama.',
    'sector_4_title' => 'Reconocimientos y Kits',
    'sector_4_desc' => 'Placas conmemorativas y sets corporativos para premiaciones internas y regalos de fin de año.',

    // FAQ 1-4
    'faq_1_q' => '¿Qué es la tagua y por qué se denomina marfil vegetal?',
    'faq_1_a' => 'La tagua es la semilla madura de la palma Phytelephas aequatorialis, nativa de los bosques húmedos del Ecuador. Una vez deshidratada, adquiere una dureza, color marfil y densidad similar a la del marfil animal, constituyendo una alternativa 100% ecológica, sostenible y legal.',
    'faq_2_q' => '¿Es posible reproducir logotipos con tipografías y detalles finos?',
    'faq_2_a' => 'Sí. Mediante sistemas de corte y grabado láser computarizado reproducimos vectores, isotipos y textos con precisión micrométrica. Previo a la fabricación enviamos una simulación digital para su revisión y aprobación técnica.',
    'faq_3_q' => '¿El grabado láser en tagua es resistente al uso diario?',
    'faq_3_a' => 'Totalmente. Al no tratarse de tintas superficiales sino de una cauterización térmica en la propia estructura botánica de la semilla, el grabado es indeleble y no se desprende con la fricción, el agua o la exposición al ambiente.',
    'faq_4_q' => '¿Cuáles son los tiempos de entrega y cobertura de envíos?',
    'faq_4_a' => 'Despachamos pedidos a Quito, Guayaquil, Cuenca y todas las provincias del Ecuador mediante Servientrega o transporte logístico especializado. Los tiempos de producción estándar oscilan entre 24 y 72 horas según el volumen requerido.',

    // CTA Final
    'cta_badge' => 'Asesoría Corporativa Inmediata',
    'cta_title' => 'Inicie su Cotización de Productos en Tagua',
    'cta_subtitle' => 'Envíenos los requerimientos de su empresa junto con su logotipo. Le entregaremos una propuesta técnica y cotización formal a la brevedad.',
    'cta_btn_text' => 'Iniciar Cotización Formal',
    'cta_btn_sec_text' => 'Contactar Asesor por WhatsApp'
];

function getTaguaContent($pdo) {
    global $defaultTaguaContent;
    $res = [];
    try {
        $stmt = $pdo->query("SELECT content_key, content_value FROM tagua_content");
        while ($row = $stmt->fetch()) {
            if ($row['content_value'] !== null && $row['content_value'] !== '') {
                $res[$row['content_key']] = $row['content_value'];
            }
        }
    } catch (PDOException $e) {}
    
    return array_merge($defaultTaguaContent, $res);
}
 