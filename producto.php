<?php
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
    <link rel="stylesheet" href="css/base.css?v=1.1.2">
    <link rel="stylesheet" href="css/layout.css?v=1.1.2">
    <link rel="stylesheet" href="css/components.css?v=1.1.2">
    <link rel="stylesheet" href="css/pages.css?v=1.1.2">
    <link rel="stylesheet" href="css/animations.css?v=1.1.2">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Fabric.js para la simulación interactiva -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

    <style>
        /* Estilo Integrado con la Marca (Fondo Claro/Hueso) */
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
        
        /* Botón Verde Corporativo */
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

    <!-- Cabecera de Página -->
    <header class="main-header">
        <div class="container">
            <div class="header-middle">
                <a href="index.php" class="logo" aria-label="CardNet.ec Inicio">
                    <img src="images/logo.png" alt="CardNet.ec Logo" class="logo-img">
                </a>
                
                <div class="header-search">
                    <svg class="search-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input class="search-input" type="text" placeholder="Buscar grabados...">
                </div>

                <div class="header-contact-status">
                    <div class="contact-status-item">
                        <div class="status-text">
                            <h4>Asesoría personalizada</h4>
                            <p>+593 90 000 0000</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="header-bottom">
            <div class="container nav-container">
                <nav class="nav-menu" aria-label="Navegación principal">
                    <a href="index.php" class="nav-link">Inicio</a>
                    <a href="index.php#destacados" class="nav-link">Destacados</a>
                    <a href="index.php#laser" class="nav-link">Grabado láser</a>
                    <a href="productos.php" class="nav-link active">Productos</a>
                    <a href="empresas.php" class="nav-link">Kits corporativos</a>
                    <a href="cotizacion.php" class="nav-link">Cotizar</a>
                </nav>
                <div class="header-bottom-actions">
                    <a href="cotizacion.php" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">Cotizar</a>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT IN LIGHT BRAND THEME -->
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
                        
                        <p style="font-size:0.75rem; color:var(--text-muted); margin: 10px 0 20px 0; text-align:center;">
                            Nuestros valores incluyen la personalización de un logo.
                        </p>

                        <button class="btn-gradient" id="btn-submit-quote">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                            </svg>
                            Guardar en mi Carrito
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
                            <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.6; white-space: pre-line;">
                                <?php echo htmlspecialchars($product['description_long']); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container footer-top section-padding">
            <div class="footer-grid">
                <div class="footer-brand-column">
                    <a href="index.php" class="logo footer-logo" aria-label="CardNet.ec Inicio">
                        <img src="images/logo.png" alt="CardNet.ec Logo" class="logo-img">
                    </a>
                    <p class="footer-description">Grabado láser y personalización corporativa en Ecuador.</p>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container footer-bottom-flex">
                <p>&copy; 2026 CardNet.ec — Detalles personalizados para marcas que cuidan su presentación.</p>
            </div>
        </div>
    </footer>

    <!-- Script de Simulación de Canvas y Cálculos -->
    <script>
        const unitPrice = <?php echo (float)$product['price']; ?>;
        const qtyInput = document.getElementById('qty-input');
        const subtotalVal = document.getElementById('subtotal-val');

        // Actualizar Subtotal
        function updateSubtotal() {
            const qty = parseInt(qtyInput.value) || 20;
            const subtotal = qty * unitPrice;
            subtotalVal.textContent = '$' + subtotal.toFixed(2);
        }

        // Eventos de botones Más / Menos
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

        // Configuración de Fabric.js Canvas
        let canvas = null;
        let mainImgPath = 'uploads/<?php echo htmlspecialchars($product['image_main']); ?>';

        document.addEventListener('DOMContentLoaded', () => {
            canvas = new fabric.Canvas('canvas-simulator', {
                width: 500,
                height: 500
            });
            
            // Cargar imagen de fondo inicial
            loadBackground(mainImgPath);

            // Cargador de logotipos
            const logoUploader = document.getElementById('logo-uploader');
            if (logoUploader) {
                logoUploader.addEventListener('change', handleLogoUpload);
            }

            // Opacidad
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

            // Eliminar logo
            const btnRemoveLogo = document.getElementById('btn-remove-logo');
            if (btnRemoveLogo) {
                btnRemoveLogo.addEventListener('click', () => {
                    const activeObject = canvas.getActiveObject();
                    if (activeObject) {
                        canvas.remove(activeObject);
                    }
                });
            }

            // Modo de Integración (Blend)
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

            // Efecto de Grabado
            const effectSelect = document.getElementById('logo-effect');
            if (effectSelect) {
                effectSelect.addEventListener('change', applyEngravingEffect);
            }

            // Submit de Cotización
            document.getElementById('btn-submit-quote').addEventListener('click', () => {
                const snapshot = canvas.toDataURL({
                    format: 'png',
                    quality: 0.95
                });

                const qty = qtyInput.value;
                const url = `cotizacion.php?producto=<?php echo urlencode($product['slug']); ?>&qty=${qty}`;
                
                sessionStorage.setItem('simulation_snapshot', snapshot);
                window.location.href = url;
            });

            updateSubtotal();
        });

        // Cargar imagen de fondo
        function loadBackground(url) {
            if (!canvas) return;
            canvas.clear();

            fabric.Image.fromURL(url, function(img) {
                const scale = Math.min(500 / img.width, 500 / img.height);
                
                canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                    scaleX: scale,
                    scaleY: scale,
                    left: (500 - img.width * scale) / 2,
                    top: (500 - img.height * scale) / 2,
                    originX: 'left',
                    originY: 'top'
                });
            });
        }

        // Cambiar fondo
        function changeCanvasBackground(url, thumbElement) {
            document.querySelectorAll('.thumbnail-item').forEach(item => {
                item.classList.remove('active');
            });
            thumbElement.classList.add('active');
            loadBackground(url);
        }

        // Subir logo
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

        // Efecto grabado con simulación de profundidad y color sólido (overlay)
        function applyEngravingEffect(e) {
            const effect = e.target ? e.target.value : e;
            const activeObject = canvas.getActiveObject();
            if (!activeObject) return;

            activeObject.filters = [];

            // Si es grabado láser, forzar la integración a "Normal" (opaco) para evitar mezclas de color irreales
            if (effect !== 'original') {
                const blendSelect = document.getElementById('logo-blend');
                if (blendSelect) {
                    blendSelect.value = 'normal';
                }
                activeObject.set('globalCompositeOperation', 'source-over');
            }

            if (effect === 'laser-silver') {
                // 1. Quitar colores originales convirtiendo a escala de grises
                activeObject.filters.push(new fabric.Image.filters.Grayscale());
                // 2. Aplicar color Plata Metálico Sólido en modo overlay al 100%
                activeObject.filters.push(new fabric.Image.filters.BlendColor({
                    color: '#E5E5E5',
                    mode: 'overlay',
                    alpha: 1.0
                }));
                // Sombra sutil para profundidad (Efecto relieve tallado)
                activeObject.set('shadow', new fabric.Shadow({
                    color: 'rgba(0, 0, 0, 0.25)',
                    blur: 2,
                    offsetX: 1,
                    offsetY: 1
                }));
            } else if (effect === 'laser-gold') {
                // 1. Quitar colores originales
                activeObject.filters.push(new fabric.Image.filters.Grayscale());
                // 2. Aplicar Color Oro Sólido
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
                // 1. Quitar colores originales
                activeObject.filters.push(new fabric.Image.filters.Grayscale());
                // 2. Quemado oscuro profundo
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
                // Quitar filtros y sombras para original
                activeObject.set('shadow', null);
            }

            activeObject.applyFilters();
            canvas.renderAll();
        }
    </script>

</body>
</html>
