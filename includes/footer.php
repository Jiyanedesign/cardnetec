<?php
// Cargar configuraciones del sitio si no están cargadas
if (!isset($site_settings) && isset($pdo)) {
    $site_settings = getSiteSettings($pdo);
}
$footer_wa_clean = cleanWhatsAppNumber($site_settings['whatsapp'] ?? '');
$footer_wa_url = formatWhatsAppUrl($site_settings['whatsapp'] ?? '', 'Hola CardNet, deseo realizar una consulta.');
$footer_wa_display = !empty($site_settings['whatsapp']) ? $site_settings['whatsapp'] : '+593 99 978 180';
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
                    <img src="images/logo.webp?v=2.1" alt="CardNet.ec Logo" class="logo-img" width="132" height="48" loading="lazy" decoding="async">
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
                <h3 class="footer-heading" style="font-size: 0.9rem; font-family: var(--font-heading); margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dark);">Contacto & Ayuda</h3>
                <div class="footer-contact-info" style="display: flex; flex-direction: column; gap: 10px; font-size: 0.85rem; color: var(--text-muted);">
                    <a href="cotizacion.php" class="footer-link">Cotizar productos</a>
                    <a href="cotizacion.php" class="footer-link">Enviar logo</a>
                    <a href="faq.php" class="footer-link">Preguntas frecuentes</a>
                    <a href="privacidad.php" class="footer-link">Política de privacidad</a>
                    <a href="terminos.php" class="footer-link">Términos de uso</a>
                    <a href="cookies.php" class="footer-link">Gestión de cookies</a>
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
                            <strong>Teléfonos / WhatsApp:</strong><br>
                            <?php foreach ($all_phones as $idx => $p): ?>
                                <a href="<?php echo formatWhatsAppUrl($p, 'Hola CardNet, deseo información sobre sus servicios.'); ?>" target="_blank" rel="noopener noreferrer" style="color: inherit; text-decoration: none; display: inline-block; margin-right: 8px; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='inherit'" title="Chatear por WhatsApp">
                                    <?php echo htmlspecialchars($p); ?>
                                </a><?php echo ($idx < count($all_phones) - 1) ? ' ·' : ''; ?>
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
    <div class="footer-bottom" style="border-top: 1px solid var(--border); padding-top: 1.75rem; padding-bottom: 2rem; background-color: var(--surface-light, #fbfcfb); text-align: center;">
        <div class="container footer-bottom-flex" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 14px; max-width: 980px; margin: 0 auto;">
            <p class="footer-copyright-row" style="font-size: 0.84rem; color: var(--text-muted); margin: 0; display: flex; align-items: center; justify-content: center; flex-wrap: wrap; gap: 10px 14px; line-height: 1.6; text-align: center;">
                <span>
                    <a href="admin/" style="color: inherit; text-decoration: none;" title="Panel de Administración">&copy; 2026</a> 
                    <strong style="color: var(--dark); font-weight: 600;">CardNet.ec</strong> — Taller de personalización de precisión y acabados de autor.
                </span>
                <span class="footer-dev-credit" style="display: inline-flex; align-items: center; gap: 8px;">
                    <span style="color: var(--border); font-weight: 300;">|</span>
                    <span>Diseñado por</span>
                    <a href="https://jiyanedesign.com" target="_blank" rel="noopener noreferrer" class="jiyanedesign-badge" title="Visitar JiyaneDesign — Diseño Web y Estrategia Digital" style="display: inline-flex; align-items: center; gap: 6px; background: #121610; color: #ffffff !important; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-decoration: none; border: 1px solid rgba(99, 174, 44, 0.45); box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12); transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); letter-spacing: 0.3px; vertical-align: middle;" onmouseover="this.style.transform='translateY(-2px) scale(1.03)'; this.style.boxShadow='0 6px 18px rgba(99, 174, 44, 0.4)'; this.style.borderColor='var(--primary, #63ae2c)';" onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 2px 6px rgba(0, 0, 0, 0.12)'; this.style.borderColor='rgba(99, 174, 44, 0.45)';">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="#7dd83c" style="flex-shrink: 0;">
                            <path d="M12 2L14.4 9.6L22 12L14.4 14.4L12 22L9.6 14.4L2 12L9.6 9.6L12 2Z"/>
                        </svg>
                        <span style="color: #ffffff;">Jiyane<span style="color: #7dd83c;">Design</span></span>
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#a0aab5" stroke-width="2.5" style="flex-shrink: 0;">
                            <path d="M7 17L17 7M17 7H7M17 7V17"/>
                        </svg>
                    </a>
                </span>
            </p>
            <div class="footer-legal-links" style="display: flex; gap: 14px 18px; font-size: 0.8rem; flex-wrap: wrap; justify-content: center; align-items: center; text-align: center; margin: 0 auto;">
                <a href="privacidad.php" class="footer-legal-link" style="color: var(--text-muted); text-decoration: none; transition: color 0.2s; font-weight: 500;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Política de Privacidad</a>
                <span style="color: var(--border);">·</span>
                <a href="terminos.php" class="footer-legal-link" style="color: var(--text-muted); text-decoration: none; transition: color 0.2s; font-weight: 500;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Términos y Políticas de Uso</a>
                <span style="color: var(--border);">·</span>
                <a href="cookies.php" class="footer-legal-link" style="color: var(--text-muted); text-decoration: none; transition: color 0.2s; font-weight: 500;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Aviso y Gestión de Cookies</a>
            </div>
        </div>
    </div>
</footer>

<!-- Botón de WhatsApp Flotante Global -->
<a href="<?php echo $footer_wa_url; ?>" class="whatsapp-float" target="_blank" rel="noopener noreferrer" aria-label="Contactar por WhatsApp" title="¿Tienes dudas? Escríbenos por WhatsApp">
    <svg class="whatsapp-icon" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 0 0 1.333 4.982L2 22l5.233-1.371a9.994 9.994 0 0 0 4.779 1.22h.005c5.505 0 9.99-4.478 9.99-9.985A9.988 9.988 0 0 0 12.012 2zm4.7 13.916c-.223.633-1.29 1.205-1.782 1.282-.477.075-.947.168-3.067-.665-2.707-1.06-4.442-3.817-4.577-3.996-.134-.178-1.096-1.455-1.096-2.781 0-1.325.692-1.973.938-2.228.246-.255.535-.319.714-.319.18 0 .358.001.514.009.16.008.375-.062.586.448.223.54.76 1.851.827 1.984.067.134.112.29.022.468-.09.18-.134.29-.268.447-.134.156-.282.35-.403.47-.134.134-.273.28-.117.548.156.268.693 1.139 1.492 1.85 1.026.914 1.89 1.196 2.158 1.33.268.134.424.112.58-.067.157-.18.67-.781.848-1.049.178-.268.358-.223.58-.134.224.089 1.42.67 1.666.792.246.123.411.18.47.282.06.101.06.586-.163 1.218z"/>
    </svg>
</a>
<script>
    window.CARDNET_WA_PHONE = '<?php echo $footer_wa_clean; ?>';
    window.CARDNET_WA_URL = '<?php echo $footer_wa_url; ?>';
</script>
