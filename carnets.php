<?php
session_start();
require_once 'db.php';
$c_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carnets Corporativos e Identificación Profesional | CardNet.ec</title>
    <meta name="description" content="Descubre por qué un buen carnet es vital para tu empresa. Explora combinaciones inteligentes de credenciales, porta carnets y lanyards personalizados.">
    <link rel="stylesheet" href="css/base.css?v=3.8">
    <link rel="stylesheet" href="css/layout.css?v=3.8">
    <link rel="stylesheet" href="css/components.css?v=3.8">
    <style>
        .carnets-hero {
            background: linear-gradient(135deg, #10140f 0%, #1c221a 100%);
            color: white;
            padding: 6rem 0;
            text-align: center;
            border-bottom: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }
        .carnets-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 50%, rgba(99, 174, 44, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .importance-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 2.25rem;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .importance-card:hover {
            transform: translateY(-6px);
            border-color: var(--primary) !important;
            box-shadow: 0 15px 30px rgba(99, 174, 44, 0.08);
        }
        .badge-type-card {
            background: #1c1b1b;
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            color: white;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .badge-type-card:hover {
            transform: translateY(-6px);
            border-color: var(--primary) !important;
            box-shadow: 0 15px 30px rgba(99, 174, 44, 0.12);
        }
        .combo-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-sm);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .combo-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary) !important;
            box-shadow: 0 20px 40px rgba(99, 174, 44, 0.08);
        }
        .combo-tag {
            background: rgba(99, 174, 44, 0.1);
            color: var(--primary-hover);
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-block;
            margin-bottom: 12px;
        }
        .combo-item-link {
            color: var(--dark);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
            display: inline-block;
        }
        .combo-item-link:hover {
            color: var(--primary);
            text-decoration: underline;
        }
        .custom-feature-item {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }
        .custom-feature-item:hover {
            border-color: var(--primary) !important;
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(99, 174, 44, 0.06);
        }
    </style>
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Hero de Carnets -->
        <section class="carnets-hero">
            <div class="container" style="max-width: 900px; position: relative; z-index: 5;">
                <span style="color: var(--primary); font-weight: 700; font-size: 0.85rem; letter-spacing: 0.1em; text-transform: uppercase;">Guía de Identificación</span>
                <h1 style="font-family: var(--font-heading); font-size: 3.2rem; font-weight: 400; margin-top: 15px; margin-bottom: 20px; color: white;">Carnets Corporativos Profesionales</h1>
                <p style="font-size: 1.15rem; color: rgba(255, 255, 255, 0.85); line-height: 1.6; margin: 0 auto;">
                    La credencial de tu empresa no es solo un trozo de plástico; es la representación de tu marca, el pilar de tu seguridad física y el símbolo de pertenencia de tus colaboradores.
                </p>
            </div>
        </section>

        <!-- Sección 1: Importancia de un buen Carnet -->
        <section class="section-padding container">
            <div class="section-header center" style="margin-bottom: 4rem;">
                <span class="section-subtitle">Valor Institucional</span>
                <h2>¿Por qué es vital un carnet profesional en tu empresa?</h2>
                <p>Implementar credenciales de alta calidad aporta múltiples beneficios operativos y de identidad en el entorno laboral.</p>
            </div>

            <div class="grid-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px;">
                <!-- Seguridad -->
                <div class="importance-card">
                    <div style="color: var(--primary); margin-bottom: 1.25rem;">
                        <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.2rem; font-family: var(--font-heading); margin-bottom: 0.75rem; color: var(--dark); font-weight: 500;">Seguridad y Control</h3>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Permite al personal de seguridad y a los sistemas de acceso verificar al instante la identidad de colaboradores y visitas autorizadas.</p>
                </div>

                <!-- Imagen Corporativa -->
                <div class="importance-card">
                    <div style="color: var(--primary); margin-bottom: 1.25rem;">
                        <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.2rem; font-family: var(--font-heading); margin-bottom: 0.75rem; color: var(--dark); font-weight: 500;">Imagen de Marca</h3>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Un carnet premium con impresión nítida transmite seriedad, orden y profesionalismo ante clientes externos, proveedores y visitas.</p>
                </div>

                <!-- Sentido de Pertenencia -->
                <div class="importance-card">
                    <div style="color: var(--primary); margin-bottom: 1.25rem;">
                        <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.2rem; font-family: var(--font-heading); margin-bottom: 0.75rem; color: var(--dark); font-weight: 500;">Pertenencia y Orgullo</h3>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Portar la credencial de la empresa fomenta la cohesión del equipo y genera un fuerte orgullo de pertenecer a la organización.</p>
                </div>

                <!-- Organización -->
                <div class="importance-card">
                    <div style="color: var(--primary); margin-bottom: 1.25rem;">
                        <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.2rem; font-family: var(--font-heading); margin-bottom: 0.75rem; color: var(--dark); font-weight: 500;">Organización Interna</h3>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Facilita la distinción de cargos, áreas o roles (ej. directivos, staff, contratistas) mediante colores y diseños específicos.</p>
                </div>
            </div>
        </section>

        <!-- Sección 2: Tipos de Carnets -->
        <section style="background: #121212; color: white; padding: 5rem 0;">
            <div class="container">
                <div class="section-header" style="margin-bottom: 4rem; max-width: 700px;">
                    <span class="section-subtitle" style="color: var(--primary); border-color: var(--primary);">Formatos y Materiales</span>
                    <h2 style="color: white;">Nuestros tipos de carnets</h2>
                    <p style="color: rgba(255,255,255,0.7);">Preparamos credenciales en distintos formatos para adaptarnos a las necesidades de tu organización.</p>
                </div>

                <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                    <!-- PVC Corporativo -->
                    <div class="badge-type-card">
                        <div style="width: 100%; aspect-ratio: 1.6; overflow: hidden; background: #1c1b1b;">
                            <img src="uploads/carnet_mockup.jpg" style="width: 100%; height: 100%; object-fit: cover; filter: grayscale(20%);" alt="Carnets PVC Estándar">
                        </div>
                        <div style="padding: 2rem;">
                            <h3 style="font-size: 1.35rem; font-family: var(--font-heading); margin-bottom: 0.75rem; font-weight: 400;">Carnets PVC Estándar</h3>
                            <p style="font-size: 0.85rem; color: rgba(255,255,255,0.7); line-height: 1.5; margin: 0;">
                                Tarjetas plásticas rígidas de alta resistencia (espesor de 30 milésimas / CR80), impresas por ambas caras con laminación de larga vida útil. Ideales para el uso diario de colaboradores.
                            </p>
                        </div>
                    </div>

                    <!-- Credenciales Gran Formato -->
                    <div class="badge-type-card">
                        <div style="width: 100%; aspect-ratio: 1.6; overflow: hidden; background: #1c1b1b;">
                            <img src="uploads/carousel_2.jpg" style="width: 100%; height: 100%; object-fit: cover; filter: grayscale(20%);" alt="Credenciales para Eventos">
                        </div>
                        <div style="padding: 2rem;">
                            <h3 style="font-size: 1.35rem; font-family: var(--font-heading); margin-bottom: 0.75rem; font-weight: 400;">Gran Formato para Eventos</h3>
                            <p style="font-size: 0.85rem; color: rgba(255,255,255,0.7); line-height: 1.5; margin: 0;">
                                Credenciales de tamaño ampliado preparadas especialmente para congresos, staff de conciertos, ferias comerciales o acreditaciones temporales de prensa y control de acceso.
                            </p>
                        </div>
                    </div>

                    <!-- Tarjetas de Proximidad RFID -->
                    <div class="badge-type-card">
                        <div style="width: 100%; aspect-ratio: 1.6; overflow: hidden; background: #1c1b1b;">
                            <img src="uploads/carnet_mockup.jpg" style="width: 100%; height: 100%; object-fit: cover; filter: grayscale(20%); " alt="Tarjetas RFID / Proximidad">
                        </div>
                        <div style="padding: 2rem;">
                            <h3 style="font-size: 1.35rem; font-family: var(--font-heading); margin-bottom: 0.75rem; font-weight: 400;">Tecnología y Proximidad</h3>
                            <p style="font-size: 0.85rem; color: rgba(255,255,255,0.7); line-height: 1.5; margin: 0;">
                                Tarjetas PVC que integran chips inteligentes de proximidad (RFID, Mifare o UHF) para conectarse de manera inalámbrica con tus sistemas biométricos de asistencia o apertura de puertas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección 3: Acabados y Personalización -->
        <section class="section-padding container">
            <div class="section-header center" style="margin-bottom: 4rem;">
                <span class="section-subtitle">Detalles de Taller</span>
                <h2>Opciones de acabados y personalización</h2>
                <p>Añade características funcionales para hacer que cada credencial sea duradera y única.</p>
            </div>

            <div class="grid-2" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px;">
                <!-- Dúplex -->
                <div class="custom-feature-item">
                    <div style="color: var(--primary); font-size: 1.5rem; flex-shrink: 0; padding-top: 4px;">✓</div>
                    <div>
                        <h4 style="font-size: 1.1rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">Impresión Dúplex (Doble Cara)</h4>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Aprovecha el reverso del carnet para imprimir códigos de conducta, números de emergencia, firma del directivo o políticas internas.</p>
                    </div>
                </div>

                <!-- Acabado brillo/mate -->
                <div class="custom-feature-item">
                    <div style="color: var(--primary); font-size: 1.5rem; flex-shrink: 0; padding-top: 4px;">✓</div>
                    <div>
                        <h4 style="font-size: 1.1rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">Laminado de Protección Brillante o Mate</h4>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">El laminado protege la impresión contra el roce diario. El acabado brillante resalta la saturación de colores y el mate ofrece una estética ejecutiva.</p>
                    </div>
                </div>

                <!-- Perforación slot punch -->
                <div class="custom-feature-item">
                    <div style="color: var(--primary); font-size: 1.5rem; flex-shrink: 0; padding-top: 4px;">✓</div>
                    <div>
                        <h4 style="font-size: 1.1rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">Perforación de Ranura (Slot Punch)</h4>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Realizamos una perforación ovalada precisa para que puedas prender el carnet directamente a un yoyo retráctil o cinta, sin necesidad de funda.</p>
                    </div>
                </div>

                <!-- Códigos QR -->
                <div class="custom-feature-item">
                    <div style="color: var(--primary); font-size: 1.5rem; flex-shrink: 0; padding-top: 4px;">✓</div>
                    <div>
                        <h4 style="font-size: 1.1rem; font-weight: 600; color: var(--dark); margin-bottom: 5px;">Códigos QR y de Barras Dinámicos</h4>
                        <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Integramos elementos legibles por lector óptico para que puedas vincular el carnet al registro digital de tu base de datos.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección 4: Combinaciones Inteligentes (Combos Promocionales) -->
        <section id="combos" class="section-padding" style="background: var(--surface-light); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
            <div class="container">
                <div class="section-header center" style="margin-bottom: 4rem;">
                    <span class="section-subtitle">Promoción & Soluciones</span>
                    <h2>Combos y Combinaciones Inteligentes</h2>
                    <p>Facilita la compra corporativa de tu equipo combinando carnets con sus accesorios a juego.</p>
                </div>

                <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                    <!-- COMBO 1: Básico -->
                    <div class="combo-card">
                        <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: #fff; border-bottom: 1px solid var(--border);">
                            <img src="uploads/fundas.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Combo Básico">
                        </div>
                        <div style="padding: 2rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <span class="combo-tag">Combo Básico Económico</span>
                            <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--dark); margin-bottom: 10px; font-weight: 500;">Combo Identificación Básica</h3>
                            <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem;">La solución más económica para congresos de gran cantidad de asistentes o identificaciones rápidas.</p>
                            
                            <h4 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 10px; font-weight: 700;">Productos incluidos:</h4>
                            <ul style="padding-left: 18px; margin: 0 0 2rem 0; font-size: 0.85rem; line-height: 1.8; color: var(--dark);">
                                <li>1x <a href="producto.php?slug=credenciales-pvc" class="combo-item-link">Carnet PVC Estándar</a></li>
                                <li>1x <a href="producto.php?slug=porta-credenciales" class="combo-item-link">Funda de PVC flexible</a></li>
                                <li>1x <a href="producto.php?slug=cintas-sin-impresion" class="combo-item-link">Cinta lisa sin impresión</a></li>
                            </ul>
                            
                            <button class="btn btn-primary btn-add-combo" data-combo="basico" style="width: 100%; text-align: center; text-transform: none; font-weight: 600; padding: 12px 0; margin-top: auto;">
                                Agregar Combo a Cotización
                            </button>
                        </div>
                    </div>

                    <!-- COMBO 2: Ejecutivo -->
                    <div class="combo-card">
                        <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: #fff; border-bottom: 1px solid var(--border);">
                            <img src="uploads/yoyos.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Combo Ejecutivo">
                        </div>
                        <div style="padding: 2rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <span class="combo-tag" style="background: rgba(99, 174, 44, 0.15);">Combo Oficina Diario</span>
                            <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--dark); margin-bottom: 10px; font-weight: 500;">Combo Ejecutivo Diario</h3>
                            <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem;">Comodidad y accesibilidad total. Excelente para personal administrativo con accesos por tarjeta.</p>
                            
                            <h4 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 10px; font-weight: 700;">Productos incluidos:</h4>
                            <ul style="padding-left: 18px; margin: 0 0 2rem 0; font-size: 0.85rem; line-height: 1.8; color: var(--dark);">
                                <li>1x <a href="producto.php?slug=credenciales-pvc" class="combo-item-link">Carnet PVC Estándar</a></li>
                                <li>1x <a href="producto.php?slug=porta-carnets" class="combo-item-link">Porta Carnet Rígido</a></li>
                                <li>1x <a href="producto.php?slug=accesorios-identificacion" class="combo-item-link">Yoyo retráctil corporativo</a></li>
                            </ul>
                            
                            <button class="btn btn-primary btn-add-combo" data-combo="ejecutivo" style="width: 100%; text-align: center; text-transform: none; font-weight: 600; padding: 12px 0; margin-top: auto;">
                                Agregar Combo a Cotización
                            </button>
                        </div>
                    </div>

                    <!-- COMBO 3: Premium -->
                    <div class="combo-card">
                        <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: #fff; border-bottom: 1px solid var(--border);">
                            <img src="uploads/cintas_full_color.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Combo Premium">
                        </div>
                        <div style="padding: 2rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <span class="combo-tag" style="background: rgba(99,174,44,0.2); color: #3b6d13;">Combo Máximo Impacto</span>
                            <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--dark); margin-bottom: 10px; font-weight: 500;">Combo Presencia Premium</h3>
                            <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem;">La máxima expresión corporativa. Combina tu carnet con una cinta impresa full color con tu logotipo.</p>
                            
                            <h4 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 10px; font-weight: 700;">Productos incluidos:</h4>
                            <ul style="padding-left: 18px; margin: 0 0 2rem 0; font-size: 0.85rem; line-height: 1.8; color: var(--dark);">
                                <li>1x <a href="producto.php?slug=credenciales-pvc" class="combo-item-link">Carnet PVC Estándar</a></li>
                                <li>1x <a href="producto.php?slug=porta-carnets" class="combo-item-link">Porta Carnet Rígido Premium</a></li>
                                <li>1x <a href="producto.php?slug=cintas-full-color" class="combo-item-link">Cinta personalizada full color</a></li>
                            </ul>
                            
                            <button class="btn btn-primary btn-add-combo" data-combo="premium" style="width: 100%; text-align: center; text-transform: none; font-weight: 600; padding: 12px 0; margin-top: auto;">
                                Agregar Combo a Cotización
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts Modulares -->
    <script src="js/main.js?v=3.8"></script>
    <script src="js/animations.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Manejador interactivo para agregar combos al carrito (Add to Quote list)
            const comboData = {
                basico: [
                    { name: "Carnet PVC Estándar (Combo)", slug: "credenciales-pvc", qty: 100, price: 1.20, snapshot: "uploads/carnet_mockup.jpg" },
                    { name: "Funda de PVC flexible (Combo)", slug: "porta-credenciales", qty: 100, price: 0.40, snapshot: "uploads/fundas.jpg" },
                    { name: "Cinta lisa sin impresión (Combo)", slug: "cintas-sin-impresion", qty: 100, price: 0.80, snapshot: "uploads/cintas_mockup.jpg" }
                ],
                ejecutivo: [
                    { name: "Carnet PVC Estándar (Combo)", slug: "credenciales-pvc", qty: 100, price: 1.20, snapshot: "uploads/carnet_mockup.jpg" },
                    { name: "Porta Carnet Rígido (Combo)", slug: "porta-carnets", qty: 100, price: 0.50, snapshot: "uploads/llavero.png" },
                    { name: "Yoyo retráctil corporativo (Combo)", slug: "accesorios-identificacion", qty: 100, price: 0.60, snapshot: "uploads/yoyos.jpg" }
                ],
                premium: [
                    { name: "Carnet PVC Estándar (Combo)", slug: "credenciales-pvc", qty: 100, price: 1.20, snapshot: "uploads/carnet_mockup.jpg" },
                    { name: "Porta Carnet Rígido (Combo)", slug: "porta-carnets", qty: 100, price: 0.50, snapshot: "uploads/llavero.png" },
                    { name: "Cinta personalizada full color (Combo)", slug: "cintas-full-color", qty: 100, price: 1.80, snapshot: "uploads/cintas_full_color.jpg" }
                ]
            };

            const addComboBtns = document.querySelectorAll(".btn-add-combo");
            addComboBtns.forEach(btn => {
                btn.addEventListener("click", function() {
                    const comboKey = this.getAttribute("data-combo");
                    const items = comboData[comboKey];
                    
                    if (!items) return;

                    // Bloquear botón para indicar carga
                    const oldText = this.textContent;
                    this.textContent = "Agregando...";
                    this.disabled = true;

                    // Crear FormData para enviar a cart-action.php
                    const formData = new FormData();
                    formData.append("items", JSON.stringify(items));

                    fetch("cart-action.php?action=add_multiple", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.textContent = oldText;
                        this.disabled = false;

                        if (data.success) {
                            // Actualizar contadores globales en el header si existen
                            const badges = document.querySelectorAll(".cart-count");
                            badges.forEach(b => {
                                b.textContent = data.cart_count;
                                b.style.display = "flex";
                            });

                            // Mostrar notificación flotante
                            if (window.showNotification) {
                                window.showNotification("Combo agregado a tu lista de cotización");
                            }

                            // Abrir automáticamente el Drawer de Cotización
                            const cartIcon = document.querySelector(".cart-icon-btn");
                            if (cartIcon) {
                                cartIcon.click();
                            }
                        }
                    })
                    .catch(err => {
                        console.error("Error al agregar combo:", err);
                        this.textContent = oldText;
                        this.disabled = false;
                    });
                });
            });
        });
    </script>
</body>
</html>
