<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $company = isset($_POST['company']) ? trim($_POST['company']) : '';
    $whatsapp = isset($_POST['whatsapp']) ? trim($_POST['whatsapp']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    
    if (empty($cart)) {
        if (isset($_POST['custom_product']) && !empty($_POST['custom_product'])) {
            $cart = [
                [
                    'name' => trim($_POST['custom_product']) . (isset($_POST['custom_type']) && $_POST['custom_type'] !== '' ? ' (' . trim($_POST['custom_type']) . ')' : ''),
                    'slug' => 'custom',
                    'qty' => !empty($_POST['custom_qty']) ? (int)$_POST['custom_qty'] : 1,
                    'price' => 0.0,
                    'snapshot' => '',
                    'subtotal' => 0.0
                ]
            ];
        } elseif (!empty($message)) {
            // Requerimiento originado desde el formulario de contacto web
            $cart = [
                [
                    'name' => 'Consulta de Contacto Web',
                    'slug' => 'contacto',
                    'qty' => 1,
                    'price' => 0.0,
                    'snapshot' => '',
                    'subtotal' => 0.0
                ]
            ];
        }
    }
    
    if (empty($cart)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'El carrito o requerimiento está vacío.']);
        exit;
    }

    // Recalcular precios de volumen en el backend
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
    }
    unset($item);

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

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'svg', 'webp', 'pdf', 'ai', 'eps'])) {
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
        $qty_val = (int)($item['qty'] ?? 1);
        $total_qty += $qty_val;
        $product_summary_parts[] = $item['name'] . " (" . $qty_val . " uds)";

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
    $inserted_id = null;
    try {
        $stmt = $pdo->prepare("INSERT INTO solicitudes (name, company, whatsapp, email, qty, message, product_name, logo_path, simulation_path, products_json, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Nuevo')");
        $stmt->execute([$name, $company, $whatsapp, $email, $total_qty, $message, $product_summary, $logo_filename, $main_simulation_path, $products_json]);
        $inserted_id = $pdo->lastInsertId();
    } catch (PDOException $e) {
        // Ignorar o registrar error
    }

    // =========================================================================
    // ENVÍO DE CORREO AUTOMÁTICO A info@cardnetec.com.ec
    // =========================================================================
    try {
        date_default_timezone_set('America/Guayaquil');
        $fecha_hora = date('d/m/Y - h:i A');

        $to_email = 'info@cardnetec.com.ec';
        $email_subject_text = 'Nueva Cotización ' . ($inserted_id ? '#' . $inserted_id . ' ' : '') . '- ' . ($name ?: 'Cliente Web') . ($company ? ' (' . $company . ')' : '');
        $email_subject = '=?UTF-8?B?' . base64_encode($email_subject_text) . '?=';

        $clean_client_phone = function_exists('cleanWhatsAppNumber') ? cleanWhatsAppNumber($whatsapp) : preg_replace('/\D/', '', $whatsapp);
        $client_wa_link = 'https://wa.me/' . $clean_client_phone . '?text=' . urlencode('Hola ' . $name . ', te saludamos de Cardnetec respecto a la cotización solicitada en la web.');
        $admin_url = 'https://cardnetec.com.ec/admin/' . ($inserted_id ? 'cotizacion-detalle.php?id=' . $inserted_id : 'index.php');

        // Construcción de filas de la tabla de productos
        $items_table_html = '';
        $total_calculado = 0;
        foreach ($cart as $item) {
            $p_name = htmlspecialchars($item['name'] ?? 'Producto');
            $p_qty = (int)($item['qty'] ?? 1);
            $p_subtotal = (float)($item['subtotal'] ?? 0);
            $total_calculado += $p_subtotal;
            $p_subtotal_str = $p_subtotal > 0 ? '$' . number_format($p_subtotal, 2) : 'A cotizar';

            $sim_link_html = '';
            if (!empty($item['simulation_file'])) {
                $sim_link_html = '<br><a href="https://cardnetec.com.ec/uploads/' . htmlspecialchars($item['simulation_file']) . '" target="_blank" style="color:#2563eb; font-size:12px; font-weight:600; text-decoration:none;">🎨 Ver Maqueta / Simulación</a>';
            }

            $items_table_html .= '
            <tr>
              <td style="padding:12px 14px; border-bottom:1px solid #e2e8f0; font-size:13px; color:#0f172a;">
                <strong>' . $p_name . '</strong>' . $sim_link_html . '
              </td>
              <td style="padding:12px 14px; border-bottom:1px solid #e2e8f0; font-size:13px; color:#0f172a; text-align:center; font-weight:700;">
                ' . $p_qty . '
              </td>
              <td style="padding:12px 14px; border-bottom:1px solid #e2e8f0; font-size:13px; color:#0f172a; text-align:right; font-weight:600;">
                ' . $p_subtotal_str . '
              </td>
            </tr>';
        }

        // Bloque de archivos adjuntos (Logotipo y Render)
        $attachments_html = '';
        if ($logo_filename || $main_simulation_path) {
            $attachments_html .= '<div style="margin-top:20px; padding:16px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;">';
            $attachments_html .= '<h4 style="margin:0 0 12px 0; font-size:13px; color:#0f172a; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">📎 Archivos y Diseños Adjuntos</h4>';
            $attachments_html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';

            if ($logo_filename) {
                $logo_url = 'https://cardnetec.com.ec/uploads/' . htmlspecialchars($logo_filename);
                $is_img = in_array(strtolower(pathinfo($logo_filename, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'svg']);
                $attachments_html .= '<td style="vertical-align:top; padding-right:15px; width:50%;">';
                $attachments_html .= '<div style="font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">LOGOTIPO DEL CLIENTE:</div>';
                if ($is_img) {
                    $attachments_html .= '<a href="' . $logo_url . '" target="_blank"><img src="' . $logo_url . '" alt="Logotipo" style="max-width:180px; max-height:100px; border:1px solid #cbd5e1; border-radius:6px; background:#ffffff; padding:4px; display:block; margin-bottom:8px;"></a>';
                }
                $attachments_html .= '<a href="' . $logo_url . '" target="_blank" style="display:inline-block; font-size:12px; color:#2563eb; text-decoration:none; font-weight:700; background:#eff6ff; padding:5px 10px; border-radius:4px; border:1px solid #bfdbfe;">📥 Descargar Logotipo</a>';
                $attachments_html .= '</td>';
            }

            if ($main_simulation_path) {
                $sim_url = 'https://cardnetec.com.ec/uploads/' . htmlspecialchars($main_simulation_path);
                $attachments_html .= '<td style="vertical-align:top; width:50%;">';
                $attachments_html .= '<div style="font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">SIMULACIÓN / RENDER VIRTUAL:</div>';
                $attachments_html .= '<a href="' . $sim_url . '" target="_blank"><img src="' . $sim_url . '" alt="Simulación" style="max-width:180px; max-height:100px; border:1px solid #cbd5e1; border-radius:6px; background:#ffffff; padding:4px; display:block; margin-bottom:8px;"></a>';
                $attachments_html .= '<a href="' . $sim_url . '" target="_blank" style="display:inline-block; font-size:12px; color:#2563eb; text-decoration:none; font-weight:700; background:#eff6ff; padding:5px 10px; border-radius:4px; border:1px solid #bfdbfe;">🔍 Ver Simulación HD</a>';
                $attachments_html .= '</td>';
            }

            $attachments_html .= '</tr></table></div>';
        }

        // Bloque de notas o mensaje del cliente
        $message_html = '';
        if (!empty($message)) {
            $message_html = '
            <div style="margin-top:20px; padding:16px; background:#fffbeb; border-left:4px solid #f59e0b; border-radius:4px;">
              <div style="font-size:12px; font-weight:700; color:#b45309; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">📝 Mensaje / Requerimiento Especial del Cliente:</div>
              <div style="font-size:14px; color:#78350f; line-height:1.5;">' . nl2br(htmlspecialchars($message)) . '</div>
            </div>';
        }

        // Estructura completa del correo HTML responsive
        $email_body = '<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nueva Cotización Cardnetec</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family:\'Segoe UI\', Arial, Helvetica, sans-serif; -webkit-font-smoothing:antialiased; color:#1e293b;">
  <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9; padding:25px 15px;">
    <tr>
      <td align="center">
        <table width="640" border="0" cellpadding="0" cellspacing="0" style="max-width:640px; width:100%; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 10px 25px rgba(0,0,0,0.06); border:1px solid #e2e8f0;">
          
          <!-- Banner Superior -->
          <tr>
            <td style="background: linear-gradient(135deg, #070e1e 0%, #0d1b3e 100%); padding:28px 32px; text-align:left; border-bottom:3px solid #2563eb;">
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="vertical-align:middle;">
                    <img src="https://cardnetec.com.ec/assets/images/logo.png" alt="Cardnetec" width="165" style="max-width:165px; height:auto; display:block; filter: brightness(0) invert(1);">
                    <div style="color:#94a3b8; font-size:12px; margin-top:6px; letter-spacing:0.5px; text-transform:uppercase; font-weight:600;">
                      Identificación & Personalización Láser
                    </div>
                  </td>
                  <td style="text-align:right; vertical-align:middle;">
                    <span style="display:inline-block; background-color:rgba(37, 99, 235, 0.25); border:1px solid #3b82f6; color:#93c5fd; padding:6px 14px; border-radius:20px; font-size:12px; font-weight:700; letter-spacing:0.5px;">
                      COTIZACIÓN ' . ($inserted_id ? '#' . $inserted_id : 'WEB') . '
                    </span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Contenido Principal -->
          <tr>
            <td style="padding:30px 32px 20px 32px;">
              
              <h2 style="margin:0 0 8px 0; font-size:20px; font-weight:700; color:#0f172a;">
                Nueva Solicitud de Cotización Recibida
              </h2>
              <p style="margin:0 0 22px 0; font-size:14px; color:#64748b; line-height:1.5;">
                Se ha generado una nueva solicitud de cotización en <a href="https://cardnetec.com.ec" style="color:#2563eb; text-decoration:none; font-weight:600;">cardnetec.com.ec</a>. A continuación el desglose detallado para su gestión:
              </p>

              <!-- Tarjeta de Datos del Cliente -->
              <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; margin-bottom:24px; overflow:hidden;">
                <tr>
                  <td colspan="2" style="background:#0f172a; color:#ffffff; padding:10px 16px; font-size:12px; font-weight:700; letter-spacing:0.5px; text-transform:uppercase;">
                    👤 Información del Cliente
                  </td>
                </tr>
                <tr>
                  <td style="padding:11px 16px; border-bottom:1px solid #e2e8f0; font-size:13px; color:#64748b; width:36%; font-weight:600;">Nombre Completo:</td>
                  <td style="padding:11px 16px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#0f172a; font-weight:700;">' . htmlspecialchars($name ?: 'No especificado') . '</td>
                </tr>
                <tr>
                  <td style="padding:11px 16px; border-bottom:1px solid #e2e8f0; font-size:13px; color:#64748b; font-weight:600;">Empresa / RUC:</td>
                  <td style="padding:11px 16px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#0f172a;">' . htmlspecialchars($company ?: 'No especificado (Particular)') . '</td>
                </tr>
                <tr>
                  <td style="padding:11px 16px; border-bottom:1px solid #e2e8f0; font-size:13px; color:#64748b; font-weight:600;">WhatsApp / Celular:</td>
                  <td style="padding:11px 16px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#0f172a;">
                    <a href="' . $client_wa_link . '" target="_blank" style="color:#16a34a; font-weight:700; text-decoration:none; display:inline-block;">
                      ' . htmlspecialchars($whatsapp ?: 'No indicado') . ' 💬 (Click para Chatear)
                    </a>
                  </td>
                </tr>
                <tr>
                  <td style="padding:11px 16px; border-bottom:1px solid #e2e8f0; font-size:13px; color:#64748b; font-weight:600;">Correo Electrónico:</td>
                  <td style="padding:11px 16px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#0f172a;">
                    ' . (!empty($email) ? '<a href="mailto:' . htmlspecialchars($email) . '" style="color:#2563eb; text-decoration:none; font-weight:600;">' . htmlspecialchars($email) . '</a>' : '<span style="color:#94a3b8; font-style:italic;">No proporcionado</span>') . '
                  </td>
                </tr>
                <tr>
                  <td style="padding:11px 16px; font-size:13px; color:#64748b; font-weight:600;">Fecha y Hora:</td>
                  <td style="padding:11px 16px; font-size:13px; color:#0f172a;">' . $fecha_hora . ' (Ecuador)</td>
                </tr>
              </table>

              <!-- Tabla de Productos -->
              <h3 style="margin:0 0 10px 0; font-size:14px; font-weight:700; color:#0f172a; text-transform:uppercase; letter-spacing:0.5px;">
                📦 Detalle del Pedido
              </h3>
              <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0; border-radius:8px; margin-bottom:20px; overflow:hidden; border-collapse:collapse;">
                <thead>
                  <tr style="background:#0f172a; color:#ffffff; font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">
                    <th style="padding:10px 14px; text-align:left;">Producto / Requerimiento</th>
                    <th style="padding:10px 14px; text-align:center; width:90px;">Cantidad</th>
                    <th style="padding:10px 14px; text-align:right; width:110px;">Subtotal Est.</th>
                  </tr>
                </thead>
                <tbody>
                  ' . $items_table_html . '
                </tbody>
                <tfoot>
                  <tr style="background:#f8fafc; font-weight:700;">
                    <td style="padding:12px 14px; font-size:13px; color:#0f172a;">Total Unidades</td>
                    <td style="padding:12px 14px; font-size:14px; color:#0f172a; text-align:center;">' . $total_qty . ' uds</td>
                    <td style="padding:12px 14px; font-size:14px; color:#2563eb; text-align:right;">' . ($total_calculado > 0 ? '$' . number_format($total_calculado, 2) : 'A convenir') . '</td>
                  </tr>
                </tfoot>
              </table>

              ' . $message_html . '

              ' . $attachments_html . '

              <!-- Botones de Acción Rápida -->
              <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:30px 0 10px 0;">
                <tr>
                  <td align="center">
                    <a href="' . $client_wa_link . '" target="_blank" style="display:inline-block; background-color:#16a34a; color:#ffffff; text-decoration:none; font-weight:700; font-size:13px; padding:12px 22px; border-radius:6px; margin:4px 6px;">
                      💬 Chatear con el Cliente
                    </a>
                    <a href="' . $admin_url . '" target="_blank" style="display:inline-block; background-color:#0f172a; color:#ffffff; text-decoration:none; font-weight:700; font-size:13px; padding:12px 22px; border-radius:6px; margin:4px 6px;">
                      🛡️ Ver en Panel Admin
                    </a>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- Pie de Correo -->
          <tr>
            <td style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:20px 32px; text-align:center; color:#94a3b8; font-size:12px; line-height:1.5;">
              Este correo es una notificación automática generada por el sitio web <strong>cardnetec.com.ec</strong>.<br>
              © ' . date('Y') . ' Cardnetec. Quito, Ecuador.
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>';

        $mail_from_name = 'Cardnetec Cotizaciones';
        $mail_from_email = 'noreply@cardnetec.com.ec';
        $mail_reply_to = (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) ? $email : 'info@cardnetec.com.ec';

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: =?UTF-8?B?" . base64_encode($mail_from_name) . "?= <" . $mail_from_email . ">\r\n";
        $headers .= "Reply-To: " . $mail_reply_to . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

        @mail($to_email, $email_subject, $email_body, $headers);
    } catch (Exception $mailEx) {
        // En caso de cualquier excepción en el correo, no interrumpir el flujo del cliente
    }

    // Obtener número de WhatsApp de configuración del sitio
    $settings = getSiteSettings($pdo);
    $target_phone = cleanWhatsAppNumber($settings['whatsapp'] ?? '');

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
    
    foreach ($cart as $item) {
        $text .= "• " . $item['name'] . " - " . $item['qty'] . " uds.\n";
    }
    
    if ($message) {
        $text .= "\n📝 *Notas:* " . $message . "\n";
    }

    if ($main_simulation_path) {
        $text .= "\n🎨 _Adjunto los renders y maquetas de simulación generadas en el taller en línea._";
    }

    $whatsappUrl = formatWhatsAppUrl($target_phone, $text);

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