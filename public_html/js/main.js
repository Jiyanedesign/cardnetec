/* ==========================================================================
   CardNet.ec - Lógica de Navegación, Header y Componentes Centrales (main.js)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Menú Hamburguesa Móvil
    const burgerMenu = document.querySelector('.burger-menu');
    const mobileNav = document.querySelector('.mobile-nav');
    const mobileNavOverlay = document.querySelector('.mobile-nav-overlay');

    if (burgerMenu && mobileNav && mobileNavOverlay) {
        const toggleMenu = () => {
            const isOpen = burgerMenu.classList.toggle('open');
            mobileNav.classList.toggle('open', isOpen);
            mobileNavOverlay.classList.toggle('open', isOpen);
            burgerMenu.setAttribute('aria-expanded', isOpen);
        };

        const closeMenu = () => {
            burgerMenu.classList.remove('open');
            mobileNav.classList.remove('open');
            mobileNavOverlay.classList.remove('open');
            burgerMenu.setAttribute('aria-expanded', 'false');
        };

        burgerMenu.addEventListener('click', toggleMenu);
        mobileNavOverlay.addEventListener('click', closeMenu);

        // Cerrar menú móvil al presionar la tecla ESC (Accesibilidad)
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mobileNav.classList.contains('open')) {
                closeMenu();
                burgerMenu.focus();
            }
        });
    }

    // 2. Control de Enlaces Activos
    const currentPath = window.location.pathname;
    const pageName = currentPath.substring(currentPath.lastIndexOf('/') + 1) || 'index.html';

    const selectActiveLinks = (selector) => {
        const links = document.querySelectorAll(selector);
        links.forEach(link => {
            const linkHref = link.getAttribute('href');
            if (linkHref === pageName) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    };

    selectActiveLinks('.nav-link');
    selectActiveLinks('.mobile-link');

    // 3. Configuración Dinámica de WhatsApp
    const whatsappFloat = document.querySelector('.whatsapp-float');
    if (whatsappFloat) {
        // Mensaje personalizado dinámico según la página activa
        let whatsappMessage = 'Hola CardNet.ec, me gustaría obtener más información sobre la personalización de artículos promocionales.';

        if (pageName === 'cotizacion.html') {
            whatsappMessage = 'Hola CardNet.ec, deseo cotizar un pedido especial de artículos promocionales personalizados.';
        } else if (pageName === 'empresas.html') {
            whatsappMessage = 'Hola CardNet.ec, me interesa conocer más sobre sus servicios B2B, kits corporativos y uniformidad visual para empresas.';
        } else if (pageName === 'productos.html') {
            whatsappMessage = 'Hola CardNet.ec, me gustaría consultar la disponibilidad y precios del catálogo de productos promocionales.';
        } else if (pageName === 'personalizacion.html') {
            whatsappMessage = 'Hola CardNet.ec, quiero consultar qué técnica de marcado recomiendan para mi logotipo corporativo.';
        } else if (pageName === 'contacto.html') {
            whatsappMessage = 'Hola CardNet.ec, estoy en su página de contacto y me gustaría agendar una llamada con un asesor corporativo.';
        }

        const phone = '593900000000'; // Formato internacional de prueba
        const encodedText = encodeURIComponent(whatsappMessage);
        whatsappFloat.setAttribute('href', `https://wa.me/${phone}?text=${encodedText}`);
    }
});
