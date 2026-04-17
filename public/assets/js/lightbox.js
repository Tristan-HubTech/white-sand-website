const lightboxModal = document.getElementById('lightboxModal');
const lightboxImage = lightboxModal.querySelector('.lightbox-image');
const lightboxCaption = lightboxModal.querySelector('.lightbox-caption');
const lightboxClose = lightboxModal.querySelector('.lightbox-close');
const lightboxPrev = lightboxModal.querySelector('.lightbox-prev');
const lightboxNext = lightboxModal.querySelector('.lightbox-next');
const lightboxOverlay = lightboxModal.querySelector('.lightbox-overlay');

let currentImages = [];
let currentIndex = 0;

const initLightbox = () => {
    const triggers = document.querySelectorAll('[data-lightbox]');
    
    triggers.forEach((trigger, index) => {
        const imageUrl = trigger.getAttribute('data-lightbox');
        const title = trigger.getAttribute('data-title') || 'Gallery Image';
        
        currentImages.push({ url: imageUrl, title });
        
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            currentIndex = index;
            openLightbox();
        });
    });
};

const openLightbox = () => {
    if (currentImages.length === 0) return;
    
    const image = currentImages[currentIndex];
    lightboxImage.src = image.url;
    lightboxCaption.textContent = image.title;
    
    lightboxModal.classList.add('active');
    document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
    lightboxModal.classList.remove('active');
    document.body.style.overflow = '';
    lightboxImage.src = '';
};

const showNext = () => {
    currentIndex = (currentIndex + 1) % currentImages.length;
    openLightbox();
};

const showPrev = () => {
    currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
    openLightbox();
};

lightboxClose.addEventListener('click', closeLightbox);
lightboxOverlay.addEventListener('click', closeLightbox);
lightboxNext.addEventListener('click', showNext);
lightboxPrev.addEventListener('click', showPrev);

document.addEventListener('keydown', (e) => {
    if (!lightboxModal.classList.contains('active')) return;
    
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') showNext();
    if (e.key === 'ArrowLeft') showPrev();
});

initLightbox();
