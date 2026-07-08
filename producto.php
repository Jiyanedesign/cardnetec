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
    <link rel="stylesheet" href="css/base.css?v=2.0">
    <link rel="stylesheet" href="css/layout.css?v=2.0">
    <link rel="stylesheet" href="css/components.css?v=2.0">
    <link rel="stylesheet" href="css/pages.css?v=2.0">
    <link rel="stylesheet" href="css/animations.css?v=1.1.2">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Fabric.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

    <style>
        .preview-tabs .btn {
            background-color: white;
            border: 1px solid var(--border) !important;
            color: var(--dark) !important;
            padding: 8px 16px !important;
            font-size: 0.72rem !important;
            letter-spacing: 0.05em !important;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02) !important;
            transition: all 0.2s ease !important;
        }
        .preview-tabs .btn.active {
            background-color: var(--primary) !important;
            color: white !important;
            border-color: var(--primary) !important;
            box-shadow: 0 4px 10px rgba(99, 174, 44, 0.2) !important;
        }
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
    <!-- Google <model-viewer> para visualizar archivos 3D subidos -->
    <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>

    <!-- Three.js y OrbitControls para Vista 3D Realista -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
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
                <div class="header-bottom-actions" style="display: flex; align-items: center; gap: 15px;">
                    <!-- Icono de Carrito Flotante con Dropdown -->
                    <div class="header-cart-dropdown-wrapper">
                        <a href="cotizacion.php" class="cart-icon-btn" aria-label="Ver cotización">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                            <?php if ($c_count > 0): ?>
                                <span class="cart-badge-count"><?php echo $c_count; ?></span>
                            <?php endif; ?>
                        </a>
                        
                        <?php if ($c_count > 0): ?>
                            <div class="minicart-dropdown">
                                <div class="minicart-items">
                                    <?php 
                                    $m_total = 0;
                                    foreach ($_SESSION['cart'] as $m_item): 
                                        $m_total += $m_item['subtotal'];
                                    ?>
                                        <div class="minicart-item">
                                            <?php if (!empty($m_item['snapshot'])): ?>
                                                <img src="<?php echo htmlspecialchars($m_item['snapshot']); ?>" width="50" height="50" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0; display: block; flex-shrink: 0;">
                                            <?php else: ?>
                                                <div class="minicart-item-img" style="background:#f4f4f4; display:flex; align-items:center; justify-content:center; font-size:0.6rem; color:#888; width: 50px; height: 50px; flex-shrink: 0; border-radius: 4px;">Liso</div>
                                            <?php endif; ?>
                                            <div class="minicart-item-info">
                                                <span class="minicart-item-name"><?php echo htmlspecialchars($m_item['name']); ?></span>
                                                <span class="minicart-item-meta"><?php echo $m_item['qty']; ?> uds x $<?php echo number_format($m_item['price'], 2); ?></span>
                                            </div>
                                            <div class="minicart-item-subtotal">
                                                $<?php echo number_format($m_item['subtotal'], 0); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="minicart-total">
                                    <span class="minicart-total-label">Total Est.</span>
                                    <span class="minicart-total-value">$<?php echo number_format($m_total, 2); ?></span>
                                </div>
                                <a href="cotizacion.php" class="btn btn-primary" style="width:100%; text-align:center; padding:10px 0; font-size:0.8rem; text-decoration:none;">Finalizar Cotización</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Botón Cotizar Principal -->
                    <a href="cotizacion.php" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">SOLICITAR COTIZACIÓN</a>
                </div>
            </div>
        </div>
    </header>

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
                    <!-- Pestañas de Vista 2D/3D -->
                    <div class="preview-tabs" style="display: flex; gap: 8px; margin-bottom: 12px; justify-content: center;">
                        <button class="btn" id="tab-2d" style="padding: 6px 14px; font-size: 0.75rem; font-weight: 600; border-radius: 30px; border: 1px solid var(--border); background: white; color: var(--dark); cursor: pointer; text-transform: uppercase;">
                            Visualizador 2D (Editor)
                        </button>
                        <button class="btn" id="tab-3d" style="padding: 6px 14px; font-size: 0.75rem; font-weight: 600; border-radius: 30px; border: 1px solid var(--border); background: white; color: var(--dark); cursor: pointer; display: flex; align-items: center; gap: 6px; text-transform: uppercase;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            </svg>
                            Vista 3D Interactiva
                        </button>
                    </div>

                    <div class="canvas-container-outer">
                        <canvas id="canvas-simulator" width="500" height="500"></canvas>
                    </div>

                    <!-- Contenedor del Visor 3D de Three.js / <model-viewer> -->
                    <div id="canvas-3d-container" style="display: none; width: 100%; aspect-ratio: 1; background: radial-gradient(circle at center, #ffffff 0%, #eeeeee 100%); border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; position: relative; box-sizing: border-box;">
                        
                        <?php if (!empty($product['model_3d'])): ?>
                            <!-- Visor 3D Avanzado de Google <model-viewer> -->
                            <model-viewer
                                id="google-model-viewer"
                                src="uploads/<?php echo htmlspecialchars($product['model_3d']); ?>"
                                alt="Modelo 3D de <?php echo htmlspecialchars($product['name']); ?>"
                                camera-controls
                                auto-rotate
                                rotation-per-second="25deg"
                                shadow-intensity="1.4"
                                exposure="1.1"
                                environment-image="neutral"
                                style="width: 100%; height: 100%;">
                            </model-viewer>
                        <?php else: ?>
                            <!-- Si no hay archivo 3D, cae al simulador Three.js de repuesto -->
                            <div style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); font-size: 0.7rem; color: var(--text-muted); pointer-events: none; background: rgba(255,255,255,0.8); padding: 4px 10px; border-radius: 20px; font-weight: 600; white-space: nowrap; z-index: 10;">
                                🖱️ Arrastra para rotar · Rueda para zoom
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Mensaje informativo e instructivo del Visor 3D -->
                    <div id="info-visor-3d" style="display: none; margin-top: 10px; text-align: center; font-size: 0.8rem; color: var(--text-muted); line-height: 1.4;">
                        <?php if (!empty($product['model_3d'])): ?>
                            Visualiza el producto en 3D, gíralo y observa sus detalles antes de personalizarlo.
                        <?php else: ?>
                            Este producto aún no tiene un modelo 3D dedicado disponible. Se muestra una simulación realista estándar.
                        <?php endif; ?>
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
                <a href="cotizacion.php" class="btn btn-primary" style="width:100%; text-align:center; padding:12px; font-weight:600;">Ver Lista y SOLICITAR COTIZACIÓN</a>
                <button onclick="document.getElementById('cart-modal').style.display='none'" class="btn btn-secondary" style="width:100%; padding:12px; font-weight:600; border:1px solid var(--border);">Seguir Diseñando</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container footer-top section-padding">
            <div class="footer-grid">
                <div class="footer-brand-column">
                    <a href="index.php" class="logo footer-logo" aria-label="CardNet.ec Inicio">
                        <img src="images/logo.png?v=2.0" alt="CardNet.ec Logo" class="logo-img">
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
                        
                        // Sincronizar el color del objeto 3D con la nueva miniatura cargada
                        if (is3DInitialized) {
                            extractProductColorAndApply();
                        }
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
        // ==========================================
        // SISTEMA DE VISUALIZACIÓN 3D CON THREE.JS
        // ==========================================
        const tab2d = document.getElementById('tab-2d');
        const tab3d = document.getElementById('tab-3d');
        const container2d = document.querySelector('.canvas-container-outer');
        const container3d = document.getElementById('canvas-3d-container');

        let scene3D, camera3D, renderer3D, controls3D, productMesh, logoTexture;
        let is3DInitialized = false;

        const productSlug = '<?php echo $product['slug']; ?>';
        const productCategory = '<?php echo addslashes($product['category_name']); ?>';

        function getUploadedLogoImage() {
            if (!canvas) return null;
            const objects = canvas.getObjects();
            for (let i = 0; i < objects.length; i++) {
                if (objects[i].type === 'image' && objects[i] !== canvas.backgroundImage) {
                    return objects[i];
                }
            }
            return null;
        }

        let productGroup3D, decalMesh;

        // Función para extraer color del producto desde la imagen activa del simulador
        function extractProductColorAndApply() {
            if (!is3DInitialized || !productMesh) return;

            const activeThumb = document.querySelector('.thumbnail-item.active img');
            const imgSrc = activeThumb ? activeThumb.src : mainImgPath;
            
            const tempImg = new Image();
            tempImg.crossOrigin = "Anonymous";
            tempImg.src = imgSrc;
            tempImg.onload = function() {
                const tempCanvas = document.createElement('canvas');
                const ctx = tempCanvas.getContext('2d');
                tempCanvas.width = 10;
                tempCanvas.height = 10;
                ctx.drawImage(tempImg, 0, 0, 10, 10);
                
                // Muestrear centro de la imagen (color predominante del producto)
                const centerData = ctx.getImageData(5, 5, 1, 1).data;
                let r = centerData[0], g = centerData[1], b = centerData[2];
                
                // Si es blanco de fondo (>230), buscar en el lateral
                if (r > 230 && g > 230 && b > 230) {
                    const sideData = ctx.getImageData(2, 5, 1, 1).data;
                    r = sideData[0];
                    g = sideData[1];
                    b = sideData[2];
                }
                
                // Si sigue siendo muy claro, usar un gris/blanco satinado por defecto
                if (r > 240 && g > 240 && b > 240) {
                    r = 230; g = 230; b = 230;
                }

                if (productMesh && productMesh.material) {
                    productMesh.material.color.setRGB(r / 255, g / 255, b / 255);
                    productMesh.material.needsUpdate = true;
                }
            };
        }

        function initThreeJS() {
            if (is3DInitialized) return;

            const width = container3d.clientWidth || 500;
            const height = container3d.clientHeight || 500;

            // Escena
            scene3D = new THREE.Scene();
            scene3D.background = new THREE.Color(0xf8fafc);

            // Cámara
            camera3D = new THREE.PerspectiveCamera(40, width / height, 0.1, 100);
            camera3D.position.set(0, 1.5, 9);

            // Renderizador
            renderer3D = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            renderer3D.setSize(width, height);
            renderer3D.setPixelRatio(window.devicePixelRatio);
            renderer3D.shadowMap.enabled = true;
            renderer3D.toneMapping = THREE.ACESFilmicToneMapping;
            renderer3D.toneMappingExposure = 1.25;
            container3d.appendChild(renderer3D.domElement);

            // Controles de órbita
            controls3D = new THREE.OrbitControls(camera3D, renderer3D.domElement);
            controls3D.enableDamping = true;
            controls3D.dampingFactor = 0.05;
            controls3D.maxPolarAngle = Math.PI / 1.8;
            controls3D.minPolarAngle = Math.PI / 3;
            controls3D.minDistance = 4;
            controls3D.maxDistance = 15;

            // Iluminación de Estudio Premium
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
            scene3D.add(ambientLight);

            const keyLight = new THREE.DirectionalLight(0xffffff, 1.0);
            keyLight.position.set(5, 8, 5);
            scene3D.add(keyLight);

            const fillLight = new THREE.DirectionalLight(0xffffff, 0.4);
            fillLight.position.set(-5, 3, 2);
            scene3D.add(fillLight);

            const rimLight = new THREE.DirectionalLight(0xffffff, 0.6);
            rimLight.position.set(0, 5, -5);
            scene3D.add(rimLight);

            productGroup3D = new THREE.Group();

            const isTermo = productSlug.includes('termo') || productCategory.toLowerCase().includes('termo') || productCategory.toLowerCase().includes('vaso');
            const isAgenda = productSlug.includes('agenda') || productCategory.toLowerCase().includes('agenda') || productCategory.toLowerCase().includes('libreta');
            const isCaja = productSlug.includes('caja') || productCategory.toLowerCase().includes('caja') || productCategory.toLowerCase().includes('kit');
            const isPlaca = productSlug.includes('placa') || productCategory.toLowerCase().includes('placa') || productCategory.toLowerCase().includes('carnet');

            // Construir Modelos Realistas y Detallados
            if (isTermo) {
                // Termo Premium con Tapa y Cuerpo
                const bodyGeo = new THREE.CylinderGeometry(1.05, 1.05, 3.8, 64);
                const bodyMat = new THREE.MeshStandardMaterial({
                    color: 0xdddddd, // Será reemplazado por la extracción de color
                    metalness: 0.15,
                    roughness: 0.55
                });
                productMesh = new THREE.Mesh(bodyGeo, bodyMat);
                productMesh.position.y = -0.4;
                productGroup3D.add(productMesh);

                // Cuello y Anillo Cromado
                const neckGeo = new THREE.CylinderGeometry(0.9, 1.05, 0.4, 64);
                const neckMat = new THREE.MeshStandardMaterial({
                    color: 0xdddddd,
                    metalness: 0.9,
                    roughness: 0.1
                });
                const neck = new THREE.Mesh(neckGeo, neckMat);
                neck.position.y = 1.7;
                productGroup3D.add(neck);

                // Tapa Plástica con Asa
                const capGeo = new THREE.CylinderGeometry(0.9, 0.9, 0.6, 64);
                const capMat = new THREE.MeshStandardMaterial({
                    color: 0x111111,
                    metalness: 0.1,
                    roughness: 0.5
                });
                const cap = new THREE.Mesh(capGeo, capMat);
                cap.position.y = 2.2;
                productGroup3D.add(cap);

                // Malla Decal (Superficie de Calcomanía para evitar estiramientos)
                const decalGeo = new THREE.CylinderGeometry(1.055, 1.055, 2.0, 64, 1, true, -Math.PI/2, Math.PI);
                const decalMat = new THREE.MeshStandardMaterial({
                    transparent: true,
                    depthWrite: false,
                    roughness: 0.55,
                    metalness: 0.15
                });
                decalMesh = new THREE.Mesh(decalGeo, decalMat);
                decalMesh.position.y = -0.4;
                productGroup3D.add(decalMesh);

            } else if (isAgenda) {
                // Agenda con lomo y hojas
                const coverGeo = new THREE.BoxGeometry(3.0, 4.2, 0.35);
                const coverMat = new THREE.MeshStandardMaterial({
                    color: 0x18181b, 
                    metalness: 0.05,
                    roughness: 0.85
                });
                productMesh = new THREE.Mesh(coverGeo, coverMat);
                productGroup3D.add(productMesh);

                // Bloque de Hojas internas (Blanco)
                const pagesGeo = new THREE.BoxGeometry(2.9, 4.1, 0.3);
                const pagesMat = new THREE.MeshStandardMaterial({
                    color: 0xf8fafc,
                    metalness: 0.0,
                    roughness: 0.9
                });
                const pages = new THREE.Mesh(pagesGeo, pagesMat);
                pages.position.x = 0.03;
                productGroup3D.add(pages);

                // Malla Decal sobre la portada
                const decalGeo = new THREE.PlaneGeometry(2.2, 3.2);
                const decalMat = new THREE.MeshStandardMaterial({
                    transparent: true,
                    depthWrite: false,
                    roughness: 0.85,
                    metalness: 0.05
                });
                decalMesh = new THREE.Mesh(decalGeo, decalMat);
                decalMesh.position.set(0, 0, 0.18);
                productGroup3D.add(decalMesh);

            } else if (isCaja) {
                // Caja de Madera fina
                const boxGeo = new THREE.BoxGeometry(4.2, 3.0, 2.4);
                const boxMat = new THREE.MeshStandardMaterial({
                    color: 0xb45309, 
                    metalness: 0.0,
                    roughness: 0.75
                });
                productMesh = new THREE.Mesh(boxGeo, boxMat);
                productGroup3D.add(productMesh);

                // Malla Decal sobre la tapa
                const decalGeo = new THREE.PlaneGeometry(3.2, 2.0);
                const decalMat = new THREE.MeshStandardMaterial({
                    transparent: true,
                    depthWrite: false,
                    roughness: 0.75,
                    metalness: 0.0
                });
                decalMesh = new THREE.Mesh(decalGeo, decalMat);
                decalMesh.position.set(0, 1.505, 0);
                decalMesh.rotation.x = -Math.PI / 2;
                productGroup3D.add(decalMesh);

            } else if (isPlaca) {
                // Placa de Acrílico sobre Base de Madera
                const plateGeo = new THREE.BoxGeometry(3.0, 4.0, 0.2);
                const plateMat = new THREE.MeshPhysicalMaterial({
                    color: 0xffffff,
                    metalness: 0.0,
                    roughness: 0.05,
                    transmission: 0.95,
                    opacity: 1,
                    transparent: true
                });
                const plate = new THREE.Mesh(plateGeo, plateMat);
                plate.position.y = 0.5;
                productGroup3D.add(plate);

                // Base de Madera
                const baseGeo = new THREE.BoxGeometry(3.4, 0.4, 1.2);
                const baseMat = new THREE.MeshStandardMaterial({
                    color: 0x78350f,
                    metalness: 0.1,
                    roughness: 0.8
                });
                const base = new THREE.Mesh(baseGeo, baseMat);
                base.position.y = -1.6;
                productGroup3D.add(base);

                // Malla Decal sobre el acrílico
                const decalGeo = new THREE.PlaneGeometry(2.4, 3.2);
                const decalMat = new THREE.MeshStandardMaterial({
                    transparent: true,
                    depthWrite: false,
                    roughness: 0.05,
                    metalness: 0.0
                });
                decalMesh = new THREE.Mesh(decalGeo, decalMat);
                decalMesh.position.set(0, 0.5, 0.11);
                productGroup3D.add(decalMesh);

                productMesh = plate; // Referencia de color al acrílico (por si cambia)

            } else {
                // Objeto Genérico Cilíndrico Refinado
                const geo = new THREE.CylinderGeometry(1.1, 1.1, 4, 32);
                const mat = new THREE.MeshStandardMaterial({ color: 0x475569, roughness: 0.4 });
                productMesh = new THREE.Mesh(geo, mat);
                productGroup3D.add(productMesh);

                const decalGeo = new THREE.CylinderGeometry(1.105, 1.105, 2.5, 32, 1, true, -Math.PI/2, Math.PI);
                const decalMat = new THREE.MeshStandardMaterial({ transparent: true, depthWrite: false });
                decalMesh = new THREE.Mesh(decalGeo, decalMat);
                productGroup3D.add(decalMesh);
            }

            scene3D.add(productGroup3D);

            is3DInitialized = true;
            extractProductColorAndApply();
            animate3D();
        }

        function update3DTexture() {
            if (!is3DInitialized || !decalMesh) return;

            const logo = getUploadedLogoImage();
            if (logo) {
                const imgElement = logo.getElement();
                const loader = new THREE.TextureLoader();
                loader.load(imgElement.src, function(texture) {
                    texture.minFilter = THREE.LinearFilter;
                    texture.wrapS = THREE.ClampToEdgeWrapping;
                    texture.wrapT = THREE.ClampToEdgeWrapping;

                    // Centrar y ajustar escala del logo en la calcomanía
                    texture.repeat.set(1, 1);
                    texture.offset.set(0, 0);

                    decalMesh.material.map = texture;

                    // Aplicar efectos visuales premium de grabado
                    const effect = document.getElementById('logo-effect').value;
                    if (effect === 'laser-silver') {
                        decalMesh.material.color.setHex(0xe2e8f0);
                        decalMesh.material.metalness = 0.95;
                        decalMesh.material.roughness = 0.15;
                    } else if (effect === 'laser-gold') {
                        decalMesh.material.color.setHex(0xf59e0b);
                        decalMesh.material.metalness = 0.95;
                        decalMesh.material.roughness = 0.15;
                    } else if (effect === 'deboss') {
                        decalMesh.material.color.setHex(0x18181b);
                        decalMesh.material.metalness = 0.05;
                        decalMesh.material.roughness = 0.95;
                    } else {
                        decalMesh.material.color.setHex(0xffffff);
                        decalMesh.material.metalness = 0.1;
                        decalMesh.material.roughness = 0.5;
                    }

                    decalMesh.material.needsUpdate = true;
                });
            } else {
                decalMesh.material.map = null;
                decalMesh.material.needsUpdate = true;
            }
        }

        function animate3D() {
            requestAnimationFrame(animate3D);
            controls3D.update();
            renderer3D.render(scene3D, camera3D);
        }

        const infoVisor = document.getElementById('info-visor-3d');
        const hasCustomModel = <?php echo !empty($product['model_3d']) ? 'true' : 'false'; ?>;

        tab2d.addEventListener('click', () => {
            tab2d.classList.add('active');
            tab3d.classList.remove('active');
            container2d.style.display = 'block';
            container3d.style.display = 'none';
            if (infoVisor) infoVisor.style.display = 'none';
        });

        tab3d.addEventListener('click', () => {
            tab3d.classList.add('active');
            tab2d.classList.remove('active');
            container2d.style.display = 'none';
            container3d.style.display = 'block';
            if (infoVisor) infoVisor.style.display = 'block';
            
            if (!hasCustomModel) {
                initThreeJS();
                update3DTexture();
            }
        });

        document.getElementById('logo-effect').addEventListener('change', update3DTexture);
        document.getElementById('logo-opacity').addEventListener('input', update3DTexture);
        
        // Agregar manejador para actualizar la textura al presionar la pestaña 3D
        
    </script>
</body>
</html>
