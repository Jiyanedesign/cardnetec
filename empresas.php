<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kits y productos personalizados para empresas | CardNet.ec</title>
    <meta name="description" content="Kits corporativos, regalos personalizados, carnets y productos para empresas, eventos e instituciones.">
    <link rel="canonical" href="https://cardnet.ec/empresas.php">
    
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://cardnet.ec/empresas.php">
    <meta property="og:title" content="Soluciones Promocionales empresas para Empresas | CardNet.ec">
    <meta property="og:description" content="Centraliza tus compras de merchandising. Envíos programados, ejecutivos de cuenta y renders rápidos para marcas ecuatorianas.">
    <meta property="og:image" content="https://cardnet.ec/images/og-image.jpg">

    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=2.0">
    <link rel="stylesheet" href="css/layout.css?v=2.0">
    <link rel="stylesheet" href="css/components.css?v=2.0">
    <link rel="stylesheet" href="css/pages.css?v=2.0">
    <link rel="stylesheet" href="css/animations.css?v=1.1.2">

    <!-- Google Fonts: Marcellus (Títulos Elegantes) & Work Sans (Textos Limpios) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Barra de Anuncios Superior -->
    <div class="top-announcement-bar">
        Envío estándar gratis en pedidos corporativos mayores a $150 | Tiempos de entrega rápidos a todo el país
    </div>

    <!-- Cabecera de Página Multicapa -->
    <header class="main-header">
        <div class="container">
            <div class="header-middle">
                <a href="index.php" class="logo" aria-label="CardNet.ec Inicio">
                    <img src="images/logo.png?v=2.0" alt="CardNet.ec Logo" class="logo-img">
                </a>
                
                <div class="header-search">
                    <svg class="search-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input class="search-input" type="text" placeholder="Buscar termos, camisetas, libretas...">
                </div>

                <div class="header-contact-status">
                    <div class="contact-status-item">
                        <span class="status-icon-wrap">
                            <svg style="width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2.5;" viewBox="0 0 24 24">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </span>
                        <div class="status-text">
                            <h4>Asesoría Directa</h4>
                            <p>+593 00 000 0000</p>
                        </div>
                    </div>
                    <div class="contact-status-item">
                        <span class="status-icon-wrap">
                            <svg style="width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2.5;" viewBox="0 0 24 24">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </span>
                        <div class="status-text">
                            <h4>¿Dudas Comerciales?</h4>
                            <p><a href="#" class="status-link">Chatea ahora</a></p>
                        </div>
                    </div>
                </div>

                <button class="burger-menu" aria-label="Abrir menú de navegación" aria-expanded="false" aria-controls="mobile-nav">
                    <span class="burger-line"></span>
                    <span class="burger-line"></span>
                    <span class="burger-line"></span>
                </button>
            </div>
        </div>

        <div class="header-bottom">
            <div class="container nav-container">
                <nav class="nav-menu" aria-label="Navegación principal">
                    <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
                    <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Inicio</a>
                    <a href="productos.php" class="nav-link <?php echo ($current_page == 'productos.php' || $current_page == 'producto.php') ? 'active' : ''; ?>">Productos</a>
                    <a href="index.php#laser" class="nav-link">Grabado láser</a>
                    <a href="empresas.php" class="nav-link <?php echo ($current_page == 'empresas.php') ? 'active' : ''; ?>">Kits empresariales</a>
                    <a href="index.php#antes-despues" class="nav-link">Personalización</a>
                    <a href="index.php#proceso" class="nav-link">Cómo pedir</a>
                    <a href="cotizacion.php" class="nav-link <?php echo ($current_page == 'cotizacion.php') ? 'active' : ''; ?>">Cotizar<?php
                    $c_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                    if ($c_count > 0) {
                        echo '<span style="background: var(--primary); color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.7rem; font-weight: bold; margin-left: 3px;">' . $c_count . '</span>';
                    }
                    ?></a>
                </nav>
                <div class="header-bottom-actions">
                    <a href="cotizacion.php" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">SOLICITAR COTIZACIÓN</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Menú Móvil -->
    <div class="mobile-nav-overlay"></div>
    <nav id="mobile-nav" class="mobile-nav" aria-label="Navegación móvil">
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <a href="index.php" class="mobile-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Inicio</a>
        <a href="productos.php" class="mobile-link <?php echo ($current_page == 'productos.php' || $current_page == 'producto.php') ? 'active' : ''; ?>">Productos</a>
        <a href="index.php#laser" class="mobile-link">Grabado láser</a>
        <a href="empresas.php" class="mobile-link <?php echo ($current_page == 'empresas.php') ? 'active' : ''; ?>">Kits empresariales</a>
        <a href="index.php#antes-despues" class="mobile-link">Personalización</a>
        <a href="index.php#proceso" class="mobile-link">Cómo pedir</a>
        <a href="cotizacion.php" class="mobile-link <?php echo ($current_page == 'cotizacion.php') ? 'active' : ''; ?>">SOLICITAR COTIZACIÓN</a>
        <a href="cotizacion.php" class="btn btn-primary" style="margin-top: 1rem; width: 100%;">SOLICITAR COTIZACIÓN</a>
    </nav>

    <!-- Encabezado de Página Interna -->
    <div class="page-header-block">
        <div class="container">
            <h1 class="page-header-title">Kits y productos personalizados para empresas</h1>
            <p class="page-header-description">Preparamos termos, agendas, carnets, cajas y detalles corporativos listos para eventos, clientes y equipos.</p>
        </div>
    </div>
    </div>

    <!-- MAIN CONTENT -->
    <main>
        
        <!-- Grid de Soluciones Corporativas empresas -->
        <section class="section-padding container reveal-on-scroll">
            <div class="section-header center">
                <span class="section-subtitle">Servicios Especializados</span>
                <h2>Soluciones de taller listas para tus campañas</h2>
            </div>

            <div class="grid-2" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-top: 2rem;">
                
                <!-- Kits de Bienvenida -->
                <div class="solution-card" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease, border-color 0.25s ease;">
                    <div style="width: 100%; aspect-ratio: 16/10; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                        <img src="uploads/kit.png" alt="Welcome Kits" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;">
                    </div>
                    <div style="padding: 2rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.3rem; font-weight: 500; color: var(--dark); margin-bottom: 0.75rem;">Kits de Bienvenida (Welcome Kits)</h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.5rem; flex-grow: 1;">
                            Diseñamos y empaquetamos cajas de cartón kraft personalizadas con tu logotipo para el primer día de tus nuevos colaboradores (Onboarding).
                        </p>
                        <div class="solution-bullets" style="margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-muted);"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--primary);"></span><span>Incluye libreta, bolígrafo, termo y prenda textil.</span></div>
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-muted);"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--primary);"></span><span>Cajas rígidas personalizadas con el isotipo de la empresa.</span></div>
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-muted);"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--primary);"></span><span>Envíos individuales directos a oficinas o domicilios.</span></div>
                        </div>
                        <a href="cotizacion.php?servicio=onboarding" class="btn btn-secondary" style="width: 100%; text-align: center; margin-top: auto; padding: 12px 0;">Cotizar Kits</a>
                    </div>
                </div>

                <!-- Merchandising Ferias -->
                <div class="solution-card" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease, border-color 0.25s ease;">
                    <div style="width: 100%; aspect-ratio: 16/10; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                        <img src="uploads/llavero.png" alt="Merchandising Ferias" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;">
                    </div>
                    <div style="padding: 2rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.3rem; font-weight: 500; color: var(--dark); margin-bottom: 0.75rem;">Merchandising para Ferias y Eventos</h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.5rem; flex-grow: 1;">
                            Material publicitario masivo y selectivo de alta rotación para stands en ferias comerciales y congresos corporativos en Ecuador.
                        </p>
                        <div class="solution-bullets" style="margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-muted);"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--primary);"></span><span>Lanyards sublimados, esferos económicos y llaveros.</span></div>
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-muted);"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--primary);"></span><span>Bolsas ecológicas impresas a una tinta con acabado limpio.</span></div>
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-muted);"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--primary);"></span><span>Plazos de producción express para fechas de evento estrictas.</span></div>
                        </div>
                        <a href="cotizacion.php?servicio=ferias" class="btn btn-secondary" style="width: 100%; text-align: center; margin-top: auto; padding: 12px 0;">Cotizar Feria</a>
                    </div>
                </div>

                <!-- Regalos Ejecutivos -->
                <div class="solution-card" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease, border-color 0.25s ease;">
                    <div style="width: 100%; aspect-ratio: 16/10; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                        <img src="uploads/caja.png" alt="Regalos Ejecutivos" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;">
                    </div>
                    <div style="padding: 2rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.3rem; font-weight: 500; color: var(--dark); margin-bottom: 0.75rem;">Regalos Ejecutivos y de Fin de Año</h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.5rem; flex-grow: 1;">
                            Artículos de gama superior para fidelizar a tus clientes VIP, directores y socios estratégicos de negocios.
                        </p>
                        <div class="solution-bullets" style="margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-muted);"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--primary);"></span><span>Termos de acero inoxidable con pantalla de temperatura LED.</span></div>
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-muted);"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--primary);"></span><span>Libretas ejecutivas con bajo relieve en tapas.</span></div>
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-muted);"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--primary);"></span><span>Cajas de madera con grabado láser permanente.</span></div>
                        </div>
                        <a href="cotizacion.php?servicio=regalos" class="btn btn-secondary" style="width: 100%; text-align: center; margin-top: auto; padding: 12px 0;">Cotizar Regalos</a>
                    </div>
                </div>

                <!-- Uniformes y Uniformidad -->
                <div class="solution-card" style="background: white; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.25s ease, border-color 0.25s ease;">
                    <div style="width: 100%; aspect-ratio: 16/10; overflow: hidden; border-bottom: 1px solid var(--border); background: var(--surface-light);">
                        <img src="uploads/agenda.png" alt="Uniformes y Uniformidad" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;">
                    </div>
                    <div style="padding: 2rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <h3 style="font-family: var(--font-heading); font-size: 1.3rem; font-weight: 500; color: var(--dark); margin-bottom: 0.75rem;">Uniformes y Vestimenta de Trabajo</h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.5rem; flex-grow: 1;">
                            Protege la presencia visual de tu equipo de cara al público mediante camisas y chaquetas bordadas impecablemente.
                        </p>
                        <div class="solution-bullets" style="margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-muted);"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--primary);"></span><span>Camisetas polo de alta densidad y tacto suave de algodón.</span></div>
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-muted);"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--primary);"></span><span>Gorras de acrílico estructuradas bordadas en relieve.</span></div>
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-muted);"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--primary);"></span><span>Hilos lavables y de alta resistencia química.</span></div>
                        </div>
                        <a href="cotizacion.php?servicio=uniformes" class="btn btn-secondary" style="width: 100%; text-align: center; margin-top: auto; padding: 12px 0;">Cotizar Uniformes</a>
                    </div>
                </div>

            </div>
        </section>

        <!-- Beneficios Corporativos CardNet -->
        <section class="section-padding section-bg-light reveal-on-scroll" style="border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding-top: 5rem; padding-bottom: 5rem;">
            <div class="container">
                
                <style>
                    @media (max-width: 768px) {
                        .split-feature-custom {
                            grid-template-columns: 1fr !important;
                            gap: 40px !important;
                        }
                        .split-feature-custom .split-visual {
                            order: -1 !important; /* Pone la imagen arriba en móviles */
                        }
                    }
                </style>

                <div class="split-feature-custom" style="display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 60px; align-items: center;">
                    
                    <!-- Columna Izquierda (Texto) -->
                    <div class="split-content" style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <div>
                            <span class="section-subtitle" style="color: var(--primary); border-color: var(--primary); font-size: 0.75rem; letter-spacing: 0.1em; text-transform: uppercase; font-weight: 700; border: 1px solid var(--primary); padding: 4px 10px; border-radius: 4px; display: inline-block; margin-bottom: 1rem;">Beneficios para Empresas</span>
                            <h2 style="font-family: var(--font-heading); font-size: 2.2rem; font-weight: 500; color: var(--dark); line-height: 1.2; margin-bottom: 1rem;">Un socio logístico para tus compras anuales</h2>
                            <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.6; margin: 0;">
                                Da de alta tu cuenta corporativa para simplificar las tareas de marketing e inducción, logrando un flujo de entrega organizado y predecible.
                            </p>
                        </div>
                        
                        <div class="split-bullets" style="display: flex; flex-direction: column; gap: 1.25rem;">
                            
                            <div class="bullet-item" style="display: flex; align-items: flex-start; gap: 12px;">
                                <svg class="bullet-icon" viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: none; stroke: var(--primary); stroke-width: 3; flex-shrink: 0; margin-top: 3px;" aria-hidden="true">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span class="bullet-text" style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5; text-align: left;">
                                    <strong>Ejecutivo asignado:</strong> Comunicación directa para resolver renders de maquetas y cotizaciones rápidas.
                                </span>
                            </div>
                            
                            <div class="bullet-item" style="display: flex; align-items: flex-start; gap: 12px;">
                                <svg class="bullet-icon" viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: none; stroke: var(--primary); stroke-width: 3; flex-shrink: 0; margin-top: 3px;" aria-hidden="true">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span class="bullet-text" style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5; text-align: left;">
                                    <strong>Impresión bajo demanda (On-demand):</strong> Adquiere stock volumétrico a precio preferente, lo guardamos en nuestro taller y lo grabamos según lo vayas necesitando.
                                </span>
                            </div>
                            
                            <div class="bullet-item" style="display: flex; align-items: flex-start; gap: 12px;">
                                <svg class="bullet-icon" viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: none; stroke: var(--primary); stroke-width: 3; flex-shrink: 0; margin-top: 3px;" aria-hidden="true">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span class="bullet-text" style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5; text-align: left;">
                                    <strong>Facturación y crédito:</strong> Opciones de pago flexibles a 30 días posteriores al despacho de mercadería (sujeto a validación comercial).
                                </span>
                            </div>
                            
                        </div>
                        
                        <div style="margin-top: 0.5rem;">
                            <a href="contacto.php" class="btn btn-primary" style="padding: 14px 32px; font-weight: 600;">Solicitar cuenta corporativa</a>
                        </div>
                    </div>
                    
                    <!-- Columna Derecha (Visual) -->
                    <div class="split-visual" style="display: flex; justify-content: center; align-items: center;">
                        <div style="width: 100%; max-width: 440px; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.06); background: white;">
                            <img src="uploads/kit.png" alt="Socio Logístico - Welcome Kit" style="width: 100%; height: auto; display: block; object-fit: cover;">
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>

        <!-- Pie de Página -->
    <footer class="main-footer">
        <div class="container footer-top section-padding" style="padding-top: 3rem; padding-bottom: 3rem;">
            <div class="footer-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 40px;">
                <div class="footer-brand-column">
                    <a href="index.php" class="logo footer-logo" aria-label="CardNet.ec Inicio">
                        <img src="images/logo.png?v=2.0" alt="CardNet.ec Logo" class="logo-img">
                    </a>
                    <p class="footer-description" style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-top: 1rem;">Grabado láser, carnets y productos personalizados para empresas, instituciones y eventos.</p>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading" style="font-size: 0.9rem; font-family: var(--font-heading); margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dark);">Productos</h3>
                    <nav class="footer-links" aria-label="Enlaces de productos" style="display: flex; flex-direction: column; gap: 8px; font-size: 0.85rem;">
                        <a href="productos.php" class="footer-link">Termos</a>
                        <a href="productos.php" class="footer-link">Agendas</a>
                        <a href="empresas.php" class="footer-link">Kits</a>
                        <a href="productos.php" class="footer-link">Placas</a>
                        <a href="productos.php" class="footer-link">Carnets</a>
                    </nav>
                </div>
                <div class="footer-links-column">
                    <h3 class="footer-heading" style="font-size: 0.9rem; font-family: var(--font-heading); margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dark);">Contacto</h3>
                    <div class="footer-contact-info" style="display: flex; flex-direction: column; gap: 10px; font-size: 0.85rem; color: var(--text-muted);">
                        <a href="https://wa.me/593000000000" class="footer-link" target="_blank" rel="noopener noreferrer">WhatsApp: +593 00 000 0000</a>
                        <a href="mailto:correo@cardnet.ec" class="footer-link">Correo: correo@cardnet.ec</a>
                        <span class="footer-link" style="color: var(--text-muted); cursor: default;">Ubicación: Ecuador</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom" style="border-top: 1px solid var(--border); padding-top: 1.5rem; padding-bottom: 1.5rem;">
            <div class="container footer-bottom-flex" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <p style="font-size: 0.8rem; color: var(--text-muted);">&copy; 2026 CardNet.ec — Detalles personalizados para marcas que cuidan su presentación.</p>
                <div class="footer-bottom-links" style="display: flex; gap: 15px; font-size: 0.8rem;">
                    <a href="faq.php" class="footer-link">Preguntas Frecuentes</a>
                    <a href="contacto.php" class="footer-link">Soporte</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Botón de WhatsApp Flotante -->
    <a href="#" class="whatsapp-float" target="_blank" rel="noopener noreferrer">
        <svg class="whatsapp-icon" viewBox="0 0 24 24" width="28" height="28" fill="currentColor">
            <path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 0 0 1.333 4.982L2 22l5.233-1.371a9.994 9.994 0 0 0 4.779 1.22h.005c5.505 0 9.99-4.478 9.99-9.985A9.988 9.988 0 0 0 12.012 2zm4.7 13.916c-.223.633-1.29 1.205-1.782 1.282-.477.075-.947.168-3.067-.665-2.707-1.06-4.442-3.817-4.577-3.996-.134-.178-1.096-1.455-1.096-2.781 0-1.325.692-1.973.938-2.228.246-.255.535-.319.714-.319.18 0 .358.001.514.009.16.008.375-.062.586.448.223.54.76 1.851.827 1.984.067.134.112.29.022.468-.09.18-.134.29-.268.447-.134.156-.282.35-.403.47-.134.134-.273.28-.117.548.156.268.693 1.139 1.492 1.85 1.026.914 1.89 1.196 2.158 1.33.268.134.424.112.58-.067.157-.18.67-.781.848-1.049.178-.268.358-.223.58-.134.224.089 1.42.67 1.666.792.246.123.411.18.47.282.06.101.06.586-.163 1.218z"/>
        </svg>
    </a>

    <!-- Scripts Modulares -->
    <script src="js/main.js"></script>
    <script src="js/animations.js"></script>
</body>
</html>
