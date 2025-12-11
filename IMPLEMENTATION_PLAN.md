# Faucet List - Auth Integration Plan

## IMPORTANT: Hosting Requirements
**Must be hosted on a VPS** - NOT DirectAdmin shared hosting because:
- Need to protect the data folder (user faucet lists)
- Need to run Syncthing for profile sync
- Will be the 3rd site testing auth.directsponsor.org (alongside clickforcharity and roflfaucet)

## What We Have
- **auth.js** (from clickforcharity) - Complete auth class that handles:
  - JWT login via `auth.directsponsor.org`
  - Session storage in localStorage (`directsponsor_session`)
  - Auto-redirect back after login
  - `isLoggedIn()`, `getSession()`, `login()`, `logout()`
  - CSS classes: `.guest-only`, `.member-only`, `.admin-only` for showing/hiding content

## What's Needed

### 1. Copy auth.js to Faucet-List-Page
Minimal changes - just update branding from ClickForCharity to Faucet List

### 2. Add to index.html
- Include `<script src="js/auth.js"></script>`
- Add login/logout UI in header
- Add `.member-only` sync controls

### 3. Create API endpoint (`api/faucets.php`)
```php
// GET: Load user's faucet list from server
// POST: Save user's faucet list to server
// Storage: JSON file per user (e.g., data/{user_id}.json)
```

### 4. Add sync logic to index.html
- On login: merge localStorage with server data
- On faucet add/delete/claim: save to server if logged in
- "Sync Now" button for manual sync

## File Structure
```
Faucet-List-Page/
├── index.html          (add auth UI + sync logic)
├── js/
│   └── auth.js         (copy from clickforcharity)
├── api/
│   └── faucets.php     (new - save/load user faucets)
└── data/               (user faucet JSON files - protected)
```

## Deployment Checklist
- [ ] Get domain (e.g., faucets.directsponsor.org or standalone)
- [ ] Set up on VPS (NOT DirectAdmin)
- [ ] Configure Syncthing for profile sync
- [ ] Set data folder permissions (writeable but not web-accessible)
- [ ] Test auth flow with auth.directsponsor.org

## Reference
- Auth server docs: `/home/andy/work/projects/auth-server/README.md`
- ClickForCharity auth.js: `/home/andy/work/projects/clickforcharity/js/auth.js`
- RoflFaucet site: `/home/andy/work/projects/roflfaucet/site/`
