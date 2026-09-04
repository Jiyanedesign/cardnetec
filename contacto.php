<?php
session_start();
require_once 'db.php';
$site_settings = getSiteSettings($pdo);
$contact_wa_display = !empty($site_settings['whatsapp']) ? $site_settings['whatsapp'] : '+593 00 000 0000';
$contact_phone_2 = !empty($site_settings['phone_2']) ? $site_settings['phone_2'] : '';
$contact_phone_3 = !empty($site_settings['phone_3']) ? $site_settings['phone_3'] : '';
$contact_email_display = !empty($site_settings['email']) ? $site_settings['email'] : 'correo@cardnet.ec';
$contact_email_2 = !empty($site_settings['email_2']) ? $site_settings['email_2'] : '';
$contact_address_display = !empty($site_settings['address']) ? $site_settings['address'] : 'Av. Amazonas, Quito, Ecuador';

$all_phones = array_filter([$contact_wa_display, $contact_phone_2, $contact_phone_3]);
$all_emails = array_filter([$contact_email_display, $contact_email_2]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Favicon Oficial -->
    <link rel="icon" type="image/png" href="favicon.png?v=2.0">
    <link rel="shortcut icon" href="favicon.ico?v=2.0">
    <link rel="apple-touch-icon" href="favicon.png?v=2.0">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | CardNet.ec - Identificación y Personalización</title>
    <meta name="description" content="Ponte en contacto con CardNet.ec. Agenda una llamada comercial, escríbenos por WhatsApp o visítanos en nuestro taller de Quito, Ecuador.">
    <link rel="canonical" href="https://cardnet.ec/contacto.php">
    
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://cardnet.ec/contacto.php">
    <meta property="og:title" content="Contacto Comercial | CardNet.ec">
    <meta property="og:description" content="Comunícate con nuestros asesores. Rápida respuesta corporativa.">
    <meta property="og:image" content="https://cardnet.ec/images/og-image.jpg">

    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=6.3">
    <link rel="stylesheet" href="css/layout.css?v=7.0">
    <link rel="stylesheet" href="css/components.css?v=6.3">
    <link rel="stylesheet" href="css/pages.css?v=6.3">
    <link rel="stylesheet" href="css/animations.css?v=1.1.3">
</head>
<body>

    <!-- Cabecera Modular -->
    <?php include 'includes/header.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="section-padding container">
        
        <div class="split-feature">
            
            <!-- Canales y Horarios -->
            <div class="split-content reveal-on-scroll">
                <span class="section-subtitle">Canales Directos</span>
                <h2>Habla con un Asesor Técnico</h2>
                <p>Nuestra planta y taller se encuentran ubicados estratégicamente para el despacho de pedidos a todo el Ecuador.</p>
                
                <div class="footer-contact-info" style="margin-bottom: 2rem; gap: 1rem;">
                    <?php if (!empty($all_phones)): ?>
                        <div class="footer-contact-item" style="font-size: 1rem; align-items: flex-start;">
                            <svg class="footer-contact-icon" style="width: 20px; height: 20px; margin-top: 3px;" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <div>
                                <strong>Teléfonos / WhatsApp:</strong><br>
                                <?php foreach ($all_phones as $idx => $p): ?>
                                    <a href="<?php echo formatWhatsAppUrl($p, 'Hola CardNet, deseo realizar una consulta técnica o cotización.'); ?>" target="_blank" rel="noopener noreferrer" style="display: inline-block; font-size: 0.95rem; color: var(--primary); text-decoration: none; margin-top: 4px; font-weight: 500; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'" title="Chatear por WhatsApp">
                                        • <?php echo htmlspecialchars($p); ?> <span style="font-size:0.8rem; opacity:0.8;">(WhatsApp directo)</span>
                                    </a><br>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($all_emails)): ?>
                        <div class="footer-contact-item" style="font-size: 1rem; align-items: flex-start;">
                            <svg class="footer-contact-icon" style="width: 20px; height: 20px; margin-top: 3px;" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="m22 6-10 7L2 6"/></svg>
                            <div>
                                <strong>Correos Electrónicos:</strong><br>
                                <?php foreach ($all_emails as $idx => $em): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($em); ?>" style="display: block; font-size: 0.95rem; color: var(--primary); text-decoration: none; margin-top: 2px;">
                                        • <?php echo htmlspecialchars($em); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="footer-contact-item" style="font-size: 1rem;">
                        <svg class="footer-contact-icon" style="width: 20px; height: 20px;" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span><strong>Ubicación:</strong> <?php echo htmlspecialchars($contact_address_display); ?></span>
                    </div>
                    <div class="footer-contact-item" style="font-size: 1rem;">
                        <svg class="footer-contact-icon" style="width: 20px; height: 20px;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span><strong>Horario:</strong> Lunes a Viernes, de 09:00 a 18:00</span>
                    </div>
                </div>

                <div class="image-placeholder theme-blue">
                    <svg class="image-placeholder-icon" viewBox="0 0 24 24" width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5">
                        <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/></svg>
                    <span class="image-placeholder-text">Av. de los Granados y Av. Eloy Alfaro, Quito</span>
                </div>
            </div>

            <!-- Formulario de Consulta -->
            <div class="split-visual reveal-on-scroll delay-100">
                <div class="solution-card">
                    <h3 style="margin-bottom: 1.25rem; font-size: 1.25rem;">Escribe a nuestro taller</h3>
                    
                    <form id="contact-form" novalidate>
                        <div class="form-group">
                            <label class="form-label" for="name">Tu Nombre Completo *</label>
                            <input class="form-input" type="text" id="name" required placeholder="Ej. Javier Ortiz">
                            <span class="form-error-msg">Este campo es obligatorio.</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Correo Corporativo *</label>
                            <input class="form-input" type="email" id="email" required placeholder="javier@empresa.com">
                            <span class="form-error-msg">Por favor, introduce un correo corporativo válido.</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="message">Consulta o Detalle Técnico *</label>
                            <textarea class="form-textarea" id="message" required placeholder="Escribe aquí tu consulta o los detalles de marcaje que deseas evaluar..."></textarea>
                            <span class="form-error-msg">Este campo es obligatorio.</span>
                        </div>

                        <button class="btn btn-primary" type="submit" style="width: 100%;">
                            Enviar Consulta
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </main>

    <!-- Pie de Página -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts Modulares -->
    <script src="js/main.js?v=7.0" defer></script>
    <script src="js/animations.js" defer></script>
    <script src="js/forms.js" defer></script>
</body>
</html>
 