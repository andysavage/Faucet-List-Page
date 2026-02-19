# Faucetlist.org Deployment Guide

## Current Status

**DirectAdmin Shared Hosting:** SSH key authentication issues with faucetlist account prevent automated deployment via rsync. Manual upload via DirectAdmin file manager works (see Manual Deployment section below).

**Migration Planning:** Consider moving to a VPS with straightforward SSH access for easier deployments.

## Quick Start

### Automated Deployment (VPS or Fixed DirectAdmin)
```bash
./deploy-to-directadmin.sh
```

### Manual Deployment (Current DirectAdmin Setup)
See "Manual Deployment via DirectAdmin File Manager" section below.

## Architecture

### Local Development
- **Location:** `/home/andy/work/projects/faucetlist.org/`
- **Site files:** `./site/` (HTML, JavaScript, CSS, PHP)
- **Data files:** `./data/` (ad templates, configurations)

### Production Server
- **Host:** directsponsor-net (DirectAdmin reseller account)
- **User:** faucetlist (dedicated account)
- **Site path:** `/home/faucetlist/public_html/`
- **Data path:** `/home/faucetlist/data/`

## Data Protection Strategy

### Server-Only Data (Protected)
Files and directories created/modified only on the server are never overwritten by deployments:

- **`/home/faucetlist/data/faucetlist/`** - User account data
  - Contains individual user profile files and settings
  - Created by users or admin through the application
  - Always preserved during deployment

### Syncable Data (Code & Templates)
Files that are managed locally and synced to the server:

- **`ads.txt`** - Banner ad templates
- **`ads-floating.txt`** - Floating ad templates
- Configuration files

## Deployment Behavior

### Site Files
- Synced with `--delete` flag
- Removes files from server that no longer exist locally
- Safe because all are source code

### Data Files
- Synced WITHOUT `--delete` flag
- Protected directories excluded via `--exclude` filters
- Server-created files are always preserved

## Adding New Protected Data

If you create new server-side directories or files that should never be overwritten:

1. Open `deploy-to-directadmin.sh`
2. Find the `SERVER_ONLY_FILES` array
3. Add your new directory/file name to the array

Example:
```bash
SERVER_ONLY_FILES=(
    "faucetlist"           # Existing user account data
    "user-uploads"         # New: user uploaded files
    "cached-data"          # New: runtime generated cache
)
```

## Prerequisites

- SSH access configured as `directsponsor-net` in `~/.ssh/config`
- rsync installed on both local and remote systems
- Write permissions to `/home/faucetlist/` on the server

## Manual SSH Access

If you need to manually check the server:

```bash
ssh directsponsor-net
cd /home/faucetlist
ls -la public_html/
ls -la data/
```

## Troubleshooting

### "Permission denied" errors
- Verify the `faucetlist` user has write permissions to its home directory
- Check SSH key configuration in `~/.ssh/config`

### "rsync: command not found"
- rsync must be installed on the DirectAdmin server
- Contact hosting support if not available

### Data overwrites occurred
- All changes can be recovered from local copies
- Server-only data in protected directories is automatically preserved
- For user data in `data/faucetlist/`, it's never touched by the deployment script

## Verification

After deployment completes successfully:

1. Check the deployment output for "Deployment complete!" message
2. Visit https://faucetlist.org in your browser
3. Verify that:
   - Top banner ads load correctly
   - Floating ads appear (bottom-left after 10 seconds)
   - All functionality works as expected

## Rollback

If deployment causes issues:

1. Fix the problem locally
2. Re-run `./deploy-to-directadmin.sh`
3. The script will overwrite the bad files with the corrected versions

The `--delete` flag for site files means old code is cleaned up, but data is always preserved.

---

## Analytics System

### Overview

The analytics system is self-hosted and privacy-first. It parses Apache access logs server-side
and generates small JSON files consumed by a client-side dashboard.

- **Source of truth**: `web-analytics/` project (sibling repo)
- **Dashboard**: `site/stats.html` — deployed automatically with every `./deploy-to-directadmin.sh`
- **Parser**: `analytics.py` (repo root) — also synced to server on every deploy
- **Data**: `~/data/analytics/report-YYYY-MM-DD.json` files (server-only, never overwritten by deploy)
- **Dashboard URL**: https://faucetlist.org/stats.html

### One-Time Server Setup (run once after first deploy)

SSH into the server and create the data directory, then set up the cron job:

```bash
ssh faucetlist-directadmin

# Create analytics data directory (outside web root, already protected)
mkdir -p ~/data/analytics

# Make parser executable
chmod +x ~/scripts/analytics.py

# Find the access log path
ls ~/domains/faucetlist.org/logs/access.log

# Test the parser manually
python3 ~/scripts/analytics.py ~/domains/faucetlist.org/logs/access.log -d ~/data/analytics

# Check JSON files were created
ls -lh ~/data/analytics/

# Set up hourly cron (runs at 5 past every hour)
crontab -e
# Add this line:
# 5 * * * * /usr/bin/python3 /home/faucetlist/scripts/analytics.py /home/faucetlist/domains/faucetlist.org/logs/access.log -d /home/faucetlist/data/analytics >> /home/faucetlist/logs/analytics-cron.log 2>&1
```

### Updating the Analytics System

When `analytics.py` or `stats.html` are improved in the `web-analytics/` project:

1. Copy updated files into this repo:
   ```bash
   cp ../web-analytics/analytics.py ./analytics.py
   cp ../web-analytics/stats.html ./site/stats.html
   ```
2. Run `./deploy-to-directadmin.sh` — both files are pushed automatically.

No manual SSH steps needed for updates; the deploy script handles it.

### File Locations (Server)

| File | Server Path |
|------|-------------|
| Dashboard | `~/domains/faucetlist.org/public_html/stats.html` |
| Parser | `~/scripts/analytics.py` |
| JSON data | `~/data/analytics/report-YYYY-MM-DD.json` |
| Access log | `~/domains/faucetlist.org/logs/access.log` |
| Cron log | `~/logs/analytics-cron.log` |
