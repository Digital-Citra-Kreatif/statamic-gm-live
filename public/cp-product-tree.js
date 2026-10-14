(() => {
    const separator = ' | ';
    let pending = false;

    function decorateCategories() {
        pending = false;

        document.querySelectorAll('.page-tree-branch a[href*="/collections/products/entries/"]').forEach((link) => {
            const label = link.textContent || '';
            const separatorIndex = label.lastIndexOf(separator);

            if (separatorIndex === -1) {
                return;
            }

            const category = label.slice(separatorIndex + separator.length).trim();

            if (!category) {
                return;
            }

            link.textContent = label.slice(0, separatorIndex);
            if (link.nextElementSibling?.classList.contains('product-tree-category')) {
                link.nextElementSibling.remove();
            }

            const badge = document.createElement('span');
            badge.className = 'product-tree-category';
            badge.textContent = category;
            link.after(badge);
        });
    }

    function scheduleDecoration() {
        if (pending) {
            return;
        }

        pending = true;
        requestAnimationFrame(decorateCategories);
    }

    new MutationObserver(scheduleDecoration).observe(document.documentElement, {
        childList: true,
        subtree: true,
    });

    scheduleDecoration();
})();
