(function () {
    var HIDE_DURATION = 15 * 60 * 1000; // 15 minutes
    var STORAGE_KEY   = 'faucetlist_banner_closed_ts';
    var CONTAINER_ID  = 'banner-wrap';

    var container = document.getElementById(CONTAINER_ID);
    if (!container) return;

    /* Hide if recently closed */
    var closedAt = localStorage.getItem(STORAGE_KEY);
    if (closedAt && (Date.now() - parseInt(closedAt)) <= HIDE_DURATION) {
        container.style.display = 'none';
        return;
    }

    /* Add close button */
    var btn = document.createElement('button');
    btn.className   = 'banner-close';
    btn.title       = 'Close';
    btn.textContent = '\u00d7';
    btn.addEventListener('click', function () {
        container.style.display = 'none';
        localStorage.setItem(STORAGE_KEY, Date.now().toString());
    });
    container.appendChild(btn);
}());
