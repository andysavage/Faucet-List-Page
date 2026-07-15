(function () {
    var HIDE_DURATION     = 15 * 60 * 1000; // 15 minutes
    var STORAGE_KEY       = 'faucetlist_banner_closed_ts';
    var NEW_VISITOR_DELAY = 30 * 1000; // 30 seconds
    var CONTAINER_ID      = 'banner-wrap';

    var container = document.getElementById(CONTAINER_ID);
    if (!container) return;

    /* Hide if recently closed */
    var closedAt = localStorage.getItem(STORAGE_KEY);
    if (closedAt && (Date.now() - parseInt(closedAt)) <= HIDE_DURATION) {
        container.style.display = 'none';
        return;
    }

    function addCloseButton() {
        var btn = document.createElement('button');
        btn.className   = 'banner-close';
        btn.title       = 'Close';
        btn.textContent = '\u00d7';
        btn.addEventListener('click', function () {
            container.style.display = 'none';
            localStorage.setItem(STORAGE_KEY, Date.now().toString());
        });
        container.appendChild(btn);
    }

    /* Delay banner for visitors with no faucets and not logged in */
    var isLoggedIn = !!localStorage.getItem('directsponsor_session');
    var faucetsRaw = localStorage.getItem('faucets');
    var hasFaucets = faucetsRaw && JSON.parse(faucetsRaw).length > 0;
    if (!isLoggedIn && !hasFaucets) {
        container.style.display = 'none';
        setTimeout(function () {
            container.style.display = '';
            addCloseButton();
        }, NEW_VISITOR_DELAY);
        return;
    }

    addCloseButton();
}());
