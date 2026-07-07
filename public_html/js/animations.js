/* ==========================================================================
   CardNet.ec - Animaciones de Entrada y Revelado Progresivo (animations.js)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Revelado al Hacer Scroll usando IntersectionObserver
    const revealElements = document.querySelectorAll('.reveal-on-scroll');

    if ('IntersectionObserver' in window && revealElements.length > 0) {
        const revealOptions = {
            root: null, // viewport
            rootMargin: '0px 0px -100px 0px', // se activa 100px antes de entrar al viewport
            threshold: 0.1 // 10% del elemento visible
        };

        const revealCallback = (entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    // Deja de observar para mantener el rendimiento y evitar reflujos
                    observer.unobserve(entry.target);
                }
            });
        };

        const observer = new IntersectionObserver(revealCallback, revealOptions);

        revealElements.forEach(element => {
            observer.observe(element);
        });
    } else {
        // Fallback para navegadores antiguos: se activan todos los elementos inmediatamente
        revealElements.forEach(element => {
            element.classList.add('active');
        });
    }

    // 2. Acordeón Accesible en la Página de Preguntas Frecuentes (FAQ)
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const trigger = item.querySelector('.faq-trigger');
        const content = item.querySelector('.faq-content');

        if (trigger && content) {
            trigger.addEventListener('click', () => {
                const isActive = item.classList.contains('active');

                // Cierra los otros elementos del acordeón (comportamiento clásico y limpio)
                faqItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                        const otherContent = otherItem.querySelector('.faq-content');
                        if (otherContent) otherContent.style.maxHeight = null;
                        const otherTrigger = otherItem.querySelector('.faq-trigger');
                        if (otherTrigger) otherTrigger.setAttribute('aria-expanded', 'false');
                    }
                });

                // Toggle del elemento actual
                if (isActive) {
                    item.classList.remove('active');
                    content.style.maxHeight = null;
                    trigger.setAttribute('aria-expanded', 'false');
                } else {
                    item.classList.add('active');
                    content.style.maxHeight = content.scrollHeight + 'px';
                    trigger.setAttribute('aria-expanded', 'true');
                }
            });
        }
    });
});
