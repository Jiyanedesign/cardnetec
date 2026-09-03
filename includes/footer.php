<?php
// Cargar configuraciones del sitio si no están cargadas
if (!isset($site_settings) && isset($pdo)) {
    $site_settings = getSiteSettings($pdo);
}
$footer_wa_clean = !empty($site_settings['whatsapp']) ? preg_replace('/[^0-9]/', '', $site_settings['whatsapp']) : '593000000000';
$footer_wa_display = !empty($site_settings['whatsapp']) ? $site_settings['whatsapp'] : '+593 00 000 0000';
$footer_phone_2 = !empty($site_settings['phone_2']) ? $site_settings['phone_2'] : '';
$footer_phone_3 = !empty($site_settings['phone_3']) ? $site_settings['phone_3'] : '';
$footer_email_display = !empty($site_settings['email']) ? $site_settings['email'] : 'correo@cardnet.ec';
$footer_email_2 = !empty($site_settings['email_2']) ? $site_settings['email_2'] : '';
$footer_address_display = !empty($site_settings['address']) ? $site_settings['address'] : 'Ecuador';

// Agrupar teléfonos y correos no vacíos
$all_phones = array_filter([$footer_wa_display, $footer_phone_2, $footer_phone_3]);
$all_emails = array_filter([$footer_email_display, $footer_email_2]);
?>
<footer class="main-footer">
    <div class="container footer-top section-padding" style="padding-top: 3rem; padding-bottom: 3rem;">
        <div class="footer-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 40px;">
            <div class="footer-brand-column">
                <a href="index.php" class="logo footer-logo" aria-label="CardNet.ec Inicio">
                    <img src="images/logo.png?v=2.0" alt="CardNet.ec Logo" class="logo-img">
                </a>
                <p class="footer-description" style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-top: 1rem;">
                    Taller especialista en personalización de precisión y grabado láser en Ecuador. Acabados indelebles en acero, cuero, madera, acrílico, marfil vegetal de tagua y credenciales. Calidad de autor pieza por pieza, sin barreras de producción masiva.
                </p>
            </div>
            <div class="footer-links-column">
                <h3 class="footer-heading" style="font-size: 0.9rem; font-family: var(--font-heading); margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dark);">Productos</h3>
                <nav class="footer-links" aria-label="Enlaces de productos" style="display: flex; flex-direction: column; gap: 8px; font-size: 0.85rem;">
                    <a href="productos.php?cat=carnets" class="footer-link">Carnets</a>
                    <a href="productos.php?cat=credenciales" class="footer-link">Credenciales</a>
                    <a href="productos.php?cat=cintas" class="footer-link">Cintas</a>
                    <a href="productos.php?cat=porta-credenciales" class="footer-link">Porta carnets</a>
                    <a href="personalizacion.php" class="footer-link">Personalización</a>
                    <a href="tagua.php" class="footer-link">Productos de Tagua</a>
                </nav>
            </div>
            <div class="footer-links-column">
                <h3 class="footer-heading" style="font-size: 0.9rem; font-family: var(--font-heading); margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dark);">Contacto</h3>
                <div class="footer-contact-info" style="display: flex; flex-direction: column; gap: 10px; font-size: 0.85rem; color: var(--text-muted);">
                    <a href="cotizacion.php" class="footer-link">Cotizar productos</a>
                    <a href="cotizacion.php" class="footer-link">Enviar logo</a>
                    <a href="faq.php" class="footer-link">Preguntas frecuentes</a>
                </div>
            </div>
            <div class="footer-links-column">
                <h3 class="footer-heading" style="font-size: 0.9rem; font-family: var(--font-heading); margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dark);">Ubicación y Contacto</h3>
                <div style="margin-bottom: 1rem;">
                    <iframe src="https://www.google.com/maps?ll=-0.165355,-78.483023&z=15&t=m&hl=es&gl=EC&mapclient=embed&cid=13164539704964091228&output=embed" width="100%" height="150" style="border:0; border-radius: 6px;" allowfullscreen="" loading="lazy"></iframe>
                </div>
                <div style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin: 0;">
                    <?php if (!empty($all_phones)): ?>
                        <div style="margin-bottom: 4px;">
                            <strong>Teléfonos:</strong><br>
                            <?php foreach ($all_phones as $idx => $p): ?>
                                <span style="display: inline-block; margin-right: 8px;">
                                    <?php echo htmlspecialchars($p); ?><?php echo ($idx < count($all_phones) - 1) ? ' ·' : ''; ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($all_emails)): ?>
                        <div style="margin-bottom: 4px;">
                            <strong>Correos:</strong><br>
                            <?php foreach ($all_emails as $idx => $em): ?>
                                <a href="mailto:<?php echo htmlspecialchars($em); ?>" style="color: inherit; text-decoration: none; display: inline-block; margin-right: 8px;">
                                    <?php echo htmlspecialchars($em); ?><?php echo ($idx < count($all_emails) - 1) ? ' ·' : ''; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div>
                        <strong>Ubicación:</strong> <?php echo htmlspecialchars($footer_address_display); ?>
                    </div>
                </div>
                <?php if (!empty($site_settings['instagram']) || !empty($site_settings['facebook'])): ?>
                    <div style="display: flex; gap: 12px; margin-top: 10px;">
                        <?php if (!empty($site_settings['instagram'])): ?>
                            <a href="<?php echo htmlspecialchars($site_settings['instagram']); ?>" target="_blank" rel="noopener noreferrer" style="color: var(--primary); font-size: 0.85rem; text-decoration: none; font-weight: 500;">Instagram</a>
                        <?php endif; ?>
                        <?php if (!empty($site_settings['facebook'])): ?>
                            <a href="<?php echo htmlspecialchars($site_settings['facebook']); ?>" target="_blank" rel="noopener noreferrer" style="color: var(--primary); font-size: 0.85rem; text-decoration: none; font-weight: 500;">Facebook</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="footer-bottom" style="border-top: 1px solid var(--border); padding-top: 1.5rem; padding-bottom: 1.5rem;">
        <div class="container footer-bottom-flex" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <p style="font-size: 0.8rem; color: var(--text-muted);"><a href="admin/" style="color: inherit; text-decoration: none;" title="Panel de Administración">&copy; 2026</a> CardNet.ec — Taller de personalización de precisión y acabados de autor.</p>
        </div>
    </div>
</footer>

<!-- Botón de WhatsApp Flotante Global -->
<a href="https://wa.me/<?php echo $footer_wa_clean; ?>" class="whatsapp-float" target="_blank" rel="noopener noreferrer" aria-label="Contactar por WhatsApp" title="¿Tienes dudas? Escríbenos por WhatsApp">
    <svg class="whatsapp-icon" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 0 0 1.333 4.982L2 22l5.233-1.371a9.994 9.994 0 0 0 4.779 1.22h.005c5.505 0 9.99-4.478 9.99-9.985A9.988 9.988 0 0 0 12.012 2zm4.7 13.916c-.223.633-1.29 1.205-1.782 1.282-.477.075-.947.168-3.067-.665-2.707-1.06-4.442-3.817-4.577-3.996-.134-.178-1.096-1.455-1.096-2.781 0-1.325.692-1.973.938-2.228.246-.255.535-.319.714-.319.18 0 .358.001.514.009.16.008.375-.062.586.448.223.54.76 1.851.827 1.984.067.134.112.29.022.468-.09.18-.134.29-.268.447-.134.156-.282.35-.403.47-.134.134-.273.28-.117.548.156.268.693 1.139 1.492 1.85 1.026.914 1.89 1.196 2.158 1.33.268.134.424.112.58-.067.157-.18.67-.781.848-1.049.178-.268.358-.223.58-.134.224.089 1.42.67 1.666.792.246.123.411.18.47.282.06.101.06.586-.163 1.218z"/>
    </svg>
</a>
