# AGENTS.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

Faucetlist.org is a cryptocurrency faucet management system that helps users track and claim from crypto earning sites. Phase 1 is a web application with localStorage for guests and optional cloud sync for logged-in users via auth.directsponsor.org.

## Architecture

### Key Components

**Frontend (site/)**
- `index.php` - Main application page with PHP-based ad rotation and inline JavaScript for UI, auth, and faucet management
- `ptc.html` - Minimal landing page for PTC traffic (fast-loading, rotating phrases, green theme)
- `demo.html` - Demo page with 5 preset timers (5/4/3/2/1 min) to showcase functionality
- `js/auth.js` - Authentication class for JWT-based login via auth.directsponsor.org
- `admin-ads.html` - Admin interface for managing banner and floating ads
- `stats.html` - Privacy-first analytics dashboard (sourced from `web-analytics/` sibling repo)

**Analytics (server-side, not in site/)**
- `analytics.py` - Log parser (repo root); deployed to `~/scripts/analytics.py` on server by deploy script
- `run-analytics.sh` - Wrapper script on server that extracts logs from DirectAdmin daily tarballs and runs parser
- Data written to `public_html/data/analytics/report-YYYY-MM-DD.json` (excluded from deploy --delete)
- Cron: `10 0 * * *` daily; source of truth and update instructions in `web-analytics/` sibling repo

**Backend API (site/api/)**
- `faucet-sync.php` - Stores/loads user faucet lists as JSON files (keyed by user_id)
- `get-banner-ads.php` - Returns ad templates from data files (supports banner and floating types)
- `save-banner-ad.php` - Admin endpoint to save/edit ads (requires admin privileges)
- `delete-banner-ad.php` - Admin endpoint to delete ads
- `upload-banner.php` - Admin endpoint to upload images, saves to `/media/` folder
- `auth-helper.php` - Authentication utility shared by ad endpoints

**Data Storage (data/)**
- `ads.txt` - Banner ad templates (HTML, separated by `---`)
- `ads-floating.txt` - Floating ad templates (managed only on server, NOT synced locally)
- `faucetlist/` - User data directory containing `{user_id}.json` files (server-only, protected from deployments)

### Data Flow

**User Data (Faucets)**
1. Guest users: localStorage only
2. Logged-in users: localStorage + POST to `faucet-sync.php`
3. Server stores in `data/faucetlist/{user_id}.json`
4. On load, app fetches from server if logged in, falls back to localStorage

**Advertisements**
1. Banner ads: PHP reads `data/ads.txt` server-side, picks random ad on each page load
2. Floating ads: PHP reads `data/ads-floating.txt` server-side, picks random ad; inline JS handles close button with 10-minute localStorage timer
3. Admin manages ads through `admin-ads.html` which calls save/delete endpoints
4. Ad content is HTML (can include images, links, scripts)

### Authentication

- JWT tokens from auth.directsponsor.org
- Session stored in localStorage under `auth_session`
- `auth.js` handles login flow, token validation, and user info
- Optional for users (guest mode works fine)

### Browser Notifications

- Toggle button (🔔) in auth bar to enable/disable
- Uses Web Notifications API (no plugin required)
- Notifies when a faucet timer reaches zero
- Works in background tabs and minimized browser
- Preference stored in localStorage (`faucetlist_notify`)
- Only works while page is open (not when browser closed)

### Timer System

- **Real-time calculations** - Uses `Date.now()` timestamps for accurate time tracking
- **Background tab immunity** - Timers remain accurate even when browser throttles background tabs
- **Single animation loop** - All timers share one `requestAnimationFrame` loop for efficiency
- **Throttled updates** - Display updates once per second for normal countdown speed
- **Clean display** - Rounds seconds to eliminate floating-point precision errors
- **Implementation**: Replaced `setTimeout` intervals with real-time elapsed time calculations

## Deployment

**Local Development**
- Requires PHP server (e.g. `php -S localhost:8000` in site/ folder)
- Works offline for faucet tracking (localStorage)
- Ad system requires PHP backend (won't show ads when opening file directly)

**Production Deployment**
- Use `./deploy-to-directadmin.sh` script
- Syncs site files to DirectAdmin server at directadmin-de.kxe.io:10500
- Data files (ads, user data) managed on server only - NOT synced from local
- Uploaded images in `/media/` folder are protected from deletion
- Uses SSH key authentication (faucetlist_key_rsa)

**Setup for Deploy Script**
```bash
./deploy-to-directadmin.sh
```

## Important Implementation Details

### Social Media / Open Graph

All public pages (`index.php`, `about.html`, `demo.html`, `ptc.html`) have Open Graph and Twitter Card meta tags for good social share previews.

The `og:image` / `twitter:image` points to a self-hosted file `og-image.png` in the site root. This was generated once via **ogcdn.net** using a saved template (1200×630px, background image overlaid with title, description and CTA text) and downloaded for self-hosting.

**Template details:**
- Service: https://www.opengraph.xyz (CDN: ogcdn.net)
- Template name: *Faucet List Clean Tech*
- Template ID: `c8bcadb7-7584-47bf-925f-627236ae1f7d`
- Version: `2`
- Background image: `https://faucetlist.org/faucetlist.png`
- Logo: `https://faucetlist.org/favicons/favicon.svg`

**URL pattern** (all values must be URL-encoded):
```
https://ogcdn.net/c8bcadb7-7584-47bf-925f-627236ae1f7d/v2/{imageUrl}/{logoUrl}/{titleText}/{descriptionText}/{ctaText}/og.png
```

To regenerate `og-image.png` (e.g. if you change the background image or text), use the URL pattern above with updated encoded values, download the result, save it as `site/og-image.png`, and redeploy.

### Ad System
- Ads in `ads.txt` and `ads-floating.txt` are separated by `---` on its own line
- PHP reads files server-side and picks random ad on each page load
- Ad content is raw HTML - can contain `<img>`, `<a>`, `<script>` tags
- Images uploaded via admin go to `/media/` folder
- Admin can upload images, add/edit/delete ads via `admin-ads.html`

### Ad-Blocker Avoidance
- **Filenames**: Avoid ad dimensions (`728x90`, `300x250`) and keywords (`banner`, `ad`, `advert`). Use neutral names like `feature-green.png`, `partner1.jpg`
- **Element IDs/classes**: Use neutral names. Current: `top-image` (banner container), `float-notice` (floating ad)
- **Don't use**: `ad-banner`, `advertisement`, `sponsor`, `promo` in IDs/classes

### Data Protection Strategy
- Data files (`data/`) are NOT synced from local - managed entirely on server
- Uploaded images in `media/*` are excluded from rsync --delete
- Site files (`site/`) use `--delete` flag (removes deleted files from server)

### User Authentication Notes
- Admin interface (`admin-ads.html`) requires login and admin privileges via `auth-helper.php`
- `requireAdmin()` function in `auth-helper.php` validates permissions
- User session contains `combined_user_id` field used for data file naming

### Mobile Responsiveness
- Main content has max-width (800px) with centered layout
- Timer display adapts (shows "✓" on very small screens)
- Progress bars use responsive sizing with flexbox

### Timer Implementation Details

**Problem Solved:** Browser throttling of `setTimeout` in background tabs caused inaccurate countdowns.

**Solution Architecture:**
- `timerStates` object stores start time and initial time left for each faucet
- `updateAllTimers()` function runs via `requestAnimationFrame` with throttling
- Real-time calculation: `timeleft = initialTimeLeft - elapsed`
- Display updates limited to once per second via `lastUpdateTime` tracking
- `formatTime()` rounds seconds to eliminate floating-point precision errors

**Key Functions:**
- `progress()` - Initializes timer state and starts animation loop
- `updateAllTimers()` - Main animation loop with throttling
- `updateTimerDisplay()` - Updates progress bar and time display
- `clearAllTimers()` - Cleans up all active timers and animation frame

## Common Tasks

### Testing the Ad System
1. Deploy to server and visit the page - banner ad should appear at top
2. For floating ads, clear `faucetlist_floating_closed_ts` in localStorage to reset the 10-minute timer
3. Verify different ads appear on page reload (PHP randomly selects from ads.txt/ads-floating.txt)

### Adding/Editing Ads
1. Go to `/admin-ads.html` and sign in (requires admin privileges)
2. Upload images using the upload section (saves to `/media/`)
3. Add ad HTML using the uploaded image URL, or paste script tags for third-party ads
4. Edit existing ads by clicking the Edit button
5. Ads are stored in `data/ads.txt` (banner) or `data/ads-floating.txt` (floating)

### Testing User Data Sync
1. Add a faucet while logged in
2. Check browser DevTools Network for POST to `faucet-sync.php`
3. Check server: `ssh directsponsor-net`, look for JSON file in `data/faucetlist/`
4. Reload page - should load from server

### Testing Timer Accuracy
1. Add a faucet with a timer (e.g., 5 minutes)
2. Click "Claim" to start the countdown
3. Switch to other tabs or minimize browser for several minutes
4. Return to verify timer shows correct remaining time (should be accurate)
5. Test notifications by enabling 🔔 and waiting for timer to complete

### SSH Access
- Use `ssh faucetlist-directadmin` (configured in ~/.ssh/config)
- Key: `~/.ssh/faucetlist_key_rsa`
- Server path: `/home/faucetlist/domains/faucetlist.org/`

## File Organization

```
faucetlist.org/
├── site/                    # Frontend + API
│   ├── index.php           # Main app with PHP ad rotation
│   ├── ptc.html            # PTC landing page (minimal, fast)
│   ├── demo.html           # Demo with preset timers
│   ├── admin-ads.html      # Ad management interface (upload, add, edit, delete)
│   ├── favicon.ico         # Root favicon for browser default
│   ├── api/                # Backend endpoints
│   │   ├── faucet-sync.php
│   │   ├── get-banner-ads.php
│   │   ├── save-banner-ad.php
│   │   ├── delete-banner-ad.php
│   │   ├── upload-banner.php
│   │   └── auth-helper.php
│   ├── js/
│   │   └── auth.js         # JWT auth class
│   ├── media/              # Uploaded ad images (server-only, protected)
│   └── favicons/           # Site branding
├── data/                    # Local reference only (NOT synced to server)
│   ├── ads.txt             # Example banner ads
│   └── ads-floating.txt    # Example floating ads
├── deploy-to-directadmin.sh # Deployment script
└── AGENTS.md               # This file
```

**Server data structure** (at `/home/faucetlist/domains/faucetlist.org/`):
```
public_html/                 # Web root
├── media/                   # Uploaded ad images
└── [site files]
data/                        # Outside web root
├── ads.txt                  # Banner ad templates
├── ads-floating.txt         # Floating ad templates  
└── faucetlist/              # User data JSON files
```

## Current Status & Next Steps

### Deployment
✅ **Automated rsync deployment working** - SSH key authentication
- Deploy script: `./deploy-to-directadmin.sh`
- Site files synced with --delete flag
- Data files and uploaded media protected (not overwritten)

### Ad System
✅ **PHP-based ad rotation** - No JavaScript fetch, no CORS issues
- Server-side random selection from txt files
- Admin interface with image upload, add/edit/delete functionality
- Images stored in `/media/` folder (avoids ad-blocker detection)

## Known Limitations

- Ad system requires PHP server (won't display ads when opening file locally)
- No analytics on ad impressions or clicks (stored in data files only)
- Floating ads use simple timer-based dismissal, not sophisticated frequency capping
- API endpoints don't validate ad HTML (assumes admin is trusted)
- No backup system for user data files (manual backup needed)
- **A-Ads "Adaptive" iframe ads don't scale properly** - Despite being labelled "responsive", they don't respect container sizing consistently. Setting width/height/aspect-ratio on the container or iframe results in blurring, cropping, or incorrect proportions. Static image ads work fine; the issue is specific to A-Ads adaptive iframes. A static version is kept on `/about.html` for A-Ads verification. Consider replacing with our own ads when inventory allows.

## Future Considerations

- ~~Phase 2: Chrome extension with notifications~~ Browser notifications now built-in
- Currently no membership/payment system
- Ad inventory management could use a database
- Could implement ad scheduling and A/B testing
