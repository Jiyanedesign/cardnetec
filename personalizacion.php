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
    <title>Maestría en Grabado Láser & Personalización de Autor | CardNet.ec</title>
    <meta name="description" content="El referente en personalización de precisión y grabado láser en Ecuador. Acabados indelebles en acero, cuero, madera, acrílico y tagua. Sin mínimos masivos.">
    <link rel="stylesheet" href="css/base.css?v=6.3">
    <link rel="stylesheet" href="css/layout.css?v=6.3">
    <link rel="stylesheet" href="css/components.css?v=6.3">
    <style>
        .personalizacion-hero {
            background: linear-gradient(135deg, #0d110b 0%, #151a12 100%);
            color: white;
            padding: 7rem 0;
            text-align: center;
            border-bottom: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }
        .personalizacion-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 50%, rgba(99, 174, 44, 0.12) 0%, transparent 70%);
            pointer-events: none;
        }
        .tech-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .tech-card:hover {
            transform: translateY(-6px);
            border-color: var(--primary) !important;
            box-shadow: 0 15px 30px rgba(99, 174, 44, 0.08);
        }
        .step-block {
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 2.25rem;
            transition: all 0.3s ease;
        }
        .step-block:hover {
            border-color: var(--primary) !important;
            box-shadow: 0 12px 28px rgba(99, 174, 44, 0.06);
        }
        @media (max-width: 767px) {
            .grid-2 {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Hero Section -->
        <section class="personalizacion-hero">
            <div class="container" style="max-width: 920px; position: relative; z-index: 5;">
                <span style="color: #9eff42; font-weight: 700; font-size: 0.85rem; letter-spacing: 0.12em; text-transform: uppercase;">El Estándar Más Alto de Ecuador</span>
                <h1 style="font-family: var(--font-heading); font-size: 3.2rem; font-weight: 400; margin-top: 15px; margin-bottom: 20px; color: white; line-height: 1.15;">Maestría en Grabado Láser & Personalización de Autor</h1>
                <p style="font-size: 1.15rem; color: rgba(255, 255, 255, 0.88); line-height: 1.6; margin: 0 auto;">
                    No somos una fábrica de maquila masiva descartable. Somos el taller donde cada pieza recibe calibración óptica microscópica, acabados permanentes y control de calidad individual.
                </p>
            </div>
        </section>

        <!-- Concepto de marcaje -->
        <section class="section-padding container">
            <div class="split-feature" style="display: flex; gap: 40px; align-items: center; flex-wrap: wrap;">
                <div class="split-content" style="flex: 1; min-width: 300px;">
                    <span class="section-subtitle">Filosofía de Taller</span>
                    <h2 style="font-family: var(--font-heading); font-size: 2.3rem; font-weight: 500; color: var(--dark); margin-bottom: 1.25rem;">¿Por qué tu marca no debe conformarse con la producción masiva genérica?</h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.25rem;">
                        Las fábricas masivas tradicionales te exigen comprar miles de unidades y aplican tintas baratas que se rayan o se borran en pocas semanas. El resultado son regalos que terminan en la basura y degradan la imagen de tu empresa.
                    </p>
                    <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem;">
                        En CardNet.ec cambiamos las reglas: grabamos piezas nobles con láser de fibra óptica y CO2 de alta gama. <strong>Sin mínimos forzados:</strong> atendemos desde piezas exclusivas y regalos directivos, hasta lotes corporativos selectos donde cada unidad se inspecciona a mano.
                    </p>
                    <a href="cotizacion.php" class="btn btn-primary" style="padding: 14px 30px; text-transform: none;">Cotizar mi proyecto de personalización</a>
                </div>
                <div class="split-visual" style="flex: 1; min-width: 300px;">
                    <div style="width: 100%; border-radius: 8px; overflow: hidden; border: 1px solid var(--border); background: var(--surface-light);">
                        <img src="uploads/termo_after.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Grabador Láser en Acción">
                    </div>
                </div>
            </div>
        </section>

        <!-- Técnicas en Detalle -->
        <section class="section-padding" style="background: var(--surface-light); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
            <div class="container">
                <div class="section-header center" style="margin-bottom: 4rem;">
                    <span class="section-subtitle">Técnicas Disponibles</span>
                    <h2>Nuestros métodos de marcaje premium</h2>
                    <p>Detalle de los procesos que utilizamos en taller para dar vida a tus productos de identidad corporativa.</p>
                </div>

                <div class="grid-2" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px;">
                    <!-- Técnica 1: Grabado Láser -->
                    <div class="tech-card">
                        <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: white; border-bottom: 1px solid var(--border);">
                            <img src="uploads/termo_after.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Grabado Láser de Termos">
                        </div>
                        <div style="padding: 2.25rem;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.3rem; color: var(--dark); margin-bottom: 10px; font-weight: 500;">Grabado Láser Fibra y CO2</h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.5rem;">
                                Erosiona milimétricamente la superficie del metal, bambú o vidrio para revelar el tono natural interno. Es un marcaje de por vida que no se borra ni se raya con el uso diario. Ideal para termos de acero y agendas de madera.
                            </p>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--primary);">
                                <span style="background: rgba(99,174,44,0.1); padding: 4px 10px; border-radius: 20px;">Acero</span>
                                <span style="background: rgba(99,174,44,0.1); padding: 4px 10px; border-radius: 20px;">Madera</span>
                                <span style="background: rgba(99,174,44,0.1); padding: 4px 10px; border-radius: 20px;">Vidrio</span>
                            </div>
                        </div>
                    </div>

                    <!-- Técnica 2: Sublimación Textil HD -->
                    <div class="tech-card">
                        <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: white; border-bottom: 1px solid var(--border);">
                            <img src="uploads/cintas_full_color.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Sublimación HD de Cintas">
                        </div>
                        <div style="padding: 2.25rem;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.3rem; color: var(--dark); margin-bottom: 10px; font-weight: 500;">Sublimación de Alta Definición</h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.5rem;">
                                Transferencia de tinta mediante calor directamente a las fibras textiles de poliéster. Los colores quedan integrados permanentemente al material, lo que permite diseños full color fotográficos y lavables.
                            </p>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--primary);">
                                <span style="background: rgba(99,174,44,0.1); padding: 4px 10px; border-radius: 20px;">Poliéster Suave</span>
                                <span style="background: rgba(99,174,44,0.1); padding: 4px 10px; border-radius: 20px;">Cintas</span>
                                <span style="background: rgba(99,174,44,0.1); padding: 4px 10px; border-radius: 20px;">Lanyards</span>
                            </div>
                        </div>
                    </div>

                    <!-- Técnica 3: Impresión Térmica Re-transferencia -->
                    <div class="tech-card">
                        <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: white; border-bottom: 1px solid var(--border);">
                            <img src="uploads/carnet_mockup.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Impresión de Carnets PVC">
                        </div>
                        <div style="padding: 2.25rem;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.3rem; color: var(--dark); margin-bottom: 10px; font-weight: 500;">Impresión por Re-transferencia</h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.5rem;">
                                Utilizado en credenciales PVC de alta gama. Imprime primero sobre un film transparente que luego se fusiona térmicamente a la tarjeta de PVC, logrando una impresión perfecta de borde a borde y protección extra.
                            </p>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--primary);">
                                <span style="background: rgba(99,174,44,0.1); padding: 4px 10px; border-radius: 20px;">Tarjetas PVC</span>
                                <span style="background: rgba(99,174,44,0.1); padding: 4px 10px; border-radius: 20px;">RFID</span>
                                <span style="background: rgba(99,174,44,0.1); padding: 4px 10px; border-radius: 20px;">Doble Cara</span>
                            </div>
                        </div>
                    </div>

                    <!-- Técnica 4: Resina Epóxica Drop -->
                    <div class="tech-card">
                        <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: white; border-bottom: 1px solid var(--border);">
                            <img src="uploads/yoyos.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Resina Drop en Yoyos">
                        </div>
                        <div style="padding: 2.25rem;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.3rem; color: var(--dark); margin-bottom: 10px; font-weight: 500;">Resina Epóxica Drop (Gota de Resina)</h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.5rem;">
                                Aplicamos una gota de poliuretano líquido transparente sobre la impresión del yoyo retráctil o pin, creando un domo tridimensional de alto brillo que magnifica el diseño y lo protege contra golpes y humedad.
                            </p>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--primary);">
                                <span style="background: rgba(99,174,44,0.1); padding: 4px 10px; border-radius: 20px;">Yoyos Retráctiles</span>
                                <span style="background: rgba(99,174,44,0.1); padding: 4px 10px; border-radius: 20px;">Resina 3D</span>
                                <span style="background: rgba(99,174,44,0.1); padding: 4px 10px; border-radius: 20px;">Pines</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- El Proceso de Validación Técnica -->
        <section class="section-padding container">
            <div class="section-header center" style="margin-bottom: 4rem;">
                <span class="section-subtitle">Metodología de Trabajo</span>
                <h2>Proceso de Validación y Trazabilidad</h2>
                <p>Nuestra forma estructurada de garantizar que el marcaje de tus productos cumpla tus expectativas.</p>
            </div>

            <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                <!-- Paso 1 -->
                <div class="step-block">
                    <span style="font-size: 2rem; font-weight: 700; color: var(--primary); display: block; margin-bottom: 10px;">01</span>
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 8px; font-weight: 500;">Revisión de Arte Vectorial</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Revisamos tu logotipo para verificar trazados, grosores mínimos de línea y tipografías para garantizar la viabilidad física del marcado.</p>
                </div>
                <!-- Paso 2 -->
                <div class="step-block">
                    <span style="font-size: 2rem; font-weight: 700; color: var(--primary); display: block; margin-bottom: 10px;">02</span>
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 8px; font-weight: 500;">Aprobación de Render Técnico</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Te compartimos una maqueta digital a escala que muestra el tamaño exacto del logotipo, su ubicación y los márgenes de seguridad en el producto.</p>
                </div>
                <!-- Paso 3 -->
                <div class="step-block">
                    <span style="font-size: 2rem; font-weight: 700; color: var(--primary); display: block; margin-bottom: 10px;">03</span>
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--dark); margin-bottom: 8px; font-weight: 500;">Calibración en Taller</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">Nuestros técnicos ajustan las variables en las grabadoras láser y sublimadoras según la densidad de la superficie para asegurar la fidelidad del marcaje.</p>
                </div>
            </div>
        </section>

        <!-- CTA Final -->
        <section class="section-padding" style="background: linear-gradient(135deg, #10140f 0%, #1c221a 100%); color: white; text-align: center; border-top: 1px solid var(--border);">
            <div class="container" style="max-width: 700px;">
                <h2 style="font-family: var(--font-heading); font-size: 2.2rem; color: white; margin-bottom: 15px; font-weight: 400;">¿Quieres ver cómo lucirá tu marca?</h2>
                <p style="color: rgba(255,255,255,0.8); font-size: 1rem; line-height: 1.6; margin-bottom: 2rem;">Contáctanos hoy mismo para que nuestros diseñadores preparen una simulación o render técnico de tus productos de identidad corporativa de forma gratuita.</p>
                <a href="cotizacion.php" class="btn btn-primary" style="padding: 14px 30px; font-weight: 600; text-transform: none;">Solicitar Maqueta de Prueba</a>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js?v=6.3"></script>
    <script src="js/animations.js"></script>
</body>
</html>
 