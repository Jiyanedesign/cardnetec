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

// Recalcular precios de carrito basados en volumen dinámicamente
$grand_total = 0;
$total_qty = 0;
foreach ($cart as $index => &$item) {
    try {
        $stmtP = $pdo->prepare("SELECT price, volume_prices FROM productos WHERE slug = ?");
        $stmtP->execute([$item['slug']]);
        $prodInfo = $stmtP->fetch();
        if ($prodInfo) {
            $base_price = (float)$prodInfo['price'];
            $volume_rules = json_decode($prodInfo['volume_prices'], true) ?: [];
            
            $applicable_price = $base_price;
            foreach ($volume_rules as $rule) {
                if ($item['qty'] >= $rule['qty']) {
                    $applicable_price = (float)$rule['price'];
                }
            }
            $item['price'] = $applicable_price;
            $item['subtotal'] = $item['qty'] * $applicable_price;
        }
    } catch (PDOException $e) {}
    $grand_total += $item['subtotal'];
    $total_qty += $item['qty'];
}
unset($item);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotiza tus productos personalizados | CardNet.ec</title>
    <meta name="description" content="Cuéntanos qué producto necesitas, sube tu logo y te ayudamos a elegir el mejor acabado para tu marca.">
    <link rel="canonical" href="https://cardnet.ec/cotizacion.php">
    <link rel="icon" type="image/png" href="favicon.png?v=2.0">
    
    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=3.6">
    <link rel="stylesheet" href="css/layout.css?v=3.6">
    <link rel="stylesheet" href="css/components.css?v=3.6">
    <link rel="stylesheet" href="css/pages.css?v=3.6">
    <link rel="stylesheet" href="css/animations.css?v=1.1.2">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        .split-checkout {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 40px;
            align-items: start;
        }
        @media (max-width: 992px) {
            .split-checkout {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }
        .form-block-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
        }
        .form-block-title {
            font-family: var(--font-heading);
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            color: var(--dark);
            border-bottom: 1px solid var(--border);
            padding-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-block-num {
            background: var(--primary);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .chip-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }
        .chip-option {
            border: 1px solid var(--border);
            padding: 10px 18px;
            border-radius: 30px;
            background: white;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }
        .chip-option:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        .chip-option.selected {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 10px rgba(99,174,44,0.15);
        }
    </style>
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <!-- Encabezado de Página Interna -->
    <div class="page-header-block">
        <div class="container">
            <h1 class="page-header-title">Cotiza tus identificaciones o productos personalizados</h1>
            <p class="page-header-description">Cuéntanos si necesitas carnets, credenciales, cintas, porta credenciales o productos personalizados, y te ayudamos a preparar una opción adecuada.</p>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="section-padding container">
        
        <?php if ($settings['min_order'] > 0 && $total_qty > 0 && $total_qty < $settings['min_order']): ?>
            <div style="background-color: #FDE8E8; color: #9B1C1C; border: 1px solid rgba(155, 28, 28, 0.2); padding: 15px; border-radius: var(--radius-sm); margin-bottom: 2rem; font-size: 0.88rem; line-height: 1.5;">
                <strong>⚠️ Pedido Mínimo Requerido:</strong> El taller requiere un pedido mínimo de <?php echo (int)$settings['min_order']; ?> unidades en total. Tu lista contiene actualmente <?php echo (int)$total_qty; ?> unidades (te faltan <?php echo (int)($settings['min_order'] - $total_qty); ?> unidades). Por favor regresa al catálogo para añadir más productos o ajusta la cantidad.
            </div>
        <?php endif; ?>

        <div class="split-checkout">
            
            <!-- Columna Izquierda: Formulario Dividido en 6 Bloques Visuales -->
            <div class="checkout-form-column reveal-on-scroll">
                <form id="quote-form" novalidate>
                    
                    <!-- BLOQUE 1: Datos del cliente -->
                    <div class="form-block-card">
                        <h3 class="form-block-title">
                            <span class="form-block-num">1</span> Datos de contacto
                        </h3>
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
                                <label class="form-label" for="quote-phone">WhatsApp / Teléfono *</label>
                                <input class="form-input" type="tel" id="quote-phone" required placeholder="Ej. 099000000">
                                <span class="form-error-msg">Mínimo 9 dígitos.</span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="quote-email">Correo Corporativo (Opcional)</label>
                                <input class="form-input" type="email" id="quote-email" placeholder="ana@empresa.com">
                            </div>
                        </div>
                    </div>

                    <!-- BLOQUE 2: ¿Qué necesitas cotizar? -->
                    <div class="form-block-card">
                        <h3 class="form-block-title">
                            <span class="form-block-num">2</span> ¿Qué necesitas cotizar?
                        </h3>
                        <div class="chip-group" id="product-chips">
                            <div class="chip-option <?php echo (empty($cart)) ? 'selected' : ''; ?>" data-value="Carnets PVC">Carnets PVC</div>
                            <div class="chip-option" data-value="Credenciales">Credenciales</div>
                            <div class="chip-option" data-value="Cintas porta credenciales">Cintas porta credenciales</div>
                            <div class="chip-option" data-value="Porta carnets">Porta carnets</div>
                            <div class="chip-option" data-value="Tarjetas PVC">Tarjetas PVC</div>
                            <div class="chip-option" data-value="Accesorios para identificación">Accesorios para identificación</div>
                            <div class="chip-option" data-value="Credenciales para eventos">Credenciales para eventos</div>
                            <div class="chip-option" data-value="Agendas personalizadas">Agendas personalizadas</div>
                            <div class="chip-option" data-value="Llaveros personalizados">Llaveros personalizados</div>
                            <div class="chip-option" data-value="Termos grabados">Termos grabados</div>
                            <div class="chip-option" data-value="Kits corporativos">Kits corporativos</div>
                            <div class="chip-option" data-value="Cajas personalizadas">Cajas personalizadas</div>
                            <div class="chip-option" data-value="Otro">Otro</div>
                        </div>
                        <input type="hidden" id="selected-product" value="Carnets PVC">
                    </div>

                    <!-- BLOQUE 3: Tipo de trabajo -->
                    <div class="form-block-card">
                        <h3 class="form-block-title">
                            <span class="form-block-num">3</span> Tipo de trabajo
                        </h3>
                        <div class="chip-group" id="type-chips">
                            <div class="chip-option selected" data-value="Diseño de carnet o credencial">Diseño de carnet o credencial</div>
                            <div class="chip-option" data-value="Impresión de carnets">Impresión de carnets</div>
                            <div class="chip-option" data-value="Cintas full color">Cintas full color</div>
                            <div class="chip-option" data-value="Cintas a un color">Cintas a un color</div>
                            <div class="chip-option" data-value="Cintas sin impresión">Cintas sin impresión</div>
                            <div class="chip-option" data-value="Porta credenciales">Porta credenciales</div>
                            <div class="chip-option" data-value="Grabado láser">Grabado láser</div>
                            <div class="chip-option" data-value="Personalización de producto">Personalización de producto</div>
                            <div class="chip-option" data-value="No estoy seguro">No estoy seguro</div>
                        </div>
                        <input type="hidden" id="selected-type" value="Diseño de carnet o credencial">
                    </div>

                    <!-- BLOQUE 4: Cantidad aproximada -->
                    <div class="form-block-card">
                        <h3 class="form-block-title">
                            <span class="form-block-num">4</span> Cantidad aproximada
                        </h3>
                        <div class="chip-group" id="qty-chips">
                            <div class="chip-option" data-value="10 a 25 unidades">10 a 25 unidades</div>
                            <div class="chip-option selected" data-value="25 a 50 unidades">25 a 50 unidades</div>
                            <div class="chip-option" data-value="50 a 100 unidades">50 a 100 unidades</div>
                            <div class="chip-option" data-value="Más de 100 unidades">Más de 100 unidades</div>
                            <div class="chip-option" data-value="Aún no sé">Aún no sé</div>
                        </div>
                        <input type="hidden" id="selected-qty-range" value="25 a 50 unidades">
                    </div>

                    <!-- BLOQUE 5: Subir logo o referencia -->
                    <div class="form-block-card">
                        <h3 class="form-block-title">
                            <span class="form-block-num">5</span> Subir logo o referencia (Opcional)
                        </h3>
                        <div class="form-group">
                            <input class="form-input" type="file" id="quote-logo" accept=".png,.jpg,.jpeg,.svg,.ai,.pdf" style="padding: 10px; width: 100%; border: 1px dashed var(--border); background: var(--surface-light); cursor: pointer;">
                            <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 6px;">
                                Soporta formatos: PNG, JPG, PDF, SVG, AI (Máx. 10MB).
                            </span>
                        </div>
                    </div>

                    <!-- BLOQUE 6: Cuéntanos tu idea -->
                    <div class="form-block-card">
                        <h3 class="form-block-title">
                            <span class="form-block-num">6</span> Cuéntanos tu idea
                        </h3>
                        <div class="form-group">
                            <textarea class="form-textarea" id="quote-notes" rows="4" placeholder="Ejemplo: Necesito carnets para 30 colaboradores con cinta porta credencial y porta carnet."></textarea>
                        </div>
                    </div>

                    <button class="btn btn-primary" type="submit" style="width: 100%; padding: 16px; font-size: 0.95rem;" <?php echo ($settings['min_order'] > 0 && $total_qty > 0 && $total_qty < $settings['min_order']) ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''; ?>>
                        Enviar solicitud de cotización
                    </button>
                    
                    <div style="display: flex; gap: 10px; margin-top: 15px;">
                        <a href="productos.php" class="btn btn-secondary" style="flex-grow: 1; text-align: center; text-transform: none; padding: 10px 0; background: white;">Ver productos</a>
                        <a href="cotizacion.php" class="btn btn-secondary" style="flex-grow: 1; text-align: center; text-transform: none; padding: 10px 0; background: white;">Cotizar una idea desde cero</a>
                    </div>
                    
                    <p style="font-size: 0.8rem; color: var(--text-muted); text-align: center; margin-top: 12px; line-height: 1.4;">
                        No necesitas tener todo definido. Puedes enviarnos una idea general y la revisamos contigo paso a paso.
                    </p>
                </form>
            </div>

            <!-- Columna Derecha: Resumen de tu Lista de Cotización -->
            <div class="checkout-summary-column reveal-on-scroll delay-100" style="position: sticky; top: 2rem;">
                <div class="form-block-card" style="margin-bottom: 0;">
                    <h3 style="margin-bottom: 1.5rem; font-size: 1.2rem; font-family: var(--font-heading); color: var(--dark); border-bottom: 1px solid var(--border); padding-bottom: 0.75rem;">Resumen de tu solicitud</h3>

                    <?php if (!empty($cart)): ?>
                        <div class="cart-list" style="display: flex; flex-direction: column; gap: 15px; max-height: 380px; overflow-y: auto; margin-bottom: 1.5rem; padding-right: 5px;">
                            <?php foreach ($cart as $index => $item): ?>
                                <div class="cart-item-card" style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <?php if ($item['snapshot']): ?>
                                            <img src="<?php echo htmlspecialchars($item['snapshot']); ?>" style="width: 48px; height: 48px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                                        <?php else: ?>
                                            <div style="width: 48px; height: 48px; border-radius: var(--radius-sm); background: var(--surface-light); display: flex; align-items: center; justify-content: center; font-size: 0.65rem; color: var(--text-muted); font-weight: 500;">Sin logo</div>
                                        <?php endif; ?>
                                        <div>
                                            <h4 style="font-size: 0.85rem; font-weight: 600; margin: 0; color: var(--dark);"><?php echo htmlspecialchars($item['name']); ?></h4>
                                            <span style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $item['qty']; ?> uds x $<?php echo number_format($item['price'], 2); ?></span>
                                        </div>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 0.85rem; font-weight: 600; color: var(--dark);">$<?php echo number_format($item['subtotal'], 2); ?></span>
                                        <button onclick="removeCartItem(<?php echo $index; ?>)" style="background: none; border: none; color: #EF4444; cursor: pointer; padding: 2px;" title="Eliminar">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; background: var(--surface-light); padding: 12px 15px; border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
                            <span style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted);">Total Estimado:</span>
                            <span style="font-size: 1.35rem; font-weight: 700; color: var(--primary);">$<?php echo number_format($grand_total, 2); ?></span>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 2.5rem 0; color: var(--text-muted);">
                            <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.2" style="opacity: 0.4; margin-bottom: 10px;">
                                <circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"/>
                            </svg>
                            <p style="font-size: 0.88rem; line-height: 1.5; margin: 0 0 1rem 0;">Aún no has agregado productos a tu lista.</p>
                            <p style="font-size: 0.78rem; margin-bottom: 1.5rem;">Explora el catálogo o cuéntanos directamente lo que buscas usando el formulario.</p>
                            <a href="productos.php" class="btn btn-secondary" style="font-size: 0.75rem; padding: 8px 14px; background: white;">Ver productos</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts Modulares -->
    <script src="js/main.js?v=3.5"></script>
    <script src="js/animations.js"></script>
    <script src="js/forms.js"></script>
    <script>
        // Chips funcionales interactivos
        function initChips(groupId, hiddenInputId) {
            const container = document.getElementById(groupId);
            if (!container) return;
            const input = document.getElementById(hiddenInputId);
            const chips = container.querySelectorAll('.chip-option');
            chips.forEach(chip => {
                chip.addEventListener('click', () => {
                    chips.forEach(c => c.classList.remove('selected'));
                    chip.classList.add('selected');
                    input.value = chip.getAttribute('data-value');
                });
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initChips('product-chips', 'selected-product');
            initChips('type-chips', 'selected-type');
            initChips('qty-chips', 'selected-qty-range');
        });

        function removeCartItem(index) {
            if (confirm('¿Deseas eliminar este producto de tu cotización?')) {
                fetch(`cart-action.php?action=remove&index=${index}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                });
            }
        }
    </script>
</body>
</html>
