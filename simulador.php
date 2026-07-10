<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador de Grabado | CardNet.ec</title>
    <meta name="description" content="Simula tu logo o texto sobre nuestros termos, agendas y artículos corporativos antes del grabado real.">
    
    <!-- CSS Modulares -->
    <link rel="stylesheet" href="css/base.css?v=4.1">
    <link rel="stylesheet" href="css/layout.css?v=4.1">
    <link rel="stylesheet" href="css/components.css?v=4.1">
    <link rel="stylesheet" href="css/pages.css?v=4.1">

    <!-- Google Fonts -->

    <!-- Fabric.js CDN para Canvas Interactivo -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

    <style>
        .simulator-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }
        @media (min-width: 1024px) {
            .simulator-layout {
                grid-template-columns: 1.1fr 0.9fr;
            }
        }
        .canvas-container-box {
            position: relative;
            background-color: var(--surface-light);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            min-height: 480px;
        }
        .canvas-wrapper {
            position: relative;
            box-shadow: var(--shadow-md);
            border-radius: var(--radius-md);
            overflow: hidden;
            background-color: white;
        }
        .controls-panel {
            background-color: var(--light);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .tab-buttons {
            display: flex;
            gap: 10px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
        }
        .tab-btn {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: var(--transition-fast);
            background-color: var(--surface-light);
            color: var(--text-muted);
        }
        .tab-btn.active {
            background-color: var(--primary);
            color: white;
        }
        .options-group {
            display: none;
            flex-direction: column;
            gap: 1rem;
        }
        .options-group.active {
            display: flex;
        }
        .color-dot-picker {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }
        .color-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            transition: var(--transition-fast);
        }
        .color-dot.active {
            border-color: var(--primary);
            transform: scale(1.1);
        }
    </style>

    <!-- Google Fonts: Marcellus (Títulos Elegantes) & Work Sans (Textos Limpios) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Barra de Anuncios Superior -->
    <div class="top-announcement-bar">
        Taller de personalización en Quito | Envíos corporativos asegurados a todo el Ecuador
    </div>

    <?php include 'includes/header.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="container section-padding">
        <div class="section-header">
            <span class="section-subtitle">Simulador Táctil</span>
            <h1 style="font-family: var(--font-heading); font-weight: 400; margin-bottom: 0.5rem;">Prueba tu logo sobre el producto</h1>
            <p>Sube tu logotipo o escribe un texto para visualizar de forma preliminar el acabado del grabado láser en el taller.</p>
        </div>

        <div class="simulator-layout">
            <!-- Área del Canvas (Visualización) -->
            <div class="canvas-container-box">
                <div class="canvas-wrapper">
                    <!-- Canvas de Fabric.js -->
                    <canvas id="simulator-canvas" width="400" height="400"></canvas>
                </div>
            </div>

            <!-- Panel de Controles -->
            <div class="controls-panel">
                <!-- 1. Selección de Producto -->
                <div class="form-group">
                    <label class="form-label" for="product-select">1. Elige el producto</label>
                    <select class="form-select" id="product-select">
                        <option value="termo" data-bg="termo_acero">Termo de Acero Inoxidable (Negro Mate)</option>
                        <option value="agenda" data-bg="agenda_cuero">Agenda Corporativa (Cuero PU Marrón)</option>
                        <option value="caja" data-bg="caja_madera">Caja de Madera Pino (Natural)</option>
                        <option value="llavero" data-bg="llavero_metal">Llavero Corporativo (Metal/Cuero)</option>
                    </select>
                </div>

                <!-- Variantes de Color del Producto -->
                <div class="form-group" id="product-color-group">
                    <label class="form-label">Color de Producto</label>
                    <div class="color-dot-picker" id="product-color-picker">
                        <button class="color-dot active" style="background-color: #1E221C;" data-color="negro" title="Negro Mate"></button>
                        <button class="color-dot" style="background-color: #FCFDFB; border: 1px solid #DCE2D8;" data-color="blanco" title="Blanco Satinado"></button>
                        <button class="color-dot" style="background-color: #63AE2C;" data-color="verde" title="Verde Corporativo"></button>
                    </div>
                </div>

                <!-- 2. Tipo de Contenido: Logo o Texto -->
                <div class="tab-buttons">
                    <button class="tab-btn active" id="btn-tab-logo">Subir Logo</button>
                    <button class="tab-btn" id="btn-tab-text">Añadir Texto</button>
                </div>

                <!-- Formulario de Logo -->
                <div class="options-group active" id="group-logo">
                    <div class="form-group">
                        <label class="form-label" for="logo-input">Sube tu logo (PNG transparente recomendado)</label>
                        <input class="form-input" type="file" id="logo-input" accept="image/png, image/jpeg">
                    </div>
                </div>

                <!-- Formulario de Texto -->
                <div class="options-group" id="group-text">
                    <div class="form-group">
                        <label class="form-label" for="text-input">Escribe tu texto</label>
                        <input class="form-input" type="text" id="text-input" placeholder="Tu marca aquí">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="text-color">Color de Grabado</label>
                        <select class="form-select" id="text-color">
                            <option value="#BAAF1A">Dorado Apagado</option>
                            <option value="#E1E8DC">Plata / Acero Pulido</option>
                            <option value="#1E221C">Negro Quemado</option>
                        </select>
                    </div>
                </div>

                <!-- 3. Cotización rápida -->
                <div class="form-group">
                    <label class="form-label" for="qty-input">Cantidad aproximada</label>
                    <input class="form-input" type="number" id="qty-input" value="50" min="1">
                </div>

                <div class="hero-actions" style="margin-top: 1rem;">
                    <button class="btn btn-primary" id="btn-whatsapp-send" style="width: 100%;">Cotizar por WhatsApp</button>
                    <button class="btn btn-secondary" id="btn-reset-canvas" style="width: 100%;">Reiniciar Simulación</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Pie de Página -->
    <?php include 'includes/footer.php'; ?>

    <!-- Script del Simulador Canvas -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Inicializar Fabric Canvas
            const canvas = new fabric.Canvas('simulator-canvas');
            
            // Coordenadas límites del área de grabado por producto
            const areasEditable = {
                termo: { x: 130, y: 120, width: 140, height: 160 },
                agenda: { x: 110, y: 110, width: 180, height: 180 },
                caja: { x: 100, y: 100, width: 200, height: 200 },
                llavero: { x: 150, y: 150, width: 100, height: 100 }
            };

            let currentProduct = "termo";
            let uploadedLogo = null;
            let textObject = null;
            let boundsRect = null;

            // Dibujar el fondo del producto y delimitar el área de marcaje
            function loadProductBackground() {
                canvas.clear();
                
                // Cargar color/textura del producto
                let colorText = "negro";
                const activeColorBtn = document.querySelector(".color-dot.active");
                if (activeColorBtn) {
                    colorText = activeColorBtn.getAttribute("data-color");
                }

                // Generar un background sintético realista
                let bgGradient;
                if (currentProduct === "termo") {
                    // Gradiente metálico según variante
                    let col1 = "#1A1D18", col2 = "#2A3026";
                    if (colorText === "blanco") { col1 = "#F5F7F3"; col2 = "#E5E9E2"; }
                    if (colorText === "verde") { col1 = "#569826"; col2 = "#6CB832"; }

                    bgGradient = new fabric.Gradient({
                        type: 'linear',
                        coords: { x1: 0, y1: 0, x2: canvas.width, y2: 0 },
                        colorStops: [
                            { offset: 0, color: col1 },
                            { offset: 0.5, color: col2 },
                            { offset: 1, color: col1 }
                        ]
                    });
                } else if (currentProduct === "agenda") {
                    bgGradient = "#8E5E38"; // Marrón cuero
                } else if (currentProduct === "caja") {
                    bgGradient = "#E2C39B"; // Madera Pino
                } else {
                    bgGradient = "#D4AF37"; // Llavero latón
                }

                // Rectángulo que simula la silueta del producto
                const productBase = new fabric.Rect({
                    left: 50,
                    top: 20,
                    width: 300,
                    height: 360,
                    rx: currentProduct === "termo" ? 40 : 10,
                    ry: currentProduct === "termo" ? 40 : 10,
                    fill: bgGradient,
                    selectable: false,
                    evented: false
                });
                
                canvas.add(productBase);

                // Dibujar línea fina de límites de grabado para guía visual del usuario
                const limits = areasEditable[currentProduct];
                boundsRect = new fabric.Rect({
                    left: limits.x,
                    top: limits.y,
                    width: limits.width,
                    height: limits.height,
                    fill: 'transparent',
                    stroke: 'rgba(99, 174, 44, 0.4)',
                    strokeDashArray: [5, 5],
                    strokeWidth: 1.5,
                    selectable: false,
                    evented: false
                });
                canvas.add(boundsRect);
                canvas.sendToBack(productBase);
                canvas.renderAll();
            }

            // Cambiar Producto
            const productSelect = document.getElementById("product-select");
            productSelect.addEventListener("change", function() {
                currentProduct = this.value;
                
                // Mostrar variante de color solo para termos
                const colorGroup = document.getElementById("product-color-group");
                if (currentProduct === "termo") {
                    colorGroup.style.display = "block";
                } else {
                    colorGroup.style.display = "none";
                }

                loadProductBackground();
                resetCanvasObjects();
            });

            // Cambiar variante de color
            const colorDots = document.querySelectorAll(".color-dot");
            colorDots.forEach(dot => {
                dot.addEventListener("click", function() {
                    colorDots.forEach(d => d.classList.remove("active"));
                    this.classList.add("active");
                    loadProductBackground();
                });
            });

            // Cambiar pestañas (Logo o Texto)
            const btnTabLogo = document.getElementById("btn-tab-logo");
            const btnTabText = document.getElementById("btn-tab-text");
            const groupLogo = document.getElementById("group-logo");
            const groupText = document.getElementById("group-text");

            btnTabLogo.addEventListener("click", () => {
                btnTabLogo.classList.add("active");
                btnTabText.classList.remove("active");
                groupLogo.classList.add("active");
                groupText.classList.remove("active");
            });

            btnTabText.addEventListener("click", () => {
                btnTabText.classList.add("active");
                btnTabLogo.classList.remove("active");
                groupText.classList.add("active");
                groupLogo.classList.remove("active");
            });

            // Subir Logo
            const logoInput = document.getElementById("logo-input");
            logoInput.addEventListener("change", function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(event) {
                    const imgObj = new Image();
                    imgObj.src = event.target.result;
                    imgObj.onload = function() {
                        if (uploadedLogo) {
                            canvas.remove(uploadedLogo);
                        }

                        uploadedLogo = new fabric.Image(imgObj);
                        
                        // Escalar logo al tamaño límite
                        const limits = areasEditable[currentProduct];
                        const scale = Math.min(
                            (limits.width * 0.7) / uploadedLogo.width,
                            (limits.height * 0.7) / uploadedLogo.height
                        );

                        uploadedLogo.set({
                            left: limits.x + (limits.width - uploadedLogo.width * scale) / 2,
                            top: limits.y + (limits.height - uploadedLogo.height * scale) / 2,
                            scaleX: scale,
                            scaleY: scale,
                            cornerColor: '#63AE2C',
                            cornerSize: 8,
                            transparentCorners: false
                        });

                        canvas.add(uploadedLogo);
                        canvas.setActiveObject(uploadedLogo);
                        canvas.renderAll();
                    };
                };
                reader.readAsDataURL(file);
            });

            // Escribir Texto
            const textInput = document.getElementById("text-input");
            const textColorSelect = document.getElementById("text-color");

            function updateText() {
                const textVal = textInput.value;
                const limits = areasEditable[currentProduct];

                if (!textVal) {
                    if (textObject) {
                        canvas.remove(textObject);
                        textObject = null;
                    }
                    canvas.renderAll();
                    return;
                }

                if (!textObject) {
                    textObject = new fabric.Text(textVal, {
                        left: limits.x + limits.width / 4,
                        top: limits.y + limits.height / 3,
                        fontFamily: 'Marcellus',
                        fontSize: 24,
                        fill: textColorSelect.value,
                        cornerColor: '#63AE2C',
                        cornerSize: 8,
                        transparentCorners: false
                    });
                    canvas.add(textObject);
                } else {
                    textObject.set({
                        text: textVal,
                        fill: textColorSelect.value
                    });
                }
                canvas.setActiveObject(textObject);
                canvas.renderAll();
            }

            textInput.addEventListener("input", updateText);
            textColorSelect.addEventListener("change", updateText);

            // Evitar que el logo o texto se salgan del área permitida
            canvas.on('object:moving', function(e) {
                const obj = e.target;
                const limits = areasEditable[currentProduct];

                const halfWidth = (obj.width * obj.scaleX) / 2;
                const halfHeight = (obj.height * obj.scaleY) / 2;

                const bounds = {
                    left: limits.x,
                    top: limits.y,
                    right: limits.x + limits.width,
                    bottom: limits.y + limits.height
                };

                // Bounding box constrain
                if (obj.left < bounds.left) obj.left = bounds.left;
                if (obj.top < bounds.top) obj.top = bounds.top;
                if (obj.left + obj.width * obj.scaleX > bounds.right) obj.left = bounds.right - obj.width * obj.scaleX;
                if (obj.top + obj.height * obj.scaleY > bounds.bottom) obj.top = bounds.bottom - obj.height * obj.scaleY;
            });

            // Reiniciar lienzo
            function resetCanvasObjects() {
                uploadedLogo = null;
                textObject = null;
                textInput.value = "";
                logoInput.value = "";
                canvas.renderAll();
            }

            document.getElementById("btn-reset-canvas").addEventListener("click", function() {
                resetCanvasObjects();
                loadProductBackground();
            });

            // Cotizar y Enviar a WhatsApp
            document.getElementById("btn-whatsapp-send").addEventListener("click", function() {
                // Ocultar bordes de guía para la foto final
                if (boundsRect) {
                    boundsRect.set({ stroke: 'transparent' });
                    canvas.renderAll();
                }

                // Generar snapshot del canvas en base64
                const dataURL = canvas.toDataURL({
                    format: 'png',
                    quality: 1.0
                });

                // Restaurar bordes de guía
                if (boundsRect) {
                    boundsRect.set({ stroke: 'rgba(99, 174, 44, 0.4)' });
                    canvas.renderAll();
                }

                const qty = document.getElementById("qty-input").value || "50";
                const productName = productSelect.options[productSelect.selectedIndex].text;
                
                // Mensaje prellenado para WhatsApp
                let message = `Hola CardNet.ec, acabo de realizar una simulación de mi marca en tu simulador web:\n\n`;
                message += `*Producto:* ${productName}\n`;
                message += `*Cantidad Estimada:* ${qty} unidades\n`;
                if (textInput.value) {
                    message += `*Texto simulado:* "${textInput.value}"\n`;
                }
                message += `\nQuiero cotizar este acabado.`;

                const phone = "593000000000"; // Cambiar por teléfono real de CardNet.ec
                const whatsappUrl = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;

                // Abrir chat
                window.open(whatsappUrl, '_blank');
            });

            // Inicialización de arranque
            loadProductBackground();
        });
    </script>
</body>
</html>
 