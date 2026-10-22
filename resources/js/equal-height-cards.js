// Equal height card

// Samakan tinggi card
function equalizeHeights(container) {
    const cards = Array.from(container.children);
    cards.forEach((c) => (c.style.height = ''));
    const maxHeight = Math.max(...cards.map((c) => c.offsetHeight));
    cards.forEach((c) => (c.style.height = maxHeight + 'px'));
}

// Tablet dan Desktop
function initEqualHeightCards() {
    const containers = document.querySelectorAll('[data-equal-height]');
    if (window.innerWidth <= 640) {
        containers.forEach((container) => {
            Array.from(container.children).forEach((c) => (c.style.height = ''));
        });
        return;
    }
    containers.forEach(equalizeHeights);
}

document.addEventListener('DOMContentLoaded', initEqualHeightCards);
window.addEventListener('resize', initEqualHeightCards);
