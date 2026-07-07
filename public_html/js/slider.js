document.addEventListener("DOMContentLoaded", function() {
    const track = document.querySelector(".carousel-track");
    const slides = Array.from(document.querySelectorAll(".carousel-slide"));
    const prevBtn = document.querySelector(".carousel-control.prev");
    const nextBtn = document.querySelector(".carousel-control.next");
    const dotsContainer = document.querySelector(".carousel-indicators");

    if (!track || slides.length === 0) return;

    let currentIndex = 0;
    let autoplayInterval;

    // Crear indicadores dinámicos
    if (dotsContainer) {
        dotsContainer.innerHTML = '';
        slides.forEach((_, index) => {
            const dot = document.createElement("button");
            dot.classList.add("carousel-dot");
            if (index === 0) dot.classList.add("active");
            dot.setAttribute("aria-label", `Slide ${index + 1}`);
            dotsContainer.appendChild(dot);
        });
    }

    const dots = Array.from(document.querySelectorAll(".carousel-dot"));

    function updateSlider() {
        track.style.transform = `translateX(-${currentIndex * 33.333}%)`;
        dots.forEach((dot, index) => {
            dot.classList.toggle("active", index === currentIndex);
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

    if (nextBtn) {
        nextBtn.addEventListener("click", () => {
            showNext();
            resetAutoplay();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener("click", () => {
            showPrev();
            resetAutoplay();
        });
    }

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
        if (Math.abs(diffX) > 60) {
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
        autoplayInterval = setInterval(showNext, 6000);
    }

    function resetAutoplay() {
        clearInterval(autoplayInterval);
        startAutoplay();
    }

    startAutoplay();
});
