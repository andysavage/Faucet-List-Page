# Manual Deployment Guide for Faucetlist.org

## Why Manual Deployment?

DirectAdmin shared hosting has SSH key authentication issues that prevent automated rsync deployment. Until migrated to a VPS or SSH is fixed, use DirectAdmin's file manager to upload files manually.

## Files to Upload

### To `/public_html/`

Upload these files from `site/` folder:

- `index.html` - Main application page
- `js/auth.js` - Authentication module  
- `js/banner-rotate.js` - Banner ad rotation
- `js/floating-ad.js` - Floating ad system
- `favicons/` - Entire folder with all favicon files
- `api/faucet-sync.php` - User data sync endpoint
- `api/get-banner-ads.php` - Ad retrieval endpoint
- `api/save-banner-ad.php` - Admin ad save endpoint
- `api/delete-banner-ad.php` - Admin ad delete endpoint
- `api/auth-helper.php` - Authentication utility

### To `/data/` 

Create `/home/faucetlist/data/` folder if it doesn't exist, then upload:

- `ads.txt` - Banner ad templates
- Create empty folder: `faucetlist/` - Will store user JSON files

### Required Directory Structure

```
/home/faucetlist/
├── public_html/
│   ├── index.html
│   ├── js/
│   │   ├── auth.js
│   │   ├── banner-rotate.js
│   │   └── floating-ad.js
│   ├── favicons/
│   │   └── [all favicon files]
│   └── api/
│       ├── faucet-sync.php
│       ├── get-banner-ads.php
│       ├── save-banner-ad.php
│       ├── delete-banner-ad.php
│       └── auth-helper.php
└── data/
    ├── ads.txt
    └── faucetlist/  [empty - user data goes here]
```

## What NOT to Upload

- `banners/` folder - Should already exist on server from directsponsor.org
- `index-previous.html` - Archived version
- `.git` folder - Version control
- `data/ads-floating.txt` - Server-managed, will be created via admin interface

## Testing After Upload

1. Visit https://faucetlist.org
2. Verify page loads correctly
3. Open DevTools → Network tab
4. Try adding a test faucet
5. Check for successful API calls to `faucet-sync.php` (if logged in)
6. Verify banner ad displays at top
7. Wait 10+ seconds for floating ad (bottom-left corner)
8. Check localStorage for `auth_session` key if login issues

## File Permissions

Ensure these permissions in DirectAdmin:

- `/home/faucetlist/data/` - Writable by PHP (755 or 775)
- `/home/faucetlist/data/faucetlist/` - Writable by PHP (755 or 775)
- All `.php` files - Readable (644)

## Troubleshooting

**Ads don't appear:**
- Check `data/ads.txt` exists and is readable
- Clear localStorage item: `ad_rotation_index`
- Check browser console for errors

**Sync doesn't work:**
- Verify `data/faucetlist/` folder exists and is writable
- Check `faucet-sync.php` for PHP errors in DirectAdmin error logs
- Confirm auth.directsponsor.org is accessible

**Floating ad doesn't show:**
- Clear localStorage item: `faucetlist_floating_closed_ts`
- Wait 10+ seconds after page load
- Check `get-banner-ads.php?type=floating` returns data

## Migration to VPS (Recommended)

### Advantages
- SSH access for automated deployments
- Better control over file permissions
- No DirectAdmin SSH key issues
- Faster deployment via rsync

### Setup Steps
1. Choose VPS (DigitalOcean, Linode, etc.)
2. Install PHP 7.2+, Apache/Nginx
3. Configure domain DNS to point to VPS
4. Update `deploy-to-directadmin.sh`:
   - `REMOTE_HOST` → VPS hostname
   - `REMOTE_SITE_PATH` → `/var/www/faucetlist.org/html`
   - `REMOTE_DATA_PATH` → `/var/www/faucetlist.org/data`
5. Set up SSH keys for passwordless deployment
6. Run `./deploy-to-directadmin.sh`

### VPS Requirements
- PHP 7.2+ with JSON support
- Write permissions to data directory
- rsync installed
- SSH access configured with keys

## Current Issues

- **SSH Authentication:** DirectAdmin faucetlist account rejects SSH key auth (publickey,gssapi-keyex,gssapi-with-mic)
- **Password Auth:** DirectAdmin port 10500 doesn't accept password authentication via rsync
- **Workaround:** Manual file upload via DirectAdmin web interface

## Password Info

DirectAdmin login (for reference):
- URL: https://directadmin-de.kxe.io:2222
- User: faucetlist
- Password stored in: `~/.bashrc` as `FAUCETLIST_PASSWORD`
