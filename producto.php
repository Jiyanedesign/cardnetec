<?php
session_start();
require_once 'db.php';

// Obtener producto por slug
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$product = null;

if ($slug) {
    try {
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM productos p LEFT JOIN categorias c ON p.category_id = c.id WHERE p.slug = ? AND p.is_active = 1");
        $stmt->execute([$slug]);
        $product = $stmt->fetch();
    } catch (PDOException $e) {
        $product = null;
    }
}

// Redireccionar si el producto no existe
if (!$product) {
    header("Location: productos.php");
    exit;
}

// Cargar materiales seleccionados
$selected_materials = [];
if (!empty($product['materials_json'])) {
    $mat_ids = json_decode($product['materials_json'], true) ?: [];
    if (!empty($mat_ids)) {
        $in_clause = implode(',', array_map('intval', $mat_ids));
        try {
            $selected_materials = $pdo->query("SELECT * FROM materiales WHERE id IN ($in_clause) AND is_active = 1")->fetchAll();
        } catch (PDOException $e) {
            $selected_materials = [];
        }
    }
}

// Cargar galería de imágenes
$gallery = json_decode($product['gallery_images'], true) ?: [];
// Añadir imagen principal al inicio de la galería si existe
if ($product['image_main']) {
    array_unshift($gallery, $product['image_main']);
}
$gallery = array_unique($gallery);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> | CardNet.ec</title>
    <meta name="description" content="<?php echo htmlspecialchars($product['description_short']); ?>">
    <link rel="stylesheet" href="css/base.css?v=4.0">
    <link rel="stylesheet" href="css/layout.css?v=4.0">
    <link rel="stylesheet" href="css/components.css?v=4.0">
    <link rel="stylesheet" href="css/pages.css?v=4.0">
    <link rel="stylesheet" href="css/animations.css?v=1.1.2">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Fabric.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

    <style>
        .product-detail-light-theme {
            background-color: var(--light);
            color: var(--text-main);
            padding: 3rem 0;
            min-height: 80vh;
        }
        .breadcrumb-light {
            display: flex;
            gap: 8px;
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
        }
        .breadcrumb-light a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color var(--transition-fast);
        }
        .breadcrumb-light a:hover {
            color: var(--primary);
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 3rem;
            align-items: start;
        }
        @media (max-width: 992px) {
            .detail-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }
        
        /* Contenedor del Preview / Canvas */
        .preview-column {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .canvas-container-outer {
            width: 100%;
            aspect-ratio: 1;
            background-color: var(--surface-light);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            box-shadow: var(--shadow-sm);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }
        .canvas-container-outer:hover {
            transform: scale(1.015);
            box-shadow: var(--shadow-md);
        }
        
        /* Efecto de transición de desvanecimiento para el canvas wrapper */
        .canvas-container {
            transition: opacity 0.2s ease-in-out;
        }

        .thumbnail-gallery {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .thumbnail-item {
            width: 70px;
            height: 70px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            overflow: hidden;
            cursor: pointer;
            transition: var(--transition-fast);
            background-color: var(--light);
        }
        .thumbnail-item.active {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(99, 174, 44, 0.2);
        }
        .thumbnail-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Caja de Controles del Simulador */
        .simulator-control-panel {
            background-color: var(--light);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
        }
        .simulator-title {
            font-family: var(--font-heading);
            font-size: 1.15rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-main);
        }
        .btn-upload-wrap {
            position: relative;
            display: inline-block;
            width: 100%;
            margin-bottom: 1rem;
        }
        .btn-upload {
            width: 100%;
            background-color: var(--surface-light);
            border: 1px dashed var(--border);
            color: var(--text-main);
            padding: 12px;
            text-align: center;
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition-fast);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }
        .btn-upload:hover {
            border-color: var(--primary);
            background-color: var(--primary-light);
        }
        .btn-upload-wrap input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .sim-slider-group {
            margin-bottom: 1rem;
        }
        .sim-slider-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-bottom: 6px;
        }
        .sim-slider {
            width: 100%;
            -webkit-appearance: none;
            height: 6px;
            border-radius: 3px;
            background: var(--border);
            outline: none;
        }
        .sim-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--primary);
            cursor: pointer;
        }
        .sim-select-group {
            margin-bottom: 1rem;
        }
        .sim-select {
            width: 100%;
            background-color: var(--light);
            color: var(--text-main);
            border: 1px solid var(--border);
            padding: 10px;
            border-radius: var(--radius-sm);
            font-size: 0.88rem;
            outline: none;
        }

        /* Columna de Datos */
        .info-column {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .stock-tag {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.82rem;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 0.5rem;
        }
        .stock-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--primary);
            display: inline-block;
        }
        .product-title-style {
            font-family: var(--font-heading);
            font-size: 2.5rem;
            margin: 0;
            line-height: 1.1;
            color: var(--text-main);
        }
        
        /* Caja de Precios y Cantidades */
        .purchase-box {
            background-color: var(--light);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
        }
        .purchase-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .qty-label, .price-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
            display: block;
        }
        .qty-selectors {
            display: flex;
            align-items: center;
            background-color: var(--surface-light);
            border-radius: 20px;
            padding: 4px 8px;
            width: fit-content;
            border: 1px solid var(--border);
        }
        .qty-btn {
            background: none;
            border: none;
            color: var(--text-main);
            font-size: 1.2rem;
            width: 32px;
            height: 32px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            user-select: none;
        }
        .qty-input {
            width: 40px;
            text-align: center;
            background: none;
            border: none;
            color: var(--text-main);
            font-weight: bold;
            font-size: 1rem;
            outline: none;
        }
        .unit-price-display {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-main);
            text-align: right;
        }
        .subtotal-row {
            border-top: 1px solid var(--border);
            padding-top: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .subtotal-label {
            font-weight: 600;
            font-size: 0.88rem;
            color: var(--text-main);
            text-transform: uppercase;
        }
        .subtotal-value {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .btn-gradient {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            width: 100%;
            background-color: var(--primary);
            color: white;
            padding: 16px;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-fast);
            text-decoration: none;
        }
        .btn-gradient:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        /* Acerca del Producto */
        .about-box {
            border-top: 1px solid var(--border);
            padding-top: 1.5rem;
        }
        .about-title {
            font-family: var(--font-heading);
            font-size: 1.25rem;
            margin-bottom: 1rem;
            color: var(--text-main);
        }
        .specs-table {
            width: 100%;
            margin-bottom: 1rem;
        }
        .specs-table tr {
            border-bottom: 1px solid var(--border);
        }
        .specs-table td {
            padding: 8px 0;
            font-size: 0.88rem;
        }
        .specs-label {
            color: var(--text-muted);
            width: 30%;
        }
    </style>
</head>
<body>

    <!-- Barra de Anuncios Superior -->
    <div class="top-announcement-bar">
        Taller de personalización en Quito | Envíos corporativos asegurados a todo el Ecuador
    </div>

    <?php include 'includes/header.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="product-detail-light-theme">
        <div class="container">
            
            <!-- Breadcrumbs -->
            <div class="breadcrumb-light">
                <a href="index.php">Inicio</a>
                <span>/</span>
                <a href="productos.php">Catálogo</a>
                <span>/</span>
                <span style="color: var(--text-main); font-weight: 500;"><?php echo htmlspecialchars($product['name']); ?></span>
            </div>

            <!-- Detalle Grid -->
            <div class="detail-grid">
                
                <!-- Columna Izquierda: Imagen / Canvas / Simulador -->
                <div class="preview-column">
                    <div class="canvas-container-outer">
                        <canvas id="canvas-simulator" width="500" height="500"></canvas>
                    </div>

                    <!-- Galería de Miniaturas -->
                    <?php if (!empty($gallery)): ?>
                        <div class="thumbnail-gallery">
                            <?php foreach ($gallery as $index => $g_img): ?>
                                <div class="thumbnail-item <?php echo $index === 0 ? 'active' : ''; ?>" onclick="changeCanvasBackground('uploads/<?php echo htmlspecialchars($g_img); ?>', this)">
                                    <img src="uploads/<?php echo htmlspecialchars($g_img); ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Panel del Simulador -->
                    <?php if ($product['allows_simulation']): ?>
                        <div class="simulator-control-panel">
                            <div class="simulator-title">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);">
                                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
                                </svg>
                                ¿Cómo se verá tu marca?
                            </div>

                            <div class="btn-upload-wrap">
                                <div class="btn-upload">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                                    </svg>
                                    Subir mi Logo (PNG/SVG)
                                </div>
                                <input type="file" id="logo-uploader" accept="image/png, image/jpeg, image/svg+xml">
                            </div>

                            <div class="sim-slider-group">
                                <div class="sim-slider-label">
                                    <span>Opacidad</span>
                                    <span id="opacity-val">80%</span>
                                </div>
                                <input type="range" class="sim-slider" id="logo-opacity" min="10" max="100" value="80">
                            </div>

                            <div class="sim-select-group">
                                <div class="sim-slider-label"><span>Modo de Integración</span></div>
                                <select class="sim-select" id="logo-blend">
                                    <option value="normal">Normal</option>
                                    <option value="multiply">Multiply (Ideal grabado oscuro)</option>
                                    <option value="screen">Screen (Ideal grabado claro)</option>
                                </select>
                            </div>

                            <div class="sim-select-group">
                                <div class="sim-slider-label"><span>Efecto de Grabado / Simulación</span></div>
                                <select class="sim-select" id="logo-effect">
                                    <option value="original">A Color / Impresión Original</option>
                                    <option value="laser-silver">Grabado Láser Plata (Acero)</option>
                                    <option value="laser-gold">Grabado Láser Dorado (Latón)</option>
                                    <option value="deboss">Bajo Relieve (Cuero/PU)</option>
                                </select>
                            </div>

                            <button id="btn-remove-logo" style="width:100%; padding:10px; background:none; border:1px solid #EF4444; color:#EF4444; border-radius:var(--radius-sm); font-weight:600; cursor:pointer; margin-top:10px;">Eliminar Logo</button>
                            
                            <p style="font-size:0.75rem; color:var(--text-muted); margin-top:10px; text-align:center;">
                                ℹ️ Arrastra para mover y usa la esquina inferior para redimensionar.
                            </p>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Columna Derecha: Información y Compra -->
                <div class="info-column">
                    <div>
                        <div class="stock-tag">
                            <span class="stock-dot"></span>
                            Stock disponible: <?php echo (int)$product['stock']; ?> unidades
                        </div>
                        <h1 class="product-title-style"><?php echo htmlspecialchars($product['name']); ?></h1>
                        <p style="color:var(--text-muted); margin-top:10px; line-height:1.5; font-size:0.95rem;"><?php echo htmlspecialchars($product['description_short']); ?></p>
                    </div>

                    <!-- Ficha de Cotización Directa -->
                    <div class="purchase-box">
                        <div class="purchase-row">
                            <div>
                                <span class="qty-label">Cantidad</span>
                                <div class="qty-selectors">
                                    <button class="qty-btn" id="qty-minus">-</button>
                                    <input type="text" class="qty-input" id="qty-input" value="20" readonly>
                                    <button class="qty-btn" id="qty-plus">+</button>
                                </div>
                            </div>
                            <div>
                                <span class="price-label">Precio Unit.</span>
                                <div class="unit-price-display" id="unit-price">$<?php echo number_format($product['price'], 2); ?></div>
                            </div>
                        </div>

                        <div class="subtotal-row">
                            <span class="subtotal-label">Subtotal Estimado</span>
                            <span class="subtotal-value" id="subtotal-val">$50.00</span>
                        </div>

                        <?php 
                        $vp_rules = json_decode($product['volume_prices'], true) ?: [];
                        if (!empty($vp_rules)): 
                        ?>
                            <div class="volume-discount-box" style="margin-top: 1.25rem; border-top: 1px solid var(--border); padding-top: 1rem; width: 100%;">
                                <h4 style="font-family: var(--font-heading); font-size: 0.85rem; margin-bottom: 0.5rem; color: var(--dark);">Descuentos por volumen:</h4>
                                <table style="width: 100%; font-size: 0.78rem; border-collapse: collapse; margin-bottom: 10px;">
                                    <thead>
                                        <tr style="background: var(--surface-light);">
                                            <th style="padding: 4px; border: 1px solid var(--border); text-align: center;">Cantidad</th>
                                            <th style="padding: 4px; border: 1px solid var(--border); text-align: center;">Precio unitario</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr style="border-bottom: 1px solid var(--border);">
                                            <td style="padding: 4px; border: 1px solid var(--border); text-align: center;">Base</td>
                                            <td style="padding: 4px; border: 1px solid var(--border); text-align: center;">$<?php echo number_format($product['price'], 2); ?></td>
                                        </tr>
                                        <?php foreach ($vp_rules as $rule): ?>
                                            <tr style="border-bottom: 1px solid var(--border);">
                                                <td style="padding: 4px; border: 1px solid var(--border); text-align: center;"><?php echo $rule['qty']; ?>+ uds</td>
                                                <td style="padding: 4px; border: 1px solid var(--border); text-align: center; color: var(--primary); font-weight: 600;">$<?php echo number_format($rule['price'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <div id="savings-alert" style="display: none; margin-top: 8px; background: rgba(99, 174, 44, 0.08); border: 1px solid rgba(99, 174, 44, 0.2); padding: 6px 10px; border-radius: 4px; font-size: 0.72rem; color: var(--primary-hover); font-weight: 600; text-align: center;"></div>
                            </div>
                        <?php endif; ?>
                        
                        <p style="font-size:0.75rem; color:var(--text-muted); margin: 10px 0 20px 0; text-align:center;">
                            Nuestros valores incluyen la personalización de un logo.
                        </p>

                        <button class="btn-gradient" id="btn-submit-quote">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <path d="M12 8v8M8 12h8"/>
                            </svg>
                            Agregar a mi Lista de Cotización
                        </button>
                    </div>

                    <!-- Acerca del Producto -->
                    <div class="about-box">
                        <h3 class="about-title">Acerca del producto</h3>
                        <table class="specs-table">
                            <tr>
                                <td class="specs-label">SKU</td>
                                <td><?php echo htmlspecialchars($product['sku']); ?></td>
                            </tr>
                            <tr>
                                <td class="specs-label">Categoría</td>
                                <td><?php echo htmlspecialchars($product['category_name'] ?: $product['category']); ?></td>
                            </tr>
                        </table>
                        <?php if ($product['description_long']): ?>
                            <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.6; white-space: pre-line; margin-bottom: 1.5rem;">
                                <?php echo htmlspecialchars($product['description_long']); ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($selected_materials)): ?>
                            <h4 style="font-family: var(--font-heading); font-size: 0.95rem; margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--dark); font-weight: 500;">Materiales y acabados recomendados:</h4>
                            <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 1rem;">
                                <?php foreach ($selected_materials as $mat): ?>
                                    <div style="background: var(--surface-light); border: 1px solid var(--border); padding: 10px 14px; border-radius: var(--radius-sm);">
                                        <strong style="font-size: 0.85rem; color: var(--dark); font-weight: 600;"><?php echo htmlspecialchars($mat['name']); ?></strong>
                                        <p style="font-size: 0.78rem; color: var(--text-muted); margin: 4px 0 0 0; line-height: 1.4;"><?php echo htmlspecialchars($mat['description']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- Modal Informativo de Lista de Cotización -->
    <div id="cart-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center; color: var(--text-main);">
        <div style="background:white; padding:2.5rem; border-radius:var(--radius-lg); max-width:420px; width:90%; text-align:center; box-shadow:var(--shadow-lg);">
            <svg width="48" height="48" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" style="margin:0 auto 1.5rem auto;">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <h3 style="font-family:var(--font-heading); font-size:1.5rem; margin-bottom:0.75rem;">¡Añadido a mi Lista!</h3>
            <p style="color:var(--text-muted); font-size:0.9rem; line-height:1.5; margin-bottom:2rem;">Hemos agregado el artículo personalizado a tu requerimiento de cotización.</p>
            <div style="display:flex; flex-direction:column; gap:10px;">
                <a href="cotizacion.php" class="btn btn-primary" style="width:100%; text-align:center; padding:12px; font-weight:600;">Ver Lista y Cotizar</a>
                <button onclick="document.getElementById('cart-modal').style.display='none'" class="btn btn-secondary" style="width:100%; padding:12px; font-weight:600; border:1px solid var(--border);">Seguir Diseñando</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Script de Simulación de Canvas, Transiciones y Carrito -->
    <script>
        const basePrice = <?php echo (float)$product['price']; ?>;
        const volumeRules = <?php echo json_encode(json_decode($product['volume_prices'], true) ?: []); ?>;
        const qtyInput = document.getElementById('qty-input');
        const subtotalVal = document.getElementById('subtotal-val');

        function getActiveUnitPrice(qty) {
            let activePrice = basePrice;
            for (let i = 0; i < volumeRules.length; i++) {
                if (qty >= volumeRules[i].qty) {
                    activePrice = volumeRules[i].price;
                }
            }
            return activePrice;
        }

        function updateSubtotal() {
            const qty = parseInt(qtyInput.value) || 20;
            const activePrice = getActiveUnitPrice(qty);
            
            // Actualizar visualizador de precio unitario
            const unitPriceElem = document.getElementById('unit-price');
            if (unitPriceElem) {
                unitPriceElem.textContent = '$' + activePrice.toFixed(2);
            }
            
            // Calcular subtotal
            const subtotal = qty * activePrice;
            subtotalVal.textContent = '$' + subtotal.toFixed(2);
            
            // Calcular ahorro/beneficio
            const savingsAlert = document.getElementById('savings-alert');
            if (savingsAlert) {
                if (activePrice < basePrice) {
                    const baseCost = qty * basePrice;
                    const savings = baseCost - subtotal;
                    const percent = ((basePrice - activePrice) / basePrice * 100).toFixed(0);
                    savingsAlert.innerHTML = `¡Ahorras $${savings.toFixed(2)} (${percent}% de descuento por cantidad)!`;
                    savingsAlert.style.display = 'block';
                } else {
                    savingsAlert.style.display = 'none';
                }
            }
        }

        document.getElementById('qty-plus').addEventListener('click', () => {
            let val = parseInt(qtyInput.value) || 20;
            qtyInput.value = val + 5;
            updateSubtotal();
        });

        document.getElementById('qty-minus').addEventListener('click', () => {
            let val = parseInt(qtyInput.value) || 20;
            if (val > 5) {
                qtyInput.value = val - 5;
                updateSubtotal();
            }
        });

        let canvas = null;
        let mainImgPath = 'uploads/<?php echo htmlspecialchars($product['image_main']); ?>';

        document.addEventListener('DOMContentLoaded', () => {
            canvas = new fabric.Canvas('canvas-simulator', {
                width: 500,
                height: 500
            });
            
            loadBackground(mainImgPath);

            const logoUploader = document.getElementById('logo-uploader');
            if (logoUploader) {
                logoUploader.addEventListener('change', handleLogoUpload);
            }

            const opacitySlider = document.getElementById('logo-opacity');
            if (opacitySlider) {
                opacitySlider.addEventListener('input', (e) => {
                    const val = e.target.value;
                    document.getElementById('opacity-val').textContent = val + '%';
                    const activeObject = canvas.getActiveObject();
                    if (activeObject) {
                        activeObject.set('opacity', val / 100);
                        canvas.renderAll();
                    }
                });
            }

            const btnRemoveLogo = document.getElementById('btn-remove-logo');
            if (btnRemoveLogo) {
                btnRemoveLogo.addEventListener('click', () => {
                    const activeObject = canvas.getActiveObject();
                    if (activeObject) {
                        canvas.remove(activeObject);
                    }
                });
            }

            const blendSelect = document.getElementById('logo-blend');
            if (blendSelect) {
                blendSelect.addEventListener('change', (e) => {
                    const mode = e.target.value;
                    const activeObject = canvas.getActiveObject();
                    if (activeObject) {
                        if (mode === 'multiply') {
                            activeObject.set('globalCompositeOperation', 'multiply');
                        } else if (mode === 'screen') {
                            activeObject.set('globalCompositeOperation', 'screen');
                        } else {
                            activeObject.set('globalCompositeOperation', 'source-over');
                        }
                        canvas.renderAll();
                    }
                });
            }

            const effectSelect = document.getElementById('logo-effect');
            if (effectSelect) {
                effectSelect.addEventListener('change', applyEngravingEffect);
            }

            // Guardar en Carrito de Sesión por AJAX
            document.getElementById('btn-submit-quote').addEventListener('click', () => {
                const snapshot = canvas.toDataURL({
                    format: 'png',
                    quality: 0.95
                });

                const qty = qtyInput.value;
                
                const formData = new FormData();
                formData.append('action', 'add');
                formData.append('name', '<?php echo addslashes($product['name']); ?>');
                formData.append('slug', '<?php echo addslashes($product['slug']); ?>');
                formData.append('qty', qty);
                formData.append('price', getActiveUnitPrice(parseInt(qtyInput.value) || 20));
                formData.append('snapshot', snapshot);

                fetch('cart-action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('cart-modal').style.display = 'flex';
                    } else {
                        alert('No se pudo guardar el producto en el cotizador.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error al conectar con la cesta de cotización.');
                });
            });

            updateSubtotal();
        });

        // Transición fluida al cambiar el fondo del Canvas (Miniaturas)
        function loadBackground(url) {
            if (!canvas) return;
            const canvasEl = document.getElementById('canvas-simulator').parentNode;
            canvasEl.style.opacity = '0';
            
            setTimeout(() => {
                canvas.clear();
                fabric.Image.fromURL(url, function(img) {
                    const scale = Math.min(500 / img.width, 500 / img.height);
                    canvas.setBackgroundImage(img, () => {
                        canvas.renderAll();
                        canvasEl.style.opacity = '1';
                    }, {
                        scaleX: scale,
                        scaleY: scale,
                        left: (500 - img.width * scale) / 2,
                        top: (500 - img.height * scale) / 2,
                        originX: 'left',
                        originY: 'top'
                    });
                });
            }, 200);
        }

        function changeCanvasBackground(url, thumbElement) {
            document.querySelectorAll('.thumbnail-item').forEach(item => {
                item.classList.remove('active');
            });
            thumbElement.classList.add('active');
            loadBackground(url);
        }

        function handleLogoUpload(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(f) {
                const data = f.target.result;
                fabric.Image.fromURL(data, function(img) {
                    img.scaleToWidth(120);
                    img.set({
                        left: 190,
                        top: 200,
                        opacity: 0.8,
                        cornerColor: '#63AE2C',
                        cornerStrokeColor: '#63AE2C',
                        transparentCorners: false,
                        borderColor: '#63AE2C'
                    });
                    
                    canvas.add(img);
                    canvas.setActiveObject(img);
                    canvas.renderAll();
                });
            };
            reader.readAsDataURL(file);
        }

        function applyEngravingEffect(e) {
            const effect = e.target ? e.target.value : e;
            const activeObject = canvas.getActiveObject();
            if (!activeObject) return;

            activeObject.filters = [];

            if (effect !== 'original') {
                const blendSelect = document.getElementById('logo-blend');
                if (blendSelect) {
                    blendSelect.value = 'normal';
                }
                activeObject.set('globalCompositeOperation', 'source-over');
            }

            if (effect === 'laser-silver') {
                activeObject.filters.push(new fabric.Image.filters.Grayscale());
                activeObject.filters.push(new fabric.Image.filters.BlendColor({
                    color: '#E5E5E5',
                    mode: 'overlay',
                    alpha: 1.0
                }));
                activeObject.set('shadow', new fabric.Shadow({
                    color: 'rgba(0, 0, 0, 0.25)',
                    blur: 2,
                    offsetX: 1,
                    offsetY: 1
                }));
            } else if (effect === 'laser-gold') {
                activeObject.filters.push(new fabric.Image.filters.Grayscale());
                activeObject.filters.push(new fabric.Image.filters.BlendColor({
                    color: '#D4AF37',
                    mode: 'overlay',
                    alpha: 1.0
                }));
                activeObject.set('shadow', new fabric.Shadow({
                    color: 'rgba(0, 0, 0, 0.3)',
                    blur: 2,
                    offsetX: 1,
                    offsetY: 1
                }));
            } else if (effect === 'deboss') {
                activeObject.filters.push(new fabric.Image.filters.Grayscale());
                activeObject.filters.push(new fabric.Image.filters.BlendColor({
                    color: '#261C14',
                    mode: 'overlay',
                    alpha: 1.0
                }));
                activeObject.set('shadow', new fabric.Shadow({
                    color: 'rgba(255, 255, 255, 0.1)',
                    blur: 1,
                    offsetX: -0.5,
                    offsetY: -0.5
                }));
            } else {
                activeObject.set('shadow', null);
            }

            activeObject.applyFilters();
            canvas.renderAll();
        }
    </script>
</body>
</html>
