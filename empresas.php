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
    <title>Soluciones de Identificación Corporativa para Empresas | CardNet.ec</title>
    <meta name="description" content="Soluciones integrales de identificación para empresas y equipos. Carnets PVC, cintas sublimadas y accesorios de alta gama con garantía de marca.">
    <link rel="stylesheet" href="css/base.css?v=3.8">
    <link rel="stylesheet" href="css/layout.css?v=3.8">
    <link rel="stylesheet" href="css/components.css?v=3.8">
    <style>
        .empresas-hero {
            background: linear-gradient(135deg, #0d110b 0%, #151a12 100%);
            color: white;
            padding: 7rem 0;
            text-align: center;
            border-bottom: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }
        .empresas-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 50%, rgba(99, 174, 44, 0.12) 0%, transparent 70%);
            pointer-events: none;
        }
        .corp-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .corp-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary) !important;
            box-shadow: 0 20px 40px rgba(99, 174, 44, 0.08);
        }
        .stat-box {
            background: #161b12;
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            color: white;
            transition: all 0.3s ease;
        }
        .stat-box:hover {
            border-color: var(--primary) !important;
            box-shadow: 0 10px 25px rgba(99, 174, 44, 0.1);
        }
    </style>
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Hero Section -->
        <section class="empresas-hero">
            <div class="container" style="max-width: 900px; position: relative; z-index: 5;">
                <span style="color: var(--primary); font-weight: 700; font-size: 0.85rem; letter-spacing: 0.1em; text-transform: uppercase;">Aliado Estratégico</span>
                <h1 style="font-family: var(--font-heading); font-size: 3.2rem; font-weight: 400; margin-top: 15px; margin-bottom: 20px; color: white;">Identificación Corporativa de Alta Gama</h1>
                <p style="font-size: 1.15rem; color: rgba(255, 255, 255, 0.85); line-height: 1.6; margin: 0 auto;">
                    Ayudamos a empresas de todos los sectores a proyectar profesionalismo y resguardar su seguridad mediante credenciales premium, cordones personalizados y accesorios de identidad corporativa.
                </p>
            </div>
        </section>

        <!-- Sección de Estadísticas / Garantía de Socios -->
        <section class="section-padding container">
            <div class="grid-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px;">
                <div class="stat-box">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary); margin-bottom: 5px;">+10K</div>
                    <div style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; color: rgba(255,255,255,0.7); letter-spacing: 0.05em;">Carnets Entregados</div>
                </div>
                <div class="stat-box">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary); margin-bottom: 5px;">100%</div>
                    <div style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; color: rgba(255,255,255,0.7); letter-spacing: 0.05em;">Garantía de Color</div>
                </div>
                <div class="stat-box">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary); margin-bottom: 5px;">24h</div>
                    <div style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; color: rgba(255,255,255,0.7); letter-spacing: 0.05em;">Respuesta de Cotización</div>
                </div>
                <div class="stat-box">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary); margin-bottom: 5px;">Servicio</div>
                    <div style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; color: rgba(255,255,255,0.7); letter-spacing: 0.05em;">Nacional en Ecuador</div>
                </div>
            </div>
        </section>

        <!-- Soluciones Corporativas Detalladas -->
        <section class="section-padding" style="background: var(--surface-light); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
            <div class="container">
                <div class="section-header center" style="margin-bottom: 4rem;">
                    <span class="section-subtitle">Categorías de Servicio</span>
                    <h2>Soluciones a tu medida</h2>
                    <p>Diseñadas específicamente para cumplir las exigencias de durabilidad y estilo de tu marca.</p>
                </div>

                <div class="grid-2" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px;">
                    <!-- Tarjeta 1: Carnets PVC -->
                    <div class="corp-card">
                        <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: white; border-bottom: 1px solid var(--border);">
                            <img src="uploads/carnet_mockup.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Credenciales PVC">
                        </div>
                        <div style="padding: 2.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--dark); margin-bottom: 12px; font-weight: 500;">Credenciales de PVC Laminado</h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 2rem;">
                                Identificaciones rígidas para empleados en PVC de alta densidad con laminación protectora UV que evita el desgaste de los colores. Disponibles con chip de proximidad RFID o código de barras integrado.
                            </p>
                            <div style="margin-top: auto; display: flex; gap: 12px;">
                                <a href="carnets.php" class="btn btn-secondary" style="flex: 1; text-align: center; text-transform: none; font-size: 0.8rem; padding: 12px 0;">Saber más</a>
                                <a href="cotizacion.php?slug=credenciales-pvc" class="btn btn-primary" style="flex: 1; text-align: center; text-transform: none; font-size: 0.8rem; padding: 12px 0;">Cotizar Ahora</a>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 2: Cintas y Lanyards -->
                    <div class="corp-card">
                        <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: white; border-bottom: 1px solid var(--border);">
                            <img src="uploads/cintas_full_color.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Cintas Sublimadas">
                        </div>
                        <div style="padding: 2.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--dark); margin-bottom: 12px; font-weight: 500;">Cintas y Lanyards Corporativos</h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 2rem;">
                                Cordones porta credencial con sublimación de alta definición en polyester suave. Personalización full color a doble cara con broches de seguridad (antipánico) y terminaciones premium.
                            </p>
                            <div style="margin-top: auto; display: flex; gap: 12px;">
                                <a href="productos.php" class="btn btn-secondary" style="flex: 1; text-align: center; text-transform: none; font-size: 0.8rem; padding: 12px 0;">Ver catálogo</a>
                                <a href="cotizacion.php?slug=cintas-full-color" class="btn btn-primary" style="flex: 1; text-align: center; text-transform: none; font-size: 0.8rem; padding: 12px 0;">Cotizar Ahora</a>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 3: Accesorios -->
                    <div class="corp-card">
                        <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: white; border-bottom: 1px solid var(--border);">
                            <img src="uploads/yoyos.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Accesorios corporativos">
                        </div>
                        <div style="padding: 2.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--dark); margin-bottom: 12px; font-weight: 500;">Accesorios para Identificación</h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 2rem;">
                                Protege y porta tus carnets con yoyos retráctiles personalizados con resina drop (gel tridimensional), fundas protectoras plásticas flexibles o estuches rígidos de policarbonato de alta seguridad.
                            </p>
                            <div style="margin-top: auto; display: flex; gap: 12px;">
                                <a href="productos.php" class="btn btn-secondary" style="flex: 1; text-align: center; text-transform: none; font-size: 0.8rem; padding: 12px 0;">Ver catálogo</a>
                                <a href="cotizacion.php" class="btn btn-primary" style="flex: 1; text-align: center; text-transform: none; font-size: 0.8rem; padding: 12px 0;">Cotizar Ahora</a>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 4: Grabado y Corporativos -->
                    <div class="corp-card">
                        <div style="width: 100%; aspect-ratio: 1.8; overflow: hidden; background: white; border-bottom: 1px solid var(--border);">
                            <img src="uploads/carousel_2.jpg" style="width: 100%; height: 100%; object-fit: cover;" alt="Productos Corporativos">
                        </div>
                        <div style="padding: 2.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                            <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--dark); margin-bottom: 12px; font-weight: 500;">Productos Corporativos Premium</h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 2rem;">
                                Suministramos agendas de cuero, termos de acero inoxidable grabados a láser y llaveros de marca personalizados para campañas corporativas, congresos de alta presencia y bienvenida de personal.
                            </p>
                            <div style="margin-top: auto; display: flex; gap: 12px;">
                                <a href="personalizacion.php" class="btn btn-secondary" style="flex: 1; text-align: center; text-transform: none; font-size: 0.8rem; padding: 12px 0;">Ver Técnicas</a>
                                <a href="cotizacion.php" class="btn btn-primary" style="flex: 1; text-align: center; text-transform: none; font-size: 0.8rem; padding: 12px 0;">Cotizar Ahora</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Por qué elegirnos / Metodología Corporativa -->
        <section class="section-padding container">
            <div class="section-header center" style="margin-bottom: 4rem;">
                <span class="section-subtitle">Nuestra Diferencia</span>
                <h2>Ventajas de trabajar con CardNet.ec</h2>
                <p>Nos enfocamos en mantener estándares rigurosos para que tu experiencia de compra corporativa sea impecable.</p>
            </div>

            <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                <div style="background: white; border: 1px solid var(--border); padding: 2.5rem; border-radius: 8px;">
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 10px;">Enfoque en Fidelidad de Marca</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin: 0;">Respetamos los códigos de color Pantone de tu marca para que la impresión y el marcaje representen fielmente tu identidad corporativa.</p>
                </div>
                <div style="background: white; border: 1px solid var(--border); padding: 2.5rem; border-radius: 8px;">
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 10px;">Atención y Render sin Costo</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin: 0;">Preparamos muestras digitales y vistas previas de marcado técnico de forma gratuita para tu validación formal antes de procesar el lote.</p>
                </div>
                <div style="background: white; border: 1px solid var(--border); padding: 2.5rem; border-radius: 8px;">
                    <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 500; color: var(--dark); margin-bottom: 10px;">Facturación y Descuentos</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin: 0;">Ofrecemos facturación corporativa reglamentaria, esquemas de crédito según convenio y atractivos descuentos escalonados por volumen.</p>
                </div>
            </div>
        </section>

        <!-- CTA Corporativo -->
        <section class="section-padding" style="background: linear-gradient(135deg, #10140f 0%, #1c221a 100%); color: white; text-align: center; border-top: 1px solid var(--border);">
            <div class="container" style="max-width: 750px;">
                <span style="color: var(--primary); font-weight: 700; font-size: 0.75rem; letter-spacing: 0.1em; text-transform: uppercase;">Área de Ventas Corporativas</span>
                <h2 style="font-family: var(--font-heading); font-size: 2.4rem; color: white; margin-top: 15px; margin-bottom: 15px; font-weight: 400;">¿Listo para elevar la presencia de tu marca?</h2>
                <p style="color: rgba(255,255,255,0.8); font-size: 1rem; line-height: 1.6; margin-bottom: 2rem;">Envíanos los requerimientos de tu equipo (número de colaboradores, tipos de carnet o accesorios) y te responderemos con una cotización formal en menos de 24 horas laborables.</p>
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <a href="cotizacion.php" class="btn btn-primary" style="padding: 14px 30px; font-weight: 600; text-transform: none;">Iniciar Cotización Corporativa</a>
                    <a href="https://wa.me/593000000000" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="background: rgba(255,255,255,0.08); color: white; border: 1px solid rgba(255,255,255,0.15); padding: 14px 30px; font-weight: 600; text-transform: none;">Hablar con un Asesor</a>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js?v=3.8"></script>
    <script src="js/animations.js"></script>
</body>
</html>
