(() => {
    const separator = ' | ';
    let pending = false;

    function decorateBadges() {
        pending = false;

        document.querySelectorAll('.page-tree-branch a[href*="/collections/products/entries/"], .page-tree-branch a[href*="/collections/dealers/entries/"]').forEach((link) => {
            const label = link.textContent || '';
            const separatorIndex = label.lastIndexOf(separator);

            if (separatorIndex === -1) {
                return;
            }

            const badgeText = label.slice(separatorIndex + separator.length).trim();

            if (!badgeText) {
                return;
            }

            link.textContent = label.slice(0, separatorIndex);
            if (link.nextElementSibling?.classList.contains('collection-tree-badge')) {
                link.nextElementSibling.remove();
            }

            const badge = document.createElement('span');
            badge.className = 'collection-tree-badge';
            badge.textContent = badgeText;
            link.after(badge);
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
