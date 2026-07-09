-- Esquema de Base de Datos para CardNet.ec (Hosting Compartido)
-- Importar este archivo en phpMyAdmin en Hostinger

CREATE TABLE IF NOT EXISTS `usuarios_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL UNIQUE,
  `order_val` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `carrusel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `cta_text` varchar(100) DEFAULT NULL,
  `cta_url` varchar(255) DEFAULT NULL,
  `order_val` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL UNIQUE,
  `description_short` text DEFAULT NULL,
  `description_long` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `image_main` varchar(255) DEFAULT NULL,
  `gallery_images` text DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `stock` int(11) DEFAULT 700,
  `price` decimal(10,2) DEFAULT 2.50,
  `volume_prices` text DEFAULT NULL,
  `materials_json` text DEFAULT NULL,
  `model_3d` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `allows_simulation` tinyint(1) DEFAULT 0,
  `cta_text` varchar(100) DEFAULT 'Quiero este acabado',
  `order_val` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `antes_despues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `image_before` varchar(255) DEFAULT NULL,
  `image_after` varchar(255) DEFAULT NULL,
  `technique` varchar(100) DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `order_val` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `materiales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `configuraciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `whatsapp` varchar(20) NOT NULL DEFAULT '593000000000',
  `email` varchar(100) NOT NULL DEFAULT 'correo@cardnet.ec',
  `address` varchar(255) NOT NULL DEFAULT 'Av. Amazonas, Quito, Ecuador',
  `instagram` varchar(150) DEFAULT NULL,
  `facebook` varchar(150) DEFAULT NULL,
  `site_title` varchar(150) NOT NULL DEFAULT 'CardNet.ec | Identificación y accesorios para personal',
  `site_description` varchar(255) NOT NULL DEFAULT 'Especialistas en identificación, carnets, credenciales y cintas porta credenciales en Ecuador.',
  `min_order` int(11) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `solicitudes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `simulation_path` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Nuevo',
  `internal_notes` text DEFAULT NULL,
  `products_json` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `logo_path` varchar(255) NOT NULL,
  `order_val` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos Semilla Iniciales
INSERT INTO `usuarios_admin` (`name`, `email`, `password`) VALUES
('CardNet Admin', 'admin@cardnet.ec', '$2y$10$w6D9t.RylJd/9hZt15H17.a4k3.b1W819J02.s8F9V24Jd/GkX9r2'); -- Contraseña: CardNetSecure2026!

INSERT INTO `configuraciones` (id, whatsapp, email, address, site_title, site_description, min_order) VALUES 
(1, '593000000000', 'correo@cardnet.ec', 'Av. Amazonas, Quito, Ecuador', 'CardNet.ec | Identificación y accesorios para personal', 'Especialistas en identificación, carnets, credenciales y cintas porta credenciales en Ecuador.', 1);

INSERT INTO `categorias` (`id`, `name`, `slug`, `order_val`, `is_active`) VALUES
(1, 'Carnets', 'carnets', 1, 1),
(2, 'Credenciales', 'credenciales', 2, 1),
(3, 'Cintas', 'cintas', 3, 1),
(4, 'Porta credenciales', 'porta-credenciales', 4, 1),
(5, 'Accesorios', 'accesorios', 5, 1),
(6, 'Tarjetas PVC', 'tarjetas-pvc', 6, 1),
(7, 'Personalización', 'personalizacion', 7, 1),
(8, 'Kits', 'kits', 8, 1),
(9, 'Placas', 'placas', 9, 1);

INSERT INTO `carrusel` (`title`, `subtitle`, `image`, `cta_text`, `cta_url`, `order_val`, `is_active`) VALUES
('Carnets PVC personalizados', 'Identificación profesional para empresas, instituciones, eventos y equipos.', 'carousel_1.jpg', 'Cotizar carnets', 'cotizacion.php', 1, 1),
('Credenciales para eventos y personal', 'Credenciales claras, funcionales y listas para identificar a tu equipo.', 'carousel_2.jpg', 'Ver credenciales', 'productos.php', 2, 1),
('Cintas porta credenciales', 'Cintas impresas full color, a un color o sin impresión para diferentes necesidades.', 'carousel_3.jpg', 'Ver opciones de cintas', 'productos.php', 3, 1),
('Porta credenciales y accesorios', 'Complementos prácticos para proteger y presentar mejor cada identificación.', 'carousel_4.jpg', 'Explorar accesorios', 'productos.php', 4, 1),
('Identificación para empresas e instituciones', 'Soluciones para equipos que necesitan verse organizados y profesionales.', 'carousel_5.jpg', 'Cotizar para mi empresa', 'cotizacion.php', 5, 1);

INSERT INTO `productos` (`name`, `slug`, `description_short`, `category_id`, `category`, `image_main`, `gallery_images`, `sku`, `price`, `is_featured`, `allows_simulation`, `is_active`, `order_val`, `cta_text`) VALUES
('Carnets PVC', 'credenciales-pvc', 'Identificación profesional e institucional impresa en PVC laminado de alta durabilidad con diseño personalizado.', 1, 'Carnets', 'carnets.png', '["carnet_detail.jpg"]', 'carnets-sku', 1.20, 1, 1, 1, 1, 'Cotizar carnets'),
('Credenciales corporativas', 'credenciales-corporativas', 'Credenciales de identificación personalizadas con acabado sobrio para colaboradores de empresas e instituciones.', 2, 'Credenciales', 'carnets.png', '[]', 'cred-corp-sku', 1.50, 1, 0, 1, 2, 'Ver credenciales'),
('Credenciales para eventos', 'credenciales-eventos', 'Credenciales claras y funcionales para staff, invitados, asistentes y control de acceso en ferias o congresos.', 2, 'Credenciales', 'carnets.png', '[]', 'cred-event-sku', 1.00, 1, 0, 1, 3, 'Ver credenciales'),
('Cintas porta credenciales full color', 'cintas-full-color', 'Cintas porta credenciales personalizadas con sublimación full color para mayor presencia de marca.', 3, 'Cintas', 'llavero.png', '[]', 'cintas-fc-sku', 1.80, 1, 0, 1, 4, 'Ver opciones de cintas'),
('Cintas a un color', 'cintas-un-color', 'Cintas porta credenciales de poliéster estampadas a un color con acabado limpio y sobrio.', 3, 'Cintas', 'llavero.png', '[]', 'cintas-uc-sku', 1.20, 1, 0, 1, 5, 'Ver opciones de cintas'),
('Cintas sin impresión', 'cintas-sin-impresion', 'Cintas porta credenciales lisas en colores institucionales básicos para uso diario o eventos.', 3, 'Cintas', 'llavero.png', '[]', 'cintas-si-sku', 0.80, 1, 0, 1, 6, 'Ver opciones de cintas'),
('Porta carnets', 'porta-carnets', 'Accesorios rígidos o flexibles transparentes para proteger y portar carnets de forma práctica.', 4, 'Porta credenciales', 'caja.png', '[]', 'porta-carnets-sku', 0.50, 1, 0, 1, 7, 'Explorar accesorios'),
('Porta credenciales', 'porta-credenciales', 'Fundas o estuches transparentes ideales para credenciales de eventos y acreditaciones de personal.', 4, 'Porta credenciales', 'caja.png', '[]', 'porta-cred-sku', 0.40, 1, 0, 1, 8, 'Explorar accesorios'),
('Tarjetas PVC', 'tarjetas-pvc', 'Tarjetas plásticas personalizadas para control de accesos, membresías, fidelización de clientes o identificación.', 6, 'Tarjetas PVC', 'carnets.png', '[]', 'tarjetas-pvc-sku', 1.10, 1, 0, 1, 9, 'Cotizar tarjetas'),
('Accesorios para identificación', 'accesorios-identificacion', 'Complementos de identificación diaria como yoyos retráctiles con resorte metálico, clips y lanyards.', 5, 'Accesorios', 'llavero.png', '[]', 'acc-id-sku', 0.60, 1, 0, 1, 10, 'Ver accesorios'),
('Agendas personalizadas', 'agendas-personalizadas', 'Agendas con cubiertas de tacto cuero (PU termosensible) listas para grabados de gran textura o bajo relieve.', 7, 'Personalización', 'agenda.png', '["agenda_before.jpg", "agenda_after.jpg"]', 'agendas-sku', 2.20, 1, 1, 1, 11, 'Quiero algo similar'),
('Llaveros corporativos', 'llaveros-corporativos', 'Llaveros de metal cepillado y cuero con grabado láser permanente de alta precisión.', 7, 'Personalización', 'llavero.png', '["llavero_detail.jpg"]', 'llaveros-sku', 0.90, 1, 1, 1, 12, 'Cotizar llaveros'),
('Termos grabados', 'termos-grabados', 'Termos de acero inoxidable con grabado láser de acabado limpio, sobrio y altamente duradero.', 7, 'Personalización', 'termo.png', '["termo_before.jpg", "termo_after.jpg"]', 'termos-grabados-sku', 1.80, 1, 1, 1, 13, 'Cotizar termos'),
('Cajas personalizadas', 'cajas-personalizadas', 'Cajas de madera o cartón estructurado a medida con grabado láser CO2 de alta calidad para presentaciones corporativas.', 7, 'Personalización', 'caja.png', '["caja_before.jpg", "caja_after.jpg"]', 'cajas-sku', 4.50, 1, 0, 1, 14, 'Ver opciones'),
('Kits empresariales', 'kits-corporativos', 'Cajas y empaques combinando termos grabados, agendas personalizadas y esferos a juego listos para entregar.', 8, 'Kits', 'kit.png', '["kit_detail.jpg"]', 'kits-sku', 12.50, 1, 0, 1, 15, 'Armar un kit'),
('Placas corporativas', 'placas-reconocimientos', 'Placas conmemorativas y de reconocimiento en acrílico, metal o madera con cortes y acabados limpios.', 9, 'Placas', 'placa.png', '["placa_detail.jpg"]', 'placas-sku', 15.00, 1, 1, 1, 16, 'Cotizar una idea');

INSERT INTO `antes_despues` (`title`, `technique`, `material`, `order_val`) VALUES
('Termos de acero inoxidable', 'Grabado láser de fibra', 'Acero inoxidable', 1),
('Agendas ejecutivas', 'Bajo relieve', 'Cuero PU', 2),
('Cajas de madera corporativas', 'Grabado láser CO2', 'Madera Pino', 3);

INSERT INTO `materiales` (`name`, `description`) VALUES
('Acero inoxidable', 'Ideal para termos, botellas y piezas metálicas de uso diario.'),
('Madera', 'Acabado cálido para reconocimientos, cajas y regalos corporativos.'),
('Acrílico', 'Limpio, moderno y versátil para placas, señalética y detalles.'),
('Cuero / PU', 'Ideal para grabados en agendas, libretas y carpetas ejecutivas.');

INSERT INTO `clientes` (id, name, logo_path, order_val, is_active) VALUES 
(1, 'APEX', 'uploads/cliente1.png', 1, 1),
(2, 'LUMINA', 'uploads/cliente2.png', 2, 1),
(3, 'VORTEX', 'uploads/cliente3.png', 3, 1),
(4, 'KRONA', 'uploads/cliente4.png', 4, 1),
(5, 'AERO', 'uploads/cliente5.png', 5, 1);
