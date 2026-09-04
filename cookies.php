<?php
session_start();
require_once 'db.php';
$site_settings = getSiteSettings($pdo);
$page_title = 'Aviso y Gestión de Cookies | CardNet.ec';
$page_description = 'Conoce nuestra política de cookies y gestiona tus preferencias en CardNet.ec. Uso transparente de cookies técnicas y de rendimiento para una experiencia óptima de cotización.';
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
    <link rel="canonical" href="https://cardnetec.com.ec/cookies.php">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://cardnetec.com.ec/cookies.php">
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

        /* Panel de Control Interactivo de Cookies */
        .cookie-manager-card {
            background: #f7faf5;
            border: 1px solid rgba(99, 174, 44, 0.25);
            border-radius: 10px;
            padding: 1.75rem;
            margin-bottom: 2.5rem;
        }
        .cookie-manager-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e1e8dc;
        }
        .cookie-manager-header h3 {
            margin: 0;
            font-size: 1.15rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .cookie-option-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            padding: 1.25rem 0;
            border-bottom: 1px solid #e9ede5;
        }
        .cookie-option-row:last-of-type {
            border-bottom: none;
        }
        .cookie-option-info h4 {
            margin: 0 0 4px 0;
            font-size: 0.98rem;
            color: var(--dark);
        }
        .cookie-option-info p {
            margin: 0;
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
        }
        
        /* Switch estilizado */
        .cookie-switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 26px;
            flex-shrink: 0;
            margin-top: 4px;
        }
        .cookie-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .cookie-slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: #cbd5e1;
            transition: .3s;
            border-radius: 26px;
        }
        .cookie-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        input:checked + .cookie-slider {
            background-color: var(--primary);
        }
        input:checked + .cookie-slider:before {
            transform: translateX(22px);
        }
        input:disabled + .cookie-slider {
            opacity: 0.6;
            cursor: not-allowed;
            background-color: var(--primary);
        }

        .cookie-actions {
            display: flex;
            gap: 12px;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        /* Tabla de Cookies */
        .cookie-table-wrap {
            overflow-x: auto;
            margin: 1.5rem 0;
        }
        .cookie-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            text-align: left;
        }
        .cookie-table th {
            background: #f0f4ee;
            padding: 10px 14px;
            color: var(--dark);
            font-weight: 600;
            border-bottom: 2px solid #dde5d9;
        }
        .cookie-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #edf1eb;
            color: #3b4437;
        }
        .cookie-table tr:nth-child(even) {
            background: #fafbfa;
        }

        /* Notificación Toast */
        #cookie-toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #192217;
            color: white;
            padding: 14px 22px;
            border-radius: 8px;
            font-size: 0.88rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 9999;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
            pointer-events: none;
            border-left: 4px solid var(--primary);
        }
        #cookie-toast.show {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
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
                <a href="index.php">Inicio</a> &gt; <span>Aviso y Gestión de Cookies</span>
            </nav>
            <span class="legal-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/><line x1="4.93" y1="4.93" x2="9.17" y2="9.17"/><line x1="14.83" y1="14.83" x2="19.07" y2="19.07"/><line x1="14.83" y1="9.17" x2="19.07" y2="4.93"/><line x1="14.83" y1="9.17" x2="18.36" y2="5.64"/><line x1="4.93" y1="19.07" x2="9.17" y2="14.83"/></svg>
                Transparencia & Privacidad
            </span>
            <h1 style="font-size: 2.2rem; margin-bottom: 0.6rem; color: #ffffff; font-family: var(--font-heading); font-weight: 600;">
                Aviso y Gestión de Cookies
            </h1>
            <p style="font-size: 0.95rem; color: rgba(255, 255, 255, 0.7); max-width: 720px; line-height: 1.6; margin: 0;">
                Explicamos con total claridad qué cookies utilizamos, para qué sirven y te facilitamos un panel interactivo para que configures tu nivel de privacidad en cualquier momento.
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
                <aside class="legal-sidebar" aria-label="Índice de cookies">
                    <div class="legal-sidebar-title">Navegación</div>
                    <ul class="legal-nav-list">
                        <li><a href="#panel-gestion" class="legal-nav-link" style="color: var(--primary); font-weight: 600;">⚙️ Panel de Gestión</a></li>
                        <li><a href="#que-son" class="legal-nav-link">1. ¿Qué son las Cookies?</a></li>
                        <li><a href="#para-que" class="legal-nav-link">2. ¿Por qué las usamos?</a></li>
                        <li><a href="#tipos" class="legal-nav-link">3. Tipos de Cookies</a></li>
                        <li><a href="#tabla-detalle" class="legal-nav-link">4. Detalle Técnico</a></li>
                        <li><a href="#desactivar" class="legal-nav-link">5. Control desde tu Navegador</a></li>
                        <li><a href="#actualizacion" class="legal-nav-link">6. Cambios en este Aviso</a></li>
                    </ul>
                    <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border); font-size: 0.78rem; color: var(--text-muted);">
                        Soporte técnico directo:<br>
                        <a href="<?php echo formatWhatsAppUrl($site_settings['whatsapp'] ?? '', 'Hola CardNet, tengo una duda sobre las cookies del sitio.'); ?>" target="_blank" rel="noopener noreferrer" style="color: var(--primary); font-weight: 600; text-decoration: none; display: inline-block; margin-top: 4px;">
                            Escríbenos por WhatsApp ➔
                        </a>
                    </div>
                </aside>

                <!-- Contenido Legal & Panel Interactivo -->
                <article class="legal-content-card">
                    
                    <!-- PANEL INTERACTIVO DE GESTIÓN -->
                    <section id="panel-gestion" class="cookie-manager-card">
                        <div class="cookie-manager-header">
                            <h3>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                                Centro de Preferencias y Gestión de Cookies
                            </h3>
                            <span style="font-size: 0.78rem; background: rgba(99, 174, 44, 0.15); color: var(--primary); padding: 4px 10px; border-radius: 4px; font-weight: 600;">
                                Ajuste en Tiempo Real
                            </span>
                        </div>
                        <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                            Puedes activar o desactivar las distintas categorías de cookies utilizadas en este sitio. Las cookies técnicas necesarias no pueden desactivarse ya que son imprescindibles para la cotización y funcionamiento del taller.
                        </p>

                        <!-- Opción 1: Técnicas -->
                        <div class="cookie-option-row">
                            <div class="cookie-option-info">
                                <h4>Cookies Técnicas & Esenciales <span style="font-size:0.75rem; color:var(--primary); font-weight:600; margin-left:6px;">(Siempre Activas)</span></h4>
                                <p>Permiten la navegación segura, la persistencia de productos en tu carrito de proforma durante la sesión y el funcionamiento del simulador digital.</p>
                            </div>
                            <label class="cookie-switch" title="Esta categoría es imprescindible para el funcionamiento del sitio web.">
                                <input type="checkbox" checked disabled>
                                <span class="cookie-slider"></span>
                            </label>
                        </div>

                        <!-- Opción 2: Analíticas -->
                        <div class="cookie-option-row">
                            <div class="cookie-option-info">
                                <h4>Cookies de Rendimiento y Análisis</h4>
                                <p>Nos ayudan a conocer el volumen de visitas y páginas más consultadas de forma totalmente anónima para optimizar la velocidad y servidores en Ecuador.</p>
                            </div>
                            <label class="cookie-switch" aria-label="Permitir cookies analíticas">
                                <input type="checkbox" id="toggle-analytics" checked>
                                <span class="cookie-slider"></span>
                            </label>
                        </div>

                        <!-- Opción 3: Personalización -->
                        <div class="cookie-option-row">
                            <div class="cookie-option-info">
                                <h4>Cookies de Preferencias y Personalización</h4>
                                <p>Recuerdan tus preferencias de interfaz, como filtros aplicados en el catálogo o el último material seleccionado en el simulador para tu comodidad.</p>
                            </div>
                            <label class="cookie-switch" aria-label="Permitir cookies de personalización">
                                <input type="checkbox" id="toggle-prefs" checked>
                                <span class="cookie-slider"></span>
                            </label>
                        </div>

                        <!-- Acciones -->
                        <div class="cookie-actions">
                            <button type="button" id="btn-save-cookie-prefs" class="btn btn-primary" style="padding: 10px 22px; font-size: 0.88rem;">
                                Guardar Mis Preferencias
                            </button>
                            <button type="button" id="btn-reset-cookie-prefs" class="btn btn-secondary" style="padding: 10px 20px; font-size: 0.88rem; background: white;">
                                Restablecer Todo
                            </button>
                        </div>
                    </section>

                    <!-- 1. ¿Qué son? -->
                    <section id="que-son" class="legal-section">
                        <h2><span class="section-num">1</span> ¿Qué son las Cookies?</h2>
                        <p>
                            Una cookie es un pequeño archivo de texto que un sitio web almacena en tu navegador web (computadora, smartphone o tablet) al acceder a determinadas páginas. Las cookies permiten a las plataformas en línea recordar tus acciones y preferencias (como inicio de sesión, productos añadidos a una cotización o idioma) para que no tengas que reconfigurarlos cada vez que navegas de una página a otra.
                        </p>
                    </section>

                    <!-- 2. ¿Para qué las usamos? -->
                    <section id="para-que" class="legal-section">
                        <h2><span class="section-num">2</span> ¿Para qué las utilizamos en CardNet.ec?</h2>
                        <p>
                            En <strong>CardNet.ec</strong> no comercializamos tus datos ni insertamos publicidad intrusiva de terceros. Nuestro uso de cookies y almacenamiento web responde estrictamente a propósitos operativos:
                        </p>
                        <ul>
                            <li><strong>Gestión de Cotizaciones en Tiempo Real:</strong> Mantener activos los artículos, cantidades y acabados que seleccionas para que puedas solicitar tu presupuesto vía WhatsApp sin perder tu selección.</li>
                            <li><strong>Simulador Interactivo:</strong> Guardar temporalmente las configuraciones de producto y renderizado visual elegidas en la herramienta de personalización.</li>
                            <li><strong>Seguridad y Rendimiento:</strong> Prevenir ataques automatizados (CSRF / bots) y monitorear la velocidad de carga de nuestras imágenes optimizadas en WebP.</li>
                        </ul>
                    </section>

                    <!-- 3. Tipos de Cookies -->
                    <section id="tipos" class="legal-section">
                        <h2><span class="section-num">3</span> Tipos de Cookies según su Finalidad</h2>
                        <ul>
                            <li><strong>Cookies Propias (de Primera Parte):</strong> Generadas directamente por los servidores de CardNet.ec para la sesión y navegación del taller.</li>
                            <li><strong>Cookies de Sesión:</strong> Se eliminan automáticamente al cerrar el navegador web.</li>
                            <li><strong>Cookies Persistentes:</strong> Permanecen en tu dispositivo durante un periodo determinado para recordar tus preferencias en futuras visitas.</li>
                        </ul>
                    </section>

                    <!-- 4. Tabla Técnica -->
                    <section id="tabla-detalle" class="legal-section">
                        <h2><span class="section-num">4</span> Inventario y Detalle de Cookies Utilizadas</h2>
                        <p>A continuación se detalla la relación de cookies técnicas empleadas en nuestro portal:</p>
                        
                        <div class="cookie-table-wrap">
                            <table class="cookie-table">
                                <thead>
                                    <tr>
                                        <th>Identificador</th>
                                        <th>Proveedor</th>
                                        <th>Propósito Técnico</th>
                                        <th>Tipo</th>
                                        <th>Caducidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>PHPSESSID</strong></td>
                                        <td>cardnetec.com.ec</td>
                                        <td>Mantiene la sesión de usuario y el carrito temporal de cotización mientras navegas.</td>
                                        <td>Esencial</td>
                                        <td>Sesión</td>
                                    </tr>
                                    <tr>
                                        <td><strong>cardnet_cookie_consent</strong></td>
                                        <td>cardnetec.com.ec (Local)</td>
                                        <td>Almacena tu consentimiento y configuración elegida en este panel de control.</td>
                                        <td>Preferencia</td>
                                        <td>1 Año</td>
                                    </tr>
                                    <tr>
                                        <td><strong>cardnet_quote_cache</strong></td>
                                        <td>cardnetec.com.ec (Local)</td>
                                        <td>Respalda los productos cotizados para evitar pérdida de datos si recargas la página.</td>
                                        <td>Funcional</td>
                                        <td>30 Días</td>
                                    </tr>
                                    <tr>
                                        <td><strong>_font_cache</strong></td>
                                        <td>Google Fonts</td>
                                        <td>Optimiza la carga y visualización de tipografías sin ralentizar el sitio.</td>
                                        <td>Rendimiento</td>
                                        <td>Permanente</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- 5. Desactivar en el Navegador -->
                    <section id="desactivar" class="legal-section">
                        <h2><span class="section-num">5</span> Cómo Administrar o Desactivar Cookies desde tu Navegador</h2>
                        <p>
                            Además de nuestro panel interactivo superior, puedes permitir, bloquear o suprimir las cookies instaladas en tu equipo mediante la configuración de las opciones de tu navegador de internet:
                        </p>
                        <ul>
                            <li><strong>Google Chrome:</strong> Configuración &gt; Privacidad y seguridad &gt; Cookies y otros datos de sitios.</li>
                            <li><strong>Mozilla Firefox:</strong> Opciones &gt; Privacidad &amp; Seguridad &gt; Cookies y datos del sitio.</li>
                            <li><strong>Apple Safari:</strong> Preferencias &gt; Privacidad &gt; Bloquear todas las cookies.</li>
                            <li><strong>Microsoft Edge:</strong> Configuración &gt; Permisos del sitio &gt; Cookies y datos almacenados.</li>
                        </ul>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">
                            <em>Nota: Si bloqueas completamente todas las cookies técnicas en tu navegador, es posible que funciones esenciales como agregar artículos al cotizador o visualizar el render del simulador experimenten limitaciones.</em>
                        </p>
                    </section>

                    <!-- 6. Actualizaciones -->
                    <section id="actualizacion" class="legal-section">
                        <h2><span class="section-num">6</span> Actualización del Aviso de Cookies</h2>
                        <p>
                            CardNet.ec podrá modificar el presente Aviso de Cookies en función de nuevas exigencias legislativas en el Ecuador o para adaptarlo a nuevas funcionalidades técnicas del sitio web. Te sugerimos revisar esta página periódicamente para mantenerte informado sobre cómo y para qué utilizamos las cookies.
                        </p>
                    </section>

                </article>
            </div>
        </div>
    </main>

    <!-- Toast de Notificación Visual -->
    <div id="cookie-toast" role="alert" aria-live="polite">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8be64d" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        <span id="cookie-toast-text">Tus preferencias de cookies han sido guardadas con éxito.</span>
    </div>

    <!-- Pie de Página Modular -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts Modulares -->
    <script src="js/main.js?v=7.0" defer></script>
    <script src="js/animations.js" defer></script>

    <!-- Script Interactivo de Gestión de Cookies -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleAnalytics = document.getElementById('toggle-analytics');
            const togglePrefs = document.getElementById('toggle-prefs');
            const btnSave = document.getElementById('btn-save-cookie-prefs');
            const btnReset = document.getElementById('btn-reset-cookie-prefs');
            const toast = document.getElementById('cookie-toast');
            const toastText = document.getElementById('cookie-toast-text');

            function showToast(msg) {
                toastText.textContent = msg;
                toast.classList.add('show');
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 3500);
            }

            // Cargar preferencias almacenadas
            try {
                const savedPrefs = localStorage.getItem('cardnet_cookie_preferences');
                if (savedPrefs) {
                    const parsed = JSON.parse(savedPrefs);
                    if (toggleAnalytics) toggleAnalytics.checked = parsed.analytics !== false;
                    if (togglePrefs) togglePrefs.checked = parsed.preferences !== false;
                }
            } catch(e) {}

            // Guardar preferencias
            if (btnSave) {
                btnSave.addEventListener('click', function() {
                    const prefs = {
                        technical: true, // Siempre obligatoria
                        analytics: toggleAnalytics ? toggleAnalytics.checked : true,
                        preferences: togglePrefs ? togglePrefs.checked : true,
                        savedAt: new Date().toISOString()
                    };
                    try {
                        localStorage.setItem('cardnet_cookie_preferences', JSON.stringify(prefs));
                        localStorage.setItem('cardnet_cookie_consent', 'true');
                    } catch(e) {}
                    showToast('Preferencias de cookies guardadas exitosamente.');
                });
            }

            // Restablecer valores
            if (btnReset) {
                btnReset.addEventListener('click', function() {
                    if (toggleAnalytics) toggleAnalytics.checked = true;
                    if (togglePrefs) togglePrefs.checked = true;
                    try {
                        localStorage.removeItem('cardnet_cookie_preferences');
                        localStorage.setItem('cardnet_cookie_consent', 'true');
                    } catch(e) {}
                    showToast('Valores restablecidos a la configuración predeterminada.');
                });
            }
        });
    </script>
</body>
</html>
