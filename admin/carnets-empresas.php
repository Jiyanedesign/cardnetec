<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Persistencia y respaldo por Cookie para servidores con sesiones estrictas
if (!isset($_SESSION['admin_logged']) && isset($_COOKIE['cardnet_admin_logged']) && $_COOKIE['cardnet_admin_logged'] === 'cardnet_auth_2026_ok') {
    $_SESSION['admin_logged'] = true;
    $_SESSION['admin_name'] = 'CardNet Admin';
}
if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit;
}
require_once '../db.php';

$message = '';
$error = '';
$upload_dir = '../uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Procesar formulario de guardado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_empresas_content'])) {
    try {
        $stmtUpsert = $pdo->prepare("INSERT INTO empresas_content (content_key, content_value) 
                                     VALUES (?, ?) 
                                     ON DUPLICATE KEY UPDATE content_value = VALUES(content_value)");

        // 1. Guardar todos los campos de texto
        foreach ($_POST as $key => $val) {
            if ($key === 'save_empresas_content' || strpos($key, 'existing_') === 0) continue;
            $stmtUpsert->execute([$key, trim($val)]);
        }

        // 2. Procesar subida de imágenes
        $image_fields = [
            'hero_image' => 'hero_image_file',
            'type_card1_img' => 'type_card1_img_file',
            'type_card2_img' => 'type_card2_img_file',
            'type_card3_img' => 'type_card3_img_file',
            'sol_1_img' => 'sol_1_img_file',
            'sol_2_img' => 'sol_2_img_file',
            'sol_3_img' => 'sol_3_img_file',
            'sol_4_img' => 'sol_4_img_file',
            'combo_1_img' => 'combo_1_img_file',
            'combo_2_img' => 'combo_2_img_file',
            'combo_3_img' => 'combo_3_img_file'
        ];

        foreach ($image_fields as $key => $file_input) {
            $img_val = $_POST['existing_' . $key] ?? '';
            if (isset($_FILES[$file_input]) && $_FILES[$file_input]['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES[$file_input]['tmp_name'];
                $file_name = $_FILES[$file_input]['name'];
                $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $new_fn = 'emp_' . $key . '_' . time() . '.' . $ext;
                    if (move_uploaded_file($file_tmp, $upload_dir . $new_fn)) {
                        $webp_fn = convertToWebP($upload_dir . $new_fn);
                        $img_val = basename($webp_fn);
                    }
                }
            }
            if (!empty($img_val)) {
                $stmtUpsert->execute([$key, $img_val]);
            }
        }

        $message = 'Contenidos de Carnets y Empresas actualizados con éxito.';
    } catch (PDOException $e) {
        $error = 'Error al guardar los contenidos: ' . $e->getMessage();
    }
}

// Cargar contenidos actuales
$emp_c = getEmpresasContent($pdo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Carnets y Empresas | CardNet Admin</title>
    <link rel="stylesheet" href="../css/base.css?v=6.3">
    <link rel="stylesheet" href="../css/layout.css?v=6.3">
    <link rel="stylesheet" href="../css/components.css?v=6.3">
    <link rel="stylesheet" href="../css/admin.css?v=6.3">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: var(--surface-light);
            font-family: 'Work Sans', sans-serif;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            background-color: var(--dark-alt);
            color: white;
            padding: 2rem 1.5rem;
            flex-shrink: 0;
            box-sizing: border-box;
        }
        .sidebar-logo {
            max-width: 140px;
            margin-bottom: 2rem;
            filter: brightness(0) invert(1);
        }
        .nav-admin {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .nav-admin-link {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            padding: 10px 15px;
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition-fast);
        }
        .nav-admin-link:hover, .nav-admin-link.active {
            color: white;
            background-color: var(--primary);
        }
        .main-content {
            flex-grow: 1;
            padding: 3rem;
            box-sizing: border-box;
            max-width: calc(100vw - 250px);
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border);
            padding-bottom: 1rem;
        }
        .admin-section-box {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
        }
        .admin-section-title {
            font-family: var(--font-heading);
            font-size: 1.25rem;
            color: var(--dark);
            border-bottom: 1px solid var(--border);
            padding-bottom: 0.75rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .form-grid-4 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
            color: var(--dark);
        }
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-size: 0.88rem;
            background: white;
            box-sizing: border-box;
        }
        .form-control:focus {
            border-color: var(--primary);
            outline: none;
        }
        textarea.form-control {
            min-height: 80px;
            resize: vertical;
        }
        .alert {
            padding: 12px 18px;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .alert-success { background-color: #DEF7EC; color: #03543F; }
        .alert-danger { background-color: #FDE8E8; color: #9B1C1C; }
        .tab-nav {
            display: flex;
            gap: 8px;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            border-bottom: 1px solid var(--border);
            padding-bottom: 1rem;
        }
        .tab-btn {
            background: white;
            border: 1px solid var(--border);
            padding: 10px 18px;
            border-radius: 20px;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-muted);
        }
        .tab-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .img-preview-box {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 8px;
            padding: 10px;
            background: var(--surface-light);
            border-radius: 6px;
            border: 1px solid var(--border);
        }
        .img-preview-box img {
            width: 80px;
            height: 55px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid var(--border);
        }
        .btn-save-fixed {
            position: sticky;
            bottom: 20px;
            z-index: 100;
            background: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid var(--border);
            margin-top: 2rem;
        }
    </style>
</head>
<body>

    <!-- Sidebar de Navegación -->
    <div class="sidebar">
        <img src="../images/logo.webp?v=2.1" alt="CardNet Logo" class="sidebar-logo">
        <nav class="nav-admin">
            <a href="index.php" class="nav-admin-link">Dashboard</a>
            <a href="carnets-empresas.php" class="nav-admin-link active">Carnets y Empresas</a>
            <a href="categorias.php" class="nav-admin-link">Categorías</a>
            <a href="secciones.php" class="nav-admin-link">Secciones Home</a>
            <a href="tagua.php" class="nav-admin-link">Tagua</a>
            <a href="etiquetas.php" class="nav-admin-link">Etiquetas</a>
            <a href="productos.php" class="nav-admin-link">Productos</a>
            <a href="materiales.php" class="nav-admin-link">Materiales</a>
            <a href="carrusel.php" class="nav-admin-link">Carrusel Hero</a>
            <a href="antes-despues.php" class="nav-admin-link">Antes y Después</a>
            <a href="clientes.php" class="nav-admin-link">Logos Clientes</a>
            <a href="credenciales.php" class="nav-admin-link">Credenciales QR</a>
            <a href="configuracion.php" class="nav-admin-link">Configuración</a>
            <a href="logout.php" class="nav-admin-link" style="margin-top: 2rem; color: #FCA5A5;">Cerrar Sesión</a>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">
        <div class="dashboard-header">
            <div>
                <h1 style="font-family: var(--font-heading); margin: 0; font-size: 2rem;">Página: Carnets y Empresas</h1>
                <p style="color: var(--text-muted); margin: 5px 0 0 0;">Personaliza todos los textos, imágenes, tarjetas y llamados a la acción de la página unificada.</p>
            </div>
            <a href="../empresas.php" target="_blank" class="btn btn-secondary">Ver Página Pública</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Navegación por Pestañas -->
        <div class="tab-nav">
            <button type="button" class="tab-btn active" onclick="switchTab('tab-hero')">1. Hero & Métricas</button>
            <button type="button" class="tab-btn" onclick="switchTab('tab-why')">2. Importancia del Carnet</button>
            <button type="button" class="tab-btn" onclick="switchTab('tab-types')">3. Tipos de Carnets</button>
            <button type="button" class="tab-btn" onclick="switchTab('tab-finishes')">4. Acabados de Taller</button>
            <button type="button" class="tab-btn" onclick="switchTab('tab-solutions')">5. Soluciones Corporativas</button>
            <button type="button" class="tab-btn" onclick="switchTab('tab-combos')">6. Combos Inteligentes</button>
            <button type="button" class="tab-btn" onclick="switchTab('tab-advantages')">7. Ventajas & CTA</button>
        </div>

        <form action="carnets-empresas.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="save_empresas_content" value="1">

            <!-- TAB 1: HERO & MÉTRICAS -->
            <div id="tab-hero" class="tab-content active">
                <div class="admin-section-box">
                    <div class="admin-section-title">
                        <span>Portada Principal (Hero)</span>
                    </div>
                    <div class="form-group">
                        <label>Insignia Superior (Badge):</label>
                        <input type="text" name="hero_badge" class="form-control" value="<?php echo htmlspecialchars($emp_c['hero_badge']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Título Principal:</label>
                        <input type="text" name="hero_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['hero_title']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Descripción Subtítulo:</label>
                        <textarea name="hero_subtitle" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['hero_subtitle']); ?></textarea>
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Texto Botón Primario:</label>
                            <input type="text" name="hero_btn1_text" class="form-control" value="<?php echo htmlspecialchars($emp_c['hero_btn1_text']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Enlace Botón Primario:</label>
                            <input type="text" name="hero_btn1_url" class="form-control" value="<?php echo htmlspecialchars($emp_c['hero_btn1_url']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Texto Botón Secundario (Asesor WhatsApp):</label>
                        <input type="text" name="hero_btn2_text" class="form-control" value="<?php echo htmlspecialchars($emp_c['hero_btn2_text']); ?>">
                    </div>
                </div>

                <div class="admin-section-box">
                    <div class="admin-section-title">
                        <span>Barra de Métricas y Estadísticas (4 Indicadores)</span>
                    </div>
                    <div class="form-grid-4">
                        <div style="background: var(--surface-light); padding: 15px; border-radius: 6px; border: 1px solid var(--border);">
                            <div class="form-group">
                                <label>Métrica 1 (Valor):</label>
                                <input type="text" name="stat_val_1" class="form-control" value="<?php echo htmlspecialchars($emp_c['stat_val_1']); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Etiqueta 1:</label>
                                <input type="text" name="stat_lbl_1" class="form-control" value="<?php echo htmlspecialchars($emp_c['stat_lbl_1']); ?>">
                            </div>
                        </div>
                        <div style="background: var(--surface-light); padding: 15px; border-radius: 6px; border: 1px solid var(--border);">
                            <div class="form-group">
                                <label>Métrica 2 (Valor):</label>
                                <input type="text" name="stat_val_2" class="form-control" value="<?php echo htmlspecialchars($emp_c['stat_val_2']); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Etiqueta 2:</label>
                                <input type="text" name="stat_lbl_2" class="form-control" value="<?php echo htmlspecialchars($emp_c['stat_lbl_2']); ?>">
                            </div>
                        </div>
                        <div style="background: var(--surface-light); padding: 15px; border-radius: 6px; border: 1px solid var(--border);">
                            <div class="form-group">
                                <label>Métrica 3 (Valor):</label>
                                <input type="text" name="stat_val_3" class="form-control" value="<?php echo htmlspecialchars($emp_c['stat_val_3']); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Etiqueta 3:</label>
                                <input type="text" name="stat_lbl_3" class="form-control" value="<?php echo htmlspecialchars($emp_c['stat_lbl_3']); ?>">
                            </div>
                        </div>
                        <div style="background: var(--surface-light); padding: 15px; border-radius: 6px; border: 1px solid var(--border);">
                            <div class="form-group">
                                <label>Métrica 4 (Valor):</label>
                                <input type="text" name="stat_val_4" class="form-control" value="<?php echo htmlspecialchars($emp_c['stat_val_4']); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Etiqueta 4:</label>
                                <input type="text" name="stat_lbl_4" class="form-control" value="<?php echo htmlspecialchars($emp_c['stat_lbl_4']); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: IMPORTANCIA DEL CARNET -->
            <div id="tab-why" class="tab-content">
                <div class="admin-section-box">
                    <div class="admin-section-title">
                        <span>Encabezado de Sección</span>
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Subtítulo / Badge:</label>
                            <input type="text" name="why_badge" class="form-control" value="<?php echo htmlspecialchars($emp_c['why_badge']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Título de la Sección:</label>
                            <input type="text" name="why_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['why_title']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Descripción de Introducción:</label>
                        <textarea name="why_subtitle" class="form-control" rows="2"><?php echo htmlspecialchars($emp_c['why_subtitle']); ?></textarea>
                    </div>
                </div>

                <div class="admin-section-box">
                    <div class="admin-section-title">
                        <span>4 Tarjetas de Valor Institucional</span>
                    </div>
                    <div class="form-grid-2">
                        <!-- Tarjeta 1 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Tarjeta 1</h4>
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="why_card1_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['why_card1_title']); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Descripción:</label>
                                <textarea name="why_card1_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['why_card1_desc']); ?></textarea>
                            </div>
                        </div>

                        <!-- Tarjeta 2 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Tarjeta 2</h4>
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="why_card2_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['why_card2_title']); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Descripción:</label>
                                <textarea name="why_card2_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['why_card2_desc']); ?></textarea>
                            </div>
                        </div>

                        <!-- Tarjeta 3 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Tarjeta 3</h4>
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="why_card3_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['why_card3_title']); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Descripción:</label>
                                <textarea name="why_card3_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['why_card3_desc']); ?></textarea>
                            </div>
                        </div>

                        <!-- Tarjeta 4 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Tarjeta 4</h4>
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="why_card4_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['why_card4_title']); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Descripción:</label>
                                <textarea name="why_card4_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['why_card4_desc']); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: TIPOS DE CARNETS -->
            <div id="tab-types" class="tab-content">
                <div class="admin-section-box">
                    <div class="admin-section-title">
                        <span>Encabezado de Formatos y Materiales</span>
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Insignia / Badge:</label>
                            <input type="text" name="types_badge" class="form-control" value="<?php echo htmlspecialchars($emp_c['types_badge']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Título de la Sección:</label>
                            <input type="text" name="types_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['types_title']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Descripción:</label>
                        <textarea name="types_subtitle" class="form-control" rows="2"><?php echo htmlspecialchars($emp_c['types_subtitle']); ?></textarea>
                    </div>
                </div>

                <div class="admin-section-box">
                    <div class="admin-section-title">
                        <span>3 Formatos de Credenciales</span>
                    </div>
                    <div class="form-grid-3">
                        <!-- Formato 1 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Formato 1</h4>
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="type_card1_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['type_card1_title']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Descripción:</label>
                                <textarea name="type_card1_desc" class="form-control" rows="4"><?php echo htmlspecialchars($emp_c['type_card1_desc']); ?></textarea>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Imagen:</label>
                                <input type="file" name="type_card1_img_file" class="form-control" accept="image/*">
                                <input type="hidden" name="existing_type_card1_img" value="<?php echo htmlspecialchars($emp_c['type_card1_img']); ?>">
                                <div class="img-preview-box">
                                    <img src="../uploads/<?php echo htmlspecialchars($emp_c['type_card1_img']); ?>" alt="Preview">
                                    <span style="font-size:0.75rem; color:var(--text-muted);"><?php echo htmlspecialchars($emp_c['type_card1_img']); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Formato 2 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Formato 2</h4>
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="type_card2_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['type_card2_title']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Descripción:</label>
                                <textarea name="type_card2_desc" class="form-control" rows="4"><?php echo htmlspecialchars($emp_c['type_card2_desc']); ?></textarea>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Imagen:</label>
                                <input type="file" name="type_card2_img_file" class="form-control" accept="image/*">
                                <input type="hidden" name="existing_type_card2_img" value="<?php echo htmlspecialchars($emp_c['type_card2_img']); ?>">
                                <div class="img-preview-box">
                                    <img src="../uploads/<?php echo htmlspecialchars($emp_c['type_card2_img']); ?>" alt="Preview">
                                    <span style="font-size:0.75rem; color:var(--text-muted);"><?php echo htmlspecialchars($emp_c['type_card2_img']); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Formato 3 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Formato 3</h4>
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="type_card3_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['type_card3_title']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Descripción:</label>
                                <textarea name="type_card3_desc" class="form-control" rows="4"><?php echo htmlspecialchars($emp_c['type_card3_desc']); ?></textarea>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Imagen:</label>
                                <input type="file" name="type_card3_img_file" class="form-control" accept="image/*">
                                <input type="hidden" name="existing_type_card3_img" value="<?php echo htmlspecialchars($emp_c['type_card3_img']); ?>">
                                <div class="img-preview-box">
                                    <img src="../uploads/<?php echo htmlspecialchars($emp_c['type_card3_img']); ?>" alt="Preview">
                                    <span style="font-size:0.75rem; color:var(--text-muted);"><?php echo htmlspecialchars($emp_c['type_card3_img']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: ACABADOS DE TALLER -->
            <div id="tab-finishes" class="tab-content">
                <div class="admin-section-box">
                    <div class="admin-section-title">
                        <span>Encabezado de Acabados de Taller</span>
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Insignia / Badge:</label>
                            <input type="text" name="finishes_badge" class="form-control" value="<?php echo htmlspecialchars($emp_c['finishes_badge']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Título:</label>
                            <input type="text" name="finishes_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['finishes_title']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Descripción:</label>
                        <textarea name="finishes_subtitle" class="form-control" rows="2"><?php echo htmlspecialchars($emp_c['finishes_subtitle']); ?></textarea>
                    </div>
                </div>

                <div class="admin-section-box">
                    <div class="admin-section-title">
                        <span>4 Opciones de Acabados y Personalización</span>
                    </div>
                    <div class="form-grid-2">
                        <!-- Acabado 1 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Acabado 1</h4>
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="finish_1_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['finish_1_title']); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Descripción:</label>
                                <textarea name="finish_1_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['finish_1_desc']); ?></textarea>
                            </div>
                        </div>

                        <!-- Acabado 2 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Acabado 2</h4>
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="finish_2_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['finish_2_title']); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Descripción:</label>
                                <textarea name="finish_2_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['finish_2_desc']); ?></textarea>
                            </div>
                        </div>

                        <!-- Acabado 3 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Acabado 3</h4>
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="finish_3_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['finish_3_title']); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Descripción:</label>
                                <textarea name="finish_3_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['finish_3_desc']); ?></textarea>
                            </div>
                        </div>

                        <!-- Acabado 4 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Acabado 4</h4>
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="finish_4_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['finish_4_title']); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Descripción:</label>
                                <textarea name="finish_4_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['finish_4_desc']); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: SOLUCIONES CORPORATIVAS -->
            <div id="tab-solutions" class="tab-content">
                <div class="admin-section-box">
                    <div class="admin-section-title">
                        <span>Encabezado de Soluciones Corporativas</span>
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Insignia / Badge:</label>
                            <input type="text" name="solutions_badge" class="form-control" value="<?php echo htmlspecialchars($emp_c['solutions_badge']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Título:</label>
                            <input type="text" name="solutions_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['solutions_title']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Descripción:</label>
                        <textarea name="solutions_subtitle" class="form-control" rows="2"><?php echo htmlspecialchars($emp_c['solutions_subtitle']); ?></textarea>
                    </div>
                </div>

                <div class="admin-section-box">
                    <div class="admin-section-title">
                        <span>4 Soluciones Integrales para Empresas</span>
                    </div>
                    <div class="form-grid-2">
                        <!-- Solución 1 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Solución 1: Credenciales PVC</h4>
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="sol_1_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_1_title']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Descripción:</label>
                                <textarea name="sol_1_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['sol_1_desc']); ?></textarea>
                            </div>
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label>Texto Botón 1:</label>
                                    <input type="text" name="sol_1_btn1_text" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_1_btn1_text']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>URL Botón 1:</label>
                                    <input type="text" name="sol_1_btn1_url" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_1_btn1_url']); ?>">
                                </div>
                            </div>
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label>Texto Botón 2:</label>
                                    <input type="text" name="sol_1_btn2_text" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_1_btn2_text']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>URL Botón 2:</label>
                                    <input type="text" name="sol_1_btn2_url" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_1_btn2_url']); ?>">
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Imagen:</label>
                                <input type="file" name="sol_1_img_file" class="form-control" accept="image/*">
                                <input type="hidden" name="existing_sol_1_img" value="<?php echo htmlspecialchars($emp_c['sol_1_img']); ?>">
                                <div class="img-preview-box">
                                    <img src="../uploads/<?php echo htmlspecialchars($emp_c['sol_1_img']); ?>" alt="Preview">
                                    <span style="font-size:0.75rem; color:var(--text-muted);"><?php echo htmlspecialchars($emp_c['sol_1_img']); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Solución 2 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Solución 2: Cintas y Lanyards</h4>
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="sol_2_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_2_title']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Descripción:</label>
                                <textarea name="sol_2_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['sol_2_desc']); ?></textarea>
                            </div>
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label>Texto Botón 1:</label>
                                    <input type="text" name="sol_2_btn1_text" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_2_btn1_text']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>URL Botón 1:</label>
                                    <input type="text" name="sol_2_btn1_url" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_2_btn1_url']); ?>">
                                </div>
                            </div>
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label>Texto Botón 2:</label>
                                    <input type="text" name="sol_2_btn2_text" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_2_btn2_text']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>URL Botón 2:</label>
                                    <input type="text" name="sol_2_btn2_url" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_2_btn2_url']); ?>">
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Imagen:</label>
                                <input type="file" name="sol_2_img_file" class="form-control" accept="image/*">
                                <input type="hidden" name="existing_sol_2_img" value="<?php echo htmlspecialchars($emp_c['sol_2_img']); ?>">
                                <div class="img-preview-box">
                                    <img src="../uploads/<?php echo htmlspecialchars($emp_c['sol_2_img']); ?>" alt="Preview">
                                    <span style="font-size:0.75rem; color:var(--text-muted);"><?php echo htmlspecialchars($emp_c['sol_2_img']); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Solución 3 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Solución 3: Accesorios para Identificación</h4>
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="sol_3_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_3_title']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Descripción:</label>
                                <textarea name="sol_3_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['sol_3_desc']); ?></textarea>
                            </div>
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label>Texto Botón 1:</label>
                                    <input type="text" name="sol_3_btn1_text" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_3_btn1_text']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>URL Botón 1:</label>
                                    <input type="text" name="sol_3_btn1_url" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_3_btn1_url']); ?>">
                                </div>
                            </div>
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label>Texto Botón 2:</label>
                                    <input type="text" name="sol_3_btn2_text" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_3_btn2_text']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>URL Botón 2:</label>
                                    <input type="text" name="sol_3_btn2_url" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_3_btn2_url']); ?>">
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Imagen:</label>
                                <input type="file" name="sol_3_img_file" class="form-control" accept="image/*">
                                <input type="hidden" name="existing_sol_3_img" value="<?php echo htmlspecialchars($emp_c['sol_3_img']); ?>">
                                <div class="img-preview-box">
                                    <img src="../uploads/<?php echo htmlspecialchars($emp_c['sol_3_img']); ?>" alt="Preview">
                                    <span style="font-size:0.75rem; color:var(--text-muted);"><?php echo htmlspecialchars($emp_c['sol_3_img']); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Solución 4 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Solución 4: Merchandising Premium</h4>
                            <div class="form-group">
                                <label>Título:</label>
                                <input type="text" name="sol_4_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_4_title']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Descripción:</label>
                                <textarea name="sol_4_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['sol_4_desc']); ?></textarea>
                            </div>
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label>Texto Botón 1:</label>
                                    <input type="text" name="sol_4_btn1_text" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_4_btn1_text']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>URL Botón 1:</label>
                                    <input type="text" name="sol_4_btn1_url" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_4_btn1_url']); ?>">
                                </div>
                            </div>
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label>Texto Botón 2:</label>
                                    <input type="text" name="sol_4_btn2_text" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_4_btn2_text']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>URL Botón 2:</label>
                                    <input type="text" name="sol_4_btn2_url" class="form-control" value="<?php echo htmlspecialchars($emp_c['sol_4_btn2_url']); ?>">
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Imagen:</label>
                                <input type="file" name="sol_4_img_file" class="form-control" accept="image/*">
                                <input type="hidden" name="existing_sol_4_img" value="<?php echo htmlspecialchars($emp_c['sol_4_img']); ?>">
                                <div class="img-preview-box">
                                    <img src="../uploads/<?php echo htmlspecialchars($emp_c['sol_4_img']); ?>" alt="Preview">
                                    <span style="font-size:0.75rem; color:var(--text-muted);"><?php echo htmlspecialchars($emp_c['sol_4_img']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 6: COMBOS INTELIGENTES -->
            <div id="tab-combos" class="tab-content">
                <div class="admin-section-box">
                    <div class="admin-section-title">
                        <span>Encabezado de Combos y Paquetes</span>
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Insignia / Badge:</label>
                            <input type="text" name="combos_badge" class="form-control" value="<?php echo htmlspecialchars($emp_c['combos_badge']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Título:</label>
                            <input type="text" name="combos_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['combos_title']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Descripción:</label>
                        <textarea name="combos_subtitle" class="form-control" rows="2"><?php echo htmlspecialchars($emp_c['combos_subtitle']); ?></textarea>
                    </div>
                </div>

                <div class="admin-section-box">
                    <div class="admin-section-title">
                        <span>3 Combos Inteligentes Promocionales</span>
                    </div>
                    <div class="form-grid-3">
                        <!-- Combo 1 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Combo 1: Básico</h4>
                            <div class="form-group">
                                <label>Etiqueta / Tag:</label>
                                <input type="text" name="combo_1_tag" class="form-control" value="<?php echo htmlspecialchars($emp_c['combo_1_tag']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Título del Combo:</label>
                                <input type="text" name="combo_1_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['combo_1_title']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Descripción:</label>
                                <textarea name="combo_1_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['combo_1_desc']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Lista de productos (uno por línea):</label>
                                <textarea name="combo_1_items" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['combo_1_items']); ?></textarea>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Imagen:</label>
                                <input type="file" name="combo_1_img_file" class="form-control" accept="image/*">
                                <input type="hidden" name="existing_combo_1_img" value="<?php echo htmlspecialchars($emp_c['combo_1_img']); ?>">
                                <div class="img-preview-box">
                                    <img src="../uploads/<?php echo htmlspecialchars($emp_c['combo_1_img']); ?>" alt="Preview">
                                    <span style="font-size:0.75rem; color:var(--text-muted);"><?php echo htmlspecialchars($emp_c['combo_1_img']); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Combo 2 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Combo 2: Ejecutivo</h4>
                            <div class="form-group">
                                <label>Etiqueta / Tag:</label>
                                <input type="text" name="combo_2_tag" class="form-control" value="<?php echo htmlspecialchars($emp_c['combo_2_tag']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Título del Combo:</label>
                                <input type="text" name="combo_2_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['combo_2_title']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Descripción:</label>
                                <textarea name="combo_2_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['combo_2_desc']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Lista de productos (uno por línea):</label>
                                <textarea name="combo_2_items" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['combo_2_items']); ?></textarea>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Imagen:</label>
                                <input type="file" name="combo_2_img_file" class="form-control" accept="image/*">
                                <input type="hidden" name="existing_combo_2_img" value="<?php echo htmlspecialchars($emp_c['combo_2_img']); ?>">
                                <div class="img-preview-box">
                                    <img src="../uploads/<?php echo htmlspecialchars($emp_c['combo_2_img']); ?>" alt="Preview">
                                    <span style="font-size:0.75rem; color:var(--text-muted);"><?php echo htmlspecialchars($emp_c['combo_2_img']); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Combo 3 -->
                        <div style="background: var(--surface-light); padding: 20px; border-radius: 6px; border: 1px solid var(--border);">
                            <h4 style="margin-top:0; color: var(--primary);">Combo 3: Premium</h4>
                            <div class="form-group">
                                <label>Etiqueta / Tag:</label>
                                <input type="text" name="combo_3_tag" class="form-control" value="<?php echo htmlspecialchars($emp_c['combo_3_tag']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Título del Combo:</label>
                                <input type="text" name="combo_3_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['combo_3_title']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Descripción:</label>
                                <textarea name="combo_3_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['combo_3_desc']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Lista de productos (uno por línea):</label>
                                <textarea name="combo_3_items" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['combo_3_items']); ?></textarea>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Imagen:</label>
                                <input type="file" name="combo_3_img_file" class="form-control" accept="image/*">
                                <input type="hidden" name="existing_combo_3_img" value="<?php echo htmlspecialchars($emp_c['combo_3_img']); ?>">
                                <div class="img-preview-box">
                                    <img src="../uploads/<?php echo htmlspecialchars($emp_c['combo_3_img']); ?>" alt="Preview">
                                    <span style="font-size:0.75rem; color:var(--text-muted);"><?php echo htmlspecialchars($emp_c['combo_3_img']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 7: VENTAJAS & CTA -->
            <div id="tab-advantages" class="tab-content">
                <div class="admin-section-box">
                    <div class="admin-section-title">
                        <span>Sección: Ventajas Corporativas</span>
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Insignia / Badge:</label>
                            <input type="text" name="adv_badge" class="form-control" value="<?php echo htmlspecialchars($emp_c['adv_badge']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Título:</label>
                            <input type="text" name="adv_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['adv_title']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Descripción:</label>
                        <textarea name="adv_subtitle" class="form-control" rows="2"><?php echo htmlspecialchars($emp_c['adv_subtitle']); ?></textarea>
                    </div>

                    <div class="form-grid-3" style="margin-top: 1.5rem;">
                        <div style="background: var(--surface-light); padding: 15px; border-radius: 6px; border: 1px solid var(--border);">
                            <div class="form-group">
                                <label>Ventaja 1 (Título):</label>
                                <input type="text" name="adv_1_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['adv_1_title']); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Descripción:</label>
                                <textarea name="adv_1_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['adv_1_desc']); ?></textarea>
                            </div>
                        </div>

                        <div style="background: var(--surface-light); padding: 15px; border-radius: 6px; border: 1px solid var(--border);">
                            <div class="form-group">
                                <label>Ventaja 2 (Título):</label>
                                <input type="text" name="adv_2_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['adv_2_title']); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Descripción:</label>
                                <textarea name="adv_2_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['adv_2_desc']); ?></textarea>
                            </div>
                        </div>

                        <div style="background: var(--surface-light); padding: 15px; border-radius: 6px; border: 1px solid var(--border);">
                            <div class="form-group">
                                <label>Ventaja 3 (Título):</label>
                                <input type="text" name="adv_3_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['adv_3_title']); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Descripción:</label>
                                <textarea name="adv_3_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['adv_3_desc']); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="admin-section-box">
                    <div class="admin-section-title">
                        <span>Llamado a la Acción Final (CTA)</span>
                    </div>
                    <div class="form-group">
                        <label>Insignia / Badge:</label>
                        <input type="text" name="cta_badge" class="form-control" value="<?php echo htmlspecialchars($emp_c['cta_badge']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Título:</label>
                        <input type="text" name="cta_title" class="form-control" value="<?php echo htmlspecialchars($emp_c['cta_title']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Descripción:</label>
                        <textarea name="cta_desc" class="form-control" rows="3"><?php echo htmlspecialchars($emp_c['cta_desc']); ?></textarea>
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Texto Botón Primario (Cotización):</label>
                            <input type="text" name="cta_btn1_text" class="form-control" value="<?php echo htmlspecialchars($emp_c['cta_btn1_text']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Texto Botón Secundario (Asesor):</label>
                            <input type="text" name="cta_btn2_text" class="form-control" value="<?php echo htmlspecialchars($emp_c['cta_btn2_text']); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTÓN GUARDAR FLOTANTE / STICKY -->
            <div class="btn-save-fixed">
                <span style="font-size: 0.9rem; color: var(--text-muted);">Los cambios se aplicarán inmediatamente a la página pública <strong>Carnets y Empresas</strong>.</span>
                <button type="submit" class="btn btn-primary" style="padding: 12px 32px; font-weight: 600; font-size: 0.95rem; text-transform: none;">
                    💾 Guardar Todos los Cambios
                </button>
            </div>
        </form>
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            
            const btn = Array.from(document.querySelectorAll('.tab-btn')).find(b => b.getAttribute('onclick').includes(tabId));
            if (btn) btn.classList.add('active');
            
            const content = document.getElementById(tabId);
            if (content) content.classList.add('active');
        }
    </script>
</body>
</html>
