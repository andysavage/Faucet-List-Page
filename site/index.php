<?php
// Read and rotate ads from a file
function getRandomAd($filename) {
    $adsFile = __DIR__ . '/../data/' . $filename;
    if (!file_exists($adsFile)) {
        return '';
    }
    
    $content = file_get_contents($adsFile);
    $ads = array_filter(array_map('trim', explode('---', $content)));
    
    if (empty($ads)) {
        return '';
    }
    
    // Pick a random ad
    return $ads[array_rand($ads)];
}

$bannerAd = getRandomAd('ads.txt');
$floatingAd = getRandomAd('ads-floating.txt');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faucet List</title>
    <link rel="icon" type="image/png" href="/favicons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicons/favicon.svg" />
    <link rel="shortcut icon" href="/favicons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="FaucetList" />
    <link rel="manifest" href="/favicons/site.webmanifest" />
    <style>
        .button,
        .button-primary,
        .button-secondary {
            display: inline-block;
            text-decoration: none;
            font-size: 13px;
            line-height: 26px;
            height: 28px;
            margin: 0;
            padding: 0 10px 1px;
            cursor: pointer;
            border-width: 1px;
            border-style: solid;
            -webkit-appearance: none;
            -webkit-border-radius: 3px;
            border-radius: 3px;
            white-space: nowrap;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .button,
        .button-secondary {
            color: #555;
            border-color: #cccccc;
            background: #f7f7f7;
            -webkit-box-shadow: inset 0 1px 0 #fff, 0 1px 0 rgba(0, 0, 0, .08);
            box-shadow: inset 0 1px 0 #fff, 0 1px 0 rgba(0, 0, 0, .08);
            vertical-align: top;
        }

        p .button {
            vertical-align: baseline;
        }

        .button:hover,
        .button-secondary:hover,
        .button:focus,
        .button-secondary:focus {
            background: #fafafa;
            border-color: #999;
            color: #222;
        }

        .button:focus,
        .button-secondary:focus {
            -webkit-box-shadow: 1px 1px 1px rgba(0, 0, 0, .2);
            box-shadow: 1px 1px 1px rgba(0, 0, 0, .2);
        }

        .button:active,
        .button-secondary:active {
            background: #eee;
            border-color: #999;
            color: #333;
            -webkit-box-shadow: inset 0 2px 5px -3px rgba(0, 0, 0, 0.5);
            box-shadow: inset 0 2px 5px -3px rgba(0, 0, 0, 0.5);
        }

        .button-primary {
            background: #2ea2cc;
            border-color: #0074a2;
            -webkit-box-shadow: inset 0 1px 0 rgba(120, 200, 230, 0.5), 0 1px 0 rgba(0, 0, 0, .15);
            box-shadow: inset 0 1px 0 rgba(120, 200, 230, 0.5), 0 1px 0 rgba(0, 0, 0, .15);
            color: #fff;
            text-decoration: none;
        }

        .button-primary:hover,
        .button-primary:focus {
            background: #1e8cbe;
            border-color: #0074a2;
            -webkit-box-shadow: inset 0 1px 0 rgba(120, 200, 230, 0.6);
            box-shadow: inset 0 1px 0 rgba(120, 200, 230, 0.6);
            color: #fff;
        }

        .button-primary:focus {
            border-color: #0e3950;
            -webkit-box-shadow: inset 0 1px 0 rgba(120, 200, 230, 0.6), 1px 1px 2px rgba(0, 0, 0, 0.4);
            box-shadow: inset 0 1px 0 rgba(120, 200, 230, 0.6), 1px 1px 2px rgba(0, 0, 0, 0.4);
        }

        .button-primary:active {
            background: #1b7aa6;
            border-color: #005684;
            color: rgba(255, 255, 255, 0.95);
            -webkit-box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1);
            box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1);
            vertical-align: top;
        }

        .progressBar {
            width: 100%;
            margin: 10px 0;
            height: 22px;
            background-color: #28a745;
            color: white;
            line-height: 22px;
            font-weight: bold;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .progressBar .progress-bar-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background-color: #17a2b8;
            z-index: 1;
        }

        .progressBar .progress-bar-text {
            position: relative;
            z-index: 2;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
            box-sizing: border-box;
        }

        #faucet-table {
            table-layout: fixed;
            width: 100%;
            border-collapse: collapse;
        }

        #faucet-table th,
        #faucet-table td {
            border: none;
            padding: 12px 8px;
            text-align: left;
        }

        #faucet-table th {
            background-color: #f7f7f7;
        }

        summary {
            cursor: pointer;
            padding: 10px;
            background-color: #f7f7f7;
            border: 1px solid #cccccc;
            border-radius: 3px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        summary:hover {
            background-color: #fafafa;
        }

        #faucet-table tbody tr {
            background: linear-gradient(to bottom, white, #f7f7f7);
        }

        .main-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        @media screen and (max-width: 600px) {
            #add-faucet-form td {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }

            #add-faucet-form input[type="text"],
            #add-faucet-form input[type="number"] {
                width: 100%;
                margin-bottom: 10px;
            }

            #add-faucet-form button {
                width: 100%;
            }

            .main-content {
                padding: 0 10px;
            }
        }

        @media screen and (max-width: 320px) {
            .ready-text {
                display: none;
            }
        }

        @media screen and (max-width: 290px) {
            .progressBar {
                height: 44px;
            }
        }

        .auth-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background: #f7f7f7;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .auth-info {
            font-size: 14px;
        }

        .auth-buttons {
            display: flex;
            gap: 10px;
        }

        /* Modal Styles */
        .modal-backdrop {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
            justify-content: center;
            align-items: center;
        }

        .modal {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal h2 {
            margin-top: 0;
        }

        .modal-group {
            margin-bottom: 15px;
        }

        .modal-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .modal-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .modal-actions .button-delete {
            margin-right: auto;
            background-color: #dc3545;
            border-color: #bd2130;
            color: white;
        }

        .modal-actions .button-delete:hover {
            background-color: #c82333;
        }

        /* Floating Ad Styles */
        .floating-ad-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: fit-content;
            max-width: calc(100% - 40px);
            height: auto;
            z-index: 1000;
            display: none;
            /* Hidden by default, shown by JS */
            animation: slideInUp 0.5s ease-out;
            background: white;
            padding: 1px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
        }

        @keyframes slideInUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .floating-ad-close {
            position: absolute;
            top: -12px;
            right: -12px;
            width: 26px;
            height: 26px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            color: #333;
            font-size: 18px;
            line-height: 26px;
            text-align: center;
            cursor: pointer;
            z-index: 1002;
            transition: transform 0.2s, color 0.2s;
        }

        .floating-ad-close:hover {
            color: #e74c3c;
            transform: scale(1.1);
        }

        .floating-ad-content {
            width: 100%;
            height: 100%;
            overflow: hidden;
            display: flex;
            justify-content: center;
        }

        .floating-ad-content img {
            display: block;
            max-width: 100%;
            border-radius: 7px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        var renderFaucets;

        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            let secs = seconds % 60;
            secs = secs < 10 ? '0' + secs : secs;
            return `${mins}:${secs}`;
        }

        function progress(timeleft, timetotal, $element) {
            var progressBarWidth = timeleft * $element.width() / timetotal;
            var text = formatTime(timeleft);
            var $fill = $element.find('.progress-bar-fill');
            var $timeLeft = $element.find('.time-left');

            $timeLeft.html(text);
            $fill.animate({ width: progressBarWidth }, timeleft === timetotal ? 0 : 1000, 'linear');

            if (timeleft > 0) {
                setTimeout(function () {
                    progress(timeleft - 1, timetotal, $element);
                }, 1000);
            } else {
                renderFaucets();
            }
        }
    </script>
    <script src="js/auth.js"></script>
</head>

<body>
    <div class="auth-bar">
        <div class="auth-info" id="auth-status"></div>
        <div class="auth-buttons">
            <a href="/demo.html" class="button-secondary">Demo</a>
            <button id="login-btn" class="button-secondary" style="display:none;">Sign In</button>
            <button id="logout-btn" class="button-secondary" style="display:none;">Sign Out</button>
        </div>
    </div>

    <div id="ad-banner"
        style="text-align:center; margin-bottom: 20px; min-height: 90px; display: flex; justify-content: center; align-items: center;">
        <?php echo $bannerAd; ?>
    </div>

    <div class="main-content">
        <h1>Faucet List</h1>

        <details>
            <summary>Add New Faucet</summary>
            <form id="add-faucet-form">
                <table>
                    <tr>
                        <td>Name</td>
                        <td><input type="text" id="faucet-name" required></td>
                        <td>Timer (in minutes)</td>
                        <td><input type="number" id="faucet-timer" required></td>
                    </tr>
                    <tr>
                        <td>Url</td>
                        <td><input type="text" id="faucet-url" required></td>
                        <td colspan="2"><button type="submit" class="button-primary">Submit</button></td>
                    </tr>
                </table>
            </form>
        </details>
        <table id="faucet-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Progress</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Faucet rows will be inserted here by JavaScript -->
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function () {
            auth.handleAuthCallback();
            updateAuthUI();

            const FaucetCloud = {
                syncUrl: './api/faucet-sync.php',

                async loadFaucets() {
                    const session = auth.getSession();
                    if (!session || !session.combined_user_id) return null;

                    try {
                        const response = await fetch(`${this.syncUrl}?user_id=${session.combined_user_id}`);
                        const result = await response.json();
                        if (result.success) {
                            return result.faucets || [];
                        }
                    } catch (error) {
                        console.error('Failed to load faucet data from cloud:', error);
                    }
                    return null;
                },

                async saveFaucets(faucets) {
                    const session = auth.getSession();
                    if (!session || !session.combined_user_id) return false;

                    try {
                        const response = await fetch(`${this.syncUrl}?user_id=${session.combined_user_id}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ faucets: faucets })
                        });
                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Server error:', response.status, errorText);
                            return false;
                        }
                        const result = await response.json();
                        return result.success;
                    } catch (error) {
                        console.error('Failed to save faucet data to cloud:', error);
                        return false;
                    }
                }
            };

            const FaucetStore = {
                async getFaucets() {
                    if (auth.isLoggedIn()) {
                        const cloudFaucets = await FaucetCloud.loadFaucets();
                        if (cloudFaucets !== null && cloudFaucets.length > 0) {
                            localStorage.setItem('faucets', JSON.stringify(cloudFaucets));
                            return cloudFaucets;
                        } else if (cloudFaucets !== null && cloudFaucets.length === 0) {
                            const localFaucets = JSON.parse(localStorage.getItem('faucets')) || [];
                            if (localFaucets.length > 0) {
                                await FaucetCloud.saveFaucets(localFaucets);
                            }
                            return localFaucets;
                        }
                    }

                    return JSON.parse(localStorage.getItem('faucets')) || [];
                },
                async saveFaucets(faucets) {
                    localStorage.setItem('faucets', JSON.stringify(faucets));

                    if (auth.isLoggedIn()) {
                        await FaucetCloud.saveFaucets(faucets);
                    }
                }
            };

            renderFaucets = async function () {
                const isSmallScreen = window.innerWidth <= 600;
                const isTinyScreen = window.innerWidth <= 320;
                let faucets = await FaucetStore.getFaucets();
                const tableBody = $('#faucet-table tbody');
                tableBody.empty();

                faucets.sort((a, b) => {
                    const now = Math.floor(Date.now() / 1000);
                    const a_last_claim = a.last_claim || 0;
                    const b_last_claim = b.last_claim || 0;
                    const a_timeleft = Math.max(0, (a.timer * 60) - (now - a_last_claim));
                    const b_timeleft = Math.max(0, (b.timer * 60) - (now - b_last_claim));
                    const a_isReady = a_timeleft === 0;
                    const b_isReady = b_timeleft === 0;

                    if (a_isReady && !b_isReady) return -1;
                    if (!a_isReady && b_isReady) return 1;
                    if (a_isReady && b_isReady) return a.timer - b.timer;
                    return a_timeleft - b_timeleft;
                });

                if (faucets.length === 0) {
                    tableBody.append(`<tr><td colspan="3">No record found</td></tr>`);
                    return;
                }

                faucets.forEach((faucet) => {
                    const now = Math.floor(Date.now() / 1000);
                    const lastClaim = faucet.last_claim || 0;
                    const timerInSeconds = faucet.timer * 60;
                    const diff = now - lastClaim;
                    const timeleft = Math.max(0, timerInSeconds - diff);

                    let progressCellHtml;
                    const timerUnit = isSmallScreen ? 'm' : ' min';
                    const readyText = isTinyScreen ? '' : (isSmallScreen ? '✓ ' : 'Ready');
                    const showParens = !isSmallScreen;

                    if (timeleft > 0) {
                        progressCellHtml = `
                            <div id="timeBar_all_${faucet.id}" class="progressBar">
                                <div class="progress-bar-fill"></div>
                                <div class="progress-bar-text">
                                    <span class="time-left">${formatTime(timeleft)}</span>
                                    <span class="set-time">${faucet.timer}${timerUnit}</span>
                                </div>
                            </div>
                        `;
                    } else {
                        progressCellHtml = `
                            <div id="timeBar_all_${faucet.id}" class="progressBar">
                                <span class="ready-text">${readyText}${showParens ? `(${faucet.timer}${timerUnit})` : `${faucet.timer}${timerUnit}`}</span>
                            </div>
                        `;
                    }

                    const row = `
                        <tr>
                            <td>${faucet.name}</td>
                            <td>${progressCellHtml}</td>
                            <td>
                                <button class="button-secondary edit-faucet" data-id="${faucet.id}">Edit</button>
                                <a href="${faucet.url}" target="_blank" class="button-primary claim-faucet" data-id="${faucet.id}">Claim</a>
                            </td>
                        </tr>
                    `;
                    tableBody.append(row);

                    if (timeleft > 0) {
                        progress(timeleft, timerInSeconds, $('#timeBar_all_' + faucet.id));
                    }
                });
            }

            $('#add-faucet-form').on('submit', async function (e) {
                e.preventDefault();
                const faucets = await FaucetStore.getFaucets();
                const newFaucet = {
                    id: Date.now(),
                    name: $('#faucet-name').val(),
                    url: $('#faucet-url').val(),
                    timer: parseInt($('#faucet-timer').val()),
                    last_claim: 0
                };
                faucets.push(newFaucet);
                await FaucetStore.saveFaucets(faucets);
                this.reset();
                renderFaucets();
            });

            $('#faucet-table').on('click', '.edit-faucet', async function () {
                const faucetId = $(this).data('id');
                const faucets = await FaucetStore.getFaucets();
                const faucet = faucets.find(f => f.id === faucetId);

                if (faucet) {
                    $('#edit-faucet-id').val(faucet.id);
                    $('#edit-faucet-name').val(faucet.name);
                    $('#edit-faucet-timer').val(faucet.timer);
                    $('#edit-faucet-url').val(faucet.url);
                    $('#edit-modal-backdrop').css('display', 'flex');
                }
            });

            $('#cancel-edit').on('click', function () {
                $('#edit-modal-backdrop').hide();
            });

            $('#edit-faucet-form').on('submit', async function (e) {
                e.preventDefault();
                const faucetId = parseInt($('#edit-faucet-id').val());
                let faucets = await FaucetStore.getFaucets();
                const index = faucets.findIndex(f => f.id === faucetId);

                if (index !== -1) {
                    faucets[index].name = $('#edit-faucet-name').val();
                    faucets[index].timer = parseInt($('#edit-faucet-timer').val());
                    faucets[index].url = $('#edit-faucet-url').val();
                    await FaucetStore.saveFaucets(faucets);
                    $('#edit-modal-backdrop').hide();
                    renderFaucets();
                }
            });

            $('#delete-faucet-confirm').on('click', async function () {
                if (!confirm('Are you sure you want to delete this faucet?')) {
                    return;
                }
                const faucetId = parseInt($('#edit-faucet-id').val());
                let faucets = await FaucetStore.getFaucets();
                faucets = faucets.filter(f => f.id !== faucetId);
                await FaucetStore.saveFaucets(faucets);
                $('#edit-modal-backdrop').hide();
                renderFaucets();
            });

            $('#faucet-table').on('click', '.claim-faucet', async function () {
                const faucetId = $(this).data('id');
                let faucets = await FaucetStore.getFaucets();
                const faucet = faucets.find(f => f.id === faucetId);
                if (faucet) {
                    faucet.last_claim = Math.floor(Date.now() / 1000);
                    await FaucetStore.saveFaucets(faucets);
                    renderFaucets();
                }
            });

            function updateAuthUI() {
                const isLoggedIn = auth.isLoggedIn();
                const session = auth.getSession();

                if (isLoggedIn && session) {
                    $('#auth-status').text(`Signed in as ${session.username}`);
                    $('#login-btn').hide();
                    $('#logout-btn').show();
                } else {
                    $('#auth-status').text('Guest mode - Sign in to sync your data');
                    $('#login-btn').show();
                    $('#logout-btn').hide();
                }

                $('#login-btn').on('click', function () {
                    auth.login();
                });

                $('#logout-btn').on('click', function () {
                    auth.logout();
                });
            }

            (async function init() {
                await renderFaucets();
            })();

            let resizeTimer;
            $(window).on('resize', function () {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(async function () {
                    await renderFaucets();
                }, 250);
            });
        });
    </script>
    <div id="edit-modal-backdrop" class="modal-backdrop">
        <div class="modal">
            <h2>Edit Faucet</h2>
            <form id="edit-faucet-form">
                <input type="hidden" id="edit-faucet-id">
                <div class="modal-group">
                    <label>Name</label>
                    <input type="text" id="edit-faucet-name" required>
                </div>
                <div class="modal-group">
                    <label>Timer (in minutes)</label>
                    <input type="number" id="edit-faucet-timer" required>
                </div>
                <div class="modal-group">
                    <label>URL</label>
                    <input type="text" id="edit-faucet-url" required>
                </div>
                <div class="modal-actions">
                    <button type="button" id="delete-faucet-confirm" class="button button-delete">Delete</button>
                    <button type="button" id="cancel-edit" class="button-secondary">Cancel</button>
                    <button type="submit" class="button-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
<?php if ($floatingAd): ?>
    <div id="floating-ad" class="floating-ad-container">
        <div class="floating-ad-close" onclick="closeFloatingAd()" title="Close Ad">×</div>
        <div class="floating-ad-content"><?php echo $floatingAd; ?></div>
    </div>
    <script>
    (function() {
        var HIDE_DURATION = 10 * 60 * 1000; // 10 minutes
        var STORAGE_KEY = 'faucetlist_floating_closed_ts';
        var container = document.getElementById('floating-ad');
        
        var closedAt = localStorage.getItem(STORAGE_KEY);
        if (!closedAt || (Date.now() - parseInt(closedAt)) > HIDE_DURATION) {
            container.style.display = 'block';
        }
        
        window.closeFloatingAd = function() {
            container.style.display = 'none';
            localStorage.setItem(STORAGE_KEY, Date.now().toString());
        };
    })();
    </script>
<?php endif; ?>
</body>

</html>
