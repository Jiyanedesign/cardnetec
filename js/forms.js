/* ==========================================================================
   CardNet.ec - Validación de Formularios y Respuestas de Conversión (forms.js)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    const quoteForm = document.querySelector('#quote-form');
    const contactForm = document.querySelector('#contact-form');

    // Función Auxiliar para Validar el Correo
    const isValidEmail = (email) => {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    };

    // Función Auxiliar para Validar Teléfono (Mínimo 9 caracteres)
    const isValidPhone = (phone) => {
        const cleanPhone = phone.replace(/\D/g, '');
        return cleanPhone.length >= 9;
    };

    // Procesar Validaciones Visuales y Errores
    const validateField = (input, validationFn, errorMsgText) => {
        const formGroup = input.closest('.form-group');
        if (!formGroup) return true;

        const errorMsg = formGroup.querySelector('.form-error-msg');
        const value = input.value.trim();

        let isValid = true;
        if (value === '' || (validationFn && !validationFn(value))) {
            isValid = false;
        }

        if (!isValid) {
            formGroup.classList.add('has-error');
            if (errorMsg) {
                errorMsg.textContent = errorMsgText;
                errorMsg.style.display = 'block';
            }
        } else {
            formGroup.classList.remove('has-error');
            if (errorMsg) {
                errorMsg.style.display = 'none';
            }
        }

        return isValid;
    };

    // Configuración del Formulario de Cotización
    if (quoteForm) {
        quoteForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const nameInput = quoteForm.querySelector('#quote-name') || quoteForm.querySelector('#name');
            const companyInput = quoteForm.querySelector('#quote-company') || quoteForm.querySelector('#company');
            const emailInput = quoteForm.querySelector('#quote-email') || quoteForm.querySelector('#email');
            const phoneInput = quoteForm.querySelector('#quote-phone') || quoteForm.querySelector('#phone');
            const logoInput = quoteForm.querySelector('#quote-logo') || quoteForm.querySelector('#logo');
            const notesTextarea = quoteForm.querySelector('#quote-notes') || quoteForm.querySelector('#details');

            let isFormValid = true;

            // Validar Campos Existentes
            if (nameInput && !validateField(nameInput, null, 'Por favor, ingresa tu nombre completo.')) isFormValid = false;
            if (emailInput && !validateField(emailInput, isValidEmail, 'Por favor, ingresa un correo electrónico válido.')) isFormValid = false;
            if (phoneInput && !validateField(phoneInput, isValidPhone, 'Ingresa un número telefónico de contacto válido (mínimo 9 dígitos).')) isFormValid = false;

            if (isFormValid) {
                const submitBtn = quoteForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Procesando cotización...';

                // Crear FormData para enviar
                const formData = new FormData();
                formData.append('name', nameInput.value);
                
                const selectedProd = document.getElementById('selected-product');
                const selectedType = document.getElementById('selected-type');
                const selectedQty = document.getElementById('selected-qty-range');
                if (selectedProd) formData.append('custom_product', selectedProd.value);
                if (selectedType) formData.append('custom_type', selectedType.value);
                if (selectedQty) formData.append('custom_qty', selectedQty.value);
                if (companyInput) formData.append('company', companyInput.value);
                formData.append('whatsapp', phoneInput.value);
                if (emailInput) formData.append('email', emailInput.value);
                if (notesTextarea) formData.append('message', notesTextarea.value);
                
                if (logoInput && logoInput.files.length > 0) {
                    formData.append('logo', logoInput.files[0]);
                }

                // Enviar al procesador PHP
                fetch('cotizar-action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success reveal-on-scroll active';
                        alertDiv.style.cssText = 'padding: 18px 22px; border-radius: 10px; margin-bottom: 24px; background: #ecfdf5; border: 1.5px solid #10b981; color: #065f46; font-size: 0.95rem; line-height: 1.5; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.15);';
                        alertDiv.innerHTML = `
                            <div style="display: flex; align-items: flex-start; gap: 14px;">
                                <span style="font-size: 1.6rem; line-height: 1;">✅</span>
                                <div>
                                    <strong style="display: block; font-size: 1.15rem; margin-bottom: 6px; color: #047857; font-weight: 700;">¡Cotización enviada con éxito!</strong>
                                    <span style="display: block; color: #065f46;">Hemos recibido tu solicitud correctamente. Pronto nuestro equipo se pondrá en contacto contigo para atenderte y enviarte tu presupuesto.</span>
                                </div>
                            </div>
                        `;
                        
                        quoteForm.prepend(alertDiv);
                        quoteForm.reset();
                        
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;

                        // Desplazar suavemente hacia el mensaje de éxito
                        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

                        setTimeout(() => {
                            // Recargar tras 7 segundos para refrescar el resumen del carrito
                            window.location.reload();
                        }, 7000);
                    } else {
                        alert(data.message || 'Ocurrió un error al procesar tu solicitud.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error de red al enviar la cotización.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            }
        });
    }

    // Configuración del Formulario de Contacto
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const nameInput = contactForm.querySelector('#name') || contactForm.querySelector('#quote-name');
            const emailInput = contactForm.querySelector('#email') || contactForm.querySelector('#quote-email');
            const messageTextarea = contactForm.querySelector('#message') || contactForm.querySelector('#quote-notes');

            let isFormValid = true;

            if (!validateField(nameInput, null, 'Por favor, introduce tu nombre.')) isFormValid = false;
            if (!validateField(emailInput, isValidEmail, 'Por favor, proporciona un correo de contacto válido.')) isFormValid = false;
            if (!validateField(messageTextarea, null, 'Por favor, escribe tu consulta o mensaje.')) isFormValid = false;

            if (isFormValid) {
                const submitBtn = contactForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Enviando mensaje...';

                // Enviar cotización por defecto
                const formData = new FormData();
                formData.append('name', nameInput.value);
                formData.append('whatsapp', '099000000');
                formData.append('email', emailInput.value);
                formData.append('message', messageTextarea.value);

                fetch('cotizar-action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success reveal-on-scroll active';
                    alertDiv.style.cssText = 'padding: 18px 22px; border-radius: 10px; margin-bottom: 24px; background: #ecfdf5; border: 1.5px solid #10b981; color: #065f46; font-size: 0.95rem; line-height: 1.5; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.15);';
                    alertDiv.innerHTML = `
                        <div style="display: flex; align-items: flex-start; gap: 14px;">
                            <span style="font-size: 1.6rem; line-height: 1;">✅</span>
                            <div>
                                <strong style="display: block; font-size: 1.15rem; margin-bottom: 6px; color: #047857; font-weight: 700;">¡Mensaje enviado con éxito!</strong>
                                <span style="display: block; color: #065f46;">Hemos recibido tu consulta correctamente. Pronto nuestro equipo se pondrá en contacto contigo para atenderte.</span>
                            </div>
                        </div>
                    `;
                    
                    contactForm.prepend(alertDiv);
                    contactForm.reset();
                    
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;

                    alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    setTimeout(() => alertDiv.remove(), 8000);
                })
                .catch(err => {
                    console.error(err);
                    alert('Error de red.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            }
        });
    }
});
