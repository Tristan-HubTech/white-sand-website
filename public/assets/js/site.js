const toggle = document.querySelector('[data-menu-toggle]');
const menu = document.querySelector('[data-menu]');

if (toggle && menu) {
    toggle.addEventListener('click', () => {
        menu.classList.toggle('open');
    });
}

const revealItems = document.querySelectorAll('.reveal');
if ('IntersectionObserver' in window && revealItems.length > 0) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    revealItems.forEach((item) => observer.observe(item));
}

const hero = document.querySelector('[data-hero-slider]');
if (hero) {
    let slides = [];
    try {
        const raw = hero.dataset.slides || hero.getAttribute('data-slides') || '[]';
        slides = JSON.parse(raw);
    } catch (error) {
        try {
            const rawFallback = hero.getAttribute('data-slides') || '[]';
            const decoded = rawFallback
                .replaceAll('&quot;', '"')
                .replaceAll('&#34;', '"')
                .replaceAll('&amp;', '&');
            slides = JSON.parse(decoded);
        } catch (innerError) {
            slides = [];
        }
    }

    if (Array.isArray(slides) && slides.length > 1) {
        const dotContainer = hero.querySelector('.kingdom-hero-dots');
        let dots = dotContainer ? Array.from(dotContainer.querySelectorAll('span')) : [];
        let activeIndex = 0;
        let pointerStartX = 0;
        let pointerEndX = 0;
        let fadeTimerId = null;        let autoSlideId = null;
        const normalizeIndex = (index) => ((index % slides.length) + slides.length) % slides.length;

        if (dotContainer && dots.length !== slides.length) {
            dotContainer.innerHTML = '';
            slides.forEach((_, idx) => {
                const dot = document.createElement('span');
                if (idx === 0) {
                    dot.classList.add('active');
                }
                dotContainer.appendChild(dot);
            });
            dots = Array.from(dotContainer.querySelectorAll('span'));
        }

        const setActiveSlide = (index, useFade = true) => {
            const nextIndex = normalizeIndex(index);
            const nextImage = `url('${slides[nextIndex]}')`;

            if (fadeTimerId) {
                clearTimeout(fadeTimerId);
                fadeTimerId = null;
            }

            if (useFade && nextIndex !== activeIndex) {
                hero.style.setProperty('--hero-image-next', nextImage);
                hero.classList.add('is-fading');

                fadeTimerId = window.setTimeout(() => {
                    hero.style.setProperty('--hero-image', nextImage);
                    hero.classList.remove('is-fading');
                    hero.style.removeProperty('--hero-image-next');
                    fadeTimerId = null;
                }, 320);
            } else {
                hero.style.setProperty('--hero-image', nextImage);
                hero.classList.remove('is-fading');
                hero.style.removeProperty('--hero-image-next');
            }

            activeIndex = nextIndex;

            dots.forEach((dot, dotIndex) => {
                dot.classList.toggle('active', dotIndex === nextIndex);
            });
        };

        const goToSlide = (index) => {
            setActiveSlide(index);
            startAutoSlide();
        };

        const startAutoSlide = () => {
            if (autoSlideId) {
                clearInterval(autoSlideId);
            }

            autoSlideId = setInterval(() => {
                setActiveSlide(activeIndex + 1, true);
            }, 4500);
        };

        dots.forEach((dot, dotIndex) => {
            dot.addEventListener('click', () => {
                goToSlide(dotIndex);
            });

            dot.addEventListener('touchstart', () => {
                goToSlide(dotIndex);
            }, { passive: true });
        });

        const handleSwipe = () => {
            const swipeDistance = pointerEndX - pointerStartX;
            if (Math.abs(swipeDistance) < 40) {
                return;
            }

            if (swipeDistance < 0) {
                goToSlide(activeIndex + 1);
                return;
            }

            goToSlide(activeIndex - 1);
        };

        hero.addEventListener('pointerdown', (event) => {
            pointerStartX = event.clientX;
            pointerEndX = event.clientX;
        });

        hero.addEventListener('pointerup', (event) => {
            pointerEndX = event.clientX;
            handleSwipe();
        });

        setActiveSlide(0, false);
        startAutoSlide();

        hero.addEventListener('mouseenter', () => {
            if (autoSlideId) {
                clearInterval(autoSlideId);
                autoSlideId = null;
            }
        });

        hero.addEventListener('mouseleave', () => {
            startAutoSlide();
        });
    }
}

// Keep page scale fixed by blocking browser zoom shortcuts.
const blockZoom = (event) => {
    if (event.ctrlKey || event.metaKey) {
        event.preventDefault();
        event.stopPropagation();
    }
};

const blockZoomKeys = (event) => {
    const isZoomKey = ['+', '-', '=', '0', 'Add', 'Subtract', 'NumpadAdd', 'NumpadSubtract'].includes(event.key);
    if ((event.ctrlKey || event.metaKey) && isZoomKey) {
        event.preventDefault();
        event.stopPropagation();
    }
};

document.addEventListener('wheel', blockZoom, { passive: false, capture: true });
window.addEventListener('wheel', blockZoom, { passive: false, capture: true });
document.addEventListener('keydown', blockZoomKeys, { capture: true });
window.addEventListener('keydown', blockZoomKeys, { capture: true });
