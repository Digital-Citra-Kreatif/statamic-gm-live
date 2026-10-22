import './bootstrap';
import './flyout-menu';
import './sticky-header';
import './hero-slider';
import './slider-thumbnail.js';
import './equal-height-cards';
import './form-career-popup';
import './site-popup';
import './share.js';
import './counter.js';
import './category-slider.js';
import './features-slider.js';
import './scroll-to-top.js';
import './floating-whatsapp.js';
import './comparison.js';
import './select-dropdown.js';
import './no-translate.js';

// Lightbox: hanya dimuat jika ada elemen data-fslightbox.
if (document.querySelector('[data-fslightbox]')) {
    import('fslightbox');
}

// Peta dealer (Leaflet): dimuat saat peta mendekati viewport.
const dealerMapEl = document.getElementById('dealer-map');

if (dealerMapEl) {
    const loadDealerMap = () => import('./dealer-map');

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(
            (entries) => {
                if (entries.some((entry) => entry.isIntersecting)) {
                    observer.disconnect();
                    loadDealerMap();
                }
            },
            { rootMargin: '600px 0px' },
        );
        observer.observe(dealerMapEl);
    } else {
        loadDealerMap();
    }
}
