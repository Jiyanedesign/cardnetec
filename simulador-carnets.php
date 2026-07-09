<?php
session_start();
require_once 'db.php';

$settings = getSiteSettings($pdo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diseño y Simulación de Credenciales PVC | CardNet.ec</title>
    <meta name="description" content="Diseña, previsualiza y cotiza credenciales, identificaciones y carnets de PVC corporativos en línea con códigos QR y fotos de empleados.">
    
    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=3.6">
    <link rel="stylesheet" href="css/layout.css?v=3.6">
    <link rel="stylesheet" href="css/components.css?v=3.6">
    <link rel="stylesheet" href="css/pages.css?v=3.6">
    <link rel="stylesheet" href="css/animations.css?v=1.1.2">

    <!-- Fabric.js y QRCode.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .simulator-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 3rem;
            margin-top: 2rem;
            align-items: start;
        }
        @media (min-width: 1024px) {
            .simulator-layout {
                grid-template-columns: 0.9fr 1.1fr;
            }
        }
        .canvas-container-box {
            position: relative;
            background-color: var(--surface-light);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        .canvas-wrapper {
            position: relative;
            box-shadow: var(--shadow-lg);
            border-radius: var(--radius-sm);
            overflow: hidden;
            background-color: white;
            border: 1px solid var(--border);
        }
        .controls-panel {
            background-color: var(--light);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            box-shadow: var(--shadow-md);
        }
        #qr-hidden {
            display: none;
        }
        .purchase-box {
            background-color: var(--surface-light);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 1.5rem;
            margin-top: 1rem;
        }
        .purchase-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .qty-selectors {
            display: flex;
            align-items: center;
            background-color: var(--light);
            border-radius: 20px;
            padding: 4px 8px;
            width: fit-content;
            border: 1px solid var(--border);
        }
        .qty-btn {
            background: none;
            border: none;
            color: var(--text-main);
            font-size: 1.2rem;
            width: 32px;
            height: 32px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            user-select: none;
        }
        .qty-input {
            width: 40px;
            text-align: center;
            background: none;
            border: none;
            color: var(--text-main);
            font-weight: bold;
            font-size: 1rem;
            outline: none;
        }
        .btn-gradient {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            width: 100%;
            background-color: var(--primary);
            color: white;
            padding: 16px;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-fast);
            text-decoration: none;
        }
        .btn-gradient:hover {
            background-color: var(--primary-hover);
        }
    </style>
</head>
<body>

    <!-- Barra de Anuncios Superior -->
    <div class="top-announcement-bar">
        Taller de credenciales y carnets PVC en Quito | Envíos a todo el Ecuador
    </div>

    <?php include 'includes/header.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="container section-padding">
        <div class="section-header">
            <span class="section-subtitle">Simulador de Credenciales</span>
            <h1 style="font-family: var(--font-heading); font-weight: 400; margin-bottom: 0.5rem; font-size: 2.2rem;">Crea y simula tus carnets PVC</h1>
            <p>Elige una plantilla y completa los datos para previsualizar cómo quedarán las identificaciones de tu personal.</p>
        </div>

        <div class="simulator-layout">
            
            <!-- Área del Canvas (Visualización) -->
            <div class="canvas-container-box">
                <div class="canvas-wrapper">
                    <canvas id="carnet-canvas" width="320" height="480"></canvas>
                </div>
            </div>

            <!-- Panel de Controles -->
            <div class="controls-panel">
                <div class="form-group">
                    <label class="form-label" for="template-select">1. Tipo de Credencial</label>
                    <select class="sim-select" id="template-select" style="width: 100%; border: 1px solid var(--border); padding: 10px; border-radius: 4px; background: white;">
                        <option value="corporativo">Corporativo Formal (Gris/Verde)</option>
                        <option value="seguridad">Seguridad / Acceso (Negro/Rojo)</option>
                        <option value="evento">Acceso de Evento (Azul Marino)</option>
                    </select>
                </div>

                <div class="grid-2" style="gap: 15px;">
                    <div class="form-group">
                        <label class="form-label" for="logo-input">Logo Corporativo</label>
                        <input class="form-input" type="file" id="logo-input" accept="image/png, image/jpeg" style="border: 1px solid var(--border); padding: 8px; width: 100%; box-sizing: border-box;">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="photo-input">Foto de Empleado</label>
                        <input class="form-input" type="file" id="photo-input" accept="image/png, image/jpeg" style="border: 1px solid var(--border); padding: 8px; width: 100%; box-sizing: border-box;">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="name-input">Nombre Completo</label>
                    <input class="form-input" type="text" id="name-input" value="Alejandro Silva" style="border: 1px solid var(--border); padding: 10px; width: 100%; box-sizing: border-box; border-radius: 4px;">
                </div>

                <div class="grid-2" style="gap: 15px;">
                    <div class="form-group">
                        <label class="form-label" for="role-input">Cargo / Puesto</label>
                        <input class="form-input" type="text" id="role-input" value="Director Operativo" style="border: 1px solid var(--border); padding: 10px; width: 100%; box-sizing: border-box; border-radius: 4px;">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="id-input">Número de Cédula</label>
                        <input class="form-input" type="text" id="id-input" value="1725489630" style="border: 1px solid var(--border); padding: 10px; width: 100%; box-sizing: border-box; border-radius: 4px;">
                    </div>
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" id="qr-toggle" checked style="cursor: pointer; width: 18px; height: 18px;">
                    <label for="qr-toggle" style="font-size: 0.9rem; cursor: pointer; user-select: none;">Incluir Código QR de verificación de seguridad</label>
                </div>

                <!-- Caja de Compra y Subtotales PVC -->
                <div class="purchase-box">
                    <div class="purchase-row">
                        <div>
                            <span style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; font-weight:600; display:block; margin-bottom:4px;">Cantidad</span>
                            <div class="qty-selectors">
                                <button class="qty-btn" id="qty-minus">-</button>
                                <input type="text" class="qty-input" id="qty-input" value="50" readonly>
                                <button class="qty-btn" id="qty-plus">+</button>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; font-weight:600; display:block; margin-bottom:4px;">Precio Unit.</span>
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);">$1.80</div>
                        </div>
                    </div>
                    
                    <div class="subtotal-row" style="border-top: 1px solid var(--border); padding-top: 1rem; margin-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 600; font-size: 0.88rem; text-transform: uppercase;">Subtotal Estimado</span>
                        <span style="font-size: 1.8rem; font-weight: bold; color: var(--primary);" id="subtotal-val">$90.00</span>
                    </div>

                    <p style="font-size:0.75rem; color:var(--text-muted); text-align:center; margin: 10px 0 15px 0;">
                        Los valores incluyen impresión a color en PVC de alta resistencia.
                    </p>

                    <button class="btn-gradient" id="btn-submit-pvc">
                        Guardar en mi Carrito
                    </button>
                </div>

                <button id="btn-reset" style="padding:10px; background:none; border:1px solid var(--border); border-radius:4px; font-weight:600; color:var(--text-muted); cursor:pointer;">Restablecer Formulario</button>
            </div>

        </div>
    </main>

    <!-- Contenedor oculto para la generación del código QR -->
    <div id="qr-hidden"></div>

    <!-- Modal Informativo de Carrito -->
    <div id="cart-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center; color: var(--text-main);">
        <div style="background:white; padding:2.5rem; border-radius:var(--radius-lg); max-width:420px; width:90%; text-align:center; box-shadow:var(--shadow-lg);">
            <svg width="48" height="48" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24" style="margin:0 auto 1.5rem auto;">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <h3 style="font-family:var(--font-heading); font-size:1.5rem; margin-bottom:0.75rem;">¡Añadido al Cotizador!</h3>
            <p style="color:var(--text-muted); font-size:0.9rem; line-height:1.5; margin-bottom:2rem;">Hemos agregado las credenciales personalizadas a tus requerimientos de cotización.</p>
            <div style="display:flex; flex-direction:column; gap:10px;">
                <a href="cotizacion.php" class="btn btn-primary" style="width:100%; text-align:center; padding:12px; font-weight:600;">Ver Carrito y Cotizar</a>
                <button onclick="document.getElementById('cart-modal').style.display='none'" class="btn btn-secondary" style="width:100%; padding:12px; font-weight:600; border:1px solid var(--border);">Seguir Diseñando</button>
            </div>
        </div>
    </div>

    <footer class="main-footer" style="margin-top: 5rem;">
        <div class="container footer-bottom-flex" style="padding: 2rem 0; border-top: 1px solid var(--border);">
            <p>&copy; 2026 CardNet.ec — Especialistas en identificación y marcado corporativo.</p>
        </div>
    </footer>

    <!-- Script de Renderizado -->
    <script>
        const unitPrice = 1.80;
        const qtyInput = document.getElementById('qty-input');
        const subtotalVal = document.getElementById('subtotal-val');

        function updateSubtotal() {
            const qty = parseInt(qtyInput.value) || 50;
            const subtotal = qty * unitPrice;
            subtotalVal.textContent = '$' + subtotal.toFixed(2);
        }

        document.getElementById('qty-plus').addEventListener('click', () => {
            let val = parseInt(qtyInput.value) || 50;
            qtyInput.value = val + 10;
            updateSubtotal();
        });

        document.getElementById('qty-minus').addEventListener('click', () => {
            let val = parseInt(qtyInput.value) || 50;
            if (val > 10) {
                qtyInput.value = val - 10;
                updateSubtotal();
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            const canvas = new fabric.Canvas('carnet-canvas', {
                width: 320,
                height: 480,
                backgroundColor: '#ffffff'
            });

            // Form inputs
            const templateSelect = document.getElementById("template-select");
            const logoInput = document.getElementById("logo-input");
            const photoInput = document.getElementById("photo-input");
            const nameInput = document.getElementById("name-input");
            const roleInput = document.getElementById("role-input");
            const idInput = document.getElementById("id-input");
            const qrToggle = document.getElementById("qr-toggle");

            // Objetos de Fabric.js
            let logoImage = null;
            let photoImage = null;
            let qrImage = null;
            let textObjects = [];

            // Dibujar Plantilla seleccionada
            function drawTemplate() {
                // Limpiar objetos excepto logo y foto de persona
                textObjects.forEach(obj => canvas.remove(obj));
                textObjects = [];

                if (qrImage) {
                    canvas.remove(qrImage);
                    qrImage = null;
                }

                const style = templateSelect.value;
                let primaryColor = "#63AE2C";
                let secondaryColor = "#2D3329";
                let headerText = "CREDENCIAL CORPORATIVA";

                if (style === "seguridad") {
                    primaryColor = "#EF4444";
                    secondaryColor = "#111827";
                    headerText = "CONTROL DE ACCESO";
                } else if (style === "evento") {
                    primaryColor = "#1E40AF";
                    secondaryColor = "#1E293B";
                    headerText = "PASE DE INVITADO";
                }

                // Fondo y cabecera
                const headerRect = new fabric.Rect({
                    left: 0,
                    top: 0,
                    fill: secondaryColor,
                    width: 320,
                    height: 80,
                    selectable: false,
                    evented: false
                });
                canvas.add(headerRect);
                textObjects.push(headerRect);

                const bannerRect = new fabric.Rect({
                    left: 0,
                    top: 80,
                    fill: primaryColor,
                    width: 320,
                    height: 6,
                    selectable: false,
                    evented: false
                });
                canvas.add(bannerRect);
                textObjects.push(bannerRect);

                // Pie de página
                const footerRect = new fabric.Rect({
                    left: 0,
                    top: 450,
                    fill: secondaryColor,
                    width: 320,
                    height: 30,
                    selectable: false,
                    evented: false
                });
                canvas.add(footerRect);
                textObjects.push(footerRect);

                // Títulos de cabecera
                const headerTextObj = new fabric.Text(headerText, {
                    left: 20,
                    top: 28,
                    fontFamily: 'Work Sans',
                    fontSize: 12,
                    fontWeight: 'bold',
                    fill: '#ffffff',
                    selectable: false,
                    evented: false
                });
                canvas.add(headerTextObj);
                textObjects.push(headerTextObj);

                const companyText = new fabric.Text("CardNet.ec", {
                    left: 20,
                    top: 48,
                    fontFamily: 'Marcellus',
                    fontSize: 14,
                    fill: '#ffffff',
                    selectable: false,
                    evented: false
                });
                canvas.add(companyText);
                textObjects.push(companyText);

                // Marco para la foto de perfil
                const photoFrame = new fabric.Rect({
                    left: 110,
                    top: 116,
                    width: 100,
                    height: 120,
                    fill: '#E5E7EB',
                    stroke: primaryColor,
                    strokeWidth: 2,
                    selectable: false,
                    evented: false
                });
                canvas.add(photoFrame);
                textObjects.push(photoFrame);

                renderUserData();
            }

            // Renderizar textos del usuario y QR
            function renderUserData() {
                // Eliminar textos anteriores
                textObjects = textObjects.filter(obj => {
                    if (obj.type === 'text' && obj !== logoImage && obj !== photoImage) {
                        canvas.remove(obj);
                        return false;
                    }
                    return true;
                });

                // Nombre
                const nameObj = new fabric.Text(nameInput.value, {
                    left: 160,
                    top: 255,
                    fontFamily: 'Work Sans',
                    fontSize: 18,
                    fontWeight: 'bold',
                    fill: '#1E221C',
                    originX: 'center',
                    selectable: false,
                    evented: false
                });
                canvas.add(nameObj);
                textObjects.push(nameObj);

                // Cargo
                const roleObj = new fabric.Text(roleInput.value.toUpperCase(), {
                    left: 160,
                    top: 280,
                    fontFamily: 'Work Sans',
                    fontSize: 11,
                    fontWeight: '600',
                    fill: '#5C6558',
                    originX: 'center',
                    selectable: false,
                    evented: false
                });
                canvas.add(roleObj);
                textObjects.push(roleObj);

                // Cédula / ID
                const idObj = new fabric.Text("ID: " + idInput.value, {
                    left: 160,
                    top: 300,
                    fontFamily: 'Work Sans',
                    fontSize: 10,
                    fill: '#5C6558',
                    originX: 'center',
                    selectable: false,
                    evented: false
                });
                canvas.add(idObj);
                textObjects.push(idObj);

                // Generar QR si está activado con la URL real de validación en línea
                if (qrToggle.checked) {
                    generateQR(window.location.origin + "/verificar.php?id=" + idInput.value);
                } else {
                    if (qrImage) {
                        canvas.remove(qrImage);
                        qrImage = null;
                    }
                }
            }

            // Generador de QR
            function generateQR(text) {
                const qrContainer = document.getElementById("qr-hidden");
                qrContainer.innerHTML = ""; // Limpiar
                
                new QRCode(qrContainer, {
                    text: text,
                    width: 72,
                    height: 72,
                    correctLevel: QRCode.CorrectLevel.H
                });

                setTimeout(() => {
                    const qrImg = qrContainer.querySelector("img");
                    if (qrImg) {
                        const imgObj = new Image();
                        imgObj.src = qrImg.src;
                        imgObj.onload = function() {
                            if (qrImage) {
                                canvas.remove(qrImage);
                            }
                            qrImage = new fabric.Image(imgObj, {
                                left: 124,
                                top: 345,
                                width: 72,
                                height: 72,
                                selectable: false,
                                evented: false
                            });
                            canvas.add(qrImage);
                            canvas.renderAll();
                        };
                    }
                }, 100);
            }

            // Manejo de Logo
            logoInput.addEventListener("change", function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(event) {
                    const imgObj = new Image();
                    imgObj.src = event.target.result;
                    imgObj.onload = function() {
                        if (logoImage) canvas.remove(logoImage);
                        
                        logoImage = new fabric.Image(imgObj);
                        const scale = Math.min(60 / logoImage.width, 36 / logoImage.height);
                        
                        logoImage.set({
                            left: 240,
                            top: 24,
                            scaleX: scale,
                            scaleY: scale,
                            selectable: false,
                            evented: false
                        });

                        canvas.add(logoImage);
                        canvas.renderAll();
                    };
                };
                reader.readAsDataURL(file);
            });

            // Manejo de Foto
            photoInput.addEventListener("change", function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(event) {
                    const imgObj = new Image();
                    imgObj.src = event.target.result;
                    imgObj.onload = function() {
                        if (photoImage) canvas.remove(photoImage);

                        photoImage = new fabric.Image(imgObj);
                        const scale = Math.min(100 / photoImage.width, 120 / photoImage.height);

                        photoImage.set({
                            left: 110,
                            top: 116,
                            scaleX: scale,
                            scaleY: scale,
                            selectable: false,
                            evented: false
                        });

                        canvas.add(photoImage);
                        canvas.renderAll();
                    };
                };
                reader.readAsDataURL(file);
            });

            // Inputs Events
            nameInput.addEventListener("input", renderUserData);
            roleInput.addEventListener("input", renderUserData);
            idInput.addEventListener("input", renderUserData);
            qrToggle.addEventListener("change", renderUserData);
            templateSelect.addEventListener("change", drawTemplate);

            // Reiniciar simulador
            document.getElementById("btn-reset").addEventListener("click", () => {
                logoImage = null;
                photoImage = null;
                qrImage = null;
                nameInput.value = "Alejandro Silva";
                roleInput.value = "Director Operativo";
                idInput.value = "1725489630";
                logoInput.value = "";
                photoInput.value = "";
                drawTemplate();
            });

            // Submit de Ficha de Credenciales por AJAX
            document.getElementById("btn-submit-pvc").addEventListener("click", function() {
                const snapshot = canvas.toDataURL({
                    format: 'png',
                    quality: 0.95
                });

                const qty = qtyInput.value;
                
                const formData = new FormData();
                formData.append('action', 'add');
                formData.append('name', `Credencial PVC (${templateSelect.value.toUpperCase()})`);
                formData.append('slug', 'credenciales-pvc');
                formData.append('qty', qty);
                formData.append('price', unitPrice);
                formData.append('snapshot', snapshot);

                fetch('cart-action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('cart-modal').style.display = 'flex';
                    } else {
                        alert('No se pudo guardar la credencial en el cotizador.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error de conexión al guardar en el cotizador.');
                });
            });

            drawTemplate();
            updateSubtotal();
        });
    </script>
</body>
</html>
