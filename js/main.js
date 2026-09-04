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

        // Inyectar Barra Inferior en Móviles con SVG Modernos y Limpios
        const defaultWaUrl = window.CARDNET_WA_URL || ('https://wa.me/' + (window.CARDNET_WA_PHONE || '59399978180'));
        const bottomBarHtml = `
            <div class="mobile-bottom-bar">
                <a href="${defaultWaUrl}" class="btn btn-secondary mobile-bottom-btn-wa" target="_blank" rel="noopener">
                    <svg style="width: 14px; height: 14px; fill: #25D366;" viewBox="0 0 24 24">
                        <path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 0 0 1.333 4.982L2 22l5.233-1.371a9.994 9.994 0 0 0 4.779 1.22h.005c5.505 0 9.99-4.478 9.99-9.985A9.988 9.988 0 0 0 12.012 2zm4.7 13.916c-.223.633-1.29 1.205-1.782 1.282-.477.075-.947.168-3.067-.665-2.707-1.06-4.442-3.817-4.577-3.996-.134-.178-1.096-1.455-1.096-2.781 0-1.325.692-1.973.938-2.228.246-.255.535-.319.714-.319.18 0 .358.001.514.009.16.008.375-.062.586.448.223.54.76 1.851.827 1.984.067.134.112.29.022.468-.09.18-.134.29-.268.447-.134.156-.282.35-.403.47-.134.134-.273.28-.117.548.156.268.693 1.139 1.492 1.85 1.026.914 1.89 1.196 2.158 1.33.268.134.424.112.58-.067.157-.18.67-.781.848-1.049.178-.268.358-.223.58-.134.224.089 1.42.67 1.666.792.246.123.411.18.47.282.06.101.06.586-.163 1.218z"/>
                    </svg>
                    WhatsApp
                </a>
                <button class="btn btn-primary toggle-quote-drawer-btn mobile-bottom-btn-quote">
                    <svg style="width: 13px; height: 13px; fill: none; stroke: currentColor; stroke-width: 2.5;" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    Mi Cotización
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

                        const phone = window.CARDNET_WA_PHONE || '59399978180'; // Número oficial de taller con 593
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

    // 9. Buscador Global con Auto-Sugerencias en Tiempo Real y Filtros de Catálogo
    const escapeHtmlHelper = (str) => {
        if (!str) return '';
        return String(str).replace(/[&<>'"]/g, tag => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#39;',
            '"': '&quot;'
        }[tag] || tag));
    };

    // A. Buscador del Encabezado Estilo Google (Universal)
    const headerSearchForm = document.getElementById('global-header-search');
    const headerSearchInput = headerSearchForm ? headerSearchForm.querySelector('.search-input') : document.querySelector('.search-input');
    const headerSearchDropdown = document.getElementById('search-results-dropdown');
    const headerClearBtn = document.getElementById('search-clear-btn');

    if (headerSearchInput && headerSearchDropdown) {
        let searchDebounceTimer = null;
        let currentSelectedIndex = -1;

        const updateClearBtn = () => {
            if (headerClearBtn) {
                headerClearBtn.style.display = headerSearchInput.value.trim().length > 0 ? 'flex' : 'none';
            }
        };

        if (headerClearBtn) {
            headerClearBtn.addEventListener('click', () => {
                headerSearchInput.value = '';
                updateClearBtn();
                headerSearchInput.focus();
                closeDropdown();
            });
        }

        const closeDropdown = () => {
            headerSearchDropdown.style.display = 'none';
            headerSearchDropdown.innerHTML = '';
            currentSelectedIndex = -1;
            if (headerSearchForm) {
                headerSearchForm.classList.remove('search-active');
            }
        };

        const escapeRegex = (string) => {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        };

        const renderSearchResults = (query, data) => {
            if (!data.results || data.results.length === 0) {
                headerSearchDropdown.innerHTML = `
                    <div class="search-dropdown-empty" style="padding: 26px 18px; text-align: center; color: #5f6368;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: #f1f3f4; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; color: #70757a;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </div>
                        <div style="font-size: 0.92rem; font-weight: 500; color: #202124; margin-bottom: 6px;">
                            No encontramos productos para "<strong>${escapeHtmlHelper(query)}</strong>"
                        </div>
                        <p style="font-size: 0.8rem; color: #70757a; margin: 0 0 14px 0; line-height: 1.4;">
                            Revisa la ortografía o consulta por WhatsApp si deseas una personalización a medida.
                        </p>
                        <a href="productos.php" style="display: inline-block; padding: 7px 18px; background: #f8f9fa; border: 1px solid #dadce0; border-radius: 18px; font-size: 0.8rem; color: var(--primary); font-weight: 600; text-decoration: none;">
                            Ver todo el catálogo →
                        </a>
                    </div>
                `;
                headerSearchDropdown.style.display = 'block';
                if (headerSearchForm) headerSearchForm.classList.add('search-active');
                currentSelectedIndex = -1;
                return;
            }

            let html = '';
            const safeQ = escapeRegex(query);
            const qReg = safeQ ? new RegExp('(' + safeQ + ')', 'gi') : null;

            data.results.forEach((item, idx) => {
                let nameHtml = escapeHtmlHelper(item.name);
                if (qReg) {
                    nameHtml = nameHtml.replace(qReg, '<strong style="color: #202124; font-weight: 700;">$1</strong>');
                }

                html += `
                    <a href="${item.url}" class="search-dropdown-item" data-index="${idx}" role="option" style="display: flex; align-items: center; gap: 14px; padding: 10px 18px; text-decoration: none; color: #202124; transition: background-color 0.12s ease; border-bottom: 1px solid #f5f5f5; box-sizing: border-box;">
                        <div class="search-dropdown-img-wrap" style="width: 44px; height: 44px; min-width: 44px; max-width: 44px; min-height: 44px; max-height: 44px; border-radius: 8px; background: #f8f9fa; border: 1px solid #e8eaed; padding: 2px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; overflow: hidden; box-sizing: border-box;">
                            <img src="${item.image}" alt="${escapeHtmlHelper(item.name)}" class="search-dropdown-img" loading="lazy" style="width: 100% !important; height: 100% !important; max-width: 100% !important; max-height: 100% !important; object-fit: contain !important; border-radius: 6px; display: block;">
                        </div>
                        <div class="search-dropdown-info" style="flex: 1; min-width: 0;">
                            <div class="search-dropdown-title" style="font-size: 0.88rem; font-weight: 500; color: #202124; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.3;">
                                ${nameHtml}
                            </div>
                            <div class="search-dropdown-meta" style="display: flex; align-items: center; gap: 8px; margin-top: 3px; font-size: 0.74rem; color: #70757a;">
                                <span class="search-dropdown-cat" style="background: #eef7e9; color: #3d781a; padding: 2px 8px; border-radius: 10px; font-weight: 600; font-size: 0.7rem;">${escapeHtmlHelper(item.category)}</span>
                                <span class="search-dropdown-price" style="font-weight: 700; color: #1f2328;">$${item.price}</span>
                            </div>
                        </div>
                        <div class="search-dropdown-arrow" style="color: #9aa0a6; flex-shrink: 0; display: flex; align-items: center;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </div>
                    </a>
                `;
            });

            html += `
                <a href="productos.php?q=${encodeURIComponent(query)}" class="search-dropdown-footer" style="padding: 12px 18px; border-top: 1px solid #ebebeb; display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem; color: var(--primary); font-weight: 600; cursor: pointer; text-decoration: none; background: #f8f9fa; border-radius: 0 0 24px 24px; box-sizing: border-box;">
                    <span style="display: flex; align-items: center; gap: 8px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Ver todos los resultados (${data.count}) en el catálogo
                    </span>
                    <span style="background: #ffffff; border: 1px solid #dadce0; border-radius: 4px; padding: 2px 7px; font-size: 11px; color: #70757a; font-family: inherit;">Enter ↵</span>
                </a>
            `;

            headerSearchDropdown.innerHTML = html;
            headerSearchDropdown.style.display = 'block';
            if (headerSearchForm) headerSearchForm.classList.add('search-active');
            currentSelectedIndex = -1;
        };

        const fetchSearchSuggestions = (query) => {
            if (!query || query.length < 2) {
                closeDropdown();
                return;
            }

            fetch('search-api.php?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        renderSearchResults(query, data);
                    } else {
                        closeDropdown();
                    }
                })
                .catch(() => {
                    closeDropdown();
                });
        };

        headerSearchInput.addEventListener('input', (e) => {
            clearTimeout(searchDebounceTimer);
            updateClearBtn();
            const query = e.target.value.trim();
            if (query.length < 2) {
                closeDropdown();
                return;
            }
            searchDebounceTimer = setTimeout(() => {
                fetchSearchSuggestions(query);
            }, 180);
        });

        updateClearBtn();

        headerSearchInput.addEventListener('keydown', (e) => {
            const items = headerSearchDropdown.querySelectorAll('.search-dropdown-item');
            if (e.key === 'ArrowDown') {
                if (headerSearchDropdown.style.display === 'block' && items.length > 0) {
                    e.preventDefault();
                    currentSelectedIndex = (currentSelectedIndex + 1) % items.length;
                    items.forEach((it, i) => it.classList.toggle('active', i === currentSelectedIndex));
                    if (items[currentSelectedIndex]) items[currentSelectedIndex].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.key === 'ArrowUp') {
                if (headerSearchDropdown.style.display === 'block' && items.length > 0) {
                    e.preventDefault();
                    currentSelectedIndex = (currentSelectedIndex - 1 + items.length) % items.length;
                    items.forEach((it, i) => it.classList.toggle('active', i === currentSelectedIndex));
                    if (items[currentSelectedIndex]) items[currentSelectedIndex].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.key === 'Enter') {
                if (currentSelectedIndex >= 0 && items[currentSelectedIndex]) {
                    e.preventDefault();
                    window.location.href = items[currentSelectedIndex].getAttribute('href');
                }
                // Si no hay item seleccionado, el formulario se envía normalmente hacia productos.php?q=...
            } else if (e.key === 'Escape') {
                closeDropdown();
            }
        });

        // Cerrar menú desplegable al hacer clic fuera del buscador
        document.addEventListener('click', (e) => {
            if (headerSearchForm && !headerSearchForm.contains(e.target)) {
                closeDropdown();
            }
        });

        headerSearchInput.addEventListener('focus', () => {
            updateClearBtn();
            const query = headerSearchInput.value.trim();
            if (query.length >= 2 && headerSearchDropdown.children.length > 0) {
                headerSearchDropdown.style.display = 'block';
                if (headerSearchForm) headerSearchForm.classList.add('search-active');
            }
        });
    }

    // B. Buscador Local y Filtros en la Página de Catálogo (productos.php)
    const catalogSearchInput = document.getElementById('product-search');
    const catalogCards = document.querySelectorAll('.catalog-product-item');
    const filterButtons = document.querySelectorAll('.filter-bar .filter-btn');
    const catalogGrid = document.querySelector('.grid-3');
    const catalogResultsCount = document.getElementById('search-results-count');

    let activeFilter = 'all';
    let localQuery = catalogSearchInput ? catalogSearchInput.value.trim().toLowerCase() : '';

    const filterCatalogCards = () => {
        if (!catalogCards.length) return;
        let matchedCount = 0;

        catalogCards.forEach(card => {
            const name = (card.getAttribute('data-name') || '').toLowerCase();
            const category = (card.getAttribute('data-category') || '').toLowerCase();
            const material = (card.getAttribute('data-material') || '').toLowerCase();
            const technique = (card.getAttribute('data-technique') || '').toLowerCase();
            const use = (card.getAttribute('data-use') || '').toLowerCase();

            const matchesQuery = !localQuery || 
                                 name.includes(localQuery) || 
                                 category.includes(localQuery) || 
                                 material.includes(localQuery) || 
                                 technique.includes(localQuery) || 
                                 use.includes(localQuery);

            let matchesCategory = false;
            if (activeFilter === 'all') {
                matchesCategory = true;
            } else {
                const normFilter = activeFilter.toLowerCase();
                matchesCategory = name.includes(normFilter) || 
                                  category.includes(normFilter) || 
                                  material.includes(normFilter) || 
                                  technique.includes(normFilter) || 
                                  use.includes(normFilter);
            }

            if (matchesQuery && matchesCategory) {
                card.style.display = '';
                matchedCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (catalogResultsCount) {
            if (localQuery) {
                catalogResultsCount.style.display = 'block';
                catalogResultsCount.textContent = `${matchedCount} producto${matchedCount !== 1 ? 's' : ''} encontrado${matchedCount !== 1 ? 's' : ''}`;
            } else {
                catalogResultsCount.style.display = 'none';
            }
        }

        let noResultsEl = document.getElementById('catalog-no-results');
        if (matchedCount === 0) {
            if (!noResultsEl) {
                noResultsEl = document.createElement('div');
                noResultsEl.setAttribute('id', 'catalog-no-results');
                noResultsEl.style.gridColumn = '1 / -1';
                noResultsEl.style.textAlign = 'center';
                noResultsEl.style.padding = '3rem 1rem';
                noResultsEl.style.color = 'var(--text-muted)';
                const waCatalogPhone = window.CARDNET_WA_PHONE || '59399978180';
                noResultsEl.innerHTML = `
                    <p style="font-size:1rem; margin-bottom:1.5rem;">No encontramos ese producto en este filtro, pero podemos grabarlo o fabricarlo a tu medida.</p>
                    <a href="https://wa.me/${waCatalogPhone}?text=Hola,%20busco%20un%20producto%20específico%20en%20CardNet..." class="btn btn-primary" target="_blank" rel="noopener noreferrer">Consultar por WhatsApp</a>
                `;
                if (catalogGrid) catalogGrid.appendChild(noResultsEl);
            } else {
                noResultsEl.style.display = 'block';
            }
        } else {
            if (noResultsEl) {
                noResultsEl.style.display = 'none';
            }
        }
    };

    if (catalogSearchInput) {
        catalogSearchInput.addEventListener('input', (e) => {
            localQuery = e.target.value.trim().toLowerCase();
            filterCatalogCards();
        });
    }

    if (filterButtons.length > 0) {
        filterButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const fVal = btn.getAttribute('data-filter');
                if (fVal) {
                    e.preventDefault();
                    filterButtons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    activeFilter = fVal;
                    filterCatalogCards();
                }
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
        const waModalPhone = window.CARDNET_WA_PHONE || '59399978180';
        const waLink = document.getElementById('modal-whatsapp-direct');
        waLink.setAttribute('href', `https://wa.me/${waModalPhone}?text=Hola,%20me%20interesa%20obtener%20más%20detalles%20de%20este%20producto:%20${encodeURIComponent(title)}`);

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

    let scrollTicking = false;
    window.addEventListener('scroll', () => {
        if (!scrollTicking) {
            window.requestAnimationFrame(() => {
                handleScrollActiveLink();
                scrollTicking = false;
            });
            scrollTicking = true;
        }
    }, { passive: true });

    // 13. Tooltip Discreto en WhatsApp Flotante
    const waFloat = document.querySelector('.whatsapp-float');
    if (waFloat) {
        waFloat.setAttribute('title', '¿Tienes dudas? Escríbenos por WhatsApp');
    }

    // 14. Galería de transición automática en Hover y Touch
    const initHoverGalleries = () => {
        const cards = document.querySelectorAll('.product-card');
        
        cards.forEach(card => {
            const img = card.querySelector('.product-card-image-wrap img');
            if (!img) return;
            
            const galleryData = card.getAttribute('data-gallery');
            if (!galleryData) return;
            
            let gallery = [];
            try {
                gallery = JSON.parse(galleryData);
            } catch(e) {
                return;
            }
            
            if (gallery.length <= 1) return;
            
            let intervalId = null;
            let currentIndex = 0;
            const originalSrc = img.getAttribute('src');
            
            const startCycling = () => {
                if (intervalId) return;
                intervalId = setInterval(() => {
                    currentIndex = (currentIndex + 1) % gallery.length;
                    img.src = gallery[currentIndex];
                }, 1300); // Transición suave cada 1.3 segundos
            };
            
            const stopCycling = () => {
                if (intervalId) {
                    clearInterval(intervalId);
                    intervalId = null;
                }
                currentIndex = 0;
                img.src = originalSrc;
            };
            
            // Eventos en Escritorio (Mouse)
            card.addEventListener('mouseenter', startCycling);
            card.addEventListener('mouseleave', stopCycling);
            
            // Eventos en Móviles (Táctil)
            card.addEventListener('touchstart', () => {
                startCycling();
            }, { passive: true });
            
            card.addEventListener('touchend', () => {
                // En móviles, detener después de 2 segundos para dar tiempo a ver la transición
                setTimeout(stopCycling, 2000);
            }, { passive: true });
        });
    };
    
    initHoverGalleries();
});

