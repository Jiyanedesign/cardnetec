-- Esquema de Base de Datos para CardNet.ec (Hosting Compartido)
-- Importar este archivo en phpMyAdmin en Hostinger

CREATE TABLE IF NOT EXISTS `usuarios_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
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
  `category` varchar(100) NOT NULL,
  `image_main` varchar(255) DEFAULT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos Semilla Iniciales
INSERT INTO `usuarios_admin` (`name`, `email`, `password`) VALUES
('CardNet Admin', 'admin@cardnet.ec', '$2y$10$w6D9t.RylJd/9hZt15H17.a4k3.b1W819J02.s8F9V24Jd/GkX9r2'); -- Contraseña por defecto: CardNetSecure2026!

INSERT INTO `carrusel` (`title`, `subtitle`, `cta_text`, `cta_url`, `order_val`, `is_active`) VALUES
('Productos personalizados que hacen visible el valor de tu marca', 'Grabado láser, termos, agendas, placas y kits corporativos con acabados limpios y duraderos.', 'Ver productos destacados', '#destacados', 1, 1),
('Termos grabados para empresas', 'Acabado láser sobre acero, sobrio y resistente al uso diario.', 'Quiero algo similar', 'cotizacion.php', 2, 1),
('Kits corporativos con mejor presentación', 'Piezas seleccionadas para representar tu marca desde el primer contacto.', 'Cotizar una idea', 'cotizacion.php', 3, 1);

INSERT INTO `productos` (`name`, `slug`, `description_short`, `category`, `is_featured`, `allows_simulation`, `cta_text`, `order_val`) VALUES
('Termos grabados', 'termos-grabados', 'Termos de acero inoxidable grabados con acabado limpio, sobrio y resistente al uso diario.', 'Artículos personalizados', 1, 1, 'Quiero este acabado', 1),
('Agendas personalizadas', 'agendas-personalizadas', 'Libretas con cubiertas de tacto cuero listas para grabados de gran textura y sobriedad.', 'Artículos personalizados', 1, 1, 'Solicitar este producto', 2),
('Placas y reconocimientos', 'placas-reconocimientos', 'Placas conmemorativas de madera noble, vidrio pulido y acrílico con cortes limpios.', 'Reconocimientos', 1, 1, 'Cotizar una idea', 3),
('Kits corporativos', 'kits-corporativos', 'Cajas coordinadas conteniendo termos grabados, agendas y bolígrafos premium a juego.', 'Kits corporativos', 1, 0, 'Armar un kit', 4),
('Llaveros corporativos', 'llaveros-corporativos', 'Detalles en metal cepillado y cuero legítimo con marcajes permanentes y de gran durabilidad.', 'Artículos personalizados', 1, 1, 'Quiero algo similar', 5),
('Cajas personalizadas', 'cajas-personalizadas', 'Cajas de madera y cartón estructurado a medida para presentaciones o kits ejecutivos.', 'Artículos personalizados', 1, 0, 'Ver opciones', 6);

INSERT INTO `antes_despues` (`title`, `technique`, `material`, `order_val`) VALUES
('Termos de acero inoxidable', 'Grabado láser de fibra', 'Acero inoxidable', 1),
('Agendas ejecutivas', 'Bajo relieve', 'Cuero PU', 2),
('Cajas de madera corporativas', 'Grabado láser CO2', 'Madera Pino', 3);

INSERT INTO `materiales` (`name`, `description`) VALUES
('Acero inoxidable', 'Ideal para termos, botellas y piezas metálicas de uso diario.'),
('Madera', 'Acabado cálido para reconocimientos, cajas y regalos corporativos.'),
('Acrílico', 'Limpio, moderno y versátil para placas, señalética y detalles.'),
('Cuero / PU', 'Ideal para grabados en agendas, libretas y carpetas ejecutivas.');
