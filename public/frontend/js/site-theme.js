document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('market-search');
    const cards = Array.from(document.querySelectorAll('#market-grid [data-market-name]'));
    const empty = document.getElementById('no-markets');
    if (search && cards.length) {
        const filter = function () {
            const query = search.value.trim().toLocaleLowerCase();
            let visible = 0;
            cards.forEach(function (card) {
                const match = (card.dataset.marketName || '').includes(query);
                card.hidden = !match;
                if (match) visible++;
            });
            if (empty) empty.hidden = visible !== 0;
        };
        search.addEventListener('input', filter);
        document.addEventListener('keydown', function (event) {
            if (event.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                event.preventDefault();
                search.focus();
            }
            if (event.key === 'Escape' && document.activeElement === search) {
                search.value = '';
                filter();
                search.blur();
            }
        });
    }
});