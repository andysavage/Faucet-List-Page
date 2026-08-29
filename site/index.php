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
    <title>Faucet List – Track Your Crypto Faucets</title>
    <meta name="description" content="Track all your cryptocurrency faucets with automatic countdown timers. Never miss a claim again.">

    <!-- Open Graph Meta Tags -->
    <meta property="og:url" content="https://faucetlist.org/">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Faucet List – Track Your Crypto Faucets">
    <meta property="og:description" content="Track all your cryptocurrency faucets with automatic countdown timers. Never miss a claim again.">
    <meta property="og:image" content="https://faucetlist.org/og-image.png">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta property="twitter:domain" content="faucetlist.org">
    <meta property="twitter:url" content="https://faucetlist.org/">
    <meta name="twitter:title" content="Faucet List – Track Your Crypto Faucets">
    <meta name="twitter:description" content="Track all your cryptocurrency faucets with automatic countdown timers. Never miss a claim again.">
    <meta name="twitter:image" content="https://faucetlist.org/og-image.png">

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
        
        body.dark-mode #faucet-table tbody tr {
            background: linear-gradient(to bottom, #2d2d2d, #252525);
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
        
        /* Dark mode styles */
        body.dark-mode {
            background-color: #1a1a1a;
            color: #e0e0e0;
        }
        
        body.dark-mode .auth-bar {
            background: #2d2d2d;
            border-bottom-color: #444;
        }
        
        body.dark-mode .button,
        body.dark-mode .button-secondary {
            color: #e0e0e0;
            border-color: #555;
            background: #3a3a3a;
            -webkit-box-shadow: inset 0 1px 0 #555, 0 1px 0 rgba(0, 0, 0, .3);
            box-shadow: inset 0 1px 0 #555, 0 1px 0 rgba(0, 0, 0, .3);
        }
        
        body.dark-mode .button:hover,
        body.dark-mode .button-secondary:hover,
        body.dark-mode .button:focus,
        body.dark-mode .button-secondary:focus {
            background: #4a4a4a;
            border-color: #666;
            color: #fff;
        }
        
        body.dark-mode .button-primary {
            background: #0073aa;
            border-color: #005a87;
            -webkit-box-shadow: inset 0 1px 0 rgba(0, 150, 220, 0.5), 0 1px 0 rgba(0, 0, 0, .3);
            box-shadow: inset 0 1px 0 rgba(0, 150, 220, 0.5), 0 1px 0 rgba(0, 0, 0, .3);
        }
        
        body.dark-mode .button-primary:hover {
            background: #0090d4;
            border-color: #006ba1;
        }
        
        body.dark-mode h1 {
            color: #e0e0e0;
        }
        
        body.dark-mode table {
            border-color: #444;
        }
        
        body.dark-mode #faucet-table th {
            background: #3a3a3a;
            color: #e0e0e0;
            border-color: #555;
        }
        
        body.dark-mode td {
            border-color: #444;
        }
        
        body.dark-mode .progressBar {
            background: #3a3a3a;
            border: 1px solid #555;
        }
        
        body.dark-mode .progress-bar-fill {
            background: #0073aa;
        }
        
        body.dark-mode .progress-bar-text {
            color: #e0e0e0;
        }
        
        body.dark-mode details {
            border-color: #444;
        }
        
        body.dark-mode summary {
            background: #3a3a3a;
            border-color: #555;
            color: #e0e0e0;
        }
        
        body.dark-mode summary:hover {
            background: #4a4a4a;
        }
        
        body.dark-mode .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.7);
        }
        
        body.dark-mode .modal {
            background-color: #2d2d2d;
            color: #e0e0e0;
        }
        
        body.dark-mode .modal h2 {
            color: #e0e0e0;
        }
        
        body.dark-mode .modal-group label {
            color: #e0e0e0;
        }
        
        body.dark-mode .modal-group input {
            background: #3a3a3a;
            border-color: #555;
            color: #e0e0e0;
        }
        
        body.dark-mode .modal-actions .button-delete {
            background-color: #c82333;
            border-color: #bd2130;
        }
        
        body.dark-mode .modal-actions .button-delete:hover {
            background-color: #a71d2a;
        }
        
        body.dark-mode .menu-dropdown-content {
            background: #2d2d2d;
            border-color: #555;
        }
        
        body.dark-mode .menu-dropdown-content a {
            color: #e0e0e0;
        }
        
        body.dark-mode .menu-dropdown-content a:hover {
            background: #3a3a3a;
        }
        
        body.dark-mode .float-notice-content {
            background: #2d2d2d;
            border-color: #555;
        }
        
        body.dark-mode .float-notice-close {
            background: #3a3a3a;
            color: #e0e0e0;
        }
        
        body.dark-mode .float-notice-close:hover {
            color: #ff6b6b;
        }
        
        body.dark-mode input[type="text"],
        body.dark-mode input[type="number"] {
            background: #3a3a3a;
            border-color: #555;
            color: #e0e0e0;
        }
        
        body.dark-mode input[type="text"]:focus,
        body.dark-mode input[type="number"]:focus {
            border-color: #0073aa;
        }
        
        .dark-mode-toggle {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            padding: 5px;
            margin-left: 10px;
        }
        
        .dark-mode-toggle:hover {
            opacity: 0.7;
        }

        .auth-info {
            font-size: 14px;
        }

        .auth-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        /* Dropdown Menu */
        .menu-dropdown {
            position: relative;
        }

        .menu-dropdown-toggle {
            cursor: pointer;
            min-width: 36px;
            text-align: center;
        }

        .menu-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 4px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            min-width: 100px;
            z-index: 100;
        }

        .menu-dropdown-content a {
            display: block;
            padding: 8px 12px;
            color: #333;
            text-decoration: none;
            white-space: nowrap;
        }

        .menu-dropdown-content a:hover {
            background: #f7f7f7;
        }

        .menu-dropdown.open .menu-dropdown-content {
            display: block;
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

        /* Floating Notice Styles */
        .float-notice {
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
            flex-direction: column;
            align-items: flex-end;
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

        .float-notice-close {
            margin-bottom: 4px;
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
            transition: transform 0.2s, color 0.2s;
        }

        .float-notice-close:hover {
            color: #e74c3c;
            transform: scale(1.1);
        }

        #banner-outer { display: flex; flex-direction: column; align-items: flex-end; }
        .banner-close {
            margin-bottom: 4px;
            width: 26px;
            height: 26px;
            background: #fff;
            border: none;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            color: #333;
            font-size: 18px;
            line-height: 26px;
            text-align: center;
            cursor: pointer;
            padding: 0;
            transition: transform 0.2s, color 0.2s;
        }
        .banner-close:hover { color: #e74c3c; transform: scale(1.1); }

        /* Priority star toggle */
        .priority-star {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 13px;
            padding: 0;
            margin-left: 6px;
            float: right;
            opacity: 0.25;
            vertical-align: middle;
            line-height: 1;
            transition: opacity 0.15s;
        }
        .priority-star.is-priority {
            opacity: 1;
        }
        .priority-star:hover {
            opacity: 1;
        }

        .float-notice-content {
            width: 100%;
            overflow: hidden;
            display: flex;
            justify-content: center;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
        }

        .float-notice-content img {
            display: block;
            max-width: 100%;
            border-radius: 7px;
        }
        
        /* Welcome Section Styles */
        .welcome-section {
            text-align: center;
            padding: 30px 20px;
            margin-bottom: 30px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        body.dark-mode .welcome-section {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            border-color: #4a5568;
        }
        
        .welcome-description {
            font-size: 16px;
            color: #495057;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        
        body.dark-mode .welcome-description {
            color: #cbd5e0;
        }
        
        .welcome-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .welcome-button {
            padding: 12px 24px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .welcome-button-demo {
            background: #28a745;
            color: white;
            border: 1px solid #28a745;
        }
        
        .welcome-button-demo:hover {
            background: #218838;
            border-color: #1e7e34;
            text-decoration: none;
            color: white;
        }
        
        .welcome-button-about {
            background: #6c757d;
            color: white;
            border: 1px solid #6c757d;
        }
        
        .welcome-button-about:hover {
            background: #5a6268;
            border-color: #545b62;
            text-decoration: none;
            color: white;
        }
        
        @media screen and (max-width: 600px) {
            .welcome-section {
                padding: 20px 15px;
                margin-bottom: 20px;
            }
            
            .welcome-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .welcome-button {
                width: 100%;
                max-width: 250px;
                justify-content: center;
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        var renderFaucets;
        var activeTimers = {}; // Track active timers by faucet ID
        var animationFrameId = null; // Global animation frame ID
        var timerStates = {}; // Track timer states with start times
        var lastUpdateTime = 0; // Track last update time for throttling
        var notifyTimeouts = {}; // Per-faucet setTimeout handles for notifications (fires in background tabs)

        function formatTime(seconds) {
            // Round to nearest integer to avoid floating point precision issues
            seconds = Math.round(seconds);
            const mins = Math.floor(seconds / 60);
            let secs = seconds % 60;
            secs = secs < 10 ? '0' + secs : secs;
            return `${mins}:${secs}`;
        }

        function notifyReady(faucetName) {
            if (localStorage.getItem('faucetlist_notify') !== 'true') return;
            if (Notification.permission !== 'granted') return;
            
            new Notification('Faucet Ready!', {
                body: faucetName + ' is ready to claim',
                icon: '/favicons/favicon-96x96.png',
                tag: 'faucet-' + faucetName
            });
        }

        function clearAllTimers() {
            if (animationFrameId) {
                cancelAnimationFrame(animationFrameId);
                animationFrameId = null;
            }
            for (var id in notifyTimeouts) clearTimeout(notifyTimeouts[id]);
            activeTimers = {};
            timerStates = {};
            notifyTimeouts = {};
            lastUpdateTime = 0;
        }

        function progress(timeleft, timetotal, $element, faucetId, faucetName) {
            // Store timer state
            timerStates[faucetId] = {
                startTime: Date.now(),
                totalTime: timetotal,
                initialTimeLeft: timeleft,
                element: $element,
                name: faucetName
            };
            activeTimers[faucetId] = true;

            // Schedule notification via setTimeout so it fires even in background tabs.
            // rAF (used for display updates) is paused by browsers in background tabs,
            // but setTimeout is not, so this is the reliable path for notifications.
            if (notifyTimeouts[faucetId]) clearTimeout(notifyTimeouts[faucetId]);
            notifyTimeouts[faucetId] = setTimeout(function() {
                if (timerStates[faucetId]) timerStates[faucetId].notified = true;
                delete notifyTimeouts[faucetId];
                notifyReady(faucetName);
            }, timeleft * 1000);

            // Start global animation loop if not already running
            if (!animationFrameId) {
                updateAllTimers();
            }
        }
        
        function updateAllTimers() {
            const now = Date.now();
            
            // Throttle updates to once per second
            if (now - lastUpdateTime < 1000) {
                if (animationFrameId) {
                    animationFrameId = requestAnimationFrame(updateAllTimers);
                }
                return;
            }
            
            lastUpdateTime = now;
            let hasActiveTimers = false;
            
            for (var faucetId in timerStates) {
                const state = timerStates[faucetId];
                const elapsed = (now - state.startTime) / 1000; // Convert to seconds
                const timeleft = Math.max(0, state.initialTimeLeft - elapsed);
                
                if (timeleft > 0) {
                    hasActiveTimers = true;
                    updateTimerDisplay(state.element, timeleft, state.totalTime);
                } else {
                    // Timer finished - cancel pending notification timeout if still waiting,
                    // then fire notification (unless setTimeout already fired it in background).
                    if (notifyTimeouts[faucetId]) {
                        clearTimeout(notifyTimeouts[faucetId]);
                        delete notifyTimeouts[faucetId];
                    }
                    delete activeTimers[faucetId];
                    delete timerStates[faucetId];
                    if (state.name && !state.notified) notifyReady(state.name);
                    setTimeout(renderFaucets, 0);
                    return; // renderFaucets will restart timers if needed
                }
            }
            
            if (hasActiveTimers) {
                animationFrameId = requestAnimationFrame(updateAllTimers);
            } else {
                animationFrameId = null;
            }
        }
        
        function updateTimerDisplay($element, timeleft, timetotal) {
            const progressPercent = (timeleft / timetotal) * 100;
            const text = formatTime(timeleft);
            const $fill = $element.find('.progress-bar-fill');
            const $timeLeft = $element.find('.time-left');

            $timeLeft.html(text);
            $fill.css('width', progressPercent + '%');
        }
    </script>
    <script src="js/auth.js"></script>
</head>

<body>
    <div class="auth-bar">
        <div class="auth-info" id="auth-status"></div>
        <div id="auth-error-display" style="color: #c82333; font-weight: bold; display: none; margin: 0 10px;"></div>
        <div class="auth-buttons">
            <button id="dark-mode-toggle" class="dark-mode-toggle" title="Toggle dark mode">🌙</button>
            <button id="notify-btn" class="button-secondary" title="Enable notifications">🔔 Off</button>
            <div class="menu-dropdown">
<button class="button-secondary menu-dropdown-toggle" title="More">???</button>
                <div class="menu-dropdown-content">
                    <a href="/guide.html">Guide</a>
                    <a href="/demo.html">Demo</a>
                    <a href="/about.html">About</a>
                </div>
            </div>
            <button id="login-btn" class="button-secondary" style="display:none;">Sign In</button>
            <button id="logout-btn" class="button-secondary" style="display:none;">Sign Out</button>
        </div>
    </div>

    <div id="banner-outer" style="position: relative; max-width: 728px; margin: 0 auto 20px auto; text-align: center;">
        <div id="banner-wrap"
            style="aspect-ratio: 728 / 90; display: flex; justify-content: center; align-items: center;">
            <?php echo $bannerAd; ?>
        </div>
    </div>
    <script src="/js/banner.js"></script>

    <div class="main-content">
        <h1>Faucet List</h1>

        <div id="welcome-section" class="welcome-section">
            <div class="welcome-description">
                Track your cryptocurrency faucets with automatic timers that stay accurate even when you switch tabs.
            </div>
            <div class="welcome-buttons">
                <a href="/demo.html" class="welcome-button welcome-button-demo">
                    <span>Try Demo</span>
                </a>
                <a href="/about.html" class="welcome-button welcome-button-about">
                    <span>Learn More</span>
                </a>
            </div>
        </div>

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
                        <td><label><input type="checkbox" id="faucet-priority"> ⭐ Priority</label></td>
                        <td><button type="submit" class="button-primary">Submit</button></td>
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
            initNotifications();

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
                _cache: null,
                async getFaucets() {
                    if (this._cache !== null) return this._cache;

                    if (auth.isLoggedIn()) {
                        const cloudFaucets = await FaucetCloud.loadFaucets();
                        if (cloudFaucets !== null) {
                            // Cloud is source of truth for logged-in users
                            localStorage.setItem('faucets', JSON.stringify(cloudFaucets));
                            this._cache = cloudFaucets;
                            return cloudFaucets;
                        }
                        // Cloud fetch failed - fall back to local without uploading
                    }

                    const faucets = JSON.parse(localStorage.getItem('faucets')) || [];
                    this._cache = faucets;
                    return faucets;
                },
                async saveFaucets(faucets) {
                    this._cache = faucets;
                    localStorage.setItem('faucets', JSON.stringify(faucets));

                    if (auth.isLoggedIn()) {
                        await FaucetCloud.saveFaucets(faucets);
                    }
                },
            };

            renderFaucets = async function () {
                clearAllTimers();
                const isSmallScreen = window.innerWidth <= 600;
                const isTinyScreen = window.innerWidth <= 320;
                let faucets = await FaucetStore.getFaucets();
                const tableBody = $('#faucet-table tbody');
                tableBody.empty();

                // Show/hide welcome section based on faucet count
                const $welcomeSection = $('#welcome-section');
                if (faucets.length === 0) {
                    $welcomeSection.show();
                } else {
                    $welcomeSection.hide();
                }

                faucets.sort((a, b) => {
                    const now = Math.floor(Date.now() / 1000);
                    const a_timeleft = Math.max(0, (a.timer * 60) - (now - (a.last_claim || 0)));
                    const b_timeleft = Math.max(0, (b.timer * 60) - (now - (b.last_claim || 0)));
                    const a_isReady = a_timeleft === 0;
                    const b_isReady = b_timeleft === 0;

                    if (a_isReady && b_isReady) {
                        // Within ready group: priority faucets first, then alphabetical
                        if (!!a.priority !== !!b.priority) return a.priority ? -1 : 1;
                        return a.name.localeCompare(b.name);
                    }
                    if (a_isReady !== b_isReady) return a_isReady ? -1 : 1;
                    // Both counting down: soonest first, priority has no effect
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

                    const isPriority = !!faucet.priority;
                    const starClass = isPriority ? 'priority-star is-priority' : 'priority-star';
                    const starIcon  = isPriority ? '⭐' : '☆';
                    const starTitle = isPriority ? 'Remove priority' : 'Set as priority';
                    const row = `
                        <tr>
                            <td>
                                ${faucet.name}<button class="${starClass}" data-id="${faucet.id}" title="${starTitle}">${starIcon}</button>
                            </td>
                            <td>${progressCellHtml}</td>
                            <td>
                                <button class="button-secondary edit-faucet" data-id="${faucet.id}">Edit</button>
                                <a href="${faucet.url}" target="_blank" class="button-primary claim-faucet" data-id="${faucet.id}">Claim</a>
                            </td>
                        </tr>
                    `;
                    tableBody.append(row);

                    if (timeleft > 0) {
                        progress(timeleft, timerInSeconds, $('#timeBar_all_' + faucet.id), faucet.id, faucet.name);
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
                    last_claim: 0,
                    priority: $('#faucet-priority').is(':checked')
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
                    $('#edit-faucet-priority').prop('checked', !!faucet.priority);
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
                    faucets[index].priority = $('#edit-faucet-priority').is(':checked');
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

            $('#faucet-table').on('click', '.priority-star', async function () {
                const faucetId = $(this).data('id');
                let faucets = await FaucetStore.getFaucets();
                const faucet = faucets.find(f => f.id === faucetId);
                if (faucet) {
                    faucet.priority = !faucet.priority;
                    await FaucetStore.saveFaucets(faucets);
                    renderFaucets();
                }
            });

            // Notifications
            function initNotifications() {
                const enabled = localStorage.getItem('faucetlist_notify') === 'true';
                updateNotifyButton(enabled && Notification.permission === 'granted');

                $('#notify-btn').on('click', async function() {
                    const currentlyEnabled = localStorage.getItem('faucetlist_notify') === 'true';
                    
                    if (currentlyEnabled) {
                        localStorage.setItem('faucetlist_notify', 'false');
                        updateNotifyButton(false);
                    } else {
                        if (!('Notification' in window)) {
                            alert('Your browser does not support notifications');
                            return;
                        }
                        
                        let permission = Notification.permission;
                        if (permission === 'default') {
                            permission = await Notification.requestPermission();
                        }
                        
                        if (permission === 'granted') {
                            localStorage.setItem('faucetlist_notify', 'true');
                            updateNotifyButton(true);
                        } else {
                            alert('Notification permission denied');
                        }
                    }
                });
            }

            function updateNotifyButton(enabled) {
                $('#notify-btn').text(enabled ? '🔔 On' : '🔔 Off');
                $('#notify-btn').attr('title', enabled ? 'Disable notifications' : 'Enable notifications');
            }

            // Dropdown menu (click for mobile)
            $('.menu-dropdown-toggle').on('click', function(e) {
                e.stopPropagation();
                $(this).closest('.menu-dropdown').toggleClass('open');
            });
            $(document).on('click', function() {
                $('.menu-dropdown').removeClass('open');
            });

            function updateAuthUI() {
                const isLoggedIn = auth.isLoggedIn();
                const session = auth.getSession();
                const authError = localStorage.getItem('auth_error');

                if (isLoggedIn && session) {
                    $('#auth-status').text(session.username);
                    $('#login-btn').hide();
                    $('#logout-btn').show();
                } else {
                    $('#auth-status').text('Guest mode');
                    $('#login-btn').show();
                    $('#logout-btn').hide();
                }
                
                // Display any auth error
                if (authError) {
                    $('#auth-error-display').text(authError).show();
                    // Auto-clear after 10 seconds
                    setTimeout(() => {
                        localStorage.removeItem('auth_error');
                        $('#auth-error-display').fadeOut();
                    }, 10000);
                }

            }

            // Dark mode functionality
            function initDarkMode() {
                const darkModeToggle = $('#dark-mode-toggle');
                const body = $('body');
                
                // Load saved preference
                const isDarkMode = localStorage.getItem('darkMode') === 'true';
                if (isDarkMode) {
                    body.addClass('dark-mode');
                    darkModeToggle.text('☀️');
                }
                
                // Toggle dark mode
                darkModeToggle.on('click', function() {
                    body.toggleClass('dark-mode');
                    const isDark = body.hasClass('dark-mode');
                    localStorage.setItem('darkMode', isDark);
                    darkModeToggle.text(isDark ? '☀️' : '🌙');
                });
            }

            // Handle login/signup - save guest faucets first
            function loginWithGuestFaucets() {
                const faucets = JSON.parse(localStorage.getItem('faucets')) || [];
                if (faucets.length > 0) {
                    // Save guest faucets to import after login
                    localStorage.setItem('faucets_to_import', JSON.stringify(faucets));
                }
                auth.login();
            }
            
            $('#login-btn').off('click').on('click', loginWithGuestFaucets);
            $('#logout-btn').off('click').on('click', function () {
                auth.logout();
            });
            
            // Import saved faucets after login
            $('#import-faucets-btn').on('click', async function() {
                const savedFaucets = JSON.parse(localStorage.getItem('faucets_to_import')) || [];
                if (savedFaucets.length > 0) {
                    // Load existing server faucets and merge with saved ones
                    const serverFaucets = await FaucetCloud.loadFaucets() || [];
                    const merged = [...serverFaucets];
                    const serverUrls = new Set(serverFaucets.map(f => f.url));
                    
                    // Add saved faucets that don't already exist
                    savedFaucets.forEach(saved => {
                        if (!serverUrls.has(saved.url)) {
                            merged.push(saved);
                        }
                    });
                    
                    await FaucetStore.saveFaucets(merged);
                    localStorage.removeItem('faucets_to_import');
                    $('#import-modal-backdrop').hide();
                    await renderFaucets();
                }
            });
            
            $('#skip-import-btn').on('click', function() {
                localStorage.removeItem('faucets_to_import');
                $('#import-modal-backdrop').hide();
            });

            (async function init() {
                initDarkMode();
                
                // Check if user just logged in with saved faucets
                if (auth.isLoggedIn() && localStorage.getItem('faucets_to_import')) {
                    const savedFaucets = JSON.parse(localStorage.getItem('faucets_to_import')) || [];
                    if (savedFaucets.length > 0) {
                        $('#import-faucet-count').text(savedFaucets.length);
                        $('#import-modal-backdrop').css('display', 'flex');
                        // Don't render faucets yet, wait for user choice
                        return;
                    }
                }
                
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
    <!-- Import Saved Faucets Modal -->
    <div id="import-modal-backdrop" class="modal-backdrop">
        <div class="modal">
            <h2>Import Your Saved Faucets</h2>
            <p>You saved <strong id="import-faucet-count">0</strong> faucets before signing up. Would you like to add them to your account?</p>
            <div class="modal-actions" style="flex-direction: column; gap: 8px;">
                <button type="button" id="import-faucets-btn" class="button-primary" style="width: 100%; text-align: center; margin-bottom: 8px;">
                    ✓ Add to my account
                </button>
                <button type="button" id="skip-import-btn" class="button" style="width: 100%; text-align: center;">
                    No thanks
                </button>
            </div>
        </div>
    </div>

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
                <div class="modal-group">
                    <label><input type="checkbox" id="edit-faucet-priority"> ⭐ Priority</label>
                </div>
                <div class="modal-actions">
                    <button type="button" id="delete-faucet-confirm" class="button button-delete">Delete</button>
                    <button type="button" id="cancel-edit" class="button-secondary">Cancel</button>
                    <button type="submit" class="button-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <footer style="margin-top:40px; padding:16px; text-align:center; font-size:0.85em; color:#6c757d; border-top:1px solid #dee2e6;">
        <a href="https://www.clickforcharity.net" target="_blank" rel="noopener">ClickForCharity.net</a>
        &nbsp;&middot;&nbsp;
        <a href="https://www.directsponsor.net" target="_blank" rel="noopener">DirectSponsor.net</a>
    </footer>

<?php if ($floatingAd): ?>
    <div id="float-notice" class="float-notice">
        <div class="float-notice-close" onclick="closeNotice()" title="Close">×</div>
        <div class="float-notice-content"><?php echo $floatingAd; ?></div>
    </div>
    <script>
    (function() {
        var HIDE_DURATION     = 10 * 60 * 1000; // 10 minutes
        var STORAGE_KEY       = 'faucetlist_floating_closed_ts';
        var NEW_VISITOR_DELAY = 30 * 1000; // 30 seconds
        var container = document.getElementById('float-notice');

        function showNotice() {
            container.style.display = 'flex';
        }

        var closedAt = localStorage.getItem(STORAGE_KEY);
        if (!closedAt || (Date.now() - parseInt(closedAt)) > HIDE_DURATION) {
            var isLoggedIn = !!localStorage.getItem('directsponsor_session');
            var faucetsRaw = localStorage.getItem('faucets');
            var hasFaucets = faucetsRaw && JSON.parse(faucetsRaw).length > 0;
            if (!isLoggedIn && !hasFaucets) {
                setTimeout(showNotice, NEW_VISITOR_DELAY);
            } else {
                showNotice();
            }
        }

        window.closeNotice = function() {
            container.style.display = 'none';
            localStorage.setItem(STORAGE_KEY, Date.now().toString());
        };
    })();
    </script>
<?php endif; ?>
</body>

</html>
