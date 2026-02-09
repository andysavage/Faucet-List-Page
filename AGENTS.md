# AGENTS.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

Faucetlist.org is a cryptocurrency faucet management system that helps users track and claim from crypto earning sites. Phase 1 is a web application with localStorage for guests and optional cloud sync for logged-in users via auth.directsponsor.org.

## Architecture

### Key Components

**Frontend (site/)**
- `index.php` - Main application page with PHP-based ad rotation and inline JavaScript for UI, auth, and faucet management
- `js/auth.js` - Authentication class for JWT-based login via auth.directsponsor.org
- `admin-ads.html` - Admin interface for managing banner and floating ads

**Backend API (site/api/)**
- `faucet-sync.php` - Stores/loads user faucet lists as JSON files (keyed by user_id)
- `get-banner-ads.php` - Returns ad templates from data files (supports banner and floating types)
- `save-banner-ad.php` - Admin endpoint to save new ads (requires admin privileges)
- `delete-banner-ad.php` - Admin endpoint to delete ads
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

## Deployment

**Local Development**
- Open `site/index.html` directly in browser
- Works entirely offline except for sync features
- Ad system requires running API (won't show ads without PHP backend)

**Production Deployment**
- Use `./deploy-to-directadmin.sh` script
- Syncs to DirectAdmin server at directadmin-de.kxe.io:10500
- Protects server-created data (faucetlist/ directory, ads-floating.txt)
- Requires `FAUCETLIST_PASSWORD` environment variable

**Setup for Deploy Script**
```bash
export FAUCETLIST_PASSWORD='your_password'
./deploy-to-directadmin.sh
```

## Important Implementation Details

### Ad System Quirks
- Ads in `ads.txt` and `ads-floating.txt` are separated by `---` on its own line
- `get-banner-ads.php` reads entire file and splits on `---` separator
- Ad content is raw HTML - can contain `<img>`, `<a>`, `<script>` tags
- No HTML escaping - admin must ensure safe ad content
- Floating ads reference images already on server (`/banners/` path) - local test images are for reference only

### Data Protection Strategy
- `SERVER_ONLY_FILES` array in deploy script lists what never gets overwritten
- Currently protects: `faucetlist/` (user data), `ads-floating.txt` (server-managed ads)
- If new server-only data is created, add to this array in deploy script
- Site files (`site/`) use `--delete` flag (removes deleted files from server)
- Data files (`data/`) sync WITHOUT `--delete` (preserves server-created content)

### User Authentication Notes
- Admin interface (`admin-ads.html`) requires login and admin privileges via `auth-helper.php`
- `requireAdmin()` function in `auth-helper.php` validates permissions
- User session contains `combined_user_id` field used for data file naming

### Mobile Responsiveness
- URL column hidden on screens ≤600px
- Timer display adapts (shows "✓" on very small screens)
- Progress bars use responsive sizing with flexbox

## Common Tasks

### Testing the Ad System
1. Deploy to server and visit the page - banner ad should appear at top
2. For floating ads, clear `faucetlist_floating_closed_ts` in localStorage to reset the 10-minute timer
3. Verify different ads appear on page reload (PHP randomly selects from ads.txt/ads-floating.txt)

### Adding New Ads
1. Via admin: Go to `/admin-ads.html`, sign in, paste HTML, select type (Banner or Floating)
2. Via direct server edit: SSH into server, edit `/home/faucetlist/data/ads.txt` or `ads-floating.txt`
3. Format: Multiple ads separated by `---` on own line

### Testing User Data Sync
1. Add a faucet while logged in
2. Check browser DevTools Network for POST to `faucet-sync.php`
3. Check server: `ssh directsponsor-net`, look for JSON file in `data/faucetlist/`
4. Reload page - should load from server

### Debugging Auth Issues
- Check localStorage for `auth_session` key
- Check `js/auth.js` for session validation logic
- API endpoints validate in `auth-helper.php`
- Check auth server logs if tokens are rejected

## File Organization

```
faucetlist.org/
├── site/                    # Frontend + API
│   ├── index.php           # Main app with PHP ad rotation
│   ├── admin-ads.html      # Ad management interface
│   ├── api/                # Backend endpoints
│   │   ├── faucet-sync.php
│   │   ├── get-banner-ads.php
│   │   ├── save-banner-ad.php
│   │   ├── delete-banner-ad.php
│   │   ├── auth-helper.php
│   │   └── [other utilities]
│   ├── js/
│   │   └── auth.js         # JWT auth class
│   └── favicons/           # Site branding
├── data/                    # Server-only data
│   ├── ads.txt             # Banner ad templates
│   ├── ads-floating.txt    # Floating ad templates
│   └── faucetlist/         # User data (protected)
├── deploy-to-directadmin.sh # Deployment script
├── DEPLOYMENT.md           # Deployment guide
└── [config files]
```

## Current Status & Next Steps

### Deployment
✅ **Automated rsync deployment working** - SSH key authentication via faucetlist-directadmin account
- Deploy script: `./deploy-to-directadmin.sh`
- Protects server user data and ads-floating.txt from overwrites
- All site files synced with --delete flag

### Ad System
✅ **PHP-based ad rotation** - No JavaScript fetch, no CORS issues
- Banner and floating ads rendered server-side from txt files
- Admin interface still works for managing ads

## Known Limitations

- Ad system requires PHP server (won't display ads when opening file locally)
- No analytics on ad impressions or clicks (stored in data files only)
- Floating ads use simple timer-based dismissal, not sophisticated frequency capping
- API endpoints don't validate ad HTML (assumes admin is trusted)
- No backup system for user data files (manual backup needed)

## Future Considerations

- Phase 2: Chrome extension with notifications (separate repo)
- Currently no membership/payment system
- Ad inventory management could use a database
- Could implement ad scheduling and A/B testing
