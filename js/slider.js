(function() {
    function initSlider() {
        const track = document.querySelector(".hero-slider-track");
        const slides = Array.from(document.querySelectorAll(".hero-slide-item"));
        const dots = Array.from(document.querySelectorAll(".hero-dot"));

        if (!track || slides.length === 0) {
            console.warn("Slider: Componentes no encontrados.");
            return;
        }

        let currentIndex = 0;
        let autoplayInterval = null;

        function updateSlider() {
            slides.forEach((slide, index) => {
                if (index === currentIndex) {
                    slide.style.opacity = "1";
                    slide.style.visibility = "visible";
                    slide.style.zIndex = "5";
                    slide.classList.add("active");
                } else {
                    slide.style.opacity = "0";
                    slide.style.visibility = "hidden";
                    slide.style.zIndex = "1";
                    slide.classList.remove("active");
                }
            });

            dots.forEach((dot, index) => {
                if (index === currentIndex) {
                    dot.style.background = "var(--primary)";
                    dot.style.width = "32px";
                    dot.classList.add("active");
                } else {
                    dot.style.background = "rgba(255, 255, 255, 0.3)";
                    dot.style.width = "32px";
                    dot.classList.remove("active");
                }
            });
        }

        function showNext() {
            currentIndex = (currentIndex + 1) % slides.length;
            updateSlider();
        }

        function showPrev() {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            updateSlider();
        }

        // Listener para los puntos de navegación
        dots.forEach((dot, index) => {
            dot.addEventListener("click", () => {
                currentIndex = index;
                updateSlider();
                resetAutoplay();
            });
        });

        // Touch Swipe para móvil
        let startX = 0;
        let isSwiping = false;

        track.addEventListener("touchstart", (e) => {
            startX = e.touches[0].clientX;
            isSwiping = true;
        }, { passive: true });

        track.addEventListener("touchmove", (e) => {
            if (!isSwiping) return;
            const diffX = e.touches[0].clientX - startX;
            if (Math.abs(diffX) > 50) {
                if (diffX > 0) {
                    showPrev();
                } else {
                    showNext();
                }
                isSwiping = false;
                resetAutoplay();
            }
        }, { passive: true });

        track.addEventListener("touchend", () => {
            isSwiping = false;
        });

        function startAutoplay() {
            stopAutoplay();
            autoplayInterval = setInterval(showNext, 5000);
        }

        function stopAutoplay() {
            if (autoplayInterval) {
                clearInterval(autoplayInterval);
                autoplayInterval = null;
            }
        }

        function resetAutoplay() {
            startAutoplay();
        }

        // Iniciar
        updateSlider();
        startAutoplay();
        console.log("Slider: Inicializado correctamente en el index (5s autoplay).");
    }

    // Ejecutar directamente ya que el script está al final del body
    if (document.readyState === "complete" || document.readyState === "interactive") {
        initSlider();
    } else {
        document.addEventListener("DOMContentLoaded", initSlider);
    }
})();
