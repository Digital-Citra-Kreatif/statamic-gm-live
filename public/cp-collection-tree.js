(() => {
    const separator = ' | ';
    let pending = false;

    function decorateBadges() {
        pending = false;

        document.querySelectorAll('.page-tree-branch a[href*="/collections/products/entries/"], .page-tree-branch a[href*="/collections/dealers/entries/"]').forEach((link) => {
            const label = link.textContent || '';

            if (!label.includes(separator)) {
                return;
            }

            // Bagian pertama tetap link, sisanya badge masing-masing
            const [title, ...badgeTexts] = label.split(separator).map((part) => part.trim());

            link.textContent = title;
            while (link.nextElementSibling?.classList.contains('collection-tree-badge')) {
                link.nextElementSibling.remove();
            }

            let previous = link;
            badgeTexts.filter(Boolean).forEach((badgeText) => {
                const badge = document.createElement('span');
                badge.className = 'collection-tree-badge';
                badge.textContent = badgeText;
                previous.after(badge);
                previous = badge;
            });
        });
    }

    function scheduleDecoration() {
        if (pending) {
            return;
        }

        pending = true;
        requestAnimationFrame(decorateBadges);
    }

    new MutationObserver(scheduleDecoration).observe(document.documentElement, {
        childList: true,
        subtree: true,
    });

    scheduleDecoration();
})();
