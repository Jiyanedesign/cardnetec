/* ==========================================================================
   CardNet.ec - Lógica de Navegación, Cotización Interactiva, Buscador,
   Filtros y Detalle de Productos (main.js)
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

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mobileNav.classList.contains('open')) {
                closeMenu();
                burgerMenu.focus();
            }
        });
    }

    // 2. Inyección Dinámica de Modales Globales (Panel de Cotización y Modal de Detalles)
    const injectGlobalModals = () => {
        if (document.getElementById('quote-drawer')) return;

        // Inyectar Drawer de Cotización
        const drawerHtml = `
            <div class="quote-drawer-overlay" id="quote-drawer-overlay"></div>
            <div class="quote-drawer" id="quote-drawer" aria-label="Mi Cotización" role="dialog">
                <div class="quote-drawer-header">
                    <h3>Mi Lista de Cotización</h3>
                    <button class="quote-drawer-close" id="quote-drawer-close" aria-label="Cerrar">&times;</button>
                </div>
                <div class="quote-drawer-body">
                    <div id="quote-drawer-items" class="drawer-items-list"></div>
                    <div id="quote-drawer-empty" style="text-align:center; padding:2rem 0; color:var(--text-muted);">
                        <p style="font-size:0.9rem;">No has añadido productos a tu lista.</p>
                        <a href="productos.php" class="btn btn-secondary" style="margin-top:1rem; display:inline-block; font-size:0.8rem;">Ver catálogo</a>
                    </div>
                    <div class="quote-drawer-form" id="quote-drawer-form-container" style="display:none;">
                        <hr style="margin:1.5rem 0; border:0; border-top:1px solid var(--border);">
                        <div class="form-group">
                            <label class="form-label" style="font-size:0.7rem; font-weight:600;">¿Tienes logotipo corporativo?</label>
                            <select class="form-input" id="drawer-has-logo" style="padding:8px 12px; font-size:0.85rem; height:auto;">
                                <option value="Sí">Sí, lo tengo listo (vector / imagen)</option>
                                <option value="No">No, solo texto o idea</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size:0.7rem; font-weight:600;" for="drawer-city">Ciudad de entrega *</label>
                            <input class="form-input" type="text" id="drawer-city" placeholder="Ej. Quito, Guayaquil..." style="padding:8px 12px; font-size:0.85rem;" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size:0.7rem; font-weight:600;" for="drawer-notes">Observaciones adicionales</label>
                            <textarea class="form-textarea" id="drawer-notes" placeholder="Colores, grabado, fecha límite..." style="min-height:70px; padding:8px 12px; font-size:0.85rem;"></textarea>
                        </div>
                        <button class="btn btn-primary" id="btn-submit-drawer-whatsapp" style="width:100%; margin-top:1rem; padding:12px; font-size:0.88rem; font-weight:600;">
                            Enviar Solicitud por WhatsApp
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', drawerHtml);

        // Inyectar Modal de Detalle de Producto
        const modalHtml = `
            <div class="product-detail-modal-overlay" id="product-detail-modal-overlay">
                <div class="product-detail-modal" id="product-detail-modal" role="dialog" aria-modal="true">
                    <div class="quote-drawer-header" style="background:var(--surface-light);">
                        <h3 id="modal-product-name" style="font-size:1.15rem;">Detalles del Producto</h3>
                        <button class="quote-drawer-close" id="product-detail-close" aria-label="Cerrar">&times;</button>
                    </div>
                    <div class="quote-drawer-body" style="padding:1.5rem;">
                        <span id="modal-product-category" style="font-size:0.7rem; color:var(--primary); font-weight:600; text-transform:uppercase; display:block; margin-bottom:5px;"></span>
                        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:1rem;">
                            <span id="modal-product-material" style="font-size:0.7rem; background:#f0f0f0; padding:2px 8px; border-radius:10px; font-weight:500;"></span>
                            <span id="modal-product-technique" style="font-size:0.7rem; background:rgba(99,174,44,0.1); color:var(--primary-hover); padding:2px 8px; border-radius:10px; font-weight:600;"></span>
                        </div>
                        <p id="modal-product-desc" style="font-size:0.88rem; color:var(--text-muted); line-height:1.6; margin-bottom:1.25rem;"></p>
                        
                        <div style="background:var(--surface-light); padding:1rem; border-radius:6px; border:1px solid var(--border); margin-bottom:1.5rem;">
                            <h4 style="font-size:0.82rem; margin-bottom:4px; font-weight:600;">📁 Qué debes enviar:</h4>
                            <p style="font-size:0.78rem; color:var(--text-muted); line-height:1.4; margin:0;">Tu logo corporativo, nombre o frase. Preferible vectorizado (.AI, .PDF, .SVG) o en alta calidad.</p>
                        </div>

                        <div style="display:flex; gap:10px;">
                            <button class="btn btn-primary" id="modal-add-btn" style="flex-grow:1; padding:12px; font-size:0.85rem; font-weight:600;">
                                Agregar a cotización
                            </button>
                            <a href="#" id="modal-whatsapp-direct" class="btn btn-secondary" target="_blank" rel="noopener noreferrer" style="padding:12px; font-size:0.85rem; font-weight:500; display:flex; align-items:center; justify-content:center;">
                                Consultar por WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Inyectar Barra Inferior en Móviles
        const bottomBarHtml = `
            <div class="mobile-bottom-bar">
                <a href="https://wa.me/593900000000" class="btn btn-secondary" target="_blank" rel="noopener" style="padding:10px; font-size:0.82rem; text-align:center; font-weight:600; display:flex; align-items:center; justify-content:center; gap:5px;">
                    💬 WhatsApp
                </a>
                <button class="btn btn-primary toggle-quote-drawer-btn" style="padding:10px; font-size:0.82rem; text-align:center; font-weight:600;">
                    📋 Mi Cotización
                </button>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', bottomBarHtml);
    };

    injectGlobalModals();

    // 3. Variables de Control
    const drawer = document.getElementById('quote-drawer');
    const drawerOverlay = document.getElementById('quote-drawer-overlay');
    const drawerClose = document.getElementById('quote-drawer-close');
    const drawerItemsContainer = document.getElementById('quote-drawer-items');
    const drawerEmpty = document.getElementById('quote-drawer-empty');
    const drawerFormContainer = document.getElementById('quote-drawer-form-container');

    const detailModalOverlay = document.getElementById('product-detail-modal-overlay');
    const detailClose = document.getElementById('product-detail-close');

    // 4. Cargar y Actualizar los Productos de la Cotización en el Drawer
    const updateDrawerUI = () => {
        fetch('cart-action.php?action=get')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const cart = data.cart;
                    // Actualizar el contador del badge en el header
                    const badges = document.querySelectorAll('.cart-badge-count');
                    badges.forEach(b => {
                        b.textContent = cart.length;
                        b.style.display = cart.length > 0 ? 'flex' : 'none';
                    });

                    if (cart.length === 0) {
                        drawerItemsContainer.innerHTML = '';
                        drawerEmpty.style.display = 'block';
                        drawerFormContainer.style.display = 'none';
                    } else {
                        drawerEmpty.style.display = 'none';
                        drawerFormContainer.style.display = 'block';

                        drawerItemsContainer.innerHTML = cart.map((item, idx) => `
                            <div class="drawer-item-card" data-index="${idx}">
                                <div class="drawer-item-info">
                                    <h4 class="drawer-item-name">${item.name}</h4>
                                    <span class="drawer-item-meta">$${parseFloat(item.price).toFixed(2)} c/u</span>
                                </div>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <input type="number" class="form-input drawer-item-qty" value="${item.qty}" min="1" data-index="${idx}" style="width:65px; padding:4px 8px; text-align:center; font-size:0.85rem; height:auto;">
                                    <button class="btn-delete-item drawer-item-remove" data-index="${idx}" title="Quitar" style="position:static; padding:4px; color:#EF4444; background:none; border:none; cursor:pointer;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><line x1="10" y1="11" x2="10" y2="17"/></svg>
                                    </button>
                                </div>
                            </div>
                        `).join('');

                        // Escuchadores de eliminación y cambio de cantidad
                        document.querySelectorAll('.drawer-item-remove').forEach(btn => {
                            btn.addEventListener('click', (e) => {
                                const idx = e.currentTarget.getAttribute('data-index');
                                removeDrawerItem(idx);
                            });
                        });

                        document.querySelectorAll('.drawer-item-qty').forEach(input => {
                            input.addEventListener('change', (e) => {
                                const idx = e.target.getAttribute('data-index');
                                const val = parseInt(e.target.value) || 1;
                                updateDrawerQty(idx, val);
                            });
                        });
                    }
                }
            });
    };

    const removeDrawerItem = (idx) => {
        fetch(`cart-action.php?action=remove&index=${idx}`)
            .then(res => res.json())
            .then(() => {
                updateDrawerUI();
                showNotification("Producto removido de la lista");
            });
    };

    const updateDrawerQty = (idx, qty) => {
        fetch(`cart-action.php?action=update_qty&index=${idx}&qty=${qty}`)
            .then(res => res.json())
            .then(() => {
                updateDrawerUI();
            });
    };

    // 5. Agregar a Cotización con Microinteracción
    const addToQuote = (name, slug, price) => {
        const formData = new FormData();
        formData.append('name', name);
        formData.append('slug', slug);
        formData.append('price', price);
        formData.append('qty', 20); // cantidad sugerida B2B inicial

        fetch('cart-action.php?action=add', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateDrawerUI();
                showNotification(`¡${name} agregado a tu lista!`);
                openDrawer();
            }
        });
    };

    // Escuchar clicks globales para agregar a cotización
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-add-to-quote')) {
            const btn = e.target;
            const name = btn.getAttribute('data-name');
            const slug = btn.getAttribute('data-slug');
            const price = btn.getAttribute('data-price');
            addToQuote(name, slug, price);
        }
    });

    // 6. Notificaciones flotantes rápidas
    const showNotification = (msg) => {
        const notif = document.createElement('div');
        notif.style.position = 'fixed';
        notif.style.bottom = '85px';
        notif.style.right = '24px';
        notif.style.background = 'var(--primary)';
        notif.style.color = 'white';
        notif.style.padding = '12px 20px';
        notif.style.borderRadius = '6px';
        notif.style.boxShadow = 'var(--shadow-md)';
        notif.style.zIndex = '3500';
        notif.style.fontSize = '0.85rem';
        notif.style.fontWeight = '600';
        notif.style.animation = 'fadeInFast 0.25s ease-out';
        notif.textContent = msg;

        document.body.appendChild(notif);
        setTimeout(() => {
            notif.style.opacity = '0';
            notif.style.transition = 'opacity 0.25s ease';
            setTimeout(() => notif.remove(), 250);
        }, 3000);
    };

    // 7. Control de visualización del Drawer
    const openDrawer = () => {
        drawer.classList.add('open');
        drawerOverlay.classList.add('open');
        updateDrawerUI();
    };

    const closeDrawer = () => {
        drawer.classList.remove('open');
        drawerOverlay.classList.remove('open');
    };

    // Interceptar clicks de botones de carrito/cotización
    document.querySelectorAll('.cart-icon-btn, .toggle-quote-drawer-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openDrawer();
        });
    });

    if (drawerClose) drawerClose.addEventListener('click', closeDrawer);
    if (drawerOverlay) drawerOverlay.addEventListener('click', closeDrawer);

    // 8. Enviar Solicitud por WhatsApp (Estructurada)
    const btnSubmitWhatsapp = document.getElementById('btn-submit-drawer-whatsapp');
    if (btnSubmitWhatsapp) {
        btnSubmitWhatsapp.addEventListener('click', () => {
            const hasLogo = document.getElementById('drawer-has-logo').value;
            const city = document.getElementById('drawer-city').value.trim();
            const obs = document.getElementById('drawer-notes').value.trim();

            if (!city) {
                alert("Por favor, ingresa la ciudad de entrega.");
                document.getElementById('drawer-city').focus();
                return;
            }

            fetch('cart-action.php?action=get')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.cart.length > 0) {
                        // Generar el mensaje estructurado
                        let msg = `Hola, quiero cotizar estos productos:\n\n`;
                        data.cart.forEach((item, idx) => {
                            msg += `${idx + 1}. *${item.name}*\n`;
                            msg += `Cantidad aproximada: ${item.qty} uds\n\n`;
                        });

                        msg += `Tengo logo: ${hasLogo}\n`;
                        msg += `Ciudad de entrega: ${city}\n`;
                        if (obs) {
                            msg += `Observaciones: ${obs}\n`;
                        }
                        msg += `\nPor favor ayúdenme con una cotización.`;

                        const phone = '593900000000'; // Número oficial de taller
                        const url = `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
                        
                        // Vaciar el carrito en sesión y localmente
                        fetch('cart-action.php?action=clear')
                            .then(() => {
                                updateDrawerUI();
                                closeDrawer();
                                window.open(url, '_blank');
                            });
                    }
                });
        });
    }

    // 9. Buscador y Filtros Combinados en Tiempo Real
    const searchInput = document.querySelector('.search-input');
    const filterButtons = document.querySelectorAll('.filter-bar .filter-btn');
    const productGrid = document.querySelector('.grid-3');

    // Cambiar input de búsqueda para que tenga ID si está presente
    if (searchInput) {
        searchInput.setAttribute('id', 'catalog-search');
    }

    let activeFilter = 'all';
    let searchQuery = '';

    const filterProducts = () => {
        const cards = document.querySelectorAll('.catalog-product-item');
        let matchedCount = 0;

        cards.forEach(card => {
            const name = card.getAttribute('data-name').toLowerCase();
            const category = card.getAttribute('data-category').toLowerCase();
            const material = card.getAttribute('data-material').toLowerCase();
            const technique = card.getAttribute('data-technique').toLowerCase();
            const use = card.getAttribute('data-use').toLowerCase();

            const queryMatch = name.includes(searchQuery) || 
                               category.includes(searchQuery) || 
                               material.includes(searchQuery) || 
                               technique.includes(searchQuery) || 
                               use.includes(searchQuery);

            let filterMatch = false;
            if (activeFilter === 'all') {
                filterMatch = true;
            } else {
                const normFilter = activeFilter.toLowerCase();
                filterMatch = name.includes(normFilter) || 
                              category.includes(normFilter) || 
                              material.includes(normFilter) || 
                              technique.includes(normFilter) || 
                              use.includes(normFilter);
            }

            if (queryMatch && filterMatch) {
                card.style.display = 'flex';
                matchedCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Gestionar el mensaje de no resultados
        let noResultsDiv = document.getElementById('catalog-no-results');
        if (matchedCount === 0) {
            if (!noResultsDiv) {
                noResultsDiv = document.createElement('div');
                noResultsDiv.setAttribute('id', 'catalog-no-results');
                noResultsDiv.style.gridColumn = '1 / -1';
                noResultsDiv.style.textAlign = 'center';
                noResultsDiv.style.padding = '3rem 0';
                noResultsDiv.style.color = 'var(--text-muted)';
                noResultsDiv.innerHTML = `
                    <p style="font-size:1rem; margin-bottom:1.5rem;">No encontramos ese producto en el catálogo, pero podemos revisarlo de forma personalizada.</p>
                    <a href="https://wa.me/593900000000?text=Hola,%20busco%20un%20producto%20específico%20que%20no%20encontré%20en%20el%20catálogo..." class="btn btn-primary" target="_blank" rel="noopener noreferrer">Consultar por WhatsApp</a>
                `;
                if (productGrid) productGrid.appendChild(noResultsDiv);
            } else {
                noResultsDiv.style.display = 'block';
            }
        } else {
            if (noResultsDiv) {
                noResultsDiv.style.display = 'none';
            }
        }
    };

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value.trim().toLowerCase();
            filterProducts();
        });
    }

    if (filterButtons.length > 0) {
        filterButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                filterButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                activeFilter = btn.getAttribute('data-filter') || btn.getAttribute('href').split('cat=')[1] || 'all';
                filterProducts();
            });
        });
    }

    // 10. Modal de Detalle de Producto
    const openModal = (title, cat, mat, tech, desc, slug) => {
        document.getElementById('modal-product-name').textContent = title;
        document.getElementById('modal-product-category').textContent = cat;
        document.getElementById('modal-product-material').textContent = mat;
        document.getElementById('modal-product-technique').textContent = tech;
        document.getElementById('modal-product-desc').textContent = desc;

        // Configurar botón del modal de añadir
        const addBtn = document.getElementById('modal-add-btn');
        addBtn.onclick = () => {
            addToQuote(title, slug, 2.50);
            closeModal();
        };

        // Configurar WhatsApp directo
        const waLink = document.getElementById('modal-whatsapp-direct');
        waLink.setAttribute('href', `https://wa.me/593900000000?text=Hola,%20me%20interesa%20obtener%20más%20detalles%20de%20este%20producto:%20${encodeURIComponent(title)}`);

        detailModalOverlay.classList.add('open');
        document.body.style.overflow = 'hidden'; // Evitar scroll
    };

    const closeModal = () => {
        detailModalOverlay.classList.remove('open');
        document.body.style.overflow = '';
    };

    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-view-details')) {
            const btn = e.target;
            const title = btn.getAttribute('data-name');
            const cat = btn.getAttribute('data-category');
            const mat = btn.getAttribute('data-material');
            const tech = btn.getAttribute('data-technique');
            const desc = btn.getAttribute('data-desc');
            const slug = btn.getAttribute('data-slug');
            openModal(title, cat, mat, tech, desc, slug);
        }
    });

    if (detailClose) detailClose.addEventListener('click', closeModal);
    if (detailModalOverlay) {
        detailModalOverlay.addEventListener('click', (e) => {
            if (e.target === detailModalOverlay) closeModal();
        });
    }

    // 11. Acordeón de FAQs
    const accordionTriggers = document.querySelectorAll('.faq-trigger');
    accordionTriggers.forEach(trigger => {
        trigger.addEventListener('click', () => {
            const item = trigger.closest('.faq-item');
            const content = item.querySelector('.faq-content');
            const isActive = item.classList.contains('active');

            // Cerrar otros
            document.querySelectorAll('.faq-item').forEach(i => {
                i.classList.remove('active');
                i.querySelector('.faq-content').style.maxHeight = null;
            });

            if (!isActive) {
                item.classList.add('active');
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        });
    });

    // 12. Menú de Scroll Activo (Scrollspy)
    const sections = document.querySelectorAll('section[id], header[id]');
    const navLinks = document.querySelectorAll('.nav-menu .nav-link, .mobile-nav .mobile-link');

    const handleScrollActiveLink = () => {
        const scrollPos = window.scrollY || document.documentElement.scrollTop;

        sections.forEach(section => {
            const sectionTop = section.offsetTop - 120;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');

            if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                navLinks.forEach(link => {
                    const href = link.getAttribute('href');
                    if (href && (href.includes(`#${sectionId}`) || (sectionId === 'inicio' && href === 'index.php'))) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });
            }
        });
    };

    window.addEventListener('scroll', handleScrollActiveLink);

    // 13. Tooltip Discreto en WhatsApp Flotante
    const waFloat = document.querySelector('.whatsapp-float');
    if (waFloat) {
        waFloat.setAttribute('title', '¿Tienes dudas? Escríbenos por WhatsApp');
    }

    // 14. Controlador de Carrusel del Hero (Derecha)
    const heroSlides = document.querySelectorAll('.hero-slide-item');
    const heroDots = document.querySelectorAll('.hero-dot');
    
    if (heroSlides.length > 1) {
        let currentSlide = 0;
        const slideInterval = 5000; // 5 segundos por imagen

        const goToSlide = (idx) => {
            heroSlides.forEach((slide, sIdx) => {
                if (sIdx === idx) {
                    slide.style.opacity = '1';
                    slide.style.zIndex = '5';
                    slide.classList.add('active');
                } else {
                    slide.style.opacity = '0';
                    slide.style.zIndex = '1';
                    slide.classList.remove('active');
                }
            });

            heroDots.forEach((dot, dIdx) => {
                if (dIdx === idx) {
                    dot.style.background = 'var(--primary)';
                    dot.classList.add('active');
                } else {
                    dot.style.background = 'rgba(255,255,255,0.4)';
                    dot.classList.remove('active');
                }
            });
            currentSlide = idx;
        };

        const nextSlide = () => {
            const next = (currentSlide + 1) % heroSlides.length;
            goToSlide(next);
        };

        let timer = setInterval(nextSlide, slideInterval);

        heroDots.forEach((dot, idx) => {
            dot.addEventListener('click', () => {
                clearInterval(timer);
                goToSlide(idx);
                timer = setInterval(nextSlide, slideInterval);
            });
        });

        // Forzar el estado inicial
        goToSlide(0);
    }
});
