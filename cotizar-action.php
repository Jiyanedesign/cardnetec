<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $company = isset($_POST['company']) ? trim($_POST['company']) : '';
    $whatsapp = trim($_POST['whatsapp']);
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 50;
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';

    $logo_filename = null;
    $simulation_filename = null;

    // Crear carpeta uploads si no existe
    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }

    // Procesar logotipo adjunto
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['logo']['tmp_name'];
        $file_name = $_FILES['logo']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'svg', 'webp'])) {
            $new_filename = 'logo_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($file_tmp, 'uploads/' . $new_filename)) {
                $logo_filename = $new_filename;
            }
        }
    }

    // Procesar snapshot de Canvas (Base64)
    if (!empty($_POST['simulation_snapshot'])) {
        $base64Image = $_POST['simulation_snapshot'];
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $type = strtolower($type[1]);

            if (in_array($type, ['jpg', 'jpeg', 'png', 'webp'])) {
                $base64Image = base64_decode($base64Image);
                if ($base64Image !== false) {
                    $new_filename = 'canvas_' . time() . '_' . uniqid() . '.' . $type;
                    file_put_contents('uploads/' . $new_filename, $base64Image);
                    $simulation_filename = $new_filename;
                }
            }
        }
    }

    // Insertar en la base de datos
    try {
        $stmt = $pdo->prepare("INSERT INTO solicitudes (name, company, whatsapp, email, qty, message, product_name, logo_path, simulation_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $company, $whatsapp, $email, $qty, $message, $product_name, $logo_filename, $simulation_filename]);
    } catch (PDOException $e) {
        // Ignorar o loggear error de inserción
    }

    // Armar enlace de WhatsApp
    $phone = "593900000000"; // Número oficial de CardNet.ec
    
    $text = "Hola CardNet.ec, he enviado una cotización desde el sitio web:\n\n";
    $text .= "*Nombre:* " . $name . "\n";
    if ($company) $text .= "*Empresa:* " . $company . "\n";
    $text .= "*WhatsApp:* " . $whatsapp . "\n";
    if ($product_name) $text .= "*Producto:* " . $product_name . "\n";
    $text .= "*Cantidad:* " . $qty . " unidades\n";
    if ($message) $text .= "*Mensaje:* " . $message . "\n";

    if ($simulation_filename) {
        $text .= "\n_Adjunto simulación realizada en el Canvas interactivo._";
    }

    $whatsappUrl = "https://wa.me/" . $phone . "?text=" . urlencode($text);

    // Responder con JSON para redirección fluida
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'redirect_url' => $whatsappUrl
    ]);
    exit;
}
