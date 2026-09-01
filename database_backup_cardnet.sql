-- -------------------------------------------------------------
-- Respaldo Completo de Base de Datos para CardNet.ec
-- Compatible con cPanel / phpMyAdmin / MySQL 5.7+ / MariaDB 10+
-- -------------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- --------------------------------------------------------
-- Tabla: `usuarios_admin`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `usuarios_admin`;
CREATE TABLE `usuarios_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `usuarios_admin` (`id`, `name`, `email`, `password`) VALUES
(1, 'CardNet Admin', 'admin@cardnet.ec', '$2y$10$wTf2i68623r5/5g1gX4w/.5X71H6d9aC4P1B2Y4E3K2G1M2N3O4P.');

-- --------------------------------------------------------
-- Tabla: `categorias`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL UNIQUE,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `custom_link` varchar(255) DEFAULT NULL,
  `order_val` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categorias` (`id`, `name`, `slug`, `description`, `image`, `custom_link`, `order_val`, `is_featured`, `is_active`) VALUES
(1, 'Artículos personalizados', 'articulos-personalizados', 'Termos, agendas, llaveros y artículos de alta gama con grabado láser.', 'termo.png', 'productos.php?cat=articulos-personalizados', 1, 1, 1),
(2, 'Identificación corporativa', 'identificacion-corporativa', 'Cintas sublimadas, lanyards y credenciales institucionales.', 'cintas_full_color.jpg', 'productos.php?cat=identificacion-corporativa', 2, 1, 1),
(3, 'Reconocimientos y placas', 'reconocimientos', 'Placas conmemorativas en madera noble y acrílico de alta precisión.', 'placa.png', 'productos.php?cat=reconocimientos', 3, 1, 1),
(4, 'Carnets en PVC', 'carnets', 'Impresión térmica de alta definición para colaboradores y eventos.', 'carnet_mockup.jpg', 'carnets.php', 4, 1, 1),
(5, 'Credenciales', 'credenciales', 'Credenciales rígidas y de eventos con accesorios de soporte.', 'carousel_2.jpg', 'productos.php?cat=credenciales', 5, 0, 1),
(6, 'Cintas y lanyards', 'cintas', 'Cintas personalizadas a full color y un color.', 'cintas_mockup.jpg', 'productos.php?cat=cintas', 6, 0, 1),
(7, 'Porta credenciales', 'porta-credenciales', 'Fundas flexibles y porta carnets rígidos.', 'fundas.jpg', 'productos.php?cat=porta-credenciales', 7, 0, 1),
(8, 'Kits corporativos', 'kits-corporativos', 'Sets ejecutivos en cajas de madera y estuches personalizados.', 'kit.png', 'productos.php?cat=kits-corporativos', 8, 0, 1),
(9, 'Productos de Tagua', 'tagua', 'Piezas exclusivas de marfil vegetal ecuatoriano con grabado láser.', 'tagua_hero_bg.jpg', 'tagua.php', 99, 0, 1);

-- --------------------------------------------------------
-- Tabla: `etiquetas`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `etiquetas`;
CREATE TABLE `etiquetas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `color_bg` varchar(20) DEFAULT '#63AE2C',
  `color_text` varchar(20) DEFAULT '#FFFFFF',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `etiquetas` (`id`, `name`, `slug`, `color_bg`, `color_text`) VALUES
(1, 'Más Vendido', 'mas-vendido', '#63AE2C', '#FFFFFF'),
(2, 'Nuevo', 'nuevo', '#10140F', '#FFFFFF'),
(3, 'Ecológico', 'ecologico', '#166534', '#FFFFFF'),
(4, 'Premium', 'premium', '#854D0E', '#FFFFFF');

-- --------------------------------------------------------
-- Tabla: `productos`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL UNIQUE,
  `description_short` text DEFAULT NULL,
  `description_long` text DEFAULT NULL,
  `image_main` varchar(255) DEFAULT NULL,
  `gallery_images` text DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 2.50,
  `stock` int(11) DEFAULT 500,
  `category` varchar(100) DEFAULT NULL,
  `cta_text` varchar(80) DEFAULT 'Cotizar',
  `order_val` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `allows_simulation` tinyint(1) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `model_3d` varchar(255) DEFAULT NULL,
  `tag_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `productos` (`id`, `category_id`, `name`, `slug`, `description_short`, `description_long`, `image_main`, `gallery_images`, `sku`, `price`, `stock`, `category`, `cta_text`, `order_val`, `is_featured`, `allows_simulation`, `is_active`) VALUES
(1, 4, 'Carnets PVC', 'credenciales-pvc', 'Identificación profesional e institucional impresa en PVC laminado de alta durabilidad.', 'Carnets impresos con tecnología térmica de alta resolución, laminado protector contra rayones y agua.', 'carnet_mockup.jpg', '["carnet_mockup.jpg"]', 'PVC-001', 1.20, 1000, 'Carnets', 'Cotizar Carnets', 1, 1, 1, 1),
(2, 5, 'Credenciales corporativas', 'credenciales-corporativas', 'Credenciales de identificación personalizadas con acabado sobrio para colaboradores.', 'Impresión doble cara en PVC rígido, compatible con códigos QR, código de barras y banda magnética.', 'carnet_mockup.jpg', '["carnet_mockup.jpg"]', 'CRED-001', 1.50, 800, 'Credenciales', 'Cotizar Credenciales', 2, 1, 1, 1),
(3, 5, 'Credenciales para eventos', 'credenciales-eventos', 'Credenciales claras y funcionales para staff, invitados, prensa y congresos.', 'Tarjetas de gran formato para acreditación rápida y control visual en ferias y eventos masivos.', 'carousel_2.jpg', '["carousel_2.jpg"]', 'CRED-EVT', 1.00, 1500, 'Credenciales', 'Cotizar Eventos', 3, 1, 1, 1),
(4, 6, 'Cintas porta credenciales full color', 'cintas-full-color', 'Cintas de poliéster satinado con sublimación fotográfica a doble cara.', 'Lanyards corporativos de textura suave con mosquetón metálico y broche de seguridad opcional.', 'cintas_full_color.jpg', '["cintas_full_color.jpg"]', 'CIN-FULL', 0.85, 2000, 'Cintas', 'Cotizar Cintas', 4, 1, 1, 1),
(5, 6, 'Cintas porta credenciales a un color', 'cintas-un-color', 'Cintas textiles de alta resistencia estampadas con logotipo institucional en serigrafía.', 'Cintas resistentes para uso diario en oficinas, fábricas y centros educativos.', 'cintas_mockup.jpg', '["cintas_mockup.jpg"]', 'CIN-1COL', 0.65, 2500, 'Cintas', 'Cotizar Cintas', 5, 0, 1, 1),
(6, 6, 'Cintas porta credenciales sin impresión', 'cintas-sin-impresion', 'Lanyards lisos en colores institucionales básicos para entrega inmediata.', 'Disponibles en azul, negro, rojo, verde y gris para identificación rápida.', 'cintas_sin_impresion.jpg', '["cintas_sin_impresion.jpg"]', 'CIN-LISA', 0.45, 3000, 'Cintas', 'Comprar Cintas', 6, 0, 1, 1),
(7, 7, 'Porta credenciales rígidos', 'porta-credenciales-rigidos', 'Soportes de acrílico y policarbonato para proteger carnets de caídas y fricción.', 'Protección rígida frontal y posterior con pasador para pinza o cinta.', 'fundas.jpg', '["fundas.jpg"]', 'PORTA-RIG', 0.50, 1200, 'Porta credenciales', 'Cotizar Soportes', 7, 0, 1, 1),
(8, 7, 'Fundas transparentes para credenciales', 'fundas-transparentes', 'Fundas flexibles de PVC transparente impermeables para eventos y congresos.', 'Ideales para credenciales de acceso temporal y acreditaciones de ferias.', 'fundas.jpg', '["fundas.jpg"]', 'FUNDA-PVC', 0.25, 5000, 'Porta credenciales', 'Cotizar Fundas', 8, 0, 1, 1),
(9, 1, 'Termo Metálico Personalizado', 'termo-metalico', 'Termo de acero inoxidable de doble pared con grabado láser permanente de logotipo.', 'Conserva bebidas frías o calientes por 12 horas. Grabado indeleble de alta precisión sin tintas.', 'termo.png', '["termo.png", "termo_after.jpg"]', 'TER-001', 9.50, 300, 'Artículos personalizados', 'Cotizar Termo', 9, 1, 1, 1),
(10, 1, 'Agenda Ejecutiva con Grabado Láser', 'agenda-ejecutiva', 'Agenda de cuero sintético con termo-grabado en bajo relieve para empresas.', 'Hojas de papel avena, cinta separadora y grabado láser de gran contraste táctil.', 'agenda.png', '["agenda.png", "agenda_after.jpg"]', 'AGE-001', 8.00, 450, 'Artículos personalizados', 'Cotizar Agenda', 10, 1, 1, 1),
(11, 3, 'Placa Conmemorativa de Reconocimiento', 'placa-reconocimiento', 'Placa de madera noble con aplique de acrílico y grabado láser de alta fidelidad.', 'Distintivo conmemorativo para homenajes, aniversarios de empresa y premiaciones institucionales.', 'placa.png', '["placa.png", "placa_detail.jpg"]', 'PLA-001', 18.00, 150, 'Reconocimientos', 'Cotizar Placa', 11, 1, 1, 1),
(12, 8, 'Kit Corporativo Ejecutivo', 'kit-corporativo', 'Set premium en caja de madera con termo grabado, libreta y bolígrafo ecológico.', 'Presentación de lujo lista para regalos corporativos de fin de año o bienvenida VIP.', 'kit.png', '["kit.png", "kit_detail.jpg"]', 'KIT-001', 22.00, 200, 'Kits corporativos', 'Cotizar Kit', 12, 1, 1, 1),
(13, 1, 'Bolígrafo Metálico con Grabado Láser', 'boligrafo-metalico', 'Bolígrafo ejecutivo de aluminio con apertura retráctil y grabado láser permanente.', 'Escritura suave, tinta alemana y grabado milimétrico de nombre o marca.', 'esfero.png', '["esfero.png", "esfero_detail.jpg"]', 'BOL-001', 1.80, 2000, 'Artículos personalizados', 'Cotizar Bolígrafo', 13, 0, 1, 1),
(14, 1, 'Llavero Metálico y Cuero Grabado', 'llavero-metal-cuero', 'Llavero robusto de aleación de zinc y cuero sintético con grabado de alta visibilidad.', 'Accesorio duradero ideal para eventos corporativos y merchandising premium.', 'llavero.png', '["llavero.png", "llavero_detail.jpg"]', 'LLAV-001', 2.20, 800, 'Artículos personalizados', 'Cotizar Llavero', 14, 0, 1, 1),
(15, 9, 'Llaveros de Tagua Grabados a Láser', 'llaveros-tagua-laser', 'Llavero de marfil vegetal ecuatoriano pulido y personalizado con grabado láser permanente de tu logo o marca.', 'Semilla de tagua seleccionada y pulida a mano, herraje metálico de alta resistencia y grabado láser indeleble.', 'tagua_llavero.jpg', '["tagua_llavero.jpg", "tagua_hero_bg.jpg"]', 'TAG-001', 2.50, 500, 'Productos de Tagua', 'Cotizar Llaveros', 15, 0, 1, 1),
(16, 9, 'Botones de Tagua Personalizados', 'botones-tagua-personalizados', 'Botones ecológicos de marfil vegetal para uniformes corporativos, camisas y prendas de alta gama.', 'Fabricados a partir de rodajas de tagua natural, acabado mate o brillante con 2 o 4 orificios y grabado perimetral.', 'tagua_botones.jpg', '["tagua_botones.jpg", "tagua_hero_bg.jpg"]', 'TAG-002', 0.90, 1200, 'Productos de Tagua', 'Cotizar Botones', 16, 0, 1, 1),
(17, 9, 'Dijes y Medallas de Tagua con Logo', 'dijes-medallas-tagua', 'Medallas y distintivos conmemorativos para eventos sostenibles, congresos internacionales y reconocimientos de marca.', 'Corte de silueta y grabado en relieve sobre lámina de tagua natural. Incluye perforación o cordón.', 'tagua_llavero.jpg', '["tagua_llavero.jpg"]', 'TAG-003', 1.80, 400, 'Productos de Tagua', 'Cotizar Medallas', 17, 0, 1, 1),
(18, 9, 'Porta Credenciales Ecológico con Tagua', 'porta-credenciales-tagua', 'Accesorio de identificación corporativa que integra cordón textil y un broche distintivo de tagua grabado.', 'La alternativa perfecta para empresas con políticas ESG y compromiso ecológico en sus eventos e identificación.', 'tagua_llavero.jpg', '["tagua_llavero.jpg"]', 'TAG-004', 3.20, 350, 'Productos de Tagua', 'Cotizar Porta Carnets', 18, 0, 1, 1),
(19, 9, 'Placa de Reconocimiento en Tagua y Madera', 'placa-reconocimiento-tagua-madera', 'Placa conmemorativa premium en madera noble con apliques centrales de tagua tallada y grabada.', 'Ideal para reconocimientos corporativos, premios ecológicos y eventos institucionales de prestigio.', 'tagua_hero_bg.jpg', '["tagua_hero_bg.jpg"]', 'TAG-005', 18.00, 100, 'Productos de Tagua', 'Cotizar Placa', 19, 0, 1, 1),
(20, 9, 'Kit Ejecutivo Ecológico con Tagua', 'kit-ejecutivo-tagua', 'Set corporativo con estuche de madera, libreta de fibra reciclada, bolígrafo de bambú y llavero de tagua grabado.', 'Presentación lista para regalos de fin de año, bienvenida a nuevos colaboradores y regalos VIP para clientes.', 'tagua_hero_bg.jpg', '["tagua_hero_bg.jpg"]', 'TAG-006', 15.50, 150, 'Productos de Tagua', 'Cotizar Kit', 20, 0, 1, 1);

-- --------------------------------------------------------
-- Tabla: `carrusel`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `carrusel`;
CREATE TABLE `carrusel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `cta_text` varchar(80) DEFAULT 'Ver productos',
  `cta_url` varchar(255) DEFAULT 'productos.php',
  `order_val` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `carrusel` (`id`, `title`, `subtitle`, `image`, `cta_text`, `cta_url`, `order_val`, `is_active`) VALUES
(1, 'Identificación que proyecta el valor de tu organización', 'Carnets en PVC, credenciales y cintas porta credenciales con impresión de alta definición para colaboradores y eventos.', 'carnet_mockup.jpg', 'Ver catálogo', 'productos.php', 1, 1),
(2, 'Acreditaciones para eventos, ferias y congresos', 'Soluciones integrales de identificación para staff, asistentes y control de acceso con entrega puntual en todo el Ecuador.', 'carousel_2.jpg', 'Cotizar para evento', 'cotizacion.php', 2, 1),
(3, 'Cintas porta credenciales con tu identidad de marca', 'Sublimación full color en poliéster de alta densidad con accesorios y herrajes metálicos reforzados.', 'cintas_full_color.jpg', 'Personalizar cintas', 'productos.php?cat=cintas', 3, 1),
(4, 'Artículos corporativos grabados a láser', 'Termos, agendas, llaveros y piezas en tagua natural con grabado térmico inalterable.', 'termo_after.jpg', 'Explorar personalización', 'personalizacion.php', 4, 1);

-- --------------------------------------------------------
-- Tabla: `clientes`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `logo_url` varchar(255) NOT NULL,
  `order_val` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `clientes` (`id`, `name`, `logo_url`, `order_val`, `is_active`) VALUES
(1, 'Cliente 1', 'cliente1.png', 1, 1),
(2, 'Cliente 2', 'cliente2.png', 2, 1),
(3, 'Cliente 3', 'cliente3.png', 3, 1),
(4, 'Cliente 4', 'cliente4.png', 4, 1),
(5, 'Cliente 5', 'cliente5.png', 5, 1);

-- --------------------------------------------------------
-- Tabla: `secciones_home`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `secciones_home`;
CREATE TABLE `secciones_home` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `secciones_home` (`id`, `section_key`, `group_name`, `title`, `subtitle`, `image`, `btn_text`, `btn_link`, `order_val`, `is_active`) VALUES
(1, 'soluciones', NULL, 'Empresas', 'Carnets, cintas y accesorios para colaboradores, áreas internas y visitantes.', 'carnet_mockup.jpg', 'Cotizar para mi empresa', 'cotizacion.php', 1, 1),
(2, 'soluciones', NULL, 'Instituciones', 'Identificación para personal administrativo, equipos de apoyo, estudiantes o miembros.', 'carousel_5.jpg', 'Solicitar opciones', 'cotizacion.php', 2, 1),
(3, 'soluciones', NULL, 'Eventos', 'Credenciales, cintas y porta credenciales para staff, invitados y asistentes.', 'carousel_2.jpg', 'Cotizar para evento', 'cotizacion.php', 3, 1),
(4, 'soluciones', NULL, 'Equipos de trabajo', 'Soluciones prácticas para identificar cargos, áreas y personal operativo.', 'cintas_mockup.jpg', 'Ver productos', 'productos.php', 4, 1),
(5, 'catalogo_opciones', 'Cintas porta credenciales', 'Cintas full color', 'Sublimación en poliéster suave de alta resolución.', 'cintas_full_color.jpg', 'Cotizar', 'cotizacion.php?producto=cintas-full-color', 1, 1),
(6, 'catalogo_opciones', 'Cintas porta credenciales', 'Cintas a un color', 'Serigrafía de alta adherencia para logotipos sólidos y sobrios.', 'cintas_mockup.jpg', 'Cotizar', 'cotizacion.php?producto=cintas-un-color', 2, 1),
(7, 'catalogo_opciones', 'Cintas porta credenciales', 'Cintas sin impresión', 'Lanyards de tela de alta resistencia en colores corporativos básicos.', 'cintas_sin_impresion.jpg', 'Ver catálogo', 'productos.php?cat=cintas', 3, 1),
(8, 'catalogo_opciones', 'Credenciales y porta credenciales', 'Credenciales PVC', 'Laminado de alta durabilidad impreso a doble cara para colaboradores.', 'carnet_mockup.jpg', 'Cotizar', 'cotizacion.php?producto=credenciales-pvc', 4, 1),
(9, 'catalogo_opciones', 'Credenciales y porta credenciales', 'Credenciales para eventos', 'Tarjetas de gran formato para staff, prensa y asistentes.', 'carousel_2.jpg', 'Cotizar', 'cotizacion.php?producto=credenciales-eventos', 5, 1),
(10, 'catalogo_opciones', 'Credenciales y porta credenciales', 'Porta credenciales', 'Soportes rígidos o fundas de PVC flexible para proteger identificaciones.', 'fundas.jpg', 'Ver catálogo', 'productos.php?cat=porta-credenciales', 6, 1);

-- --------------------------------------------------------
-- Tabla: `tagua_content`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tagua_content`;
CREATE TABLE `tagua_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_key` varchar(100) NOT NULL UNIQUE,
  `content_value` longtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tagua_content` (`content_key`, `content_value`) VALUES
('hero_badge', 'Marfil Vegetal Ecuatoriano · Línea Ecológica Corporativa'),
('hero_title', 'Artesanía y Precisión en Tagua Personalizada'),
('hero_subtitle', 'Transformamos la semilla de marfil vegetal ecuatoriano en artículos de identificación, botones, accesorios y regalos corporativos mediante grabado y corte láser de alta definición.'),
('hero_image', 'tagua_hero_bg.jpg'),
('hero_btn_text', 'Explorar Catálogo de Tagua'),
('hero_btn_url', '#catalogo-tagua'),
('hero_btn_sec_text', 'Consultar con un Asesor'),
('stat_1_title', '100% Biodegradable'),
('stat_1_desc', 'Sustituto natural al plástico'),
('stat_2_title', 'Grabado Láser HD'),
('stat_2_desc', 'Marcado térmico inalterable'),
('stat_3_title', 'Origen Ecuatoriano'),
('stat_3_desc', 'Cosecha silvestre seleccionada'),
('stat_4_title', 'Pedidos por Mayor'),
('stat_4_desc', 'Empresas, eventos y marcas'),
('esg_badge', 'Sostenibilidad & Cumplimiento ESG'),
('esg_title', 'Propiedades y Ventajas del Marfil Vegetal'),
('esg_description', 'La Tagua (Phytelephas aequatorialis) es una semilla originaria de los bosques húmedos del Ecuador. Al madurar y secarse, alcanza una dureza y tonalidad aperlada equivalente al marfil animal, permitiendo obtener piezas orgánicas de alta elegancia sin impacto ecológico negativo.'),
('benefit_1_title', 'Cero Huella Plástica'),
('benefit_1_desc', 'Ideal para organizaciones con directrices ambientales y programas de sostenibilidad que requieren merchandising 100% orgánico.'),
('benefit_2_title', 'Grabado Inalterable'),
('benefit_2_desc', 'La tecnología láser cauteriza la fibra interna de la semilla generando un contraste nítido que resiste fricción, humedad y paso del tiempo.'),
('benefit_3_title', 'Piezas Exclusivas'),
('benefit_3_desc', 'Cada botón, llavero o distintivo conserva las vetas y tonalidades botánicas de la semilla, haciendo que cada producto sea irrepetible.'),
('benefit_4_title', 'Producción Nacional'),
('benefit_4_desc', 'Elaborado con materia prima recolectada éticamente en Ecuador, apoyando el trabajo artesanal y la preservación de los bosques.'),
('catalog_badge', 'Catálogo Corporativo'),
('catalog_title', 'Línea de Productos en Tagua'),
('catalog_description', 'Modelos listos para personalizar con su logotipo institucional, numeración o nombres.'),
('custom_box_title', '¿Requiere medidas, siluetas o cortes especiales?'),
('custom_box_desc', 'Fabricamos piezas bajo plano vectorial con diámetros, grosores y perforaciones a la medida de su proyecto.'),
('custom_box_btn_text', 'Solicitar Fabricación a Medida'),
('process_badge', 'Control y Precisión'),
('process_title', 'Flujo de Producción y Grabado en Taller'),
('process_description', 'Protocolo riguroso desde la selección de la semilla hasta la entrega corporativa.'),
('step_1_title', 'Selección y Curado'),
('step_1_desc', 'Clasificación de semillas maduras con porcentaje de humedad controlado para evitar deformaciones mecánicas.'),
('step_2_title', 'Torneado y Calibración'),
('step_2_desc', 'Desbaste y pulido a espejo para generar una superficie plana milimétrica de alta adherencia óptica.'),
('step_3_title', 'Marcado Láser CNC'),
('step_3_desc', 'Aplicación del diseño vectorial mediante haz concentrado de alta resolución para líneas y textos finos.'),
('step_4_title', 'Inspección y Despacho'),
('step_4_desc', 'Control de calidad unidad por unidad, ensamblado de herrajes y empaque protegido para envío nacional.'),
('sectors_badge', 'Aplicaciones Comerciales'),
('sectors_title', 'Soluciones para Empresas e Instituciones'),
('sectors_description', 'Sectores que integran productos de Tagua en sus operaciones y eventos.'),
('sector_1_title', 'Cumbres y Congresos ESG'),
('sector_1_desc', 'Credenciales, medallas conmemorativas y souvenirs para eventos corporativos con compromiso ecológico.'),
('sector_2_title', 'Hotelería y Turismo VIP'),
('sector_2_desc', 'Llaveros de habitación con numeración permanente y detalles exclusivos para huéspedes internacionales.'),
('sector_3_title', 'Confección y Uniformes'),
('sector_3_desc', 'Botones personalizados con monograma grabado para camisas ejecutivas y prendas corporativas de alta gama.'),
('sector_4_title', 'Reconocimientos y Kits'),
('sector_4_desc', 'Placas conmemorativas y sets corporativos para premiaciones internas y regalos de fin de año.'),
('faq_1_q', '¿Qué es la tagua y por qué se denomina marfil vegetal?'),
('faq_1_a', 'La tagua es la semilla madura de la palma Phytelephas aequatorialis, nativa de los bosques húmedos del Ecuador. Una vez deshidratada, adquiere una dureza, color marfil y densidad similar a la del marfil animal, constituyendo una alternativa 100% ecológica, sostenible y legal.'),
('faq_2_q', '¿Es posible reproducir logotipos con tipografías y detalles finos?'),
('faq_2_a', 'Sí. Mediante sistemas de corte y grabado láser computarizado reproducimos vectores, isotipos y textos con precisión micrométrica. Previo a la fabricación enviamos una simulación digital para su revisión y aprobación técnica.'),
('faq_3_q', '¿El grabado láser en tagua es resistente al uso diario?'),
('faq_3_a', 'Totalmente. Al no tratarse de tintas superficiales sino de una cauterización térmica en la propia estructura botánica de la semilla, el grabado es indeleble y no se desprende con la fricción, el agua o la exposición al ambiente.'),
('faq_4_q', '¿Cuáles son los tiempos de entrega y cobertura de envíos?'),
('faq_4_a', 'Despachamos pedidos a Quito, Guayaquil, Cuenca y todas las provincias del Ecuador mediante Servientrega o transporte logístico especializado. Los tiempos de producción estándar oscilan entre 24 y 72 horas según el volumen requerido.'),
('cta_badge', 'Asesoría Corporativa Inmediata'),
('cta_title', 'Inicie su Cotización de Productos en Tagua'),
('cta_subtitle', 'Envíenos los requerimientos de su empresa junto con su logotipo. Le entregaremos una propuesta técnica y cotización formal a la brevedad.'),
('cta_btn_text', 'Iniciar Cotización Formal'),
('cta_btn_sec_text', 'Contactar Asesor por WhatsApp');

-- --------------------------------------------------------
-- Tabla: `configuraciones`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `configuraciones`;
CREATE TABLE `configuraciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_title` varchar(150) DEFAULT 'CardNet.ec | Identificación y accesorios para personal en Ecuador',
  `site_description` text DEFAULT 'Especialistas en carnets PVC, credenciales, cintas impresas y accesorios personalizados.',
  `whatsapp` varchar(50) DEFAULT '+593 99 999 9999',
  `phone_2` varchar(50) DEFAULT '',
  `phone_3` varchar(50) DEFAULT '',
  `email` varchar(100) DEFAULT 'info@cardnet.ec',
  `email_2` varchar(100) DEFAULT '',
  `address` varchar(255) DEFAULT 'Quito - Ecuador',
  `facebook` varchar(255) DEFAULT 'https://facebook.com/cardnetec',
  `instagram` varchar(255) DEFAULT 'https://instagram.com/cardnetec',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `configuraciones` (`id`, `site_title`, `site_description`, `whatsapp`, `phone_2`, `phone_3`, `email`, `email_2`, `address`, `facebook`, `instagram`) VALUES
(1, 'CardNet.ec | Identificación y accesorios para personal en Ecuador', 'Especialistas en carnets PVC, credenciales, cintas porta credenciales impresas y productos personalizados para empresas.', '+593 99 999 9999', '', '', 'info@cardnet.ec', '', 'Quito, Ecuador', 'https://facebook.com/cardnetec', 'https://instagram.com/cardnetec');

-- --------------------------------------------------------
-- Tabla: `solicitudes`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `solicitudes`;
CREATE TABLE `solicitudes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `company` varchar(150) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `items_json` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
