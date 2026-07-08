<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $company = isset($_POST['company']) ? trim($_POST['company']) : '';
    $whatsapp = trim($_POST['whatsapp']);
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    
    if (empty($cart)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'El carrito está vacío.']);
        exit;
    }

    // Crear carpeta uploads si no existe
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Procesar logotipo adjunto
    $logo_filename = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['logo']['tmp_name'];
        $file_name = $_FILES['logo']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'svg', 'webp'])) {
            $new_filename = 'logo_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($file_tmp, $upload_dir . $new_filename)) {
                $logo_filename = $new_filename;
            }
        }
    }

    // Procesar los snapshots de Canvas dentro de cada ítem de carrito
    // y guardarlos físicamente en el servidor
    $total_qty = 0;
    $product_summary_parts = [];
    $main_simulation_path = null;

    foreach ($cart as $index => &$item) {
        $total_qty += $item['qty'];
        $product_summary_parts[] = $item['name'] . " (" . $item['qty'] . " uds)";

        if (!empty($item['snapshot'])) {
            $base64Image = $item['snapshot'];
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                $type = strtolower($type[1]);

                if (in_array($type, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $base64Image = base64_decode($base64Image);
                    if ($base64Image !== false) {
                        $new_filename = 'canvas_' . time() . '_' . uniqid() . '.' . $type;
                        file_put_contents($upload_dir . $new_filename, $base64Image);
                        
                        // Guardar la ruta física del archivo subido en el ítem
                        $item['simulation_file'] = $new_filename;
                        
                        // Usar el primer snapshot como principal de la cotización
                        if ($main_simulation_path === null) {
                            $main_simulation_path = $new_filename;
                        }
                    }
                }
            }
        }
    }
    unset($item); // romper referencia

    $product_summary = implode(", ", $product_summary_parts);
    $products_json = json_encode($cart);

    // Insertar en la base de datos solicitudes
    try {
        $stmt = $pdo->prepare("INSERT INTO solicitudes (name, company, whatsapp, email, qty, message, product_name, logo_path, simulation_path, products_json, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Nuevo')");
        $stmt->execute([$name, $company, $whatsapp, $email, $total_qty, $message, $product_summary, $logo_filename, $main_simulation_path, $products_json]);
    } catch (PDOException $e) {
        // Ignorar o registrar error
    }

    // Obtener número de WhatsApp de configuración del sitio
    $settings = getSiteSettings($pdo);
    $target_phone = !empty($settings['whatsapp']) ? preg_replace('/[^0-9]/', '', $settings['whatsapp']) : '593900000000';

    // Construir mensaje estructurado para WhatsApp
    $text = "💼 *NUEVA SOLICITUD DE COTIZACIÓN empresas*\n\n";
    $text .= "👤 *Cliente:* " . $name . "\n";
    if ($company) {
        $text .= "🏢 *Empresa:* " . $company . "\n";
    }
    $text .= "📱 *WhatsApp:* " . $whatsapp . "\n";
    if ($email) {
        $text .= "✉️ *Correo:* " . $email . "\n";
    }
    $text .= "\n📦 *Resumen del Pedido:*\n";
    
    $grand_total = 0;
    foreach ($cart as $item) {
        $text .= "• " . $item['name'] . " - " . $item['qty'] . " uds. (Subtotal: $" . number_format($item['subtotal'], 2) . ")\n";
        $grand_total += $item['subtotal'];
    }

    $text .= "\n💰 *Presupuesto Estimado:* $" . number_format($grand_total, 2) . "\n";
    
    if ($message) {
        $text .= "\n📝 *Notas:* " . $message . "\n";
    }

    if ($main_simulation_path) {
        $text .= "\n🎨 _Adjunto los renders y maquetas de simulación generadas en el taller en línea._";
    }

    $whatsappUrl = "https://wa.me/" . $target_phone . "?text=" . urlencode($text);

    // Vaciar el carrito de la sesión
    $_SESSION['cart'] = [];

    // Responder
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'redirect_url' => $whatsappUrl
    ]);
    exit;
}
