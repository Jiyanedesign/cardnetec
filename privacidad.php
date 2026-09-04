<?php
session_start();
require_once 'db.php';
$site_settings = getSiteSettings($pdo);
$page_title = 'Política de Privacidad y Protección de Datos | CardNet.ec';
$page_description = 'Conoce nuestra política de privacidad y protección de datos conforme a la LOPDP de Ecuador. Transparencia, confidencialidad de marcas y seguridad de la información en CardNet.ec.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Favicon Oficial -->
    <link rel="icon" type="image/png" href="favicon.png?v=2.0">
    <link rel="shortcut icon" href="favicon.ico?v=2.0">
    <link rel="apple-touch-icon" href="favicon.png?v=2.0">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_description; ?>">
    <link rel="canonical" href="https://cardnetec.com.ec/privacidad.php">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://cardnetec.com.ec/privacidad.php">
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $page_description; ?>">
    <meta property="og:image" content="https://cardnetec.com.ec/images/og-image.jpg">

    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=6.3">
    <link rel="stylesheet" href="css/layout.css?v=6.3">
    <link rel="stylesheet" href="css/components.css?v=6.3">
    <link rel="stylesheet" href="css/pages.css?v=6.3">
    <link rel="stylesheet" href="css/animations.css?v=1.1.3">

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .legal-hero {
            background: linear-gradient(135deg, #0f140e 0%, #171f15 100%);
            color: #ffffff;
            padding: 4.5rem 0 3.5rem;
            border-bottom: 1px solid var(--border);
            position: relative;
        }
        .legal-hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(99, 174, 44, 0.4), transparent);
        }
        .legal-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(99, 174, 44, 0.15);
            color: #8be64d;
            border: 1px solid rgba(99, 174, 44, 0.3);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 5px 12px;
            border-radius: 999px;
            margin-bottom: 1rem;
        }
        .legal-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 3rem;
            align-items: start;
        }
        @media (max-width: 900px) {
            .legal-layout {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }
        .legal-sidebar {
            position: sticky;
            top: 90px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }
        .legal-sidebar-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border);
        }
        .legal-nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .legal-nav-link {
            display: block;
            font-size: 0.82rem;
            color: var(--text-muted);
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 6px;
            transition: all 0.2s ease;
            line-height: 1.4;
        }
        .legal-nav-link:hover {
            color: var(--primary);
            background: rgba(99, 174, 44, 0.06);
            transform: translateX(3px);
        }
        .legal-content-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            line-height: 1.75;
            color: #2b3327;
        }
        @media (max-width: 600px) {
            .legal-content-card {
                padding: 1.5rem;
            }
        }
        .legal-section {
            margin-bottom: 2.5rem;
            scroll-margin-top: 100px;
        }
        .legal-section:last-child {
            margin-bottom: 0;
        }
        .legal-section h2 {
            font-size: 1.25rem;
            color: var(--dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 0.4rem;
            border-bottom: 1px solid #edf1eb;
        }
        .legal-section h2 .section-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: rgba(99, 174, 44, 0.12);
            color: var(--primary);
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 700;
        }
        .legal-section p {
            font-size: 0.92rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }
        .legal-section ul {
            padding-left: 1.25rem;
            margin-bottom: 1rem;
            font-size: 0.92rem;
            color: var(--text-muted);
        }
        .legal-section li {
            margin-bottom: 0.5rem;
        }
        .legal-highlight-box {
            background: #f7faf5;
            border-left: 4px solid var(--primary);
            padding: 1.25rem 1.5rem;
            border-radius: 0 8px 8px 0;
            margin: 1.25rem 0;
            font-size: 0.88rem;
            color: #2b3327;
        }
        .breadcrumb-nav {
            font-size: 0.8rem;
            margin-bottom: 1rem;
            color: rgba(255, 255, 255, 0.6);
        }
        .breadcrumb-nav a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.2s;
        }
        .breadcrumb-nav a:hover {
            color: #8be64d;
        }
    </style>
</head>
<body>

    <!-- Cabecera Modular -->
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <header class="legal-hero">
        <div class="container">
            <nav class="breadcrumb-nav" aria-label="Breadcrumb">
                <a href="index.php">Inicio</a> &gt; <span>Política de Privacidad</span>
            </nav>
            <span class="legal-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Marco LOPDP Ecuador
            </span>
            <h1 style="font-size: 2.2rem; margin-bottom: 0.6rem; color: #ffffff; font-family: var(--font-heading); font-weight: 600;">
                Política de Privacidad y Protección de Datos
            </h1>
            <p style="font-size: 0.95rem; color: rgba(255, 255, 255, 0.7); max-width: 720px; line-height: 1.6; margin: 0;">
                En CardNet.ec asumimos un compromiso inquebrantable con la protección, confidencialidad y tratamiento transparente de tus datos personales e identidad corporativa, en estricto cumplimiento de la Ley Orgánica de Protección de Datos Personales de la República del Ecuador.
            </p>
            <div style="font-size: 0.8rem; color: rgba(255, 255, 255, 0.5); margin-top: 1.2rem;">
                Última actualización: Septiembre de 2026 · Quito, Ecuador
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="section-padding" style="background: #fbfcfb;">
        <div class="container">
            <div class="legal-layout">
                
                <!-- Sidebar de Navegación Rápida -->
                <aside class="legal-sidebar" aria-label="Índice del documento">
                    <div class="legal-sidebar-title">Índice del Documento</div>
                    <ul class="legal-nav-list">
                        <li><a href="#responsable" class="legal-nav-link">1. Responsable del Tratamiento</a></li>
                        <li><a href="#marco-legal" class="legal-nav-link">2. Marco Legal Aplicable</a></li>
                        <li><a href="#datos-recopilados" class="legal-nav-link">3. Datos que Recopilamos</a></li>
                        <li><a href="#finalidades" class="legal-nav-link">4. Finalidades del Tratamiento</a></li>
                        <li><a href="#confidencialidad-logos" class="legal-nav-link">5. Confidencialidad de Logotipos</a></li>
                        <li><a href="#conservacion" class="legal-nav-link">6. Tiempo de Conservación</a></li>
                        <li><a href="#derechos-arco" class="legal-nav-link">7. Tus Derechos (ARCO)</a></li>
                        <li><a href="#seguridad" class="legal-nav-link">8. Medidas de Seguridad</a></li>
                        <li><a href="#transferencias" class="legal-nav-link">9. Destinatarios y Envíos</a></li>
                        <li><a href="#contacto-privacidad" class="legal-nav-link">10. Canal Oficial de Privacidad</a></li>
                    </ul>
                    <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border); font-size: 0.78rem; color: var(--text-muted);">
                        ¿Tienes dudas sobre tus datos?<br>
                        <a href="<?php echo formatWhatsAppUrl($site_settings['whatsapp'] ?? '', 'Hola CardNet, deseo realizar una consulta sobre privacidad de datos.'); ?>" target="_blank" rel="noopener noreferrer" style="color: var(--primary); font-weight: 600; text-decoration: none; display: inline-block; margin-top: 4px;">
                            Escríbenos por WhatsApp ➔
                        </a>
                    </div>
                </aside>

                <!-- Cuerpo Legal -->
                <article class="legal-content-card">
                    
                    <!-- 1. Responsable -->
                    <section id="responsable" class="legal-section">
                        <h2><span class="section-num">1</span> Responsable del Tratamiento de Datos</h2>
                        <p>
                            El responsable del tratamiento de los datos personales recabados a través del sitio web <strong>cardnetec.com.ec</strong> y sus canales comerciales directos es <strong>CardNet.ec</strong> (en adelante, "CardNet", "nosotros" o "el taller"), con sede y taller principal de producción ubicado en la ciudad de Quito, República del Ecuador.
                        </p>
                        <p>
                            Para cualquier requerimiento, consulta o ejercicio de derechos relativos a la privacidad de tu información personal, puedes contactar de forma directa a nuestro oficial de atención:
                        </p>
                        <ul>
                            <li><strong>Entidad:</strong> CardNet.ec — Taller de Precisión y Soluciones de Identificación</li>
                            <li><strong>Correo Electrónico:</strong> <a href="mailto:<?php echo htmlspecialchars($site_settings['email'] ?? 'correo@cardnet.ec'); ?>" style="color: var(--primary);"><?php echo htmlspecialchars($site_settings['email'] ?? 'correo@cardnet.ec'); ?></a></li>
                            <li><strong>Atención Telefónica / WhatsApp:</strong> <a href="<?php echo formatWhatsAppUrl($site_settings['whatsapp'] ?? ''); ?>" target="_blank" rel="noopener noreferrer" style="color: var(--primary);"><?php echo htmlspecialchars($site_settings['whatsapp'] ?? '+593 99 978 180'); ?></a></li>
                            <li><strong>Domicilio Físico:</strong> <?php echo htmlspecialchars($site_settings['address'] ?? 'Quito, Ecuador'); ?></li>
                        </ul>
                    </section>

                    <!-- 2. Marco Legal -->
                    <section id="marco-legal" class="legal-section">
                        <h2><span class="section-num">2</span> Marco Legal Aplicable (Ecuador)</h2>
                        <p>
                            Esta Política de Privacidad se rige estrictamente por la normativa vigente en la República del Ecuador, en especial por la <strong>Ley Orgánica de Protección de Datos Personales (LOPDP)</strong>, promulgada en el Quinto Suplemento del Registro Oficial No. 459 el 26 de mayo de 2021, su Reglamento de aplicación y las directrices emanadas por la Autoridad Nacional de Protección de Datos Personales.
                        </p>
                        <p>
                            Todo tratamiento de datos personales efectuado por CardNet se sustenta en principios de juridicidad, lealtad, transparencia, finalidad legítima, pertinencia y minimización, proporcionalidad, confidencialidad y seguridad.
                        </p>
                    </section>

                    <!-- 3. Datos Recopilados -->
                    <section id="datos-recopilados" class="legal-section">
                        <h2><span class="section-num">3</span> Datos Personales que Recopilamos</h2>
                        <p>
                            Recopilamos únicamente los datos indispensables y proporcionados voluntariamente por el usuario para posibilitar el diseño, cotización, manufactura y despacho de artículos de identificación y grabado técnico:
                        </p>
                        <ul>
                            <li><strong>Datos de Contacto y Representación:</strong> Nombres, apellidos, empresa o institución solicitante, cargo, dirección de correo electrónico corporativo o personal, y número de WhatsApp o teléfono celular.</li>
                            <li><strong>Datos Tributarios y de Facturación:</strong> Número de Cédula de Identidad, Registro Único de Contribuyentes (RUC), Razón Social y dirección fiscal requeridos por el Servicio de Rentas Internas (SRI) del Ecuador para la emisión de comprobantes de venta válidos.</li>
                            <li><strong>Datos de Entrega y Logística:</strong> Dirección física de despacho, referencias de ubicación, cantón, provincia y nombre de la persona autorizada a recibir el pedido.</li>
                            <li><strong>Material Gráfico y de Marca:</strong> Logotipos vectoriales o en imagen, tipografías, manuales de marca, nombres de colaboradores y fotografías facilitadas por el cliente para la impresión de carnets PVC, credenciales o marcado láser.</li>
                        </ul>
                    </section>

                    <!-- 4. Finalidades -->
                    <section id="finalidades" class="legal-section">
                        <h2><span class="section-num">4</span> Finalidades y Legitimación del Tratamiento</h2>
                        <p>Tus datos son procesados bajo bases jurídicas legítimas (ejecución de medidas precontractuales, cumplimiento contractual y obligaciones tributarias) para los siguientes fines exclusivos:</p>
                        <ul>
                            <li><strong>Emisión de Cotizaciones:</strong> Elaborar proformas detalladas y presupuestos ajustados a los insumos, acabados y volumen requerido por tu empresa.</li>
                            <li><strong>Simulación y Aprobación de Artes:</strong> Generar maquetas virtuales de pre-producción (renders de marcaje en carnets, cintas, madera o tagua) para tu revisión y visto bueno técnico.</li>
                            <li><strong>Producción y Marcaje:</strong> Programar la maquinaria de impresión digital y grabado láser con las especificaciones y datos exactos de cada colaborador o credencial.</li>
                            <li><strong>Despacho y Notificación:</strong> Gestionar el envío mediante operadores logísticos autorizados (Servientrega u operadores locales) y notificar el código de guía para rastreo.</li>
                            <li><strong>Facturación Electrónica:</strong> Emitir los comprobantes legales autorizados por el SRI.</li>
                        </ul>
                    </section>

                    <!-- 5. Confidencialidad de Logotipos -->
                    <section id="confidencialidad-logos" class="legal-section">
                        <h2><span class="section-num">5</span> Confidencialidad Absoluta de Logotipos e Identidad Corporativa</h2>
                        <div class="legal-highlight-box">
                            <strong>Garantía de Secreto Industrial y Protección de Marca:</strong><br>
                            CardNet comprende el incalculable valor de la propiedad intelectual de cada empresa. Los archivos gráficos, logotipos, emblemas, firmas y bases de datos de personal que nos remitas son tratados con estricta confidencialidad. Nunca comercializamos, alquilamos ni cedemos tus vectores o artes a terceros, y solo son operados por los técnicos responsables de la producción asignada.
                        </div>
                        <p>
                            Las muestras fotográficas publicadas en nuestro catálogo o portafolio corresponden a trabajos previamente autorizados por nuestros clientes o maquetas conceptuales ilustrativas elaboradas para demostración de acabados.
                        </p>
                    </section>

                    <!-- 6. Conservación -->
                    <section id="conservacion" class="legal-section">
                        <h2><span class="section-num">6</span> Plazo de Conservación de los Datos</h2>
                        <p>
                            Los datos de facturación se conservan durante el plazo de 7 años exigido por la normativa tributaria ecuatoriana (Código Tributario y disposiciones del SRI).
                        </p>
                        <p>
                            Los archivos de arte, vectores y bases de datos de personal para credenciales se archivan de forma segura en servidores locales cifrados con el único propósito de agilizar futuras reimpresiones o reposiciones de carnets solicitadas expresamente por tu institución. Si deseas la eliminación inmediata de dichos archivos tras la culminación de un pedido, puedes requerirlo por escrito en cualquier momento.
                        </p>
                    </section>

                    <!-- 7. Derechos ARCO -->
                    <section id="derechos-arco" class="legal-section">
                        <h2><span class="section-num">7</span> Ejercicio de tus Derechos (Acceso, Rectificación, Cancelación y Oposición)</h2>
                        <p>
                            Conforme al Capítulo III de la LOPDP, tú eres titular indiscutible de tus datos personales y puedes ejercer los siguientes derechos sin costo alguno:
                        </p>
                        <ul>
                            <li><strong>Acceso e Información:</strong> Conocer qué datos tuyos constan en nuestros sistemas y con qué fin son procesados.</li>
                            <li><strong>Rectificación y Actualización:</strong> Modificar datos incompletos, inexactos o desactualizados.</li>
                            <li><strong>Eliminación / Cancelación:</strong> Solicitar la supresión de tus datos cuando ya no sean necesarios para los fines que motivaron su recolección o cuando haya fenecido la relación comercial, salvo impedimento legal.</li>
                            <li><strong>Oposición:</strong> Oponerte al tratamiento de tus datos para fines secundarios o comunicaciones informativas.</li>
                            <li><strong>Portabilidad:</strong> Recibir tus datos en un formato estructurado y de uso común.</li>
                        </ul>
                        <p>
                            Para ejercer cualquiera de estos derechos, envía un correo a <strong><?php echo htmlspecialchars($site_settings['email'] ?? 'correo@cardnet.ec'); ?></strong> adjuntando copia legible de tu Cédula o RUC e indicando claramente el derecho que deseas ejercer. Daremos respuesta en un plazo no mayor a 15 días laborables.
                        </p>
                    </section>

                    <!-- 8. Seguridad -->
                    <section id="seguridad" class="legal-section">
                        <h2><span class="section-num">8</span> Medidas Técnicas y Organizativas de Seguridad</h2>
                        <p>
                            CardNet implementa rigurosos estándares para preservar la integridad, confidencialidad y disponibilidad de la información:
                        </p>
                        <ul>
                            <li>Transmisión web cifrada mediante certificados SSL/TLS con encriptación moderna.</li>
                            <li>Aislamiento de bases de datos y acceso restringido exclusivamente al personal técnico autorizado bajo cláusulas de confidencialidad.</li>
                            <li>Copias de respaldo periódicas contra contingencias y protocolos de verificación de integridad.</li>
                        </ul>
                    </section>

                    <!-- 9. Destinatarios -->
                    <section id="transferencias" class="legal-section">
                        <h2><span class="section-num">9</span> Destinatarios, Envíos y Terceros Proveedores</h2>
                        <p>
                            CardNet no realiza transferencias internacionales de datos ni vende bases de información. Únicamente comunicamos los datos estrictamente pertinentes a:
                        </p>
                        <ul>
                            <li><strong>Operadores de Logística Nacional:</strong> Servientrega u operadores de encomiendas de confianza con el único propósito de concretar la entrega física de tus paquetes en cualquier provincia del Ecuador.</li>
                            <li><strong>Autoridades Gubernamentales:</strong> Al Servicio de Rentas Internas (SRI) o autoridades judiciales en estricto cumplimiento de mandatos legales imperativos.</li>
                        </ul>
                    </section>

                    <!-- 10. Contacto -->
                    <section id="contacto-privacidad" class="legal-section">
                        <h2><span class="section-num">10</span> Canal de Contacto y Modificaciones</h2>
                        <p>
                            CardNet se reserva el derecho de actualizar esta Política para reflejar mejoras en nuestros sistemas de seguridad o cambios en la legislación ecuatoriana. Cualquier modificación sustancial será publicada en esta misma URL indicando la fecha de última actualización.
                        </p>
                        <p>
                            Si tienes cualquier consulta sobre esta política o requieres asistencia personalizada, contáctanos a través de:
                        </p>
                        <div style="margin-top: 1rem; display: flex; gap: 15px; flex-wrap: wrap;">
                            <a href="mailto:<?php echo htmlspecialchars($site_settings['email'] ?? 'correo@cardnet.ec'); ?>" class="btn btn-primary" style="padding: 10px 22px; font-size: 0.88rem;">
                                Enviar Correo Electrónico
                            </a>
                            <a href="<?php echo formatWhatsAppUrl($site_settings['whatsapp'] ?? '', 'Hola CardNet, deseo consultar sobre el tratamiento de datos personales.'); ?>" class="btn btn-secondary" target="_blank" rel="noopener noreferrer" style="padding: 10px 22px; font-size: 0.88rem;">
                                Consultar por WhatsApp
                            </a>
                        </div>
                    </section>

                </article>
            </div>
        </div>
    </main>

    <!-- Pie de Página Modular -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts Modulares -->
    <script src="js/main.js?v=6.3" defer></script>
    <script src="js/animations.js" defer></script>
</body>
</html>
