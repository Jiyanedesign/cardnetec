<?php
session_start();
require_once 'db.php';
$site_settings = getSiteSettings($pdo);
$page_title = 'Términos y Políticas de Uso | CardNet.ec';
$page_description = 'Conoce los términos y condiciones de uso del taller CardNet.ec. Aprobación de artes, propiedad de logotipos, tolerancias de grabado láser, facturación y envíos a todo el Ecuador.';
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
    <link rel="canonical" href="https://cardnetec.com.ec/terminos.php">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://cardnetec.com.ec/terminos.php">
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $page_description; ?>">
    <meta property="og:image" content="https://cardnetec.com.ec/images/og-image.jpg">

    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=6.3">
    <link rel="stylesheet" href="css/layout.css?v=7.0">
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
                <a href="index.php">Inicio</a> &gt; <span>Términos y Políticas de Uso</span>
            </nav>
            <span class="legal-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Términos del Servicio
            </span>
            <h1 style="font-size: 2.2rem; margin-bottom: 0.6rem; color: #ffffff; font-family: var(--font-heading); font-weight: 600;">
                Términos y Políticas de Uso
            </h1>
            <p style="font-size: 0.95rem; color: rgba(255, 255, 255, 0.7); max-width: 720px; line-height: 1.6; margin: 0;">
                Condiciones que rigen el acceso a nuestro portal web, solicitudes de cotización, simulación digital, fabricación física de credenciales, artículos de tagua y servicios de grabado láser en Ecuador.
            </p>
            <div style="font-size: 0.8rem; color: rgba(255, 255, 255, 0.5); margin-top: 1.2rem;">
                Última actualización: Septiembre de 2026 · CardNet Ecuador
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="section-padding" style="background: #fbfcfb;">
        <div class="container">
            <div class="legal-layout">
                
                <!-- Sidebar de Navegación Rápida -->
                <aside class="legal-sidebar" aria-label="Índice de términos">
                    <div class="legal-sidebar-title">Índice de Cláusulas</div>
                    <ul class="legal-nav-list">
                        <li><a href="#aceptacion" class="legal-nav-link">1. Aceptación y Ámbito</a></li>
                        <li><a href="#taller" class="legal-nav-link">2. Servicios del Taller</a></li>
                        <li><a href="#cotizaciones" class="legal-nav-link">3. Cotizaciones y Precios</a></li>
                        <li><a href="#propiedad-intelectual" class="legal-nav-link">4. Propiedad de Logotipos</a></li>
                        <li><a href="#aprobacion-artes" class="legal-nav-link">5. Aprobación de Artes</a></li>
                        <li><a href="#tolerancias" class="legal-nav-link">6. Tolerancias de Fabricación</a></li>
                        <li><a href="#pagos-facturacion" class="legal-nav-link">7. Pagos y Facturación SRI</a></li>
                        <li><a href="#envios-logistica" class="legal-nav-link">8. Despachos y Envíos</a></li>
                        <li><a href="#garantia-devoluciones" class="legal-nav-link">9. Garantía y Reposición</a></li>
                        <li><a href="#jurisdiccion" class="legal-nav-link">10. Ley y Jurisdicción</a></li>
                    </ul>
                    <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border); font-size: 0.78rem; color: var(--text-muted);">
                        ¿Preguntas comerciales?<br>
                        <a href="<?php echo formatWhatsAppUrl($site_settings['whatsapp'] ?? '', 'Hola CardNet, deseo resolver dudas sobre los términos de servicio.'); ?>" target="_blank" rel="noopener noreferrer" style="color: var(--primary); font-weight: 600; text-decoration: none; display: inline-block; margin-top: 4px;">
                            Hablar con un Asesor ➔
                        </a>
                    </div>
                </aside>

                <!-- Contenido Legal -->
                <article class="legal-content-card">
                    
                    <!-- 1. Aceptación -->
                    <section id="aceptacion" class="legal-section">
                        <h2><span class="section-num">1</span> Aceptación de los Términos y Ámbito</h2>
                        <p>
                            El acceso, navegación y uso del portal web <strong>cardnetec.com.ec</strong>, así como la interacción con nuestro taller, cotizador en línea, simulador virtual y canales de atención por WhatsApp, implica la aceptación plena e incondicional de los presentes Términos y Políticas de Uso. Si el usuario no está de acuerdo con cualquiera de estas disposiciones, deberá abstenerse de utilizar la plataforma.
                        </p>
                    </section>

                    <!-- 2. Servicios del Taller -->
                    <section id="taller" class="legal-section">
                        <h2><span class="section-num">2</span> Naturaleza del Servicio y Taller Especializado</h2>
                        <p>
                            CardNet es un taller técnico ecuatoriano dedicado a la confección, impresión y grabado de artículos de identificación corporativa, institucional y promocional. Nuestros servicios abarcan:
                        </p>
                        <ul>
                            <li>Fabricación de carnets PVC laminados de alta fidelidad, credenciales institucionales, tarjetas de membresía y formatos de proximidad RFID.</li>
                            <li>Cintas y lanyards textiles sublimados a full color con accesorios y broches de seguridad.</li>
                            <li>Personalización en marfil vegetal de Tagua 100% natural ecuatoriana (llaveros, botones, medallas, broches y reconocimientos).</li>
                            <li>Marcado y grabado láser de alta precisión sobre acero inoxidable, aluminio, madera, cuero, corcho y acrílicos.</li>
                        </ul>
                    </section>

                    <!-- 3. Cotizaciones -->
                    <section id="cotizaciones" class="legal-section">
                        <h2><span class="section-num">3</span> Cotizaciones, Proformas y Lista de Precios</h2>
                        <p>
                            Los valores y presupuestos generados en la herramienta de cotización web constituyen <strong>proformas referenciales</strong> sujetas a confirmación técnica por parte de nuestro equipo comercial, tomando en consideración:
                        </p>
                        <ul>
                            <li>Revisión de la calidad del vector gráfico o resolución del logotipo provisto por el cliente.</li>
                            <li>Verificación de existencias de insumos, accesorios y acabados especiales requeridos.</li>
                            <li>Volumen final de producción solicitado.</li>
                        </ul>
                        <p>
                            Las proformas emitidas formalmente por nuestros asesores tienen una validez de <strong>15 días calendario</strong> desde su fecha de emisión. Todos los precios están cotizados en Dólares de los Estados Unidos de América (USD) y no incluyen el Impuesto al Valor Agregado (IVA) correspondiente a la legislación tributaria ecuatoriana, el cual se detallará en la proforma final.
                        </p>
                    </section>

                    <!-- 4. Propiedad Intelectual -->
                    <section id="propiedad-intelectual" class="legal-section">
                        <h2><span class="section-num">4</span> Propiedad Intelectual y Declaración de Marca del Cliente</h2>
                        <div class="legal-highlight-box">
                            <strong>Garantía de Legitimidad de Marca por parte del Cliente:</strong><br>
                            Al remitir logotipos, isotipos, marcas registradas, sellos institucionales o bases de datos de personal para su producción, el cliente declara bajo juramento ser el legítimo titular de los derechos de propiedad intelectual correspondientes, o contar con la debida autorización o licencia expresa de su titular. CardNet actúa estrictamente como operador técnico de manufactura y no asume responsabilidad civil, penal ni administrativa por infracciones marcaria o de derechos de autor causadas por el cliente.
                        </div>
                        <p>
                            Los contenidos, arquitectura web, código fuente, fotografías originales, simulador interactivo y logotipos propios de <strong>CardNet.ec</strong> constituyen propiedad intelectual protegida por la ley, quedando prohibida su reproducción o explotación sin autorización previa.
                        </p>
                    </section>

                    <!-- 5. Aprobación de Artes -->
                    <section id="aprobacion-artes" class="legal-section">
                        <h2><span class="section-num">5</span> Proceso Obligatorio de Aprobación de Artes y Maquetas</h2>
                        <p>
                            Para garantizar la total satisfacción y precisión en cada orden de trabajo:
                        </p>
                        <ul>
                            <li>Antes de ingresar un pedido a la mesa de grabado láser o a la línea de impresión, CardNet enviará al cliente una <strong>maqueta virtual (render digital)</strong> que ilustra la disposición, tamaño y posición del logotipo sobre el artículo seleccionado.</li>
                            <li>La producción iniciará <strong>únicamente tras la aprobación formal</strong> del cliente (expresada por WhatsApp o correo electrónico).</li>
                            <li>Una vez aprobada la maqueta por parte del cliente, no se aceptarán reclamos por errores ortográficos, omisiones de datos o tipografías que constaban en el diseño aprobado. Modificaciones posteriores solicitadas cuando el trabajo ya fue impreso o grabado generarán costos adicionales de reposición de material.</li>
                        </ul>
                    </section>

                    <!-- 6. Tolerancias -->
                    <section id="tolerancias" class="legal-section">
                        <h2><span class="section-num">6</span> Tolerancias de Fabricación y Materiales Orgánicos</h2>
                        <p>
                            Dado que en CardNet trabajamos tanto con polímeros técnicos como con elementos naturales nobles:
                        </p>
                        <ul>
                            <li><strong>Tagua y Madera Natural:</strong> Al tratarse de materia orgánica 100% natural, cada semilla de tagua y pieza de madera posee vetas, tonalidades de color y espesores particulares. El grabado láser interactúa de manera única con la densidad de cada fibra vegetal, lo cual es prueba de su autenticidad artesanal y no constituye defecto de fabricación.</li>
                            <li><strong>Colorimetría en PVC y Sublimación:</strong> Los tonos visualizados en pantallas digitales (perfil de color RGB) pueden presentar variaciones sutiles respecto a los pigmentos de impresión física (CMYK) o la termofijación sobre cintas de poliéster.</li>
                        </ul>
                    </section>

                    <!-- 7. Pagos y Facturación SRI -->
                    <section id="pagos-facturacion" class="legal-section">
                        <h2><span class="section-num">7</span> Modalidades de Pago y Facturación Electrónica</h2>
                        <p>
                            Las condiciones estándar de fabricación personalizada requieren:
                        </p>
                        <ul>
                            <li><strong>Anticipo del 50%:</strong> Para reserva de insumos, montaje de artes e inicio de la orden de producción en taller.</li>
                            <li><strong>Saldo del 50%:</strong> Contra entrega en taller (Quito) o previo al despacho para envíos interprovinciales.</li>
                            <li><strong>Medios de Pago:</strong> Transferencia bancaria directa a cuentas corporativas de CardNet en entidades financieras autorizadas de Ecuador, o pagos en efectivo en taller.</li>
                            <li><strong>Facturación SRI:</strong> Todo trabajo concluido es amparado con su respectiva factura electrónica autorizada por el Servicio de Rentas Internas (SRI), remitida al correo provisto por el cliente.</li>
                        </ul>
                    </section>

                    <!-- 8. Despachos y Envíos -->
                    <section id="envios-logistica" class="legal-section">
                        <h2><span class="section-num">8</span> Despachos, Envíos y Logística a Nivel Nacional</h2>
                        <p>
                            CardNet despacha pedidos con cobertura a todas las provincias y cantones del Ecuador:
                        </p>
                        <ul>
                            <li><strong>Cobertura Nacional:</strong> Despachamos a Pichincha, Guayas, Azuay, Manabí, Tungurahua, Loja, Imbabura, Chimborazo, El Oro, Santo Domingo y demás regiones del territorio ecuatoriano a través de <strong>Servientrega</strong> o cooperativas de transporte interprovincial de confianza.</li>
                            <li><strong>Tiempos de Entrega:</strong> Los tiempos de manufactura se coordinan según el volumen de la orden (habitualmente entre 24 y 72 horas laborables tras la aprobación del arte). El transporte habitualmente toma entre 24 y 48 horas adicionales dependiendo del cantón de destino.</li>
                            <li><strong>Seguimiento:</strong> CardNet proporcionará al cliente el número de guía de envío para el rastreo en línea de su encomienda.</li>
                        </ul>
                    </section>

                    <!-- 9. Garantía -->
                    <section id="garantia-devoluciones" class="legal-section">
                        <h2><span class="section-num">9</span> Garantía del Taller y Políticas de Reposición</h2>
                        <p>
                            En CardNet nos comprometemos con la excelencia técnica. Si el producto recibido presenta algún defecto de fábrica, desprendimiento de laminado o una discrepancia evidente respecto a la maqueta aprobada formalmente:
                        </p>
                        <ul>
                            <li>El cliente dispondrá de un plazo de <strong>5 días hábiles</strong> a partir de la recepción del paquete para notificar la novedad adjuntando fotografías o video demostrativo.</li>
                            <li>Si se verifica que el error fue atribuible al proceso de manufactura de nuestro taller, CardNet procederá a la <strong>reposición íntegra y prioritaria</strong> de las piezas defectuosas sin costo adicional alguno para el cliente.</li>
                            <li>Por tratarse de artículos personalizados y grabados con marcas o nombres particulares, no proceden cancelaciones ni devoluciones dinerarias una vez ejecutada la producción aprobada.</li>
                        </ul>
                    </section>

                    <!-- 10. Jurisdicción -->
                    <section id="jurisdiccion" class="legal-section">
                        <h2><span class="section-num">10</span> Legislación Aplicable y Resolución de Controversias</h2>
                        <p>
                            Los presentes Términos y Condiciones se interpretan y rigen de conformidad con las leyes de la República del Ecuador. Ante cualquier desacuerdo, disputa o controversia que derive de la interpretación o ejecución de estos términos, las partes procurarán resolverla en primer término mediante diálogo amigable y directo. En caso de no alcanzarse un acuerdo, las partes se someten voluntariamente a la jurisdicción y competencia de los jueces y tribunales de la ciudad de Quito, Distrito Metropolitano, renunciando a cualquier otro fuero.
                        </p>
                    </section>

                </article>
            </div>
        </div>
    </main>

    <!-- Pie de Página Modular -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts Modulares -->
    <script src="js/main.js?v=7.0" defer></script>
    <script src="js/animations.js" defer></script>
</body>
</html>
