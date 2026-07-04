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

            const nameInput = quoteForm.querySelector('#name');
            const emailInput = quoteForm.querySelector('#email');
            const phoneInput = quoteForm.querySelector('#phone');
            const productSelect = quoteForm.querySelector('#product-type');
            const quantityInput = quoteForm.querySelector('#quantity');
            const detailsTextarea = quoteForm.querySelector('#details');

            let isFormValid = true;

            // Validar Campo por Campo
            if (!validateField(nameInput, null, 'Por favor, ingresa tu nombre completo.')) isFormValid = false;
            if (!validateField(emailInput, isValidEmail, 'Por favor, ingresa un correo electrónico válido.')) isFormValid = false;
            if (!validateField(phoneInput, isValidPhone, 'Ingresa un número telefónico de contacto válido (mínimo 9 dígitos).')) isFormValid = false;
            if (!validateField(productSelect, null, 'Por favor, selecciona una categoría de producto.')) isFormValid = false;
            if (!validateField(quantityInput, (val) => parseInt(val) > 0, 'La cantidad estimada debe ser mayor a 0.')) isFormValid = false;

            if (isFormValid) {
                // Simulación de Envío Exitoso
                const submitBtn = quoteForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Procesando cotización...';

                // Simular Latencia de Red
                setTimeout(() => {
                    // Mostrar Alerta de Éxito
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success reveal-on-scroll active';
                    alertDiv.innerHTML = `
                        <strong>¡Solicitud Recibida!</strong> Hemos registrado tus requerimientos de personalización. Un diseñador comercial de CardNet.ec se comunicará contigo en menos de 2 horas.
                    `;
                    
                    quoteForm.prepend(alertDiv);
                    quoteForm.reset();
                    
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;

                    // Remover alerta tras 8 segundos
                    setTimeout(() => alertDiv.remove(), 8000);
                }, 1500);
            }
        });
    }

    // Configuración del Formulario de Contacto
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const nameInput = contactForm.querySelector('#name');
            const emailInput = contactForm.querySelector('#email');
            const messageTextarea = contactForm.querySelector('#message');

            let isFormValid = true;

            if (!validateField(nameInput, null, 'Por favor, introduce tu nombre.')) isFormValid = false;
            if (!validateField(emailInput, isValidEmail, 'Por favor, proporciona un correo de contacto válido.')) isFormValid = false;
            if (!validateField(messageTextarea, null, 'Por favor, escribe tu consulta o mensaje.')) isFormValid = false;

            if (isFormValid) {
                const submitBtn = contactForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Enviando mensaje...';

                setTimeout(() => {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success reveal-on-scroll active';
                    alertDiv.innerHTML = `
                        <strong>¡Mensaje Enviado!</strong> Gracias por ponerte en contacto. Nos comunicaremos contigo a la brevedad posible.
                    `;
                    
                    contactForm.prepend(alertDiv);
                    contactForm.reset();
                    
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;

                    setTimeout(() => alertDiv.remove(), 8000);
                }, 1200);
            }
        });
    }
});
