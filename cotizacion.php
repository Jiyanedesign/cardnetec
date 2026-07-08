<?php
session_start();
require_once 'db.php';

// Obtener datos globales del sitio
$settings = getSiteSettings($pdo);

// Soporte para enlaces directos heredados: Añadir al carrito y recargar
if (isset($_GET['producto'])) {
    $prod_slug = trim($_GET['producto']);
    
    // Si es credencial PVC
    if ($prod_slug === 'credenciales-pvc') {
        $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 50;
        $item = [
            'name' => 'Credencial PVC',
            'slug' => 'credenciales-pvc',
            'qty' => $qty,
            'price' => 1.80,
            'snapshot' => '',
            'subtotal' => $qty * 1.80
        ];
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $_SESSION['cart'][] = $item;
    } else {
        // Consultar producto
        try {
            $stmt = $pdo->prepare("SELECT * FROM productos WHERE slug = ?");
            $stmt->execute([$prod_slug]);
            $p = $stmt->fetch();
            if ($p) {
                $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 20;
                $item = [
                    'name' => $p['name'],
                    'slug' => $p['slug'],
                    'qty' => $qty,
                    'price' => (float)$p['price'],
                    'snapshot' => '',
                    'subtotal' => $qty * (float)$p['price']
                ];
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
                $_SESSION['cart'][] = $item;
            }
        } catch (PDOException $e) {}
    }

    header("Location: cotizacion.php");
    exit;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$grand_total = 0;
foreach ($cart as $item) {
    $grand_total += $item['subtotal'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotiza productos personalizados | CardNet.ec</title>
    <meta name="description" content="Solicita una cotización para productos personalizados, carnets, kits corporativos y grabado láser.">
    <link rel="canonical" href="https://cardnet.ec/cotizacion.php">
    
    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=2.0">
    <link rel="stylesheet" href="css/layout.css?v=2.0">
    <link rel="stylesheet" href="css/components.css?v=2.0">
    <link rel="stylesheet" href="css/pages.css?v=2.0">
    <link rel="stylesheet" href="css/animations.css?v=1.1.2">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .split-checkout {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 3rem;
            align-items: start;
        }
        @media (max-width: 992px) {
            .split-checkout {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }
        .cart-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 1.5rem;
        }
        .cart-item-card {
            background-color: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 12px;
            display: flex;
            gap: 12px;
            align-items: center;
            position: relative;
        }
        .cart-item-img {
            width: 70px;
            height: 70px;
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid var(--border);
            background-color: var(--surface-light);
        }
        .cart-item-info {
            flex-grow: 1;
        }
        .cart-item-name {
            font-weight: 600;
            font-size: 0.92rem;
            margin: 0 0 4px 0;
            color: var(--text-main);
        }
        .cart-item-meta {
            font-size: 0.78rem;
            color: var(--text-muted);
        }
        .cart-item-subtotal {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary);
            text-align: right;
        }
        .btn-delete-item {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            color: #EF4444;
            cursor: pointer;
            padding: 2px;
        }
        .cart-total-box {
            background-color: var(--surface-light);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>

    <!-- Barra de Anuncios Superior -->
    <div class="top-announcement-bar">
        Taller de personalización en Quito | Envíos corporativos asegurados a todo el Ecuador
    </div>

    <!-- Cabecera de Página -->
    <header class="main-header">
        <div class="container">
            <div class="header-middle">
                <a href="index.php" class="logo" aria-label="CardNet.ec Inicio">
                    <img src="images/logo.png?v=2.0" alt="CardNet.ec Logo" class="logo-img">
                </a>
                
                <div class="header-contact-status">
                    <div class="contact-status-item">
                        <span class="status-icon-wrap">
                            <svg style="width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2.5;" viewBox="0 0 24 24">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72"/>
                            </svg>
                        </span>
                        <div class="status-text">
                            <h4>Asesoría Directa</h4>
                            <p>+593 00 000 0000</p>
                        </div>
                    </div>
                </div>
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
            </div>
        </div>
    </header>

    <!-- Encabezado de Página Interna -->
    <div class="page-header-block">
        <div class="container">
            <h1 class="page-header-title">Cotiza tus productos personalizados</h1>
            <p class="page-header-description">Cuéntanos qué producto necesitas, sube tu logo y te ayudamos a elegir el mejor acabado.</p>
        </div>
    </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="section-padding container">
        
        <div class="split-checkout">
            
            <!-- Columna Izquierda: Formulario Comercial -->
            <div class="checkout-form-column reveal-on-scroll">
                <div class="solution-card">
                    <h3 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-family: var(--font-heading);">Datos del cliente</h3>
                    
                    <?php if (!empty($cart)): ?>
                        <form id="quote-form" novalidate>
                            <div class="grid-2" style="gap: 0 15px;">
                                <div class="form-group">
                                    <label class="form-label" for="quote-name">Tu Nombre *</label>
                                    <input class="form-input" type="text" id="quote-name" required placeholder="Ej. Ana Lucía">
                                    <span class="form-error-msg">Este campo es obligatorio.</span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="quote-company">Empresa / RUC</label>
                                    <input class="form-input" type="text" id="quote-company" placeholder="Ej. Innova SA">
                                </div>
                            </div>

                            <div class="grid-2" style="gap: 0 15px;">
                                <div class="form-group">
                                    <label class="form-label" for="quote-email">Correo Corporativo *</label>
                                    <input class="form-input" type="email" id="quote-email" required placeholder="ana@empresa.com">
                                    <span class="form-error-msg">Introduce un correo corporativo válido.</span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="quote-phone">Teléfono / WhatsApp *</label>
                                    <input class="form-input" type="tel" id="quote-phone" required placeholder="099000000">
                                    <span class="form-error-msg">Mínimo 9 dígitos telefónicos.</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="quote-logo">Logotipo Vectorial (Opcional)</label>
                                <input class="form-input" type="file" id="quote-logo" accept=".ai,.pdf,.svg,.png,.jpg" style="padding: 0.5rem 1rem; width: 100%; box-sizing: border-box; background: white; border: 1px solid var(--border);">
                                <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 4px;">Soporta formatos: .AI, .PDF, .SVG, .PNG (max. 10MB)</span>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="quote-notes">Instrucciones de Grabado / Observaciones</label>
                                <textarea class="form-textarea" id="quote-notes" placeholder="Indica instrucciones adicionales, colores de grabado específicos o fecha requerida de entrega..."></textarea>
                            </div>

                            <button class="btn btn-primary" type="submit" style="width: 100%; margin-top: 0.5rem; padding: 14px;">
                                Enviar Solicitud de Presupuesto
                            </button>
                        </form>
                    <?php else: ?>
                        <!-- Formulario bloqueado / Lista vacía -->
                        <div style="text-align: center; padding: 2rem 0;">
                            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Debes añadir al menos un artículo personalizado a tu lista para cotizar.</p>
                            <a href="productos.php" class="btn btn-primary">Ir al catálogo de productos</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Columna Derecha: Resumen de tu Lista de Cotización -->
            <div class="checkout-summary-column reveal-on-scroll delay-100">
                <h3 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-family: var(--font-heading);">Resumen de tu Cotización</h3>

                <?php if (!empty($cart)): ?>
                    <div class="cart-list">
                        <?php foreach ($cart as $index => $item): ?>
                            <div class="cart-item-card">
                                <?php if ($item['snapshot']): ?>
                                    <img src="<?php echo htmlspecialchars($item['snapshot']); ?>" class="cart-item-img">
                                <?php else: ?>
                                    <div class="cart-item-img" style="display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: var(--text-muted);">
                                        Sin Logo
                                    </div>
                                <?php endif; ?>

                                <div class="cart-item-info">
                                    <h4 class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <span class="cart-item-meta"><?php echo $item['qty']; ?> uds x $<?php echo number_format($item['price'], 2); ?></span>
                                </div>

                                <div class="cart-item-subtotal">
                                    $<?php echo number_format($item['subtotal'], 2); ?>
                                </div>

                                <!-- Botón de eliminar -->
                                <button class="btn-delete-item" onclick="removeCartItem(<?php echo $index; ?>)" title="Eliminar de la lista">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                                    </svg>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="cart-total-box">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 600; text-transform: uppercase; font-size: 0.82rem;">Total Estimado:</span>
                            <span style="font-size: 1.8rem; font-weight: bold; color: var(--primary);">$<?php echo number_format($grand_total, 2); ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="border: 1px dashed var(--border); border-radius: var(--radius-md); padding: 3rem; text-align: center; color: var(--text-muted);">
                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 1rem auto; opacity: 0.5;">
                            <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                        <p style="font-size: 0.9rem;">El cotizador está vacío.</p>
                    </div>
                <?php endif; ?>

                <div class="solution-card" style="margin-top: 1.5rem; padding: 1.25rem;">
                    <h4 style="margin-bottom: 0.5rem; font-size: 0.92rem;">💡 ¿Cómo funciona el envío?</h4>
                    <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5;">Al enviar este formulario, los datos se registrarán en nuestro sistema comercial de taller, y se abrirá una ventana a nuestro WhatsApp para que un asesor te asista directamente.</p>
                </div>
            </div>

        </div>

    </main>

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

    <!-- Scripts Modulares -->
    <script src="js/main.js?v=3.5"></script>
    <script src="js/animations.js"></script>
    <script src="js/forms.js"></script>
    <script>
        // Eliminar elemento de la cesta
        function removeCartItem(index) {
            if (confirm('¿Deseas eliminar este producto de tu cotización?')) {
                fetch(`cart-action.php?action=remove&index=${index}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                })
                .catch(err => console.error(err));
            }
        }
    </script>
</body>
</html>
